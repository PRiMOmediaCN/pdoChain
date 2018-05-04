<?php
/*
 * 测试环境配置信息
 */
return array(

    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_DEPLOY_TYPE' =>  1,

    //当部署方式为集中式时是数据库配置，部署方式为主从服务器时是主库配置
    'MASTER' => array(
        'DB_TYPE' => 'mysql',
        'DB_HOST' => 'rm-2ze4b2qv8pd8qvn2n.mysql.rds.aliyuncs.com',
        'DB_NAME' => 'juzi',
        'DB_USER' => 'juzi',
        'DB_PWD' => 'J6zifenq12&c0m86',
        'DB_PREFIX' => '',
        'DB_PORT' => '3306',
        'DB_CHARSET' => 'UTF8',
    ),

    //从库配置，只有当数据库部署方式为主从服务器时有效
    'SLAVE' => array(
        'DB_TYPE' => 'mysql',
        'DB_HOST' => 'rr-2ze0fry4gf7209z41.mysql.rds.aliyuncs.com',
        'DB_NAME' => 'juzi',
        'DB_USER' => 'phpdev',
        'DB_PWD' => 'UvVHTU@CUz2kEFO',
        'DB_PREFIX' => '',
        'DB_PORT' => '3306',
        'DB_CHARSET' => 'UTF8',
    ),


);


