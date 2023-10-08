<script src="{template}jquery-1.11.1.min.js" charset="utf-8"></script>
<script src="{template}jquery.particleground.min.js" charset="utf-8"></script>
<style> body{height:auto} canvas{position:absolute} </style>
<script> var particlegroundJQ = jQuery.noConflict(true); </script>
<script>
layui.use(['jquery'],function(){
    layui.$('body').css('height',layui.$(document).height()).css('background','{$backgroundcolor}');
    layui.$('.layadmin-user-login-main').css('background','#fff').css('box-shadow','0 0 8px #eeeeee');
    layui.$(window).resize(function() {
        layui.$('body').css('height',layui.$(document).height());
    });
    particlegroundJQ(document).ready(function(){
        particlegroundJQ('body').particleground({
            dotColor:'{$dotcolor}',
            lineColor:'{$linecolor}',
            density:30000
        });
    });
});
</script>