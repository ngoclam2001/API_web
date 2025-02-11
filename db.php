<?php
define('DEFAULT_TIME_FORMAT', 'Y-m-d H:i:s');
$servername = "localhost";
$username = "admin";
$password = "123456";
$dbname = "";

global $connect_db, $cnn;
$connect_db = false; $cnn = null;

class dbBasic
{
    function connect()
    {
        global $connect_db;
        if (!$connect_db) {
            $connect_db = true;
            global $cnn, $servername, $username, $password, $dbname;
            if (!$cnn = mysqli_connect($servername, $username, $password)) die('Error: Not connect to database!');
            if (!mysqli_select_db($cnn, $dbname)) die('Error: Not open table ' . $dbname . '!');
            mysqli_query($cnn, 'set names utf8'); 
        }
    }
    function insertData($table, $data) {
        $this->connect();
        global $cnn;
        
        // Check if data is multidimensional array
        if (is_array(reset($data))) {
            $fields = implode(',', array_keys(reset($data)));
            $values = [];
            foreach ($data as $row) {
                $values[] = "('" . implode("','", array_values($row)) . "')";
            }
            $values = implode(',', $values);
        } else {
            $fields = implode(',', array_keys($data));
            $values = "('" . implode("','", array_values($data)) . "')";
        }
        
        $sql = "INSERT INTO $table ($fields) VALUES $values";
        return mysqli_query($cnn, $sql);
    }

    function updateData($table, $data, $where) {
        $this->connect();
        global $cnn;
        $sets = array();
        foreach($data as $key => $value) {
            $sets[] = "$key='$value'";
        }
        $sql = "UPDATE $table SET " . implode(',', $sets) . " WHERE $where";
        return mysqli_query($cnn, $sql);
    }

    function deleteData($table, $where) {
        $this->connect();
        global $cnn;
        $sql = "DELETE FROM $table WHERE $where";
        return mysqli_query($cnn, $sql);
    }

