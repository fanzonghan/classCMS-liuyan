<?php
if(!defined('ClassCms')) {exit();}
class email {
    function init(){
        Return array('template_dir' => 'template');
    }
    function table(){
        return array(I()=>array('title'=>'varchar(255)','posttime'=>'bigint(10)','state'=>'int(1)','email'=>'text','args'=>'text'));
    }
    function config() {
        $configs=array();
        $configs[]=array('configname'=>'日志','hash'=>'log','inputhash'=>'switch','defaultvalue'=>1,'tips'=>'开启后,将记录每次发送的信息');
        $configs[]=array('configname'=>'服务器','hash'=>'host','inputhash'=>'text','defaultvalue'=>'','tips'=>'SMTP服务器地址,如:qq邮箱为smtp.qq.com,163为smtp.163.com');
        $configs[]=array('configname'=>'端口','hash'=>'port','inputhash'=>'text','defaultvalue'=>'','tips'=>'SMTP服务器端口,如:25,465');
        $configs[]=array('configname'=>'安全协议','hash'=>'secure','inputhash'=>'radio','defaultvalue'=>'1','values'=>"1:无\n2:SSL\n3:TLS",'savetype'=>1,'tips'=>'根据SMTP服务器配置选择相应选项');
        $configs[]=array('configname'=>'账号','hash'=>'username','inputhash'=>'text','defaultvalue'=>'','tips'=>'发信账号,如qq邮箱账号为123456@qq.com格式');
        $configs[]=array('configname'=>'密码','hash'=>'password','inputhash'=>'text','defaultvalue'=>'','tips'=>'发信邮箱密码或授权码');
        $configs[]=array('configname'=>'队列','hash'=>'task','inputhash'=>'switch','defaultvalue'=>0,'tips'=>'请先安装 <a href="https://classcms.com/class/task/" target="_blank" class="layui-btn layui-btn-xs cms-btn">计划任务[task]</a> 应用,并使用"接口触发"方式部署.<br>通过队列发送邮件,可以加快网站响应时间,但会延迟几秒发邮件的时间.<br>');
        $configs[]=array('configname'=>'发件人名称','hash'=>'fromname','inputhash'=>'text','defaultvalue'=>'ClassCMS','tips'=>'');
        Return $configs;
    }
    function auth() {
        Return array('admin:index'=>'浏览发送记录','admin:detail'=>'查看详情','admin:del;admin:clean'=>'删除记录','admin:test;admin:testSend'=>'测试发送');
    }
    function addLog($config,$state=1){
        if(!config('log')){return false;}
        $log=array();
        $query['table']=I();
        if(isset($config['title'])){
            $query['title']=$config['title'];
        }else{
            $query['title']='';
        }
        $query['posttime']=time();
        if($state){
            $query['state']=1;
        }else{
            $query['state']=0;
        }
        if(is_array($config['to'])){
            $query['email']=implode(';',$config['to']);
        }else{
            $query['email']=$config['to'];
        }
        unset($config['username']);
        unset($config['password']);
        unset($config['task']);
        $query['args']=json_encode($config);
        return insert($query);
    }
    function send($config=array()) {
        if(!is_array($config)){return false;}
        if(!isset($config['task'])){$config['task']=config('task');}
        if($config['task']){
            $config['task']=0;
            return C('task:add',array('title'=>'email','classfunction'=>'this:send','args'=>array($config)));
        }
        if(!class_exists('PHPMailer')){require_once(classDir(I()).'PHPMailer.php');}
        if(!class_exists('SMTP')){require_once(classDir(I()).'SMTP.php');}
        $mail = new PHPMailer();
        if(isset($config['debug'])){
            $mail->SMTPDebug = $config['debug'];
        }
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        if(!isset($config['host'])){$config['host']=config('host');}
        if(empty($config['host'])){return false;}
        $mail->Host = $config['host'];
        if(!isset($config['secure'])){$config['secure']=config('secure');}
        if($config['secure']==2){
            $mail->SMTPSecure = 'ssl';
        }elseif($config['secure']==3){
            $mail->SMTPSecure = 'tls';
        }
        if(!isset($config['port'])){$config['port']=config('port');}
        if(empty($config['port'])){$config['port']=25;}
        $mail->Port = $config['port'];
        if(isset($config['charset']) && $config['charset']){
            $mail->CharSet = $config['charset'];
        }else{
            $mail->CharSet = 'UTF-8';
        }
        if(isset($config['xmailer'])){$mail->XMailer=$config['xmailer'];}else{$mail->XMailer='ClassCMS';}
        if(!isset($config['username'])){$config['username']=config('username');}
        $mail->Username = $config['username'];
        if(!isset($config['password'])){$config['password']=config('password');}
        $mail->Password = $config['password'];
        if(!isset($config['fromname'])){$config['fromname']=config('fromname');}
        $mail->FromName = $config['fromname'];
        if(isset($config['from'])){
            $mail->From = $config['from'];
        }else{
            $mail->From = $config['username'];
        }
        if(!isset($config['ishtml'])){
            $mail->isHTML(true);
        }elseif(isset($config['ishtml']) && $config['ishtml']){
            $mail->isHTML(true);
        }
        if(isset($config['to']) && !empty($config['to'])){
            if(is_array($config['to'])){
                foreach ($config['to'] as $to) {
                    $mail->addAddress($to);
                }
            }else{
                $mail->addAddress($config['to']);
            }
        }else{
            return false;
        }
        if(isset($config['title'])){
            $mail->Subject = $config['title'];
        }else{
            return false;
        }
        if(isset($config['content'])){
            $mail->Body = $config['content'];
        }
        if(isset($config['file'])){
            if(is_array($config['file'])){
                foreach ($config['file'] as $file) {
                    $mail->addAttachment($file);
                }
            }else{
                $mail->addAttachment($config['file']);
            }
        }
        $state=$mail->send();
        $config['error']=$mail->ErrorInfo;
        C('this:addLog',$config,$state);
        return $state;
    }
}
