<?php
/**********************************************************
 * log::setLog("test".time());
 * //记录信息数组
 * $logs=new Log();
 * $arr=array(
 * 'type'=>'info',
 * 'info'=>'test',
 * 'time'=>date("Y-m-d H:i:s",time())
 * );
 * log::setLog($arr);
 **********************************************************/


class Log {
    private static $_filepath; //文件路径
    private static $_filename; //日志文件名
    private static $_filehandle; //文件句柄


    /**
     *作用:初始化记录类
     *输入:文件的路径,要写入的文件名
     *输出:无
     */
    public function Log($dir = null, $filename = null) {
        //默认路径为当前路径
        $dir = rtrim(LOG_PATH, '/');
        $this->_filepath = empty ( $dir ) ? '' : $dir;

        //默认为以时间＋.log的文件文件
        $this->_filename = empty ( $filename ) ? date ( 'Y-m-d', time () ) . '.log' : $filename;


        //生成路径字串
        $path = $this->_createPath ( $this->_filepath, $this->_filename );
//        die($path);
        //判断是否存在该文件
        if (! $this->_isExist ( $path )) { //不存在
            //没有路径的话，默认为当前目录
            if (! empty ( $this->_filepath )) {
                //创建目录
                if (! $this->_createDir ( $this->_filepath )) { //创建目录不成功的处理
                    die ( "创建目录失败!" );
                }
            }
            //创建文件
            if (! $this->_createLogFile ( $path )) { //创建文件不成功的处理
                if(!mkdir($path, true)){
                    die ( "创建文件失败!" );
                }
            }
        }

        //生成路径字串
        $path = $this->_createPath ( $this->_filepath, $this->_filename );
        //打开文件
        $this->_filehandle = fopen ( $path, "a+" );
    }

    /**
     *作用:写入记录
     *输入:要写入的记录
     *输出:无
     */
    public static function setLog($log) {
        //传入的数组记录
        $str = "";
        if (is_array ( $log )) {
            foreach ( $log as $k => $v ) {
                $str .= $k . " : " . $v . "\r\n";
            }
        } else {
            $str = $log . "\r\n";
        }

        $log_path = LOG_PATH.date('Y').'/'.date('m').'/'.date('d');
        
        if(self::_createDir($log_path)){
            file_put_contents($log_path."/log.txt",$str."\r\n",FILE_APPEND);
        }else{
            die ( "写入日志失败" );
        }

    }

    /**
     *作用:判断文件是否存在
     *输入:文件的路径,要写入的文件名
     *输出:true | false
     */
    private static function _isExist($path) {
        return file_exists ( $path );
    }

    /**
     *作用:创建目录(引用别人超强的代码-_-;;)
     *输入:要创建的目录
     *输出:true | false
     */
    private static function _createDir($dir) {
        return is_dir ( $dir ) or (self::_createDir ( dirname ( $dir ) ) and mkdir ( $dir, 0777 ));
    }

    /**
     *作用:创建日志文件
     *输入:要创建的目录
     *输出:true | false
     */
    private static function _createLogFile($path) {
        $handle = fopen ( $path, "w" ); //创建文件
        fclose ( $handle );
        return self::_isExist ( $path );
    }

    /**
     *作用:构建路径
     *输入:文件的路径,要写入的文件名
     *输出:构建好的路径字串
     */
    private static function _createPath($dir, $filename) {
        if (empty ( $dir )) {
            return $filename;
        } else {
            return $dir . "/" . $filename;
        }
    }

    /**
     *功能: 析构函数，释放文件句柄
     *输入: 无
     *输出: 无
     */
    function __destruct() {
        //关闭文件
        fclose ( self::_filehandle );
    }
}
?>