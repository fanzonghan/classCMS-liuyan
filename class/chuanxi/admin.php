<?php
if(!defined('ClassCms')) {exit();}
class chuanxi_admin {
    function index() {
        $log_query=array();
        $log_query['table']=I();
        $log_query['optimize']=true;
        $log_query['page']=page('pagesize',30);
        $log_query['order']='id desc';
        $array['logs']=all($log_query);
        $array['tips']='';
        if(!config('appkey') ){
            $array['tips']='未正确配置appkey <a href="?do=admin:class:setting&hash='.I().'" class="layui-btn layui-btn-xs cms-btn">设置</a>';
        }
        V('index',$array);
    }
    function test() {
        $array['tips']='';
        if(!config('appkey')){
            $array['tips']='未正确配置appkey <a href="?do=admin:class:setting&hash='.I().'" class="layui-btn layui-btn-xs cms-btn">设置</a>';
        }
        V('test',$array);
    }
    function testSend() {
        if(!isset($_POST['appkey']) || empty($_POST['appkey'])){
            Return E('appkey不能为空');
        }
        $config=array('appkey'=>$_POST['appkey'],'title'=>@$_POST['title'],'content'=>@$_POST['content']);
        if(isset($_POST['task'])){
            $config['task']=1;
            if(!C('cms:class:get','task')){
                Return E('未安装 计划任务 应用');
            }
        }else{
            $config['task']=0;
        }
        $state=C('this:send',$config);
        if($config['task']){
            if($state){
                Return '创建队列成功,请等待计划任务执行,并检查发送日志,查看是否成功发送';
            }else{
                Return E('创建队列失败,请确认是否正确部署计划任务应用');
            }
        }else{
            if($state){
                Return '发送成功';
            }else{
                Return E('发送失败');
            }
        }
    }
    function detail() {
        $log_query=array();
        $log_query['table']=I();
        $log_query['where']=array('id'=>intval($_GET['id']));
        if(!$array=one($log_query)) {
            Return E('日志不存在');
        }
        $args=json_decode($array['args'],1);
        $array['content']=$args['content'];
        if(isset($args['error'])){
            $array['error']=$args['error'];
        }
        V('detail',$array);
    }
    function del() {
        $del_query=array();
        $del_query['table']=I();
        if(isset($_POST['id'])) {
            $del_query['where']=array('id'=>intval($_POST['id']));
        }else {
            Return E('删除失败');
        }
        if(del($del_query)) {
            Return '删除成功';
        }else {
            Return E('删除失败');
        }
    }
    function clean() {
        $del_query=array();
        $del_query['table']=I();
        if(!isset($_POST['id'])) {
            Return E('清空失败');
        }
        if(del($del_query)) {
            Return '清空成功';
        }else {
            Return E('清空失败');
        }
    }
}