<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>
{admin:head(增加组件)}
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
{loop $groups as $key=> $kind}
        <div class="layui-card">
            {if $key}<div class="layui-card-header">{$key}</div>{/if}
            <div class="layui-card-body">
                <div class="layui-row layui-btn-container">
                    {loop $kinds as $kind}
                    <?php
                        if(!isset($kind['groupname'])){
                            $kind['groupname']='';
                        }
                    ?>
                    {if $key==$kind['groupname']}
                    <a class="layui-btn layui-btn-sm layui-btn-primary" href="?do={this}:manage:addSet&hash={$kind.hash}"><i class="layui-icon layui-icon-add-1"></i><b>{$kind.name}</b></a>
                    {/if}
                    {/loop}
                </div>
            </div>
        </div>
{/loop}

    </div>
</div>
{admin:body:~()}

</body>
</html>