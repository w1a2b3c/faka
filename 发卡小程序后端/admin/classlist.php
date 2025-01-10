<?php
include('../includes/common.php');
$title = '分类管理';
include('./head.php');
if ($islogin!=1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
echo '<link href="//lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <div class="container" style="padding-top:70px;">
    <div class="col-sm-12 col-md-10 center-block" style="float: none;">
';
$my = (isset($_GET['my']) ? $_GET['my'] : NULL);
if ($my=='add_submit') {
    $name = $_POST['name'];
    if ($name==NULL) {
        exit('<script language=\'javascript\'>alert(\'保存错误,请确保每项都不为空!\');history.go(-1);</script>');
    } else {
        $sql = 'insert into `kami_class` (`name`,`active`) values (\'' . $name . '\',\'1\')';
        if ($cid = $DB->insert($sql)) {
            $DB->query('UPDATE `kami_class` SET `sort`=\'' . $cid . '\' WHERE `cid`=\'' . $cid . '\'');
            exit('<script language=\'javascript\'>alert(\'添加分类成功！\');window.location.href=\'classlist.php\';</script>');
        } else {
            exit('<script language=\'javascript\'>alert(\'添加分类失败！' . $DB->error() . '\');history.go(-1);</script>');
        }
    }
} elseif ($my=='edit_submit') {
    $cid = $_GET['cid'];
    $rows = $DB->get_row('select * from kami_class where cid=\'' . $cid . '\' limit 1');
    if (!$rows) {
        exit('<script language=\'javascript\'>alert(\'当前记录不存在！\');history.go(-1);</script>');
    }
    $name = $_POST['name'];
    if ($name==NULL) {
        exit('<script language=\'javascript\'>alert(\'保存错误,请确保每项都不为空!\');history.go(-1);</script>');
    } elseif ($DB->query('update kami_class set name=\'' . $name . '\' where cid=\'' . $cid . '\'')) {
        exit('<script language=\'javascript\'>alert(\'修改分类成功！\');window.location.href=\'classlist.php\';</script>');
    } else {
        exit('<script language=\'javascript\'>alert(\'修改商品失败！' . $DB->error() . '\');history.go(-1);</script>');
    }
} elseif ($my=='delete') {
    $cid = $_GET['cid'];
    $sql = 'DELETE FROM kami_class WHERE cid=\'' . $cid . '\'';
    if ($DB->query($sql)) {
        exit('<script language=\'javascript\'>alert(\'删除成功！\');window.location.href=\'classlist.php\';</script>');
    } else {
        exit('<script language=\'javascript\'>alert(\'删除失败！' . $DB->error() . '\');history.go(-1);</script>');
    }
} else {
    $numrows = $DB->count('SELECT count(*) from kami_class');
    $sql = ' 1';
    echo $con;
    echo '<div class="panel panel-primary">
	<div class="panel-heading">
		分类名
	</div>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>排序操作</th><th>名称（';
    echo $numrows;
    echo '）</th><th>操作</th></tr></thead>
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
    $rs = $DB->query('SELECT * FROM kami_class WHERE' . $sql . ' order by sort asc');
    while ($res = $DB->fetch($rs)) {
        echo '<form action="classlist.php?my=edit_submit&cid=' . $res['cid'] . '" method="POST" class="form-inline"><tr><td>
	<a class="btn btn-xs sort_btn" title="移到顶部" onclick="sort(' . $res['cid'] . ',0)"><i class="fa fa-long-arrow-up"></i></a><a class="btn btn-xs sort_btn" title="移到上一行" onclick="sort(' . $res['cid'] . ',1)"><i class="fa fa-chevron-circle-up"></i></a><a class="btn btn-xs sort_btn" title="移到下一行" onclick="sort(' . $res['cid'] . ',2)"><i class="fa fa-chevron-circle-down"></i></a><a class="btn btn-xs sort_btn" title="移到底部" onclick="sort(' . $res['cid'] . ',3)"><i class="fa fa-long-arrow-down"></i></a>
	</td><td><input type="text" class="form-control input-sm" name="name" value="' . $res['name'] . '" placeholder="分类名称" required></td><td><button type="submit" class="btn btn-primary btn-sm">修改</button>&nbsp;' . ($res['active']==1 ? '<span class="btn btn-sm btn-success" onclick="setActive(' . $res['cid'] . ',0)">隐藏</span>' : '<span class="btn btn-sm btn-warning" onclick="setActive(' . $res['cid'] . ',1)">显示</span>') . '&nbsp;<a onclick="showMoney(' . $res['cid'] . ')" class="btn btn-sm btn-danger">购买金额【'.$res['money'].'】</a>&nbsp;<a href="./fakalist.php?cid=' . $res['cid'] . '" class="btn btn-warning btn-sm">卡密管理</a>&nbsp;<a onclick="showIntroduce(' . $res['cid'] . ')" class="btn btn-info btn-sm">分类介绍</a>&nbsp;<a onclick="showUseTip(' . $res['cid'] . ')" class="btn btn-default btn-sm">使用提示</a>&nbsp;<a href="./classlist.php?my=delete&cid=' . $res['cid'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'你确实要删除此商品吗？\');">删除</a></td></tr></form>';
    }
}
echo '<form action="classlist.php?my=add_submit" method="POST" class="form-inline" id="addclass"><tr><td></td><td><input type="text" class="form-control input-sm" name="name" placeholder="分类名称" required></td><td colspan="3"><button type="submit" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span> 添加分类</button>&nbsp;</td></tr></form>';
echo '          </tbody>
        </table>
      </div>
';

echo"   </div>
  </div>
</div>
";
?>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<script>
function setActive(cid,active) {
	$.ajax({
		type : 'GET',
		url : 'ajax.php?act=setClass&cid='+cid+'&active='+active,
		dataType : 'json',
		success : function(data) {
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}

function showMoney(cid){
    var title = '购买金额';
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=getMoney',
        data : {cid:cid},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.prompt({title: '填写'+title, value: data.result, formType: 0}, function(money, index){
                    var ii = layer.load(2, {shade:[0.1,'#fff']});
                    $.ajax({
                        type : 'POST',
                        url : 'ajax.php?act=setMoney',
                        data : {cid:cid,money:money},
                        dataType : 'json',
                        success : function(data) {
                            layer.close(ii);
                            if(data.code == 0){
                                layer.msg('填写'+title+'成功');
                            }else{
                                layer.alert(data.msg);
                            }
                        },
                        error:function(data){
                            layer.msg('服务器错误');
                            return false;
                        }
                    });
                });
            }else{
                layer.alert(data.msg);
            }
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
}

function showIntroduce(cid){
    var title = '分类介绍';
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=getIntroduce',
        data : {cid:cid},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.prompt({title: '填写'+title, value: data.result, formType: 2}, function(text, index){
                    var ii = layer.load(2, {shade:[0.1,'#fff']});
                    $.ajax({
                        type : 'POST',
                        url : 'ajax.php?act=setIntroduce',
                        data : {cid:cid,text:text},
                        dataType : 'json',
                        success : function(data) {
                            layer.close(ii);
                            if(data.code == 0){
                                layer.msg('填写'+title+'成功');
                            }else{
                                layer.alert(data.msg);
                            }
                        },
                        error:function(data){
                            layer.msg('服务器错误');
                            return false;
                        }
                    });
                });
            }else{
                layer.alert(data.msg);
            }
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
}
function showUseTip(cid){
    var title = '卡密使用提示';
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=getUseTip',
        data : {cid:cid},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.prompt({title: '填写'+title, value: data.result, formType: 2}, function(text, index){
                    var ii = layer.load(2, {shade:[0.1,'#fff']});
                    $.ajax({
                        type : 'POST',
                        url : 'ajax.php?act=setUseTip',
                        data : {cid:cid,text:text},
                        dataType : 'json',
                        success : function(data) {
                            layer.close(ii);
                            if(data.code == 0){
                                layer.msg('填写'+title+'成功');
                            }else{
                                layer.alert(data.msg);
                            }
                        },
                        error:function(data){
                            layer.msg('服务器错误');
                            return false;
                        }
                    });
                });
            }else{
                layer.alert(data.msg);
            }
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
}
function sort(cid,sort) {
	$.ajax({
		type : 'GET',
		url : 'ajax.php?act=setClassSort&cid='+cid+'&sort='+sort,
		dataType : 'json',
		success : function(data) {
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function getImage(cid) {
	layer.confirm('是否从该分类下的商品图片获取一张作为分类图片？', {
		btn: ['确定'] //按钮
	}, function(){
	$.ajax({
		type : 'GET',
		url : 'ajax.php?act=getClassImage&cid='+cid,
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				layer.msg('获取图片成功');
				$("input[name='img"+cid+"']").val(data.url);
			}else{
				layer.alert('该分类下商品都没有图片');
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
	});
}
function editClass(cid) {
	var name = $("input[name='name"+cid+"']").val();
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=editClass&cid='+cid,
		data : {name:name},
		dataType : 'json',
		success : function(data) {
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function saveAll() {
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=editClassAll',
		data : $('#classlist').serialize(),
		dataType : 'json',
		success : function(data) {
			alert('保存成功！');
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function saveAllImages() {
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=editClassImages',
		data : $('#classlist').serialize(),
		dataType : 'json',
		success : function(data) {
			alert('保存成功！');
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function fileSelect(cid){
	$("#file"+cid).trigger("click");
}

function fileUpload(cid){
	var fileObj = $("#file"+cid)[0].files[0];
	if (typeof (fileObj) == "undefined" || fileObj.size <= 0) {
		return;
	}
	var formData = new FormData();
	formData.append("do","upload");
	formData.append("type","class");
	formData.append("file",fileObj);
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		url: "ajax.php?act=uploadimg",
		data: formData,
		type: "POST",
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		success: function (data) {
			layer.close(ii);
			if(data.code == 0){
				layer.msg('上传图片成功');
				$("input[name='img"+cid+"']").val(data.url);
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	})
}
</script>
