<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/1/29
 * Time: 13:30
 * Url: blocks.php?bid=123[&fid=123][&tid=123]
 */
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();

if(!isset($_GET['bid'])) {
    log_and_jump(__FILE__, __LINE__, "访问非法页面：" . $_SERVER['REQUEST_URI'], "errorpages/404.html");
    exit;
}
$bid = $_GET['bid'];

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
    $mysqli->close();
    exit;
}
// 判断bid是否有效
$query = "select count(*) as count from weto_blocks where id = $bid and closed = 0";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    if($row['count'] == 0) {
        log_and_jump(__FILE__, __LINE__, "访问非法页面：" . $_SERVER['REQUEST_URI'], "errorpages/404.html");
        $result->free();
        $mysqli->close();
        exit;
    }
    $result->free();
}
// 判断fid是否有效
$fid = isset($_GET['fid']) ? $_GET['fid'] : null;
if(!is_null($fid)) {
    $query = "select count(*) as count from weto_forums where bid = $bid and id = $fid and closed = 0";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        if($row['count'] == 0) {
            log_and_jump(__FILE__, __LINE__, "访问非法页面：" . $_SERVER['REQUEST_URI'], "errorpages/404.html");
            $result->free();
            $mysqli->close();
            exit;
        }
        $result->free();
    }
}

// 获取该bid下的所有forum
$forums_arr = $forum_articles_arr = $forum_rank_arr = $fid_arr = array();
$query = "select id, forumname, posts, rank from weto_forums where bid = $bid and closed = 0";
if($result = $mysqli->query($query)) {
    while($row = $result->fetch_assoc()) {
        $forums_arr[] = $row;

        $fid_arr[] = $row["id"];
        $forum_articles_arr[] = $row["posts"];
        $forum_rank_arr[] = $row["rank"];
    }
    $result->free();
}
if(!empty($forum_articles_arr) && !empty($forum_rank_arr) && !empty($forums_arr)) {
    array_multisort($forum_articles_arr, SORT_DESC, $forum_rank_arr, SORT_ASC, $forums_arr);
}

$tmpObj = new Template();

$tmpObj->bid = $bid;
$tmpObj->fid = $fid;
$tmpObj->forums_arr = $forums_arr;
$tmpObj->can_post = true;
$tmpObj->can_reply = false;

if(!is_null($fid)) {
    $page_url = "blocks.php?bid=$bid&fid=$fid&";
    $cond_page = " where fid = $fid and top = 0 and deleted = 0";
    $cond = " where fid = $fid and top = 0 and deleted = 0 and closed = 0 and wa.fid = wf.id order by createtime desc";
} elseif(!empty($fid_arr)) {
    $page_url = "blocks.php?bid=$bid&";
    $fid_str = implode(",", $fid_arr);
    $cond_page = " where fid in ($fid_str) and top = 0 and deleted = 0";
    $cond = " where fid in ($fid_str) and top = 0 and deleted = 0 and closed = 0 and wa.fid = wf.id order by createtime desc";
} else {
    $page_url = "blocks.php?bid=$bid&";
}
// 获取page_arr
$page_arr = array(
    'page_url'      => $page_url,
    'cur_page'      => 1,
    'total_page'    => 1
);
$tmpObj->page = $page_arr;
if(isset($cond_page)) {
    $query = "select count(*) as count from weto_articles" . $cond_page;
    if(get_page_para($query, $mysqli, $page_arr)) {
        $tmpObj->page = $page_arr;
    } else {
        log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    }
}
// 获取articles_list
$tmpObj->articles = array();
if(isset($cond) && isset($page_arr['cur_page'])) {
    $limit = " limit " . ($page_arr['cur_page'] - 1) * ITEMS_PER_PAGE . ", " . ITEMS_PER_PAGE;
    $query = "select wa.id, fid, forumname, title, author, views, replies, digest, attachment, createtime, lastpost, lastposter
          from weto_articles wa, weto_forums wf" . $cond . $limit;
    if($result = $mysqli->query($query)) {
        while($row = $result->fetch_assoc()) {
            $row['title'] = htmlspecialchars($row['title']);
            $tmpObj->articles[] = $row;
        }
        $result->free();
    }
}

$mysqli->close();

$db_seo_arr = get_seo('weto_blocks', $bid);
$tmpObj->seo_arr = $db_seo_arr === false ? $meta_arr : $db_seo_arr;

$tmpObj->rec_vegs = get_veg_price();

$tmpObj->display('blocks.tpl');