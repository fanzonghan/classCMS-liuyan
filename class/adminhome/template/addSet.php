<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(增加组件)}</head>
<body>
{if !count($configs)}
<script>
    parent.buildCard('{$id}');
    parent.layer.close(parent.layer.getFrameIndex(window.name));
</script>
{else}
<div class="layui-fluid layui-form">
<input type="hidden" name="kindhash" value="{$kind.hash}">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-card-header">{$kind.name}</div>
            <div class="layui-card-body" style="padding-bottom:50px">
                {loop $configs as $config}
                    <div class="layui-form-item">
                        <label class="layui-form-label">{$config.configname}</label>
                        <div class="layui-input-right">
                        <div class="layui-input-block">
                            {cms:input:form($config)}
                        </div>
                        <div class="layui-form-mid">{$config.tips}</div>
                        </div>
                    </div>
                {/loop}
          </div>
          
        </div>
        <div class="layui-form-item layui-layout-admin">
            <div class="layui-input-block">
                <div class="layui-footer">
                    <button class="layui-btn cms-btn" lay-submit="" lay-filter="form-submit">增加</button>
                    <button type="button" class="layui-btn layui-btn-primary closebtn">关闭</button>
                </div>
            </div>
          </div>
     </div>
</div>
<script>
layui.use(['index'],function(){
    layui.form.on('submit(form-submit)', function(data){
        layui.$('button[lay-filter=form-submit]').blur();
        layui.admin.req({type:'post',url:"?do={this}:manage:addPost",data:data.field,async:true,beforeSend:function(){
            layui.admin.load('保存中...');
        },done: function(res){
            if (res.error==0)
            {
                parent.buildCard(res.id);
                parent.layer.close(parent.layer.getFrameIndex(window.name));
            }
        }});
      return false;
    });
    layui.$('.closebtn').click(function(){
        parent.layer.close(parent.layer.getFrameIndex(window.name));
    });
});
</script>
{/if}
{admin:body:~()}
</body>
</html>
