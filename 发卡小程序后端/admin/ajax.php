<?php
include("../includes/common.php");
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

@header('Content-Type: application/json; charset=UTF-8');

switch ($act) {
    case 'setClass': //分类上下架
        $cid = intval($_GET['cid']);
        $active = intval($_GET['active']);
        $DB->query("update kami_class set active='$active' where cid='{$cid}'");
        exit('{"code":0,"msg":"succ"}');
        break;
    case 'setClassSort': //排序操作
        $cid = intval($_GET['cid']);
        $sort = intval($_GET['sort']);
        if (setClassSort($cid, $sort)) {
            exit('{"code":0,"msg":"succ"}');
        } else {
            exit('{"code":-1,"msg":"失败"}');
        }
        break;
    case 'getIntroduce':
        $cid = intval($_POST['cid']);
        $rows = $DB->get_row("select * from kami_class where cid='$cid' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前分类不存在！"}');
        exit('{"code":0,"result":"' . UnicodeEncode($rows['introduce']) . '"}');
        break;
    case 'setIntroduce':
        $cid = intval($_POST['cid']);
        $text = daddslashes($_POST['text']);
        $rows = $DB->query("update kami_class set introduce='$text' where cid='{$cid}'");
        if (!$rows) exit('{"code":-1,"msg":"保存失败！"}');
        exit('{"code":0,"result":"succ"}');
        break;
    case 'getUseTip':
        $cid = intval($_POST['cid']);
        $rows = $DB->get_row("select * from kami_class where cid='$cid' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前分类不存在！"}');
        exit('{"code":0,"result":"' . UnicodeEncode($rows['usetip']) . '"}');
        break;
    case 'setUseTip':
        $cid = intval($_POST['cid']);
        $text = daddslashes($_POST['text']);
        $rows = $DB->query("update kami_class set usetip='$text' where cid='{$cid}'");
        if (!$rows) exit('{"code":-1,"msg":"保存失败！"}');
        exit('{"code":0,"result":"succ"}');
        break;
    case 'getMoney':
        $cid = intval($_POST['cid']);
        $rows = $DB->get_row("select * from kami_class where cid='$cid' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前分类不存在！"}');
        exit('{"code":0,"result":"' . $rows['money'] . '"}');
        break;
    case 'setMoney':
        $cid = intval($_POST['cid']);
        $money = round(daddslashes($_POST['money']), 2);
        $rows = $DB->query("update kami_class set money='$money' where cid='{$cid}'");
        if (!$rows) exit('{"code":-1,"msg":"保存失败！"}');
        exit('{"code":0,"result":"succ"}');
        break;
    case 'getRate':
        $id = intval($_POST['id']);
        $rows = $DB->get_row("select * from kami_user where id='$id' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前用户不存在！"}');
        exit('{"code":0,"result":"' . $rows['rate'] . '"}');
        break;
    case 'setRate':
        $id = intval($_POST['id']);
        $rate = intval($_POST['rate']);
        if ($rate > 100 || $rate < 0) {
            exit('{"code":-1,"msg":"填写范围1-100"}');
        }
        $rows = $DB->query("update kami_user set rate='$rate' where id='{$id}'");
        if (!$rows) exit('{"code":-1,"msg":"保存失败！"}');
        exit('{"code":0,"result":"succ"}');
        break;
    case 'getKm':
        $trade_no = daddslashes($_POST['id']);
        $rows = $DB->get_row("select * from kami_faka where trade_no='$trade_no' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前卡密不存在！"}');
        exit('{"code":0,"result":"' . $rows['km'] . '"}');
        break;
    case 'deliverKm':
        $trade_no = daddslashes($_POST['id']);

        $order = $DB->get_row("select * from kami_pay where trade_no='{$trade_no}'");
        if ($order) {
            $openid = $order['openid'];

            $kid = $DB->get_row("select * from kami_faka where cid={$order['cid']} and usetime is null");
            if (!$kid) {
                exit(json_encode(array('code' => -1, 'msg' => '此类型卡密已经被领完了，请联系管理员加卡密。')));
            }

            $orderRecord = $DB->get_row("select * from kami_faka where trade_no='{$trade_no}'");
            if ($orderRecord) {
                exit(json_encode(array('code' => -1, 'msg' => '已发卡卡密，不能重复获得！')));
            }

            if ($order['status'] == 1) {//已经支付成功
                if ($DB->query("update kami_faka set usetime='$date',users='$openid',mode=1,trade_no='{$trade_no}' where kid={$kid['kid']} and cid={$order['cid']}")) {
                    exit(json_encode(array('code' => 0, 'result' => '发送卡密到用户成功', 'carmel' => $carmel)));
                } else {
                    exit(json_encode(array('code' => -1, 'msg' => '未知成功，请稍后重试。')));
                }
            } else {
                //未支付
                exit(json_encode(array('code' => -1, 'msg' => '未支付成功，有问题请联系管理员或过会在“记录”查看！')));
            }
        } else {
            exit(json_encode(array('code' => -1, 'msg' => '订单号不存在')));
        }
        break;
    default:
        exit('{"code":-4,"msg":"No Act"}');
        break;
}


function UnicodeEncode($str)
{
    preg_match_all('/./u', $str, $matches);
    $unicodeStr = "";
    foreach ($matches[0] as $m) {
        //拼接
        $unicodeStr .= "&#" . base_convert(bin2hex(iconv('UTF-8', "UCS-4", $m)), 16, 10);
    }
    return $unicodeStr;
}
