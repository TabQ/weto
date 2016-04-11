<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/3/26
 * Time: 19:23
 */
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();

if(!isset($_SESSION['uid'])) {
    header('Location: user.php?action=login');
    exit;
}

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
    $mysqli->close();
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_GET['action'] == 'add_detail') {
        trim_addslashes($_POST);

        extract($_POST);
        $name = intval($name);
        $rate = intval($rate);
        $price = floatval($price);
        $unit = intval($unit);
        $time = strtotime($time);
        $uid = $_SESSION['uid'];
        $username = $_SESSION['username'];

        $query = "insert into weto_goods_detail(nid, rid, unid, price, time, uid, username) values($name, $rate, $unit, $price, $time, $uid, '$username')";
        if(!$result = $mysqli->query($query)) {
            $mysqli->close();
            log_and_jump(__FILE__, __LINE__, "插入失败：$query", "errorpages/opterror.html");
            exit;
        }
    } elseif($_GET['action'] == 'add_name') {
        $name = $_POST['name'];
        if(preg_match('/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]{1,10}$/u', $name) != 1) {
            $mysqli->close();
            $json_arr = array('code' => 0, 'msg' => '最多10个中文字符、英文字符或数字');
            echo json_encode($json_arr);
            exit;
        }
        // 先查询是否有重复
        $query = "select count(*) as count from weto_goods_name wn, weto_goods_cate wc where wc.name='农副产品' and wn.cid=wc.id and wn.name='$name' limit 1";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            if(!empty($row['count'])) {
                $mysqli->close();
                $json_arr = array('code' => -1, 'msg' => '对不起，已有该产品');
                echo json_encode($json_arr);
                exit;
            }
        } else {
            $mysqli->close();
            log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
            exit;
        }
        // 然后查询农副产品相应cid（一般为1）
        $query = "select id from weto_goods_cate where name='农副产品' limit 1";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            if(is_null($row)) {
                $mysqli->close();
                $json_arr = array('code' => -2, 'msg' => '对不起，还未建立该产品所属种类，请通知管理员添加');
                echo json_encode($json_arr);
                exit;
            } else {
                // 添加
                $cid = $row['id'];
                $query = "insert into weto_goods_name(cid, name) values($cid, '$name')";
                if($result = $mysqli->query($query)) {
                    $mysqli->close();
                    $json_arr = array('code' => 1, 'msg' => '添加成功');
                    echo json_encode($json_arr);
                    exit;
                } else {
                    $mysqli->close();
                    $json_arr = array('code' => -3, 'msg' => '添加失败，请重试');
                    echo json_encode($json_arr);
                    exit;
                }

            }

        } else {
            $mysqli->close();
            log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
            exit;
        }

    } else {
        log_and_jump(__FILE__, __LINE__, $_SESSION['username']."非法访问：".$_SERVER['REQUEST_URI'], "errorpages/404.html");
        $mysqli->close();
        exit;
    }
}

// 查询名称
$names = array();
$query = "select wn.id, wn.name from weto_goods_name wn, weto_goods_cate wc where wc.name='农副产品' and wn.cid=wc.id order by wn.id";
if($result = $mysqli->query($query)) {
    while($row = $result->fetch_assoc()) {
        $names[] = $row;
    }
    $result->free();
} else {
    $mysqli->close();
    log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
    exit;
}

// 查询等级
$rates = array();
$query = "select id, name from weto_goods_rate order by id";
if($result = $mysqli->query($query)) {
    while($row = $result->fetch_assoc()) {
        $rates[] = $row;
    }
    $result->free();
} else {
    $mysqli->close();
    log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
    exit;
}
// 查询单位
$units = array();
$query = "select id, name from weto_goods_unit order by id";
if($result = $mysqli->query($query)) {
    while($row = $result->fetch_assoc()) {
        $units[] = $row;
    }
    $result->free();
} else {
    $mysqli->close();
    log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
    exit;
}

// 查询最近新增5条信息
$goods = array();
$query = "select wn.name as nname, wr.name as rname, wd.price, wu.name as uname, wd.time, wd.username from weto_goods_detail wd, weto_goods_rate wr, weto_goods_unit wu,
weto_goods_name wn, weto_goods_cate wc where wd.rid = wr.id and wd.unid = wu.id and wd.nid = wn.id and wn.cid = wc.id and wc.name = '农副产品' order by wd.id desc limit 5";
if($result = $mysqli->query($query)) {
    while($row = $result->fetch_assoc()) {
        $goods[] = $row;
    }
    $result->free();
} else {
    $mysqli->close();
    log_output(__FILE__, __LINE__, "查询错误：$query");
    exit;
}

$mysqli->close();

$tmpObj = new Template();

$tmpObj->names = $names;
$tmpObj->rates = $rates;
$tmpObj->units = $units;
$tmpObj->goods = $goods;

$tmpObj->seo_arr = $meta_arr;

$tmpObj->rec_vegs = get_veg_price();

$tmpObj->display('offer.tpl');