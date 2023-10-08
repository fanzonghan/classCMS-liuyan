<?php
if(!defined('ClassCms')) {exit();}
class comment_message {
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'addWatch','hookedfunction'=>'comment:add:=','enabled'=>1,'requires'=>'');
        $hooks[]=array('hookname'=>'edit','hookedfunction'=>'comment:edit','enabled'=>1,'requires'=>'');
        $hooks[]=array('hookname'=>'editWatch','hookedfunction'=>'comment:edit:=','enabled'=>1,'requires'=>'');
        $hooks[]=array('hookname'=>'kind','hookedfunction'=>'message:kind:all:=','enabled'=>1);
        Return $hooks;
    }
    function kind($class,$args,$return){
        $return[]=array( 'title'=>'文章评论', 'hash'=>'article', 'groupname'=>'评论通知', 'tips'=>'我的文章收到评论时,发送通知给我', 'classhash'=>I());
        $return[]=array( 'title'=>'回复评论', 'hash'=>'reply', 'groupname'=>'评论通知', 'tips'=>'我的评论收到回复时,发送通知给我', 'classhash'=>I());
        $return[]=array( 'title'=>'所有评论', 'hash'=>'all', 'groupname'=>'评论通知', 'tips'=>'收到任何评论时会将消息发送给我(适合站点管理员使用)', 'classhash'=>I());
        return $return;
    }
    function config() {
        if($admin=C('cms:user:get',C('admin:nowUser'))){
            $userhash=$admin['hash'];
        }else{
            $userhash='';
        }
        $configs=array();
        $configs[]=array('configname'=>'时间限制','hash'=>'limit','inputhash'=>'number','tabname'=>'','defaultvalue'=>180,'tips'=>'单个用户在时间限制内,只会收到一条评论通知,单位:秒,填0则不限制');
        $configs[]=array('configname'=>'全部评论通知','hash'=>'all','inputhash'=>'tags','tabname'=>'','defaultvalue'=>$userhash,'tips'=>'收到任何评论时会将消息发送给填写的账户(适合站点管理员使用)');
        $configs[]=array('configname'=>'文章评论','hash'=>'article','inputhash'=>'switch','tabname'=>'','defaultvalue'=>1,'tips'=>'当文章被回复时,将通知发送给文章作者');
        $configs[]=array('configname'=>'回复通知','hash'=>'reply','inputhash'=>'switch','tabname'=>'','defaultvalue'=>1,'tips'=>'当评论被回复时,将通知发送给被评论用户');
        $configs[]=array('configname'=>'游客通知','hash'=>'guest','inputhash'=>'switch','tabname'=>'','defaultvalue'=>0,'tips'=>'当游客评论被回复时,将发送给游客email通知');
        Return $configs;
    }
    function limit($uid,$set=false){
        if($set){
            return config('limit_'.$uid,time());
        }else{
            $last_time=config('limit_'.$uid);
            if(!$last_time){
                $last_time=0;
            }
            if(time()>=($last_time+config('limit'))){
                return true;
            }
        }
        return false;
    }
    function addWatch($class,$args,$return){
        if(!$return){
            return;
        }
        if(!$comment=C('comment:get',$return)){
            return;
        }
        $p_comment=false;
        if($comment['pid']){
            $p_comment=C('comment:get',$comment['pid']);
        }
        $link_html='';
        if($comment['cid'] && $comment['aid']){
            $article=C('cms:article:getOne',array('cid'=>$comment['cid'],'fullurl'=>1,'where'=>array('id'=>$comment['aid'])));
            if($article){
                $link_html=' <a href="'.$article['link'].'" target="_blank">'.$article['link'].'</a>';
            }
        }
        if(substr($comment['link'],0,7)=='http://' || substr($comment['link'],0,8)=='https://') {
            $link_html=' <a href="'.$comment['url'].'" target="_blank">'.$comment['url'].'</a>';
        }
        $receivers=array();
        //管理员通知
        if($all=config('all')){
            $all_users=array_filter(explode(';',$all));
            $status_tips='';
            if(!$comment['status']){
                $status_tips='[需审核] ';
            }
            foreach ($all_users as $userhash) {
                if($thisuser=C('cms:user:get',$userhash)){
                    if(C('this:limit',$thisuser['id'])){
                        $receivers[]=$thisuser['id'];
                        C('message:add',array('userid'=>$thisuser['id'],'kind'=>'all','title'=>$status_tips.$comment['nick'].'发表了新评论','content'=>C('cms:common:text',$comment['content'],200,'...').$link_html));
                        C('this:limit',$thisuser['id'],true);
                    }
                }
            }
        }
        if($comment['status']==1){//审核通过才发送通知
            //游客通知
            if($p_comment && !$p_comment['uid'] && $p_comment['status']==1 && $p_comment['mail'] && config('guest')){
                if(C('this:limit',$p_comment['mail'])){
                    $mail_config=array('title'=>$comment['nick'].' 回复了您的评论','to'=>$p_comment['mail'],'content'=>C('cms:common:text',$comment['content'],200,'...').$link_html);
                    C('email:send',$mail_config);
                    C('this:limit',$p_comment['mail'],true);
                }
            }
            //回复通知
            if($p_comment && $p_comment['uid'] && $p_comment['status']==1 && !in_array($p_comment['uid'],$receivers) && config('reply')){
                if(C('this:limit',$p_comment['uid'])){
                    $receivers[]=$p_comment['uid'];
                    C('message:add',array('userid'=>$p_comment['uid'],'kind'=>'reply','title'=>$comment['nick'].' 回复了我的评论','content'=>C('cms:common:text',$comment['content'],200,'...').$link_html));
                    C('this:limit',$p_comment['uid'],true);
                }
            }
            //文章通知
            if(isset($article['uid']) && $article['uid']  && !in_array($article['uid'],$receivers)  && config('article')){
                if(C('this:limit',$article['uid'])){
                    $receivers[]=$article['uid'];
                    C('message:add',array('userid'=>$article['uid'],'kind'=>'article','title'=>$comment['nick'].' 评论了我的文章','content'=>C('cms:common:text',$comment['content'],200,'...').$link_html));
                    C('this:limit',$article['uid'],true);
                }
            }
        }
    }
    function edit($config){
        if(isset($config['id']) && !isset($config['replycount'])){
            $GLOBALS['comment']['last_edit_comment']=C('comment:get',$config['id']);
        }
    }
    function editWatch($class,$args,$return){
        if(!$return || isset($args[0]['replycount'])){
            return;
        }
        if(!$comment=C('comment:get',$args[0]['id'])){
            return;
        }
        if(!isset($GLOBALS['comment']['last_edit_comment']) || !$GLOBALS['comment']['last_edit_comment']){
            return;
        }
        if($GLOBALS['comment']['last_edit_comment']['status']==$comment['status']){//状态未改变
            return;
        }
        $p_comment=false;
        if($comment['pid']){
            $p_comment=C('comment:get',$comment['pid']);
        }
        $link_html='';
        if($comment['cid'] && $comment['aid']){
            $article=C('cms:article:getOne',array('cid'=>$comment['cid'],'fullurl'=>1,'where'=>array('id'=>$comment['aid'])));
            if($article){
                $link_html=' <a href="'.$article['link'].'" target="_blank">'.$article['link'].'</a>';
            }
        }
        if(substr($comment['link'],0,7)=='http://' || substr($comment['link'],0,8)=='https://') {
            $link_html=' <a href="'.$comment['url'].'" target="_blank">'.$comment['url'].'</a>';
        }
        $receivers=array(C('admin:nowUser'));
        if(isset($_SERVER['HTTP_AUTHORIZATION'])){
            $userid=C('cms:user:checkToken',str_replace("Bearer ","",$_SERVER['HTTP_AUTHORIZATION']));
            if($userid){
                $receivers[]=$userid;
            }
        }
        //管理员通知
        if($all=config('all')){
            $all_users=array_filter(explode(';',$all));
            $status_tips='';
            if(!$comment['status']){
                $status_tips='[需审核] ';
            }
            foreach ($all_users as $userhash) {
                if($thisuser=C('cms:user:get',$userhash)){
                    if(!in_array($thisuser['id'],$receivers)){
                        if(C('this:limit',$thisuser['id'])){
                            $receivers[]=$thisuser['id'];
                            C('message:add',array('userid'=>$thisuser['id'],'kind'=>'all','title'=>$status_tips.$comment['nick'].'发表了新评论','content'=>C('cms:common:text',$comment['content'],200,'...').$link_html));
                            C('this:limit',$thisuser['id'],true);
                        }
                    }
                }
            }
        }
        if($p_comment && $p_comment['status']!=1){//上级评论需审核通过才发送通知
            return;
        }
        if($comment['status']==1){//审核通过才发送通知
            //游客通知
            if($p_comment && !$p_comment['uid'] && $p_comment['mail'] && config('guest')){
                if(C('this:limit',$p_comment['mail'])){
                    $mail_config=array('title'=>$comment['nick'].' 回复了您的评论','to'=>$p_comment['mail'],'content'=>C('cms:common:text',$comment['content'],200,'...').$link_html);
                    C('email:send',$mail_config);
                    C('this:limit',$p_comment['mail'],true);
                }
            }
            //回复通知
            if($p_comment && $p_comment['uid'] && !in_array($p_comment['uid'],$receivers) && config('reply')){
                if(C('this:limit',$p_comment['uid'])){
                    $receivers[]=$p_comment['uid'];
                    C('message:add',array('userid'=>$p_comment['uid'],'kind'=>'reply','title'=>$comment['nick'].' 回复了我的评论','content'=>C('cms:common:text',$comment['content'],200,'...').$link_html));
                    C('this:limit',$p_comment['uid'],true);
                }
            }
            //文章通知
            if(isset($article['uid']) && $article['uid']  && !in_array($article['uid'],$receivers)  && config('article')){
                if(C('this:limit',$article['uid'])){
                    $receivers[]=$article['uid'];
                    C('message:add',array('userid'=>$article['uid'],'kind'=>'article','title'=>$comment['nick'].' 评论了我的文章','content'=>C('cms:common:text',$comment['content'],200,'...').$link_html));
                    C('this:limit',$article['uid'],true);
                }
                
            }
        }
    }
}
