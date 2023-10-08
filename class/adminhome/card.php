<?php
class adminhome_card {
    function all(){
        $cards=all('table',I().'_card','order','cardorder desc,id asc','where',where('enabled',1));
        $manage=P('manage:add');
        $user=C('cms:user:get',C('admin:nowUser'));
        $user_rolehashs=array_filter(explode(';',$user['rolehash']));
        $classes=C('cms:class:all');
        $kinds=C('this:kind:all');
        foreach ($cards as $key => $card) {
            $show=false;
            if($manage){
                $show=true;
            }else{
                $thisrolehashs=array_filter(explode(';',$card['rolehash']));
                foreach ($user_rolehashs as $user_rolehash) {
                    if(in_array($user_rolehash,$thisrolehashs)){
                        $show=true;
                    }
                }
            }
            if($kind=C('this:kind:get',$card['kindhash'])){
                $thisfunction=explode(':',$kind['function']);
                foreach ($classes as $class) {
                    if($class['hash']==$thisfunction[0] && !$class['enabled']){
                        $show=false;
                    }
                }
            }else{
                $show=false;
            }
            if($show){
                $cards[$key]['cardconfigs']=json_decode($card['cardconfigs'],1);
            }else{
                unset($cards[$key]);
            }
        }
        return $cards;
    }
    function get($id){
        $card=one('table',I().'_card','where',where('id',$id));
        if(!$card){
            return false;
        }
        $card['cardconfigs']=json_decode($card['cardconfigs'],1);
        return $card;
    }
    function build($card){
        if(!is_array($card)){
            if(!$card=C('this:card:get',$card)){
                return false;
            }
        }
        $kind=C('this:kind:get',$card['kindhash']);
        if(!$kind){
            return false;
        }
        $card['cardconfigs']['id']=$card['id'];
        $build=C($kind['function'],$card['cardconfigs']);
        if(!isset($build['size']) || !$build['size']){ $build['size']='6'; }
        $sizes=explode('*',trim($build['size']));
        if($sizes[0]==1){ $layout='layui-col-lg1 layui-col-md2 layui-col-sm4 layui-col-xs6'; }
        if($sizes[0]==2){ $layout='layui-col-lg2 layui-col-md4 layui-col-sm4 layui-col-xs6'; }
        if($sizes[0]==3){ $layout='layui-col-lg4 layui-col-md6 layui-col-sm6 layui-col-xs12'; }
        if($sizes[0]==4){ $layout='layui-col-lg6 layui-col-md6 layui-col-sm6 layui-col-xs12'; }
        if($sizes[0]==5){ $layout='layui-col-lg8 layui-col-md8 layui-col-sm8 layui-col-xs12'; }
        if($sizes[0]==6){ $layout='layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12'; }
        if(!isset($layout)){ $layout='layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12'; }
        if(!isset($build['layout']) || !$build['layout']){ $build['layout']=$layout; }
        if(!isset($build['height']) && isset($sizes[1]) && $sizes[1]){
            $build['height']=' cardheight-'.$sizes[1];
        }
        if(!isset($build['title'])){ $build['title']=''; }
        if(empty($build['title'])){
            $build['title']=$kind['name'];
        }
        $html='<div class="'.$build['layout'].'" rel="'.$card['id'].'"><div class="layui-card">';
        $html.='<div class="layui-card-header">';
        $html.='<span';
        if(isset($build['color']) && $build['color']){
            $html.=' style="color:'.$build['color'].'"';
        }
        $html.='>'.$build['title'].'</span>';
        $html.='<p class="action layui-layout-right">';
        if(isset($build['link']) && $build['link']){
            if(!isset($build['linktitle']) || empty($build['linktitle'])){
                $build['linktitle']='查看';
            }
            $html.='<a class="more" lay-href="'.$build['link'].'" lay-text="'.$build['title'].'">'.$build['linktitle'].'</a>';
        }
        $html.='</p></div>';
        if(isset($build['content']) && $build['content']){
            $html.='<div class="layui-card-body';
            if(isset($build['height']) && $build['height']){
                $html.=' '.$build['height'];
            }
            $html.='">';
            $html.=$build['content'];
            $html.='</div>';
        }
        $html.='</div></div>';
        return $html;
    }
    function add($config=array()){
        if(!isset($config['kindhash'])){
            return false;
        }
        $kind=C('this:kind:get',$config['kindhash']);
        if(!$kind){
            return false;
        }
        if(!isset($config['cardconfigs'])){
            $config['cardconfigs']=array();
        }
        if(isset($kind['config']) && $kind['config']){
            foreach ($kind['config'] as $thisconfig) {
                if(!isset($config['cardconfigs'][$thisconfig['hash']])){
                    if(isset($thisconfig['defaultvalue'])){
                        $config['cardconfigs'][$thisconfig['hash']]=$thisconfig['defaultvalue'];
                    }
                }
            }
        }
        $config['table']=I().'_card';
        $query['enabled']=1;
        if(!isset($config['enabled'])){
            $config['enabled']=1;
        }
        if(!isset($config['rolehash'])){
            $config['rolehash']=C('cms:user:$admin_role');
        }
        if(!isset($config['cardorder'])){
            $config['cardorder']=1;
        }
        $config['cardconfigs']=json_encode($config['cardconfigs']);
        $id=insert($config);
        return $id;
    }
    function edit($config=array()){
        $card=C('this:card:get',$config['id']);
        if(!$card){
            return false;
        }
        $query=$config;
        $query['table']=I().'_card';
        $query['where']=where('id',$config['id']);
        if(isset($query['cardconfigs']) && is_array($query['cardconfigs'])){
            $query['cardconfigs']=json_encode($query['cardconfigs']);
        }
        return update($query);
    }
    function del($id){
        $card=C('this:card:get',$id);
        if(!$card){
            return false;
        }
        return del('table',I().'_card','where',where('id',$id));
    }
}