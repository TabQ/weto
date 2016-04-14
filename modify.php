<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14
 * Time: 12:51
 */
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();
if(!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    header('Location: /index.php');
    exit;
}

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
    $mysqli->close();
    exit;
}

$query = "INSERT INTO weto_goods_cate(name) values('农副产品')";
if(!$result = $mysqli->query($query)) {
    log_and_jump(__FILE__, __LINE__, "插入失败：$query", "errorpages/opterror.html");
    $mysqli->close();
    exit;
} else {
    echo 'ok';
    $mysqli->close();
}