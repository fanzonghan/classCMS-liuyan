<?php
if(!defined('ClassCms')) {exit();}
class message_sender {
    function all() {
        return array();
    }
    function get($senderhash='') {
        $senders=C('this:sender:all');
        if($senders && is_array($senders)) {
            foreach($senders as $sender) {
                if($sender['hash']==strtolower($senderhash)) {
                    Return $sender;
                }
            }
        }
        Return false;
    }
    function getConfigs($senderhash,$kind='',$classhash=''){
        if(!$sender=C('this:sender:get',$senderhash)){
            return false;
        }
        if(!isset($sender['config']) || !is_array($sender['config'])){
            $sender['config']=array();
        }
        $configs=$sender['config'];
        if(!$configs){
            return array();
        }
        foreach ($configs as $key => $config) {
            if($kind && $classhash){
                $config['value']=config(C('this:sender:configHash',$config['hash'],$senderhash,$kind,$classhash));
            }elseif(isset($config['defaultvalue'])){
                $config['value']=$config['defaultvalue'];
            }else{
                $config['value']='';
            }
            $config['auth']['all']=true;
            $config['source']='message';
            $config['ajax_url']='?do='.I().':admin:ajax&sender='.$senderhash.'&confighash='.$config['hash'].'&csrf='.C('admin:csrfForm');
            $configs[$key]=C('cms:input:configReset',$config);
        }
        return $configs;
    }
    function configHash($hash,$senderhash,$kind,$classhash){
        return I().':sender|'.$senderhash.'|'.$kind.'|'.$classhash.'|'.$hash;
    }
    function authHash($senderhash,$kind,$classhash){
        return I().':'.$senderhash.':'.$kind.':'.$classhash.':enabled';
    }
    function pauthHash($senderhash,$kind,$classhash){
        return $senderhash.':'.$kind.':'.$classhash.':enabled';
    }
    function getEditable($senderhash,$kind,$classhash){
        return config(I().':senderEditable|'.$senderhash.'|'.$kind.'|'.$classhash);
    }
    function setEditable($value,$senderhash,$kind,$classhash){
        return config(I().':senderEditable|'.$senderhash.'|'.$kind.'|'.$classhash,$value);
    }
    function getDefault($senderhash,$kind,$classhash){
        return config(I().':senderDefault|'.$senderhash.'|'.$kind.'|'.$classhash);
    }
    function setDefault($value,$senderhash,$kind,$classhash){
        return config(I().':senderDefault|'.$senderhash.'|'.$kind.'|'.$classhash,$value);
    }
    function getUserAllChecked($userid=0){
        if(!$userid){
            $userid=C('admin:nowUser');
        }
        return all('table',I().'_checked','where',where('userid',$userid));
    }
    function userConfigHash($hash,$senderhash,$userid=0){
        if(!$userid){
            $userid=C('admin:nowUser');
        }
        return I().':senderuserconfig|'.$senderhash.'|'.$hash.'|'.$userid;
    }
    function getUserConfigs($senderhash,$userid=0){
        if(!$sender=C('this:sender:get',$senderhash)){
            return false;
        }
        if(!isset($sender['userconfig']) || !is_array($sender['userconfig'])){
            $sender['userconfig']=array();
        }
        $configs=$sender['userconfig'];
        if(!$configs){
            return array();
        }
        foreach ($configs as $key => $config) {
            $config['value']=config(C('this:sender:userConfigHash',$config['hash'],$senderhash,$userid));
            if($config['value']===false){
                if(isset($config['defaultvalue'])){
                    $config['value']=$config['defaultvalue'];
                }else{
                    $config['value']='';
                }
            }
            $config['auth']['all']=true;
            $config['source']='message';
            $config['ajax_url']='?do='.I().':user:ajax&sender='.$senderhash.'&confighash='.$config['hash'].'&csrf='.C('admin:csrfForm');
            $configs[$key]=C('cms:input:configReset',$config);
        }
        return $configs;
    }
}