<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>找回密码</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    {layui:css()}
    {layui:js()}
    {admin:css()}
    <script>layui.config({base: '{template_url(admin)}static/'}).extend({index: 'lib/index'}).use(['index','form'],function(){});</script>
</head>
<body>
<div class="layadmin-user-login layadmin-user-display-show">
    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <h2 class="cmscolor">找回密码</h2>
            <p>{config(forgottips)|nl2br()}</p>
        </div>
        <div class="layadmin-user-login-box layadmin-user-login-body layui-form" lay-filter="classcms-forgot-form">
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-username"></label>
                <input type="text" name="userhash" value="" placeholder="账号" class="layui-input">
            </div>
            <div class="layui-form-item forgotkind"></div>
            {this:forgotFormitem:~()}
            <div class="layui-form-item" style="margin-top: 20px;">
                <button class="layui-btn layui-btn-fluid cms-btn" lay-submit lay-filter="classcms-forgot-submit">找 回</button>
            </div>
            <div class="layui-trans layui-form-item layadmin-user-login-other">
            {this:forgotIco:~()} 
            {if config('reg')}
            <a href="{this:regLink()}" class="layadmin-user-jump-change layadmin-link">注册账号</a>
            {/if}
            <a href="{this:loginLink()}" class="layadmin-user-jump-change layadmin-link">登入账号</a>
            </div>
        </div>
    </div>
    {admin:loginCopyright()}
</div>
<script>
layui.use(['form'],function(){
    function forgotpost(){
        var data = layui.form.val("classcms-forgot-form");
        layui.admin.req({type:'post',url:"?do=user:forgot:post",data:data,async:true,beforeSend:function(){
            layui.admin.load('提交中...');
        },done: function(res){
            if (res.error==0)
            {
                var confirm=layer.confirm(res.msg, {btn: ['好的','返回'],shadeClose:1},function(){window.location='{this:loginLink()}';},function(){
                    layui.layer.close(confirm);
                    });
            }
        }});
        return false;
    }
    layui.form.on('submit(classcms-forgot-submit)', function(data){forgotpost();});
});
</script>
{this:forgotBody:~()}
</body>
</html>