# pdoChain

使用方法

解压压缩包后，引入init.php
include($_SERVER['DOCUMENT_ROOT']."/frame/init.php"); 修改config目录下的数据库配置文件，develop.php为测试环境配置文件，product.php为正式环境配置文件，更多用法参考demo.php。
常用数据库操作

1、查询单条数据
M('user')->where('id = 1')->find(); //返回所有字段数组
M('user')->where('id = 1')->find(‘name’); //返回'name'字段数组
M('user')->where('id = 1')->find(‘name’, false); //返回'name'之外字段数组
M('user')->where('id = 1')->find(‘id,name’); //返回指定多个字段数组
M('user')->where('id = 1')->show(true)->find(‘name’); ////只返回sql语句不执行 2、查询多条数据
M('user')->where('status = 1')->limit(1, 10)->findAll(); //返回所有字段数组
M('user')->where('status = 1')->limit(1, 10)->findAll(‘name’); //返回指定字段数组
M('user')->where('status = 1')->limit(1, 10)->findAll(‘name’, false); //返回指定字段之外数组
M('user')->where('status = 1')->page(1, 10)->findAll(‘name’); //分页查询，page(当前页码，每页展示数量)
M('user')->where('status = 1')->limit(20)->lock(true)->findAll(); //加锁，非INNODB存储引擎无效，主要用于事务
M('user')->where('status = 1 and age >= 18')->limit(10)->show(true)->findAll(); //只返回sql语句不执行 3、多表联查
M('user')->join('order')->on('user.id = order.user_id')->where('user.status = 1')->findAll(); //两表联查，默认为内连查询(INNER JOIN)
M('user')->join('order'， 'left')->on('user.id = order.user_id')->join('goods', 'right')->on('goods.id = order.goods_id')->where('user.status = 1')->findAll(); //三表联查，左联查询(LEFT JOIN) 4、查询数量 
M('user')->where('status = 1')->findAll('count(*)');或M('user')->where('status = 1')->count(); //返回数据数量
M('user')->where('status = 1')->show(true)->count(); //只返回sql语句不执行 5、添加单条数据
M('user')->add(['name' => '勾国印', 'sex' => 1, 'age' => 18]); //返回新插入数据的自增id
M('user')->show(true)->add(['name' => '勾国印', 'sex' => 1, 'age' => 18]); //只返回sql语句不执行 6、添加多条数据
M('user')->addAll(['name' => '勾国印', 'sex' => 1, 'age' => 18], ['name' => '勾国磊', 'sex' => 2, 'age' => 19], ['name' => '张东川', 'sex' => 0, 'age' => 16]); //返回影响行数(参数个数不限，但是字段必须相同)
M('user')->show(true)->addAll(['name' => '勾国印', 'sex' => 1, 'age' => 18], ['name' => '勾国磊', 'sex' => 2, 'age' => 19], ['name' => '张东川', 'sex' => 0, 'age' => 16]);  //只返回sql语句不执行 7、更新数据
M('user')->where('id = 1')->update(['sex' => 2]); //返回布尔值，不管更新数据有没有改动都返回true，除非操作失败返回false
M('user')->where('id = 1')->show(true)->update(['sex' => 2]); //只返回sql语句不执行 8、删除数据
M('user')->where('id = 1')->delete(); //返回布尔值
M('user')->where('id = 1')->show(true)->delete(); //只返回sql语句不执行 9、执行原生sql语句（自动读写分离）
M()->query('select id from user where status = 1'); //查询数据，返回结果集数组
M()->query('select count(*) from user where status = 1'); //查询数量，返回结果集数量
M()->query("insert into user set  name = '勾国印'"); //新增数据，返回新插入数据的自增id
M()->query("update user  set name = '勾国印' where id = 10"); //返回布尔值，不管更新数据有没有改动都返回true，除非操作失败返回false
M()->query("delete from user  where id = 10"); //删除数据，返回布尔值
M()->show(true)->query("select id from user where status = 1");  //只返回sql语句不执行 10、事务处理(非INNODB存储引擎无效)
try{
    M('user')->startTrans(); //开启事务
    M('user')->add(['name' => '勾国印']); //具体数据库操作
    M('user')->commit(); //提交事务
}catch(PDOException $e){
    echo $e->getMessage();
    M('user')->rollback(); //回滚
} 
常用公共函数

