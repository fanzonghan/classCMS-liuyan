<?php if(!defined('ClassCms')) {exit();}?>
<script>
layui.use(['jquery','form','layer'],function(){
    {if $regshow}
        layui.$('button[lay-filter=classcms-reg-submit]').parent().before('<div class="layui-form-item"><input type="checkbox" name="_useragreement" lay-skin="primary" value="1" title="已阅读并同意用户协议"{if config('regagreementchecked')} checked{/if}> <span id="userAgreementBtn" class="cmscolor" style="vertical-align:bottom;cursor:pointer">查看协议</span></div>');
        layui.form.render();
    {/if}
    {if $loginshow}
        layui.$('button[lay-filter=classcms-login-submit]').parent().before('<div class="layui-form-item"><input type="checkbox" name="_useragreement" lay-skin="primary" value="1" title="已阅读并同意用户协议"{if config('regagreementchecked')} checked{/if}> <span id="userAgreementBtn" class="cmscolor" style="vertical-align:bottom;cursor:pointer">查看协议</span></div>');
        layui.form.render();
    {/if}
    {if $loginshow || $regshow}
        layui.$('#userAgreementBtn').click(function(){
            if(layui.$(window).width()>800){ width=750; }else{ width=layui.$(window).width()-20; }
            if(layui.$(window).height()<550){ height=layui.$(window).height()-100; }else{ height=400; }
            layui.layer.open({
            type: 1,
            title: '用户协议',
            area: [width+'px', height+'px'],
            content: layui.$('#userAgreementContent').html()
            });
        });
    {/if}
});
</script>
<div id="userAgreementContent" style="display:none"><div style="padding:5px">{config('agreementcontent')|nl2br()}</div></div>