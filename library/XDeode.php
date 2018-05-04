<?php
/**
 * ID加密解密类 XDeode.class.php
 * Author: 勾国印 (phper@gouguoyin.cn)
 * Date: 2015-5-19 上午10:01:51
 */

class XDeode
{
    private $strbase = "Flpvf70CsakVjqgeWUPXQxSyJizmNH6B1u3b8cAEKwTd54nRtZOMDhoG2YLrI";
    private $key,$length,$codelen,$codenums,$codeext;

    function __construct($length = 10,$key = 2543.5415412812)
    {
        $this->key      = $key;
        $this->length   = $length;
        $this->codelen  = substr($this->strbase, 0, $this->length);
        $this->codenums = substr($this->strbase, $this->length, 10);
        $this->codeext  = substr($this->strbase, $this->length + 10);
    }
    //加密
    public function encode($id)
    {
        $rtn     = "";
        $numslen = strlen($id);
        //密文第一位标记数字的长度
        $begin   = substr($this->codelen,$numslen - 1,1);

        //密文的扩展位
        $extlen = $this->length - $numslen - 1;
        $temp   = str_replace('.', '', $id / $this->key);
        $temp   = substr($temp,-$extlen);

        $arrextTemp = str_split($this->codeext);
        $arrext     = str_split($temp);
        foreach ($arrext as $v) {
            $rtn .= $arrextTemp[$v];
        }

        $arrnumsTemp = str_split($this->codenums);
        $arrnums     = str_split($id);
        foreach ($arrnums as $v) {
            $rtn .= $arrnumsTemp[$v];
        }
        return $begin.$rtn;
    }
    //解密
    public function decode($code)
    {
        $begin = substr($code,0,1);
        $rtn = '';
        $len = strpos($this->codelen,$begin);
        if($len!== false){
            $len++;
            $arrnums = str_split(substr($code,-$len));
            foreach ($arrnums as $v) {
                $rtn .= strpos($this->codenums,$v);
            }
        }
        return $rtn;
    }

}
