<?php
if(!defined('ClassCms')) {exit();}
class adminmenu {
    function init(){
        Return array(
            'template_dir' => 'template',
        );
    }
    function table(){
        return array(I()=>array('menuname'=>'varchar(255)','enabled'=>'int(1)','target'=>'int(1)','fid'=>'int(9)','icon'=>'varchar(32)','menuorder'=>'int(9)','kind'=>'varchar(32)','kindvalue'=>'varchar(255)','auth'=>'text()'));
    }
    function install() {
        update(array('table'=>'class','where'=>array('hash'=>I()),'menu'=>1));
        $channelInput=C('cms:form:add',array('hash'=>'kindvalue_channel','formname'=>'栏目选择','kind'=>'var','inputhash'=>'classchannel'));
        if(!$channelInput) {
            Return false;
        }
        config('channelinputid',$channelInput);
    }
    function upgrade($old_version) {
        if(version_compare($old_version,'1.2','<')) {
            C($GLOBALS['C']['DbClass'].':addField',I(),'target','int(1)');
        }
    }
    function auth() {
        Return array('index;add;addPost;edit;editPost;go;order;orderPost;del;channelAjax'=>'管理菜单');
    }
    function index() {
        $array['menuhtml']=C('this:menuCollapse');
        V('index',$array);
    }
    function add() {
        $array['breadcrumb']=array(array('url'=>'?do=admin:class:config&hash=adminmenu','title'=>'后台菜单自定义'),array('url'=>'?do=adminmenu:index','title'=>'菜单管理'),array('title'=>'增加'));
        if(isset($_GET['fid'])) {$array['fid']=intval($_GET['fid']);}
        $array['kind']='channel';
        $array['kindvalue']='';
        $array['auth']=C('cms:user:$admin_role');
        $array['channelinput']=C('cms:form:build',config('channelinputid'));
        $array['channelinput']['ajax_url']='?do=adminmenu:channelAjax';
        $array['channelinput']['classhash']=C('cms:class:defaultClass');
        $array['classselectinput']=array('inputhash'=>'classselect','name'=>'kindvalue_class');
        V('form',$array);
    }
    function addPost() {
        $menu_array=array('table'=>I());
        $menu_array['menuname']=trim(htmlspecialchars($_POST['menuname']));
        $menu_array['enabled']=C('cms:input:post',array('inputhash'=>'switch','name'=>'enabled'));
        $menu_array['target']=C('cms:input:post',array('inputhash'=>'switch','name'=>'target'));
        $menu_array['icon']=htmlspecialchars($_POST['icon']);
        $menu_array['fid']=intval($_POST['fid']);
        if($menu_array['fid']) {
            $fidmenu=one(array('table'=>I(),'where'=>array('id'=>$menu_array['fid'])));
            if(!$fidmenu) {
                Return C('admin:ajax','增加失败,父菜单不存在',1);
            }
        }
        $menu_array['menuorder']=0;
        $menu_array['kindvalue']='';
        $menu_array['kind']=$_POST['kind'];
        if($menu_array['kind']=='channel') {
            $menu_array['kindvalue']=intval($_POST['kindvalue_channel']);
        }
        if($menu_array['kind']=='url') {
            $menu_array['kindvalue']=trim($_POST['kindvalue_url']);
        }
        if($menu_array['kind']=='class') {
            $menu_array['kindvalue']=trim($_POST['kindvalue_class']);
            if(!is_hash($menu_array['kindvalue'])) {
                Return C('admin:ajax','应用不正确',1);
            }
            if($menu_array['kindvalue']==I()) {
                Return C('admin:ajax','应用冲突',1);
            }
        }
        if($menu_array['kind']=='diy') {
            $menu_array['kindvalue']=trim($_POST['kindvalue_diy']);
            if($menu_array['kindvalue']=='adminmenu:menu') {
                Return C('admin:ajax','方法名冲突',1);
            }
        }
        $menu_array['auth']=C('cms:input:post',array('inputhash'=>'rolecheckbox','name'=>'auth'));
        if(insert($menu_array)) {
            Return C('admin:ajax','增加成功');
        }else {
            Return C('admin:ajax','增加失败',1);
        }
    }
    function edit() {
        $array=one(array('table'=>I(),'where'=>array('id'=>@$_GET['id'])));
        if(!$array) {
            Return C('admin:error','菜单不存在');
        }
        $array['breadcrumb']=array(
                        array('url'=>'?do=admin:class:config&hash=adminmenu','title'=>'后台菜单自定义'),
                        array('url'=>'?do=adminmenu:index','title'=>'菜单管理'),
                        array('title'=>$array['menuname'].' 编辑'),
                        );
        $array['channelinput']=C('cms:form:build',config('channelinputid'));
        $array['channelinput']['ajax_url']='?do=adminmenu:channelAjax';
        if(empty($array['kind'])) {
            $array['kind']='channel';
        }
        if($array['kind']=='channel' && $array['kindvalue']) {
            $array['channelinput']['value']=$array['kindvalue'];
        }else {
            $array['channelinput']['classhash']=C('cms:class:defaultClass');
        }
        $array['classselectinput']=array('inputhash'=>'classselect','name'=>'kindvalue_class');
        if($array['kind']=='class' && $array['kindvalue']) {
            $array['classselectinput']['value']=$array['kindvalue'];
        }
        V('form',$array);
    }
    function editPost() {
        $menu=one(array('table'=>I(),'where'=>array('id'=>@$_POST['id'])));
        if(!$menu) {
            Return C('admin:ajax','菜单不存在',1);
        }
        $menu_array=array('table'=>I(),'where'=>array('id'=>$menu['id']));
        $menu_array['menuname']=trim(htmlspecialchars($_POST['menuname']));
        $menu_array['enabled']=C('cms:input:post',array('inputhash'=>'switch','name'=>'enabled'));
        $menu_array['target']=C('cms:input:post',array('inputhash'=>'switch','name'=>'target'));
        $menu_array['icon']=htmlspecialchars($_POST['icon']);
        $menu_array['fid']=intval($_POST['fid']);
        if($menu_array['fid']) {
            $fidmenu=one(array('table'=>I(),'where'=>array('id'=>$menu_array['fid'])));
            if(!$fidmenu) {
                Return C('admin:ajax','编辑失败,父菜单不存在',1);
            }
        }
        if($menu_array['fid']!=$menu['fid']) {
            $menus=all(array('table'=>I(),'order'=>'menuorder desc,id asc','where'=>array('fid'=>$menu_array['fid'])));
            if(count($menus)) {
                $order_edit_array=array('table'=>I());
                foreach($menus as $key=>$thismenu) {
                    $order_edit_array['where']=array('id'=>$thismenu['id']);
                    $order_edit_array['menuorder']=count($menus)-$key;
                    update($order_edit_array);
                }
            }
            $menu_array['menuorder']=0;
            if($menu_array['fid']==$menu['id']) {
                Return C('admin:ajax','编辑失败,父菜单冲突',1);
            }
            if($menu_array['fid']) {
                $parents=C('this:getParents',$menu_array['fid']);
                foreach($parents as $parent) {
                    if($parent['id']==$menu['id']) {
                        Return C('admin:ajax','编辑失败,父菜单冲突',1);
                    }
                }
            }
        }
        $menu_array['kind']=$_POST['kind'];
        if($menu_array['kind']=='channel') {
            $menu_array['kindvalue']=intval($_POST['kindvalue_channel']);
        }
        if($menu_array['kind']=='url') {
            $menu_array['kindvalue']=trim($_POST['kindvalue_url']);
        }
        if($menu_array['kind']=='class') {
            $menu_array['kindvalue']=trim($_POST['kindvalue_class']);
            if(!is_hash($menu_array['kindvalue'])) {
                Return C('admin:ajax','应用不正确',1);
            }
            if($menu_array['kindvalue']==I()) {
                Return C('admin:ajax','应用冲突',1);
            }
        }
        if($menu_array['kind']=='diy') {
            $menu_array['kindvalue']=trim($_POST['kindvalue_diy']);
            if($menu_array['kindvalue']=='adminmenu:menu') {
                Return C('admin:ajax','方法名冲突',1);
            }
        }
        $menu_array['auth']=C('cms:input:post',array('inputhash'=>'rolecheckbox','name'=>'auth'));
        if(update($menu_array)) {
            Return C('admin:ajax','编辑成功');
        }else {
            Return C('admin:ajax','编辑失败',1);
        }
    }
    function order() {
        $menu=one(array('table'=>I(),'where'=>array('id'=>@$_GET['id'])));
        if(!$menu) {
            Return C('admin:error','菜单不存在');
        }
        $array['breadcrumb']=array(array('url'=>'?do=admin:class:config&hash=adminmenu','title'=>'后台菜单自定义'),array('url'=>'?do=adminmenu:index','title'=>'菜单管理'),array('title'=>'自定义排序'));
        $array['menus']=all(array('table'=>I(),'order'=>'menuorder desc,id asc','where'=>array('fid'=>$menu['fid'])));
        V('order',$array);
    }
    function channelAjax() {
        $array['channelinput']=C('cms:form:build',config('channelinputid'));
        $ajax=C('cms:input:ajax',$array['channelinput']);
        Return C('admin:ajax',$ajax);
    }
    function orderPost() {
        $ids=explode('|',$_POST['ids']);
        $menu_edit_query['table']=I();
        foreach($ids as $key=>$id) {
            if(is_numeric($id)) {
                $menu_edit_query['where']=array('id'=>$id);
                $menu_edit_query['menuorder']=count($ids)-$key;
                update($menu_edit_query);
            }
        }
        Return C('admin:ajax','修改成功');
    }
    function del() {
        if(!$menu=one(array('table'=>I(),'where'=>array('id'=>@$_POST['id'])))) {
            Return C('admin:ajax','菜单不存在',1);
        }
        if($childmenus=all(array('table'=>I(),'where'=>array('fid'=>$menu['id'])))) {
            Return C('admin:ajax','请先删除下属菜单',1);
        }
        if(del(array('table'=>I(),'where'=>array('id'=>$menu['id'])))) {
            Return C('admin:ajax','删除成功');
        }else {
            Return C('admin:ajax','删除失败',1);
        }
    }
    function go() {
        $menu=one(array('table'=>I(),'where'=>array('id'=>@$_GET['id'])));
        if(!$menu) {
            Return C('admin:error','菜单不存在');
        }
        if($menu['kind']=='channel') {
            if(!$menu['kindvalue']) {
                Return C('admin:error','栏目未选');
            }
            jump('?do=admin:article:home&cid='.$menu['kindvalue']);
        }elseif($menu['kind']=='url') {
            if(!$menu['kindvalue']) {
                Return C('admin:error','链接地址未填写');
            }
            jump($menu['kindvalue']);
        }else{
            Return C('admin:error','当前菜单类型无法跳转链接');
        }
        Return true;
    }
    function menuCollapse($fid=0) {
        $menus=C('this:getChild',$fid);
        if(!count($menus)) {
            Return '';
        }
        $html='<div class="layui-collapse" rel="collapse_'.$fid.'">';
        foreach($menus as $menu) {
            $disabled='';
            if(!$menu['enabled']) {
                $disabled=' cms-text-disabled';
            }
            $html.='<div class="layui-colla-item" rel="'.$menu['id'].'"><h2 class="layui-colla-title'.$disabled.'">'.$menu['menuname'].'</h2><div class="layui-colla-content layui-show">'.C('this:menuCollapse',$menu['id']).'</div><div class="action layui-btn-container"><a class="layui-btn layui-btn-xs layui-btn-primary layui-hide-xs" href="?do=adminmenu:add&fid='.$menu['id'].'" alt="增加" title="增加"><i class="layui-icon layui-icon-add-1"></i></a><a class="layui-btn layui-btn-xs layui-btn-primary" href="?do=adminmenu:edit&id='.$menu['id'].'" alt="编辑" title="编辑"><i class="layui-icon layui-icon-edit"></i></a><a class="layui-btn layui-btn-xs layui-btn-primary layui-hide-xs" href="?do=adminmenu:order&id='.$menu['id'].'" alt="排序" title="排序"><i class="layui-icon layui-icon-find-fill"></i></a><a class="layui-btn layui-btn-xs layui-btn-primary"  lay-text="'.$menu['menuname'].'" lay-href="?do=adminmenu:go&id='.$menu['id'].'" alt="前往" title="前往"><i class="layui-icon layui-icon-share"></i></a><a class="layui-btn layui-btn-xs layui-btn-primary layui-hide-xs delmenu" alt="删除" title="删除"  rel="'.$menu['id'].'"><i class="layui-icon layui-icon-close"></i></a></div></div>';
        }
        $html.='</div>';
        Return $html;
    }
    function getChild($fid=0) {
        Return all(array('table'=>I(),'order'=>'menuorder desc,id asc','where'=>array('fid'=>$fid)));
    }
    function getParents($id=0,$parents=array(),$times=0) {
        $menu=one(array('table'=>I(),'where'=>array('id'=>$id)));
        if(!$menu) {
            unset($parents[0]);
            Return array_reverse($parents);
        }
        $parents[]=$menu;
        if($menu['fid']) {
            Return C('this:getParents',$menu['fid'],$parents,$times+1);
        }else {
            unset($parents[0]);
            Return array_reverse($parents);
        }
    }
    function menu($fid=0) {
        if($fid) {
            $allmenu=array();
        }else {
            $allmenu['child']=array();
        }
        $menus=C('this:getChild',$fid);
        foreach($menus as $menu) {
            if($returnmenu=C('this:getMenu',$menu)) {
                if($fid) {
                    $allmenu[]=$returnmenu;
                }else {
                    $allmenu['child'][]=$returnmenu;
                }
            }
        }
        Return $allmenu;
    }
    function getMenu($menu) {
        if(!$menu['enabled']) {
            Return false;
        }
        if(!isset($GLOBALS['C']['adminmenu']['nowrole'])) {
            if($userid=C('admin:nowUser')) {
                if($useinfo=C('cms:user:get',$userid)) {
                    $GLOBALS['C']['adminmenu']['nowrole']=$useinfo['rolehash'];
                }else {
                    Return false;
                }
            }else {
                Return false;
            }
        }
        $rolehashs=explode(';',$GLOBALS['C']['adminmenu']['nowrole']);
        $menuroles=explode(';',$menu['auth']);
        foreach($rolehashs as $role) {
            if(in_array($role,$menuroles)) {
                if($menu['kind']=='channel') {
                    if($menu['kindvalue']) {
                        $returnmenu=array('title'=>$menu['menuname'],'url'=>'?do=admin:article:home&cid='.$menu['kindvalue'],'ico'=>$menu['icon'],'child'=>C('this:menu',$menu['id']));
                    }else {
                        $returnmenu=array('title'=>$menu['menuname'],'url'=>'','ico'=>$menu['icon'],'child'=>C('this:menu',$menu['id']));
                    }
                }
                if($menu['kind']=='url') {
                    $returnmenu=array('title'=>$menu['menuname'],'target'=>@$menu['target'],'url'=>$menu['kindvalue'],'ico'=>$menu['icon'],'child'=>C('this:menu',$menu['id']));
                }
                if($menu['kind']=='class') {
                    if($class=C('cms:class:get',$menu['kindvalue'])) {
                        $returnmenu=array('title'=>$menu['menuname'],'url'=>'','ico'=>$menu['icon']);
                        $child_menu=C('this:menu',$menu['id']);
                        if($classmenu=C('this:diyMenu',$menu['kindvalue'].':menu')) {
                            if(is_array($child_menu) && count($child_menu)) {
                                $returnmenu['child']=array_merge($classmenu['child'],$child_menu);
                            }else {
                                $returnmenu['child']=$classmenu['child'];
                            }
                        }else {
                            if($class['adminpage'] && P($class['adminpage'],$class['hash'])) {
                                $returnmenu['url']='?do='.$class['hash'].':'.$class['adminpage'];
                            }elseif(P('class:config','admin')) {
                                $returnmenu['url']='?do=admin:class:config&hash='.$class['hash'];
                            }
                            $returnmenu['child']=$child_menu;
                        }
                    }else {
                        $returnmenu=array('title'=>$menu['menuname'],'url'=>'','ico'=>$menu['icon'],'child'=>C('this:menu',$menu['id']));
                    }
                }
                if($menu['kind']=='diy') {
                    if($menu['kindvalue']) {
                        $returnmenu=array('title'=>$menu['menuname'],'url'=>'','ico'=>$menu['icon']);
                        $child_menu=C('this:menu',$menu['id']);
                        if($diymenu=C('this:diyMenu',$menu['kindvalue'])) {
                            if(is_array($child_menu) && count($child_menu)) {
                                $returnmenu['child']=array_merge($diymenu['child'],$child_menu);
                            }else {
                                $returnmenu['child']=$diymenu['child'];
                            }
                        }else {
                            $returnmenu['child']=$child_menu;
                        }
                    }else {
                        $returnmenu=array('title'=>$menu['menuname'],'url'=>'','ico'=>$menu['icon'],'child'=>C('this:menu',$menu['id']));
                    }
                    
                }
                Return $returnmenu;
            }
        }
        Return false;
    }
    function diyMenu($function) {
        $diymenu=C($function);
        $functions=explode(':',$function);
        if(isset($diymenu['child']) && is_array($diymenu['child']) && count($diymenu)) {
            Return C('this:diyMenuUrl',$diymenu,$functions[0]);
        }
        Return false;
    }
    function diyMenuUrl($diymenu,$classhash) {
        if(isset($diymenu['function']) && !empty($diymenu['function'])) {
            if(!P($diymenu['function'],$classhash)) {Return false;}
            if(empty($diymenu['url'])) {
                $diymenu['url']='?do='.$classhash.':'.$diymenu['function'];
                unset($diymenu['function']);
            }
        }
        if(isset($diymenu['child']) && is_array($diymenu['child']) && count($diymenu)) {
            foreach($diymenu['child'] as $key=>$child) {
                if($diyMenuUrl=C('this:diyMenuUrl',$child,$classhash)) {
                    $diymenu['child'][$key]=$diyMenuUrl;
                }
            }
        }
        Return $diymenu;
    }
}