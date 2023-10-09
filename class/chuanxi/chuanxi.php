<?php
if(!defined('ClassCms')) {exit();}
class chuanxi {
    function init(){
        Return array('template_dir' => 'template');
    }
    function table(){
        return array(I()=>array('title'=>'text','posttime'=>'bigint(10)','state'=>'int(1)','content'=>'text','args'=>'text'));
    }
    function config() {
        $configs=array();
        $configs[]=array('configname'=>'appKey','hash'=>'appkey','inputhash'=>'text','tips'=>'如果消息未指定appKey,则使用此处填写的appKey,前往 https://cx.super4.cn/ 登入后获取appKey','defaultvalue'=>'');
        $configs[]=array('configname'=>'日志','hash'=>'log','inputhash'=>'switch','defaultvalue'=>1,'tips'=>'开启后,将记录每次发送的信息');
        $configs[]=array('configname'=>'队列','hash'=>'task','inputhash'=>'switch','defaultvalue'=>0,'tips'=>'请先安装 <a href="https://classcms.com/class/task/" target="_blank" class="layui-btn layui-btn-xs cms-btn">计划任务[task]</a> 应用,并使用"接口触发"方式部署.<br>通过队列发送消息,可以加快网站响应时间,但会延迟几秒发消息的时间.<br>');
        Return $configs;
    }
    function auth() {
        Return array('admin:index'=>'浏览发送记录','admin:detail'=>'查看详情','admin:del;admin:clean'=>'删除记录','admin:test;admin:testSend'=>'测试发送');
    }
    function send($config=array()) {
        if(!is_array($config)){return false;}
        if(!isset($config['task'])){$config['task']=config('task');}
        if($config['task']){
            $config['task']=0;
            return C('task:add',array('title'=>'传息','classfunction'=>'this:send','args'=>array($config)));
        }

        if(!isset($config['title'])){return E('empty title');}

        if(!isset($config['content']) || !$config['content']){
            $config['content']=$config['title'];
        }

        if(!isset($config['appkey'])){$config['appkey']=config('appkey');}
        if(empty($config['appkey'])){
            return E('empty appkey');
        }
        $url='https://cx.super4.cn/push_msg?appkey='.$config['appkey'].'&title='.urlencode($config['title']).'&content='.urlencode($config['content']);
        if(!$return=C('cms:common:send',$url)){
            C('this:addLog',$config,$return);
            return false;
        }
        $json=json_decode($return,1);
        if(isset($json['code']) && $json['code']==200){
            C('this:addLog',$config,1);
            return true;
        }
        if(isset($json['message'])){
            C('this:addLog',$config,0);
            return E($json['message']);
        }
        C('this:addLog',$config,$return);
        return false;
    }
    function addLog($config,$state=1){
        if(!config('log')){return false;}
        $log=array();
        $query['table']=I();
        if(isset($config['title'])){
            $query['title']=$config['title'];
        }else{
            $query['title']='';
        }
        $query['posttime']=time();
        if($state){
            $query['state']=1;
        }else{
            $query['state']=0;
        }
        if(isset($config['content'])){
            $query['content']=$config['content'];
        }else{
            $query['content']='';
        }
        unset($config['task']);
        $query['args']=json_encode($config);
        return insert($query);
    }
}
