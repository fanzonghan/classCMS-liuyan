<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(详情)}{comment:css()}</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-card-body">
            <table class="layui-table" lay-skin="line">
            <tbody>
                {if $comment.aid && $comment.cid}
                    {if $article}
                    <tr><td style="width:60px">文章:</td><td><a class="cmscolor" href="{$article.link}" target="_blank">{if isset($article.title)}{$article.title}{else}[标题未知]{/if}</a></td></tr>
                    {else}
                    <tr><td style="width:60px">文章:</td><td>文章已被删除</td></tr>
                    {/if}
                {elseif $comment.url}
                <tr><td style="width:60px">网址</td><td><a class="cmscolor" href="{$comment.url}" target="_blank">{$comment.url}</a></td></tr>
                {/if}
                {if isset($user)}
                <tr><td style="width:60px">评论者</td><td>用户:{$user.username}[{$user.hash}]</td></tr>
                {else}
                <tr><td style="width:60px">评论者</td><td>游客:{$comment.nick} {if $comment.mail} 邮箱:{$comment.mail}{/if}{if $comment.mail} 网址:{$comment.link}{/if}</td></tr>
                {/if}
                <tr><td style="width:60px">信息</td><td>ip:{$comment.ip} 系统:{$comment.os} 浏览器:{$comment.browser}</td></tr>
                <tr><td style="width:60px">时间</td><td>{date(Y-m-d H:i:s,$comment.addtime)} {if $comment.edittime}编辑:{date(Y-m-d H:i:s,$comment.edittime)}{/if}</td></tr>
                <tr><td style="width:60px">点赞数</td><td>{$comment.likecount}</td></tr>

                <tr><td style="width:60px">操作</td><td>
                    
                {if P('admin:status')}
                    <a class="status_change layui-btn layui-btn-sm {if $comment.status==0}cms-btn{else}layui-btn-primary{/if}" rel="0">待审核</a> 
                    <a class="status_change layui-btn layui-btn-sm {if $comment.status==1}cms-btn{else}layui-btn-primary{/if}" rel="1">通过</a> 
                    <a class="status_change layui-btn layui-btn-sm  {if $comment.status==2}cms-btn{else}layui-btn-primary{/if}" rel="2">垃圾</a>
                {/if}
                {if P('admin:sticky')}
                <a class="layui-btn layui-btn-sm layui-btn-primary articlesticky">{if $comment.sticky}已置顶{else}置顶{/if}</a>
                {/if}
                {if P('admin:del')}
                <a class="layui-btn layui-btn-sm layui-btn-primary articledel">删除评论</a>
                {/if}
                </td>
                </tr>
                <tr><td colspan="2">评论内容:</td></tr>
                <tr><td colspan="2" class="wl-content" data-waline>{$comment.content}</td></tr>
            </tbody>
            </table>
          </div>
        </div>
     </div>
</div>
<script>layui.use(['index'],function(){
    layui.$('.articlesticky').click(function(){
        id=layui.$(this).parents('tr').attr('rel');
        layui.layer.confirm('是否{if $comment.sticky}取消{/if}置顶此评论?', {
          btn: ['{if $comment.sticky}取消置顶{else}置顶{/if}','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:admin:sticky",data:{ id: {$comment.id},sticky:{if $comment.sticky}0{else}1{/if}},async:true,beforeSend:function(){
                layui.admin.load('置顶中...');
            },done: function(res){
                if (res.error==0)
                {
                    layer.confirm(res.msg, {btn: ['好的'],shadeClose:1,end :function(){layui.admin.events.reload();}}, function(){layui.admin.events.reload();});
                }
            }});
        });
    });    
    {if P('admin:del')}
    layui.$('.articledel').click(function(){
        id=layui.$(this).parents('tr').attr('rel');
        layui.layer.confirm('是否删除此评论?<br>如有下属评论,也将同时删除!', {
          btn: ['删除','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:admin:del",data:{ id: {$comment.id}},async:true,beforeSend:function(){
                layui.admin.load('删除中...');
            },done: function(res){
                if (res.error==0)
                {
                    layui.layer.msg(res.msg);
                    parent.layer.close(parent.layer.getFrameIndex(window.name));
                    parent.layui.$('tr[rel='+{$comment.id}+']').remove();
                }
            }});
        });
    });
    {/if}
    {if P('admin:status')}
    layui.$('.status_change').click(function(){
        status=layui.$(this).attr('rel');
        layui.$('.status_change i').remove();
        layui.$(this).append('<i class="layui-icon layui-icon-loading-1 layui-anim layui-anim-rotate layui-anim-loop"></i>');
        layui.admin.req({type:'post',url:"?do={this}:admin:status",data:{ id: {$comment.id},status:status},async:true,beforeSend:function(){
            layui.admin.load('更改中...');
        },done: function(res){
            layui.$('.status_change i').remove();
            if (res.error==0)
            {
                layui.layer.msg(res.msg);
                layui.$('.status_change').addClass('layui-btn-primary').removeClass('cms-btn');
                layui.$('.status_change[rel='+status+']').removeClass('layui-btn-primary').addClass('cms-btn');
            }
        }});
    });
    {/if}
});
</script>
{admin:body:~()}
</body>
</html>
