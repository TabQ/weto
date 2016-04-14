<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/3/18
 * Time: 14:12
 */
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();
$_SESSION['jump_url'] = $_SERVER['REQUEST_URI'];    // 记录跳转地址

if(!isset($_GET['bid'])) {
    log_and_jump(__FILE__, __LINE__, "访问非法页面：" . $_SERVER['REQUEST_URI'], "errorpages/404.html");
    exit;
}
$bid = $_GET['bid'];

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
    $mysqli->close();
    exit;
}
// 判断bid是否有效
$query = "select count(*) as count from weto_blocks where id = $bid and closed = 0";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    if($row['count'] == 0) {
        log_and_jump(__FILE__, __LINE__, "访问非法页面：" . $_SERVER['REQUEST_URI'], "errorpages/404.html");
        $result->free();
        $mysqli->close();
        exit;
    }
    $result->free();
}

// 获取已开通版面
$forums_arr = array();
$query = "select id, forumname from weto_forums where bid = $bid and closed = 0 order by posts desc, rank asc";
if($result = $mysqli->query($query)) {
    while($row = $result->fetch_assoc()) {
        $forums_arr[] = $row;
    }
    $result->free();
} else {
    log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
    $mysqli->close();
    exit;
}
// 关闭已过期申请中版面
$now = time();
$query = "update weto_preforums set overdue=1 where bid=$bid and time+".PREFORUM_OVERDUE." <= $now";
if(!$result = $mysqli->query($query)) {
    log_and_jump(__FILE__, __LINE__, "更新失败：$query", "errorpages/opterror.html");
    $mysqli->close();
    exit;
}
// 获取申请中版面
$preforums_arr = array();
$query = "select id, name, proposer from weto_preforums where bid=$bid and overdue=0 order by name, time desc";
if($result = $mysqli->query($query)) {
    while($row = $result->fetch_assoc()) {
        $preforums_arr[] = $row;
    }
    $result->free();
} else {
    log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
    $mysqli->close();
    exit;
}
// 获取申请中版面支持者
foreach($preforums_arr as &$value) {
    if(!empty($value)) {
        $query = "select username, time from weto_supporters where pfid=$value[id] order by time";
        if($result = $mysqli->query($query)) {
            while($row = $result->fetch_assoc()) {
                $value['supporters'][] = $row;
            }
            $result->free();
        } else {
            log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!isset($_SESSION['username'])) {
        $mysqli->close();
        log_output(__FILE__, __LINE__, "未登陆访问非法页面：".$_SERVER['REQUEST_URI']);
        $json_arr = array('code' => -1, 'msg' => '未登陆');
        echo json_encode($json_arr);
        exit;
    }

    if(can_access($mysqli) != 1) {
        $mysqli->close();
        $json_arr = array('code' => -2, 'msg' => '对不起，您现在不能申请版面');
        exit;
    }

    $name = trim($_POST['name']);
    if(preg_match('/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]{1,10}$/u', $name) != 1) {
        $mysqli->close();
        $json_arr = array('code' => -3, 'msg' => '最多10个中文字符、英文字符或数字');
        echo json_encode($json_arr);
        exit;
    } else {
        // 先查询已开通版面
        $query = "select count(*) as count from weto_forums where forumname='$name' and closed=0 limit 1";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            if($row['count'] == 1) {
                $mysqli->close();
                $json_arr = array('code' => -4, 'msg' => '该版面已开通');
                echo json_encode($json_arr);
                exit;
            }
        } else {
            log_output(__FILE__, __LINE__, "查询错误：$query");
            $mysqli->close();
            $json_arr = array('code' => -5, 'msg' => '操作失败请重试');
            echo json_encode($json_arr);
            exit;
        }
        // 然后查询该版面是否已在其他block申请中
        $query = "select count(*) as count from weto_preforums where name='$name' and bid != $bid and overdue=0 limit 1";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            if($row['count'] != 0) {
                $mysqli->close();
                $json_arr = array('code' => 0, 'msg' => '对不起，该版面已在其他版块申请中');
                echo json_encode($json_arr);
                exit;
            }
        } else {
            log_output(__FILE__, __LINE__, "查询错误：$query");
            $mysqli->close();
            $json_arr = array('code' => -5, 'msg' => '操作失败请重试');
            echo json_encode($json_arr);
            exit;
        }
        // 其次查询该用户是否已申请过该版面
        $query = "select count(*) as count from weto_preforums where name='$name' and proposer='$_SESSION[username]' and overdue=0";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            if($row['count'] != 0) {
                $mysqli->close();
                $json_arr = array('code' => -7, 'msg' => '对不起，该版面您正在申请中');
                echo json_encode($json_arr);
                exit;
            }
        } else {
            log_output(__FILE__, __LINE__, "查询错误：$query");
            $mysqli->close();
            $json_arr = array('code' => -5, 'msg' => '操作失败请重试');
            echo json_encode($json_arr);
            exit;
        }

        $now = time();
        !get_magic_quotes_gpc() && $name = addslashes($name);
        $query = "insert into weto_preforums(bid, name, proposer, time) values($bid, '$name', '$_SESSION[username]', $now)";
        if($result = $mysqli->query($query)) {
            unset($result);
            $query = "insert into weto_supporters(pfid, uid, username, time) values($mysqli->insert_id, $_SESSION[uid], '$_SESSION[username]', $now)";
            if($result = $mysqli->query($query)) {
                $mysqli->close();
                $json_arr = array('code' => 1, 'msg' => '申请成功');
                echo json_encode($json_arr);
                exit;
            } else {
                log_output(__FILE__, __LINE__, "插入失败：$query");
                $mysqli->close();
                $json_arr = array('code' => -6, 'msg' => '操作失败请重试');
                echo json_encode($json_arr);
                exit;
            }

        } else {
            log_output(__FILE__, __LINE__, "插入失败：$query");
            $mysqli->close();
            $json_arr = array('code' => -6, 'msg' => '操作失败请重试');
            echo json_encode($json_arr);
            exit;
        }
    }
}

$tmpObj = new Template();

$tmpObj->forums_arr = $forums_arr;
$tmpObj->preforums_arr = $preforums_arr;

$db_seo_arr = get_seo('weto_blocks', $bid);
$tmpObj->seo_arr = $db_seo_arr === false ? $meta_arr : $db_seo_arr;

$tmpObj->rec_vegs = get_veg_price();

$tmpObj->display('blocks_ext.tpl');