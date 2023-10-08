<?php
if(!defined('ClassCms')) {exit();}
class cravatar {
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'avatar','hookedfunction'=>'comment:mail2avatar','enabled'=>1,'requires'=>'');
        Return $hooks;
    }
    function config() {
        $configs=array();
        $configs[]=array('configname'=>'头像宽度','hash'=>'width','inputhash'=>'number','tabname'=>'','defaultvalue'=>'120','tips'=>'返回的头像宽度与高度');
        $configs[]=array('configname'=>'默认头像','hash'=>'default','inputhash'=>'radio','tabname'=>'','defaultvalue'=>'1','values'=>"0:默认\n1:灰色头像\n2:几何图案\n3:像素人脸\n4:机器人",'savetype'=>1,'tips'=>'如果不能解析邮箱对应的头像,返回默认的头像类型.');
        Return $configs;
    }
    function avatar($mail){
        if(!G('default')){ G('default',config('default')); }
        if(!G('width')){ G('width',config('width')); }
        $str='?s='.G('width');
        if(G('default')==0){
            if(!G('avatar')){
                if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=='on'){
                    G('avatar','https://'.C('cms:common:serverName').C('cms:common:serverPort').C('cms:config:get','avatar','comment'));
                }else{
                    G('avatar','http://'.C('cms:common:serverName').C('cms:common:serverPort').C('cms:config:get','avatar','comment'));
                }
            }
            $str.='&d='.G('avatar');
        }elseif(G('default')==1){
            $str.='&d=mp';
        }elseif(G('default')==2){
            $str.='&d=identicon';
        }elseif(G('default')==3){
            $str.='&d=retro';
        }elseif(G('default')==4){
            $str.='&d=robohash';
        }
        $mailhash=md5(strtolower(trim($mail)));
        return 'https://cravatar.cn/avatar/' . $mailhash.$str;
    }
}