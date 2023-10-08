<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(设置)}</head>
<body>
<div class="layui-fluid  layui-form">
<input type="hidden" name="_sender" value="{$sender.hash}">
<input type="hidden" name="_kind" value="{$kind.hash}">
<input type="hidden" name="_classhash" value="{$kind.classhash}">
    <div class="layui-row">
        <div class="layui-card">
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
            {if count($roles)}
                <div class="layui-form-item">
                    <label class="layui-form-label">权限</label>
                    <div class="layui-input-right">
                    <div class="layui-input-block">
                        {loop $roles as $role}
                            {if $admin_role_name==$role.hash}
                                <input type="checkbox"  title="{$role.rolename}[{$role.hash}]" name="role_{$role.hash}" lay-skin="primary" checked disabled>
                            {else}
                                <input type="checkbox"  title="{$role.rolename}[{$role.hash}]" name="role_{$role.hash}" lay-skin="primary" {if C('admin:roleCheck',$authhash,$role.hash,false)} checked{/if}>
                            {/if}
                        {/loop}
                    </div>
                    <div class="layui-form-mid">是否允许对应角色使用此通知方式</div>
                    </div>
                </div>
            {/if}
            <div class="layui-form-item">
                <label class="layui-form-label">默认</label>
                <div class="layui-input-right">
                <div class="layui-input-block">
                {$default_input_config.name=_default}
                {$default_input_config.value=$_default}
                {$default_input_config.inputhash=switch}
                {cms:input:form($default_input_config)}
                </div>
                <div class="layui-form-mid">默认是否启用此通知方式</div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">修改</label>
                <div class="layui-input-right">
                <div class="layui-input-block">
                {$editable_input_config.name=_editable}
                {$editable_input_config.value=$_editable}
                {$editable_input_config.inputhash=switch}
                {cms:input:form($editable_input_config)}
                </div>
                <div class="layui-form-mid">是否允许开启或关闭此通知方式,关闭后则由"默认"是否启用决定</div>
                </div>
            </div>
          </div>
          
        </div>
        <div class="layui-form-item layui-layout-admin">
            <div class="layui-input-block">
                <div class="layui-footer">
                <button class="layui-btn cms-btn" lay-submit="" lay-filter="form-submit">保存</button>
                <button type="button" class="layui-btn layui-btn-primary closebtn">关闭</button>            </div>
            </div>
          </div>
     </div>
</div>
<script>
layui.use(['index'],function(){
    layui.$('.tips').click(function(){
        layer.tips(layui.$(this).attr('rel'),layui.$(this));
    });
    layui.form.on('submit(form-submit)', function(data){
        layui.$('button[lay-filter=form-submit]').blur();
        layui.admin.req({type:'post',url:"?do={this}:admin:setPost",data:data.field,async:true,beforeSend:function(){
            layui.admin.load('保存中...');
        },done: function(res){
            if (res.error==0)
            {
                var confirm=layer.confirm(res.msg, {btn: ['好的','返回'],shadeClose:1},function(){layui.admin.events.reload();},function(){
                     parent.layer.close(parent.layer.getFrameIndex(window.name));
                 });
            }
        }});
      return false;
    });
    layui.$('.closebtn').click(function(){
        parent.layer.close(parent.layer.getFrameIndex(window.name));
    });
});
</script>
{admin:body:~()}
</body>
</html>