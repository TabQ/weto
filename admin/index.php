<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/2/15
 * Time: 10:04
 */
include "../config.php";
include "../template.class.php";
include_once "../functions.php";

session_start();
if(!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    header('Location: /index.php');
    exit;
}

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_output(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error");
    $mysqli->close();
    exit("数据库连接失败，请重试");
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    trim_addslashes($_POST);

    if($_POST['action'] == 'site') {
        $query = "update weto_site set seotitle='".$_POST['home_title']."',keywords='".$_POST['home_keywords']."',description='".$_POST['home_des']."' where id=1";
        if($result = $mysqli->query($query)) {
            $mysqli->close();
            exit('success');
        } else {
            log_output(__FILE__, __LINE__, "更新错误：$query");
            $mysqli->close();
            exit('error');
        }
    } elseif($_POST['action'] == 'pretops') {
        $now = time();
        $title = $_POST['title'];
        $message = $_POST['message'];
        $uid = $_SESSION['uid'];
        $username = $_SESSION['username'];

        $query = "update weto_pretops set title = '$title', message = '$message', time = $now, uid = $uid, username = '$username' where id = 1";
        if($result = $mysqli->query($query)) {
            $mysqli->close();
           exit('success');
        } else {
            log_output(__FILE__, __LINE__, "更新错误：$query");
            $mysqli->close();
            exit('error');
        }
    }
} else {
    $tmpObj = new Template();

    $tmpObj->seo_arr = $meta_arr;

    // 获取站点基本信息
    $query = "select seotitle, keywords, description from weto_site where id = 1";
    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();
    $result->free();

    $tmpObj->title = $row['seotitle'];
    $tmpObj->keywords = $row['keywords'];
    $tmpObj->description = $row['description'];

    // 获取预设置顶贴内容
    $query = "select title, message from weto_pretops where id = 1 limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
        if(!is_null($row)) {
            $pretops = $row;
        }
    } else {
        log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    }

    $tmpObj->pretops = $pretops;

    $tmpObj->admin = true;
    $tmpObj->item = "index";

    $tmpObj->rec_vegs = get_veg_price();

    $tmpObj->display('admin/index.tpl');
}

$mysqli->close();
