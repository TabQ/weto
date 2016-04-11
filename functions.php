<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/1/17
 * Time: 14:26
 */
include_once "config.php";

function log_output($file, $line, $msg) {
    $dir = SITE_ROOT . '/logs/';
    if(!is_dir($dir)) {
        mkdir($dir, 0664, true);
    }

    $file_path = $dir . date('Y-m-d') . '.log';
    $handle = fopen($file_path, 'a');
    $msg = date('H:i:s') . "  File:" . $file . "  Line:" . $line . "  Message:$msg" . "\r\n";
    fputs($handle, $msg);
    fclose($handle);
}

function jump($page) {
    header('Location: /'. $page);
}

function jump_($page) {
    echo "<script language='javascript' type='text/javascript'>window.location.href='".$page."'</script>";
}

function log_and_jump($file, $line, $msg, $page) {
    log_output($file, $line, $msg);
    jump($page);
}

function can_access($mysqli) {
    $query = "select forbidden, forbiddentime from weto_users where username = '" . $_SESSION['username'] . "' limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
        if($row['forbidden'] == 1) {
            if($row['forbiddentime'] + FORBIDDEN_TIME >= time()) {
                jump("errorpages/noaccess.html");
                return 0;
            } else {
                $query = "update weto_users set forbidden = 0, forbiddentime = 0 where username = '" . $_SESSION['username'] . "'";
                $result = $mysqli->query($result);
                if($result === false) {
                    log_output(__FILE__, __LINE__, "更新weto_users表出错：$query");
                }
                return 1;
            }
        }
        return 1;
    } else {
        log_and_jump(__FILE__, __LINE__, "查询weto_users表出错：$query", "errorpages/opterror.html");
        return -1;
    }
}

function get_page_para($query, $mysqli, &$page_arr) {
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
        $page_arr['total_page'] = $row['count'] == 0 ? 1 : intval(ceil($row['count'] / ITEMS_PER_PAGE));
    } else {
        return false;
    }

    $page_arr['cur_page'] = isset($_GET['p']) ? $_GET['p'] : 1;
    $page_arr['cur_page'] = $page_arr['cur_page'] > $page_arr['total_page'] ? $page_arr['total_page'] : $page_arr['cur_page'];

    return true;
}

function addslashes_all(&$var) {
    if(!isset($var)) {
        return;
    } else {
        if(is_array($var)) {
            foreach($var as &$value) {
                addslashes_all($value);
            }
        } else {
            if(!get_magic_quotes_gpc()) {
                $var = addslashes($var);
            }
        }
    }
}

function trim_addslashes(&$var) {
    if(!isset($var)) {
        return;
    } else {
        if(is_array($var)) {
            foreach($var as &$value) {
                trim_addslashes($value);
            }
        } else {
            $var = trim($var);
            if(!get_magic_quotes_gpc()) {
                $var = addslashes($var);
            }
        }
    }
}

function ext($file_name) {
    return strtolower(substr(strrchr($file_name, '.'), 1));
}

