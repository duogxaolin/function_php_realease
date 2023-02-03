<?php
error_reporting(1);
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
$connect = array( //connect database
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'database'
);
class System_Core
{
    
    public function connect_db()
    {
        global $connect;
        $conn = mysqli_connect($connect['hostname'], $connect['username'], $connect['password'], $connect['database']);
        mysqli_select_db($conn, $connect['database']) or die("Lỗi kết nối db");
        $conn->set_charset("utf8");
        return $conn;
    }
    
    public function __construct()
    {
        $this->connect_db();
    }
    public function home_url()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domain   = $_SERVER['HTTP_HOST'];
        return $protocol . $domain;
    }
    
    public function home_uri()
    {
        $domain = $_SERVER['REQUEST_URI'];
        return $domain;
    }
    public function to_slug($str)
    {
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }
    function query($sql)
    {
        $row = $this->connect_db()->query($sql);
        return $row;
    }
    function cong($table, $data, $sotien, $where)
    {
        $row = $this->connect_db()->query("UPDATE `$table` SET `$data` = `$data` + '$sotien' WHERE $where ");
        return $row;
    }
    function tru($table, $data, $sotien, $where)
    {
        $row = $this->connect_db()->query("UPDATE `$table` SET `$data` = `$data` - '$sotien' WHERE $where ");
        return $row;
    }
    function insert($table, $data)
    {
        $field_list = '';
        $value_list = '';
        foreach ($data as $key => $value)
        {
            $field_list .= ",$key";
            $value_list .= ",'".mysqli_real_escape_string($this->connect_db(), $value)."'";
        }
        $sql = 'INSERT INTO '.$table. '('.trim($field_list, ',').') VALUES ('.trim($value_list, ',').')';
 
        return mysqli_query($this->connect_db(), $sql);
    }
    function update($table, $data, $where)
    {
        $sql = '';
        foreach ($data as $key => $value)
        {
            $sql .= "$key = '".mysqli_real_escape_string($this->connect_db(), $value)."',";
        }
        $sql = 'UPDATE '.$table. ' SET '.trim($sql, ',').' WHERE '.$where;
        return mysqli_query($this->connect_db(), $sql);
    }
    function remove($table, $where)
    {
        $sql = "DELETE FROM $table WHERE $where";
        return mysqli_query($this->connect_db(), $sql);
    }
    function get_list($sql)
    {
        $result = mysqli_query($this->connect_db(), $sql);
        if (!$result)
        {
            die ('Lỗi? Help DuogXaoLin');
        }
        $return = array();
        while ($row = mysqli_fetch_assoc($result))
        {
            $return[] = $row;
        }
        mysqli_free_result($result);
        return $return;
    }
    function get_row($sql)
    {
        $result = mysqli_query($this->connect_db(), $sql);
        if (!$result)
        {
            die ('Lỗi? Help DuogXaoLin');
        }
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        if ($row)
        {
            return $row;
        }
        return false;
    }
    function num_rows($sql)
    {
        $result = mysqli_query($this->connect_db(), $sql);
        if (!$result)
        {
            die ('Lỗi? Help DuogXaoLin');
        }
        $row = mysqli_num_rows($result);
        mysqli_free_result($result);
        if ($row)
        {
            return $row;
        }
        return false;
    }
}
function format_date($time){
    return date("H:i:s d/m/Y", $time);
}
function gettime()
{
    return date('Y/m/d H:i:s', time());
}
function check_string($data)
{
    return trim(htmlspecialchars(addslashes($data)));
    //return str_replace(array('<',"'",'>','?','/',"\\",'--','eval(','<php'),array('','','','','','','','',''),htmlspecialchars(addslashes(strip_tags($data))));
}
function format_cash($price)
{
    return str_replace(",", ".", number_format($price));
}