1、M($tableName = ' ', $tablePrefix = ' ')： 实例化模型

M('user')->find(); 2、I($name, $default = ' ') ：获取请求参数值
$page =  I('p', 1); 3、C($name, $default = ' ') ：获取配置文件里的配置字段
C('EMAIL'); //返回EMAIL数组
C('EMAIL.EMAIL_TITLE', '勾国印'); //返回EMAIL_TITLE字段的值，如果不存在返回默认值 4、_uri($table_name, $where, $field = null, $show = flase)：获取单条数据/字段
_uri('user', 'id = 1'); //返回id为1的会员所有字段数组
_uri('user', 'id = 1', 'name'); //返回id为1的会员名字，如勾国印
_uri('user', 2, 'sex'); //返回主键字段为2的会员性别，如男
_uri('user', 'id = 1', 'name,sex'); //返回id为1的会员名字和性别数组
_uri('user', 2, 'name,sex'); //返回主键字段为2的会员名字和性别数组
_uri('user', 2, 'name,sex', true); //只返回sql语句不执行 5、_list($table_name, $where, $num = 10, $order_by = 'id desc', $field = null, $show = false)：获取多条数据
_list('user', 'status = 1', 0); //返回所有满足条件的所有字段数组
_list('user', 'status = 1', 10); //返回10条所有满足条件的所有字段数组
_list('user', 'status = 1', 20, 'id desc,sort asc', 'name,sex'); //返回20条所有满足条件的指定字段数组
_list('user', 'status = 1', 20, 'id desc,sort asc', 'name,sex', true); //只返回sql语句不执行 6、cookie($key, $value = ' ', $expire = 24)：cookie操作，过期时间单位小时
cookie('name', '勾国印'); //设置cookie
cookie('name'); //获取键为name的cookie值
cookie('name', null); //删除键为name的cookie值
7、session($key, $value = ' ')：session操作

session('name', '勾国印'); //设置session
session('name'); //获取键为name的session值
session('name', null); //删除键为name的session值 8、send_email($eamil, $title, $content)：发送邮件
$message= array(
  	'id' => 1,
	'title' => '商品一',
	'price' => 100,
);
send_email('gouguolei@vip.qq.com', '邮件标题', var_export($message, true)); 9、http_request($url, $type = 'get', $data = null, $content_type = 'application/json', $time_out = 20)：模拟curl请求
$api_url = 'http://192.168.1.222:9088/ethank-member-manager/memberCardExternalCall/addRewards.json';
        
$info = array(
    'phoneNum' => '18515947075',
    'price'  => 100,
);        

$result = json_decode(http_request($api_url, 'GET', $info), true); 10、id_encrypt($id, $length = 10)：数字加密，返回字符串。（默认加密后长度为10）
id_encrypt(1); //返回FzxxmyQzxV 11、id_descrypt($str)：解密加密后的字符串，返回数字
id_descrypt('FzxxmyQzxV'); //返回1 12、random_hash($length = 4)：生成随机字符串
random_hash(10); //返回nWn3BhJ2CG 13、get_client_ip()：获取访问ip
get_client_ip(); //返回223.72.90.34、 14、ajaxReturn($code, $msg, $response)：返回AJAX数据
ajaxReturn('200', '登录成功');
ajaxReturn('200', '登录成功', ['user_id' => 1]);
调试模式

在url上加上debug=1参数即可开启调试模式，会在当前页面展示sql错误和异常错误，参数名可以在init.php文件里定义
define('DEBUG_PARAM', 'debug');

原贴地址：http://www.gouguoyin.cn/php/129.html
