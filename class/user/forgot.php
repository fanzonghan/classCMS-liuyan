<?php
if(!defined('ClassCms')) {exit();}
class user_forgot {
    function index(){
        return V('forgot');
    }
    function post(){
        if(!isset($_POST['userhash'])){
            Return C('admin:ajax','参数错误',1);
        }
        if(!is_hash($_POST['userhash'])) {
            Return C('admin:ajax','账号格式有误',1);
        }
        if(config('forgotcaptcha')){
            if(!isset($_POST['captcha']) || empty($_POST['captcha'])) {
                Return C('admin:ajax','请填写验证码',1);
            }
            if(!C('captcha:check')) {
                Return C('admin:ajax','验证码不正确,请刷新后再试',1);
            }
        }
        if(!C('this:get',array('hash'=>$_POST['userhash']))){
            Return C('admin:ajax','账号不存在',1);
        }
        return C('admin:ajax','未选择找回密码方式',1);
    }
}