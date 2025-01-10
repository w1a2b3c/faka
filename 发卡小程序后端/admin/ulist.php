<?php
include('../includes/common.php');
$title = '用户信息';
include('./head.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}

echo '  <div class="container" style="padding-top:70px;">
    <div class="col-md-12 center-block" style="float: none;">';

$my = isset($_GET['my']) ? $_GET['my'] : null;


if ($my == 'delete') {
    $id = $_GET['id'];
    $rows = $DB->get_row("select * from kami_user where id='$id' limit 1");
    if (!$rows)
        showmsg('当前记录不存在！', 3);
    $urls = explode(',', $rows['url']);
    $sql = "DELETE FROM kami_user WHERE id='$id'";
    if ($DB->query($sql))
        showmsg('删除成功！<br/><br/><a href="./ulist.php">>>返回用户列表</a>', 1);
    else
        showmsg('删除失败！' . $DB->error(), 4);
} else {

    echo '<form action="ulist.php" method="GET" class="form-inline"><input type="hidden" name="my" value="search">
  <div class="form-group">
    <label>搜索</label>
	<select name="column" class="form-control"><option value="gtkid">用户ID</option><option value="openid">openid</option></select>
  </div>
  <div class="form-group">
    <input type="text" class="form-control" name="value" placeholder="搜索内容">
  </div>
  <button type="submit" class="btn btn-primary">搜索</button>
</form>';

    if ($my == 'search') {
        $sql = " `{$_GET['column']}`='{$_GET['value']}'";
        $numrows = $DB->count("SELECT count(*) from kami_user WHERE{$sql}");
        $con = '包含 ' . $_GET['value'] . ' 的共有 <b>' . $numrows . '</b> 个用户';
        $link = '&my=search&column=' . $_GET['column'] . '&value=' . $_GET['value'];
    } else {
        $numrows = $DB->count("SELECT count(*) from kami_user WHERE 1");
        $sql = " 1";
        $con = '共有 <b>' . $numrows . '</b> 个用户';
    }
    echo $con;
    ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>用户ID</th>
                <th>openid</th>
                <th>费率(折)</th>
                <th>添加时间/最后登录时间</th>
                <th>操作</th>
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

            $rs = $DB->query("SELECT * FROM kami_user WHERE{$sql} order by id desc limit $offset,$pagesize");
            while ($res = $DB->fetch($rs)) {
                echo '<tr><td><b>' . $res['gtkid'] . '</b></td><td><b>' . $res['openid'] . '</b></td><td><a onclick="showRate(' . $res['id'] . ')" >' . $res['rate'] . '</a></td><td>' . $res['addtime'] . '<br/>' . $res['lasttime'] . '</td><td><a href="./ulist.php?my=delete&id=' . $res['id'] . '" class="btn btn-xs btn-danger" onclick="return confirm(\'你确实要删除此用户吗？\');">删除</a></td></tr>';
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
        echo '<li><a href="ulist.php?page=' . $first . $link . '">首页</a></li>';
        echo '<li><a href="ulist.php?page=' . $prev . $link . '">&laquo;</a></li>';
    } else {
        echo '<li class="disabled"><a>首页</a></li>';
        echo '<li class="disabled"><a>&laquo;</a></li>';
    }
    for ($i = 1; $i < $page; $i++)
        echo '<li><a href="ulist.php?page=' . $i . $link . '">' . $i . '</a></li>';
    echo '<li class="disabled"><a>' . $page . '</a></li>';
    for ($i = $page + 1; $i <= $pages; $i++)
        echo '<li><a href="ulist.php?page=' . $i . $link . '">' . $i . '</a></li>';
    echo '';
    if ($page < $pages) {
        echo '<li><a href="ulist.php?page=' . $next . $link . '">&raquo;</a></li>';
        echo '<li><a href="ulist.php?page=' . $last . $link . '">尾页</a></li>';
    } else {
        echo '<li class="disabled"><a>&raquo;</a></li>';
        echo '<li class="disabled"><a>尾页</a></li>';
    }
    echo '</ul>';
#分页
}
?>
</div>
</div>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<script>
    function showRate(id) {
        var title = '用户费率';
        var ii = layer.load(2, {shade: [0.1, '#fff']});
        $.ajax({
            type: 'POST',
            url: 'ajax.php?act=getRate',
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                layer.close(ii);
                if (data.code == 0) {
                    layer.prompt({title: '填写' + title, value: data.result, formType: 0}, function (rate, index) {
                        var ii = layer.load(2, {shade: [0.1, '#fff']});
                        $.ajax({
                            type: 'POST',
                            url: 'ajax.php?act=setRate',
                            data: {id: id, rate: rate},
                            dataType: 'json',
                            success: function (data) {
                                layer.close(ii);
                                if (data.code == 0) {
                                    layer.msg('填写' + title + '成功');
                                } else {
                                    layer.alert(data.msg);
                                }
                            },
                            error: function (data) {
                                layer.msg('服务器错误');
                                return false;
                            }
                        });
                    });
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