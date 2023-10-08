<?php
if(!defined('ClassCms')) {exit();}
class userinfoonly {
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'addUser','hookedfunction'=>'cms:user:add','enabled'=>1);
        $hooks[]=array('hookname'=>'editUser','hookedfunction'=>'cms:user:edit','enabled'=>1);
        Return $hooks;
    }
    function config() {
        $infos=C('cms:form:all','info');
        $infos=C('cms:form:getColumnCreated',$infos,'user');
        $infosvalue='';
        foreach($infos as $info) {
            if($infosvalue){$infosvalue.="\n";}
            $infosvalue.=$info['hash'].':'.$info['formname'];
        }
        $configs=array();
        $configs[]=array('configname'=>'属性','hash'=>'infos','inputhash'=>'checkbox','defaultvalue'=>'','values'=>$infosvalue,'savetype'=>1,'tips'=>'勾选需要设置唯一的用户属性，如邮箱、QQ号码、手机号码等。勾选后,不同用户无法设置相同的属性值。');
        Return $configs;
    }
    function setOnly($infohash){
        $infos=explode(';',config('infos'));
        foreach ($infos as $thishash) {
            if($thishash==$infohash){
                return true;
            }
        }
        $infos[]=$infohash;
        Return config('infos',implode(';',$infos));
    }
    function removeOnly($infohash){
        $infos=explode(';',config('infos'));
        foreach ($infos as $key=>$thishash) {
            if($thishash==$infohash){
                unset($infos[$key]);
            }
        }
        Return config('infos',implode(';',$infos));
    }
    function addUser($config) {
        $infohashs=explode(';',config('infos'));
        $infos=C('cms:form:all','info');
        foreach ($infos as $info) {
            if($info['enabled'] && in_array($info['hash'],$infohashs) && isset($config[$info['hash']])){
                if($user=one(array('table'=>'user','column'=>'id','where'=>array($info['hash']=>$config[$info['hash']])))) {
                    Return E('存在相同的'.$info['formname'].':'.$config[$info['hash']]);
                }
            }
        }
    }
    function editUser($config) {
        $infohashs=explode(';',config('infos'));
        $infos=C('cms:form:all','info');
        foreach ($infos as $info) {
            if($info['enabled'] && in_array($info['hash'],$infohashs) && isset($config[$info['hash']])){
                if($user=one(array('table'=>'user','column'=>'id','where'=>array($info['hash']=>$config[$info['hash']],'id<>'=>$config['id'])))) {
                    Return E('存在相同的'.$info['formname'].':'.$config[$info['hash']]);
                }
            }
        }
    }
}