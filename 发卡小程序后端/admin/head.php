<?php
@header('Content-Type: text/html; charset=UTF-8');
?>
    <!DOCTYPE html>
    <html lang="zh-cn">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title><?php echo $title ?></title>
        <link href="//lib.baomitu.com/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
        <script src="//lib.baomitu.com/jquery/1.12.4/jquery.min.js"></script>
        <script src="//lib.baomitu.com/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <!--[if lt IE 9]>
        <script src="//lib.baomitu.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="//lib.baomitu.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
<body>
<?php if ($islogin == 1) { ?>
    <nav class="navbar navbar-fixed-top navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">导航按钮</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="./">系统管理中心</a>
            </div><!-- /.navbar-header -->
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="./"><span class="glyphicon glyphicon-home"></span> 平台首页</a>
                    </li>

                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span
                                    class="glyphicon glyphicon-th"></span> 卡密管理<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="./classlist.php">卡密分类</a></li>
                            <li><a href="./fakalist.php">库存管理</a></li>
                            <li><a href="./fakakms.php?my=add">添加卡密</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="./order.php"><span class="glyphicon glyphicon-credit-card""></span> 支付订单</a>
                    </li>
                    <li>
                        <a href="./ulist.php"><span class="glyphicon glyphicon-user"></span> 用户信息</a>
                    </li>
                    <li>
                        <a href="./set.php?mod=site"><span class="glyphicon glyphicon-cog"></span> 系统设置</a>
                    </li>

                    <li><a href="./login.php?logout"><span class="glyphicon glyphicon-log-out"></span> 退出登陆</a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container -->
    </nav><!-- /.navbar -->
<?php } ?>