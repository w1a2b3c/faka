<?php
include("../includes/common.php");
$title='系统管理中心';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

  <div class="container" style="padding-top:70px;">
    <div class="col-sm-12 col-md-3">
		<div class="list-group">
			<div class="list-group-item list-group-item-success">
				<h3 class="panel-title">卡密管理</h3>
			</div>
			<a class="list-group-item" href="./classlist.php"><span class="glyphicon glyphicon-list" aria-hidden="true">&nbsp;分类添加</span></a>
			<a class="list-group-item" href="./classlist.php"><span class="glyphicon glyphicon-list" aria-hidden="true">&nbsp;卡密管理</span></a>
			<a class="list-group-item" href="./fakakms.php?my=add"><span class="glyphicon glyphicon-list" aria-hidden="true">&nbsp;卡密添加</span></a>
            <a class="list-group-item" href="./order.php"><span class="glyphicon glyphicon-list" aria-hidden="true">&nbsp;支付订单</span></a>
            <a class="list-group-item" href="./ulist.php"><span class="glyphicon glyphicon-list" aria-hidden="true">&nbsp;用户信息</span></a>
	</div>

        <div class="list-group">
            <div class="list-group-item list-group-item-success">
                <h3 class="panel-title">小程序设置</h3>
            </div>
            <a class="list-group-item" href="./set.php?mod=site"><span class="glyphicon glyphicon-list" aria-hidden="true">&nbsp;系统设置</span></a>
        </div>


    </div>
  </div>