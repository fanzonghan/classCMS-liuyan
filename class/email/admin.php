<?php
if(!defined('ClassCms')) {exit();}
class email_admin {
    function index() {
        $log_query=array();
        $log_query['table']=I();
        $log_query['optimize']=true;
        $log_query['page']=page('pagesize',30);
        $log_query['order']='id desc';
        $array['logs']=all($log_query);
        $array['tips']='';
        if(!config('host') || !config('username') || !config('password')){
            $array['tips']='未正确配置邮件发送参数 <a href="?do=admin:class:setting&hash='.I().'" class="layui-btn layui-btn-xs cms-btn">设置</a>';
        }
        V('index',$array);
    }
    function test() {
        $array['tips']='';
        if(!config('host') || !config('username') || !config('password')){
            $array['tips']='未正确配置邮件发送参数 <a href="?do=admin:class:setting&hash='.I().'" class="layui-btn layui-btn-xs cms-btn">设置</a>';
        }
        V('test',$array);
    }
    function testSend() {
        if(!isset($_POST['to'])){
            Return C('admin:ajax','失败',1);
        }
        $config=array('to'=>$_POST['to'],'title'=>@$_POST['title'],'content'=>@$_POST['content']);
        if(isset($_POST['task'])){
            $config['task']=1;
        }else{
            $config['task']=0;
        }
        $state=C('email:send',$config);
        if($config['task']){
            if($state){
                Return C('admin:ajax','创建队列成功,请等待计划任务执行,并检查发送日志,查看是否成功发送邮件');
            }else{
                Return C('admin:ajax','创建队列失败,请确认是否正确部署计划任务应用',1);
            }
        }else{
            if($state){
                Return C('admin:ajax','发送成功');
            }else{
                Return C('admin:ajax','发送失败,错误信息可以从发送记录详情中查看',1);
            }
        }
    }
    function detail() {
        $log_query=array();
        $log_query['table']=I();
        $log_query['where']=array('id'=>intval($_GET['id']));
        if(!$array=one($log_query)) {
            Return C('admin:error','日志不存在');
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
            Return C('admin:ajax','删除失败',1);
        }
        if(del($del_query)) {
            Return C('admin:ajax','删除成功');
        }else {
            Return C('admin:ajax','删除失败',1);
        }
    }
    function clean() {
        $del_query=array();
        $del_query['table']=I();
        if(!isset($_POST['id'])) {
            Return C('admin:ajax','清空失败',1);
        }
        if(del($del_query)) {
            Return C('admin:ajax','清空成功');
        }else {
            Return C('admin:ajax','清空失败',1);
        }
    }
}