<?php
/**
 * 核心函数库
 * Auth:gouguoyin
 * Blog:www.gouguoyin.cn
 */
/*
 * 实例化模型
 */
if(!function_exists('M')){
    function M($tableName = '',$tablePrefix = ''){
        return new Model($tableName, $tablePrefix);
    }
}

/*
 * 接收参数
 */
if(!function_exists('I')){
    function I($name,$default = '') {

        return Request::getParam($name, $default);

    }
}


/*
 * 获取配置信息
 */
if(!function_exists('C')){
    function C($key = '', $default = ''){

        return Config::get($key, $default);

    }
}

/*
 * 获取单条数据
 */
if(!function_exists('_uri')){
    function _uri($table_name, $where, $field = null, $show = false){
        if(!$table_name || !$where){
            return '';
        }

        if (is_int(strstr) && !strstr($where, '=')) {
            $where = 'id = '.$where;
        }

        $result = M($table_name)->where($where)->show($show)->find($field);

        if($field && !strstr($field, ',')){
            return $result[$field];
        }

        return $result;

    }
}

/*
 * 获取多条数据
 */
if(!function_exists('_list')){
    function _list($table_name, $where, $num = 10, $order_by = 'id desc', $field = null, $show = false){

        if(!$table_name || !$where || !$num || !$order_by){
            return array();
        }

        $result = M($table_name)->where($where)->limit($num)->order($order_by)->show($show)->findAll($field);

        return $result;

    }
}

/*
 * cookie，过期时间单位为小时
 */
if(!function_exists('cookie')){
    function cookie($name, $value = '', $expire = 24){
        if(is_null($value)){
            //删除cookie
            Cookie::del($name);
        }elseif(!$value){
            return Cookie::get($name);
        }else{
            Cookie::set($name, $value, $expire*3600);
        }
    }
}

/*
 * session
 */
if(!function_exists('session')){
    function session($name, $value = ''){
        if(is_null($value)){
            //删除cookie
            Session::del($name);
        }elseif(!$value){
            return Session::get($name);
        }else{
            Session::set($name, $value);
        }
    }
}

/*
 * 加载核心类库文件
 */
if(!function_exists('load_lib')){
    function load_lib($lib_name){
        $lib_file = '';
        if($lib_name){
            $lib_file = LIB_PATH.'/'.$lib_name.'.php';
        }

        if(!is_file($lib_file)){
            return ;
        }

        include $lib_file;
    }
}

/*
 * 加载助手函数
 */
if(!function_exists('load_helper')){
    function load_helper($helper_name)
    {
        $helper_dir = HELPER_PATH;

        if ($helper_name) {
            $helper_file = $helper_dir.$helper_name.'.php';

        }

        if(!is_file($helper_file)){
            return ;
        }

        include $helper_file;

    }
}

/*
 * 捕获异常
 */
if(!function_exists('catch_exception')){
    function catch_exception() {
        ob_start();
        $msg = '';


        $e = error_get_last();

        $ignore_type = array(
            '2',//E_WARNING,报告运行的非致命错误
            '8',//E_NOTICE,报告通告，表示所做的事情可能是错误的.
            '32',//E_CORE_WARNING,报告PHP引擎启动时非致命错误
            '128',//E_COMPILE_WARNING,报告编译时出现的非致命错误
            '1024',//E_USER_NOTICE,报告用户触发的通告
            '2048',//E_STRICT,报告不赞成的用法和不推荐的行为
            '8192',//报告不赞成的用法和不推荐的行为
        );

        if(is_null($e) || in_array($e['type'], $ignore_type)){
            return ;
        }

        $msg= '<br>';
        $msg.= 'Error TYPE:'.$e['type'];
        $msg.= '<br>';
        $msg.= 'Error INFO:'.$e['message'];
        $msg.= '<br>';
        $msg.= 'Error FILE:'.$e['file'];
        $msg.= '<br>';
        $msg.= 'Error LINE:'.$e['line'];

        if(C('EMAIL_NOTICE')){
            send_email(C('SYTEM_EMAIL'), '系统异常警告', $msg);
        }

        if(C('WEIXIN_NOTICE')){
            weixin_helper::send_template('系统异常警告', $e['message'], '异常文件：'.$e['file']);
        }


        if(!APP_DEBUG){
            return ;
        }

        echo $msg;

    }
}

/*
 * 字符串过滤
 */
if(!function_exists('string_filter')){
    function string_filter(&$value){
        $value = trim($value);
        $value = htmlspecialchars($value);

        // 过滤查询特殊字符
        if(preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i',$value)){
            $value .= ' ';
        }

        return $value;
    }
}

/*
 * 判断ip是否允许访问
 */
if (!function_exists('allow_visit')) {
    function allow_visit($ip = null){
        if(!$ip){
            //获取当前ip
            $ip = get_client_ip();
        }

        //获取IP黑名单配置信息
        $forbid_ips   = C('FORBID_IP');

        //如果未设置黑名单，则允许所有访问
        if(!count($forbid_ips)){
            return true;
        }

        $check_ip_arr = explode('.', $ip);
        if(!in_array($ip, $forbid_ips)) {
            foreach ($forbid_ips as $forbid_ip){
                if(strpos($forbid_ip, '*') !== false){
                    $arr = array();
                    $arr = explode('.', $forbid_ip);
                    for($i=0; $i<4; $i++){
                        if($arr[$i] != '*'){
                            if($arr[$i] != $check_ip_arr[$i]){
                                return true;
                            }
                        }
                    }

                    return false;

                }
            }
        }else{
            return false;
        }
    }
}

if(!function_exists('vp')){
    function vp($d, $t='v', $e=true){
        echo '<pre>';
        if($t=='v'){
            var_dump($d);
        }elseif($t=='p'){
            print_r($d);
        }else{
            echo $d;
        }
        echo '<pre>';
        if($e)exit();
    }
}













