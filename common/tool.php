<?php
/*
 * 工具函数库
 * Auth:gouguoyin
 * Blog:www.gouguoyin.cn
 */
//生成随机字符串
if (!function_exists('random_hash')) {
    function random_hash($length = 4)
    {
        $salt = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        $count = count($salt);
        $hash = '';
        for ($i = 0; $i < $length; $i++) {
            $hash .= $salt[mt_rand(0, $count-1)];
        }
        return $hash;
    }
}

//curl请求
if (!function_exists('http_request')) {
    function http_request($url, $type = 'get', $data = null, $content_type = 'application/json', $time_out = 20){
        $curl = curl_init();
        $type = strtoupper($type);


        if (!in_array($type, array('POST','GET'))) {
            $error_array = array(
                'error_no' => '-1',
                'error_msg' => '非法的请求方式'
            );
            return json_encode($error_array);
        }

        if ($data) {
            if ($type == 'POST') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            } else if ($type == 'GET') {
                $data = http_build_query($data);
                $url = strpos($url, '?') !== false ? $url.'&'.$data :  $url.'?'.$data;
            }
        }

        $headers = array(
            "Content-type: $content_type",
            "Cache-Control: no-cache",
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER , false);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST , false);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT,$time_out);
        $output     = curl_exec($curl);
        $curl_no    = curl_errno($curl);
        $curl_error = curl_error($curl);
        $curl_info  = curl_getinfo($curl);
        curl_close($curl);

        if ($curl_no > 0) {
            $error_array = array(
                'error_no' => $curl_no,
                'error_msg' => $curl_error
            );
            return json_encode($error_array);
        }

        //print_r($curl_info);exit;

        $http_code = $curl_info['http_code'];

        if ('200' != $http_code) {
            $error_array = array(
                'error_no' => $http_code,
                'error_msg' => 'URL请求错误，HTTP状态码为'.$http_code
            );
            return json_encode($error_array);
        }
        return $output;
    }
}

//发送邮件
if (!function_exists('send_email')) {
    function send_email($address, $title, $message)
    {

        if(!$address){
            return ['status' => false, 'msg' => '缺少收件地址'];
        }

        if(!$title){
            return ['status' => false, 'msg' => '缺少邮件标题'];
        }

        if(!$message){
            return ['status' => false, 'msg' => '缺少收件内容'];
        }


        $email_title = C('EMAIL.TITLE');
        $email_host = C('EMAIL.HOST');
        $email_user = C('EMAIL.USER');
        $email_password = C('EMAIL.PASSWORD');
        $email_port = C('EMAIL.PORT');

        if(!$email_user || !$email_password || !$email_host || !$email_title){
            return ['status' => false, 'msg' => '缺少必要配置参数'];
        }

        require(VENDOR_PATH."/PHPMailer/class.phpmailer.php");

        $mail = new \PHPMailer();

        // 使用SMTP方式发送
        $mail->IsSMTP();

        // 邮局服务器地址
        $mail->Host = $email_host;

        // 邮局服务器端口
        $mail->Port = $email_port;

        // 启用SMTP验证功能
        $mail->SMTPAuth = true;

        // 发件人email地址
        $mail->Username = $email_user;

        // 发件人email密码
        $mail->Password = $email_password;

        // 发件人email地址
        $mail->From = $email_user;

        // 发件人昵称
        $mail->FromName = $email_title;

        // 收件人地址，可以替换成任何想要接收邮件的email信箱
        $mail->AddAddress($address);

        // 是否使用HTML格式
        $mail->IsHTML(true);

        // 邮件标题
        $mail->Subject = $title;

        // 邮件内容
        $mail->Body = $message;

        if(!$mail->Send())
        {
            return ['status' => false, 'msg' => $mail->ErrorInfo];

        }else{
            return ['status' => true, 'msg' => '发送成功'];
        }

    }
}

//获取用户真实ip
if (!function_exists('get_client_ip')) {
    function get_client_ip(){
        return Request::getClientIp();
    }
}

//调用接口获取ip所在地址
if (!function_exists('http_request')) {
    function get_ip_address($ip = '', $type = null)
    {
        if(!$ip){
            $ip = get_client_ip();
        }
        if($ip == '0.0.0.0'){
            return '';
        }

        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        //调用淘宝接口获取信息
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        if ($data['code']) {
            return $data['data'];
        } else {
            $country = $data['data']['country'];
            $province = $data['data']['region'];
            $city = $data['data']['city'];
            $area = $data['data']['area'];

            if($type == 'country'){
                return $country;
            }elseif($type == 'province'){
                return $province;
            }elseif($type == 'area'){
                return $area;
            }elseif($type == 'city'){
                return $city;
            }else{
                return $country.' '.$area.' '.$province.' '.$city;
            }

        }

    }
}

//id加密
if (!function_exists('id_encrypt')) {
    function id_encrypt($id, $length = 10)
    {
        if(!is_numeric($id)){
            return ;
        }
        $XDeode = new XDeode($length);
        return $XDeode->encode($id);
    }
}

//id解密
if (!function_exists('id_descrypt')) {
    function id_descrypt($str)
    {
        $XDeode = new XDeode();
        return $XDeode->decode($str);
    }
}

//ajax 返回
if (!function_exists('ajaxReturn')) {
    function ajaxReturn($code, $msg, $response)
    {
        $data = array(
            'code' =>(string)$code,
            'msg'  => $msg,
        );

        if (isset($response)) {
            $data['data'] = $response;
        }

        // 返回JSON数据格式到客户端 包含状态信息
        //header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}



