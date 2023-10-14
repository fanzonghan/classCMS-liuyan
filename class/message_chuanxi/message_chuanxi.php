<?php
if(!defined('ClassCms')) {exit();}
class message_chuanxi {
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'all','hookedfunction'=>'message:sender:all:=','enabled'=>1);
        Return $hooks;
    }
    function all($class,$args,$return){
        $return[]=array(
            'title'=>'传息',
            'hash'=>'chuanxi',
            'classhash'=>I(),
            'classfunction'=>I().':sender',
            'userconfig'=>array(
                                array('configname'=>'appKey','hash'=>'appkey','inputhash'=>'text','tips'=>'<a href="https://cx.qingsonge.com/#" class="layui-btn">前往登录</a> <a style="color: red">获取appKey</a><br><iframe src="https://cx.qingsonge.com/#" width="600px" height="500px"></iframe>','defaultvalue'=>''),
            )
        );
        return $return;
    }
    function sender($config=array()) {
        if(isset($config['appkey']) && $config['appkey']){
            $config=array('title'=>$config['message']['title'],'appkey'=>$config['appkey'],'content'=>$config['message']['content']);
            Return C('chuanxi:send',$config);
        }
        return false;
    }
}