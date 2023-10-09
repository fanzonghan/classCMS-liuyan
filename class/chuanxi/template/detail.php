<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(详情)}</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-card-body">
            <table class="layui-table" lay-skin="line">
            <tbody>
                <tr><td style="width:60px;text-align:right">标题:</td><td>{$title}</td></tr>
                <tr><td style="width:60px;text-align:right">时间:</td><td>{date(y-m-d H:i:s,$posttime)}</td></tr>
                <tr><td style="width:60px;text-align:right">状态:</td><td>{if $state}发送成功{else}发送失败<br>{if isset($error)}{$error}{/if}{/if}</td></tr>
                <tr><td colspan="2"><br>{htmlspecialchars($content)}<br></td></tr>
            </tbody>
            </table>
          </div>
        </div>
     </div>
</div>
{admin:body:~()}
</body>
</html>
