<?php
class adminhome {
    function init(){
        Return array(
            'template_dir' => 'template'
        );
    }
    function install(){
        C('this:card:add',array('kindhash'=>'text','cardconfigs'=>array('title'=>'欢迎使用','size'=>'3*1','content'=>'ClassCMS是一款简单、灵活、安全、易于拓展的内容管理系统。<a href="http://classcms.com/demo/" class="layui-btn cms-btn layui-btn-xs" target="_blank">演示</a> <a href="http://classcms.com/class/cms/doc/" class="layui-btn cms-btn layui-btn-xs" target="_blank">文档</a>')));
    }
    function table(){
        return array(
                I().'_card'=>array('kindhash'=>'varchar(32)','enabled'=>'int(1)','rolehash'=>'varchar(255)','cardorder'=>'int(11)','cardconfigs'=>'longtext()')
        );
    }
    function auth() {
        Return array('index'=>'使用主页','manage:build;manage:ajax;manage:add;manage:addPost;manage:addSet;manage:set;manage:setPost;manage:del;manage:order'=>'管理组件');
    }
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'defaultPage','hookedfunction'=>'admin:defaultPage','enabled'=>1,'requires'=>'p.index');
        Return $hooks;
    }
    function defaultPage() {
        return '?do='.I().':index';
    }
    function index() {
        $array=array();
        $array['cards']=C('this:card:all');
        v('index',$array);
    }
}

