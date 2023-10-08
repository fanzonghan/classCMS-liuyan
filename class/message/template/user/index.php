<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(消息中心)}
    <style>
        .messagetitle:hover{color:#1E9FFF!important}
    </style>
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-form">
            <div class="layui-card">
                <div class="layui-card-header">
                    <div class="layui-row">
                        <div id="cms-breadcrumb">{admin:breadcrumb($breadcrumb)}</div>
                        <div id="cms-right-top-button">
                            {if P('user:sender')}<a href="?do={this}:user:sender" class="layui-btn layui-btn-sm layui-btn-danger"><i class="layui-icon layui-icon-set"></i><b>通知设置</b></a>{/if}
                            {if P('admin:sender')}<a href="?do={this}:admin:sender" class="layui-btn layui-btn-sm layui-btn-danger"><i class="layui-icon layui-icon-set"></i><b>管理设置</b></a>{/if}
                        </div>
                    </div>
                </div>
                <div class="layui-card-body">
                    {if count($messages)}
                    <table class="layui-table" lay-skin="line" id="articles">
                    <thead>
                        <tr>
                            <th style="width:20px"></th>
                            <th>&nbsp;&nbsp;&nbsp;标题</th>
                            <th style="width:140px">时间</th>
                            <th class="layui-hide-xs" style="width:220px">类型</th>
                        </tr> 
                    </thead>
                    <tbody>
                        {loop $messages as $message}
                            <tr rel="{$message.id}">
                            <td style="width:20px"><input type="checkbox" name="check_article" lay-skin="primary" ></td>
                            <td class="messagetd"><span class="layui-badge-dot"{if $message.ifread} style="visibility: hidden"{/if}></span> <a class="cmscolor messagetitle" href="?do={this}:user:detail&id={$message.id}">{$message.title}</a></td>
                            <td>{if $message.addtime}{date(y-m-d H:i,$message.addtime)}{/if}</td>
                            <td class="layui-hide-xs">
                                <?php
                                    if(isset($kinds[$message['kind']])){
                                        echo($kinds[$message['kind']]);
                                    }
                                ?>
                            </td>
                            </tr>
                        {/loop}
                    </tbody>
                    </table>
                    {else}
                    <blockquote class="layui-elem-quote layui-text">
                        暂无任何消息
                    </blockquote>
                    {/if}
                    <div class="layui-row">
                        <div id="cms-left-bottom-button" class="layui-btn-container">
                            {if count($messages)}
                            <a class="layui-btn layui-btn-sm layui-btn-primary choseall">全选</a>
                            <a class="layui-btn layui-btn-sm layui-btn-primary choseback">反选</a>
                            <a class="layui-btn layui-btn-sm layui-btn-primary read">已读</a>
                            {if P('user:del')}<a class="layui-btn layui-btn-sm layui-btn-primary delchosed">删除</a>{/if}
                            <a class="layui-btn layui-btn-sm layui-btn-primary readAll">全部已读</a>
                            {if P('user:del')}<a class="layui-btn layui-btn-sm layui-btn-primary delAll">全部删除</a>{/if}
                            {/if}
                        </div>
                        <div id="cms-right-bottom-button" class="layui-btn-container">
                            {admin:pagelist()}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function chosedArticle(){
    articles=new Array();
    layui.$('#articles tbody input[name=check_article]').each(function(){
        if (layui.$(this).prop("checked"))
        {
            articles.push(layui.$(this).parents('tr').attr('rel'));
        }
    });
    return articles.join(';');
}
layui.use(['index'],function(){
    layui.$('.choseall').click(function(){
        layui.$('#articles tbody input[name=check_article]').prop("checked",true);
        layui.form.render('checkbox');
    });
    layui.$('.choseback').click(function(){
        layui.$('#articles tbody input[name=check_article]').each(function(){
            if (layui.$(this).prop("checked"))
            {
                layui.$(this).prop("checked",false);
            }else{
                layui.$(this).prop("checked",true);
            }
        });
        layui.form.render('checkbox');
    });
    layui.$('.read').click(function(){
        readArticle(chosedArticle());
    });
    layui.$('.readAll').click(function(){
        layui.layer.confirm('是否将全部消息标记为已读', {
            btn: ['确定','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:user:readAll",data:{readAll:1},async:true,beforeSend:function(){
                layui.admin.load('提交中...');
            },done: function(res){
                if (res.error==0)
                {
                    parent.layui.$('#message_notice_btn').css('visibility','hidden');
                    layui.layer.msg(res.msg);
                    layui.admin.events.reload();
                }
            }});
        });
    });
    layui.$('.delAll').click(function(){
        layui.layer.confirm('是否删除全部消息!!!', {
            btn: ['确定','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:user:delAll",data:{delAll:1},async:true,beforeSend:function(){
                layui.admin.load('删除中...');
            },done: function(res){
                if (res.error==0)
                {
                    parent.layui.$('#message_notice_btn').css('visibility','hidden');
                    layui.layer.msg(res.msg);
                    layui.admin.events.reload();
                }
            }});
        });
    });
    function readArticle(ids){
        if (ids.length==0)
        {
            layui.layer.msg('请先选择消息');
            return;
        }
        layui.layer.confirm('是否标记已读', {
            btn: ['确定','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:user:read",data:{ ids: ids},async:true,beforeSend:function(){
                layui.admin.load('提交中...');
            },done: function(res){
                if (res.error==0)
                {
                    layui.layer.msg(res.msg);
                    layui.admin.events.reload();
                }
            }});
        });
    }
    layui.$('.delchosed').click(function(){
        delArticle(chosedArticle());
    });
    function delArticle(ids){
        if (ids.length==0)
        {
            layui.layer.msg('请先选择消息');
            return;
        }
        layui.layer.confirm('是否删除选中的消息', {
            btn: ['删除','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:user:del",data:{ ids: ids},async:true,beforeSend:function(){
                layui.admin.load('删除中...');
            },done: function(res){
                if (res.error==0)
                {
                    layui.layer.msg(res.msg);
                    del_ids = ids.split(";");
                    for (i=0; i<del_ids.length; i++ ){
                        layui.$('#articles tbody tr[rel='+del_ids[i]+']').remove();
                    }
                }
                if (layui.$('#articles tbody tr').length==0)
                {
                    layui.admin.events.reload();
                }
            }});
        });
    }
    layui.$('.messagetitle').click(function(event){
        console.log();
        if(layui.$(this).parent().find('span').css('visibility')=='visible'){
            checkmsg=1;
        }else{
            checkmsg=0;
        }
        layui.$(this).parent().find('span').css('visibility','hidden');
        href=layui.$(this).attr('href');
        id=layui.$(this).parent().parent().attr('rel');
        if(layui.$(window).width()<900){ width=layui.$(window).width(); }else{ width=780; }
        if(layui.$(window).height()<700){ height=layui.$(window).height(); }else{ height=480; }
        layui.layer.open({
          type: 2,
          shade: 0,
          title:'详情',
          shade:0.2,
          shadeClose:true,
          area: [width+'px', height+'px'],
          content: href,
          success:function(){
            {if config('checkMessage')}
            if(checkmsg){
                layui.admin.req({type:'post',url:"?do={this}:user:check",data:{check:id},async:true,done: function(res){
                    if (res.error==0 && !res.newmsg)
                    {
                        parent.layui.$('#message_notice_btn').css('visibility','hidden');
                    }
                }});
            }
            {/if}
          }
        });
        event.preventDefault();
        return;
    });
    {if P('admin:del')}
    layui.$('.articledel').click(function(){
        id=layui.$(this).parents('tr').attr('rel');
        layui.layer.confirm('是否删除此文章', {
          btn: ['删除','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:admin:del",data:{ id: id},async:true,beforeSend:function(){
                layui.admin.load('删除中...');
            },done: function(res){
                if (res.error==0)
                {
                    layui.layer.msg(res.msg);
                    layui.$('tr[rel='+taskid+']').remove();
                }
            }});
        });
    });
    {/if}
});
</script>
{admin:body:~()}
</body>
</html>
