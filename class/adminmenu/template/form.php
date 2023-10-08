<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>
{if isset($menuname)}
    {admin:head:(菜单管理 编辑)}
{else}
    {admin:head:(菜单管理 增加)}
{/if}
</head>
<body>
    <div class="layui-fluid">
        <div class="layui-row">
        <div class="layui-form">
            {if isset($id)}<input type="hidden" name="id" value="{$id}">{/if}
            <div class="layui-card">
                <div class="layui-card-header">
                    <div class="layui-row">
                        <div id="cms-breadcrumb">{admin:breadcrumb($breadcrumb)}</div>
                        <div id="cms-right-top-button"></div>
                    </div>
                </div>
                <div class="layui-card-body">
                      <div class="layui-form-item layui-form-item-width-auto">
                        <label class="layui-form-label">菜单名称</label>
                            <div class="layui-input-right">
                                <div class="layui-input-block">
                                  <input type="text" name="menuname" value="{if isset($menuname)}{$menuname}{/if}" class="layui-input" lay-verify="required">
                                </div>
                                <div class="layui-form-mid"></div>
                            </div>
                      </div>

                      <div class="layui-form-item">
                        <label class="layui-form-label">菜单图标</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                                {$icon_input_config.name=icon}
                                {if isset($icon)}
                                    {$icon_input_config.value=$icon}
                                {else}
                                   {$icon_input_config.value=''}
                                {/if}
                                {$icon_input_config.inputhash=icon}
                                {cms:input:form($icon_input_config)}
                            </div>
                            <div class="layui-form-mid"></div>
                        </div>
                      </div>

                      <div class="layui-form-item">
                        <label class="layui-form-label">上级菜单</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                                {$fid_input_config.name=fid}
                                {if isset($fid)}
                                    {$fid_input_config.value=$fid}
                                {else}
                                   {$fid_input_config.value=''}
                                {/if}
                                {$fid_input_config.table='adminmenu'}
                                {$fid_input_config.titlecolumn='menuname'}
                                {$fid_input_config.order='menuorder desc,id asc'}
                                {$fid_input_config.inputhash=databasetree}
                                {cms:input:form($fid_input_config)}
                            </div>
                            <div class="layui-form-mid"></div>
                        </div>
                      </div>

                      <div class="layui-form-item">
                        <label class="layui-form-label">是否启用</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                                {$enabled_input_config.name=enabled}
                                {if isset($enabled)}
                                    {$enabled_input_config.value=$enabled}
                                {else}
                                   {$enabled_input_config.value=1}
                                {/if}
                                {$enabled_input_config.inputhash=switch}
                                {cms:input:form($enabled_input_config)}
                            </div>
                            <div class="layui-form-mid"></div>
                        </div>
                      </div>

                      <div class="layui-form-item">
                        <label class="layui-form-label">类型</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                                <input type="radio" name="kind" lay-filter="kind" value="channel" title="栏目管理"{if isset($kind) && $kind=="channel"} checked{/if}>
                                <input type="radio" name="kind" lay-filter="kind" value="url" title="链接地址"{if isset($kind) && $kind=="url"} checked{/if}>
                                <input type="radio" name="kind" lay-filter="kind" value="class" title="应用菜单"{if isset($kind) && $kind=="class"} checked{/if}>
                                <input type="radio" name="kind" lay-filter="kind" value="diy" title="自定义"{if isset($kind) && $kind=="diy"} checked{/if}>
                            </div>
                            <div class="layui-form-mid"></div>
                        </div>
                      </div>

                      <div class="layui-form-item layui-form-item-width-auto kindvaluediv kindvalue_channel" style="display:none">
                        <label class="layui-form-label">栏目ID</label>
                            <div class="layui-input-right">
                                <div class="layui-input-block">
                                  {cms:input:form($channelinput)}
                                </div>
                                <div class="layui-form-mid">请选择菜单需要链接到的栏目</div>
                            </div>
                      </div>

                      <div class="layui-form-item layui-form-item-width-auto kindvaluediv kindvalue_url" style="display:none">
                        <label class="layui-form-label">链接地址</label>
                            <div class="layui-input-right">
                                <div class="layui-input-block">
                                  <input type="text" name="kindvalue_url" value="{if isset($kind) && $kind=="url"}{htmlspecialchars($kindvalue)}{/if}" class="layui-input">
                                </div>
                                <div class="layui-form-mid">需要链接到的地址,后台地址为?do=xxx:xxx:xxx形式</div>
                            </div>
                      </div>

                      <div class="layui-form-item layui-form-item-width-auto kindvaluediv kindvalue_url" style="display:none">
                        <label class="layui-form-label">新窗口</label>
                            <div class="layui-input-right">
                                <div class="layui-input-block">
                                    {$target_input_config.name=target}
                                    {if isset($target)}
                                        {$target_input_config.value=$target}
                                    {else}
                                    {$target_input_config.value=0}
                                    {/if}
                                    {$target_input_config.inputhash=switch}
                                    {cms:input:form($target_input_config)}
                                </div>
                                <div class="layui-form-mid">在新窗口中打开链接</div>
                            </div>
                      </div>

                      <div class="layui-form-item layui-form-item-width-auto kindvaluediv kindvalue_class" style="display:none">
                        <label class="layui-form-label">应用</label>
                            <div class="layui-input-right">
                                <div class="layui-input-block">
                                    {cms:input:form($classselectinput)}
                                </div>
                                <div class="layui-form-mid">显示此应用的后台菜单</div>
                            </div>
                      </div>

                      <div class="layui-form-item layui-form-item-width-auto kindvaluediv kindvalue_diy" style="display:none">
                        <label class="layui-form-label">方法名</label>
                            <div class="layui-input-right">
                                <div class="layui-input-block">
                                  <input type="text" name="kindvalue_diy" value="{if isset($kind) && $kind=="diy"}{htmlspecialchars($kindvalue)}{/if}" class="layui-input">
                                </div>
                                <div class="layui-form-mid">方法内返回菜单数组,如:admin:menu,具体详见 <a href="http://classcms.com/class/cms/doc/10039.html" class="cmscolor" target="_blank">后台菜单说明</a></div>
                            </div>
                      </div>

                      <div class="layui-form-item">
                        <label class="layui-form-label">权限</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                                {$auth_input_config.name=auth}
                                {$auth_input_config.value=$auth}
                                {$auth_input_config.inputhash=rolecheckbox}
                                {cms:input:form($auth_input_config)}
                            </div>
                            <div class="layui-form-mid">勾选角色后对应的用户才显示此菜单</div>
                        </div>
                      </div>
                </div>
            </div>
            <div class="layui-form-item layui-layout-admin">
                <div class="layui-input-block">
                    <div class="layui-footer">
                    <button class="layui-btn layui-btn-normal cms-btn" lay-submit="" lay-filter="form-submit">{if isset($menuname)}保存{else}增加{/if}</button>
                    <button type="button" class="layui-btn layui-btn-primary" layadmin-event="back">返回</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
<script>layui.use(['index'],function(){
    function changekind(){
        kind=layui.$('input[name=kind]:checked').val();
        layui.$('.kindvaluediv').hide();
        layui.$('.kindvalue_'+kind).show();
        layui.form.render('radio');
    }
    layui.form.on('radio(kind)', function(data){
        changekind();
    });
    changekind();
    layui.form.on('submit(form-submit)', function(data){
        layui.$('button[lay-filter=form-submit]').blur();
        layui.admin.req({type:'post',url:"?do=adminmenu:{if isset($id)}editPost{else}addPost{/if}",data:data.field,async:true,beforeSend:function(){
            layui.admin.load('提交中...');
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