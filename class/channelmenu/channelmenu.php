<?php
if(!defined('ClassCms')) {exit();}
class channelmenu {
    function install() {
        update(array('table'=>'class','where'=>array('hash'=>__Class__),'menu'=>1));
        if($defaultclass=C('cms:class:defaultClass')){
            C('this:add',$defaultclass);
        }
    }
    function upgrade($old_version) {
        if(version_compare($old_version,'1.8','<')) {
            $class=C('cms:class:get',config('class'));
            if($class){
                C('this:add',$class['hash']);
            }
        }
    }
    function auth() {
        Return array('editpost'=>'设置栏目菜单');
    }
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'show','hookedfunction'=>'admin:body','enabled'=>1,'requires'=>'GLOBALS.C.admin.load=admin:class:config;p.editpost');
        Return $hooks;
    }
    function show() {
        if($class=C('cms:class:get',@$_GET['hash'])) {
            if($class['enabled'] && $class['module']){
                V('template',array('hash'=>$class['hash'],'checked'=>config('menu_'.$class['hash'])));
            }
        }
    }
    function add($classhash){
        if(is_hash($classhash)){
            return config('menu_'.$classhash,1);
        }
        return false;
    }
    function del($classhash){
        if(is_hash($classhash)){
            return C('cms:config:del','menu_'.$classhash);
        }
        return false;
    }
    function editpost(){
        $classhash=@$_POST['hash'];
        $state=@$_POST['state'];
        if($state=='false') {
            if(C('this:del',$classhash)){
                return C('admin:ajax','栏目菜单已取消');
            }
        }else {
            if(C('this:add',$classhash)){
                return C('admin:ajax','栏目菜单设置成功');
            }
        }
        return C('admin:ajax','error',1);
    }
    function menu() {
        $classes=C('cms:class:all',1);
        $menu=array();
        $hideDisabledChannel=!P('class:changestate','admin');
        foreach ($classes as $key => $class) {
            if(!$class['module']){continue;}
            if(!config('menu_'.$class['hash'])){continue;}
            if(!$homechannel=C('cms:channel:home',$class['hash'])){continue;}
            if(isset($homechannel['channel_menu']) && !empty($homechannel['channel_menu'])){
                $menu['child'][]=array('title'=>$homechannel['channel_menu'],'function'=>'','ico'=>'layui-icon-align-left');
                end($menu['child']);
                $menukey=key($menu['child']);
            }else{
                $menukey=false;
            }
            $channels_1=C('cms:channel:all',0,$class['hash'],999,true,$hideDisabledChannel);
            if(!count($channels_1)){continue;}
            foreach($channels_1 as $channel) {
                if(!isset($channel['channel_icon'])) {$channel['channel_icon']='';}
                if($channels_1_power=C('this:power',$channel)){
                    if($menukey===false){
                        $menu['child'][]=array('title'=>$channel['channelname'],'ico'=>$channel['channel_icon'],'url'=>C('this:url',$channel));
                        end($menu['child']);
                        $channels_1_key=key($menu['child']);
                    }else{
                        $menu['child'][$menukey]['child'][]=array('title'=>$channel['channelname'],'ico'=>$channel['channel_icon'],'url'=>C('this:url',$channel));
                        end($menu['child'][$menukey]['child']);
                        $channels_1_key=key($menu['child'][$menukey]['child']);
                    }
                }
                $channels_2=C('cms:channel:all',$channel['id'],$class['hash'],999,true,$hideDisabledChannel);
                foreach($channels_2 as $key2=>$channel2) {
                    if(!isset($channel2['channel_icon'])) {$channel2['channel_icon']='';}
                    if($channels_2_power=C('this:power',$channel2)) {
                        if(!$channels_1_power) {
                            $channels_1_power=1;
                            if($menukey===false){
                                $menu['child'][]=array('title'=>$channel['channelname'],'ico'=>$channel['channel_icon'],'url'=>'');
                                end($menu['child']);
                                $channels_1_key=key($menu['child']);
                            }else{
                                $menu['child'][$menukey]['child'][]=array('title'=>$channel['channelname'],'ico'=>$channel['channel_icon'],'url'=>'');
                                end($menu['child'][$menukey]['child']);
                                $channels_1_key=key($menu['child'][$menukey]['child']);
                            }
                        }
                        if($menukey===false){
                            $menu['child'][$channels_1_key]['child'][]=array('title'=>$channel2['channelname'],'ico'=>$channel2['channel_icon'],'url'=>C('this:url',$channel2));
                            end($menu['child'][$channels_1_key]['child']);
                            $channels_2_key=key($menu['child'][$channels_1_key]['child']);
                        }else{
                            $menu['child'][$menukey]['child'][$channels_1_key]['child'][]=array('title'=>$channel2['channelname'],'ico'=>$channel2['channel_icon'],'url'=>C('this:url',$channel2));
                            end($menu['child'][$menukey]['child'][$channels_1_key]['child']);
                            $channels_2_key=key($menu['child'][$menukey]['child'][$channels_1_key]['child']);
                        }
                    }
                    $channels_3=C('cms:channel:all',$channel2['id'],$class['hash'],999,true,$hideDisabledChannel);
                    foreach($channels_3 as $key3=>$channel3) {
                        if($channelpower3=C('this:power',$channel3)) {
                            if(!$channels_1_power) {
                                $channels_1_power=1;
                                if($menukey===false){
                                    $menu['child'][]=array('title'=>$channel['channelname'],'ico'=>$channel['channel_icon'],'url'=>'');
                                    end($menu['child']);
                                    $channels_1_key=key($menu['child']);
                                }else{
                                    $menu['child'][$menukey]['child'][]=array('title'=>$channel['channelname'],'ico'=>$channel['channel_icon'],'url'=>'');
                                    end($menu['child'][$menukey]['child']);
                                    $channels_1_key=key($menu['child'][$menukey]['child']);
                                }
                            }
                            if(!$channels_2_power) {
                                $channels_2_power=1;
                                if($menukey===false){
                                    $menu['child'][$channels_1_key]['child'][]=array('title'=>$channel2['channelname'],'ico'=>$channel2['channel_icon'],'url'=>'');
                                    end($menu['child'][$channels_1_key]['child']);
                                    $channels_2_key=key($menu['child'][$channels_1_key]['child']);
                                }else{
                                    $menu['child'][$menukey]['child'][$channels_1_key]['child'][]=array('title'=>$channel2['channelname'],'ico'=>$channel2['channel_icon'],'url'=>'');
                                    end($menu['child'][$menukey]['child'][$channels_1_key]['child']);
                                    $channels_2_key=key($menu['child'][$menukey]['child'][$channels_1_key]['child']);
                                }
                            }
                            if(!isset($channel3['channel_icon'])) {$channel3['channel_icon']='';}
                            if($menukey===false){
                                $menu['child'][$channels_1_key]['child'][$channels_2_key]['child'][]=array('title'=>$channel3['channelname'],'ico'=>$channel3['channel_icon'],'url'=>C('this:url',$channel3));
                            }else{
                                $menu['child'][$menukey]['child'][$channels_1_key]['child'][$channels_2_key]['child'][]=array('title'=>$channel3['channelname'],'ico'=>$channel3['channel_icon'],'url'=>C('this:url',$channel3));
                            }
                        }
                    }
                }
            }
        }
        if(!isset($menu['child']) || !count($menu['child'])) {
            Return false;
        }
        return $menu;
    }
    function url($channel) {
        if(isset($GLOBALS['channelmenu']['url'][$channel['classhash']][$channel['modulehash']])) {
            if(empty($GLOBALS['channelmenu']['url'][$channel['classhash']][$channel['modulehash']])) {
                Return '';
            }else {
                Return $GLOBALS['channelmenu']['url'][$channel['classhash']][$channel['modulehash']].$channel['id'];
            }
        }
        $array['channel']=C('admin:article:channelGet',$channel['id']);
        $array['columns']=C('cms:form:all','column',$array['channel']['_module']['hash'],$array['channel']['_module']['classhash']);
        if(count($array['columns'])){
            $array['columns']=C('cms:form:getColumnCreated',$array['columns'],$array['channel']['_module']['table']);
        }
        foreach($array['columns'] as $key=>$column) {
            $array['columns'][$key]['auth']=C('admin:formAuth',$column['id']);
            if(!$array['columns'][$key]['auth']['read']) {
                unset($array['columns'][$key]);
            }
        }
        if(count($array['columns']) && (C('admin:moduleAuth',$array['channel']['_module'],'list') || C('admin:moduleAuth',$array['channel']['_module'],'add'))) {
            $GLOBALS['channelmenu']['url'][$channel['classhash']][$channel['modulehash']]='?do=admin:article:home&cid=';
        }elseif(C('admin:article:varEnabled',$array['channel']['_module'])) {
            $GLOBALS['channelmenu']['url'][$channel['classhash']][$channel['modulehash']]='?do=admin:article:home&cid=';
        }else {
            $GLOBALS['channelmenu']['url'][$channel['classhash']][$channel['modulehash']]='';
            Return '';
        }
        Return $GLOBALS['channelmenu']['url'][$channel['classhash']][$channel['modulehash']].$channel['id'];
    }
    function power($channel) {
        if(P('module:permission','admin')) {
            Return true;
        }
        if(isset($GLOBALS['channelmenu']['module'][$channel['classhash']][$channel['modulehash']])) {
            $module=$GLOBALS['channelmenu']['module'][$channel['classhash']][$channel['modulehash']];
        }else {
            $module=C('cms:module:get',$channel['modulehash'],$channel['classhash']);
            $GLOBALS['channelmenu']['module'][$channel['classhash']][$channel['modulehash']]=$module;
        }
        if(C('admin:moduleAuth',$module,'list') || C('admin:moduleAuth',$module,'var') || C('admin:moduleAuth',$module,'add') ) {
            Return true;
        }
        Return false;
    }
}