    function selectData($table, $where = '1', $fields = '*') {
        $this->connect();
        global $cnn;
        $sql = "SELECT $fields FROM $table WHERE $where";
        $result = mysqli_query($cnn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    function count($table, $where = '1') {
        $this->connect();
        global $cnn;
        $sql = "SELECT COUNT(*) as count FROM $table WHERE $where";
        $result = mysqli_query($cnn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }

    function getOne($table, $id, $fields = '*') {
        $this->connect();
        global $cnn;
        $sql = "SELECT $fields FROM $table WHERE id = $id";
        $result = mysqli_query($cnn, $sql);
        return mysqli_fetch_assoc($result);
    }

    function getAll($table, $where = '1', $fields = '*', $orderBy = 'id DESC') {
        $this->connect(); 
        global $cnn;
        $sql = "SELECT $fields FROM $table WHERE $where ORDER BY $orderBy";
        $result = mysqli_query($cnn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    function getMaxId($table) {
        $this->connect();
        global $cnn;
        $sql = "SELECT MAX(id) as max_id FROM $table";
        $result = mysqli_query($cnn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['max_id'];
    }
}
#
class core
{
    function readCSV($file) {
        $list_city = [];
        if (($handle = fopen($file, "r")) !== FALSE) {
            fgetcsv($handle);
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (!empty($data[0]) && !empty($data[1])) {
                    $list_city[$data[0]] = $data[1];
                }
            }
            fclose($handle);
        }
        return $list_city;
    }
    function header($link)
    {
        Header('HTTP/1.1 301 Moved Permanently');
        header('location: ' . $link);
        global $connect_db, $cnn;
        if ($connect_db) {
            if (!mysqli_close($cnn)) die('Error close SQL');
        }
        $cnn->close();
        exit();
        die();
    }
    function getIP()
    {
        return $_SERVER['REMOTE_ADDR'];
    }
    function toString($int)
    {
        return number_format($int, 0, ',', '.');
    }
    function toSlug($doc)
    {
        $str = addslashes(html_entity_decode($doc));
        $str = str_replace('|', '', $str);
        $str = $this->toNormal($str);
        $str = preg_replace('/[^a-zA-Z0-9\/_|+ -]/', '', $str);
        $str = preg_replace('/( )/', '-', $str);
        $str = str_replace('--', '-', $str);
        $str = str_replace('/', '-', $str);
        $str = str_replace('\/', '', $str);
        $str = str_replace('+', '', $str);
        $str = strtolower($str);
        $str = stripslashes($str);
        return trim($str, '-');
    }
    function toNormal($str)
    {
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/', 'A', $str);
        $str = preg_replace('/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/', 'E', $str);
        $str = preg_replace('/(Ì|Í|Ị|Ỉ|Ĩ)/', 'I', $str);
        $str = preg_replace('/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/', 'O', $str);
        $str = preg_replace('/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/', 'U', $str);
        $str = preg_replace('/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/', 'Y', $str);
        $str = preg_replace('/(Đ)/', 'D', $str);
        return $str;
    }
    function toNumber($str)
    {
        $str = preg_replace('/[^0-9\/_|+ -]/', '', $str);
        $str = str_replace('_', '', $str);
        $str = str_replace(' ', '', $str);
        return intval($str);
    }
    function toBytes($bytes, $precision = 2, $_mb = 1024)
    {
        $base = log($bytes) / log($_mb);
        $suffixes = array('', 'k', 'M', 'G', 'T');
        return round(pow($_mb, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }
    function get_limit_content($string, $length = 255)
    {
        $string = strip_tags($string);
        if (strlen($string) > 0) {
            $arr = explode(' ', $string);
            $return = '';
            if (count($arr) > 0) {
                $count = 0;
                if ($arr) foreach ($arr as $str) {
                    $count += strlen($str);
                    if ($count > $length) {
                        $return .= '...';
                        break;
                    }
                    $return .= ' ' . $str;
                }
            }
            return $return;
        }
    }
    function formatUrlsInText($text)
    {
        $reg_exUrl = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
        preg_match_all($reg_exUrl, $text, $matches);
        $usedPatterns = array();
        foreach ($matches[0] as $pattern) {
            if (!array_key_exists($pattern, $usedPatterns)) {
                $usedPatterns[$pattern] = true;
                $text = str_replace($pattern, '<a href="' . $pattern . '" rel="nofollow" target="_blank">' . $pattern . '</a> ', $text);
            }
        }
        return $text;
    }
    function time_ago($tm, $rcs = 0)
    {
        $cur_tm = time();
        $dif = $cur_tm - $tm;
        if ($dif <= 0) return 'gần đây';
        $pds = array('giây', 'phút', 'giờ', 'ngày', 'tuần', 'tháng', 'năm', 'thập kỉ');
        $lngh = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);
        for ($v = sizeof($lngh) - 1; ($v >= 0) && (($no = $dif / $lngh[$v]) <= 1); $v--);
        if ($v < 0) $v = 0;
        $_tm = $cur_tm - ($dif % $lngh[$v]);
        $no = floor($no);
        if ($no <> 1) $pds[$v] .= '';
        $x = sprintf("%d %s ", $no, $pds[$v]);
        if (($rcs == 1) && ($v >= 1) && (($cur_tm - $_tm) > 0)) $x .= $this->time_ago($_tm);
        return $x . ' trước';
    }
    function time_str($time)
    {
        return date(DEFAULT_TIME_FORMAT, $time);
    }
    function db2datepicker($str, $has_time = true)
    {
        if (!$str || $str == '1970-01-01 00:00:00' || $str == '0000-00-00 00:00:00' || $str == '0000-00-00' || $str == '1970-01-01') return false;
        if ($has_time) $res = date('d/m/Y H:i:s', strtotime($str));
        else $res = date('d/m/Y', strtotime($str));
        return $res;
    }
    function datepicker2db($str, $has_time = true)
    {
        if (!$str || trim($str) == '') return '';
        if ($has_time) {
            $arr = explode(' ', trim($str));
            $date = explode('/', $arr[0]);
            return $date[2] . '-' . $date[1] . '-' . $date[0] . ' ' . $arr[1];
        } else {
            $date = explode('/', trim($str));
            return $date[2] . '-' . $date[1] . '-' . $date[0];
        }
    }
    function pathToArray($path)
    {
        if (!$path) return array();
        $path = trim($path, '|');
        if (!$path) return array();
        $path = str_replace('||', '|', $path);
        if (!$path) return array();
        return explode('|', $path);
    }
    function arrayToPath($arr)
    {
        if (is_array($arr)) return '|' . implode('|', $arr) . '|';
        else return '';
    }
    function getWidthImage($url)
    {
        $res = getimagesize($url);
        return $res[0];
    }
    function fill_chunck($array, $parts)
    {
        $t = 0;
        $result = array_fill(0, $parts - 1, array());
        $max = ceil(count($array) / $parts);
        foreach ($array as $v) {
            count($result[$t]) >= $max and $t++;
            $result[$t][] = $v;
        }
        return $result;
    }
    function uploadFile($_file, $allowed = null, $filename = null)
    {
        $extension = strtolower(pathinfo($_FILES[$_file]['name'], PATHINFO_EXTENSION));
        if ($allowed && !in_array($extension, $allowed)) return false;
        if (!$filename) $filename = date('ymdHis');
        $f = 'uploads/' . $filename . '.' . $extension;
        if (move_uploaded_file($_FILES[$_file]['tmp_name'], $f)) return $filename . '.' . $extension;
        else return false;
    }
    function downloadFile($url, $allowed = null, $k = '')
    {
        if (stripos($url, '?')) $url = array_shift(explode('?', $url));
        $extension = strtolower(end(explode('.', $url)));
        $filename = $this->toSlug(basename(rtrim($url, '.' . $extension))) . '-' . date('His') . $k;
        if ($allowed && !in_array($extension, $allowed)) return false;
        $data = @file_get_contents($this->encodeURI($url));
        if ($data) {
            $upload = file_put_contents('uploads/' . $filename . '.' . $extension, $data);
            if ($upload) return $filename . '.' . $extension;
        } else return false;
    }
    function encodeURI($url)
    {
        $unescaped = array('%2D' => '-', '%5F' => '_', '%2E' => '.', '%21' => '!', '%7E' => '~', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
        $reserved = array('%3B' => ';', '%2C' => ',', '%2F' => '/', '%3F' => '?', '%3A' => ':', '%40' => '@', '%26' => '&', '%3D' => '=', '%2B' => '+', '%24' => '$');
        $score = array('%23' => '#');
        return strtr(rawurlencode($url), array_merge($reserved, $unescaped, $score));
    }
    function removeArrayByValue($array, $value)
    {
        if (!$value) return $array;
        if (!$array) return array();
        if (($key = array_search($value, $array)) !== false) unset($array[$key]);
        return $array;
    }
    function removeArrayByArray($array_input, $array_search)
    {
        if ($array_search) foreach ($array_search as $v) $array_input = $this->removeArrayByValue($array_input, $v);
        return $array_input;
    }
}
$core = new core;

if ($connect_db && $cnn) {
    $cnn->close();
}
exit();
