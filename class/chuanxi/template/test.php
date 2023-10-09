<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(测试)}</head>
<body>
  <div class="layui-fluid">
    <div class="layui-row">

<div class="layui-form">
    <div class="layui-card">
        <div class="layui-card-header">
            <div class="layui-row">
                <?php
                    $breadcrumb=array(
                        array('url'=>'?do=admin:class:config&hash='.I(),'title'=>'传息'),
                        array('url'=>'?do='.I().':admin:index','title'=>'发送记录'),
                        array('title'=>'测试')
                    );
                ?>
                <div id="cms-breadcrumb">{admin:breadcrumb($breadcrumb)}</div>
            </div>
        </div>
        <div class="layui-card-body">
                {if $tips}
                    <blockquote class="layui-elem-quote layui-text">
                        {$tips}
                    </blockquote>
                {/if}
                  <div class="layui-form-item layui-form-item-width-auto">
                    <label class="layui-form-label">appKey</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                              <input type="text" name="appkey" value="{config(appkey)}" class="layui-input" lay-verify="required">
                            </div>
                            <div class="layui-form-mid"></div>
                        </div>
                  </div>

                  <div class="layui-form-item layui-form-item-width-auto">
                    <label class="layui-form-label">标题</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                              <input type="text" name="title" value="测试标题{rand(10000,99999)}" class="layui-input" lay-verify="required">
                            </div>
                            <div class="layui-form-mid"></div>
                        </div>
                  </div>

                  <div class="layui-form-item layui-form-item-width-auto">
                    <label class="layui-form-label">内容</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                              <input type="text" name="content" value="测试内容{rand(10000,99999)}" class="layui-input" lay-verify="required">
                            </div>
                            <div class="layui-form-mid"></div>
                        </div>
                  </div>


                  <div class="layui-form-item layui-form-item-width-auto">
                    <label class="layui-form-label">队列发送</label>
                    <div class="layui-input-right">
                    <div class="layui-input-block">
                        <input type="checkbox" name="task" lay-skin="switch"  lay-text="|"></div>
                    <div class="layui-form-mid">通过队列发送,可以加快响应时间,但无法判断是否成功发送.<br>请先安装 <a href="https://classcms.com/class/task/" target="_blank" class="layui-btn layui-btn-xs cms-btn">计划任务[task]</a> 应用,并使用"接口触发"方式部署.</div>
                    </div>
                  </div>

        </div>

    </div>

    <div class="layui-form-item layui-layout-admin">
        <div class="layui-input-block">
            <div class="layui-footer">
            <button class="layui-btn cms-btn" lay-submit="" lay-filter="form-submit">发送</button>
            <button type="button" class="layui-btn layui-btn-primary" layadmin-event="back">返回</button>
            </div>
        </div>
    </div>

     </div>
  </div>
  </div>
  
<script>layui.use(['index'],function(){
    layui.form.on('submit(form-submit)', function(data){
        layui.$('button[lay-filter=form-submit]').blur();
        layui.admin.req({type:'post',url:"?do={this}:admin:testSend",data:data.field,async:true,beforeSend:function(){
            layui.admin.load('提交中...');
        },done: function(res){
            if (res.error==0)
            {
                var confirm=layer.confirm(res.msg, {btn: ['好的','返回'],shadeClose:1},function(){layui.layer.close(confirm);},function(){
                    layui.admin.events.back();
                    });
            }
        }});
      return false;
    });
});
</script>
{admin:body:~()}
</body>
</html>
