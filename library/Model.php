<?php

/**
 * PDO数据库操作类
 * Auth:勾国印
 * Blog:www.gouguoyin.cn
 */
class Model {
    private  $dbtype; //数据库类型
    private  $dbhost; //数据库地址
    private  $dbport; //数据库端口号
    private  $dbname; //数据库名
    private  $dbuser; //数据库用户名
    private  $dbpass; //数据库密码
    private  $dbprefix; //表前缀
    private  $dbcharset; //数据库字符编码
    private  $stmt = null;
    private  $masterDB = null;
    private  $slaveDB = null;
    private  $db = null;
    private  $sql = '';
    private  $connect = true; // 是否长连接

    private $chain = ['show' => '', 'lock' => '','count' => ''];

    private $option = ['join' => '', 'on' => '', 'where' => '', 'order' => '', 'limit' => ''];


    /*
     * 构造函数
     */
    public function __construct($tableName = '',$tablePrefix = '') {
        if(!$tablePrefix){
            $tablePrefix = $this->dbprefix;
        }
        //完整表名
        $this->tableName = $tablePrefix.$tableName;

        $this->masterDB = $this->masterConnect();
        if(C('DB_DEPLOY_TYPE')){
            //选择从库连接
            $this->slaveDB = $this->slaveConnect();
        }else{
            //选择主库连接
            $this->slaveDB = $this->masterConnect();
        }

    }

    /*
     * 析构函数
     */
    public function __destruct() {
        $this->close();
    }


    /*
     * 主库连接
     */
    private function masterConnect() {
        //获取主库配置信息
        $this->dbtype = C('MASTER.DB_TYPE');
        $this->dbhost = C('MASTER.DB_HOST');
        $this->dbname = C('MASTER.DB_NAME');
        $this->dbuser = C('MASTER.DB_USER');
        $this->dbpass = C('MASTER.DB_PWD');
        $this->dbport = C('MASTER.DB_PORT');
        $this->dbprefix  = C('MASTER.DB_PREFIX');
        $this->dbcharset = C('MASTER.DB_CHARSET');

        try {
            $this->db = new PDO($this->dbtype . ':host=' . $this->dbhost . ';port=' . $this->dbport . ';dbname=' . $this->dbname, $this->dbuser, $this->dbpass, array(
                PDO::ATTR_PERSISTENT => $this->connect
            ));

            $this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            //设置字符集
            $this->db->exec('SET NAMES ' . $this->dbcharset);

            return $this->db;
        }
        catch(PDOException $e) {
            $this->sqlException("Connect Error Infomation:" . $e->getMessage());
        }
    }

    /*
     * 从库连接
     */
    private function slaveConnect() {
        //获取从库配置信息
        $this->dbtype = C('SLAVE.DB_TYPE', 'mysql');
        $this->dbhost = C('SLAVE.DB_HOST');
        $this->dbname = C('SLAVE.DB_NAME');
        $this->dbuser = C('SLAVE.DB_USER');
        $this->dbpass = C('SLAVE.DB_PWD');
        $this->dbport = C('SLAVE.DB_PORT', '3306');
        $this->dbprefix  = C('SLAVE.DB_PREFIX', '');
        $this->dbcharset = C('SLAVE.DB_CHARSET', 'UTF8');

        try {
            $this->db = new PDO($this->dbtype . ':host=' . $this->dbhost . ';port=' . $this->dbport . ';dbname=' . $this->dbname, $this->dbuser, $this->dbpass, array(
                PDO::ATTR_PERSISTENT => $this->connect
            ));
            $this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            //设置字符集
            $this->db->exec('SET NAMES ' . $this->dbcharset);

            return $this->db;
        }
        catch(PDOException $e) {
            $this->sqlException("Connect Error Infomation:" . $e->getMessage());
        }
    }

    /*
     * 关闭数据连接
     */
    public function close() {
        //关闭主库连接
        $this->masterDB = null;
        //关闭从库连接
        $this->slaveDB = null;
    }

    /*
     * join操作
     */
    public function join($tableName, $type = 'inner') {
        $this->option['join'][] = ' '.strtoupper($type)." JOIN {$tableName}";
        return $this;
    }

    /*
     * on操作，必须配合join使用,且必须紧跟在join操作后面
     */
    public function on($on) {
        $on = (string)$on;
        if($this->option['join']){
            $this->option['on'][] = "ON ".$on;
        }
        return $this;
    }

