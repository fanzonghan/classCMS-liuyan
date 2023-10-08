<?php
if(!defined('ClassCms')) {exit();}
class keeplogin {
    function hook(){
        $hooks=array();
        $hooks[]=array('hookname'=>'checkbox','hookedfunction'=>'admin:loginFormitem','enabled'=>1);
        $hooks[]=array('hookname'=>'adminCookie','hookedfunction'=>'admin:adminCookie:=','enabled'=>1);
        $hooks[]=array('hookname'=>'tokenTime','hookedfunction'=>'cms:user:makeToken','enabled'=>1);
        Return $hooks;
    }
    function checkbox(){
        echo('<div class="layui-form-item"><input type="checkbox" name="keeplogin_classcms" title="保持登入" lay-skin="primary"></div>');
    }
    function adminCookie($class,$args,$return) {
        if(isset($_POST['keeplogin_classcms']) && isset($args[0]) && !empty($args[0])) {
            if(version_compare(PHP_VERSION,'7.3.0','<')){
                setcookie('token'.C('admin:cookieHash'),$args[0],time()+3600*24*365,$GLOBALS['C']['SystemDir'],null,null,true);
            }else{
                setcookie('token'.C('admin:cookieHash'),$args[0],array('expires'=>time()+3600*24*365,'path'=>$GLOBALS['C']['SystemDir'],'domain'=>null,'secure'=>null,'httponly'=>true));
            }
        }
    }
    function tokenTime($userid,$overtime=0) {
        if(isset($_POST['keeplogin_classcms'])) {
            return array('cms:user:makeToken',$userid,3600*24*365);
        }
    }
}