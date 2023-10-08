<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(代码)}</head>
<body>

<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-card-header">模板调用示例</div>
            <div class="layui-card-body">
<textarea style="width:100%;height:200px">
<html>
    <head>
        <title>Comment</title>
        <?php echo('{');?>comment:css()<?php echo('}');?>{br}
        <?php echo('{');?>comment:js()<?php echo('}');?>{br}
    </head>
    <body>
        <div id="comment"></div>
        <?php echo('{');?>$config=array()<?php echo('}');?>{br}
        <?php echo('{');?>$config.el=#comment<?php echo('}');?>{br}
        <?php echo('{');?>comment:code($config)<?php echo('}');?>{br}
    </body>
</html>
</textarea>

            </div>
        </div>
        <div class="layui-card">
            <div class="layui-card-header">第三方调用示例</div>
            <div class="layui-card-body">
<textarea style="width:100%;height:200px">
<html>
    <head>
        <title>Comment</title>
        {comment:css()}{br}
        {comment:js()}{br}
    </head>
    <body>
        <div id="comment"></div>
        <script>Waline.init({json_encode($config)});</script>
    </body>
</html>
</textarea>

<blockquote class="layui-elem-quote layui-text">
参数介绍详见:<a href="https://waline.js.org/reference/component.html" target="_blank" class="cmscolor" >https://waline.js.org/reference/component.html</a>
</blockquote>
            </div>
        </div>
    </div>
</div>


{admin:body:~()}
</body>
</html>
