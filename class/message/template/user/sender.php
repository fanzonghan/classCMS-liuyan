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
                        <div id="cms-right-top-button"></div>
                    </div>
                </div>
                    {if !count($senders)}
                    <div class="layui-card-body">
                    <blockquote class="layui-elem-quote layui-text">
                        无通知方式,请联系管理员.
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
                                <th>{if $thead}{$sender.title} {if $sender.userconfig}<button type="button" data-title="{$sender.title}" data-sender="{$sender.hash}" class="setbtn layui-btn layui-btn-primary layui-btn-xs">设置</button>{/if}{/if}</th>
                            {/loop}
                            {$thead=0}
                        </tr>
                    </thead>
                    {loop $kinds as $kind}
                        {if $kind['groupname']==$group}
                            <tr>
                                <th class="kindname">{$kind.title} {if $kind.tips}<i class="layui-icon layui-icon-tips tips" rel="{$kind.tips}"></i>{/if}</th>
                                {loop $senders as $sender}
                                    <td>
                                        <?php
                                            if($kind['status'][$sender['hash']]['enabled']){
                                                if($kind['status'][$sender['hash']]['editable']){
                                                    if($kind['status'][$sender['hash']]['checked']){
                                                        echo('<input type="checkbox" lay-filter="senderswitch" name="'.$sender['hash'].'|'.$kind['hash'].'|'.$kind['classhash'].'" lay-skin="switch" checked  lay-text="|">');
                                                    }else{
                                                        echo('<input type="checkbox" lay-filter="senderswitch" name="'.$sender['hash'].'|'.$kind['hash'].'|'.$kind['classhash'].'" lay-skin="switch"  lay-text="|">');
                                                    }
                                                }else{
                                                    if($kind['status'][$sender['hash']]['checked']){
                                                        echo('<input type="checkbox" lay-skin="switch" checked disabled lay-text="|">');
                                                    }else{
                                                        echo('<input type="checkbox" lay-skin="switch" disabled  lay-text="|">');
                                                    }
                                                }
                                            }else{
                                                echo('<input type="checkbox" lay-skin="switch" disabled  lay-text="|">');
                                            }
                                        ?>
                                    </td>
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
    layui.form.on('switch(senderswitch)', function(obj){
        layui.admin.req({type:'post',url:"?do={this}:user:senderChange",data:{ hash: obj.elem.name, state: obj.elem.checked},async:true,beforeSend:function(){
            layui.admin.load('请稍等...');
        },done: function(res){
            if (res.error==0)
            {
                layui.layer.msg(res.msg);
            }
        }});
    });
    layui.$('.tips').click(function(){
        layer.tips(layui.$(this).attr('rel'),layui.$(this));
    });
    layui.$('.setbtn').click(function(){
        sender=layui.$(this).attr('data-sender');
        title=layui.$(this).attr('data-title');
        if(layui.$(window).width()<900){ width=layui.$(window).width(); }else{ width=880; }
        if(layui.$(window).height()<700){ height=layui.$(window).height(); }else{ height=680; }
        layui.layer.open({
          type: 2,shadeClose:1,
          title:title,
          area: [width+'px', height+'px'],
          content: '?do={this}:user:set&&sender='+sender
        }); 
    });
});
</script>
{admin:body:~()}
</body>
</html>
