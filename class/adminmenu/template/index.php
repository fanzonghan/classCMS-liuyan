<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>
{admin:head:(菜单管理)}
<style>
.layui-colla-item{position:relative}
.action{position: absolute;right: 15px;top: 10px;font-size: 14px;}
.action .layui-btn:hover{background-color: #1E9FFF;color: #fff;border-color: #1E9FFF;}
.layui-btn .layui-icon{margin-right:0}
</style>
</head>
<body>
    <div class="layui-fluid">
        <div class="layui-row">
            <div class="layui-card">
                <div class="layui-card-header">
                    <div class="layui-row">
                        <?php
                        $breadcrumb=array(
                        array('url'=>'?do=admin:class:config&hash=adminmenu','title'=>'后台菜单自定义'),
                        array('title'=>'菜单管理')
                        );
                        ?>
                        <div id="cms-breadcrumb">{admin:breadcrumb($breadcrumb)}</div>
                        <div id="cms-right-top-button">
                            <a href="?do=adminmenu:add&fid=0" class="layui-btn layui-btn-sm layui-btn-danger"><i class="layui-icon layui-icon-add-1"></i><b>增加</b></a>
                            <a class="layui-btn layui-btn-sm layui-btn-danger openall"><i class="layui-icon layui-icon-down"></i><b>展开</b></a>
                            <a class="layui-btn layui-btn-sm layui-btn-danger closeall"><i class="layui-icon layui-icon-right"></i><b>折叠</b></a>
                        </div>
                    </div>
                </div>
                <div class="layui-card-body">
{$menuhtml}

<div class="layui-row">
    <div id="cms-left-bottom-button" class="layui-btn-container">
    </div>
    <div id="cms-right-bottom-button" class="layui-btn-container">
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
<script>
layui.use(['index'],function(){
    layui.$('.delmenu').click(function(){
        id=layui.$(this).attr('rel');
        layui.layer.confirm('是否删除此菜单', {
          btn: ['删除','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do=adminmenu:del",data:{ id: id},async:true,beforeSend:function(){
                layui.admin.load('删除中...');
            },done: function(res){
                if (res.error==0)
                {
                    layui.admin.events.loadmenu();
                    layui.layer.msg('删除成功');
                    layui.$('.layui-colla-item[rel='+id+']').remove();
                }
            }});
        });
    });
    layui.$('.openall').click(function(){
        layui.$('.layui-colla-content').addClass('layui-show');
        layui.element.render('collapse');
    });
    layui.$('.closeall').click(function(){
        layui.$('.layui-colla-content').removeClass('layui-show');
        layui.element.render('collapse');
    });
});
</script>
{admin:body:~()}
</body>
</html>