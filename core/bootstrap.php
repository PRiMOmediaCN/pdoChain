<?php
/*
 * 定义核心常量
 */
define('COMMON_PATH', ROOT_PATH.'/common/'); //定义公共函数目录
define('CONFIG_PATH', ROOT_PATH.'/config/'); //定义配置文件目录
define('LIB_PATH', ROOT_PATH.'/library/');   //定义类库文件目录
define('HELPER_PATH', ROOT_PATH.'/helper/'); //定义助手函数目录
define('VENDOR_PATH', ROOT_PATH.'/vendor/'); //定义第三方组件目录
define('LOG_PATH', ROOT_PATH.'/log/');       //定义日志目录

//引入配置文件
include(CONFIG_PATH.'/'.APP_ENV.'.php');

//引入公共函数文件
include(COMMON_PATH.'/function.php');
include(COMMON_PATH.'/tool.php');
include(COMMON_PATH.'/validate.php');

//注册自动加载函数
spl_autoload_register('load_lib');
spl_autoload_register('load_helper');

//判断是否允许访问
if(!allow_visit()){
    header('HTTP/1.1 403 Forbidden');
    exit('Access forbidden');
}

//调试模式判断
if(!I(DEBUG_PARAM, 0)){
    //关闭debug模式
    define('APP_DEBUG', false);
}else{
    error_reporting(0);
    define('APP_DEBUG', true);
}

//自定义异常处理函数
register_shutdown_function('catch_exception');

