<?php
/**
 * 会员助手函数
 */
class user_helper{
    //获取当前登录用户id
    public static function get_user_id(){
        $user_id =  cookie('user_id');
        return isset($user_id) ? $user_id : 0;
    }


    /**
     * 检测用户是否存在
     * @param  string $mobile 手机号
     * @return boolean true 存在 false 不存在
     */
    public static function checkMobile ($mobile='') {
    	$rs = M('hy_yy')->where(" a1 = '{$mobile}'")->show(true)->count();
    	return $rs ? true : false;
    }

    /**
     * 用户详细信息
     * @param  [type]  $pk       条件 ID 或 手机号
     * @param  boolean $isMobile 是否为手机号 默认用户ID
     * @return array
     */
    public static function get_user_info ($pk, $isMobile=false) {
    	$where = array( ' 1 ');
    	if($isMobile) {
    		$where[] = " a1 = '{$pk}' ";
    	}else {
    		$where[] = " id = '{$pk}' ";
    	}
    	$where = join(' AND ', $where);
    	$rs = M('hy_yy')->where($where)->find();
    	return $rs;
    }


}