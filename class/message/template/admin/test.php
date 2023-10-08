<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(测试)}</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-form">

            <div class="layui-card">
                <div class="layui-card-body clear">



                    <div class="layui-form-item layui-form-item-width-auto">
                        <label class="layui-form-label">标题</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                                <input type="text" name="title" value="测试标题_{rand(10000,99999)}" class="layui-input" lay-verify="required">
                            </div>
                            <div class="layui-form-mid"></div>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">内容</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                            <textarea class="layui-textarea" name="content" placeholder="">测试内容_{rand(10000,99999)}</textarea>
                            </div>
                            <div class="layui-form-mid"></div>
                        </div>
                    </div>

                    <div class="layui-form-item layui-form-item-width-auto">
                        <label class="layui-form-label">用户</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                                <input type="text" name="user" value="{$user.hash}" class="layui-input">
                            </div>
                            <div class="layui-form-mid">目标用户的id或账号<br>不填写则按照"默认接收账号"发送消息通知</div>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">类型</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                            <select name="kind">
                            <option value="">请选择</option>
                            {loop $groups as $group}
                                {if $group}<option value="" disabled>----{$group}--------</option>{/if}
                                {loop $kinds as $kind}
                                    {if $kind['groupname']==$group}
                                        <option value="{$kind.classhash}|{$kind.hash}">{$kind.title}</option>
                                    {/if}
                                {/loop}
                            {/loop}
                            </select>
                            </div>
                            <div class="layui-form-mid">选择对应的消息类型后消息通知将按照用户选择的通知方式发送<br>不选消息类型则按照"默认通知方式"发送消息通知</div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="layui-form-item layui-layout-admin">
                <div class="layui-input-block">
                    <div class="layui-footer">
                        <button class="layui-btn cms-btn" lay-submit="" lay-filter="form-submit">发送</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
  
<script>layui.use(['index'],function(){
    layui.form.on('submit(form-submit)', function(data){
        layui.$('button[lay-filter=form-submit]').blur();
        layui.admin.req({type:'post',url:"?do={this}:admin:testPost",data:data.field,async:true,beforeSend:function(){
            layui.admin.load('提交中...');
        },done: function(res){
            if (res.error==0)
            {
                var confirm=layer.confirm(
                        res.msg,
                        {btn: ['好的'],shadeClose:1},
                        function(){
                            layui.layer.close(confirm);
                        }
                    );
            }
        }});
      return false;
    });
});
</script>
{admin:body:~()}
</body>
</html>