    /*
     * 过滤条件
     */
    public function where($where) {
        $where = (string)$where;
        $this->option["where"] = "WHERE " . $where;
        return $this;
    }

    /*
     * 排序方式
     */
    public function order($order = 'id DESC') {
        $this->option["order"] = "ORDER BY " . $order;
        return $this;
    }


    /*
     * 限制数量
     */
    public function limit($offset,$rows = 0) {
        $offset = intval($offset);
        $rows = intval($rows);

        if (!$rows ){
            $this->option["limit"] = "LIMIT 0," . $offset;
        }else{
            $this->option["limit"] = "LIMIT $offset," . $rows;
        }

        return $this;
    }

    /*
     * 数据分页
     */
    public function page($page_no, $per_rows = 10) {
        $page_no = intval($page_no);
        $per_rows = intval($per_rows);

        $offset = ($page_no - 1) * $per_rows;
        $this->option['limit'] = " limit {$offset},{$per_rows}";

        return $this;

    }


    /*
     * 加锁，仅支持innodb，一般用于事务
     */
    public function lock($bool = false) {
        $this->chain['lock'] = 'FOR UPDATE';

        return $this;
    }

    /*
     * 只显示sql语句
     */
    public function show($bool = false) {

        $this->chain['show'] = $bool;

        return $this;
    }

    /*
     * 查询指定字段
     */
    private function field($field,$include = true){
        if($include || $field == '*'){
            return $field;
        }

        $fields  =  explode(',',$field);

        $all_fields     =  $this->getFields();
        $field      =  $fields?array_diff($all_fields,$fields):'*';

        return implode(',', $field);
    }

    //获取当前库的所有表名
    public function getTablesName() {

        //选择主库连接
        $this->db = $this->masterDB;

        $this->sql = 'SHOW TABLES FROM '.$this->dbname;

        $this->stmt = $this->db->query($this->sql);

        if($this->stmt === false){
            return $this->sqlError();
        }

        return $this->stmt->fetchAll(PDO::FETCH_COLUMN);

    }

    //获取当前表的所有字段
    public function getFields(){
        //选择主库连接
        $this->db = $this->masterDB;

        $this->sql = 'DESC '.$this->tableName;

        if($this->chain['show'] === true){
            return $this->sql;
        }

        $this->stmt = $this->db->query($this->sql);

        if($this->stmt === false){
            return $this->sqlError();
        }

        return $this->stmt->fetchAll(PDO::FETCH_COLUMN);

    }

    //获取当前表的主键字段
    public function getPK(){
        //选择主库连接
        $this->db = $this->masterDB;

        $this->stmt = $this->db->query('DESC '.$this->tableName);

        if($this->stmt === false){
            return $this->sqlError();
        }

        $table_fields = $this->stmt->fetchAll();
        foreach($table_fields as $table_field){
            if($table_field['Key'] == 'PRI'){
                return $table_field['Field'];
            }
        }

        return '';

    }

    /**
     * 查询单条数据
     * @param string $field 查询字段，多个字段英文逗号连接
     * @return array
     */
    public function find($field = '*',$include = true) {
        $field = $this->field($field, $include);

        if(C('DB_DEPLOY_TYPE')){
            //选择从库连接
            $this->db = $this->slaveDB;
        }else{
            //选择主库连接
            $this->db = $this->masterDB;
        }

        $this->limit(1);

        $this->sql = "SELECT $field FROM $this->tableName";
        $this->sql .= $this->sqlOptions();

        if($this->chain['lock']){
            $this->sql .= ' ' .$this->chain['lock'];
        }

        if($this->chain['show'] === true){
            return $this->sql;
        }

        $this->stmt = $this->db->query($this->sql);

        if($this->stmt === false){
            return $this->sqlError();
        }

        return $this->stmt->fetch(PDO::FETCH_BOTH);

    }

