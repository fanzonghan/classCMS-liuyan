<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>注册账号</title>
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
            <h2 class="cmscolor">注册账号</h2>
            <p>{config(regtips)|nl2br()}</p>
        </div>
        <div class="layadmin-user-login-box layadmin-user-login-body layui-form" lay-filter="classcms-reg-form">
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-username"></label>
                <input type="text" name="userhash" value="" lay-verify="hash" placeholder="账号" class="layui-input">
            </div>
            {this:regFormitem:~()}
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-password"></label>
                <input type="password" name="passwd" value="" lay-verify="required" placeholder="密码" class="layui-input">
            </div>
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-password"></label>
                <input type="password" name="passwd2" value="" lay-verify="required" placeholder="重复密码" class="layui-input">
            </div>
            {this:regFormitem2:~()}
            {this:reg:showInput()}
            {this:regFormitem3:~()}
            <div class="layui-form-item" style="margin-top: 20px;">
                <button class="layui-btn layui-btn-fluid cms-btn" lay-submit lay-filter="classcms-reg-submit">注 册</button>
            </div>
            <div class="layui-trans layui-form-item layadmin-user-login-other">
            {this:regIco:~()}
            {if config('forgot')}
            <a href="{this:forgotLink()}" class="layadmin-user-jump-change layadmin-link">找回密码</a>
            {/if}
            <a href="{this:loginLink()}" class="layadmin-user-jump-change layadmin-link">登入账号</a>
            </div>
        </div>
    </div>
    {admin:loginCopyright()}
</div>
<script>
layui.use(['form'],function(){
    function regpost(){
        var data = layui.form.val("classcms-reg-form");
        layui.admin.req({type:'post',url:"?do=user:reg:post",data:data,async:true,beforeSend:function(){
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
    layui.form.on('submit(classcms-reg-submit)', function(data){regpost();});
});
</script>
{this:regBody:~()}
</body>
</html>