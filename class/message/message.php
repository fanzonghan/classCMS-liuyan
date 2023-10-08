<?php
if(!defined('ClassCms')) {exit();}
class message {
    function install(){
        C('this:add',array('userid'=>C('admin:nowUser'),'title'=>'安装成功','content'=>'消息中心安装成功','senders'=>array()));
    }
    function init(){
        Return array('template_dir' => 'template');
    }
    function upgrade($oldversion){
        if(version_compare($oldversion,'1.3','<')) {
            C($GLOBALS['C']['DbClass'].':delTable',I().'_kind');
            C($GLOBALS['C']['DbClass'].':delTable',I().'_sender');
        }
    }
    function table(){
        return array(
                    I()=>array('title'=>'varchar(255)','ifread'=>'int(1)','userid'=>'int(9)','classhash'=>'varchar(32)','kind'=>'varchar(65)','addtime'=>'bigint(10)','content'=>'longtext'),
                    I().'_checked'=>array('userid'=>'int(9)','sender'=>'varchar(255)','kindhash'=>'varchar(32)','classhash'=>'varchar(32)','enabled'=>'int(1)')
        );
    }
    function auth() {
        $auth=array();
        $auth['普通用户']=array('user:index;user:detail;user:read;user:readAll;user:check'=>'查看消息','user:del;user:delAll'=>'删除消息','user:sender;user:senderChange;user:set;user:setPost;user:ajax'=>'通知设置');
        $auth['管理设置']=array('admin:sender;admin:set;admin:setPost;admin:ajax'=>'管理设置','admin:test;admin:testPost'=>'测试发送','admin:clean'=>'清空消息');
        Return $auth;
    }
    function config() {
        $configs=array();
        $configs[]=array('configname'=>'后台提醒','hash'=>'checkMessage','inputhash'=>'switch','defaultvalue'=>'0','tips'=>'程序会循环请求判断是否有新消息,当有新消息时,后台显示小红点.');
        $senders=C('this:sender:all');
        $sendervalue='';
        foreach($senders as $sender) {
            if($sendervalue){$sendervalue.="\n";}
            $sendervalue.=$sender['hash'].':'.$sender['title'];
        }
        $configs[]=array('configname'=>'默认通知方式','hash'=>'defaultsender','inputhash'=>'checkbox','defaultvalue'=>'','values'=>$sendervalue,'savetype'=>1,'tips'=>'未知类型的消息默认的通知方式. 已知类型的消息通知方式会按照用户设置发送');
        if($nowuser=C('cms:user:get',C('admin:nowUser'))){
            $defaultuserhash=$nowuser['hash'];
        }else{
            $defaultuserhash='';
        }
        $configs[]=array('configname'=>'默认接收账号','hash'=>'defaultuserhash','inputhash'=>'text','defaultvalue'=>$defaultuserhash,'tips'=>'当发送的消息未指定用户id时,将发送给此用户');
        Return $configs;
    }
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'showButton','hookedfunction'=>'admin:icoNav:=','enabled'=>1,'requires'=>'p.user:index');
        $hooks[]=array('hookname'=>'user:checkMessage','hookedfunction'=>'admin:body','enabled'=>1,'requires'=>'globals.C.admin.load=admin:index;p.user:check;config.checkMessage');
        Return $hooks;
    }
    function showButton($class,$args,$return) {
        if(config('checkMessage')){
            $unreadmsg=one('table',I(),'where',where('userid',C('admin:nowUser'),'ifread',0));
        }else{
            $unreadmsg=false;
        }
        if($unreadmsg){
            Return $return.'<li class="layui-nav-item" lay-unselect=""><a title="消息" lay-href="?do=message:user:index"><i class="layui-icon layui-icon-notice"></i><span id="message_notice_btn" class="layui-badge-dot"></span></a></li>';
        }else{
            Return $return.'<li class="layui-nav-item" lay-unselect=""><a title="消息" lay-href="?do=message:user:index"><i class="layui-icon layui-icon-notice"></i><span id="message_notice_btn" style="visibility: hidden" class="layui-badge-dot"></span></a></li>';
        }
    }
    function add($config){
        if(!isset($config['table'])){
            $config['table']=I();
        }
        if(!isset($config['userid']) || empty($config['userid'])){
            $config['userid']=config('defaultuserhash');
            if(!$config['userid']){
                return E('empty userid');
            }
        }
        $user=C('cms:user:get',$config['userid']);
        if(!$user){
            return E('error user');
        }
        $config['userid']=$user['id'];
        if(!isset($config['ifread'])){
            $config['ifread']=0;
        }
        if(!isset($config['classhash'])) {
            $config['classhash']=I(-1);
        }
        if(!isset($config['kind'])){
            $config['kind']='';
        }
        if(!isset($config['addtime'])){
            $config['addtime']=time();
        }
        if(!isset($config['title'])){
            $config['title']='';
        }
        if(empty($config['title'])){
            return E('empty title');
        }
        if(!isset($config['content'])){
            $config['content']='';
        }
        if($config['kind']){
            $kind=C('this:kind:get',$config['kind'],$config['classhash']);
            if(!$kind){
                return E('error kind');
            }
            if(!isset($config['senders'])){
                $config['senders']=array();
                $usersenders=all('table',I().'_checked','where',where('userid',$user['id'],'kindhash',$kind['hash'],'classhash',$kind['classhash']));
                $allsenders=C('this:sender:all');
                foreach ($allsenders as $thissender) {
                    $thisauthhash=C('this:sender:pauthHash',$thissender['hash'],$kind['hash'],$kind['classhash']);
                    if(P($thisauthhash,false,$user['id'])){
                        $thiseditable=C('this:sender:getEditable',$thissender['hash'],$kind['hash'],$kind['classhash']);
                        if($thiseditable){
                            unset($thischeck);
                            foreach ($usersenders as $usersender) {
                                if($usersender['sender']==$thissender['hash']){
                                    $thischeck=$usersender['enabled'];
                                    if($usersender['enabled']){
                                        $config['senders'][$thissender['hash']]=array();
                                    }
                                }
                            }
                            if(!isset($thischeck)){
                                $thisdefault=C('this:sender:getDefault',$thissender['hash'],$kind['hash'],$kind['classhash']);
                                if($thisdefault){
                                    $config['senders'][$thissender['hash']]=array();
                                }
                            }
                        }else{
                            $thisdefault=C('this:sender:getDefault',$thissender['hash'],$kind['hash'],$kind['classhash']);
                            if($thisdefault){
                                $config['senders'][$thissender['hash']]=array();
                            }
                        }
                    }
                }
            }
        }else{
            $kind=false;
            if(!isset($config['senders'])){
                $config['senders']=array();
                $senderhashs=explode(';',config('defaultsender'));
                foreach ($senderhashs as $senderhash) {
                    if(!empty($senderhash)){
                        $config['senders'][$senderhash]=array();
                    }
                }
            }
        }
        $senders=$config['senders'];
        unset($config['senders']);
        foreach ($senders as $hash=>$sender) {
            if(!$sender=C('this:sender:get',$hash)){
                break;
            }
            if($config['kind']){
                $thisConfigs=C('this:sender:getConfigs',$sender['hash'],$config['kind'],$config['classhash']);
            }else{
                $thisConfigs=C('this:sender:getConfigs',$sender['hash']);
            }
            $senderConfig=$senders[$hash];
            $senderConfig['message']=$config;
            $userConfigs=C('this:sender:getUserConfigs',$sender['hash'],$user['id']);
            foreach ($userConfigs as $userConfig) {
                if(!isset($senderConfig[$userConfig['hash']])){
                    $senderConfig[$userConfig['hash']]=$userConfig['value'];
                }
            }
            foreach ($thisConfigs as $thisConfig) {
                if(!isset($senderConfig[$thisConfig['hash']])){
                    $senderConfig[$thisConfig['hash']]=$thisConfig['value'];
                }
            }
            C($sender['classfunction'],$senderConfig);
        }
        return insert($config);
    }
}
