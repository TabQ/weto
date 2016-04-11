<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/3/9
 * Time: 19:34
 */
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();
if(!isset($_SESSION['username'])) {
    log_output(__FILE__, __LINE__, "非法访问：" . $_SERVER['REQUEST_URI']);
    header('Location: /index.php');
    exit;
}

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
    $mysqli->close();
    exit;
}

$tmpObj = new Template();

$tmpObj->result = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_POST['nickname'] = trim($_POST['nickname']);
    !get_magic_quotes_gpc() && $_POST['nickname'] = addslashes($_POST['nickname']);
    $query = "update weto_users set nickname='$_POST[nickname]', avatar=$_POST[index] where id=$_SESSION[uid]";
    if(!$result = $mysqli->query($query)) {
        log_and_jump(__FILE__, __LINE__, "更新错误：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    } else {
        $tmpObj->result = "修改成功";
        $_SESSION['avatar'] = $_POST['index'];
    }
}

$tmpObj->userinfo = array();
$query = "select nickname, avatar from weto_users where id=$_SESSION[uid] limit 1";
if($result = $mysqli->query($query)) {
    while($row = $result->fetch_assoc()) {
        $row['nickname'] = htmlspecialchars($row['nickname']);
        $tmpObj->userinfo = $row;
    }
    $result->free();
} else {
    log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
    $mysqli->close();
    exit;
}

$mysqli->close();

$tmpObj->seo_arr = $meta_arr;
$tmpObj->seo_arr['seotitle'] = '用户中心';
$tmpObj->max_avatar = MAX_AVATAR_INDEX;

$tmpObj->rec_vegs = get_veg_price();

$tmpObj->display('userinfo.tpl');