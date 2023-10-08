<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(消息中心)}
<style>
    .kindname{width:250px}
    @media screen and (max-width: 992px){
        .kindname{width:150px}
    }
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
                            {if P('admin:test')}<a  class="layui-btn layui-btn-sm layui-btn-danger message_test"><i class="layui-icon layui-icon-delete"></i><b>测试发送</b></a>{/if}
                            {if P('admin:clean')}<a  class="layui-btn layui-btn-sm layui-btn-danger message_clean"><i class="layui-icon layui-icon-delete"></i><b>清空消息</b></a>{/if}
                        </div>
                    </div>
                </div>
                {if !count($senders)}
                    <div class="layui-card-body">
                    <blockquote class="layui-elem-quote layui-text">
                        未安装任何通知应用,请前往应用商店安装.
                    </blockquote>
                    </div>
                {/if}
                {if !count($kinds)}
                    <div class="layui-card-body">
                    <blockquote class="layui-elem-quote layui-text">
                        无任何消息类型,无需设置.
                    </blockquote>
                    </div>
                {/if}
            </div>

            {if count($senders) && count($kinds)}
            <div class="layui-card">
                    <div class="layui-card-body">
                        <table class="layui-table" lay-skin="line" id="articles">
                            
                {$thead=1}
                {loop $groups as $group}
                        <thead>
                            <tr>
                                <th>{$group}</th>
                                {loop $senders as $sender}
                                    <th>{if $thead}{$sender.title}{/if}</th>
                                {/loop}
                                {$thead=0}
                            </tr>
                        </thead>
                            {loop $kinds as $kind}
                                {if $kind['groupname']==$group}
                                    <tr>
                                        <th class="kindname">{$kind.title} {if $kind.tips}<i class="layui-icon layui-icon-tips tips" rel="{$kind.tips}"></i>{/if}</th>
                                        {loop $senders as $sender}
                                            <td><button type="button" data-title="{$sender.title}-{$kind.title}" data-sender="{$sender.hash}" data-kind="{$kind.hash}" data-classhash="{$kind.classhash}" class="setbtn layui-btn layui-btn-primary layui-btn-xs">设置</button></td>
                                        {/loop}
                                    </tr>
                                {/if}
                            {/loop}
                {/loop}
                
                </table>
                    </div>
                </div>
            {/if}


                    <div class="layui-row">
                        <div id="cms-left-bottom-button" class="layui-btn-container"></div>
                        <div id="cms-right-bottom-button" class="layui-btn-container"></div>
                    </div>


        </div>
    </div>
</div>
<script>layui.use(['index'],function(){
    layui.$('.tips').click(function(){
        layer.tips(layui.$(this).attr('rel'),layui.$(this));
    });
    layui.$('.message_clean').click(function(){
        delid=layui.$(this).parents('tr').attr('rel');
            layui.layer.confirm('是否清空所有用户的消息!!!', {
              btn: ['清空','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
                layui.admin.req({type:'post',url:"?do={this}:admin:clean",data:{ clean:1},async:true,beforeSend:function(){
                    layui.admin.load('清空中...');
                },done: function(res){
                    if (res.error==0)
                    {
                        layui.layer.msg(res.msg);
                    }
                }});
            });
    });
    layui.$('.message_test').click(function(){
        if(layui.$(window).width()<900){ width=layui.$(window).width(); }else{ width=880; }
        if(layui.$(window).height()<700){ height=layui.$(window).height(); }else{ height=680; }
        layui.layer.open({
          type: 2,shadeClose:1,
          title:'测试发送',
          area: [width+'px', height+'px'],
          content: '?do=message:admin:test'
        }); 
    });
    layui.$('.setbtn').click(function(){
        title=layui.$(this).attr('data-title');
        kind=layui.$(this).attr('data-kind');
        sender=layui.$(this).attr('data-sender');
        classhash=layui.$(this).attr('data-classhash');
        if(layui.$(window).width()<900){ width=layui.$(window).width(); }else{ width=880; }
        if(layui.$(window).height()<700){ height=layui.$(window).height(); }else{ height=680; }
        layui.layer.open({
          type: 2,shadeClose:1,
          title:title,
          area: [width+'px', height+'px'],
          content: '?do={this}:admin:set&classhash='+classhash+'&kind='+kind+'&sender='+sender
        }); 
    });
});
</script>
{admin:body:~()}
</body>
</html>
