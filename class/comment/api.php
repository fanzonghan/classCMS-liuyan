<?php
if(!defined('ClassCms')) {exit();}
class comment_api {
    function login(){
        if(!config('userlogin')){
            return false;
        }
        if(isset($_SERVER['HTTP_REFERER'])){
            $domains=array_filter(explode(';',config('domainlimit')));
            $domains[]=C('cms:common:serverName');
            $referer=parse_url($_SERVER['HTTP_REFERER']);
            foreach ($domains as $domain) {
                if(isset($referer['host']) && $referer['host']==$domain){
                    jump(route('adminpath',array(),'admin').'?_commentlogin=1');
                    return true;
                }
            }
        }
        return false;
    }
    function profile(){
        if(!config('userlogin')){
            return false;
        }
        if(isset($_SERVER['HTTP_REFERER'])){
            $domains=array_filter(explode(';',config('domainlimit')));
            $domains[]=C('cms:common:serverName');
            $referer=parse_url($_SERVER['HTTP_REFERER']);
            foreach ($domains as $domain) {
                if(isset($referer['host']) && $referer['host']==$domain){
                    if(!C('admin:nowUser') && isset($_GET['token'])){
                        if($userid=C('cms:user:checkToken',$_GET['token'])){
                            C('admin:adminCookie',$_GET['token']);
                            C('admin:csrfSet',1);
                        }
                    }
                    jump(route('adminpath',array(),'admin').'?home=admin:my:edit&_commentlogin=1');
                    return true;
                }
            }
        }
        return false;
    }
    function article(){
        return C('cms:common:echoJson',array('errmsg'=>'error','errno'=>0));
    }
    function detail(){
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        header('Access-Control-Allow-Methods:*');
        if(!$comment=C('this:get',$_GET['id'])){
            return C('cms:common:echoJson',array('errmsg'=>'评论不存在','errno'=>0));
        }
        $post=json_decode(file_get_contents('php://input'),1);
        $userid=false;
        if(isset($_SERVER['HTTP_AUTHORIZATION'])){
            $userid=C('cms:user:checkToken',str_replace("Bearer ","",$_SERVER['HTTP_AUTHORIZATION']));
        }
        if(!$userid){
            $userid=C('admin:nowUser');
        }
        if(!$userid){
            if(isset($post['like'])){
                if(config('guestlike')){
                    if($post['like']=='true'){
                        $post['like']=true;
                    }else{
                        $post['like']=false;
                    }
                    if(C('this:like',$comment['id'],$post['like'])){
                        return C('cms:common:echoJson',array('errmsg'=>'','errno'=>0));
                    }elseif(E()){
                        return C('cms:common:echoJson',array('errmsg'=>E(),'errno'=>0));
                    }else{
                        return C('cms:common:echoJson',array('errmsg'=>'error','errno'=>0));
                    }
                }
            }
            return C('cms:common:echoJson',array('errmsg'=>'请先登入','errno'=>0));
        }
        $user=C('cms:user:get',$userid);
        if(!$user){
            return C('cms:common:echoJson',array('errmsg'=>'账号异常','errno'=>0));
        }
        if(isset($post['status'])){//审核
            if(!P('manage',I(),$user['id'])){
                return C('cms:common:echoJson',array('errmsg'=>'无权限','errno'=>0));
            }
            if($post['status']=='waiting'){
                $status=0;
            }elseif($post['status']=='approved'){
                $status=1;
            }else{
                $status=2;
            }
            $newcomment=array('id'=>$comment['id'],'status'=>$status);
            if(C('this:edit',$newcomment)){
                return C('cms:common:echoJson',array('errmsg'=>'','errno'=>0,'data'=>''));
            }elseif(E()){
                return C('cms:common:echoJson',array('errmsg'=>E(),'errno'=>0));
            }else{
                return C('cms:common:echoJson',array('errmsg'=>'编辑失败','errno'=>0));
            }
        }elseif(isset($post['like'])){//点赞
            if($post['like']=='true'){
                $post['like']=true;
            }else{
                $post['like']=false;
            }
            if(C('this:like',$comment['id'],$post['like'],$user['id'])){
                return C('cms:common:echoJson',array('errmsg'=>'','errno'=>0));
            }elseif(E()){
                return C('cms:common:echoJson',array('errmsg'=>E(),'errno'=>0));
            }
            return C('cms:common:echoJson',array('errmsg'=>'error','errno'=>0));
        }elseif(isset($post['sticky'])){//置顶
            if(!P('manage',I(),$user['id'])){
                return C('cms:common:echoJson',array('errmsg'=>'无权限','errno'=>0));
            }
            if($comment['pid']){
                return C('cms:common:echoJson',array('errmsg'=>'二级评论无法置顶','errno'=>0));
            }
            if($post['sticky']){
                $post['sticky']=1;
            }else{
                $post['sticky']=0;
            }
            $newcomment=array('id'=>$comment['id'],'sticky'=>$post['sticky']);
            if(C('this:edit',$newcomment)){
                $updatedcomment=C('this:get',$comment['id']);
                $data=C('this:comment2data',$updatedcomment);
                return C('cms:common:echoJson',array('errmsg'=>'','errno'=>0,'data'=>$data));
            }elseif(E()){
                return C('cms:common:echoJson',array('errmsg'=>E(),'errno'=>0));
            }else{
                return C('cms:common:echoJson',array('errmsg'=>'编辑失败','errno'=>0));
            }
        }elseif(isset($post['comment']['comment'])){//修改评论
            $newcomment=array('id'=>$comment['id'],'content_orig'=>$post['comment']['comment'],'edittime'=>time(),'ip'=>C('cms:common:ip'));
            if(!P('manage',I(),$user['id'])){
                if($user['id']==$comment['uid']){
                    if(config('allowedit')){
                        if(config('usercheck')){
                            $newcomment['status']=0;
                        }
                    }else{
                        return C('cms:common:echoJson',array('errmsg'=>'无法修改已发表的评论','errno'=>0));
                    }
                }else{
                    return C('cms:common:echoJson',array('errmsg'=>'无权限','errno'=>0));
                }
            }
            if(C('this:edit',$newcomment)){
                $updatedcomment=C('this:get',$comment['id']);
                $data=C('this:comment2data',$updatedcomment);
                if($data['status']=='waiting'){
                    $data['label']='需审核';
                    $data['comment']='[编辑后需重新审核]<br>'.$data['comment'];
                }
                return C('cms:common:echoJson',array('errmsg'=>'','errno'=>0,'data'=>$data));
            }elseif(E()){
                return C('cms:common:echoJson',array('errmsg'=>E(),'errno'=>0));
            }else{
                return C('cms:common:echoJson',array('errmsg'=>'编辑失败','errno'=>0));
            }
        }elseif(strtolower($_SERVER['REQUEST_METHOD'])=='delete'){//删除评论
            if(!P('manage',I(),$user['id'])){
                if($comment['uid']!=$user['id']){
                    return C('cms:common:echoJson',array('errmsg'=>'无权限','errno'=>0));
                }
                if(!config('allowdel')){
                    return C('cms:common:echoJson',array('errmsg'=>'无法删除评论','errno'=>0));
                }
                if(one(array('table'=>I(),'where'=>array('pid'=>$comment['id'])))){
                    $newcomment=array('id'=>$comment['id'],'content_orig'=>'[已被删除]');
                    C('this:edit',$newcomment);
                    $updatedcomment=C('this:get',$comment['id']);
                    $data=C('this:comment2data',$updatedcomment);
                    return C('cms:common:echoJson',array('errmsg'=>'','errno'=>0,'data'=>$data));
                }
            }
            if(C('this:del',$comment['id'])){
                return C('cms:common:echoJson',array('errmsg'=>'','errno'=>0,'some'=>''));
            }elseif(E()){
                return C('cms:common:echoJson',array('errmsg'=>E(),'errno'=>0,'data'=>''));
            }else{
                return C('cms:common:echoJson',array('errmsg'=>'删除失败','errno'=>0,'data'=>''));
            }
        }
        return false;
    }
    function comment(){
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        header('Access-Control-Allow-Methods:*');
        if(isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD'])=='options'){
            return true;
        }
        $userid=false;
        if(isset($_SERVER['HTTP_AUTHORIZATION'])){
            $userid=C('cms:user:checkToken',str_replace("Bearer ","",$_SERVER['HTTP_AUTHORIZATION']));
        }
        if(!$userid){
            $userid=C('admin:nowUser');
        }
        if($_SERVER['REQUEST_METHOD']==='GET'){//评论列表
            $query=array();
            if(!isset($_GET['path'])){
                return false;
            }
            $cid_id=explode(':',$_GET['path']);
            if(C('cms:common:verify',$cid_id[0],'id') && C('cms:common:verify',@$cid_id[1],'id')){
                $query['where']['cid']=$cid_id[0];
                $query['where']['aid']=$cid_id[1];
                $GLOBALS['C']['article']=C('cms:article:getOne',array('cid'=>$query['where']['cid'],'where'=>array('id'=>$query['where']['aid'])));
            }else{
                $query['where']['url']=$_GET['path'];
            }
            $query['where']['rid']=0;
            $query['table']=I();
            $query['page']=page('pagename','page','pagesize',config('pagesize'));
            if(!isset($_GET['sortBy'])){
                $_GET['sortBy']='insertedAt_desc';
            }
            if($_GET['sortBy']=='like_desc'){
                $query['order']='sticky desc,stickytime asc,likecount desc,addtime desc';
            }elseif($_GET['sortBy']=='insertedAt_asc'){
                $query['order']='sticky desc,stickytime asc,addtime asc';
            }else{
                $query['order']='sticky desc,stickytime asc,addtime desc';
            }
            $status=true;
            if($userid){
                if(P('manage',I(),$userid)){
                    $status=false;
                }
            }
            if($status){
                $query['where']['status']=1;
            }
            $comments=all($query);
            pagelist();
            $pageinfo=pageinfo();
            $comments_ids=array();
            $comments_uids=array();
            foreach ($comments as $key => $comment) {
                $comments_ids[]=$comment['id'];
                if($comment['uid'] && !in_array($comment['uid'],$comments_uids)){
                    $comments_uids[]=$comment['uid'];
                }
            }
            $return=array('page'=>$pageinfo['page'],'totalPages'=>$pageinfo['pagecount'],'pageSize'=>$pageinfo['pagesize'],'count'=>$pageinfo['article']);
            if(count($comments_ids)){
                $children_where=$query['where'];
                $children_where['rid']=$comments_ids;
                $children=all('table',I(),'order','sticky desc,stickytime asc,addtime asc','where',$children_where);
                foreach ($children as $child) {
                    if($child['uid'] && !in_array($child['uid'],$comments_uids)){
                        $comments_uids[]=$child['uid'];
                    }
                }
            }else{
                $children=array();
            }
            $allcomments=array_merge($comments,$children);
            if(count($comments_uids)){
                $users=all('table','user','where',array('id'=>$comments_uids));
                foreach ($users as $key => $thisuser) {
                    if(P('manage',I(),$thisuser['id'])){
                        $users[$key]['_comment_usertype']='administrator';
                    }else{
                        $users[$key]['_comment_usertype']='';
                    }
                }
            }else{
                $users=array();
            }
            $return['data']=array();
            foreach ($comments as $comment) {
                $thiscomment=C('this:comment2data',$comment,$allcomments,$users);
                $return['data'][]=$thiscomment;
            }
            return C('cms:common:echoJson',$return);
        }
        if($_SERVER['REQUEST_METHOD']==='POST'){//提交评论
            $post=json_decode(file_get_contents('php://input'),1);
            $comment=array();
            if(isset($post['pid'])){
                $comment['pid']=intval($post['pid']);
            }else{
                $comment['pid']=0;
            }
            if($userid){
                $user=C('cms:user:get',$userid);
            }else{
                if(!config('guestcomment')){
                    return C('cms:common:echoJson',array('errmsg'=>'请先登入'));
                }
                $user=false;
            }
            if($user){
                $comment['uid']=$user['id'];
                $comment['nick']=$user['username'];
                if(P('manage',I(),$user['id'])){
                    $user['_comment_usertype']='administrator';
                }else{
                    $user['_comment_usertype']='';
                }
            }else{
                $comment['uid']=0;
                $comment['mail']=@$post['mail'];
                if($comment['mail'] && !C('cms:common:verify',$comment['mail'],'email')){
                    return C('cms:common:echoJson',array('errmsg'=>'邮箱格式不正确'));
                }
                $comment['link']=@$post['link'];
                if($comment['link']) {
                    if(stripos($comment['link'],'@') || (strtolower(substr($comment['link'],0,7))!='http://' && strtolower(substr($comment['link'],0,8))!='https://')){
                        return C('cms:common:echoJson',array('errmsg'=>'网址格式不正确'));
                    }
                }
                $comment['nick']=@$post['nick'];
            }
            if(isset($post['comment'])){
                $comment['content_orig']=$post['comment'];
            }else{
                $comment['content_orig']='';
            }
            if(!$comment['content_orig']){
                return C('cms:common:echoJson',array('errmsg'=>'请填写评论'));
            }
            if(isset($post['url'])){
                $cid_id=explode(':',$post['url']);
                if(C('cms:common:verify',$cid_id[0],'id') && C('cms:common:verify',@$cid_id[1],'id')){
                    $comment['cid']=$cid_id[0];
                    $comment['aid']=$cid_id[1];
                }else{
                    $comment['url']=$post['url'];
                }
            }
            $id=C('this:add',$comment);
            if($id){
                if(!$newcomment=C('this:get',$id)){
                    return C('cms:common:echoJson',array('errmsg'=>'评论失败,请联系管理员'));
                }
                $return=array('errmsg'=>'','errno'=>0);
                $comments=array();
                if($comment['pid'] && $pid_comments=one(array('table'=>I(),'where'=>array('id'=>$comment['pid'])))){
                    $comments[]=$pid_comments;
                }
                $return['data']=C('this:comment2data',$newcomment,$comments,array($user));
                if($return['data']['status']=='waiting'){
                    $return['data']['label']='待审核';
                }
                return C('cms:common:echoJson',$return);
            }else{
                return C('cms:common:echoJson',array('errmsg'=>E()));
            }
        }
    }
}