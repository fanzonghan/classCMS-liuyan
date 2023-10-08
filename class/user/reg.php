<?php
if(!defined('ClassCms')) {exit();}
class user_reg {
    function index(){
        return V('reg');
    }
    function post(){
        if(!isset($_POST['username']) || !isset($_POST['userhash']) || !isset($_POST['passwd']) || !isset($_POST['passwd2'])){
            Return C('admin:ajax','参数错误',1);
        }
        if(config('regcaptcha')){
            if(!isset($_POST['captcha']) || empty($_POST['captcha'])) {
                Return C('admin:ajax','请填写验证码',1);
            }
            if(!C('captcha:check')) {
                Return C('admin:ajax','验证码不正确,请刷新后再试',1);
            }
        }
        if(config('regagreement') && !isset($_POST['_useragreement'])){
            Return C('admin:ajax','同意用户协议后才可以注册',1);
        }
        if(!is_hash($_POST['userhash'])) {
            Return C('admin:ajax','账号格式有误',1);
        }
        if(C('this:get',array('hash'=>$_POST['userhash']))){
            Return C('admin:ajax','账号已被使用',1);
        }
        $_POST['passwd']=trim($_POST['passwd']);
        $_POST['passwd2']=trim($_POST['passwd2']);
        if(empty($_POST['passwd'])) {
            Return C('admin:ajax','请填写密码',1);
        }
        if($_POST['passwd']!==$_POST['passwd2']) {
            Return C('admin:ajax','密码不一致',1);
        }
        $user=array('username'=>$_POST['username'],'hash'=>$_POST['userhash'],'passwd'=>$_POST['passwd']);
        if(config('regcheck')){
            $user['enabled']=0;
        }else{
            $user['enabled']=1;
        }
        $regForms=C('this:reg:getForms');
        foreach ($regForms as $form) {
            $info_value=C('cms:input:post',$form);
            if($info_value===null) {
            }elseif(is_array($info_value) && isset($info_value['error'])) {
                return C('admin:ajax',$form['formname'].' '.$info_value['error'],1);
            }elseif($info_value===false) {
                return C('admin:ajax',$form['formname'].' 错误',1);
            }else{
                $user[$form['hash']]=$info_value;
            }
        }
        $user['rolehash']=config('regrole');
        $user['regtime']=time();
        $user['regip']=C('cms:common:ip');
        if(C('cms:user:add',$user)){
            C('captcha:del');
            if($user['enabled']){
                return C('admin:ajax','注册成功,请登入');
            }else{
                return C('admin:ajax','注册成功,请等待审核');
            }
        }
        if(E()){
            return C('admin:ajax',E(),1);
        }
        return C('admin:ajax','注册失败',1);
    }
    function showInput(){
        $array['forms']=C('this:reg:getForms');
        if(count($array['forms'])){
            V('regform',$array);
        }
    }
    function formAjax() {
        if(!isset($_GET['reginfoid'])) {
            Return C('admin:ajax','参数错误',1);
        }
        $forms=C('this:reg:getForms');
        foreach ($forms as $form) {
            if($form['id']==$_GET['reginfoid']){
                $ajax=C('cms:input:ajax',$form);
                Return C('admin:ajax',$ajax);
            }
        }
        Return C('admin:ajax','参数错误',1);
    }
    function getForms(){
        $infos=C('cms:form:all','info');
        $infos=C('cms:form:getColumnCreated',$infos,'user');
        $infohashs=explode(';',config('reginfos'));
        $role=config('regrole');
        $forms=array();
        foreach ($infos as $info) {
            if($info['enabled'] && in_array($info['hash'],$infohashs)){
                $thisinfo=C('cms:form:build',$info['id']);
                $thisinfo['auth']=C('this:formAuth',$thisinfo,$role);
                if(($thisinfo['inputhash']=='text' || $thisinfo['inputhash']=='textarea') && empty($thisinfo['placeholder'])){
                    $thisinfo['placeholder']=$thisinfo['formname'];
                }
                $thisinfo['source']='userregform';
                $thisinfo['value']=C('cms:input:defaultvalue',$thisinfo);
                $thisinfo['ajax_url']='?do=user:reg:formAjax&reginfoid='.$thisinfo['id'];
                $forms[]=$thisinfo;
            }
        }
        return $forms;
    }
    function formAuth($info,$role) {
        $userauth=array();
        $inputauth=C('cms:input:auth',array('inputhash'=>$info['inputhash']));
        if(C('admin:rolesCheck','admin:info:index',$role)) {
            foreach($inputauth as $key=>$this_auth) {
                if(stripos($key,'|false')===false) {
                    $userauth[$key]=true;
                }else {
                    $userauth[$key]=false;
                }
            }
            Return $userauth;
        }
        foreach($inputauth as $key=>$this_auth) {
            $userauth[$key]=C('admin:rolesCheck',C('cms:form:authStr',$info,$key),$role);
        }
        $userauth['read']=1;
        $userauth['write']=1;
        Return $userauth;
    }
}