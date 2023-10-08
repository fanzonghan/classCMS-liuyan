<?php if(!defined('ClassCms')) {exit();}?>
<script>
layui.use(['index'],function(){
    layui.$('#config td').eq(1).append('<input type="checkbox" name="template" title="栏目菜单" lay-filter="channelmenu" lay-skin="primary"{if $checked} checked{/if}>');
    layui.form.render('checkbox');
    layui.form.on('checkbox(channelmenu)', function(obj){
        layui.admin.req({type:'post',url:"?do={this}:editpost",data:{ hash: obj.elem.name, state: obj.elem.checked,hash: '{$hash}'},async:true,beforeSend:function(){
            layui.admin.load('请稍等...');
        },done: function(res){
            if (res.error==0)
            {
                var confirm=layer.confirm(res.msg, {btn: ['好的'],shadeClose:1,end :function(){layui.admin.events.loadmenu();}}, function(){layui.layer.close(confirm);});
            }
        }});
    });
    
});
</script>



