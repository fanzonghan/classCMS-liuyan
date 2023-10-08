<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(评论演示)}{comment:css()}{comment:js()}</head>
<body style="background:#fff">
<div id="msg" style="padding:20px"></div>

{$config=array()}
{$config.el=#msg}
{comment:code($config)}

{admin:body:~()}
</body>
</html>
