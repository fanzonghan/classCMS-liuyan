<?php
if(!defined('ClassCms')) {exit();}
class user_agreement {
    function regShow(){
        V('agreement',array('regshow'=>config('regagreement'),'loginshow'=>config('loginregagreement')));
    }
    function loginShow(){
        V('agreement',array('regshow'=>config('regagreement'),'loginshow'=>config('loginregagreement')));
    }
    function loginPost(){
        if(isset($_POST['userhash']) && isset($_POST['passwd']) && !isset($_POST['_useragreement'])){
            Return C('admin:ajax','同意用户协议后才可以登入',1);
        }
    }
}