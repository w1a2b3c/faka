<?php
//error_reporting(0);
include __DIR__ . '/../includes/common.php';
require __DIR__ . '/../includes/wxLogin.php';
$code = isset($_GET['code']) && !empty($_GET['code']) ? $_GET['code'] : exit(json_encode(array('code' => -1, 'msg' => '缺失code参数值')));

$wxLogin = wxLogin::wx_login($code);
$wxLogin = json_decode($wxLogin, true);
if ($wxLogin['code'] != 200) {
    exit('{"code":-1,"msg":"' . $wxLogin['msg'] . '！"}');
}
$wxOpenid = $wxLogin['openid'];

$myUser = $DB->get_row("select * from kami_user where openid='{$wxOpenid}'");
if (!$myUser) {
    $spl = "insert into `kami_user` (`gtkid`,`openid`,`rate`,`addtime`,`lasttime`,`ip`) values ('" . getGTK($wxOpenid) . "','" . $wxOpenid . "',100,'" . $date . "','" . $date . "','" . real_ip() . "')";
    if (!$DB->query($spl)) {
        exit('{"code":-1,"msg":"注册失败！"}');
    }
}

$carmel = $cidCarmel['km'];
$mode = (int)$conf['examine'];
$datas['code'] = 200;
$datas['userid'] = $myUser['gtkid'];
$datas['examine'] = $mode;

if ($mode == 1) {//微信支付发卡

}

$recordDatas = $DB->query("select * from kami_faka where users='{$wxOpenid}'");
foreach ($recordDatas as $item) {
    $name = $DB->get_row("select * from kami_class where cid='{$item['cid']}'");
    $datas['recordList'][] = [
        'cid' => $item['cid'],
        'name' => $name['name'],
        'usetip' => $name['usetip'],
        'km' => $item['km'],
        'usetime' => $item['usetime'],
        'mode' => $item['mode'],
    ];
}

exit(json_encode($datas));