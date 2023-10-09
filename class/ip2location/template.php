<?php if(!defined('ClassCms')) {exit();}?>
<!DOCTYPE html>
<html>
<head>{admin:head(IP地址查询)}</head>
<body>
  <div class="layui-fluid">
    <div class="layui-row">

<div class="layui-form">
    <div class="layui-card">
        <div class="layui-card-header">
            <div class="layui-row">
                <div id="cms-breadcrumb">{admin:breadcrumb($breadcrumb)}</div>
                <div id="cms-right-top-button"></div>
            </div>
        </div>
        <div class="layui-card-body">
                  <form method="get" action=""><input type="hidden" name="do" value="ip2location:index">
                  <div class="layui-form-item layui-form-item-width-auto">
                    <label class="layui-form-label">IP</label>
                        <div class="layui-input-right">
                            <div class="layui-input-block">
                              <input type="text" name="ip" value="{$ip}" class="layui-input"  lay-verify="required"> <br>
                              <button class="layui-btn layui-btn-normal cms-btn" lay-submit="" lay-filter="form-submit">查询测试</button>
                            </div>
                            <div class="layui-form-mid">
                            <?php
                                if(isset($info)) {
                                    echo('返回信息:');
                                    print_r($info);
                                }
                            ?>
                            </div>
                        </div>
                  </div>
                  </form>
        </div>
        <div class="layui-card-body">
            <blockquote class="layui-elem-quote layui-text">
                免费IP数据库 (纯真IP库，已经格式为国家、省、市、县、运营商)。<br>
                此工具基于纯真 IP 库，并且把非结构化的数据结构化。<br>
                国内 ip 都能识别出省，基本可以识别出市、运营商，有部分能识别出县，以及公司小区学校网吧等信息。<br>
                数据库文件更新日期:2023年02月01日更新。<br>
                如需更新IP库,请前往http://www.cz88.net/,下载纯真免费IP库安装后,提取程序目录内的qqwry.dat文件,覆盖本应用目录内对应的文件。<br>
                代码来源于:https://github.com/itbdw/ip-database
            </blockquote>
        </div>
        <div class="layui-card-body">
            <blockquote class="layui-elem-quote layui-text">
                在应用中可以通过C('ip2location:get','1.2.3.4')来获取对应的IP信息<br>
                如果ip错误，返回 $result['error'] 信息<br>
                如查询到对应信息,则返回:<br>
                $result['ip']            输入的ip<br>
                $result['country']       国家 如 中国<br>
                $result['province']      省份信息 如 浙江省<br>
                $result['city']          市 如 杭州市<br>
                $result['county']        区县 如 xxx区<br>
                $result['isp']           运营商 如 移动<br>
                $result['area']          最完整的信息 如 浙江省杭州市xxx区xxx网吧<br>
                province city county isp 对中国以外的ip无法识别
            </blockquote>
        </div>
    </div>

     </div>
  </div>
  </div>
{admin:body:~()}
</body>
</html>
