<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0">
    <title>{if isset($.title) && !empty($.title)}{$.title}{else}{$.channelname}{/if}</title>
    {if isset($.keywords)}<meta name="keywords" content="{$.keywords}">{br}{/if}
    {if isset($.description)}<meta name="description" content="{$.description}">{br}{/if}
    {layui:css()}
    {comment:css()}
    {comment:js()}
</head>
<body>
<div id="comment"></div>
{$config=array()}
{$config.el=#comment}
{comment:code($config)}
</body>
</html>
