<?php
if(!defined('ClassCms')) {exit();}
class user {
    function init(){ Return array( 'template_dir' => 'template' ); }
    function install() {
        $infos=C('this:infos');
        if(is_array($infos)){
            foreach ($infos as $info) {
                C('cms:form:add',$info);
            }
        }
    }
    function infos(){
        return array(
            array('kind'=>'info','formname'=>'邮箱','hash'=>'email','inputhash'=>'text','config'=>array('regular'=>'email')),
            array('kind'=>'info','formname'=>'手机','hash'=>'phone','inputhash'=>'text','config'=>array('regular'=>'phone')),
            array('kind'=>'info','formname'=>'积分','hash'=>'points','inputhash'=>'number','defaultvalue'=>0),
            array('kind'=>'info','formname'=>'余额','hash'=>'money','inputhash'=>'number','defaultvalue'=>0,'config'=>array('savetype'=>2)),
            array('kind'=>'info','formname'=>'头像','hash'=>'avatar','inputhash'=>'imgupload'),
            array('kind'=>'info','formname'=>'生日','hash'=>'birthday','inputhash'=>'datetime'),
            array('kind'=>'info','formname'=>'注册时间','hash'=>'regtime','inputhash'=>'datetime','config'=>array('time'=>1)),
            array('kind'=>'info','formname'=>'注册ip','hash'=>'regip','inputhash'=>'text'),
            array('kind'=>'info','formname'=>'个人介绍','hash'=>'profile','inputhash'=>'textarea'),
            array('kind'=>'info','formname'=>'个人主页','hash'=>'homepage','inputhash'=>'text'),
            array('kind'=>'info','formname'=>'QQ','hash'=>'qq','inputhash'=>'text'),
        );
    }
    function config() {
        $infos=C('cms:form:all','info');
        $infos=C('cms:form:getColumnCreated',$infos,'user');
        $infosvalue='';
        foreach($infos as $info) {
            if($infosvalue){$infosvalue.="\n";}
            $infosvalue.=$info['hash'].':'.$info['formname'];
        }
        $configs=array();
        $configs[]=array('tabname'=>'注册','configname'=>'开启注册','hash'=>'reg','inputhash'=>'switch','defaultvalue'=>0,'tips'=>'开启后可以注册账号');
        $configs[]=array('tabname'=>'注册','configname'=>'提示','hash'=>'regtips','inputhash'=>'textarea','defaultvalue'=>'请填写您的注册信息','tips'=>'注册页提示文字');
        $configs[]=array('tabname'=>'注册','configname'=>'验证码','hash'=>'regcaptcha','inputhash'=>'switch','defaultvalue'=>0,'tips'=>'注册时需要填写验证码,,请先安装 <a class="cmscolor" target="_blank" href="https://classcms.com/class/captcha/">验证码</a> 插件');
        $configs[]=array('tabname'=>'注册','configname'=>'选项','hash'=>'reginfos','inputhash'=>'checkbox','defaultvalue'=>'','values'=>$infosvalue,'savetype'=>1,'tips'=>'注册时所需要填写的用户属性,如需设置必填,请在后台->用户管理->属性管理内设置<br>需要设置唯一属性的(如邮箱手机号等),请安装 <a class="cmscolor" target="_blank" href="https://classcms.com/class/userinfoonly/">用户属性唯一</a> 插件');
        $configs[]=array('tabname'=>'注册','configname'=>'审核','hash'=>'regcheck','inputhash'=>'switch','defaultvalue'=>1,'tips'=>'开启后注册需要审核通过才能登入');
        $configs[]=array('tabname'=>'注册','configname'=>'默认角色','hash'=>'regrole','inputhash'=>'rolecheckbox','defaultvalue'=>'','tips'=>'注册后,用户默认的角色,<a class="cmscolor" href="?do=admin:user:roleIndex">角色管理</a>');


        $configs[]=array('tabname'=>'登入','configname'=>'登入标题','hash'=>'logintitle','inputhash'=>'text','defaultvalue'=>'','tips'=>'登入页标题文字');
        $configs[]=array('tabname'=>'登入','configname'=>'登入提示','hash'=>'logintips','inputhash'=>'textarea','defaultvalue'=>'','tips'=>'登入页提示文字');
        $configs[]=array('tabname'=>'登入','configname'=>'验证码','hash'=>'logincaptcha','inputhash'=>'switch','defaultvalue'=>0,'tips'=>'登入时需要填写验证码,,请先安装 <a class="cmscolor" target="_blank" href="https://classcms.com/class/captcha/">验证码</a> 插件');

        $configs[]=array('tabname'=>'找回','configname'=>'找回密码','hash'=>'forgot','inputhash'=>'switch','defaultvalue'=>0,'tips'=>'开启后,可以通过邮箱找回密码,请先安装邮件发送应用,否则无法发送邮件');
        $configs[]=array('tabname'=>'找回','configname'=>'找回提示','hash'=>'forgottips','inputhash'=>'textarea','defaultvalue'=>'','tips'=>'找回密码页提示文字');
        $configs[]=array('tabname'=>'找回','configname'=>'验证码','hash'=>'forgotcaptcha','inputhash'=>'switch','defaultvalue'=>0,'tips'=>'找回密码时需要填写验证码,请先安装 <a class="cmscolor" target="_blank" href="https://classcms.com/class/captcha/">验证码</a> 插件');

        $configs[]=array('tabname'=>'协议','configname'=>'注册页显示','hash'=>'regagreement','inputhash'=>'switch','defaultvalue'=>0,'tips'=>'注册页显示用户协议');
        $configs[]=array('tabname'=>'协议','configname'=>'登入页显示','hash'=>'loginregagreement','inputhash'=>'switch','defaultvalue'=>0,'tips'=>'登入页显示用户协议');
        $configs[]=array('tabname'=>'协议','configname'=>'默认勾选','hash'=>'regagreementchecked','inputhash'=>'switch','defaultvalue'=>1,'tips'=>'默认勾选');
        $configs[]=array('tabname'=>'协议','configname'=>'用户协议','hash'=>'agreementcontent','inputhash'=>'textarea','defaultvalue'=>"<b>一、服务条款</b>\n 本网站提供的服务将完全按照其发布的使用协议、服务条款和操作规则严格执行。为获得本网站服务，服务使用人（以下称“用户”）应当同意本协议的全部条款并按照页面上的提示完成全部的注册程序。\n\n <b>二、目的</b>\n 本协议是以规定用户使用本网站提供的服务时，本网站间的权利、义务、服务条款等基本事宜为目的。\n\n <b>三、遵守法律及法律效力</b>\n 本服务使用协议在向用户公告后，开始提供服务或以其他方式向用户提供服务同时产生法律效力。\n 用户同意遵守《中华人民共和国保密法》、《计算机信息系统国际联网保密管理规定》、《中华人民共和国计算机信息系统安全保护条例》、《计算机信息网络国际联网安全保护管理办法》、《中华人民共和国计算机信息网络国际联网管理暂行规定》及其实施办法等相关法律法规的任何及所有的规定，并对会员以任何方式使用服务的任何行为及其结果承担全部责任。\n 在任何情况下，如果本网站合理地认为用户的任何行为，包括但不限于用户的任何言论和其他行为违反或可能违反上述法律和法规的任何规定，本网站可在任何时候不经任何事先通知终止向会员提供服务。\n 本网站可能不时的修改本协议的有关条款，一旦条款内容发生变动，本网站将会在相关的页面提示修改内容。在更改此使用服务协议时，本网站将说明更改内容的执行日期，变更理由等。且应同现行的使用服务协议一起，在更改内容发生效力前7日内及发生效力前日向用户公告。\n 用户需仔细阅读使用服务协议更改内容，用户由于不知变更内容所带来的伤害，本网站一概不予负责。\n 如果不同意本网站对服务条款所做的修改，用户有权停止使用网络服务。如果用户继续使用网络服务，则视为用户接受服务条款的变动。\n\n <b>四、服务内容</b>\n 本网站服务的具体内容由本网站根据实际情况提供，本网站保留随时变更、中断或终止部分或全部服务的权利。\n\n <b>五、会员的义务</b>\n 用户在申请使用本网站服务时，必须向本网站提供准确的个人资料，如个人资料有任何变动，必须及时更新。\n 用户注册成功后，本网站将给予每个用户一个用户帐号及相应的密码，该用户帐号和密码由用户负责保管；用户应当对以其用户帐号进行的所有活动和事件负法律责任。\n 用户在使用本网站网络服务过程中，必须遵循以下原则：\n 遵守中国有关的法律和法规；\n 不得为任何非法目的而使用网络服务系统；\n 遵守所有与网络服务有关的网络协议、规定和程序；\n 不得利用本网站服务系统传输任何危害社会，侵蚀道德风尚，宣传不法宗教组织等内容；\n 不得利用本网站服务系统进行任何可能对互联网的正常运转造成不利影响的行为；\n 不得利用本网站服务系统上载、张贴或传送任何非法、有害、胁迫、滥用、骚扰、侵害、中伤、粗俗、猥亵、诽谤、侵害他人隐私、辱骂性的、恐吓性的、庸俗淫秽的及有害或种族歧视的或道德上令人不快的包括其他任何非法的信息资料；\n 不得利用本网站服务系统进行任何不利于本网站的行为；\n 如发现任何非法使用用户帐号或帐号出现安全漏洞的情况，应立即通告本网站。\n\n <b>六、本网站的权利及义务</b>\n 本网站除特殊情况外（例如：协助公安等相关部门调查破案等），致力于努力保护会员的个人资料不被外漏，且不得在未经本人的同意下向第三者提供会员的个人资料。\n 本网站根据提供服务的过程，经营上的变化，无需向会员得到同意即可更改，变更所提供服务的内容。\n 本网站在提供服务过程中，应及时解决会员提出的不满事宜，如在解决过程中确有难处，可以采取公开通知方式或向会员发送电子邮件寻求解决办法。\n 本网站在下列情况下可以不通过向会员通知，直接删除其上载的内容：\n 有损于本网站，会员或第三者名誉的内容；\n 利用本网站服务系统上载、张贴或传送任何非法、有害、胁迫、滥用、骚扰、侵害、中伤、粗俗、猥亵、诽谤、侵害他人隐私、辱骂性的、恐吓性的、庸俗淫秽的及有害或种族歧视的或道德上令人不快的包括其他任何非法的内容；\n 侵害本网站或第三者的版权，著作权等内容；\n 存在与本网站提供的服务无关的内容；\n 无故盗用他人的ID(固有用户名)，姓名上载、张贴或传送任何内容及恶意更改，伪造他人上载内容。\n\n <b>七、服务变更、中断或终止</b>\n 如因系统维护或升级的需要而需暂停服务，本网站将尽可能事先进行通告。\n 如发生下列任何一种情形，本网站有权随时中断或终止向用户提供本协议项下的服务而无需通知用户：\n 用户提供的个人资料不真实；\n 用户违反本协议中规定的使用规则。\n 除前款所述情形外，本网站同时保留在不事先通知用户的情况下随时中断或终止部分或全部服务的权利，对于所有服务的中断或终止而造成的任何损失，本网站无需对用户或任何第三方承担任何责任。\n\n <b>八、违约赔偿</b>\n 如因本网站违反有关法律、法规或本协议项下的任何条款而给用户造成损失，本网站同意承担由此造成的损害赔偿责任。\n 用户同意保障和维护本网站及其他用户的利益，如因用户违反有关法律、法规或本协议项下的任何条款而给本网站或任何其他第三人造成损失，用户同意承担由此造成的损害赔偿责任。\n\n <b>九、协议修改</b>\n 本网站有权随时修改本协议的任何条款，一旦本协议的内容发生变动，本完整将会通过适当方式向用户提示修改内容。\n 如果不同意本网站对本协议相关条款所做的修改，用户有权停止使用网络服务。如果用户继续使用网络服务，则视为用户接受本网站对本协议相关条款所做的修改。\n\n <b>十、通知送达</b>\n 本协议项下本网站对于用户所有的通知均可通过网页公告、电子邮件或常规的信件传送等方式进行；该等通知于发送之日视为已送达收件人。\n 用户对于本网站的通知应当通过本网站对外正式公布的联系信息进行送达。\n\n <b>十一、法律管辖</b>\n 本协议的订立、执行和解释及争议的解决均应适用中国法律并受中国法院管辖。\n 如双方就本协议内容或其执行发生任何争议，双方应尽量友好协商解决；协商不成时，任何一方均可向本公司所在地的人民法院提起诉讼。\n\n <b>十二、其他规定</b>\n 本协议构成双方对本协议之约定事项及其他有关事宜的完整协议，除本协议规定的之外，未赋予本协议各方其他权利。\n 如本协议中的任何条款无论因何种原因完全或部分无效或不具有执行力，本协议的其余条款仍应有效并且有约束力。\n 本协议中的标题仅为方便而设，在解释本协议时应被忽略。\n\n",'tips'=>'用户协议内容','style'=>'height:500px');


        Return $configs;
    }
    function hook() {
        $hooks=array();
        $hooks[]=array('hookname'=>'nologinActionCheck','hookedfunction'=>'admin:nologinActionCheck','enabled'=>1);
        $hooks[]=array('hookname'=>'loginButtons','hookedfunction'=>'admin:loginIco','enabled'=>1);

        $hooks[]=array('hookname'=>'loginTitle','hookedfunction'=>'admin:loginTitle','enabled'=>1,'requires'=>'config.logintitle');
        $hooks[]=array('hookname'=>'loginTips','hookedfunction'=>'admin:loginBody','enabled'=>1,'requires'=>'config.logintips');

        $hooks[]=array('hookname'=>'agreement:regShow','hookedfunction'=>'user:regBody','enabled'=>1,'requires'=>'config.regagreement');
        $hooks[]=array('hookname'=>'agreement:loginShow','hookedfunction'=>'admin:loginBody','enabled'=>1,'requires'=>'config.loginregagreement');
        $hooks[]=array('hookname'=>'agreement:loginPost','hookedfunction'=>'admin:login','enabled'=>1,'requires'=>'config.loginregagreement');

        $hooks[]=array('hookname'=>'captcha:regShow','hookedfunction'=>'user:regFormitem3','enabled'=>1,'requires'=>'config.regcaptcha');
        $hooks[]=array('hookname'=>'captcha:forgotShow','hookedfunction'=>'user:forgotFormitem','enabled'=>1,'requires'=>'config.forgotcaptcha');
        $hooks[]=array('hookname'=>'captcha:loginShow','hookedfunction'=>'admin:loginFormitem','enabled'=>1,'requires'=>'config.logincaptcha');
        $hooks[]=array('hookname'=>'captcha:loginCheck','hookedfunction'=>'admin:login','enabled'=>1,'requires'=>'POST.userhash;config.logincaptcha');
        Return $hooks;
    }
    function loginButtons(){
        if(config('forgot')){
            echo('<a href="'.C('this:forgotLink').'" class="layadmin-user-jump-change layadmin-link">找回密码</a>');
        }
        if(config('reg')){
            echo('<a href="'.C('this:regLink').'" class="layadmin-user-jump-change layadmin-link">注册帐号</a>');
        }
    }
    function loginTitle(){
        return config('logintitle');
    }
    function loginTips(){
        echo('<div id="userlogintips" style="display:none"><div style="padding:5px;color: #999;font-weight: 300;">'.nl2br(config('logintips')).'</div></div> <script> layui.use(["jquery"],function(){ layui.$("#LAY-user-login h2.cmscolor").after("<p>"+layui.$("#userlogintips").html()+"</p>"); }); </script>');
    }
    function nologinActionCheck($do) {
        $actions=array();
        if(config('reg')){
            $actions[]='user:reg:index';
            $actions[]='user:reg:post';
            $actions[]='user:reg:formAjax';
        }
        if(config('forgot')){
            $actions[]='user:forgot:index';
            $actions[]='user:forgot:post';
        }
        $dos=explode(':',$do);
        foreach($actions as $action) {
            $action=str_replace("*",end($dos),$action);
            if($do==$action) {
                Return true;
            }
        }
    }
    function id(){
        return C('admin:nowUser');
    }
    function get($where=false){
        if(is_array($where)){
            if(!$user=one(array('table'=>'user','column'=>'id','where'=>$where))) {
                Return false;
            }
            $where=$user['id'];
        }
        if($where==false){
            $where=C('this:id');
        }
        if(!$where){
            return false;
        }
        return C('cms:user:get',$where);
    }
    function adminLink(){
        if(isset($GLOBALS['C']['AdminDir'])){
            return rewriteUri($GLOBALS['C']['AdminDir']);
        }
        return false;
    }
    function regLink(){
        if(config('reg')){
            return C('this:adminLink').'?do=user:reg:index';
        }
        return false;
    }
    function loginLink(){
        return C('this:adminLink').'?do=admin:login';
    }
    function logoutLink(){
        return C('this:adminLink').'?do=admin:logout&csrf='.C('admin:csrfValue');
    }
    function forgotLink(){
        if(config('forgot')){
            return C('this:adminLink').'?do=user:forgot:index';
        }
        return false;
    }
}