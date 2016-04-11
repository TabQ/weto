<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/3/10
 * Time: 11:34
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

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_pwd = trim($_POST['old_pwd']);
    $new_pwd = trim($_POST['new_pwd']);
    $new_pwd2 = trim($_POST['new_pwd2']);

    if($new_pwd != $new_pwd2) {
        $json_arr = array('code' => 0, 'msg' => '新密码输入不一致');
        echo json_encode($json_arr);
        exit;
    } elseif(preg_match('/[a-zA-Z0-9!@#%&_\-\$]{5,20}/i', $new_pwd) != 1) {
        $json_arr = array('code' => 0, 'msg' => '密码由5-20位英文字母、数字及特殊字符组成');
        echo json_encode($json_arr);
        exit;
    }

    $mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
    if($mysqli->connect_errno) {
        log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
        $mysqli->close();
        exit;
    }

    if(get_magic_quotes_gpc()) {
        $old_pwd = md5($old_pwd);
        $new_pwd = md5($new_pwd);
        $new_pwd2 = md5($new_pwd2);
    } else {
        $old_pwd = md5(addslashes($old_pwd));
        $new_pwd = md5(addslashes($new_pwd));
        $new_pwd2 = md5(addslashes($new_pwd2));
    }
    $query = "select password from weto_users where id=$_SESSION[uid] limit 1";
    if($result = $mysqli->query($query)) {
        while($row = $result->fetch_assoc()) {
            if($row['password'] == $old_pwd) {
                $result->free();
                $query = "update weto_users set password='$new_pwd' where id=$_SESSION[uid]";
                if($result = $mysqli->query($query)) {
                    $json_arr = array('code' => 1, 'msg' => '修改成功，请重新登录');
                    echo json_encode($json_arr);
                    $mysqli->close();
                    exit;
                } else {
                    $json_arr = array('code' => 0, 'msg' => '密码修改失败，请重试');
                    echo json_encode($json_arr);
                    $mysqli->close();
                    exit;
                }
            } else {
                $json_arr = array('code' => 0, 'msg' => '原始密码输入不正确');
                echo json_encode($json_arr);
                $mysqli->close();
                exit;
            }
        }
        $result->free();
        log_output(__FILE__,__LINE__, "用户修改密码时出现错误：$query");
        $json_arr = array('code' => 0, 'msg' => '修改密码出现错误');
        echo json_encode($json_arr);
        $mysqli->close();
        exit;
    } else {
        log_and_jump(__FILE__, __LINE__, "查询失败：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    }
}

$tmpObj = new Template();

$tmpObj->seo_arr = $meta_arr;
$tmpObj->seo_arr['seotitle'] = '修改密码';

$tmpObj->rec_vegs = get_veg_price();

$tmpObj->display('chg_pwd.tpl');