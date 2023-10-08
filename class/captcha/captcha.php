<?php
if(!defined('ClassCms')) {exit();}
class captcha {
    function install() {
        if(!function_exists("gd_info")){Return '未开启php gd2扩展';}
    }
    function route(){
        $routes=array();
        $routes[]=array('hash'=>'image','uri'=>'/captcha_image/','function'=>'image','enabled'=>1);
        Return $routes;
    }
    function auth() {
        Return array('admin'=>'预览');
    }
    function config() {
        $configs=array();
        $configs[]=array('configname'=>'随机字符串','hash'=>'randstrs','inputhash'=>'text','tips'=>'只支持英文字母与数字','tabname'=>'','defaultvalue'=>'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789','nonull'=>1);
        $configs[]=array('configname'=>'大小写','hash'=>'capital','inputhash'=>'switch','tips'=>'是否区分大小写','tabname'=>'','defaultvalue'=>0);
        $configs[]=array('configname'=>'难度','hash'=>'difficulty','inputhash'=>'slider','tips'=>'验证码图片识别的难度','tabname'=>'','defaultvalue'=>0,'min'=>0,'max'=>5,'showstep'=>1);
        $configs[]=array('configname'=>'字符数','hash'=>'length','inputhash'=>'number','tips'=>'验证码图片内的字符数量','tabname'=>'','defaultvalue'=>4,'min'=>1);
        $configs[]=array('configname'=>'宽度','hash'=>'width','inputhash'=>'number','tips'=>'验证码图片高度','tabname'=>'','defaultvalue'=>100,'min'=>20);
        $configs[]=array('configname'=>'高度','hash'=>'height','inputhash'=>'number','tips'=>'验证码图片宽度','tabname'=>'','defaultvalue'=>30,'min'=>20);
        Return $configs;
    }
    function admin() {
        $array['breadcrumb']=array(array('url'=>'?do=admin:class:config&hash=captcha','title'=>'验证码'),array('title'=>'预览'));
        V('template',$array);
    }
    function html($input=1) {
        $html='';
        if($input) {
            $html.='<input type="text" id="captcha" name="captcha" value="" class="layui-input">';
        }
        $html.='<img src="'.route('image').'" id="captcha_image">';
        Return $html;
    }
    function check($value='') {
        if(empty($value) && isset($_POST['captcha'])) {
            $value=trim($_POST['captcha']);
        }
        $captcha=C('cms:common:session','captcha');
        if(!config('capital')) {
            $captcha=strtolower($captcha);
            $value=strtolower($value);
        }
        if(!empty($value) && $value==$captcha) {
            Return true;
        }else {
            Return false;
        }
    }
    function del() {
        Return C('cms:common:session','captcha','');
    }
    function image() {
        $sessionstr='';
        if(function_exists("gd_info")){
            $randstrs=config('randstrs');
            $width=intval(config('width'));
            $height=intval(config('height'));
            $length=intval(config('length'));
            $difficulty=intval(config('difficulty'));
            $image   = imagecreatetruecolor($width, $height);
            imagefilledrectangle($image, 0, 0, ($width -2), ($height -2), imagecolorallocate($image, 255, 255, 255));
            imagerectangle($image, 0, 0, $width, $height, imagecolorallocate($image, 0, 0, 0));
            for ($i = 0; $i < $difficulty*2*$length; $i++) {
                imagesetpixel($image, rand(1, $width-2), rand(1, $height-2), imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255)));
            }

            $strwidth=intval(floor($width/$length));
            if($width<60 || $height<30) {
                $fontsize=14;
            }elseif($width<100 || $height<50) {
                $fontsize=20;
            }elseif($width<150 || $height<80) {
                $fontsize=22;
            }else {
                $fontsize=14;
            }
            for ($i = 0; $i < $length; $i++) {
                $randstr=C('cms:common:randStr',1,$randstrs);
                $sessionstr.=$randstr;
                $randwidth=rand(-intval($strwidth/(8-$difficulty)),intval($strwidth/(8-$difficulty)));
                if($i==0) {$randwidth=rand(0,intval($strwidth/5));}
                $x=floor($width/$length)*$i+$randwidth;
                $y=$height-$fontsize/3;
                if($y<=$fontsize){
                    $y=$fontsize;
                }
                $angel=rand(-$difficulty*5,$difficulty*5);
                $color = imagecolorallocate($image, rand(50, 255), rand(50, 120), rand(50, 255));
                imagettftext($image, $fontsize,$angel,intval($x),intval($y),$color,classDir('this')."font.otf", $randstr);
            }
            for($i=0; $i<$difficulty; ++$i){
                $x = rand(5, $width-5);
                $y = rand(2, floor($height/4));
                $x1 = rand($x-5*$length, $x+5*$length);
                if($x1<2) {$x1=2;}elseif($x1>($width-4)) {$x1=$width-4;}
                $y1 = rand(floor($height/4*3), $height-5);
                if($y1<2) {$y1=2;}elseif($y1>($height-3)) {$y1=$height-3;}
                $color=imagecolorallocate($image, 255, 255, 255);
                imageline($image, $x, $y, $x1, $y1,$color);
                if($difficulty==4) {
                    imageline($image, $x+1, $y+1, $x1+1, $y1+1,$color);
                }elseif($difficulty==5) {
                    imageline($image, $x+1, $y+1, $x1+1, $y1+1,$color);
                    imageline($image, $x+2, $y+2, $x1+2, $y1+2,$color);
                }
            }
            header("Pragma:no-cache");
            header("Cache-control:no-cache");
            header("Content-type: image/png");
            imagepng($image);
            imagedestroy($image);
            C('cms:common:session','captcha',$sessionstr);
            Return true;
        }
        Return false;
    }
}