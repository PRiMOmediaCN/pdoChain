<?php
/**
 * 常见操作演示
 */

include_once("init.php");


$model = M('players');

/*单条查询*/
$result = $model->where('id = 3')->find();

/*多条查询*/
//$result = $model->where('id > 3')->findAll('a1,a2', false);

/*更新查询*/
//$result = $model->where('id > 3')->update(['a1' => '33333']);

/*删除查询*/
//$result = $model->where('id > 5')->delete();

/*插入单条数据*/
//$result = $model->add(['a1' => '111111']);

/*插入多条数据*/
//$result = $model->addAll(['a1' => '111111'], ['a1' => '22222'],['a1' => '33333']);

/*事务*/
//try{
//    $model->startTrans();
//    $result = $model->add(['a1' => '111111']);
//    $result = $model->add(['a133333' => '222222']);
//
//    $model->commit();
//}catch(Exception $e){
//    echo $e->getMessage();
//    $model->rollback();
//}



//print_r($result);
?>