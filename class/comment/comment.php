<?php
if(!defined('ClassCms')) {exit();}
class comment {
    function init(){
        Return array('template_dir' => 'template');
    }
    function menu() {
        Return array('title'=>'评论管理','function'=>'admin:index','ico'=>'layui-icon-dialogue');
    }
    function auth() {
        $auth=array('manage'=>'评论管理(前台)','admin:index'=>'评论列表','admin:detail'=>'评论详情','admin:status'=>'评论审核','admin:sticky'=>'置顶评论','admin:del'=>'评论删除','admin:test'=>'评论演示','admin:code'=>'调用代码');
        Return $auth;
    }
    function hook(){
        $hooks=array();
        $hooks[]=array('hookname'=>'login','hookedfunction'=>'admin:index','enabled'=>1,'requires'=>'GET._commentlogin;config.domainlimit');
        Return $hooks;
    }
    function login(){
        if($storage=C('this:userStorage',C('admin:nowUser'))){
            $storage['remember']=0;
            $domains=array_filter(explode(';',config('domainlimit')));
            echo('<script>if(window.opener){');
            echo('window.opener.postMessage({type: "userInfo",data:'.json_encode($storage).'},"/");');
            foreach ($domains as $domain) {
                echo('window.opener.postMessage({type: "userInfo",data:'.json_encode($storage).'},"http://'.$domain.'");');
                echo('window.opener.postMessage({type: "userInfo",data:'.json_encode($storage).'},"https://'.$domain.'");');
            }
            echo('}</script>');
        }
    }
    function route(){
        $routes=array();
        $routes[]=array('hash'=>'api','uri'=>'/'.I().'/','function'=>'api','enabled'=>1);
        $routes[]=array('hash'=>'login','uri'=>'/'.I().'/ui/login','function'=>'api:login','enabled'=>1);
        $routes[]=array('hash'=>'profile','uri'=>'/'.I().'/ui/profile','function'=>'api:profile','enabled'=>1);
        $routes[]=array('hash'=>'comment','uri'=>'/'.I().'/comment','function'=>'api:comment','enabled'=>1);
        $routes[]=array('hash'=>'detail','uri'=>'/'.I().'/comment/(id)','function'=>'api:detail','enabled'=>1);
        $routes[]=array('hash'=>'article','uri'=>'/'.I().'/article','function'=>'api:article','enabled'=>1);
        Return $routes;
    }
    function install() {
        C($GLOBALS['C']['DbClass'].':addIndex',I(),'status');
        C($GLOBALS['C']['DbClass'].':addIndex',I(),'cid');
        C($GLOBALS['C']['DbClass'].':addIndex',I(),'aid');
        C($GLOBALS['C']['DbClass'].':addIndex',I(),'rid');
        C($GLOBALS['C']['DbClass'].':addIndex',I(),'uid');
        C($GLOBALS['C']['DbClass'].':addIndex',I(),'addtime');
        C($GLOBALS['C']['DbClass'].':addIndex',I(),'replycount');
        C($GLOBALS['C']['DbClass'].':addIndex',I(),'likecount');
        C($GLOBALS['C']['DbClass'].':addIndex',I(),'sticky');
        C($GLOBALS['C']['DbClass'].':addIndex',I(),'stickytime');
        update(array('table'=>'class','where'=>array('hash'=>I()),'menu'=>1));
    }
    function get($id) {
        $query=array();
        $query['table']=I();
        $query['where']=array('id'=>$id);
        Return one($query);
    }
    function getSon($id){
        $query=array();
        $query['table']=I();
        $query['where']=array('pid'=>$id);
        Return all($query);
    }
    function getAllSon($id,$sons=array()){
        $thissons=C('this:getSon',$id);
        foreach ($thissons as $thison) {
            $sons[]=$thison;
            $sons=C('this:getAllSon',$thison['id'],$sons);
        }
        return $sons;
    }
    function add($config){
        $comment=array();
        if(isset($config['uid'])){$comment['uid']=intval($config['uid']); }else{ $comment['uid']=0;}
        if(isset($config['cid'])){$comment['cid']=intval($config['cid']); }else{ $comment['cid']=0;}
        if(isset($config['aid'])){$comment['aid']=intval($config['aid']); }else{ $comment['aid']=0;}
        if(isset($config['pid'])){
            $comment['pid']=intval($config['pid']);
            if($comment['pid']>0){
                if($fcomment=C('this:get',$comment['pid'])){
                    if($fcomment['rid']){
                        $comment['rid']=$fcomment['rid'];
                    }else{
                        $comment['rid']=$fcomment['id'];
                    }
                }else{
                    return E('上级评论不存在');
                }
            }
        }else{
            $comment['pid']=0;
        }
        if(!isset($comment['rid'])){
            $comment['rid']=0;
        }
        if(isset($config['url'])){ 
            $comment['url']=C('cms:common:text',$config['url'],300);
        }else{ 
            $comment['url']='';
        }
        $comment['addtime']=time();
        $comment['edittime']=0;
        if(isset($config['status'])){ 
            $comment['status']=$config['status'];
        }else{ 
            if($comment['uid']){
                if(P('manage',I(),$comment['uid'])){
                    $comment['status']=1;
                }elseif(config('usercheck')){
                    $comment['status']=0;
                }else{
                    $comment['status']=1;
                }
            }else{
                if(config('guestcheck')){
                    $comment['status']=0;
                }else{
                    $comment['status']=1;
                }
            }
        }

        if($comment['status']==1 && $comment['pid']){
            C('this:edit',array('id'=>$comment['pid'],'replycount'=>'{{replycount+1}}'));
        }

        $comment['replycount']=0;
        $comment['likecount']=0;
        $comment['sticky']=0;
        $comment['stickytime']=0;

        if(isset($config['nick'])){
            $comment['nick']=C('cms:common:text',$config['nick'],50);
        }else{
            $comment['nick']='';
        }
        if(isset($config['mail'])){
            $comment['mail']=C('cms:common:text',$config['mail'],50);
        }else{
            $comment['mail']='';
        }
        if(isset($config['link'])){
            $comment['link']=C('cms:common:text',$config['link'],125);
        }else{
            $comment['link']='';
        }

        if(isset($config['title'])){
            $comment['title']=C('cms:common:text',$config['title'],125);
        }else{
            $comment['title']='';
        }

        if(isset($config['content_orig'])){
            $comment['content_orig']=C('this:replaceEmoji',$config['content_orig']);
            if(strlen($comment['content_orig'])>60000 ){
                return E('内容太长了');
            }
            if(isset($config['content'])){
                $comment['content']=$config['content'];
            }else{
                $comment['content']=C('cms:common:markdown',htmlspecialchars($comment['content_orig']));
                $comment['content']=str_replace('&amp;','&',$comment['content']);
            }
        }elseif(isset($config['content'])){
            $comment['content']=$config['content'];
            if(strlen($comment['content'])>60000 ){
                return E('内容太长了');
            }
        }else{
            return E('内容不存在');
        }
        if(strlen($comment['content_orig'])>60000 || strlen($comment['content'])>60000){
            return E('内容太长了');
        }
        if(isset($config['ip'])){$comment['ip']=$config['ip'];}else{$comment['ip']=C('cms:common:ip');}
        if(isset($config['city'])){
            $comment['city']=$config['city'];
        }else{
            $location=@C('ip2location:get',$comment['ip']);
            if(isset($location['country']) && $location['country']=='中国'){
                if(isset($location['city']) && $location['city']){
                    $comment['city']=$location['province'].$location['city'];
                }elseif(isset($location['province']) && $location['province']){
                    $comment['city']=$location['province'];
                }
            }elseif(isset($location['country'])){
                $comment['city']=$location['country'];
            }else{
                $comment['city']='';
            }
        }
        $uaInfo=C('this:uaInfo');
        if(isset($config['os'])){
            $comment['os']=$config['os'];
        }else{
            $comment['os']=$uaInfo['os'];
        }
        if(isset($config['browser'])){
            $comment['browser']=$config['browser'];
        }else{
            $comment['browser']=$uaInfo['browser'];
        }
        $comment['table']=I();
        Return insert($comment);
    }
    function edit($config) {
        if(!isset($config['id'])) {
            Return false;
        }
        if(!$thiscomment=C('this:get',$config['id'])) {
            Return false;
        }
        $comment=array('table'=>I(),'where'=>array('id'=>$config['id']));
        if(isset($config['edittime'])){
            $comment['edittime']=$config['edittime'];
        }
        if(isset($config['likecount'])){
            $comment['likecount']=$config['likecount'];
        }
        if(isset($config['replycount'])){
            $comment['replycount']=$config['replycount'];
        }
        
        if(isset($config['sticky'])){
            $comment['sticky']=intval($config['sticky']);
            if($config['sticky']){
                $comment['stickytime']=time();
            }else{
                $comment['stickytime']=0;
            }
        }

        if(isset($config['nick'])){
            $comment['nick']=C('cms:common:text',$config['nick'],50);
        }
        if(isset($config['mail'])){
            $comment['mail']=C('cms:common:text',$config['mail'],50);
        }
        if(isset($config['link'])){
            $comment['link']=C('cms:common:text',$config['link'],125);
        }

        if(isset($config['content_orig'])){
            $comment['content_orig']=C('this:replaceEmoji',$config['content_orig']);
            if(strlen($comment['content_orig'])>60000 ){
                return E('内容太长了');
            }
            if(isset($config['content'])){
                $comment['content']=$config['content'];
            }else{
                $comment['content']=C('cms:common:markdown',htmlspecialchars($comment['content_orig']));
                $comment['content']=str_replace('&amp;','&',$comment['content']);
            }
        }elseif(isset($config['content'])){
            $comment['content']=$config['content'];
            if(strlen($comment['content'])>60000 ){
                return E('内容太长了');
            }
        }
        if(isset($config['status'])){ 
            $comment['status']=$config['status'];
            if($thiscomment['status']!=$comment['status']){
                if($thiscomment['status']==1){
                    C('this:edit',array('id'=>$thiscomment['pid'],'replycount'=>'{{replycount-1}}'));
                }else{
                    C('this:edit',array('id'=>$thiscomment['pid'],'replycount'=>'{{replycount+1}}'));
                }
            }
        }
        if(isset($config['ip'])){
            $comment['ip']=$config['ip'];
            if(isset($config['city'])){
                $comment['city']=$config['city'];
            }else{
                $location=@C('ip2location:get',$comment['ip']);
                if(isset($location['country']) && $location['country']=='中国'){
                    if(isset($location['city']) && $location['city']){
                        $comment['city']=$location['province'].$location['city'];
                    }elseif(isset($location['province']) && $location['province']){
                        $comment['city']=$location['province'];
                    }
                }elseif(isset($location['country'])){
                    $comment['city']=$location['country'];
                }
            }
        }
        if(isset($config['ua'])){
            $uaInfo=C('this:uaInfo',$config['ua']);
            if(isset($config['os'])){
                $comment['os']=$config['os'];
            }else{
                $comment['os']=$uaInfo['os'];
            }
            if(isset($config['browser'])){
                $comment['browser']=$config['browser'];
            }else{
                $comment['browser']=$uaInfo['browser'];
            }
        }
        Return update($comment);
    }
    function del($id) {
        if(!$comment=C('this:get',$id)){
            return E('评论不存在');
        }
        $ids=array($id);
        $sons=C('this:getAllSon',$id);
        foreach ($sons as $key => $son) {
            $ids[]=$son['id'];
        }
        if($comment['pid']){
            C('this:edit',array('id'=>$comment['pid'],'replycount'=>'{{replycount-1}}'));
        }
        $del_query=array();
        $del_query['table']=I();
        $del_query['where']=array('id'=>$ids);
        if(del($del_query)){
            del(array('table'=>I().'_like','where'=>array('pid'=>$ids)));
            return true;
        }
        return false;
    }
    function like($id,$add=1,$userid=0){
        if($userid){
            $like_log=one('table',I().'_like','where',where('pid',$id,'uid',$userid));
        }else{
            $like_log=one('table',I().'_like','where',where('pid',$id,'ip',C('cms:common:ip'),'addtime>',time()-3600*24));
        }
        if($add){
            if($like_log){return E('已经赞过了'); }
            insert('table',I().'_like','uid',$userid,'pid',$id,'addtime',time(),'ip',C('cms:common:ip'));
            return update('table',I(),'where',array('id'=>$id),'likecount','{{likecount+1}}');
        }else{
            if(!$like_log){ return E('还未赞过'); }
            del('table',I().'_like','where',where('id',$like_log['id']));
            return update('table',I(),'where',array('id'=>$id),'likecount','{{likecount-1}}');
        }
    }
    function articleCount($cid,$id){
        return total(I(),where('cid',$cid,'aid',$id,'status',1,'pid',0));
    }
    function recent($limit=10,$offset=0){
        $query=array('table'=>I(),'order'=>'addtime desc','limit'=>$limit,'offset'=>$offset,'where'=>array('status'=>1));
        $comments=all($query);
        $uids=array();
        foreach ($comments as $key => $comment) {
            if(empty(C('cms:common:text',$comment['content'],10))){
                unset($comments[$key]);
            }else{
                $comments[$key]['link']=$comment['url'];
                if(empty($comments[$key]['link']) && $comment['cid'] && $comment['aid']){
                    if($article=C('cms:article:getOne',array('cid'=>$comment['cid'],'where'=>array('id'=>$comment['aid'])))){
                        $comments[$key]['link']=$article['link'];
                    }
                }
                if($comment['uid'] && !in_array($comment['uid'],$uids)){
                    $uids[]=$comment['uid'];
                }
            }
        }
        $users=array();
        if(count($uids)){
            $users=all('table','user','where',array('id'=>$uids));
        }
        if(count($users)){
            foreach ($comments as $key => $comment) {
                if($comment['uid']){
                    foreach ($users as $user) {
                        if($comment['uid']==$user['id']){
                            $comments[$key]['nick']=$user['username'];
                        }
                    }
                }
            }
        }
        if(count($comments)<$limit && $offset<5){
            $newComments=C('this:recent',$limit,$offset+$limit);
            foreach ($newComments as $newcomment) {
                if(count($comments)<=$limit){
                    $comments[]=$newcomment;
                }
            }
        }
        return $comments;
    }
    function replaceEmoji($markdown){
        preg_match_all('/<img class="wl-emoji" src="(.+?)" alt="(.+?)">/sim', $markdown, $emojis);
        if(isset($emojis[2])){
            foreach ($emojis[2] as $key => $emoji_name) {
                $markdown=str_replace($emojis[0][$key],'!['.$emojis[2][$key].']('.$emojis[1][$key].')',$markdown);
            }
        }
        return $markdown;
    }
    function uaInfo($ua=''){
        $info=array('os'=>'','browser'=>'');
        if(empty($ua)){
            $ua=@$_SERVER['HTTP_USER_AGENT'];
        }
        $oss=array('Windows'=>'windows;winnt', 'Android'=>'android', 'IOS'=>'iphone;ipad', 'MacOS'=>'os x', 'Linux'=>'linux;freebsd;debian;ubuntu;centos');
        foreach($oss as $key=>$os) {
            $oss=explode(';',$os);
            foreach ($oss as $str) {
                if(stripos($ua,$str)!==false) {
                    $info['os']=$key;
                    break 2;
                }
            }
        }
        $browsers=array( 'Edge'=>'Edge;edga/;edg/', '微信'=>'micromessenger', '抖音'=>'bytedance', '百度'=>'baidubox', '小米'=>'miuibrowser', '华为'=>'huaweibrowser', 'VIVO'=>'VivoBrowser', 'OPPO'=>'HeyTapBrowser', 'UC'=>'ucbrowser', 'QQ'=>'QQBrowser', '夸克'=>'quark', 'IE'=>'MSIE;Internet Explorer;Trident/', 'Chrome'=>'Chrome', 'Firefox'=>'Firefox', 'Safari'=>'Safari', '360'=>'360se');
        foreach($browsers as $key=>$browser) {
            $browsers=explode(';',$browser);
            foreach ($browsers as $str) {
                if(stripos($ua,$str)!==false) {
                    $info['browser']=$key;
                    break 2;
                }
            }
        }
        return $info;
    }
    function table(){
        return array(
                    I()=>array(
                        'uid'=>'bigint(10)',
                        'rid'=>'bigint(10)',//根评论id
                        'pid'=>'bigint(10)',//回复评论id
                        'cid'=>'bigint(10)',//栏目id
                        'aid'=>'bigint(10)',//文章id
                        'url'=>'varchar(255)',//网址???
                        'addtime'=>'bigint(10)',
                        'edittime'=>'bigint(10)',
                        'status'=>'int(1)',//0:未审核 1:已审核 2:spam
                        'replycount'=>'int(9)',//回复次数
                        'likecount'=>'int(9)',//点赞数
                        'sticky'=>'int(1)',//置顶
                        'stickytime'=>'bigint(10)',//置顶时间,默认=0
                        'nick'=>'varchar(50)',//游客名
                        'mail'=>'varchar(50)',//游客email
                        'link'=>'varchar(125)',//游客homepage
                        'title'=>'varchar(125)',//文章标题
                        'content_orig'=>'text()',//markdown
                        'content'=>'text()',//html
                        'ip'=>'varchar(50)',
                        'city'=>'varchar(20)',
                        'os'=>'varchar(20)',
                        'browser'=>'varchar(20)',
                    ),
                    I().'_like'=>array(
                        'uid'=>'bigint(10)',
                        'pid'=>'bigint(10)',
                        'addtime'=>'bigint(10)',
                        'ip'=>'varchar(50)',
                    )
        );
    }
    function config() {
        $configs=array();
        $configs[]=array('configname'=>'排序','hash'=>'order','inputhash'=>'radio','tabname'=>'界面','defaultvalue'=>'0','values'=>"0:按正序(新至旧)\n1:按倒序(旧至新)\n2:按热度(点赞数多至少)",'savetype'=>1,'tips'=>'评论列表默认的排序方式');
        $configs[]=array('configname'=>'数量','hash'=>'pagesize','inputhash'=>'number','tabname'=>'界面','defaultvalue'=>'10','tips'=>'每次加载的评论数量');
        $configs[]=array('configname'=>'默认文字','hash'=>'placeholder','inputhash'=>'text','tabname'=>'界面','defaultvalue'=>'欢迎评论','tips'=>'评论框默认文字');
        $configs[]=array('configname'=>'表情包','hash'=>'emoji','inputhash'=>'tags','tabname'=>'界面','defaultvalue'=>'https://unpkg.com/@waline/emojis@1.1.0/qq;https://unpkg.com/@waline/emojis@1.1.0/weibo','tips'=>'自定义评论输入框的表情,详见https://waline.js.org/guide/client/emoji.html');
        $configs[]=array('configname'=>'GIF表情','hash'=>'gif','inputhash'=>'switch','tips'=>'是否启用gif表情搜索','tabname'=>'界面','defaultvalue'=>'1');
        $configs[]=array('configname'=>'地址','hash'=>'location','inputhash'=>'switch','tips'=>'是否显示评论者省份信息,安装 <a class="cmscolor" target="_blank" href="https://classcms.com/class/ip2location/">IP位置查询</a> 应用后才能获取ip对应的地理位置','tabname'=>'界面','defaultvalue'=>0);
        $configs[]=array('configname'=>'浏览器','hash'=>'browser','inputhash'=>'switch','tips'=>'是否显示评论者浏览器信息','tabname'=>'界面','defaultvalue'=>'1');
        $configs[]=array('configname'=>'系统','hash'=>'os','inputhash'=>'switch','tips'=>'是否显示评论者操作系统信息','tabname'=>'界面','defaultvalue'=>'1');
        $configs[]=array('configname'=>'版权信息','hash'=>'copyright','inputhash'=>'switch','tips'=>'是否显示页脚版权信息,保持打开以支持 Waline','tabname'=>'界面','defaultvalue'=>'1');

        $configs[]=array('configname'=>'登录','hash'=>'userlogin','inputhash'=>'switch','tips'=>'评论框显示"登录"按钮,点击后将会显示后台登入界面,安装 <a class="cmscolor" target="_blank" href="https://classcms.com/class/user/">会员扩展</a> 插件后可启用会员注册','tabname'=>'会员','defaultvalue'=>0);
        $configs[]=array('configname'=>'安全域名','hash'=>'domainlimit','inputhash'=>'tags','tabname'=>'会员','defaultvalue'=>C('cms:common:serverName'),'tips'=>'限制第三方调用的域名,如域名不匹配,则无法登入');
        $configs[]=array('configname'=>'审核','hash'=>'usercheck','inputhash'=>'switch','tips'=>'会员发表评论是否需要审核','tabname'=>'会员','defaultvalue'=>1);
        $configs[]=array('configname'=>'编辑','hash'=>'allowedit','inputhash'=>'switch','tips'=>'是否允许会员编辑自己的评论,注意:如开启了审核功能,编辑后的评论依然需要重新审核','tabname'=>'会员','defaultvalue'=>0);
        $configs[]=array('configname'=>'删除','hash'=>'allowdel','inputhash'=>'switch','tips'=>'是否允许会员删除自己的评论,注意:如存在下属评论,则只能将评论内容重置为[已删除]','tabname'=>'会员','defaultvalue'=>0);
        $configs[]=array('configname'=>'默认头像','hash'=>'avatar','inputhash'=>'imgupload','tips'=>'用户默认头像','tabname'=>'会员','defaultvalue'=>template_url().'avatar.png');

        $configs[]=array('configname'=>'点赞','hash'=>'guestlike','inputhash'=>'switch','tabname'=>'游客','defaultvalue'=>'0','tips'=>'是否允许游客点赞');
        $configs[]=array('configname'=>'评论','hash'=>'guestcomment','inputhash'=>'switch','tabname'=>'游客','defaultvalue'=>'1','tips'=>'是否允许游客提交评论');
        $configs[]=array('configname'=>'审核','hash'=>'guestcheck','inputhash'=>'switch','tips'=>'游客发表评论是否需要审核','tabname'=>'游客','defaultvalue'=>1);
        $configs[]=array('configname'=>'昵称','hash'=>'nick','inputhash'=>'radio','tabname'=>'游客','defaultvalue'=>'1','values'=>"0:不需要\n1:必填",'savetype'=>1,'tips'=>'游客发表评论时是否需要填写昵称');
        $configs[]=array('configname'=>'邮箱','hash'=>'mail','inputhash'=>'radio','tabname'=>'游客','defaultvalue'=>'1','values'=>"0:不需要\n1:选填\n2:必填",'savetype'=>1,'tips'=>'游客发表评论时是否需要填写邮箱');
        $configs[]=array('configname'=>'网址','hash'=>'link','inputhash'=>'radio','tabname'=>'游客','defaultvalue'=>'1','values'=>"0:不需要\n1:选填\n2:必填",'savetype'=>1,'tips'=>'游客发表评论时是否需要填写个人网站网址');
        Return $configs;
    }
    function css(){
        return '<link rel="stylesheet" href="//'.C('cms:common:serverName').C('cms:common:serverPort').template_url().'waline.css" />';
    }
    function js(){
        return '<script src="//'.C('cms:common:serverName').C('cms:common:serverPort').template_url().'waline.js"></script>';
    }
    function code($config=array()){
        if(!isset($config['el'])){
            return E('未指定对象,详见:https://classcms.com/class/comment/');
        }
        $config['el']=$config['el'];
        $config['serverURL']=route('api',array('action'=>''),I(),1);
        $config['login']='enable';
        if(!config('userlogin')){
            $config['login']='disable';
        }elseif(!config('guestcomment')){
            $config['login']='force';
        }
        if(!isset($config['path'])){
            if(isset($GLOBALS['C']['article']['cid']) && isset($GLOBALS['C']['article']['id'])){
                $config['path']=$GLOBALS['C']['article']['cid'].':'.$GLOBALS['C']['article']['id'];
            }else{
                $config['path']=C('cms:common:url');
            }
        }
        $config['imageUploader']=false;
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
        if(!isset($config['emoji'])){
            $config['emoji']=config('emoji');
            if(!$config['emoji']){
                $config['emoji']=false;
            }else{
                $config['emoji']=array_filter(explode(';',$config['emoji']));
            }
        }
        if(!isset($config['dark'])){
            $config['dark']=false;
        }
        if(!isset($config['placeholder'])){
            $config['placeholder']=config('placeholder');
        }
        if(!isset($config['copyright'])){
            $config['copyright']=config('copyright');
        }
        if(!isset($config['pageSize'])){
            $config['pageSize']=config('pagesize');
        }

        if($userid=C('admin:nowUser')){
            $user=C('cms:user:get',$userid);
        }else{
            $user=false;
        }
        $js='';
        $config['reaction']=false;
        $config['meta']=array();
        $config['requiredMeta']=array();
        if($user){
            if(P('manage',I(),$user['id'])){
                $type='administrator';
            }else{
                $type='';
            }
            if($storage=C('this:userStorage',$user)){
                $js='if(window.localStorage){localStorage.setItem("WALINE_USER",\''.json_encode($storage).'\');}';
            }
        }else{
            $js='if(window.localStorage){localStorage.setItem("WALINE_USER",\'{}\');}';
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
        }
        return '<script>'.$js.'Waline.init('.json_encode($config).');</script>';
    }
    function userStorage($user){
        if(!is_array($user)){
            $user=C('cms:user:get',$user);
            if(!$user){
                return array();
            }
        }
        if(!isset($user['email'])){ $user['email']=''; }
        if(!isset($user['avatar'])){ $user['avatar']=''; }
        if(!isset($user['homepage'])){ $user['homepage']=''; }
        $storage=array('objectId'=>$user['id'],'mail'=>$user['email'],'avatar'=>$user['avatar'],'link'=>$user['homepage'],'nick'=>$user['username'],'display_name'=>$user['username'],'label'=>"");
        if(substr($storage['avatar'],0,1)=='/' && substr($storage['avatar'],0,2)!='//'){
            if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=='on'){
                $storage['avatar']='https://'.C('cms:common:serverName').C('cms:common:serverPort').$storage['avatar'];
            }else{
                $storage['avatar']='http://'.C('cms:common:serverName').C('cms:common:serverPort').$storage['avatar'];
            }
        }
        if(!$storage['avatar']){
            $storage['avatar']=C('this:mail2avatar',$storage['mail']);
        }
        if(P('manage',I(),$user['id'])){
            $storage['type']='administrator';
        }else{
            $storage['type']='';
        }
        $cookieHash=C('admin:cookieHash');
        if (isset($_COOKIE['token'.$cookieHash])  && !empty($_COOKIE['token'.$cookieHash])){
            if(C('cms:user:checkToken',$_COOKIE['token'.$cookieHash])) {
                $storage['token']=$_COOKIE['token'.$cookieHash];
            }
        }
        if(!isset($storage['token'])){
            if($token=C('cms:user:makeToken',$user['id'])){
                $storage['token']=$token;
            }
        }
        return $storage;
    }
    function usersInfo($ids=array()){
        $infos=array();
        $users=all(array('table'=>'user','where'=>array('id'=>$ids)));
        foreach ($users as $user) {
            $infos[$user['id']]=array('id'=>$user['id'],'nick'=>$user['username'],'mail'=>@$user['email'],'link'=>@$user['homepage']);
        }
        return $infos;
    }
    function mail2avatar($mail){
        if(!G('avatar')){ G('avatar','//'.C('cms:common:serverName').C('cms:common:serverPort').config('avatar')); }
        return G('avatar');
    }
    function comment2data($comment,$allcomments=array(),$users=array()){
        if(!G('location')){ G('location',config('location')); }
        if(!G('os')){ G('os',config('os')); }
        if(!G('browser')){ G('browser',config('browser')); }
        if(!G('avatar')){ G('avatar','//'.C('cms:common:serverName').C('cms:common:serverPort').config('avatar')); }
        $return=array('objectId'=>$comment['id'],'like'=>intval($comment['likecount']),'insertedAt'=>date("c",$comment['addtime']),'createdAt'=>date("c",$comment['addtime']),'updatedAt'=>date("c",$comment['edittime']),'comment'=>$comment['content'],'orig'=>$comment['content_orig']);
        if($comment['status']==0){
            $return['status']='waiting';
        }elseif($comment['status']==1){
            $return['status']='approved';
        }else{
            $return['status']='spam';
        }
        if($comment['pid']){
            $return['pid']=$comment['pid'];
            $return['rid']=$comment['rid'];
            foreach ($allcomments as $thiscomment) {
                if($thiscomment['id']==$comment['pid'] && $thiscomment['pid']){//3级评论才显示blockquote
                    $blockquote_username='';
                    if($thiscomment['uid']){
                        foreach ($users as $user) {
                            if(isset($user['id']) && $user['id']==$thiscomment['uid']){
                                $blockquote_username=$user['username'];
                                break;
                            }
                        }
                    }else{
                        $blockquote_username=$thiscomment['nick'];
                    }
                    $replay_content=C('cms:common:text',$thiscomment['content'],150);
                    if(empty($replay_content)){
                        preg_match_all('/<img src="(.+?)"/sim',$thiscomment['content'], $imgs);
                        if(isset($imgs[1])){
                            foreach ($imgs[1] as $key => $img) {
                                if($key<5){
                                    $replay_content.='<img class="wl-emoji" src="'.$img.'">';
                                }
                            }
                            if(count($imgs[1])>4){
                                $replay_content.='...';
                            }
                        }
                    }
                    if($blockquote_username){
                        $return['comment']='<blockquote style="white-space: nowrap;overflow: hidden;text-overflow:ellipsis;"> <a href="#'.$return['pid'].'" xlink:show="new">'.$blockquote_username.'</a>:'.$replay_content.' </blockquote>'.$return['comment'];
                    }else{
                        $return['comment']='<blockquote style="white-space: nowrap;overflow: hidden;text-overflow:ellipsis;"> '.C('cms:common:text',$thiscomment['content'],30,'...').' </blockquote>'.$return['comment'];
                    }
                    break;
                }
            }
        }
        if(G('location')){
            $return['addr']=$comment['city'];
        }
        if(G('os')){
            $return['os']=$comment['os'];
        }
        if(G('browser')){
            $return['browser']=$comment['browser'];
        }
        if($comment['sticky']){
            $return['sticky']=true;
        }
        if($comment['uid']){
            if(isset($GLOBALS['C']['article']['uid']) && $comment['uid']==$GLOBALS['C']['article']['uid']){
                $return['label']='作者';
            }
            if(!count($users)){
                if($user=C('cms:user:get',$comment['uid'])){
                    if(P('manage',I(),$user['id'])){
                        $user['_comment_usertype']='administrator';
                    }else{
                        $user['_comment_usertype']='';
                    }
                    $users=array($user);
                }
            }
            foreach ($users as $user) {
                if(isset($user['id']) && $user['id']==$comment['uid']){
                    $return['nick']=$user['username'];
                    $return['user_id']=$user['id'];
                    $return['link']=@$user['homepage'];
                    $return['mail']=@$user['email'];
                    if(isset($user['_comment_usertype'])){
                        if($user['_comment_usertype']=='administrator'){
                            $return['label']='管理员';
                        }
                    }
                    if(!isset($user['avatar'])){ $user['avatar']=''; }
                    $return['avatar']=@$user['avatar'];
                    if(substr($return['avatar'],0,1)=='/' && substr($return['avatar'],0,2)!='//'){
                        if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=='on'){
                            $return['avatar']='https://'.C('cms:common:serverName').C('cms:common:serverPort').$return['avatar'];
                        }else{
                            $return['avatar']='http://'.C('cms:common:serverName').C('cms:common:serverPort').$return['avatar'];
                        }
                    }
                    if(!$return['avatar']){
                        $return['avatar']=C('this:mail2avatar',$return['mail']);
                    }
                    break;
                }
            }
            if(!isset($return['nick'])){
                $return['nick']='[账号已删除]';
            }
        }else{
            $return['nick']=$comment['nick'];
            $return['link']=$comment['link'];
            $return['mail']=$comment['mail'];
            $return['avatar']=C('this:mail2avatar',$return['mail']);
        }
        $return['children']=array();
        if(!$comment['pid']){
            foreach ($allcomments as $thiscomment) {
                if($thiscomment['rid']==$comment['id']){
                    $return['children'][]=C('this:comment2data',$thiscomment,$allcomments,$users);
                }
            }
        }
        return $return;
    }
}