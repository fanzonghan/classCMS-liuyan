<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(验证码预览)}</head>
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
                <div class="layui-card-body">
                    {if !function_exists("gd_info")}
                    <blockquote class="layui-elem-quote layui-text">
                        gd库未开启,无法使用本应用
                    </blockquote>
                    {else}
                        验证码:&nbsp;&nbsp;&nbsp;&nbsp;{this:html(`0)} <a class="layui-btn layui-btn-sm cms-btn" layadmin-event="refresh">刷新</a>
                    {/if}
                </div>
            </div>
         </div>
  </div>
  </div>
{admin:body:~()}
</body>
</html>
