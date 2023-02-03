<?php
error_reporting(1);
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
$domain = 'localhost';
$connect = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'likesub1s'
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
    public function home_urls()
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

    public function get_tag($slug)
    {
        $result = mysqli_query($this->connect_db(), "SELECT * FROM `tag` WHERE `slug` ='$slug'");
        $row    = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $row;
        
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
    public function get_slug($slug)
    {
        $result = mysqli_query($this->connect_db(), "SELECT * FROM `product` WHERE `slug` ='$slug'");
        $row    = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $row;
        
    }
    function site($data,$domain)
    {
        $this->connect_db();
        $row = $this->connect_db()->query("SELECT * FROM `options` WHERE`domain`= '$domain' ")->fetch_array();
        return $row[$data];
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
    function update_value($table, $data, $where, $value1)
    {
        $sql = '';
        foreach ($data as $key => $value){
            $sql .= "$key = '".mysqli_real_escape_string($this->connect_db(), $value)."',";
        }
        $sql = 'UPDATE '.$table. ' SET '.trim($sql, ',').' WHERE '.$where.' LIMIT '.$value1;
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
    
     function format_phone_number($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        if (strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, strlen($phoneNumber) - 10);
            $areaCode    = substr($phoneNumber, -10, 3);
            $nextThree   = substr($phoneNumber, -7, 3);
            $lastFour    = substr($phoneNumber, -4, 4);
            
            $phoneNumber = '' . $countryCode . '' . $areaCode . '.' . $nextThree . '.' . $lastFour;
        } else if (strlen($phoneNumber) == 10) {
            $areaCode  = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour  = substr($phoneNumber, 6, 4);
            
            $phoneNumber = '' . $areaCode . '.' . $nextThree . '.' . $lastFour;
        } else if (strlen($phoneNumber) == 7) {
            $nextThree = substr($phoneNumber, 0, 3);
            $lastFour  = substr($phoneNumber, 3, 4);
            
            $phoneNumber = $nextThree . '-' . $lastFour;
        }
        
        return $phoneNumber;
    }
    


$sell = new System_Core;
$config = [
    'url'       => $base_url
];
if(isset($_SESSION['username']))
{ 
    $getUser = $sell->get_row(" SELECT * FROM users WHERE username = '".$_SESSION['username']."' ");
    if(!$getUser)
    {
        session_start();
        session_destroy();
        header('location: /');
    }
}
else
{
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
function curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    
    curl_close($ch);
    return $data;
}
function random($string, $int)
{  
    return substr(str_shuffle($string), 0, $int);
}
function pheptru($int1, $int2)
{
    return $int1 - $int2;
}
function phepcong($int1, $int2)
{
    return $int1 + $int2;
}
function phepnhan($int1, $int2)
{
    return $int1 * $int2;
}
function phepchia($int1, $int2)
{
    return $int1 / $int2;
}
function check_img($img)
{
    $filename = $_FILES[$img]['name'];
    $ext = explode(".", $filename);
    $ext = end($ext);
    $valid_ext = array("png","jpeg","jpg","PNG","JPEG","JPG","gif","GIF");
    if(in_array($ext, $valid_ext))
    {
        return true;
    }
}
function msg_error3($text)
{
    return '<div class="alert alert-danger alert-dismissible error-messages">
    '.$text.'</div>';
}
function msg_success3($text)
{
    return '<div class="alert alert-success alert-dismissible error-messages">
    '.$text.'</div>';
}


function msg_success2($text)
{
    return die('<div class="alert alert-success alert-dismissible error-messages">
    '.$text.'</div>');
}
function msg_error2($text)
{
    return die('<div class="alert alert-danger alert-dismissible error-messages">
    '.$text.'</div>');
}
function msg_warning2($text)
{
    return die('<div class="alert alert-warning alert-dismissible error-messages">
    '.$text.'</div>');
}
function msg_success($text, $url, $time)
{
    return die('<div class="alert alert-success alert-dismissible error-messages">
    '.$text.'</div><script type="text/javascript">setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function msg_error($text, $url, $time)
{
    return die('<div class="alert alert-danger alert-dismissible error-messages">
    '.$text.'</div><script type="text/javascript">setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function msg_warning($text, $url, $time)
{
    return die('<div class="alert alert-warning alert-dismissible error-messages">
    '.$text.'</div><script type="text/javascript">setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function admin_msg_success($text, $url, $time)
{
    return die('<script type="text/javascript">Swal.fire("Thành Công", "'.$text.'","success");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function admin_msg_error($text, $url, $time)
{
    return die('<script type="text/javascript">Swal.fire("Thất Bại", "'.$text.'","error");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function admin_msg_warning($text, $url, $time)
{
    return die('<script type="text/javascript">Swal.fire("Thông Báo", "'.$text.'","warning");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function XoaDauCach($text)
{
    return trim(preg_replace('/\s+/',' ', $text));
}
function check_username($data)
{
    if (preg_match('/^[a-zA-Z0-9_-]{3,16}$/', $data, $matches))
    {
        return True;
    }
    else
    {
        return False;
    }
}
function check_email($data)
{
    if (preg_match('/^.+@.+$/', $data, $matches))
    {
        return True;
    }
    else
    {
        return False;
    }
}
function check_phone($data)
{
    if (preg_match('/^\+?(\d.*){3,}$/', $data, $matches))
    {
        return True;
    }
    else
    {
        return False;
    }
}
function check_url($url)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_HEADER, 1);
    curl_setopt($c, CURLOPT_NOBODY, 1);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_FRESH_CONNECT, 1);
    if(!curl_exec($c))
    {
        return false;
    }
    else
    {
        return true;
    }
}
function myip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))     
    {  
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];  
    }  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))    
    {  
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];  
    }  
    else  
    {  
        $ip_address = $_SERVER['REMOTE_ADDR'];  
    }
    return $ip_address;
}