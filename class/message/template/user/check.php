<script>
layui.use(['index'],function(){
    function message_check()
    {
        layui.admin.req({type:'post',url:"?do={this}:user:check",data:{ check:0},async:true,done: function(res){
            if (res.error==0)
            {
                if(res.newmsg){
                    layui.$('#message_notice_btn').css('visibility','visible');
                }else{
                    layui.$('#message_notice_btn').css('visibility','hidden');
                }
            }
        }});
    }
    self.setInterval(message_check,10000);
});
</script>