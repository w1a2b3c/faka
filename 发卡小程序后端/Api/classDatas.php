<?php
error_reporting(0);
include __DIR__ . '/../includes/common.php';
require __DIR__ . '/../includes/wxLogin.php';
$code = isset($_REQUEST['code']) && !empty($_REQUEST['code']) ? $_REQUEST['code'] : exit('{"code":-1,"msg":"参数补齐！"}');
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
$DB->query("update kami_user set lasttime='$date' where openid='{$wxOpenid}'");//更新登录

$rate = $myUser['rate'] / 100;

$classDatas['code'] = 200;
$class = $DB->query('select * from kami_class where active=1');
foreach ($class as $item) {
    $classDatas['class'][] = [
        'cid' => $item['cid'],
        'name' => $item['name'] . ($conf['examine'] == '1' ? '-' . round($item['money'] * $rate, 2) . '元' : ''),
        'payName' => $item['name'],
        'introduce' => $item['introduce'],
        'usetip' => $item['usetip'],
    ];
}
$classDatas['data']['xcx_name'] = $conf['xcx_name'];
$classDatas['data']['adVideoId'] = $conf['adVideoId'];
$classDatas['data']['adVideoTip'] = $conf['adVideoTip'];
$classDatas['data']['shareTip'] = $conf['shareTip'];
$classDatas['data']['xcxappid'] = $conf['xcxappid'];
$classDatas['data']['xcxpath'] = $conf['xcxpath'];
$classDatas['data']['ruleImg'] = $conf['ruleImg'];
$classDatas['data']['contact'] = $conf['contact'];
$classDatas['data']['examine'] = (int)$conf['examine'];
$classDatas['data']['shareTitle'] = $conf['shareTitle'];
$classDatas['data']['shareImg'] = $conf['shareImg'];
$classDatas['data']['gl1'] = $conf['gl1'];
$classDatas['data']['appid1'] = $conf['appid1'];
$classDatas['data']['path1'] = $conf['path1'];
$classDatas['data']['glimg1'] = $conf['glimg1'];
$classDatas['data']['gl2'] = $conf['gl2'];
$classDatas['data']['appid2'] = $conf['appid2'];
$classDatas['data']['path2'] = $conf['path2'];
$classDatas['data']['glimg2'] = $conf['glimg2'];
$classDatas['data']['gl3'] = $conf['gl3'];
$classDatas['data']['appid3'] = $conf['appid3'];
$classDatas['data']['path3'] = $conf['path3'];
$classDatas['data']['glimg3'] = $conf['glimg3'];

exit(json_encode($classDatas));