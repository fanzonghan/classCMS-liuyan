<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(评论列表)}</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-form">
            <div class="layui-card">
                <div class="layui-card-header">
                    <div class="layui-row">
                        <?php
                            $breadcrumb=array(array('url'=>'?do=admin:class:config&hash='.I(),'title'=>'评论'),array('title'=>'评论列表'));
                        ?>
                        <div id="cms-breadcrumb">{admin:breadcrumb($breadcrumb)}</div>
                        <div id="cms-right-top-button">
                            {if P('admin:code')}<a class="layui-btn layui-btn-sm layui-btn-danger commentcode"><i class="layui-icon layui-icon-fonts-code"></i><b>调用代码</b></a>{/if}
                            {if P('admin:test')}<a class="layui-btn layui-btn-sm layui-btn-danger commenttest"><i class="layui-icon layui-icon-picture"></i><b>评论演示</b></a>{/if}
                        </div>
                    </div>
                </div>
                <div class="layui-card-body">
                    <div class="layui-inline">
                        <select name="status" lay-filter="status">
                        <option value="all"{if $status=='all'} selected{/if}>全部</option>
                        <option value="0"{if $status=='0'} selected{/if}>待审核</option>
                        <option value="1"{if $status=='1'} selected{/if}>已审核</option>
                        <option value="2"{if $status=='2'} selected{/if}>垃圾</option>
                        </select>
                    </div>
                    <table class="layui-table" lay-skin="line" id="articles">
                    <thead>
                        <tr>
                            <th style="width:20px"></th>
                            <th>评论</th>
                            <th class="layui-hide-xs" style="width:140px">时间</th>
                            <th class="layui-hide-xs" style="width:140px">评论者</th>
                            {if P('admin:status')}<th style="width:175px;text-align:center">审核</th>{/if}
                            <th style="width:180px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {loop $articles as $article}
                            <tr rel="{$article.id}">
                            <td><input type="checkbox" name="check_article" lay-skin="primary" ></td>
                            <td>
                                {if $article.sticky}[置顶] {/if}
                                <?php
                                    $content=C('cms:common:text',$article['content'],150,' <a class="cmscolor articledetail" style="cursor:pointer">详情</a>');
                                    if($content){
                                        echo($content);
                                    }else{
                                        echo('[无文字内容]');
                                    }
                                ?>
                            </td>
                            <td class="layui-hide-xs">{if $article.addtime}{date(m-d H:i,$article.addtime)}{/if}</td>
                            <td class="layui-hide-xs">{if $article.uid}{$article.nick}{else}{$article.nick} [游客]{/if}</td>
                            {if P('admin:status')}<td><a class="status_change layui-btn layui-btn-sm {if $article.status==0}cms-btn{else}layui-btn-primary{/if}" rel="0">待审核</a> <a class="status_change layui-btn layui-btn-sm {if $article.status==1}cms-btn{else}layui-btn-primary{/if}" rel="1">通过</a> <a class="status_change layui-btn layui-btn-sm  {if $article.status==2}cms-btn{else}layui-btn-primary{/if}" rel="2">垃圾</a></td>{/if}
                            <td class="btn">
                                {if P('admin:detail')}<a class="layui-btn layui-btn-sm layui-btn-primary articledetail">详情</a>{/if}
                                {if P('admin:del')}<a class="layui-btn layui-btn-sm layui-btn-primary articledel">删除</a>{/if}
                            </td>
                            </tr>
                        {/loop}
                    </tbody>
                    </table>
                    <div class="layui-row">
                        <div id="cms-left-bottom-button" class="layui-btn-container">
                        {if P('del')}
                        <a class="layui-btn layui-btn-sm layui-btn-primary choseall">全选</a>
                        <a class="layui-btn layui-btn-sm layui-btn-primary choseback">反选</a>
                        <a class="layui-btn layui-btn-sm layui-btn-primary delchosed">删除</a>
                        {/if}
                        </div>
                        <div id="cms-right-bottom-button" class="layui-btn-container">
                            {admin:pagelist()}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function chosedArticle(){
    articles=new Array();
    layui.$('#articles tbody input[name=check_article]').each(function(){
        if (layui.$(this).prop("checked"))
        {
            articles.push(layui.$(this).parents('tr').attr('rel'));
        }
    });
    return articles.join(';');
}
layui.use(['index'],function(){
    layui.form.on('select(status)', function(data){
        window.location.href="?do=comment:admin:index&status="+data.value;
    });
    layui.$('.choseall').click(function(){
        layui.$('#articles tbody input[name=check_article]').prop("checked",true);
        layui.form.render('checkbox');
    });
    layui.$('.choseback').click(function(){
        layui.$('#articles tbody input[name=check_article]').each(function(){
            if (layui.$(this).prop("checked"))
            {
                layui.$(this).prop("checked",false);
            }else{
                layui.$(this).prop("checked",true);
            }
        });
        layui.form.render('checkbox');
    });
    layui.$('.delchosed').click(function(){
        delArticle(chosedArticle());
    });
    function delArticle(ids){
        if (ids.length==0)
        {
            layui.layer.msg('请先选择评论');
            return;
        }
        layui.layer.confirm('是否删除选中的评论?<br>如有下属评论,也将同时删除!', {
            btn: ['删除','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:admin:del",data:{ ids: ids},async:true,beforeSend:function(){
                layui.admin.load('删除中...');
            },done: function(res){
                if (res.error==0)
                {
                    layui.layer.msg('删除成功');
                    del_ids = ids.split(";");
                    for (i=0; i<del_ids.length; i++ ){
                        layui.$('#articles tbody tr[rel='+del_ids[i]+']').remove();
                    }
                }
                if (layui.$('#articles tbody tr').length==0)
                {
                    layui.admin.events.reload();
                }
            }});
        });
    }
    {if P('admin:detail')}
    layui.$('.articledetail').click(function(){
        if(layui.$(window).width()<900){
            width=layui.$(window).width();
        }else{
            width=880;
        }
        if(layui.$(window).height()<700){
            height=layui.$(window).height();
        }else{
            height=680;
        }
        id=layui.$(this).parents('tr').attr('rel');
        layui.layer.open({
          type: 2,
          shade: 0,
          title:'详情',
          area: [width+'px', height+'px'],
          content: '?do={this}:admin:detail&id='+id
        }); 
    });
    {/if}
    {if P('admin:test')}
    layui.$('.commenttest').click(function(){
        if(layui.$(window).width()<900){
            width=layui.$(window).width();
        }else{
            width=880;
        }
        if(layui.$(window).height()<700){
            height=layui.$(window).height();
        }else{
            height=680;
        }
        layui.layer.open({
          type: 2,
          shade: 0,
          title:'评论演示',
          area: [width+'px', height+'px'],
          content: '?do={this}:admin:test'
        }); 
    });
    {/if}
    {if P('admin:code')}
    layui.$('.commentcode').click(function(){
        if(layui.$(window).width()<900){
            width=layui.$(window).width();
        }else{
            width=880;
        }
        if(layui.$(window).height()<700){
            height=layui.$(window).height();
        }else{
            height=680;
        }
        layui.layer.open({
          type: 2,
          shade: 0,
          title:'调用代码',
          area: [width+'px', height+'px'],
          content: '?do={this}:admin:code'
        }); 
    });
    {/if}    
    {if P('admin:del')}
    layui.$('.articledel').click(function(){
        id=layui.$(this).parents('tr').attr('rel');
        layui.layer.confirm('是否删除此评论?<br>如有下属评论,也将同时删除!', {
          btn: ['删除','取消'],skin:'layer-danger',title:'请确认',shadeClose:1}, function(){
            layui.admin.req({type:'post',url:"?do={this}:admin:del",data:{ id: id},async:true,beforeSend:function(){
                layui.admin.load('删除中...');
            },done: function(res){
                if (res.error==0)
                {
                    layui.layer.msg(res.msg);
                    layui.$('tr[rel='+id+']').remove();
                }
            }});
        });
    });
    {/if}
    {if P('admin:status')}
    layui.$('.status_change').click(function(){
        id=layui.$(this).parents('tr').attr('rel');
        status=layui.$(this).attr('rel');
        layui.$(this).parents('td').find('i').remove();
        layui.$(this).append('<i class="layui-icon layui-icon-loading-1 layui-anim layui-anim-rotate layui-anim-loop"></i>');
        layui.admin.req({type:'post',url:"?do={this}:admin:status",data:{ id: id,status:status},async:true,beforeSend:function(){
            layui.admin.load('更改中...');
        },done: function(res){
            layui.$('#articles tr[rel='+id+'] .status_change').find('i').remove();
            if (res.error==0)
            {
                layui.layer.msg(res.msg);
                layui.$('#articles tr[rel='+id+'] .status_change').addClass('layui-btn-primary').removeClass('cms-btn');
                layui.$('#articles tr[rel='+id+'] .status_change[rel='+status+']').removeClass('layui-btn-primary').addClass('cms-btn');
            }
        }});
    });
    {/if}
});
</script>
{admin:body:~()}
</body>
</html>
