<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(详情)}
<style>
    .layui-card-header span{font-size:0.8em;color:#ccc}
</style>
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-card-header">{$title} <span>{date(Y-m-d H:i:s,$addtime)}</span> </div>
            <div class="layui-card-body">{$content}</div>
        </div>
     </div>
</div>
{admin:body:~()}
</body>
</html>
