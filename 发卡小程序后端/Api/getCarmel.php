<?php
error_reporting(0);
include __DIR__ . '/../includes/common.php';
require __DIR__ . '/../includes/wxLogin.php';
$code = isset($_GET['code']) && !empty($_GET['code']) ? $_GET['code'] : exit(json_encode(array('code' => -1, 'msg' => '缺失code参数值')));
$cid = isset($_GET['cid']) && !empty($_GET['cid']) ? $_GET['cid'] : exit(json_encode(array('code' => -1, 'msg' => '缺失cid参数值')));
$trade_no = isset($_GET['trade_no']) && !empty($_GET['trade_no']) ? $_GET['trade_no'] : NULL;

//微信小程序校验
$wxLogin = wxLogin::wx_login($code);
$wxLogin = json_decode($wxLogin, true);
if ($wxLogin['code'] != 200) {
    exit('{"code":-1,"msg":"' . $wxLogin['msg'] . '！"}');
}

$openid = $wxLogin['openid'];
$classCid = $DB->get_row("select * from kami_class where active=1 and cid={$cid}");
if (!$classCid) {
    exit(json_encode(array('code' => -1, 'msg' => '当前领取的卡密类型不存在')));
}
$cidCarmel = $DB->get_row("select * from kami_faka where cid={$cid} and usetime is null");
if (!$cidCarmel) {
    exit(json_encode(array('code' => -1, 'msg' => '此类型卡密已经被领完了，请联系管理员加卡密。')));
}
$carmel = $cidCarmel['km'];
$mode = (int)$conf['examine'];
if ($mode == 1) {//微信支付发卡
    if (empty($trade_no)) {
        exit(json_encode(array('code' => -1, 'msg' => '未知错误，请联系管理员！')));
    }
    $order = $DB->get_row("select * from kami_pay where trade_no='{$trade_no}' and openid='{$openid}' and cid={$cid}");
    if ($order) {
        $orderRecord = $DB->get_row("select * from kami_faka where trade_no='{$trade_no}'");
        if ($orderRecord) {
            exit(json_encode(array('code' => -1, 'msg' => '已发卡卡密，不能重复获得！')));
        }

        if ($order['status'] == 1) {//已经支付成功
            if ($DB->query("update kami_faka set usetime='$date',users='$openid',mode=$mode,trade_no='{$trade_no}' where kid={$cidCarmel['kid']} and cid={$cid}")) {
                exit(json_encode(array('code' => 200, 'msg' => '领取成功', 'carmel' => $carmel)));
            } else {
                exit(json_encode(array('code' => -1, 'msg' => '未知成功，请稍后重试。')));
            }
        } else {
            //未支付
            exit(json_encode(array('code' => -1, 'msg' => '未支付成功，有问题请联系管理员或过会在“记录”查看！')));
        }
    }else{
        exit(json_encode(array('code' => -1, 'msg' => '订单号不存在！')));
    }
}
if ($DB->query("update kami_faka set usetime='$date',users='$openid',mode=$mode where kid={$cidCarmel['kid']} and cid={$cid}")) {
    exit(json_encode(array('code' => 200, 'msg' => '领取成功', 'carmel' => $carmel)));
} else {
    exit(json_encode(array('code' => -1, 'msg' => '未知成功，请稍后重试。')));
}


