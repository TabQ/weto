<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/3/9
 * Time: 5:58
 */
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();
if(isset($_SESSION['username'])) {
    header('Location: /index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
    if($mysqli->connect_errno) {
        log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
        $mysqli->close();
        exit;
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $query = "select count(*) count from weto_users where username='$username' and email='$email' limit 1";
    if($result = $mysqli->query($query)) {
        while($row = $result->fetch_assoc()) {
            if($row['count'] == 1) {
                $result->free();
                $mysqli->close();

                $json_arr = array('code' => 1, 'msg' => '', 'username' => $username);
                echo json_encode($json_arr);
                exit;
            } else {
                $result->free();
                $mysqli->close();

                $json_arr = array('code' => 0, 'msg' => '用户名或邮箱输入错误');
                echo json_encode($json_arr);
                exit;
            }
        }

        $result->free();
        $mysqli->close();
        log_and_jump(__FILE__, __LINE__, "非法操作：$query", "errorpages/opterror.html");
        exit;
    } else {
        log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    }
}

$tmpObj = new Template();

$tmpObj->seo_arr = $meta_arr;
$tmpObj->seo_arr['seotitle'] = '忘记密码';

$tmpObj->rec_vegs = get_veg_price();

$tmpObj->display('forget.tpl');