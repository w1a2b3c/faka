<?php
include('../includes/common.php');
$title = '支付订单';
include('./head.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}


$my = isset($_GET['my']) ? $_GET['my'] : null;
echo '  <div class="container" style="padding-top:70px;">
    <div class="col-md-12 center-block" style="float: none;">';
echo '<form action="order.php" method="GET" class="form-inline"><input type="hidden" name="my" value="search">
  <div class="form-group">
    <label>搜索</label>
	<select name="column" class="form-control"><option value="trade_no">商户订单号</option><option value="name">商品名称</option><option value="money">金额</option></select>
  </div>
  <div class="form-group">
    <input type="text" class="form-control" name="value" placeholder="搜索内容">
  </div>
  <button type="submit" class="btn btn-primary">搜索</button>
</form>';

if ($my == 'search') {
    if ($_GET['column'] == 'name') {
        $sql = " `{$_GET['column']}` like '%{$_GET['value']}%'";
    } else {
        $sql = " `{$_GET['column']}`='{$_GET['value']}'";
    }
    $numrows = $DB->count("SELECT count(*) from kami_pay WHERE{$sql}");
    $con = '包含 ' . $_GET['value'] . ' 的共有 <b>' . $numrows . '</b> 条订单';
    $link = '&my=search&column=' . $_GET['column'] . '&value=' . $_GET['value'];
} else {
    $numrows = $DB->count("SELECT count(*) from kami_pay WHERE 1");
    $sql = " 1";
    $con = '共有 <b>' . $numrows . '</b> 条订单';
}
echo $con;
?>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>商户订单号</th>
            <th>商品名称/金额</th>
            <th>支付方式</th>
            <th>创建时间/完成时间</th>
            <th>支付状态</th>
            <th>订单操作</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $pagesize = 30;
        $pages = intval($numrows / $pagesize);
        if ($numrows % $pagesize) {
            $pages++;
        }
        if (isset($_GET['page'])) {
            $page = intval($_GET['page']);
        } else {
            $page = 1;
        }
        $offset = $pagesize * ($page - 1);

        $rs = $DB->query("SELECT * FROM kami_pay WHERE{$sql} order by addtime desc limit $offset,$pagesize");
        while ($res = $DB->fetch($rs)) {
            echo '<tr><td><b><a href="#" title="支付通知" target="_blank" rel="noreferrer">' . $res['trade_no'] . '</a></b></td><td>' . $res['name'] . '<br/>￥' . $res['money'] . '</td><td>微信小程序</td><td>' . $res['addtime'] . '<br/>' . $res['endtime'] . '</td><td>' . ($res['status'] == 1 ? '<font color=green>已完成</font>' : '<font color=blue>未完成</font>') . '</td><td><a onclick="showKm(\'' . $res['trade_no'] . '\')" class="btn btn-info btn-xs">查看卡密</a>&nbsp;<a onclick="deliverKm(\'' . $res['trade_no'] . '\')" class="btn btn-success btn-xs">重新发卡</a></td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<?php
echo '<ul class="pagination">';
$first = 1;
$prev = $page - 1;
$next = $page + 1;
$last = $pages;
if ($page > 1) {
    echo '<li><a href="order.php?page=' . $first . $link . '">首页</a></li>';
    echo '<li><a href="order.php?page=' . $prev . $link . '">&laquo;</a></li>';
} else {
    echo '<li class="disabled"><a>首页</a></li>';
    echo '<li class="disabled"><a>&laquo;</a></li>';
}
for ($i = 1; $i < $page; $i++)
    echo '<li><a href="order.php?page=' . $i . $link . '">' . $i . '</a></li>';
echo '<li class="disabled"><a>' . $page . '</a></li>';
if ($pages >= 10) $s = 10;
else $s = $pages;
for ($i = $page + 1; $i <= $s; $i++)
    echo '<li><a href="order.php?page=' . $i . $link . '">' . $i . '</a></li>';
echo '';
if ($page < $pages) {
    echo '<li><a href="order.php?page=' . $next . $link . '">&raquo;</a></li>';
    echo '<li><a href="order.php?page=' . $last . $link . '">尾页</a></li>';
} else {
    echo '<li class="disabled"><a>&raquo;</a></li>';
    echo '<li class="disabled"><a>尾页</a></li>';
}
echo '</ul>';
#分页
?>
</div>
</div>


<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<script>
    function showKm(id) {
        var ii = layer.load(2, {shade: [0.1, '#fff']});
        $.ajax({
            type: 'POST',
            url: 'ajax.php?act=getKm',
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                layer.close(ii);
                if (data.code == 0) {
                    layer.alert("卡密:" + data.result);
                } else {
                    layer.alert(data.msg);
                }
            },
            error: function (data) {
                layer.msg('服务器错误');
                return false;
            }
        });
    }


    function deliverKm(id) {
        var ii = layer.load(2, {shade: [0.1, '#fff']});
        $.ajax({
            type: 'POST',
            url: 'ajax.php?act=deliverKm',
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                layer.close(ii);
                if (data.code == 0) {
                    layer.alert(data.result);
                } else {
                    layer.alert(data.msg);
                }
            },
            error: function (data) {
                layer.msg('服务器错误');
                return false;
            }
        });
    }
</script>
