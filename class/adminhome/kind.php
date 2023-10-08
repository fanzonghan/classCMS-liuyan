<?php
class adminhome_kind {
    function get($hash){
        $kinds=C('this:kind:all');
        foreach ($kinds as $kind) {
            if($kind['hash']==$hash){
                return $kind;
            }
        }
        return false;
    }
    function all(){
        if(C('cms:class:get','kindeditor')) {
            $texteditor='kindeditor';
        }else {
            $editor=one('table','input','where',where('groupname','编辑器','enabled',1,'classenabled',1));
            if($editor){
                $texteditor=$editor['hash'];
            }else{
                $texteditor='textarea';
            }
        }
        return array(
            array('name'=>'文本','hash'=>'text','groupname'=>'常用','function'=>'adminhome:kind:text','config'=>array(array('configname'=>'标题','hash'=>'title','inputhash'=>'text','tips'=>'自定义组件标题','defaultvalue'=>''),array('configname'=>'尺寸','hash'=>'size','inputhash'=>'text','tips'=>'自定义组件尺寸,如:3*2 最小:1*1  最大6*4','defaultvalue'=>'3*1'),array('configname'=>'文本信息','hash'=>'content','inputhash'=>$texteditor,'tips'=>'请输入需要显示在后台主页的文本信息','defaultvalue'=>''))),
            array('name'=>'图片','hash'=>'picture','groupname'=>'常用','function'=>'adminhome:kind:picture','config'=>array(array('configname'=>'标题','hash'=>'title','inputhash'=>'text','tips'=>'自定义组件标题','defaultvalue'=>''),array('configname'=>'尺寸','hash'=>'size','inputhash'=>'text','tips'=>'自定义组件尺寸,如:3*2 最小:1*1  最大6*4','defaultvalue'=>'3*2'),array('configname'=>'图片','hash'=>'img','inputhash'=>'imgupload','tips'=>'请上传需要显示的图片','defaultvalue'=>''))),
            array('name'=>'应用数量','hash'=>'classcount','groupname'=>'常用','function'=>'adminhome:kind:classCount'),
            array('name'=>'模型数量','hash'=>'modulecount','groupname'=>'常用','function'=>'adminhome:kind:moduleCount','config'=>array(array('configname'=>'应用','hash'=>'classhash','module'=>1,'inputhash'=>'classselect','tips'=>'需要显示模型数量的应用','defaultvalue'=>''))),
            array('name'=>'用户数量','hash'=>'usercount','groupname'=>'常用','function'=>'adminhome:kind:userCount'),
            array('name'=>'服务器信息','hash'=>'serverinfos','groupname'=>'常用','function'=>'adminhome:kind:serverInfos'),
            array('name'=>'PHP信息','hash'=>'phpinfos','groupname'=>'常用','function'=>'adminhome:kind:phpInfos'),
            array('name'=>'栏目文章数量','hash'=>'channelarticlecount','groupname'=>'文章','function'=>'adminhome:kind:channelArticleCount','config'=>array(array('configname'=>'栏目','hash'=>'channel','inputhash'=>'classchannel','tips'=>'请选择需要显示文章数量的栏目','defaultvalue'=>''))),
            array('name'=>'模型文章数量','hash'=>'modulearticlecount','groupname'=>'文章','function'=>'adminhome:kind:moduleArticleCount','config'=>array(array('configname'=>'模型','hash'=>'module','inputhash'=>'classmodule','tips'=>'请选择需要显示文章数量的模型','defaultvalue'=>''))),
            array('name'=>'栏目文章列表','hash'=>'channelarticlelist','groupname'=>'文章','function'=>'adminhome:kind:channelArticleList','config'=>array(array('configname'=>'栏目','hash'=>'channel','inputhash'=>'classchannel','tips'=>'请选择需要显示文章的栏目','defaultvalue'=>''),array('configname'=>'尺寸','hash'=>'size','inputhash'=>'text','tips'=>'自定义组件尺寸,如:3*2 最小:1*1  最大6*4','defaultvalue'=>'3*2'),array('configname'=>'文章数量','hash'=>'pagesize','inputhash'=>'number','tips'=>'请填写需要显示的文章数量','defaultvalue'=>'7'))),
            array('name'=>'模型文章列表','hash'=>'modulearticlelist','groupname'=>'文章','function'=>'adminhome:kind:moduleArticleList','config'=>array(array('configname'=>'模型','hash'=>'module','inputhash'=>'classmodule','tips'=>'请选择需要显示文章的模型','defaultvalue'=>''),array('configname'=>'尺寸','hash'=>'size','inputhash'=>'text','tips'=>'自定义组件尺寸,如:3*2 最小:1*1  最大6*4','defaultvalue'=>'3*2'),array('configname'=>'文章数量','hash'=>'pagesize','inputhash'=>'number','tips'=>'请填写需要显示的文章数量','defaultvalue'=>'7'))),
            array('name'=>'栏目数量','hash'=>'channelcount','groupname'=>'栏目','function'=>'adminhome:kind:channelCount','config'=>array(array('configname'=>'应用','hash'=>'classhash','module'=>1,'inputhash'=>'classselect','tips'=>'需要显示栏目数量的应用','defaultvalue'=>''))),
            array('name'=>'下属栏目列表','hash'=>'channellist','groupname'=>'栏目','function'=>'adminhome:kind:channelList','config'=>array(array('configname'=>'父栏目','hash'=>'channel','inputhash'=>'classchannel','tips'=>'显示栏目下属全部栏目列表','defaultvalue'=>''),array('configname'=>'尺寸','hash'=>'size','inputhash'=>'text','tips'=>'自定义组件尺寸,如:3*2 最小:1*1  最大6*4','defaultvalue'=>'3*2'))),
            array('name'=>'模型栏目列表','hash'=>'modulechannellist','groupname'=>'栏目','function'=>'adminhome:kind:moduleChannelList','config'=>array(array('configname'=>'模型','hash'=>'module','inputhash'=>'classmodule','tips'=>'显示模型下属栏目列表','defaultvalue'=>''),array('configname'=>'尺寸','hash'=>'size','inputhash'=>'text','tips'=>'自定义组件尺寸,如:3*2 最小:1*1  最大6*4','defaultvalue'=>'3*2'))),
            array('name'=>'应用栏目列表','hash'=>'classchannellist','groupname'=>'栏目','function'=>'adminhome:kind:classChannelList','config'=>array(array('configname'=>'应用','hash'=>'classhash','module'=>1,'inputhash'=>'classselect','tips'=>'显示应用下属全部栏目列表','defaultvalue'=>''),array('configname'=>'尺寸','hash'=>'size','inputhash'=>'text','tips'=>'自定义组件尺寸,如:3*2 最小:1*1  最大6*4','defaultvalue'=>'3*2'))),
        );
    }
    function getConfigs($hash,$id=0){
        if(!$kind=C('this:kind:get',$hash)){
            return array();
        }
        if(!isset($kind['config'])){
            return array();
        }
        $configs=$kind['config'];
        if(!$configs){
            return array();
        }
        if($id){
            if($card=C('this:card:get',$id)){
                $cardConfigs=$card['cardconfigs'];
            }
        }
        foreach ($configs as $key => $config) {
            if(isset($cardConfigs[$config['hash']])){
                $config['value']=$cardConfigs[$config['hash']];
            }elseif(isset($config['defaultvalue'])){
                $config['value']=$config['defaultvalue'];
            }else{
                $config['value']='';
            }
            $config['auth']['all']=true;
            $config['source']='message';
            $config['ajax_url']='?do='.I().':manage:ajax&kind='.$kind['hash'].'&confighash='.$config['hash'].'&csrf='.C('admin:csrfForm');
            $configs[$key]=C('cms:input:configReset',$config);
        }
        return $configs;
    }
    function classCount($config=array()) {
        $classcount=count(C('cms:class:all'));
        if($classcount>99999){ $h='h3'; }elseif($classcount>999){ $h='h2'; }else{ $h='h1'; }
        return array('size'=>'1*1','title'=>'应用','content'=>'<'.$h.'><a class="cmscolor" lay-href="?do=admin:class:index" lay-text="应用管理">'.$classcount.'</a></'.$h.'>');
    }
    function moduleCount($config=array()){
        if($config['classhash']){
            $modulecount=count(C('cms:module:all',$config['classhash']));
            if($modulecount>99999){ $h='h3'; }elseif($modulecount>999){ $h='h2'; }else{ $h='h1'; }
            return array('size'=>'1*1','title'=>'模型','content'=>'<'.$h.'><a class="cmscolor" lay-href="?do=admin:module:index&classhash='.$config['classhash'].'" lay-text="模型管理">'.$modulecount.'</a></'.$h.'>');
        }else{
            return array('size'=>'1*1','title'=>'模型','content'=>'<h2>未知</h2>');
        }
    }
    function channelCount($config=array()){
        if($config['classhash']){
            $channelcount=total('channel',where(array('classhash'=>$config['classhash'])));
            if($channelcount>99999){ $h='h3'; }elseif($channelcount>999){ $h='h2'; }else{ $h='h1'; }
            return array('size'=>'1*1','title'=>'栏目','content'=>'<'.$h.'><a class="cmscolor" lay-href="?do=admin:channel:index&classhash='.$config['classhash'].'" lay-text="栏目管理">'.$channelcount.'</a></'.$h.'>');
        }else{
            return array('size'=>'1*1','title'=>'栏目','content'=>'<h2>未知</h2>');
        }
    }
    function userCount($config=array()) {
        $usercount=total('user');
        if($usercount>99999){ $h='h3'; }elseif($usercount>999){ $h='h2'; }else{ $h='h1'; }
        return array('size'=>'1*1','title'=>'用户','content'=>'<'.$h.'><a class="cmscolor" lay-href="?do=admin:user:index" lay-text="用户管理">'.total('user').'</a></'.$h.'>');
    }
    function text($config=array()) {
        return $config;
    }
    function picture($config=array()) {
        if(isset($config['img']) && $config['img']){
            $config['content']='<img src="'.$config['img'].'">';
        }
        return $config;
    }
    function channelArticleCount($config=array()){
        $channel=C('cms:channel:get',$config['channel']);
        if(!$channel){
            return array('size'=>'1*1','title'=>'未选择栏目','content'=>'<h2>未知</h2>');
        }
        if(!$module=C('cms:module:get',$channel['modulehash'],$channel['classhash'])){
            return array('size'=>'1*1','title'=>'未知栏目','content'=>'<h2>未知</h2>');
        }
        $total=total($module['table'],where('cid',$channel['id']));
        if($total>99999){ $h='h3'; }elseif($total>999){ $h='h2'; }else{ $h='h1'; }
        return array('size'=>'1*1','title'=>$channel['channelname'],'content'=>'<'.$h.'><a class="cmscolor" lay-href="?do=admin:article:home&cid='.$config['channel'].'"  lay-text="'.$channel['channelname'].'">'.$total.'</a></'.$h.'>');
    }
    function moduleArticleCount($config=array()){
        $classmodule=explode(':',$config['module']);
        $module=C('cms:module:get',@$classmodule[1],$classmodule[0]);
        if(!$module){
            return array('size'=>'1*1','title'=>'未选择模型','content'=>'<h2>未知</h2>');
        }
        $cids=C('cms:channel:moduleChannel',$classmodule[1],0,$classmodule[0]);
        $total=total($module['table'],where('cid',$cids));
        if($total>99999){ $h='h3'; }elseif($total>999){ $h='h2'; }else{ $h='h1'; }
        return array('size'=>'1*1','title'=>$module['modulename'],'content'=>'<'.$h.'>'.$total.'</'.$h.'>');
    }
    function channelArticleList($config=array()){
        $channel=C('admin:article:channelGet',@$config['channel']);
        if(!$channel){
            $config['title']='未选择栏目';
            return $config;
        }
        $article_query=array();
        if(C('admin:moduleAuth',$channel['_module'],'limit|false')) {
            $article_query['where']['uid']=C('admin:nowUser');
        }
        $article_query['cid']=$config['channel'];
        $article_query['column']='id,title';
        $article_query['pagesize']=$config['pagesize'];
        $articles=C('cms:article:get',$article_query);
        $content='<table class="layui-table"lay-size="sm"><tbody> ';
        foreach ($articles as $article) {
            if(!isset($article['title'])){ $article['title']='无title字段'; }
            $content.='<tr><td><a class="cmscolor" lay-href="?do=admin:article:edit&cid='.$article['cid'].'&id='.$article['id'].'"  lay-text="'.$article['title'].'">'.$article['title'].'</a></td>';
            if(isset($article['link']) && $article['link']){
                $content.='<td style="width:40px;text-align:center"><a class="cmscolor" target="_blank" href="'.$article['link'].'">浏览</a></td>';
            }
            $content.='</tr>';
        }
        $content.='</tbody> </table>';
        $config['title']=$channel['channelname'];
        $config['content']=$content;
        $config['link']='?do=admin:article:home&cid='.$config['channel'];
        return $config;
    }
    function moduleArticleList($config=array()){
        $classmodule=explode(':',$config['module']);
        $module=C('cms:module:get',@$classmodule[1],$classmodule[0]);
        if(!$module){
            $config['title']='未选择模型';
            return $config;
        }
        $channels=C('cms:channel:moduleChannel',$classmodule[1],1,$classmodule[0]);
        if(isset($channels[0])){
            $channel=C('admin:article:channelGet',$channels[0]);
            $article_query=array();
            if(C('admin:moduleAuth',$channel['_module'],'limit|false',C('admin:nowUser'))) {
                $article_query['where']['uid']=C('admin:nowUser');
            }
            $article_query['classhash']=$classmodule[0];
            $article_query['modulehash']=$classmodule[1];
            $article_query['column']='id,title';
            $article_query['pagesize']=$config['pagesize'];
            $articles=C('cms:article:get',$article_query);
        }else{
            $articles=array();
        }
        
        $content='<table class="layui-table"lay-size="sm"><tbody> ';
        foreach ($articles as $article) {
            if(!isset($article['title'])){ $article['title']='无title字段'; }
            $content.='<tr><td><a class="cmscolor" lay-href="?do=admin:article:edit&cid='.$article['cid'].'&id='.$article['id'].'"  lay-text="'.$article['title'].'">'.$article['title'].'</a></td>';
            if(isset($article['link']) && $article['link']){
                $content.='<td style="width:40px;text-align:center"><a class="cmscolor" target="_blank" href="'.$article['link'].'">浏览</a></td>';
            }
            $content.='</tr>';
        }
        $content.='</tbody> </table>';
        $config['title']=$module['modulename'];
        $config['content']=$content;
        return $config;
    }
    function channelList($config=array()){
        $title='栏目列表';
        $channels=array();
        if($channel=C('cms:channel:get',$config['channel'])){
            $title=$channel['channelname'];
            $channels=C('cms:channel:nav',$channel['id'],999,$channel['classhash']);
        }
        $content='<div class="layui-row layui-btn-container">';
        foreach ($channels as $thischannel) {
            $content.='<a class="layui-btn layui-btn-sm layui-btn-primary" lay-href="?do=admin:article:home&cid='.$thischannel['id'].'">'.$thischannel['channelname'].'</a>';
        }
        $content.='</div>';
        return array('size'=>$config['size'],'title'=>$title,'content'=>$content);
    }
    function moduleChannelList($config=array()){
        if(isset($config['module']) && $config['module']){
            $classmodule=explode(':',$config['module']);
        }
        $title='栏目列表';
        $channels=array();
        if($module=C('cms:module:get',@$classmodule[1],@$classmodule[0])){
            $title=$module['modulename'];
            $channelids=C('cms:channel:moduleChannel',$classmodule[1],1,$classmodule[0]);
            $channels=array();
            foreach ($channelids as $channelid) {
                if($thischannel=C('cms:channel:get',$channelid)){
                    $channels[]=$thischannel;
                }
            }
        }
        $content='<div class="layui-row layui-btn-container">';
        foreach ($channels as $thischannel) {
            $content.='<a class="layui-btn layui-btn-sm layui-btn-primary" lay-href="?do=admin:article:home&cid='.$thischannel['id'].'">'.$thischannel['channelname'].'</a>';
        }
        $content.='</div>';
        return array('size'=>$config['size'],'title'=>$title,'content'=>$content);
    }
    function classChannelList($config=array()){
        $title='栏目列表';
        $channels=array();
        if($class=C('cms:class:get',@$config['classhash'])){
            $channels=C('cms:channel:nav',0,999,$config['classhash']);
        }
        $content='<div class="layui-row layui-btn-container">';
        foreach ($channels as $thischannel) {
            $content.='<a class="layui-btn layui-btn-sm layui-btn-primary" lay-href="?do=admin:article:home&cid='.$thischannel['id'].'">'.$thischannel['channelname'].'</a>';
        }
        $content.='</div>';
        return array('size'=>$config['size'],'title'=>$title,'content'=>$content);
    }
    function serverInfos($config=array()){
        $inis = ini_get_all();
        $content='<table class="layui-table"><tbody> ';
        $content.='<tr><td style="width:70px">域名</td><td>'.server_name().'</td></tr>';
        $content.='<tr><td>服务器</td><td>'.PHP_OS.'</td></tr>';
        if(isset($_SERVER['SERVER_SOFTWARE'])) {
            $content.='<tr><td>WEB软件</td><td>'.$_SERVER['SERVER_SOFTWARE'].'</td></tr>';
        }
        if(isset($_SERVER['SERVER_PROTOCOL'])) {
            $content.='<tr><td>协议</td><td>'.$_SERVER['SERVER_PROTOCOL'].'</td></tr>';
        }
        if(isset($inis['date.timezone']['local_value'])) {
            $content.='<tr><td>时区</td><td>'.$inis['date.timezone']['local_value'].'</td></tr>';
        }else {
            $content.='<tr><td>时区</td><td>未知</td></tr>';
        }
        $content.='<tr><td>时间</td><td>'.date('Y-m-d H:i:s').'('.time().')</td></tr>';
        $content.='<tr><td>客户端IP</td><td>'.C('cms:common:ip').'</td></tr>';
        $content.='<tr><td>客户端UA</td><td>'.@$_SERVER["HTTP_USER_AGENT"].'</td></tr>';

        $content.='</tbody> </table>';
        $config['title']='服务器信息';
        $config['size']='3*3';
        $config['content']=$content;
        return $config;
    }
    function phpInfos(){
        $inis = ini_get_all();
        $content='<table class="layui-table"><tbody> ';
        $content.='<tr><td style="width:70px">版本</td><td>'.PHP_VERSION.'</td></tr>';
        $content.='<tr><td>运行方式</td><td>'.@php_sapi_name().'</td></tr>';
        if(isset($inis['max_execution_time']['local_value'])) {
            $content.='<tr><td>脚本超时</td><td>'.$inis['max_execution_time']['local_value'].'</td></tr>';
        }else {
            $content.='<tr><td>脚本超时</td><td>未知</td></tr>';
        }
        if(isset($inis['post_max_size']['local_value'])) {
            $content.='<tr><td>POST限制</td><td>'.$inis['post_max_size']['local_value'].'</td></tr>';
        }else {
            $content.='<tr><td>POST限制</td><td>未知</td></tr>';
        }
        if(isset($inis['upload_max_filesize']['local_value'])) {
            $content.='<tr><td>上传限制</td><td>'.$inis['upload_max_filesize']['local_value'].'</td></tr>';
        }else {
            $content.='<tr><td>上传限制</td><td>未知</td></tr>';
        }
        if($exts=@get_loaded_extensions()) {
            $content.='<tr><td>扩展</td><td>'.implode(' ',$exts).'</td></tr>';
        }else {
            $content.='<tr><td>上传限制</td><td>未知</td></tr>';
        }

        $content.='</tbody> </table>';
        $config['title']='PHP信息';
        $config['size']='3*3';
        $config['content']=$content;
        return $config;
    }
}