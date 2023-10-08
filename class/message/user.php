<?php
if(!defined('ClassCms')) {exit();}
class message_user {
    function checkMessage(){
        V('user/check');
    }
    function index() {
        if(P('class:config','admin')){
            $array['breadcrumb']=array(array('url'=>'?do=admin:class:config&hash='.I(),'title'=>'消息中心'),array('title'=>'消息列表'));
        }else{
            $array['breadcrumb']=array(array('title'=>'消息列表'));
        }
        $query=array();
        $query['table']=I();
        $query['optimize']=true;
        $query['page']=page('pagesize',30);
        $query['order']='addtime desc,id desc';
        $query['where']['userid']=C('admin:nowUser');
        $array['messages']=all($query);
        $kinds=C('this:kind:all');
        foreach ($kinds as $key => $kind) {
            $array['kinds'][$kind['hash']]=$kind['title'];
        }
        return V('user/index',$array);
    }
    function read() {
        if($_POST['ids']){
            $ids=explode(';',$_POST['ids']);
            if(update('table',I(),'where',where('userid',C('admin:nowUser'),'id',$ids),'ifread','1')){
                return '已读成功';
            }
        }
        return E('error');
    }
    function readAll() {
        if(isset($_POST['readAll'])){
            if(update('table',I(),'where',where('userid',C('admin:nowUser')),'ifread','1')){
                return '全部已读成功';
            }
        }
        return E('error');
    }
    function del() {
        if($_POST['ids']){
            $ids=explode(';',$_POST['ids']);
            if(del('table',I(),'where',where('userid',C('admin:nowUser'),'id',$ids))){
                return '删除成功';
            }
        }
        return E('error');
    }
    function delAll() {
        if(isset($_POST['delAll'])){
            if(del('table',I(),'where',where('userid',C('admin:nowUser')))){
                return '全部删除成功';
            }
        }
        return E('error');
    }
    function detail() {
        $message_query=array();
        $message_query['table']=I();
        $message_query['where']=array('id'=>intval($_GET['id']),'userid'=>C('admin:nowUser'));
        if(!$array=one($message_query)) {
            return E('消息不存在');
        }
        update('table',I(),'where',where('userid',C('admin:nowUser'),'id',$array['id']),'ifread','1');
        return V('user/detail',$array);
    }
    function check(){
        if(isset($_POST['check'])){
            $message_query=array();
            $message_query['table']=I();
            if($_POST['check']){
                $message_query['where']=array('userid'=>C('admin:nowUser'),'ifread'=>'0','id<>'=>$_POST['check']);
            }else{
                $message_query['where']=array('userid'=>C('admin:nowUser'),'ifread'=>'0');
            }
            if(one($message_query)) {
                return array('newmsg'=>1);
            }
            return array('newmsg'=>0);
        }
        return E('error');
    }
    function sender() {
        if(P('class:config','admin')){
            $array['breadcrumb']=array(array('url'=>'?do=admin:class:config&hash='.I(),'title'=>'消息中心'));
        }
        $array['breadcrumb'][]=array('url'=>'?do=message:user:index','title'=>'消息列表');
        $array['breadcrumb'][]=array('title'=>'通知设置');
        $array['senders']=C('this:sender:all');
        $array['kinds']=C('this:kind:all');
        $userAllChecked=C('this:sender:getUserAllChecked');
        foreach ($array['kinds'] as $key => $kind) {
            $allsender=false;
            foreach ($array['senders'] as $sender) {
                $authHash=C('this:sender:pauthHash',$sender['hash'],$kind['hash'],$kind['classhash']);
                $array['kinds'][$key]['status'][$sender['hash']]['enabled']=P($authHash);
                if($array['kinds'][$key]['status'][$sender['hash']]['enabled']){
                    $allsender=true;
                }
                $array['kinds'][$key]['status'][$sender['hash']]['editable']=C('this:sender:getEditable',$sender['hash'],$kind['hash'],$kind['classhash']);
                $array['kinds'][$key]['status'][$sender['hash']]['checked']=C('this:sender:getDefault',$sender['hash'],$kind['hash'],$kind['classhash']);
                foreach ($userAllChecked as $thischeck) {
                    if($thischeck['sender']==$sender['hash'] && $thischeck['kindhash']==$kind['hash'] && $thischeck['classhash']==$kind['classhash']){
                        $array['kinds'][$key]['status'][$sender['hash']]['checked']=$thischeck['enabled'];
                    }
                }
            }
            if(!$allsender){
                unset($array['kinds'][$key]);
            }
        }
        foreach ($array['senders'] as $key => $sender) {
            $allkind=false;
            foreach ($array['kinds'] as $kind) {
                if($kind['status'][$sender['hash']]['enabled']){
                    $allkind=true;
                }
            }
            if(!$allkind){
                unset($array['senders'][$key]);
            }
        }
        $array['groups']=C('this:kind:groups');
        foreach ($array['groups'] as $key => $group) {
            $ifin=false;
            foreach ($array['kinds'] as $kind) {
                if($kind['groupname']==$group){
                    $ifin=true;
                }
            }
            if(!$ifin){
                unset($array['groups'][$key]);
            }
        }
        Return V('user/sender',$array);
    }
    function senderChange(){
        $hashs=explode('|',$_POST['hash']);
        if(!isset($hashs[2])){
            return E('error');
        }
        if(!$sender=C('this:sender:get',$hashs[0])){
            return E('error');
        }
        if(!$kind=C('this:kind:get',$hashs[1],$hashs[2])){
            return E('error');
        }
        if($sender['hash']==$hashs[0] && $kind['hash']==$hashs[1] && $kind['classhash']==$hashs[2]){
            $authHash=C('this:sender:pauthHash',$sender['hash'],$kind['hash'],$kind['classhash']);
            if(!P($authHash)){
                return E('无权限');
            }
            if(!C('this:sender:getEditable',$sender['hash'],$kind['hash'],$kind['classhash'])){
                return E('无法修改');
            }
            if(!del('table',I().'_checked','where',where('userid',C('admin:nowUser'),'sender',$sender['hash'],'kindhash',$kind['hash'],'classhash',$kind['classhash']))){
                return E('修改失败');
            }
            if(@$_POST['state']=='false') {
                $id=insert(array('table'=>I().'_checked','enabled'=>0,'userid'=>C('admin:nowUser'),'sender'=>$sender['hash'],'kindhash'=>$kind['hash'],'classhash'=>$kind['classhash']));
                if(!$id){
                    return E('修改失败');
                }
                return '已关闭 '.$kind['title'].'-'.$sender['title'].' 通知';
            }else {
                $id=insert(array('table'=>I().'_checked','enabled'=>1,'userid'=>C('admin:nowUser'),'sender'=>$sender['hash'],'kindhash'=>$kind['hash'],'classhash'=>$kind['classhash']));
                if(!$id){
                    return E('修改失败');
                }
                return '已开启 '.$kind['title'].'-'.$sender['title'].' 通知';
            }
        }
        return E('error');
    }
    function set() {
        $array['sender']=C('this:sender:get',$_GET['sender']);
        if(!$array['sender']){
            Return E('error');
        }
        $array['configs']=C('this:sender:getUserConfigs',$_GET['sender']);
        if(!$array['configs']){
            return E('该字段无配置选项');
        }
        Return V('user/set',$array);
    }
    function setPost() {
        $sender=C('this:sender:get',$_POST['_sender']);
        if(!$sender){
            Return E('error');
        }
        $allow=false;
        $kinds=C('this:kind:all');
        foreach ($kinds as $key => $kind) {
            $authHash=C('this:sender:pauthHash',$sender['hash'],$kind['hash'],$kind['classhash']);
            if(P($authHash)){
                $allow=true;
            }
        }
        if(!$allow){
            Return E('error');
        }

        $configs=C('this:sender:getUserConfigs',$_POST['_sender']);
        foreach ($configs as $key => $config) {
            $value=C('cms:input:post',$config);
            if(is_array($value) && isset($value['error'])){
                Return E($config['configname'].':'.$value['error']);
            }elseif($value===false) {
                Return E($config['configname'].':error');
            }
            config(C('this:sender:userConfigHash',$config['hash'],$_POST['_sender']),$value);
        }
        Return '修改成功';
    }
    function ajax() {
        $sender=C('this:sender:get',@$_GET['sender']);
        if(!$sender){
            Return E('error');
        }
        $allow=false;
        $kinds=C('this:kind:all');
        foreach ($kinds as $key => $kind) {
            $authHash=C('this:sender:pauthHash',$sender['hash'],$kind['hash'],$kind['classhash']);
            if(P($authHash)){
                $allow=true;
            }
        }
        if(!$allow){
            Return E('error');
        }
        if(!$configs=C('this:sender:getUserConfigs',$_GET['sender'])) {
            Return E('error');
        }
        foreach($configs as $config) {
            if($config['hash']==@$_GET['confighash']) {
                $config['source']='message_set';
                $config['auth']['all']=true;
                Return C('cms:input:ajax',$config);
            }
        }
        Return E('参数不存在');
    }
}