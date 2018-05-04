<?php
//通用配置
return array(
    'SYTEM_EMAIL' => '245629560@qq.com',
//    'SYTEM_EMAIL' => 'wangxudong@juzifenqi.com',

    //邮箱配置
    'EMAIL' => array(
        'TITLE' => '桔子分期PHP',
        'HOST' => 'smtpdm.aliyun.com',
        'USER' => 'php@notice.juzifenqi.cn',
        'PASSWORD' => 'BKXHES2boewc0A',
        'PORT' => '80',
    ),

    //COOKIE配置
    'COOKIE' => array(
        'EXPIRE'   =>  0,       // Cookie有效期
        'DOMAIN'   =>  '',      // Cookie有效域名
        'PATH'     =>  '/',     // Cookie路径
        'PREFIX'   =>  '',      // Cookie前缀 避免冲突
        'KEY'      =>  '',      // Cookie加密密钥
    ),

    //异常时是否邮件通知
    'EMAIL_NOTICE' => false,

    //异常时是否微信通知
    'WEIXIN_NOTICE' => false,

    //禁止访问ip,支持通配符
    'FORBID_IP' => array(/*'223.72.90.34', '223.72.68.*'*/),


);


