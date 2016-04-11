<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/2/3
 * Time: 12:40
 */
include "config.php";
include "functions.php";
include "watermask.class.php";

session_start();
if(!isset($_SESSION['username'])) {
    header('Location: index.php');
}

//文件保存目录路径
$save_path = SITE_ROOT . '/uploads/';
//文件保存目录url
$save_url = str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])) . 'uploads/';
//定义允许上传的文件扩展名
$ext_arr = array(
    'image' => array('gif', 'jpg', 'png')
);
// 最大图片大小2M
$max_size = 2000000;
// 上传失败
if(!empty($_FILES['imgFile']['error'])) {
    switch($_FILES['imgFile']['error']) {
        case 1:
            $error = '超过php.ini允许的大小';
            break;
        case 2:
            $error = '超过表单允许的大小';
            break;
        case 3:
            $error = '图片只有部分被上传';
            break;
        case 4:
            $error = '请选择图片';
            break;
        case 6:
            $error = '找不到临时目录';
            break;
        case 7:
            $error = '写文件到硬盘出错';
            break;
        case 8:
            $error = 'File upload stopped by extension';
            break;
        case 999:
        default:
            $error = '未知错误';
    }
    alert($error);
}

// 有文件上传时
if(!empty($_FILES)) {
    //原文件名
    $file_name = $_FILES['imgFile']['name'];
    //服务器上临时文件名
    $tmp_name = $_FILES['imgFile']['tmp_name'];
    //文件大小
    $file_size = $_FILES['imgFile']['size'];
    //检查文件名
    if(!$file_name) {
        alert('请选择文件');
    }
    //检查目录
    if(!file_exists($save_path)) {
        mkdir($save_path, 0664);
    }
    //检查目录写权限
    if(@is_writable($save_path) === false) {
        alert('上传目录没有写权限');
    }
    //检查是否已上传
    if(@is_uploaded_file($tmp_name) === false) {
        alert('上传失败');
    }
    //检查文件大小
    if($file_size > $max_size) {
        alert('上传文件大小超过限制');
    }
    //检查目录名
    $dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
    if(empty($ext_arr[$dir_name])) {
        alert('目录名不正确');
    }
    //获取文件扩展名
    $file_ext = check_file_type($tmp_name);
    //检查扩展名
    if(!in_array($file_ext, array('gif', 'jpg', 'png'))) {
        @unlink($tmp_name);
        alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式");
    }

    //创建文件夹
    if($dir_name !== '') {
        $save_path .= $dir_name . "/";
        $save_url .= $dir_name . "/";
        if(!file_exists($save_path)) {
            mkdir($save_path);
        }
    }
    $ymd = date('Y-m-d');
    $save_path .= $ymd . "/";
    $save_url .= $ymd . "/";
    if(!file_exists($save_path)) {
        mkdir($save_path);
    }
    //新文件名
    $new_file_name = uniqid() . '.' . $file_ext;
    // 移动文件
    $file_path = $save_path . $new_file_name;
    if(move_uploaded_file($tmp_name, $file_path) === false) {
        alert('上传文件失败');
    }
    @chmod($file_path, 0644);

    // 加水印
    $waterMaskObj = new WaterMask($file_path);
    $waterMaskObj->waterType = 1;
    $waterMaskObj->pos = 9;
    $waterMaskObj->transparent = 45;
    $waterMaskObj->waterImg = SITE_ROOT . '/templates/img/weto.png';
    $waterMaskObj->output();

    $file_url = $save_url . $new_file_name;

    header('Content-type: text/html; charset=UTF-8');
    echo json_encode(array('error' => 0, 'url' => $file_url));
    exit;
}

function alert($msg) {
    header('Content-type: text/html; charset=UTF-8');
    echo json_encode(array('error' => 1, 'message' => $msg));
    exit;
}