<?php
/**
 * 验证类 Validate.class.php
 * Author: 勾国印 (phper@gouguoyin.cn)
 * Date: 2015-5-19 上午10:01:51
*/

class Validate
{
    //验证是否在手机端访问
    public static function onMobile()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_agents = array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
        $is_mobile = false;
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                $is_mobile = true;
                break;
            }
        }
        return $is_mobile;
    }

    //验证是否在微信端访问
    public static function onWeixin()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if(stripos($user_agent, 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    //验证是否是IE浏览器访问
    public static function onIE()
    {
        $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
        if(strpos($useragent, 'msie ') !== false) return true;
        return false;
    }

    //验证是否是合法邮箱
    public static function isEmail($string)
    {
        return preg_match('/^[_.0-9a-z-a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,4}$/', $string);
    }

    //验证是否是合法网址
    public static function isUrl($url)
    {
        return preg_match('/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/', trim($url));
    }

    //验证是否是合法固话
    public static function isTel($tel)
    {
        return preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/', trim($tel));
    }

    //验证是否是合法手机号
    public static function isPhone($phone)
    {
        return preg_match('/^1[34578]{1}\d{9}$/', trim($phone));
    }

    //验证是否是合法ip
    public static function isIP($ip)
    {
        return preg_match('/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/', trim($ip));
    }

    //验证是否是合法数字
    public static function isNumber($number)
    {
        return preg_match('/^\d{0,}$/', trim($number));
    }

    //验证是否是合法身份证
    public static function isCard($card)
    {
        return preg_match("/^(\d{15}|\d{17}[\dx])$/i", trim($card));
    }

    //验证是否是合法邮编
    public static function isZip($zip)
    {
        return preg_match('/^[1-9]\d{5}$/', trim($zip));
    }

    //验证是否是合法QQ号
    public static function isQQ($qq)
    {
        return preg_match('/^[1-9]\d{4,12}$/', trim($qq));
    }

    //验证是否是纯英文字母
    public static function isEnglish($englisg)
    {
        return preg_match('/^[A-Za-z]+$/', trim($englisg));
    }

    //验证是否是纯中文
    public static function isChinese($chinese)
    {
        return preg_match("/^([\xE4-\xE9][\x80-\xBF][\x80-\xBF])+$/", trim($chinese));
    }

    //验证是否是合法的标识符(以字母或下划线开始，后面跟着任何字母，数字或下划线)
    public static function isIdentifier($value)
    {
        return preg_match('/^[a-zA-Z_0-9][a-zA-Z0-9_]+$/', trim($value));
    }

    //验证是否为合法姓名 数字字母或下划线
    public static function isName($name)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', trim($name));
    }

    //验证是否是合法日期
    public static function isDate($date, $formats = array("Y-m-d", "Y/m/d"))
    {

        $unixTime = strtotime($date);
        if (!$unixTime) {
            return false;
        }

        foreach ($formats as $format)
        {
            if (date($format, $unixTime) == $date) {
                return true;
            }
        }

        return false;

    }


}
