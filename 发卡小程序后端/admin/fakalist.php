<?php
include('../includes/common.php');
$title = '发卡库存管理';
include('./head.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
echo '  <div class="container" style="padding-top:70px;">
    <div class="col-md-12 center-block" style="float: none;">
';
$rs = $DB->query('SELECT * FROM kami_class WHERE 1 order by sort asc');
$select = '<option value="0">所有</option>';
while ($res = $DB->fetch($rs)) {
    $select .= '<option value="' . $res['cid'] . '">' . $res['name'] . '</option>';
}
$my = (isset($_GET['my']) ? $_GET['my'] : NULL);
if ($my == 'move') {
    $type = $_POST['type'];
    if (!$type || $type == '批量操作') {
        exit('<script language=\'javascript\'>alert(\'请选择分类\');history.go(-1);</script>');
    }
    $checkbox = $_POST['checkbox'];
    if (!$checkbox) {
        exit('<script language=\'javascript\'>alert(\'请选择分类\');history.go(-1);</script>');
    }
    $i = 0;
    foreach ($checkbox as $cid) {
        if ($type == (-1)) {
            $DB->query('update kami_class set active=1 where cid=\'' . $cid . '\' limit 1');
        } elseif ($type == (-2)) {
            $DB->query('update kami_class set active=0 where cid=\'' . $cid . '\' limit 1');
        } elseif ($type == (-3)) {
            $DB->query('DELETE FROM kami_class WHERE cid=\'' . $cid . '\' limit 1');
        }
        $i = $cid;
    }
    exit('<script language=\'javascript\'>alert(\'成功移动' . $i . '个商品\');history.go(-1);</script>');
} else {
    if ($_GET['cid']) {
        $cid = intval($_GET['cid']);
        $numrows = $DB->count('SELECT count(*) from kami_class where cid=\'' . $cid . '\'');
        $sql = ' cid=\'' . $cid . '\'';
    } else {
        $numrows = $DB->count('SELECT count(*) from kami_class where 1 ');
        $sql = ' active=1 or active=0';
    }
    echo '<form action="fakalist.php" method="GET" class="form-inline">
  <div class="form-group">
    <select name="cid" class="form-control" default="';
    echo $_GET['cid'];
    echo '">';
    echo $select;
    echo '</select>
  </div>
  <button type="submit" class="btn btn-primary">搜索</button>&nbsp;
</form>

	  <form name="form1" method="post" action="fakalist.php?my=move">
	  <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>分类名称</th><th>剩余卡密</th><th>已售出</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
';
    $pagesize = 30;
    $pages = intval($numrows / $pagesize);
    if ($numrows % $pagesize) {
    }
    if (isset($_GET['page'])) {
        $page = intval($_GET['page']);
    } else {
        $page = 1;
    }
    $offset = $pagesize * ($page - 1);
    $rs = $DB->query('SELECT a.*,(select count(b.cid) from kami_faka as b where b.cid=a.cid and users IS NULL and usetime IS NULL) as leftcount,(select count(b.cid) from kami_faka as b where b.cid=a.cid and users IS NOT NULL) as sellcount FROM kami_class as a WHERE' . $sql . ' order by sort asc limit ' . $offset . ',' . $pagesize);
    while ($res = $DB->fetch($rs)) {
        echo '<tr><td><input type="checkbox" name="checkbox[]" id="list1" value="' . $res['cid'] . '" onClick="unselectall1()"><b>' . $res['cid'] . '</b></td><td>' . $res['name'] . '</td><td>' . $res['leftcount'] . '</td><td>' . $res['sellcount'] . '</td><td>' . ($res['active'] == 1 ? '<span class="btn btn-xs btn-success" onclick="setActive(' . $res['cid'] . ',0)">上架中</span>' : '<span class="btn btn-xs btn-warning" onclick="setActive(' . $res['cid'] . ',1)">已下架</span>') . '</td><td><a href="./fakakms.php?cid=' . $res['cid'] . '" class="btn btn-info btn-xs">查看卡密</a>&nbsp;<a href="./fakakms.php?my=add&cid=' . $res['cid'] . '" class="btn btn-success btn-xs">加卡</a>&nbsp;<a href="./shopedit.php?my=delete&cid=' . $res['cid'] . '" class="btn btn-xs btn-danger" onclick="return confirm(\'你确实要删除此商品吗？\');">删除</a></td></tr>
';
    }
}
echo '          </tbody>
        </table>
<input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox">&nbsp;全选&nbsp;
<select name="type"><option selected>批量操作</option><option value="-1">&gt;改为上架中</option><option value="-2">&gt;改为已下架</option><option value="-3">&gt;删除选中</option></select>
<input type="submit" name="Submit" value="确定">
</div>
</form>
      
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<script>
var checkflag1 = "false";
function check1(field) {
if (checkflag1 == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag1 = "true";
return "false"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag1 = "false";
return "true"; }
}

function unselectall1()
{
    if(document.form1.chkAll1.checked){
	document.form1.chkAll1.checked = document.form1.chkAll1.checked&0;
	checkflag1 = "false";
    } 	
}

function setActive(cid,active) {
	$.ajax({
		type : \'GET\',
		url : \'ajax.php?act=setClass&cid=\'+cid+\'&active=\'+active,
		dataType : \'json\',
		success : function(data) {
			window.location.reload();
		},
		error:function(data){
			layer.msg(\'服务器错误\');
			return false;
		}
	});
}

var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>

';
echo '<ul class="pagination">';
$first = 1;
$prev = $page - 1;
$next = $page + 1;
$last = $pages;
if ($page > 1) {
    echo '<li><a href="fakalist.php?page=' . $first . $link . '">首页</a></li>';
    echo '<li><a href="fakalist.php?page=' . $prev . $link . '">&laquo;</a></li>';
} else {
    echo '<li class="disabled"><a>首页</a></li>';
    echo '<li class="disabled"><a>&laquo;</a></li>';
}
$i = 1;
while ($i < $page) {
    echo '<li><a href="fakalist.php?page=' . $i . $link . '">' . $i . '</a></li>';
}
echo '<li class="disabled"><a>' . $page . '</a></li>';
$i = $page + 1;
while ($i <= $pages) {
    echo '<li><a href="fakalist.php?page=' . $i . $link . '">' . $i . '</a></li>';
}
echo '';
if ($page < $pages) {
    echo '<li><a href="fakalist.php?page=' . $next . $link . '">&raquo;</a></li>';
    echo '<li><a href="fakalist.php?page=' . $last . $link . '">尾页</a></li>';
} else {
    echo '<li class="disabled"><a>&raquo;</a></li>';
    echo '<li class="disabled"><a>尾页</a></li>';
}
echo '</ul>';
echo '    </div>
  </div>';
