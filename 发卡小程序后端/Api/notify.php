<?php
include_once '../Api/wechatConfig.php';
include __DIR__ . '/../includes/common.php';
$xml = file_get_contents('php://input');//监听是否有数据传入
libxml_disable_entity_loader(true);
$postObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
$result_code = $postObj->result_code;
$sign = $postObj->sign;
$openid = $postObj->openid;
$total_fee = $postObj->total_fee;
$out_trade_no = $postObj->out_trade_no;
$code = $postObj->code;
$total_fee = $total_fee / 100;
if (empty($code) || $notifyKey != $code) {
    file_put_contents('./log.txt', '密钥失败:' . $code . '订单号' . $out_trade_no, FILE_APPEND);
    exit;
}

if ($result_code == 'SUCCESS') {
    $sql = "select * from kami_pay where trade_no='{$out_trade_no}' and openid='{$openid}' and money={$total_fee}";
    $order = $DB->get_row($sql);
    if ($order) {
        $rows = $DB->query("update kami_pay set endtime='{$date}',status=1 where trade_no='{$out_trade_no}'");
        echo 'SUCCESS';
    }
}
//file_put_contents('./log.txt', '状态：' . $result_code . 'sign:' . $sign . 'openid:' . $openid . '金额' . $total_fee . 'out_trade_no:' . $out_trade_no, FILE_APPEND);