    /**
     * 查询数据集
     * @param (string)$field 查询字段，多个字段英文逗号连接
     * @return object 当前PDO对象
     */
    public function findAll($field = '*',$include = true){
        $field = $this->field($field, $include);

        if(C('DB_DEPLOY_TYPE')){
            //选择从库连接
            $this->db = $this->slaveDB;
        }else{
            //选择主库连接
            $this->db = $this->masterDB;
        }

        $this->sql = "SELECT $field FROM $this->tableName";

        $this->sql .= $this->sqlOptions();

        if($this->chain['lock']){
            $this->sql .= ' ' .$this->chain['lock'];
        }

        if($this->chain['show'] === true){
            return $this->sql;
        }


        $this->stmt = $this->db->query($this->sql);

        if($this->stmt === false){
            return $this->sqlError();
        }

        //查询数据数量
        if (strstr($field, 'count(')) {
            return $this->stmt->fetchColumn();
        }

        //只返回关联数组
        return $this->stmt->fetchAll(PDO::FETCH_BOTH);
    }

    /**
     * findAll的别名，功能同findAll
     * @param (string)$field 查询字段，多个字段英文逗号连接
     * @return object 当前PDO对象
     */
    public function select($field = '*'){
        return $this->findAll($field);
    }

    /**
     * 查询数据数量
     * @return int
     */
    public function count(){
        return $this->select('count(*)');
    }


    /**
     * 添加单条数据
     * @param (array)$data
     * @return int 自增主键id
     */
    public function add($data){

        if(!$data || !is_array($data)){
            return 0;
        }
        //选择主库连接
        $this->db = $this->masterDB;

        $this->sql= "INSERT INTO $this->tableName SET ".$this->sqlParms($data);


        if($this->chain['show'] === true){
            return $this->sql;
        }

        $this->stmt = $this->db->query($this->sql);

        if($this->stmt === false){
            return $this->sqlError();
        }

        return $this->db->lastInsertId();

    }

    /**
     * 添加多条数据
     * @param (array)$data1,$data2,$data3,.....
     * @return int 影响行数
     */
    public function addAll(){
        //获取参数数量
        $args_count = func_num_args();

        if(func_num_args() <= 1){
            return 0;
        }
        //获取参数列表
        $args_list =  func_get_args();
        $fields = '';
        $values = '';
        foreach($args_list[0] as $k =>$v){
            $fields .= '`'.$k.'`,';
        }

        $fields = '('.trim($fields, ',').')';

        for ($i = 0; $i < $args_count; $i++) {
            if(!is_array($args_list[$i])){
                return 0;
            }
            $values .= '(';
            foreach($args_list[$i] as $k1 =>$v1){
                if(is_string($v1)){
                    $values .= "'".$v1."',";
                }else{
                    $values .= $v1.",";
                }
            }
            $values = trim($values, ',');
            $values .= '),';
        }

        $values = trim($values, ',');

        //选择主库连接
        $this->db = $this->masterDB;

        $this->sql= "INSERT INTO $this->tableName {$fields} VALUES {$values}";


        if($this->chain['show'] === true){
            return $this->sql;
        }

        $this->stmt = $this->db->query($this->sql);

        if($this->stmt === false){
            return $this->sqlError();
        }

        return $this->stmt->rowCount();

    }


    /**
     * 更新数据
     * @param (array)$data
     * @return bool
     */
    public function update($data){
        if(!$data || !is_array($data)){
            return false;
        }

        //连接主库
        $this->db = $this->masterDB;

        if($this->option['where']){
            $this->sql= "UPDATE $this->tableName SET ";

            $this->sql .= $this->sqlParms($data).$this->sqlOptions();

            if($this->chain['show'] === true){
                return $this->sql;
            }

            $this->stmt = $this->db->exec($this->sql);

            if($this->stmt === false){
                return $this->sqlError();
            }

            return true;
        }

    }

    /**
     * 删除数据
     * @return bool
     */
    public function delete(){
        //连接主库
        $this->db = $this->masterDB;

        if($this->option['where']){
            $this->sql = "DELETE FROM $this->tableName";
            $this->sql .= $this->sqlOptions();

            if($this->chain['show'] === true){
                return $this->sql;
            }

            $this->stmt = $this->db->exec($this->sql);

            if($this->stmt === false){
                return $this->sqlError();
            }

            return true;

        }
    }

