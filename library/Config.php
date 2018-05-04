<?php
/**
 * 配置信息封装
 * Auth:勾国印
 * Blog:www.gouguoyin.cn
 */
class Config {

	//获取配置字段的值
	public static function get($key = '', $default = '')
	{
		$common_config = include CONFIG_PATH.'/config.php';
		$db_config = include CONFIG_PATH.'/'.APP_ENV.'.php';

		$_config = array_merge($db_config, $common_config);

		if(!$key){
			return $_config;
		}

		if (!strpos($key, '.')) {
			return $_config[$key];
		}

		$_key = explode('.', $key);
		$_value = $_config[$_key[0]][$_key[1]];
		if($default){
			return $_value ? $_value : $default;
		}

		return $_value;

	}
}
?>