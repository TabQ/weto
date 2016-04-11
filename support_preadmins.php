<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/3/23
 * Time: 8:07
 */
include "config.php";
include_once "functions.php";

session_start();

if(!isset($_SESSION['username'])) {
    log_output(__FILE__, __LINE__, "未登陆访问页面：".$_SERVER['REQUEST_URI']);
    $json_arr = array('code' => -1, 'msg' => '请登陆');
    echo json_encode($json_arr);
    exit;
}

if(!isset($_GET['paid'])) {
    log_output(__FILE__, __LINE__, $_SESSION['username']."访问非法页面：" . $_SERVER['REQUEST_URI']);
    $json_arr = array('code' => -2, 'msg' => '该页面不存在');
    echo json_encode($json_arr);
    exit;
}
$paid = $_GET['paid'];

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_output(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error");
    $mysqli->close();
    $json_arr = array('code' => -3, 'msg' => '数据库连接失败');
    echo json_encode($json_arr);
    exit;
}

// 判断paid是否有效
$query = "select count(*) as count from weto_preadmins where id=$paid and overdue=0 limit 1";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    $result->free();
    if(empty($row['count'])) {
        log_output(__FILE__, __LINE__, $_SESSION['username']."访问非法页面：".$_SERVER['REQUEST_URI']);
        $mysqli->close();
        $json_arr = array('code' => -4, 'msg' => '该申请者不存在或已过期');
        echo json_encode($json_arr);
        exit;
    }
} else {
    log_output(__FILE__, __LINE__, "查询错误：$query");
    $mysqli->close();
    $json_arr = array('code' => -5, 'msg' => '操作失败，请重试');
    echo json_encode($json_arr);
    exit;
}

$uid = $_SESSION['uid'];
// 查询该用户是否已支持过该paid
$query = "select count(*) as count from weto_support_preadmins where paid=$paid and uid=$uid limit 1";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    $result->free();
    if(!is_null($row) && $row['count'] > 0) {
        $mysqli->close();
        $json_arr = array('code' => -6, 'msg' => '对不起，您已支持过该申请人');
        echo json_encode($json_arr);
        exit;
    }
} else {
    log_output(__FILE__, __LINE__, "查询错误：$query");
    $mysqli->close();
    $json_arr = array('code' => -5, 'msg' => '操作失败，请重试');
    echo json_encode($json_arr);
    exit;
}

// 增加支持者
$now = time();
$username = $_SESSION['username'];
$query = "insert into weto_support_preadmins(paid, uid, username, time) values($paid, $uid, '$username', $now)";
if($result = $mysqli->query($query)) {
    // 查询该申请人支持者是否已达30人，如果达到同时更新weto_preadmins和weto_forums
    $query = "select count(*) as count from weto_support_preadmins where paid=$paid limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
        if(!is_null($row) && $row['count'] >= MAX_SUPPORT_PREADMINS) {
            // 更新weto_preadmins
            $query = "update weto_preadmins set overdue=1 where id=$paid";
            if($result = $mysqli->query($query)) {
                // 版主申请成功
                $fid = $_POST['fid'];
                $adminlist = $_POST['adminlist'];
                $preadmin = $_POST['preadmin'];
                $adminlist .= $preadmin . ',';
                $query = "update weto_forums set adminlist = '$adminlist' where id=$fid and closed=0";
                if($result = $mysqli->query($query)) {
                    $json_arr = array('code' => 2, 'msg' => '成功，申请人正式成为版主');
                    echo json_encode($json_arr);
                    $mysqli->close();
                    exit;
                } else {
                    log_output(__FILE__, __LINE__, "更新失败：$query");
                    $mysqli->close();
                    $json_arr = array('code' => -7, 'msg' => '操作失败，请重试');
                    echo json_encode($json_arr);
                    exit;
                }
            } else {
                log_output(__FILE__, __LINE__, "更新失败：$query");
                $mysqli->close();
                $json_arr = array('code' => -7, 'msg' => '操作失败，请重试');
                echo json_encode($json_arr);
                exit;
            }
        } elseif(!is_null($row) && $row['count'] < MAX_SUPPORT_PREADMINS) {
            $mysqli->close();
            $json_arr = array('code' => 1, 'msg' => '成功');
            echo json_encode($json_arr);
            exit;
        }
    } else {
        log_output(__FILE__, __LINE__, "查询错误：$query");
        $mysqli->close();
        $json_arr = array('code' => -5, 'msg' => '操作失败，请重试');
        echo json_encode($json_arr);
        exit;
    }
} else {
    log_output(__FILE__, __LINE__, "插入失败：$query");
    $mysqli->close();
    $json_arr = array('code' => -8, 'msg' => '操作失败，请重试');
    echo json_encode($json_arr);
    exit;
}