    /**
     * 执行原生sql语句
     * @param (string)$sql
     * @return object
     */
    public function query($sql, $show = false){

        if($show === true || $this->chain['show'] == true){
            return $sql;
        }

        if (strstr($sql, 'count')) {
            //查询数量

            if(C('DB_DEPLOY_TYPE')){
                //选择从库连接
                $this->db = $this->slaveDB;
            }else{
                //选择主库连接
                $this->db = $this->masterDB;
            }

            $this->stmt = $this->db->query($sql);

            if($this->stmt === false){
                return $this->sqlError();
            }

            return $this->stmt->fetchColumn();

        }

        if (strstr($sql, 'select')) {
            //查询操作

            if(C('DB_DEPLOY_TYPE')){
                //选择从库连接
                $this->db = $this->slaveDB;
            }else{
                //选择主库连接
                $this->db = $this->masterDB;
            }

            $this->stmt = $this->db->query($sql);

            if($this->stmt === false){
                return $this->sqlError();
            }

            return $this->stmt->fetchAll(4);
        }


        if (strstr($sql, 'insert')) {
            //添加操作

            //连接主库
            $this->db = $this->masterDB;
            $this->stmt = $this->db->query($sql);

            if($this->stmt === false){
                return $this->sqlError($sql);
            }

            return $this->db->lastInsertId();
        }

        if (strstr($sql, 'update') || strstr($sql, 'delete')) {
            //更新/删除操作

            //选择主库连接
            $this->db = $this->masterDB;

            $this->stmt = $this->db->exec($sql);

            if($this->stmt === false){
                return $this->sqlError($sql);
            }

            return true;
        }

    }

    /**
     * 开启事务(需要数据库支持，否则无效)
     * @return mixed
     */
    public function startTrans(){
        //选择主库连接
        $this->db = $this->masterDB;

        //关闭自动提交
        $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
        return $this->db->beginTransaction();
    }

    /**
     * 执行开启事务(需要数据库支持，否则无效)
     * @return mixed
     */
    public function commit(){
        //选择主库连接
        $this->db = $this->masterDB;

        return $this->db->commit();
    }

    /**
     * 事务回滚(需要数据库支持，否则无效)
     * @return mixed
     */
    public function rollback(){
        //选择主库连接
        $this->db = $this->masterDB;

        return $this->db->rollBack();

    }

    /**
     * 获取sql操作方法字符串
     * @return string
     */
    private function sqlOptions(){
//        return implode(" ", $this->option);
        $options = array();

        foreach($this->option as $k => $v){
            if(is_array($v)){
                foreach($v as $k1 => $v1){
                    $options[$k1] .= ' '.$v1;
                }
            }else{
                $options[$k] = $v;
            }
        }

        return str_replace('  ', ' ', implode(" ", $options));

    }


    /**
     * 获取sql参数字符串
     * @param (array)$args
     * @return string
     */
    private function sqlParms($args){
        $parms = '';
        if(is_array($args)){
            foreach ($args as $k => $v) {
                if ($v == '' || $k == 'show') {
                    continue;
                }
                $parms .= "`$k`='$v'".',';
            }

            return rtrim($parms, ',');
        }
    }
    /**
     * 获取sql异常信息
     * @return string
     */
    private function sqlException($msg){
        if(C('EMAIL_NOTICE')){
            send_email(C('SYTEM_EMAIL'), '数据库连接错误警告', $msg);
        }

        if(C('WEIXIN_NOTICE')){
            weixin_helper::send_template(['title' => '数据库连接错误警告', 'msg' => $msg]);
        }

        if(!APP_DEBUG){
            return ;
        }
        die("Connect Error Infomation:" . $msg);
    }


    /**
     * 获取sql执行错误信息
     * @return string
     */
    private function sqlError($sql = '') {

        if(!$sql){
            $sql = $this->sql;
        }
        $errorInfo = $this->db->errorInfo();

        $msg = '';

        if($errorInfo[0] !== '00000'){
            $msg= '<br>';
            $msg.= 'Query Error:'.$errorInfo[2];
            $msg.= '<br>';
            $msg.= 'Error SQL:'.$sql;
            $msg.= '<br>';

        }
        if(C('EMAIL_NOTICE')){
            send_email(C('SYTEM_EMAIL'), 'SQL异常警告', $msg);
        }

        if(C('WEIXIN_NOTICE')){
            weixin_helper::send_template('SQL异常警告', $errorInfo[2], "异常SQL:{$sql}");
        }

        //如果没有开启调试模式，不输出错误信息
        if(!APP_DEBUG){
            return ;
        }

        return $msg;
    }


}