<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>
{admin:head:(菜单管理 排序)}
</head>
<body>
    <div class="layui-fluid">
        <div class="layui-row">
        <div class="layui-form">
            <div class="layui-card">
                <div class="layui-card-header">
                    <div class="layui-row">
                        <div id="cms-breadcrumb">{admin:breadcrumb($breadcrumb)}</div>
                        <div id="cms-right-top-button"></div>
                    </div>
                </div>
                <div class="layui-card-body">
                    {if count($menus)>1}
                        <blockquote class="layui-elem-quote layui-text">
                            拖动 <i class="layui-icon layui-icon-find-fill sortable-color"></i> 符号进行排序
                        </blockquote>
                    {else}
                        <blockquote class="layui-elem-quote layui-text">
                            需要多个同级菜单才可排序
                        </blockquote>
                    {/if}
                    <table class="layui-table" lay-skin="line" >
                        <tbody id="menus">
                            {loop $menus as $menu}
                            <tr rel="{$menu.id}">
                                <td>
                                <i class="layui-icon layui-icon-find-fill sortable-color"></i>
                                <span{if $menu.enabled==0} class="cms-text-disabled"{/if}>{$menu.menuname}</span>
                                </td>
                            </tr>
                            {/loop}
                        </tbody>
                    </table>
                    
                </div>
            </div>
            <div class="layui-form-item layui-layout-admin">
                <div class="layui-input-block">
                    <div class="layui-footer">
                        {if count($menus)>1}
                            <button class="layui-btn layui-btn-normal cms-btn" lay-submit="" lay-filter="form-submit">保存</button>
                        {else}
                            <button class="layui-btn layui-btn-normal layui-btn-disabled" lay-submit="" lay-filter="form-submit">保存</button>
                        {/if}
                        <button type="button" class="layui-btn layui-btn-primary" layadmin-event="back">返回</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
<script>layui.use(['index','sortable'],function(){
    new Sortable(menus, {
        handle: '.layui-icon',
    });
    layui.form.on('submit(form-submit)', function(data){
        layui.$('button[lay-filter=form-submit]').blur();
        idarray='';
        layui.$('#menus tr').each(function(){
            idarray=idarray+'|'+layui.$(this).attr('rel');
        });
        layui.admin.req({type:'post',url:"?do=adminmenu:orderPost",data:{ids:idarray},async:true,beforeSend:function(){
            layui.admin.load('修改排序中...');
        },done: function(res){
            if (res.error==0)
            {
                layui.admin.events.loadmenu();
                var confirm=layer.confirm(res.msg, {btn: ['好的','返回'],shadeClose:1},function(){layui.layer.close(confirm);},function(){
                    layui.admin.events.back();
                    });
            }
        }});
      return false;
    });
});
</script>
{admin:body:~()}
</body>
</html>