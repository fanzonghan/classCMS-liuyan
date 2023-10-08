<?php
if(!defined('ClassCms')) {exit();}
class user_captcha {
    function regShow(){
        V('captcha');
    }
    function loginShow(){
        V('captcha');
    }
    function forgotShow(){
        V('captcha');
    }
    function loginCheck() {
        if(!isset($_POST['captcha']) || empty($_POST['captcha'])) {
            Return C('admin:ajax','请填写验证码',1);
        }
        if(!C('captcha:check')) {
            Return C('admin:ajax','验证码不正确,请刷新后再试',1);
        }
        C('captcha:del');
    }
}