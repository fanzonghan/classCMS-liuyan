<?php if(!defined('ClassCms')) {exit();}?>
<style> #captcha{display:inline-block;padding-left:10px;margin-right:10px;width:120px} </style>
<div class="layui-form-item">
    {captcha:html()}
    <script>
        layui.use(['jquery'],function(){
            layui.$('#captcha').attr('placeholder','验证码');
            layui.$('#captcha_image').css('cursor','pointer').attr('rel',layui.$('#captcha_image').attr('src'));
            layui.$('#captcha_image').click(function(){
                layui.$('#captcha_image').attr('src',layui.$('#captcha_image').attr('rel')+'?'+Math.round(Math.random()*100000000))
            });
        });
    </script>
</div>