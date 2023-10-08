<?php if(!defined('ClassCms')) {exit();}?>
<style>
    .layadmin-user-login-body .regform .layui-input{padding-left:10px}
    .layadmin-user-login-body .regform .layui-form-label{float:none;text-align:left;padding:9px 15px 9px 5px;width:100%}
    .layadmin-user-login-body .regform .layui-input-right{margin-left:0}
</style>
{loop $forms as $form}
    <div class="layui-form-item regform">
        {if $form.inputhash=='text' || $form.inputhash=='textarea'}
            <div class="layui-input-block">
                {cms:input:form($form)}
            </div>
        {else}
            <label class="layui-form-label">{$form.formname}</label>
            <div class="layui-input-block">
                <div class="layui-input-right">{cms:input:form($form)}</div>
            </div>
        {/if}
    </div>
{/loop}