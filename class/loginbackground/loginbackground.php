<?php
if(!defined('ClassCms')) {exit();}
class loginbackground {
    function config() {
        $configs=array();
        $configs[]=array('configname'=>'背景颜色','hash'=>'backgroundcolor','inputhash'=>'colorpicker','tips'=>'登入页背景颜色,默认为#1E9FFF','tabname'=>'','defaultvalue'=>'#1E9FFF');
        $configs[]=array('configname'=>'点的颜色','hash'=>'dotcolor','inputhash'=>'colorpicker','tips'=>'特效中点的颜色,默认为#7ec7fd','tabname'=>'','defaultvalue'=>'#7ec7fd');
        $configs[]=array('configname'=>'线的颜色','hash'=>'linecolor','inputhash'=>'colorpicker','tips'=>'特效中线的颜色,默认为#7ec7fd','tabname'=>'','defaultvalue'=>'#7ec7fd');
        Return $configs;
    }
    function hook(){
        $hooks=array();
        $hooks[]=array('hookname'=>'background','hookedfunction'=>'admin:loginBody','enabled'=>1);
        $hooks[]=array('hookname'=>'all','hookedfunction'=>'cms:body','enabled'=>1,'requires'=>'GET.do;GLOBALS.C.admin.load');
        Return $hooks;
    }
    function background(){
        C('this:backgroundTemplate');
    }
    function all(){
        if($GLOBALS['C']['admin']['load']==$_GET['do'] && C('admin:nologinActionCheck',$_GET['do'])){
            C('this:backgroundTemplate');
        }
    }
    function backgroundTemplate(){
        $array['backgroundcolor']=config('backgroundcolor');
        $array['dotcolor']=config('dotcolor');
        $array['linecolor']=config('linecolor');
        if(empty($array['backgroundcolor'])) {$array['backgroundcolor']='#1E9FFF';}
        if(empty($array['dotcolor'])) {$array['dotcolor']='#7ec7fd';}
        if(empty($array['linecolor'])) {$array['linecolor']='#7ec7fd';}
        V('template',$array);
    }
}
