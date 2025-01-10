<?php
error_reporting(0);
include('../includes/common.php');
include_once '../Api/wechatConfig.php';
$title = '后台管理';
include('./head.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
echo '  <div class="container" style="padding-top:70px;">
    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
';
$mod = (isset($_GET['mod']) ? $_GET['mod'] : NULL);
if ($mod == 'site_n' && $_POST['do'] == 'submit') {
    $xcx_name = $_POST['xcx_name'];
    $adVideoId = $_POST['adVideoId'];
    $adVideoTip = $_POST['adVideoTip'];
    $shareTip = $_POST['shareTip'];
    $xcxappid = $_POST['xcxappid'];
    $xcxpath = $_POST['xcxpath'];
    $ruleImg = $_POST['ruleImg'];
    $contact = $_POST['contact'];
    $examine = $_POST['examine'];
    $shareTitle = $_POST['shareTitle'];
    $shareImg = $_POST['shareImg'];
    $payName = $_POST['payName'];
    $appid = $_POST['appid'];
    $secret = $_POST['secret'];
    $mch_id = $_POST['mch_id'];
    $key = $_POST['key'];
    $notifyKey = $_POST['notifyKey'];
    $user = $_POST['user'];
    $pwd = $_POST['pwd'];
    $gl1 = $_POST['gl1'];
    $appid1 = $_POST['appid1'];
    $path1 = $_POST['path1'];
    $glimg1 = $_POST['glimg1'];
    $gl2 = $_POST['gl2'];
    $appid2 = $_POST['appid2'];
    $path2 = $_POST['path2'];
    $glimg2 = $_POST['glimg2'];
    $gl3 = $_POST['gl3'];
    $appid3 = $_POST['appid3'];
    $path3 = $_POST['path3'];
    $glimg3 = $_POST['glimg3'];
    if ($xcx_name == NULL) {
        showmsg('必填项不能为空！', 3);
    }
    $wechatConfig = "<?php
     /*微信小程序校验配置*/
     \$appid='$appid';
     \$secret='$secret';
     \$mch_id='$mch_id';
     \$key='$key';
     \$notifyKey='$notifyKey';
     ?>";
    if (!file_put_contents('../Api/wechatConfig.php', $wechatConfig)) {
        showmsg('小程序校验配置改失败！');
    }
    saveSetting('xcx_name', $xcx_name);
    saveSetting('examine', $examine);
    saveSetting('adVideoId', $adVideoId);
    saveSetting('adVideoTip', $adVideoTip);
    saveSetting('shareTip', $shareTip);
    saveSetting('xcxappid', $xcxappid);
    saveSetting('xcxpath', $xcxpath);  
    saveSetting('shareTitle', $shareTitle);
    saveSetting('shareImg', $shareImg);
    saveSetting('payName', $payName);
    saveSetting('ruleImg', $ruleImg);
    saveSetting('contact', $contact);
    saveSetting('admin_user', $user);
    saveSetting('gl1', $gl1);
    saveSetting('appid1', $appid1);
    saveSetting('path1', $path1);
    saveSetting('glimg1', $glimg1);
    saveSetting('gl2', $gl2);
    saveSetting('appid2', $appid2);
    saveSetting('path2', $path2);
    saveSetting('glimg2', $glimg2);
    saveSetting('gl3', $gl3);
    saveSetting('appid3', $appid3);
    saveSetting('path3', $path3);
    saveSetting('glimg3', $glimg3);
    if (!empty($pwd)) {
        saveSetting('admin_pass', $pwd);
    }
    showmsg('修改成功！', 1);
} else {
    if ($mod == 'site') {
        echo '<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">系统配置</h3></div>
<div class="panel-body">
  <form action="./set.php?mod=site_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit"/>
	<div class="form-group">
	  <label class="col-sm-2 control-label">小程序名称</label>
	  <div class="col-sm-10"><input type="text" name="xcx_name" value="';
        echo $conf['xcx_name'];
        echo '" class="form-control" required/></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">激励视频ID</label>
	  <div class="col-sm-10"><input type="text" name="adVideoId" value="';
        echo $conf['adVideoId'];
        echo '" class="form-control"/></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">未观看完视频广告提示内容</label>
	  <div class="col-sm-10"><input type="text" name="adVideoTip" value="';
        echo $conf['adVideoTip'];
        echo '" class="form-control"/></div>
	</div>
		<div class="form-group">
	  <label class="col-sm-2 control-label">卡密获取方式</label>
	  <div class="col-sm-10"><select class="form-control" name="examine" default="';
        echo $conf['examine'];
        echo '"><option value="0">直接获取</option><option value="1">微信支付获取</option><option value="2">看激励视频获取</option><option value="3">看激励视频以及分享获取</option></select>
	  <pre>审核时如看广告：推荐选择“看激励视频获取”。</pre></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">分享提示内容</label>
	  <div class="col-sm-10"><input type="text" name="shareTip" value="';
        echo $conf['shareTip'];
        echo '" class="form-control"/></div>
	</div>
		<div class="form-group">
	  <label class="col-sm-2 control-label">规则图片</label>
	  <div class="col-sm-10"><input type="text" name="ruleImg" value="';
        echo $conf['ruleImg'];
        echo '" class="form-control"/></div>
	</div>
		<div class="form-group">
	  <label class="col-sm-2 control-label">appid</label>
	  <div class="col-sm-10"><input type="text" name="xcxappid" value="';
        echo $conf['xcxappid'];
        echo '" class="form-control"/></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">path</label>
	  <div class="col-sm-10"><input type="text" name="xcxpath" value="';
        echo $conf['xcxpath'];
        echo '" class="form-control"/></div>        
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">联系方式</label>
	  <div class="col-sm-10"><input type="text" name="contact" value="';
        echo $conf['contact'];
        echo '" class="form-control"/></div>
	</div><br/>
				<h4>右上角分享设置</h4>
	<div class="form-group">
	  <label class="col-sm-2 control-label">标题</label>
	  <div class="col-sm-10"><input type="text" name="shareTitle" value="';
        echo $conf['shareTitle'];
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">图片链接</label>
	  <div class="col-sm-10"><input type="text" name="shareImg" value="';
        echo $conf['shareImg'];
        echo '" class="form-control" /></div>
	</div><br/>
	
	
<h4>关联小程序设置</h4>
	<div class="form-group">
	  <label class="col-sm-2 control-label">小程序名称</label>
	  <div class="col-sm-10"><input type="text" name="gl1" value="';
        echo $conf['gl1'];
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">appid</label>
	  <div class="col-sm-10"><input type="text" name="appid1" value="';
        echo $conf['appid1'];
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">path</label>
	  <div class="col-sm-10"><input type="text" name="path1" value="';
        echo $conf['path1'];
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">图片</label>
	  <div class="col-sm-10"><input type="text" name="glimg1" value="';
        echo $conf['glimg1'];
        echo '" class="form-control" /></div>
	</div><br/>	
	
	<div class="form-group">
	  <label class="col-sm-2 control-label">小程序名称</label>
	  <div class="col-sm-10"><input type="text" name="gl2" value="';
        echo $conf['gl2'];
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">appid</label>
	  <div class="col-sm-10"><input type="text" name="appid2" value="';
        echo $conf['appid2'];
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">path</label>
	  <div class="col-sm-10"><input type="text" name="path2" value="';
        echo $conf['path2'];
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">图片</label>
	  <div class="col-sm-10"><input type="text" name="glimg2" value="';
        echo $conf['glimg2'];
        echo '" class="form-control" /></div>
	</div><br/>
	
	<div class="form-group">
	  <label class="col-sm-2 control-label">小程序名称</label>
	  <div class="col-sm-10"><input type="text" name="gl3" value="';
        echo $conf['gl3'];
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">appid</label>
	  <div class="col-sm-10"><input type="text" name="appid3" value="';
        echo $conf['appid3'];
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">path</label>
	  <div class="col-sm-10"><input type="text" name="path3" value="';
        echo $conf['path3'];
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">图片</label>
	  <div class="col-sm-10"><input type="text" name="glimg3" value="';
        echo $conf['glimg3'];
        echo '" class="form-control" /></div>
	</div><br/>
	
	
			<h4>小程序校验与支付设置</h4>
	<div class="form-group">
	  <label class="col-sm-2 control-label">appid</label>
	  <div class="col-sm-10"><input type="text" name="appid" value="';
        echo $appid;
        echo '" class="form-control" /></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">secret</label>
	  <div class="col-sm-10"><input type="text" name="secret" value="';
        echo $secret;
        echo '" class="form-control" /></div>
	</div><br/>
		<div class="form-group">
	  <label class="col-sm-2 control-label">支付mch_id</label>
	  <div class="col-sm-10"><input type="text" name="mch_id" value="';
        echo $mch_id;
        echo '" class="form-control" /></div>
	</div>
			<div class="form-group">
	  <label class="col-sm-2 control-label">支付key</label>
	  <div class="col-sm-10"><input type="text" name="key" value="';
        echo $key;
        echo '" class="form-control" /></div>
	</div>
				<div class="form-group">
	  <label class="col-sm-2 control-label">校验订单Key</label>
	  <div class="col-sm-10"><input type="text" name="notifyKey" value="';
        echo $notifyKey;
        echo '" class="form-control" /></div>
	</div>
				<div class="form-group">
	  <label class="col-sm-2 control-label">支付名称</label>
	  <div class="col-sm-10"><input type="text" name="payName" value="';
        echo $conf['payName'];
        echo '" class="form-control" />
	  <pre>不填则以原本商品名称</pre></div>
	</div><br/>
	<h4>管理员账号设置</h4>
	<div class="form-group">
	  <label class="col-sm-2 control-label">用户名</label>
	  <div class="col-sm-10"><input type="text" name="user" value="';
        echo $conf['admin_user'];
        echo '" class="form-control" required/></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">密码重置</label>
	  <div class="col-sm-10"><input type="text" name="pwd" value="" class="form-control" placeholder="不修改请留空"/></div>
	</div><br/>
	<div class="form-group">
	  <div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
	 </div>
	</div>


  </form>
</div>
</div>
<script>
$("select[name=\'captcha_open\']").change(function(){
	if($(this).val() == 1){
		$("#captcha_frame").css("display","inherit");
	}else{
		$("#captcha_frame").css("display","none");
	}
});
   
 var items = $("select[default]");
 for (i = 0; i < items.length; i++) {
     $(items[i]).val($(items[i]).attr("default") || 0);
    }
</script>
';
    }
}
?>