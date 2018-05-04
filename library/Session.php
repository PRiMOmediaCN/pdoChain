<?php
/**
 * session操作封装
 * Auth:勾国印
 * Blog:www.gouguoyin.cn
 */
class Session
{
    /*
     * 构造函数
     */
    public function __construct() {
        session_start();
    }

    /*
     * 设置session
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    /*
     * 获取session
     */
    public static function get($key)
    {
        if(isset($_SESSION[$key]))
            return $_SESSION[$key];
        else
            return false;
    }
    /*
     * 删除指定session
     */
    public static function del($key)
    {
        unset($_SESSION[$key]);
    }
    /*
     * 销毁所有session
     */
    public static function destroy()
    {
        $_SESSION = array();
        session_destroy();
    }

}
?>