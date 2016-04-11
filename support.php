<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/3/20
 * Time: 13:15
 */
include "config.php";
include_once "functions.php";

session_start();

if(!isset($_SESSION['username'])) {
    log_output(__FILE__, __LINE__, "未登陆访问页面：".$_SERVER['REQUEST_URI']);
    $json_arr = array('code' => -1, 'msg' => '请登陆');
    echo json_encode($json_arr);
    exit;
}

if(!isset($_GET['pfid'])) {
    log_output(__FILE__, __LINE__, $_SESSION['username']."访问非法页面：" . $_SERVER['REQUEST_URI']);
    $json_arr = array('code' => -2, 'msg' => '该页面不存在');
    echo json_encode($json_arr);
    exit;
}
$pfid = $_GET['pfid'];

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_output(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error");
    $mysqli->close();
    $json_arr = array('code' => -3, 'msg' => '数据库连接失败');
    echo json_encode($json_arr);
    exit;
}

// 判断pfid是否有效
$query = "select count(*) as count from weto_preforums where id=$pfid and overdue=0 limit 1";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    $result->free();
    if(empty($row['count'])) {
        log_output(__FILE__, __LINE__, $_SESSION['username']."访问非法页面：".$_SERVER['REQUEST_URI']);
        $mysqli->close();
        $json_arr = array('code' => -4, 'msg' => '该版面不存在或已过期');
        echo json_encode($json_arr);
        exit;
    }
} else {
    log_output(__FILE__, __LINE__, "查询错误：$query");
    $mysqli->close();
    $json_arr = array('code' => -5, 'msg' => '操作失败，请重试');
    echo json_encode($json_arr);
    exit;
}

// 查询该用户是否已支持过该pfid
$query = "select count(*) as count from weto_supporters where pfid=$pfid and uid=$_SESSION[uid] limit 1";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    $result->free();
    if(!is_null($row) && $row['count'] == 1) {
        $mysqli->close();
        $json_arr = array('code' => -6, 'msg' => '对不起，您已支持过该版面');
        echo json_encode($json_arr);
        exit;
    }
} else {
    log_output(__FILE__, __LINE__, "查询错误：$query");
    $mysqli->close();
    $json_arr = array('code' => -5, 'msg' => '操作失败，请重试');
    echo json_encode($json_arr);
    exit;
}

// 增加支持者
$now = time();
$query = "insert into weto_supporters(pfid, uid, username, time) values($pfid, $_SESSION[uid], '$_SESSION[username]', $now)";
if($result = $mysqli->query($query)) {
    // 查询该版面支持者是否已达50人，如果达到同时更新weto_preforums和weto_forums
    $query = "select count(*) as count from weto_supporters where pfid=$pfid limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
        if(!is_null($row) && $row['count'] >= MAX_SUPPORTERS) {
            // 更新weto_preforums
            $query = "update weto_preforums set overdue=1 where id=$pfid";
            if($result = $mysqli->query($query)) {
                // 开通该版面
                $query = "insert into weto_forums(bid, forumname, adminlist) values($_POST[bid], '$_POST[name]', '$_POST[proposer],')";
                if($result = $mysqli->query($query)) {

                    $fid = $mysqli->insert_id;
                    // 插入预设置顶贴
                    $query = "select title, message, time, uid, username from weto_pretops where id = 1 limit 1";
                    if($result = $mysqli->query($query)) {
                        $row = $result->fetch_assoc();
                        $result->free();
                        if(!empty($row['title']) && !empty($row['message'])) {

                            $query = "insert into weto_articles(fid, title, uid, author, top, createtime, edittime, lastpost, lastposter)
values($fid, '$row[title]', $row[uid], '$row[username]', 1, $row[time], $row[time], $row[time], '$row[username]')";
                            if($result = $mysqli->query($query)) {
                                $aid = $mysqli->insert_id;
                                // 插入预设置顶贴fistpost
                                $query = "insert into weto_posts(aid, firstpost, uid, author, message, createtime, edittime)
values($aid, 1, $row[uid], '$row[username]', '$row[message]', $row[time], $row[time])";
                                if(!$result = $mysqli->query($query)) {
                                    log_output(__FILE__, __LINE__, "插入失败：$query");
                                }
                            } else {
                                log_output(__FILE__, __LINE__, "插入失败：$query");
                            }
                        }
                    } else {
                        log_output(__FILE__, __LINE__, "查询错误：$query");
                    }

                    $json_arr = array('code' => 2, 'msg' => '恭喜，该版面支持者数已达目标，系统自动开通，您是第一个访问该版面的人', 'fid' => $fid);
                    echo json_encode($json_arr);
                    $mysqli->close();
                    exit;
                } else {
                    log_output(__FILE__, __LINE__, "插入失败：$query");
                    $mysqli->close();
                    $json_arr = array('code' => -7, 'msg' => '操作失败，请重试');
                    echo json_encode($json_arr);
                    exit;
                }
            } else {
                log_output(__FILE__, __LINE__, "更新失败：$query");
                $mysqli->close();
                $json_arr = array('code' => -8, 'msg' => '操作失败，请重试');
                echo json_encode($json_arr);
                exit;
            }
        } elseif(!is_null($row) && $row['count'] < MAX_SUPPORTERS) {
            $mysqli->close();
            $json_arr = array('code' => 1, 'msg' => '成功');
            echo json_encode($json_arr);
            exit;
        }
    } else {
        log_output(__FILE__, __LINE__, "查询错误：$query");
        $mysqli->close();
        $json_arr = array('code' => -5, 'msg' => '操作失败，请重试');
        echo json_encode($json_arr);
        exit;
    }
} else {
    log_output(__FILE__, __LINE__, "插入失败：$query");
    $mysqli->close();
    $json_arr = array('code' => -7, 'msg' => '操作失败，请重试');
    echo json_encode($json_arr);
    exit;
}