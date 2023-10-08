<?php
if(!defined('ClassCms')) {exit();}
class admintabs {
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'js','hookedfunction'=>'admin:head','enabled'=>1,'requires'=>'globals.C.admin.load=admin:index');
        $hooks[]=array('hookname'=>'classTarget','hookedfunction'=>'admin:body','enabled'=>1,'requires'=>'globals.C.admin.load=admin:class:index');
        $hooks[]=array('hookname'=>'classconfigTarget','hookedfunction'=>'admin:body','enabled'=>1,'requires'=>'globals.C.admin.load=admin:class:config');
        Return $hooks;
    }
    function js() {
        echo('<script>window.pageTabs=1;</script>');
    }
    function classTarget() {
        echo('<script>layui.use(["index"],function(){layui.$("tbody tr[rel] a.layui-btn[href]").each(function(){if (layui.$(this).text()=="主页" && !layui.$(this).hasClass("layui-btn-disabled")){layui.$(this).attr("lay-href",layui.$(this).attr("href")).removeAttr("href");layui.$(this).attr("lay-text",layui.$(this).parents("tr[rel]").find("td a").eq(0).text());}});});</script>');
    }
    function classconfigTarget() {
        echo('<script>layui.use(["index"],function(){layui.$("#manage a.layui-btn").each(function(){if (layui.$(this).text()=="主页" && !layui.$(this).hasClass("layui-btn-disabled")){layui.$(this).attr("lay-href",layui.$(this).attr("href")).removeAttr("href");layui.$(this).attr("lay-text",layui.$(document).attr("title"));}});});</script>');
    }
}