function scale_image($src_file, $des_file, $forced_width = 192, $forced_height = 144, $r = 0xF2, $g = 0xF2, $b = 0xF2) {
    $img_comp = 10;

    $des_ext = ext($des_file);
    if(!in_array($des_ext, array('gif', 'jpg', 'png'))) {
        return false;
    }

    $img_comp = 100 - $img_comp;
    $img_info = getimagesize($src_file);
    $src_width = $img_info[0];
    $src_height = $img_info[1];
    if($src_width == 0 || $src_height == 0) {
        return false;
    }

    $src_scale = $src_width / $src_height;
    $des_scale = $forced_width / $forced_height;

    if(!function_exists('imagecreatefromjpeg')) {
        copy($src_file, $des_file);
        return true;
    }

    // 按规定比例缩略
    if($src_width <= $forced_width && $src_height <= $forced_height) {
        $des_width = $src_width;
        $des_height = $src_height;
    } elseif($src_scale >= $des_scale) {
        $des_width = ($src_width >= $forced_width) ? $forced_width : $src_width;
        $des_height = $des_width / $src_scale;
    } else {
        $des_height = ($src_height >= $forced_height) ? $forced_height : $src_height;
        $des_width = $des_height * $src_scale;
    }

    switch($img_info['mime']) {
        case 'image/jpeg': $img_src = imagecreatefromjpeg($src_file); break;
        case 'image/gif': $img_src = imagecreatefromgif($src_file); break;
        case 'image/png': $img_src = imagecreatefrompng($src_file); break;
        default: return false;
    }
    if(!$img_src) return false;

    $img_dst = imagecreatetruecolor($forced_width, $forced_height);
    $img_color = imagecolorallocate($img_dst, $r, $g, $b);
    imagefill($img_dst, 0, 0, $img_color);
    $des_x = intval(round(($forced_width - $des_width) / 2));
    $des_y = intval(round(($forced_height - $des_height) / 2));
    imagecopyresampled($img_dst, $img_src, $des_x, $des_y, 0, 0, $des_width, $des_height, $src_width, $src_height);

    switch($des_ext) {
        case 'jpg': imagejpeg($img_dst, $des_file, $img_comp); break;
        case 'gif': imagegif($img_dst, $des_file); break;
        case 'png': imagepng($img_dst, $des_file, version_compare(PHP_VERSION, '5.1.2') == 1 ? 7 : 70); break;
        default: return false;
    }

    imagedestroy($img_dst);

    return true;
}

function check_file_type($filename) {
    if(!file_exists($filename)) return 'file does not exist';

    $file = fopen($filename, "rb");
    $bin = fread($file, 2); //只读2字节
    fclose($file);
    $strInfo = @unpack("c2chars", $bin);
    $typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
    switch ($typeCode)
    {
        case 7790:
            $fileType = 'exe';
            break;
        case 7784:
            $fileType = 'midi';
            break;
        case 8297:
            $fileType = 'rar';
            break;
        case 255216:
            $fileType = 'jpg';
            break;
        case 7173:
            $fileType = 'gif';
            break;
        case 6677:
            $fileType = 'bmp';
            break;
        case 13780:
            $fileType = 'png';
            break;
        default:
            $fileType = 'unknown'.$typeCode;
    }
//Fix
    if ($strInfo['chars1']=='-1' && $strInfo['chars2']=='-40' ) {
        return 'jpg';
    }
    if ($strInfo['chars1']=='-119' && $strInfo['chars2']=='80' ) {
        return 'png';
    }
    return $fileType;
}

function get_seo($table, $id, $col1 = 'seotitle', $col2 = 'keywords', $col3 = 'description') {
    global $dbArr;

    $mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
    if($mysqli->connect_errno) {
        log_output(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error");
        $mysqli->close();
        return false;
    }

    $query = "select $col1, $col2, $col3 from $table where id = $id limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
    } else {
        $row = false;
        log_output(__FILE__, __LINE__, "查询{$table}出错：$query");
    }

    $mysqli->close();

    return $row;
}

function get_veg_price() {
    global $dbArr;

    $mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
    if($mysqli->connect_errno) {
        log_output(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error");
        $mysqli->close();
        return false;
    }

    // 先查询最后一次发布的时间
    $query = "select time from weto_goods_detail order by time desc limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
        if(!empty($row['time'])) {
            $rec_time = $row['time'];
        }
    } else {
        log_output(__FILE__, __LINE__, "查询错误：$query");
        $mysqli->close();
        return false;
    }
    // 查询最近一次发布的十条信息
    $rec_vegs = array();
    if(!empty($rec_time)) {
        $query = "select wn.id, wn.name as nname, wd.price, wu.name as uname from weto_goods_detail wd, weto_goods_name wn, weto_goods_unit wu, weto_goods_cate wc
where wc.name='农副产品' and wn.cid=wc.id and wn.id=wd.nid and wd.unid=wu.id and wd.time=$rec_time order by wn.id limit 10";
        if($result = $mysqli->query($query)) {
            while($row = $result->fetch_assoc()) {
                $rec_vegs[] = $row;
            }
            $result->free();
        } else {
            log_output(__FILE__, __LINE__, "查询错误：$query");
            $mysqli->close();
            return false;
        }
    }

    $mysqli->close();

    return $rec_vegs;
}