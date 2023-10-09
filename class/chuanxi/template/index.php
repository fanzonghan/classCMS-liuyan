<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(发送记录)}</head>
<body>
  <div class="layui-fluid">
    <div class="layui-row">

<div class="layui-form">
    <div class="layui-card">
        <div class="layui-card-header">
            <div class="layui-row">
                <?php
                    $breadcrumb=array(
                        array('url'=>'?do=admin:class:config&hash='.I(),'title'=>'传息'),
                        array('title'=>'发送记录')
                    );
                ?>
                <div id="cms-breadcrumb">{admin:breadcrumb($breadcrumb)}</div>
                <div id="cms-right-top-button">
                {if P('admin:test')}<a href="?do={this}:admin:test" class="layui-btn layui-btn-sm layui-btn-danger"><i class="layui-icon layui-icon-release"></i><b>测试</b></a>{/if}
                {if P('admin:del')}<a class="layui-btn layui-btn-sm layui-btn-danger clean"><i class="layui-icon layui-icon-delete"></i><b>清空</b></a>{/if}
                </div>
            </div>
        </div>
<div class="layui-card-body">
    {if $tips}
        <blockquote class="layui-elem-quote layui-text">
            {$tips}
        </blockquote>
    {/if}
    <table class="layui-table" lay-skin="line" id="articles">
    <thead>
      <tr>
        <th>标题</th>
        <th style="width:150px">日期</th>
        <th></th>
      </tr> 
    </thead>
    <tbody>
    {loop $logs as $log}
        <tr rel="{$log.id}">
            <td><span{if !$log.state} class="cms-text-disabled"{/if}>{$log.title}</span>{if !$log.state}[错误]{/if}</td>
            <td>{date(y-m-d H:i:s,$log.posttime)}</td>
            <td class="btn">
                {if P('admin:detail')}<a class="layui-btn layui-btn-sm layui-btn-primary logdetail">详情</a>{/if}
                {if P('admin:del')}<a class="layui-btn layui-btn-sm layui-btn-primary logdel">删除</a>{/if}
            </td>
        </tr>
    {/loop}
    </tbody>
  </table>
    <div class="layui-row">
        <div id="cms-left-bottom-button" class="layui-btn-container"></div>
        <div id="cms-right-bottom-button" class="layui-btn-container">
            {admin:pagelist()}
        </div>
    </div>
</div>

    </div>


     </div>
  </div>
  </div>
  
<script>layui.use(['index'],function(){
    layui.$('.logdel').click(function(){
        logid=layui.$(this).parents('tr').attr('rel');
        layui.layer.confirm('是否删除此记录', {
          btn: ['删除','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:admin:del",data:{ id: logid},async:true,beforeSend:function(){
                layui.admin.load('删除中...');
            },done: function(res){
                if (res.error==0)
                {
                    layui.layer.msg(res.msg);
                    layui.$('tr[rel='+logid+']').remove();
                }
            }});
        });
    });
    layui.$('.clean').click(function(){
        layui.layer.confirm('是否清空所有发送记录!!!', {
          btn: ['清空','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:admin:clean",data:{ id: 0},async:true,beforeSend:function(){
                layui.admin.load('清空中...');
            },done: function(res){
                if (res.error==0)
                {
                    layui.admin.events.reload();
                }
            }});
        });
    });
    layui.$('.logdetail').click(function(){
        logid=layui.$(this).parents('tr').attr('rel');
        layui.layer.open({
          type: 2,
              shade: 0,title:'详情',
          area: ['380px', '400px'],
          content: '?do={this}:admin:detail&id='+logid
        }); 
    });
});
</script>
{admin:body:~()}
</body>
</html>
