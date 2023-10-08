function buildCard(id){
    layui.admin.req({type:'post',url:"?do=adminhome:manage:build",data:{id:id},async:true,beforeSend:function(){
            layui.admin.load('加载中...');
        },done: function(res){
            if (res.error==0)
            {
                if(layui.$('#cards>div[rel='+id+']').length){
                    layui.$('#cards>div[rel='+id+']').after(res.html).remove();
                }else{
                    layui.$('#cards').append(res.html);
                }
                resetState();
            }
        }});
}
function resetState(){
    if(layui.$('.diy_btn').attr('rel')==1){
        layui.$('#cards p.action a.move').remove();
        layui.$('#cards p.action a.set').remove();
        layui.$('#cards p.action a.close').remove();
        layui.$('#cards p.action').append('<a class="move"><i class="layui-icon layui-icon-find-fill"></i></a> <a class="set"><i class="layui-icon layui-icon-set-fill"></i></a> <a class="close"><i class="layui-icon layui-icon-close"></i></a>');
    }else{
        layui.$('#cards p.action a.move').remove();
        layui.$('#cards p.action a.set').remove();
        layui.$('#cards p.action a.close').remove();
    }
}
layui.use(['index','sortable'],function(){
layui.$('.diy_btn').click(function(){
    if(layui.$(this).attr('rel')==0){
        layui.$(this).attr('rel',1);
        layui.$(this).find('b').text('完成');
        layui.$('.add_btn').show();
    }else{
        layui.$(this).attr('rel',0);
        layui.$(this).find('b').text('自定义');
        layui.$('.add_btn').hide();
    }
    resetState();
});

layui.$('.add_btn').click(function(){
    if(layui.$(window).width()<900){ width=layui.$(window).width(); }else{ width=880; }
    if(layui.$(window).height()<700){ height=layui.$(window).height(); }else{ height=680; }
    layui.layer.open({
      type: 2,shadeClose:1,
      title:'增加组件',
      area: [width+'px', height+'px'],
      content: '?do=adminhome:manage:add'
    }); 
});

layui.$('#cards').on('click','.close',function(){
    thisparent=layui.$(this).parent().parent().parent().parent();
    id=layui.$(this).parent().parent().parent().parent().attr('rel');
    layui.layer.confirm('是否删除此组件', {
        btn: ['删除','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
        layui.admin.req({type:'post',url:"?do=adminhome:manage:del",data:{ id:id},async:true,beforeSend:function(){
            layui.admin.load('删除中...');
        },done: function(res){
            if (res.error==0)
            {
                layui.layer.msg(res.msg);
                thisparent.remove();
            }
        }});
    });
});

layui.$('#cards').on('click','.set',function(){
    thisparent=layui.$(this).parent().parent().parent().parent();
    id=layui.$(this).parent().parent().parent().parent().attr('rel');
    if(layui.$(window).width()<900){ width=layui.$(window).width(); }else{ width=880; }
    if(layui.$(window).height()<700){ height=layui.$(window).height(); }else{ height=680; }
    layui.layer.open({
      type: 2,shadeClose:1,
      title:'配置组件',
      area: [width+'px', height+'px'],
      content: '?do=adminhome:manage:set&id='+id
    }); 
});


new Sortable(cards, {
    handle: '.move',
    onSort: function (evt) {
        cardsarray='';
        layui.$('#cards>div').each(function(){
            cardsarray=cardsarray+'|'+layui.$(this).attr('rel');
        });
        layui.admin.req({type:'post',url:"?do=adminhome:manage:order",data:{ cardsarray: cardsarray},async:true,beforeSend:function(){
            layui.admin.load('修改排序中...');
        },done: function(res){
            
        }});
    }
});

});