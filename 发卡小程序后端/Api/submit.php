<?php
error_reporting(0);
header("Content-Type:text/json;charset=UTF-8");

include_once __DIR__ . '/../includes/common.php';
require __DIR__ . '/../includes/wxLogin.php';
require __DIR__ . '/../includes/wxpay/WeixinPay.php';
$code = isset($_REQUEST['code']) && !empty($_REQUEST['code']) ? $_REQUEST['code'] : exit('{"code":-1,"msg":"参数补齐！"}');
$contents = isset($_REQUEST['content']) && !empty($_REQUEST['content']) ? $_REQUEST['content'] : exit('{"code":-1,"msg":"参数补齐！"}');


$wxLogin = wxLogin::wx_login($code);
$wxLogin = json_decode($wxLogin, true);
if ($wxLogin['code'] != 200) {
    exit('{"code":-1,"msg":"' . $wxLogin['msg'] . '！"}');
}
$wxOpenid = $wxLogin['openid'];

$content = json_decode($contents, true);
$trade_no = $content['order'];
$cid = $content['cid'];
$name = $content['name'];

if (empty($trade_no) || empty($cid) || empty($name)) {
    exit('{"code":-1,"msg":"缺少参数,创建订单失败！"}');
}
$numrows = $DB->count("SELECT count(*) from kami_faka WHERE cid={$cid} and usetime IS NULL and users IS NULL");
if ($numrows < 1) {
    exit('{"code":-1,"msg":"库存不足，请联系管理员加库！"}');
}

$classCid = $DB->get_row("select * from kami_class where active=1 and cid={$cid}");
if (!$classCid) {
    exit('{"code":-1,"msg":"订单价格获取失败！"}');
}
$money = $classCid['money'];
if (empty($money) || $money == '0.00') {
    exit('{"code":-1,"msg":"订单价格为0元，不支持购买！pay1"}');
}

$order = $DB->get_row("select * from kami_pay where trade_no='{$trade_no}'");
if ($order) {
    exit('{"code":-1,"msg":"订单号重复,请重新创建！"}');
}

$myUser = $DB->get_row("select * from kami_user where openid='{$wxOpenid}'");
if (!$myUser) {
    $spl = "insert into `kami_user` (`openid`,`rate`,`addtime`,`lasttime`,`ip`) values ('" . $wxOpenid . "',100,'" . $date . "','" . $date . "','" . real_ip() . "')";
    if (!$DB->query($spl)) {
        exit('{"code":-1,"msg":"注册失败！pay"}');
    }
}

$rate = $myUser['rate'] / 100;
if (!empty($conf['payName'])) {
    $name = $conf['payName'];
}
$payRmb = round($money * $rate, 2);
if (empty($payRmb)) {
    exit('{"code":-1,"msg":"订单价格为0元，不支持购买！pay2"}');
}
$setPay = new WeixinPay($wxOpenid, $trade_no, $name, $payRmb);
$pay = $setPay->pay();//下单获取返回值
$pay['code'] = -1;


$spl = "insert into `kami_pay` (`trade_no`,`openid`,`cid`,`name`,`money`,`content`,`addtime`,`ip`,`sign`,`status`) values ('" . $trade_no . "','" . $wxOpenid . "','" . $cid . "','" . $name . "','" . $payRmb . "','" . $contents . "','" . $date . "','" . real_ip() . "','" . $pay['paySign'] . "',0)";
if ($DB->query($spl)) {
    $pay['code'] = 200;
    exit(json_encode($pay));
} else {
    exit('{"code":-1,"msg":"创建订单失败！"}');
}