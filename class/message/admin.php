<?php
if(!defined('ClassCms')) {exit();}
class message_admin {
    function sender() {
        if(P('class:config','admin')){
            $array['breadcrumb']=array(array('url'=>'?do=admin:class:config&hash='.I(),'title'=>'消息中心'));
        }
        $array['breadcrumb'][]=array('url'=>'?do=message:user:index','title'=>'消息列表');
        $array['breadcrumb'][]=array('title'=>'管理设置');

        $array['senders']=C('this:sender:all');
        $array['kinds']=C('this:kind:all');
        $array['groups']=C('this:kind:groups');
        Return V('admin/sender',$array);
    }
    function set() {
        $array['sender']=C('this:sender:get',$_GET['sender']);
        if(!$array['sender']){
            Return E('error');
        }
        $array['kind']=C('this:kind:get',$_GET['kind'],$_GET['classhash']);
        if(!$array['kind']){
            Return E('error');
        }
        $array['configs']=C('this:sender:getConfigs',$_GET['sender'],$_GET['kind'],$_GET['classhash']);
        $array['admin_role_name']=C('cms:user:$admin_role');
        $array['roles']=C('cms:user:roleAll');
        $array['authhash']=C('this:sender:authHash',$_GET['sender'],$_GET['kind'],$_GET['classhash']);
        $array['_default']=C('this:sender:getDefault',$_GET['sender'],$_GET['kind'],$_GET['classhash']);
        $array['_editable']=C('this:sender:getEditable',$_GET['sender'],$_GET['kind'],$_GET['classhash']);
        Return V('admin/set',$array);
    }
    function setPost() {
        $sender=C('this:sender:get',$_POST['_sender']);
        if(!$sender){
            Return E('error');
        }
        $kind=C('this:kind:get',$_POST['_kind'],$_POST['_classhash']);
        if(!$kind){
            Return E('error');
        }
        $configs=C('this:sender:getConfigs',$_POST['_sender'],$_POST['_kind'],$_POST['_classhash']);
        foreach ($configs as $key => $config) {
            $value=C('cms:input:post',$config);
            if(is_array($value) && isset($value['error'])){
                Return E($config['configname'].':'.$value['error']);
            }elseif($value===false) {
                Return E($config['configname'].':error');
            }
            config(C('this:sender:configHash',$config['hash'],$_POST['_sender'],$_POST['_kind'],$_POST['_classhash']),$value);
        }
        $default=C('cms:input:post',array('inputhash'=>'switch','name'=>'_default'));
        C('this:sender:setDefault',$default,$_POST['_sender'],$_POST['_kind'],$_POST['_classhash']);
        $editable=C('cms:input:post',array('inputhash'=>'switch','name'=>'_editable'));
        C('this:sender:setEditable',$editable,$_POST['_sender'],$_POST['_kind'],$_POST['_classhash']);
        $roles=C('cms:user:roleAll');
        $authhash=C('this:sender:authHash',$_POST['_sender'],$_POST['_kind'],$_POST['_classhash']);
        $admin_role_name=C('cms:user:$admin_role');
        foreach ($roles as $role) {
            if($role['hash']!=$admin_role_name){
                C('cms:user:authDelAll',array('authkind'=>$authhash,'rolehash'=>$role['hash']));
                if(C('cms:input:post',array('inputhash'=>'switch','name'=>'role_'.$role['hash']))){
                    C('cms:user:authEdit',array('rolehash'=>$role['hash'],'hash'=>$authhash,'authkind'=>$authhash));
                }
            }
        }
        Return '修改成功';
    }
    function ajax() {
        if(!$configs=C('this:sender:getConfigs',@$_GET['sender'])) {
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
    function test(){
        $array['kinds']=C('this:kind:all');
        $array['groups']=C('this:kind:groups');
        $array['user']=C('cms:user:get',C('admin:nowUser'));
        Return V('admin/test',$array);
    }
    function testPost() {
        $config['title']=trim(htmlspecialchars($_POST['title']));
        $config['content']=trim(htmlspecialchars($_POST['content']));
        $config['userid']=trim(htmlspecialchars($_POST['user']));
        $kind=explode('|',$_POST['kind']);
        if(C('this:kind:get',@$kind[1],$kind[0])){
            $config['kind']=$kind[1];
            $config['classhash']=$kind[0];
        }
        if(C('this:add',$config)) {
            Return '发送成功';
        }else {
            Return E('发送失败');
        }
    }
    function clean() {
        if($_POST['clean']){
            if(del('table',I())){
                Return '清空成功';
            }
        }
        Return E('error');
    }
}