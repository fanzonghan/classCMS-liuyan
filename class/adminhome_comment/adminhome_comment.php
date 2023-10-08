<?php
class adminhome_comment {
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'adminHomeKind','hookedfunction'=>'adminhome:kind:all:=');
        Return $hooks;
    }
    function adminHomeKind($class,$args,$return){
        $return[]=array('name'=>'最新评论','hash'=>'comment','groupname'=>'评论','function'=>'adminhome_comment:comment','config'=>array(array('configname'=>'尺寸','hash'=>'size','inputhash'=>'text','tips'=>'自定义组件尺寸,如:3*2 最小:1*1  最大6*4','defaultvalue'=>'3*3'),array('configname'=>'数量','hash'=>'pagesize','inputhash'=>'number','tips'=>'请填写需要显示的评论数量','defaultvalue'=>'12')));
        return $return;
    }
    function comment($config=array()) {
        $query=array();
        $query['table']='comment';
        $query['optimize']=true;
        $query['page']=page('pagesize',$config['pagesize']);
        $query['order']='addtime desc';
        $comments=all($query);

        $config['link']='?do=comment:admin:index';
        $config['linktitle']='更多';
        $config['title']='最新评论';
        $config['content']='<table class="layui-table"lay-size="sm" style="width:100%"><tbody> ';
        foreach ($comments as $comment) {
            $config['content'].='<tr><td style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">';
            $content='['.date('m-d H:i',$comment['addtime']).'] '.C('cms:common:text',$comment['content'],100);
            if(!$content){
                $content='[无文字内容]';
            }
            if(!$comment['status']){
                $content='[待审核]'.$content;
            }
            $config['content'].='<a class="cmscolor" style="cursor:pointer" rel="'.$comment['id'].'">'.$content.'</a></td></tr>';
        }
        $config['content'].='</tbody> </table><script> layui.use(["index"],function(){ layui.$("#cards>div[rel='.$config['id'].'] .layui-card-body").on("click","a",function(){ id=layui.$(this).attr("rel"); if(layui.$(window).width()<900){ width=layui.$(window).width(); }else{ width=780; } if(layui.$(window).height()<700){ height=layui.$(window).height(); }else{ height=480; } layui.layer.open({ type: 2, shade: 0, title:"详情", shade:0.2, shadeClose:true, area: [width+"px", height+"px"], content: "?do=comment:admin:detail&id="+id}); }); }); </script>';
        return $config;
    }
}