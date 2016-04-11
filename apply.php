<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/3/21
 * Time: 17:57
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

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_output(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error");
    $mysqli->close();
    $json_arr = array('code' => -2, 'msg' => '数据库连接失败，请重试');
    echo json_encode($json_arr);
    exit;
}

$fid = $_POST['fid'];
$uid = $_SESSION['uid'];
$username = $_SESSION['username'];
// 查询该版面是否符合申请条件（版面存在且未关闭、所申请版面版主数目未达3人）
$query = "select adminlist from weto_forums where id=$fid and closed=0 limit 1";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    $result->free();
    if(is_null($row)) {
        $mysqli->close();
        $json_arr = array('code' => -3, 'msg' => '对不起，该版面不可申请');
        echo json_encode($json_arr);
        exit;
    } else {
        $admin_list = explode(',', trim($row['adminlist'], ','));
        $total = count($admin_list);
        // admin及system不计算在内
//        in_array('admin', $admin_list) && $total--;
//        in_array('system', $admin_list) && $total--;

        if($total >= MAX_ADMINS) {
            $mysqli->close();
            $json_arr = array('code' => -4, 'msg' => '对不起，该版面管理员已满');
            echo json_encode($json_arr);
            exit;
        }
    }
} else {
    log_output(__FILE__, __LINE__, "查询错误：$query");
    $mysqli->close();
    $json_arr = array('code' => -5, 'msg' => '操作失败请重试');
    echo json_encode($json_arr);
    exit;
}
// 查询该用户是否已正在申请中
$query = "select count(*) as count from weto_preadmins where fid=$fid and uid=$uid and overdue=0 limit 1";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    $result->free();
    if($row['count'] != 0) {
        $mysqli->close();
        $json_arr = array('code' => 0, 'msg' => '对不起，该版面您正在申请中');
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
// 通过
$now = time();
$query = "insert into weto_preadmins(fid, uid, username, time) values($fid, $uid, '$username', $now)";
if($result = $mysqli->query($query)) {
    unset($result);
    $query = "insert into weto_support_preadmins(paid, uid, username, time) values($mysqli->insert_id, $uid, '$username', $now)";
    if($result = $mysqli->query($query)) {
        $mysqli->close();
        $json_arr = array('code' => 1, 'msg' => '成功');
        echo json_encode($json_arr);
        exit;
    } else {

        log_output(__FILE__, __LINE__, "插入失败：$query");
        $mysqli->close();
        $json_arr = array('code' => -6, 'msg' => '添加支持者失败，请手动添加');
        echo json_encode($json_arr);
        exit;
    }

} else {
    $mysqli->close();
    $json_arr = array('code' => -6, 'msg' => '操作失败，请重试');
    echo json_encode($json_arr);
    exit;
}
