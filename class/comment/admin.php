<?php
if(!defined('ClassCms')) {exit();}
class comment_admin {
    function index() {
        $query=array();
        $query['table']=I();
        $query['optimize']=true;
        $query['page']=page('pagesize',50);
        $query['order']='sticky desc,stickytime asc,addtime desc';
        if(isset($_GET['status']) && $_GET['status']=='0'){
            $query['where']=where('status',0);
            $array['status']=0;
        }elseif(isset($_GET['status']) && $_GET['status']=='1'){
            $query['where']=where('status',1);
            $array['status']=1;
        }elseif(isset($_GET['status']) && $_GET['status']=='2'){
            $query['where']=where('status',2);
            $array['status']=2;
        }else{
            $array['status']='all';
        }
        $array['articles']=all($query);
        $userids=array();
        foreach ($array['articles'] as $key => $article) {
            if($article['uid']){
                $userids[]=$article['uid'];
            }
        }
        if(count($userids)){
            $usersInfo=C('this:usersInfo',$userids);
            foreach ($array['articles'] as $key => $article) {
                if(isset($usersInfo[$article['uid']])){
                    $array['articles'][$key]['nick']=$usersInfo[$article['uid']]['nick'];
                }
            }
        }
        V('index',$array);
    }
    function test() {
        V('test');
    }
    function code() {
        $config=array('el'=>'#comment','serverURL'=>route('api',array('action'=>''),I(),1),'el'=>'#comment','el'=>'#comment','el'=>'#comment',);
        $config['login']='enable';
        if(!config('userlogin')){
            $config['login']='disable';
        }elseif(!config('guestcomment')){
            $config['login']='force';
        }
        $config['imageUploader']=true;
        if(!config('gif')){
            $config['search']=false;
        }
        $order=config('order');
        if($order==1){
            $config['commentSorting']='oldest';
        }elseif($order==2){
            $config['commentSorting']='hottest';
        }else{
            $config['commentSorting']='latest';
        }
        $config['emoji']=config('emoji');
        if(!$config['emoji']){
            $config['emoji']=false;
        }else{
            $config['emoji']=array_filter(explode(';',$config['emoji']));
        }
        $config['placeholder']=config('placeholder');
        $config['copyright']=config('copyright');
        $config['pageSize']=config('pagesize');
        $config['meta']=array();
        $config['requiredMeta']=array();
        $nick=config('nick');
        if($nick){
            $config['meta'][]='nick';
            $config['requiredMeta'][]='nick';
        }
        $link=config('link');
        if($link){
            $config['meta'][]='link';
            if($link>1){
                $config['requiredMeta'][]='link';
            }
        }
        $mail=config('mail');
        if($mail){
            $config['meta'][]='mail';
            if($mail>1){
                $config['requiredMeta'][]='mail';
            }
        }
        V('code',array('config'=>$config));
    }
    function status(){
        if(!$comment=C('this:get',$_POST['id'])){
            return E('评论不存在');
        }
        if(C('this:edit',array('id'=>$comment['id'],'status'=>intval($_POST['status'])))){
            return '更改成功';
        }elseif(E()){
            return E(E());
        }
        return '更改失败';
    }
    function del() {
        if(isset($_POST['ids'])){
            $ids=explode(';',$_POST['ids']);
        }elseif(isset($_POST['id'])){
            $ids=array($_POST['id']);
        }else{
            return false;
        }
        foreach ($ids as $id) {
            if(!$comment=C('this:get',$id)){
                return E('评论不存在,id:'.$id);
            }
            if(!C('this:del',$id)){
                return E('删除失败,id:'.$id);
            }
        }
        Return '删除成功';
    }
    function detail() {
        if(!$array['comment']=C('this:get',$_GET['id'])){
            Return E('评论不存在');
        }
        if($array['comment']['uid']){
            $array['user']=C('cms:user:get',$array['comment']['uid']);
        }
        if($array['comment']['cid'] && $array['comment']['aid']){
            $array['article']=C('cms:article:getOne',array('cid'=>$array['comment']['cid'],'where'=>array('id'=>$array['comment']['aid'])));
        }
        V('detail',$array);
    }
    function sticky(){
        if(!$comment=C('this:get',$_POST['id'])){
            Return E('评论不存在');
        }
        if($comment['pid']){
            return E('二级评论无法置顶');
        }
        if(C('this:edit',array('id'=>$comment['id'],'sticky'=>intval($_POST['sticky'])))){
            if($_POST['sticky']){
                Return '置顶成功';
            }else{
                Return '已取消置顶';
            }
        }elseif(E()){
            return E(E());
        }
        return '更改失败';
    }
}