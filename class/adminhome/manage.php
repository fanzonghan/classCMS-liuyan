<?php
class adminhome_manage {
    function build(){
        $build=C('this:card:build',$_POST['id']);
        if($build){
            Return array('html'=>$build);
        }
        return false;
    }
    function ajax() {
        if(!$configs=C('this:kind:getConfigs',@$_GET['kind'])) {
            Return E('error');
        }
        foreach($configs as $config) {
            if($config['hash']==@$_GET['confighash']) {
                $config['source']='adminhome_set';
                $config['auth']['all']=true;
                Return C('cms:input:ajax',$config);
            }
        }
        Return E('参数不存在');
    }
    function add(){
        $array['kinds']=C('this:kind:all');
        $array['groups']=array();
        foreach ($array['kinds'] as $kind) {
            if(!isset($kind['groupname'])){
                $kind['groupname']='';
            }
            if(!isset($array['groups'][$kind['groupname']])){
                $array['groups'][$kind['groupname']]=1;
            }
        }
        Return V('add',$array);
    }
    function addSet(){
        $array['kinds']=C('this:kind:all');
        $array['kind']=array();
        foreach ($array['kinds'] as $kind) {
            if(isset($_GET['hash']) && $_GET['hash']==$kind['hash']){
                $array['kind']=$kind;
            }
        }
        if(!$array['kind']){
            return E('error');
        }
        $array['configs']=C('this:kind:getConfigs',$array['kind']['hash']);
        $array['roles']=C('cms:user:roleAll');
        if(count($array['roles'])>1){
            $array['configs'][]=array('configname'=>'权限','hash'=>'rolehash','inputhash'=>'rolecheckbox','tips'=>'请选择需要显示此组件的角色,注:如用户拥有"管理组件"权限,则无论是否勾选权限,都将显示此组件','value'=>C('cms:user:$admin_role'));
        }
        if(!count($array['configs'])){
            $array['id']=C('this:card:add',array('kindhash'=>$array['kind']['hash']));
        }
        Return V('addSet',$array);
    }
    function addPost(){
        $array['kind']=C('this:kind:get',@$_POST['kindhash']);
        if(!$array['kind']){
            return E('error');
        }
        $cardconfigs=array();
        if(isset($array['kind']['config']) && $array['kind']['config']){
            $configs=$array['kind']['config'];
            foreach ($configs as $key => $config) {
                $value=C('cms:input:post',$config);
                if(is_array($value) && isset($value['error'])){
                    Return E($config['configname'].':'.$value['error']);
                }elseif($value===false) {
                    Return E($config['configname'].':error');
                }
                $cardconfigs[$config['hash']]=$value;
            }
        }
        $rolehash=C('cms:input:post',array('hash'=>'rolehash','inputhash'=>'rolecheckbox'));
        $array['id']=C('this:card:add',array('kindhash'=>$array['kind']['hash'],'rolehash'=>$rolehash,'cardconfigs'=>$cardconfigs));

        return array('id'=>$array['id']);
    }
    function set(){
        if(!$array['card']=C('this:card:get',@$_GET['id'])){
            return E('error card');
        }
        if(!$array['kind']=C('this:kind:get',$array['card']['kindhash'])){
            return E('error kind');
        }
        $array['configs']=C('this:kind:getConfigs',$array['kind']['hash'],$array['card']['id']);
        $array['roles']=C('cms:user:roleAll');
        if(count($array['roles'])>1){
            $array['configs'][]=array('configname'=>'权限','hash'=>'rolehash','inputhash'=>'rolecheckbox','tips'=>'请选择需要显示此组件的角色,注:如用户拥有"管理组件"权限,则无论是否勾选权限,都将显示此组件','value'=>$array['card']['rolehash']);
        }
        if(!count($array['configs'])){
            return E('当前组件无配置选项');
        }
        Return V('set',$array);
    }
    function setPost(){
        if(!$array['card']=C('this:card:get',@$_POST['id'])){
            return E('error card');
        }
        if(!$array['card']){
            return E('error');
        }
        $array['kind']=C('this:kind:get',$array['card']['kindhash']);
        $cardconfigs=array();
        if(isset($array['kind']['config']) && $array['kind']['config']){
            $configs=$array['kind']['config'];
            foreach ($configs as $key => $config) {
                $value=C('cms:input:post',$config);
                if(is_array($value) && isset($value['error'])){
                    Return E($config['configname'].':'.$value['error']);
                }elseif($value===false) {
                    Return E($config['configname'].':error');
                }
                $cardconfigs[$config['hash']]=$value;
            }
        }
        $rolehash=C('cms:input:post',array('hash'=>'rolehash','inputhash'=>'rolecheckbox'));
        if(C('this:card:edit',array('id'=>$array['card']['id'],'rolehash'=>$rolehash,'cardconfigs'=>$cardconfigs))){
            return array('id'=>$array['card']['id']);
        }
        Return E('保存失败');
    }
    function del(){
        if(C('this:card:del',$_POST['id'])){
            return '已删除';
        }
        return E('删除失败');
    }
    function order(){
        $cardsarray=explode('|',$_POST['cardsarray']);
        foreach($cardsarray as $key=>$cardid) {
            if(!empty($cardid)) {
                C('this:card:edit',array('id'=>$cardid,'cardorder'=>count($cardsarray)-$key));
            }
        }
        Return '修改成功';
    }
}