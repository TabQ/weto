<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/1/28
 * Time: 20:19
 */
include "functions.php";

session_start();

if(isset($_SESSION['username'])) {
    unset($_SESSION['username']);
    unset($_SESSION['uid']);
    unset($_SESSION['avatar']);

    // 清除跳转地址
    if(isset($_SESSION['jump_url'])) {
        unset($_SESSION['jump_url']);
    }

    session_destroy();
    header('Location: index.php');
} else {
    header('Location: index.php');
    log_output(__FILE__, __LINE__, "未登陆用户访问logout.php页面");
}