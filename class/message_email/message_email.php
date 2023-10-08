<?php
if(!defined('ClassCms')) {exit();}
class message_email {
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'all','hookedfunction'=>'message:sender:all:=','enabled'=>1);
        Return $hooks;
    }
    function all($class,$args,$return){
        $return[]=array(
            'title'=>'邮箱',
            'hash'=>'email',
            'classhash'=>I(),
            'classfunction'=>I().':sender',
            'userconfig'=>array(
                                array('configname'=>'接收邮箱','hash'=>'email','inputhash'=>'text','tips'=>'接收此消息邮件的邮箱地址','defaultvalue'=>'')
            )
        );
        return $return;
    }
    function sender($config=array()) {
        if(isset($config['email']) && $config['email']){
            $config=array('title'=>$config['message']['title'],'to'=>$config['email'],'content'=>$config['message']['content']);
            Return C('email:send',$config);
        }
        return false;
    }
}