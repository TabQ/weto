<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/3/27
 * Time: 12:45
 */
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
    $mysqli->close();
    exit;
}

// 检测nid的正确性并获取nname
$nid = 0;
$nname = '';
if(!isset($_GET['nid'])) {
    $query = "select wn.id, wn.name as nname from weto_goods_name wn, weto_goods_cate wc where wc.name='农副产品' and wn.cid=wc.id order by wn.id limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
        if(!empty($row['id'])) {
            $nid = $row['id'];
            $nname = $row['nname'];
        }
    } else {
        log_output(__FILE__, __LINE__, "查询出错：$query");
    }
} else {
    $query = "select wn.name as nname from weto_goods_name wn, weto_goods_cate wc where wc.name='农副产品' and wn.cid=wc.id and wn.id=$_GET[nid] limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
        if(is_null($row)) {
            log_and_jump(__FILE__, __LINE__, "访问非法页面：".$_SERVER['REQUEST_URI'], "errorpages/404.html");
            $mysqli->close();
            exit;
        } else {
            $nid = $_GET['nid'];
            $nname = $row['nname'];
        }
    } else {
        log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    }
}

// 先查询最后一次发布的时间
$rec_time = 0;
$query = "select time from weto_goods_detail order by time desc limit 1";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    $result->free();
    if(!empty($row['time'])) {
        $rec_time = $row['time'];
    }
} else {
    log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
    $mysqli->close();
    exit;
}
// 查询最近一次发布的所有信息
$goods = array();
if(!empty($rec_time)) {
    $query = "select wn.name as nname, wn.id, wr.name as rname, wd.price, wu.name as uname, wd.time, wd.username from weto_goods_detail wd, weto_goods_rate wr, weto_goods_unit wu,
weto_goods_name wn, weto_goods_cate wc where wd.rid = wr.id and wd.unid = wu.id and wd.nid = wn.id and wn.cid = wc.id and wc.name = '农副产品' and wd.time = $rec_time order by wn.id";
    if($result = $mysqli->query($query)) {
        while($row = $result->fetch_assoc()) {
            $goods[] = $row;
        }
        $result->free();
    } else {
        log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    }
}
// 查询$nid走势
$get_nid_chart_data = array();
if(!empty($nid)) {
    $query = "select wn.name as nname, wd.price, wd.time from weto_goods_detail wd, weto_goods_name wn where wn.id=$nid and wn.id=wd.nid order by wd.time limit 10";
    if($result = $mysqli->query($query)) {
        while($row = $result->fetch_assoc()) {
            $row['time'] = date('Y-m-d', $row['time']);
            $get_nid_chart_data['labels'][] = $row['time'];
            $datasets['data'][] = $row['price'];
        }
        $result->free();
        $datasets['fillColor'] = $chart_arr['fillColor'];
        $datasets['strokeColor'] = $chart_arr['strokeColor'];
        $datasets['pointColor'] = $chart_arr['pointColor'];
        $datasets['pointStrokeColor'] = $chart_arr['pointStrokeColor'];
        $get_nid_chart_data['datasets'][] = $datasets;
    } else {
        log_output(__FILE__, __LINE__, "查询错误：$query");
    }
}

$mysqli->close();

$tmpObj = new Template();

$tmpObj->goods = $goods;
$tmpObj->get_nid_chart_data = json_encode($get_nid_chart_data);
$tmpObj->nid = $nid;
$tmpObj->nname = $nname;
$tmpObj->rec_time = date('Y-m-d', $rec_time);

$tmpObj->seo_arr = $meta_arr;

$tmpObj->rec_vegs = get_veg_price();

$tmpObj->display('vegetables.tpl');