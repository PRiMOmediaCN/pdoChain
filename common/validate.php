<?php
/**
 * 验证函数库 validate.php
 * Author: 勾国印 (phper@gouguoyin.cn) 
 * Date: 2015年12月8日 下午10:21:20
*/

/**
 * 是否是手机端访问（包含微信端）
 */
function on_mobile()
{
    return Validate::onMobile();
}

/**
 * 是否是微信端访问
 */
function on_weixin()
{
    return Validate::onWeixin();
}

/**
 * 是否是IE浏览器判断
 */
function on_ie() {
	return Validate::onIE();
}

/**
 * 是否是合法邮箱
 */
function is_email($email) {
	return Validate::isEmail($email);
}

/**
 * 是否是合法网址
 */
function is_url($url) {
	return Validate::isUrl($url);
}

/**
 * 是否是合法固定电话
 */
function is_tel($tel) {
	return Validate::isTel($tel);
}

/**
 * 是否是合法手机号
 */
function is_phone($phone) {
	return Validate::isPhone($phone);
}

/**
 * 是否是合法IP
 */
function is_ip($ip) {
    return Validate::isIP($ip);
}

/**
 * 是否是数字
 */
function is_number($number) {
	return Validate::isNumber($number);
}

/**
 * 是否是合法身份证
 */
function is_card($card) {
    return Validate::isCard($card);
}

/**
 * 是否是合法邮编
 */
function is_zip($zip) {
	return Validate::isZip($zip);
}

/**
 * 是否是合法QQ号
 */
function is_qq($qq) {
	return Validate::isQQ($qq);
}

/**
 * 是否是纯英文字母
 */
function is_english($english) {
	return Validate::isEnglish($english);
}

/**
 * 是否是纯中文
 */
function is_chinese($chinese) {
	return Validate::isChinese($chinese);
}
/**
 * 是否是合法的标识符(以字母或下划线开始，后面跟着任何字母，数字或下划线)
 */
function is_identifier($value) {
    return Validate::isIdentifier($value);
} 

/**
 * 是否为合法姓名 数字字母或下划线
 */
function is_name($name) {
    return Validate::isName($name);
}

/**是否是合法日期
 * @param $date
 * @param array $formats
 * @return bool
 */
function is_date($date, $formats = array("Y-m-d", "Y/m/d")) {
    return Validate::isDate($date, $formats);
}

