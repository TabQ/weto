<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/1/27
 * Time: 16:26
 */
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();
if(isset($_SESSION['username'])) {
    header('Location: index.php');
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!isset($_GET['which'])) {
        log_and_jump(__FILE__, __LINE__, "未携带which参数post进入user.php页面：{$_SERVER['QUERY_STRING']}", "errorpages/404.html");
        exit;
    } else {
        if($_GET['which'] == 'error') {
            log_output(__FILE__, __LINE__, $_POST['error']);
            exit;
        } else {
            $mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
            if($mysqli->connect_errno) {
                log_output(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error");
                $mysqli->close();
                exit("数据库连接失败，请重试");
            }

            if($_GET['which'] == 'username') {
                $username = get_magic_quotes_gpc() ? $_POST['username'] : addslashes($_POST['username']);
                $query = "select count(*) as count from weto_users where username='".$username."'";
                $result = $mysqli->query($query);
                if($result === false) {
                    log_output(__FILE__, __LINE__, "查询用户名出错，语句：{$query}，错误码：{$mysqli->error}");
                    $mysqli->close();
                    exit("查询出错，请重试");
                } else {
                    $row = $result->fetch_assoc();
                    if($row['count'] == 0) {
                        echo 'success';
                    } elseif($row['count'] > 0) {
                        echo '该用户名已存在';
                    } else {
                        log_output(__FILE__, __LINE__, "查询用户名返回负值：" . $row['count']);
                        echo '查询用户名返回负值，请报告管理员';
                    }
                    $result->free();
                    $mysqli->close();
                }
            } elseif($_GET['which'] == 'email') {
                $email = get_magic_quotes_gpc() ? $_POST['email'] : addslashes($_POST['email']);
                $query = "select count(*) as count from weto_users where email='".$email."'";
                $result = $mysqli->query($query);
                if($result === false) {
                    log_output(__FILE__, __LINE__, "查询email出错，语句：{$query}，错误码：{$mysqli->error}");
                    $mysqli->close();
                    exit("查询出错，请重试");
                } else {
                    $row = $result->fetch_assoc();
                    if($row['count'] == 0) {
                        echo 'success';
                    } elseif($row['count'] > 0) {
                        echo '该邮箱地址已存在';
                    } else {
                        log_output(__FILE__, __LINE__, "查询email返回负值：" . $row['count']);
                        echo '查询email返回负值，请报告管理员';
                    }
                    $result->free();
                    $mysqli->close();
                }
            } elseif($_GET['which'] == 'register') {
                if(!get_magic_quotes_gpc()) {
                    $username = addslashes($_POST['username']);
                    $email = addslashes($_POST['email']);
                    $password = md5(addslashes($_POST['password']));
                } else {
                    $username = $_POST['username'];
                    $email = $_POST['email'];
                    $password = md5($_POST['password']);
                }
                $avatar = rand(0, MAX_AVATAR_INDEX);
                $now = time();
                $query = "insert into weto_users(username, email, password, avatar, regdate) values('"."$username', '"."$email', '"."$password', $avatar, $now)";
                $result = $mysqli->query($query);
                if($result === false) {
                    log_output(__FILE__, __LINE__, "user表插入数据失败，语句：{$query}，错误码：{$mysqli->errno}");
                    $mysqli->close();
                    exit("操作失败，请重试");
                } else {
                    echo 'success';
                    $_SESSION['username'] = $_POST['username'];
                    $_SESSION['uid'] = $mysqli->insert_id;
                    $_SESSION['avatar'] = $avatar;
                    $mysqli->close();
                }
            } elseif($_GET['which'] == 'login') {
                if(!get_magic_quotes_gpc()) {
                    $username = $_POST['username'];
                    $password = md5(addslashes($_POST['password']));
                } else {
                    $username = stripslashes($_POST['username']);
                    $password = md5($_POST['password']);
                }
                $query = "select id, username, avatar from weto_users where username='$username' and password='$password'";
                $result = $mysqli->query($query);
                if($result === false) {
                    log_output(__FILE__, __LINE__, "查询用户名及密码失败");
                    $mysqli->close();
                    exit('操作失败，请重试');
                } else {
                    $row = $result->fetch_assoc();
                    if(empty($row)) {
                        echo '用户名或密码不正确';
                    } else {
                        echo 'success';
                        $_SESSION['username'] = $_POST['username'];
                        $_SESSION['uid'] = $row['id'];
                        $_SESSION['avatar'] = $row['avatar'];
                    }
                    $result->free();
                    $mysqli->close();
                }
            } else {
                log_and_jump(__FILE__, __LINE__, "携带未知which参数：{$_GET['which']} post进入user.php页面", "errorpages/404.html");
                $mysqli->close();
                exit;
            }

        }
    }
} else {

    if(isset($_GET['action'])) {
        $tmpObj = new Template();

        $tmpObj->seo_arr = $meta_arr;
        $tmpObj->seo_arr['seotitle'] = $_GET['action'] == 'login' ? '登录' : '注册';
        // 实现跳转
        if(!empty($_SESSION['jump_url'])) {
            $tmpObj->jump_url = $_SESSION['jump_url'];
        } else {
            $tmpObj->jump_url = 'index.php';
        }

        $tmpObj->rec_vegs = get_veg_price();

        $tmpObj->display('user.tpl');
    } else {
        log_and_jump(__FILE__, __LINE__, "用户访问非法页面：{$_SERVER['PHP_SELF']}，QueryString: {$_SERVER['QUERY_STRING']}", "errorpages/404.html");
        exit;
    }
}