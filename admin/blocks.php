<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/16
 * Time: 13:26
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
    if($_GET['action'] == 'add') {
        $query = "insert into weto_blocks(blockname, rank, seotitle, keywords, description)
                  values('$_POST[name]', $_POST[rank], '$_POST[seotitle]', '$_POST[keywords]', '$_POST[description]')";
        $result = $mysqli->query($query);
        if($result) {
            exit('success');
        } else {
            $mysqli->close();
            exit('error');
        }
    }
    if($_GET['action'] == 'edit') {
        $query = "update weto_blocks set blockname = '$_POST[name]', rank = $_POST[rank], seotitle = '$_POST[seotitle]',
                  keywords = '$_POST[keywords]', description = '$_POST[description]' where id = $_POST[id]";
        $result = $mysqli->query($query);
        if($result) {
            exit('success');
        } else {
            $mysqli->close();
            exit('error');
        }
    }
} else {
    $tmpObj = new Template();

    $tmpObj->seo_arr['seotitle'] = $meta_arr['seotitle'];
    $tmpObj->seo_arr['keywords'] = $meta_arr['keywords'];
    $tmpObj->seo_arr['description'] = $meta_arr['description'];

    $query = "select id, blockname as name, rank, seotitle, keywords, description from weto_blocks where closed = 0 order by rank asc";
    $result = $mysqli->query($query);
    $tmpObj->blocks = array();
    while($row = $result->fetch_assoc()) {
        $tmpObj->blocks[] = $row;
    }
    $result->free();

    $tmpObj->admin = true;
    $tmpObj->item = "blocks";

    $tmpObj->rec_vegs = get_veg_price();

    $tmpObj->display('admin/index.tpl');
}

$mysqli->close();