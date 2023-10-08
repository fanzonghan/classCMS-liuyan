<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>
{admin:head(后台主页)}
<link rel="stylesheet" href="{template}css.css" media="all">
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-card-header">
                <div class="layui-row">
                    <div id="cms-breadcrumb">{admin:breadcrumb()}</div>
                    <div id="cms-right-top-button">
                    {if P('manage:add')}
                        <a class="layui-btn layui-btn-sm layui-btn-primary add_btn" style="display:none"><i class="layui-icon layui-icon-add-1"></i><b>组件</b></a>
                        <a class="layui-btn layui-btn-sm layui-btn-primary diy_btn" rel="0"><i class="layui-icon layui-icon-edit"></i><b>自定义</b></a>
                    {/if}
                    </div>
                </div>
            </div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space20" id="cards">
                    {loop $cards as $card}
                    {this:card:build($card)}
                    {/loop}
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{template}js.js"></script>
{admin:body:~()}
</body>
</html>