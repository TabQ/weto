<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/16
 * Time: 13:27
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
    if(isset($_POST['action']) && $_POST['action'] == 'add') {

        $query = "insert into weto_forums(bid, forumname, rank, seotitle, keywords, description)
                  values($_POST[bid], '$_POST[name]', $_POST[rank], '$_POST[seotitle]', '$_POST[keywords]', '$_POST[description]')";

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

            $mysqli->close();
             exit('success');
        } else {
            $mysqli->close();
            exit('error');
        }
    }
    if(isset($_POST['action']) && $_POST['action'] == 'edit') {
        $query = "update weto_forums set bid=$_POST[bid], forumname='$_POST[name]', rank=$_POST[rank], seotitle='$_POST[seotitle]',
                  keywords='$_POST[keywords]', description='$_POST[description]' where id=$_POST[fid]";
        $result = $mysqli->query($query);
        if($result) {
            $mysqli->close();
            exit('success');
        } else {
            $mysqli->close();
            exit('error');
        }
    }
    if(isset($_POST['username'])) {
        if($_POST['username'] != '') {
            $query = "select username from weto_users where username='$_POST[username]'";
            if($result = $mysqli->query($query)) {
                $row = $result->fetch_assoc();
                $result->free();
                if($row['username'] != $_POST['username']) {
                    $mysqli->close();
                    exit('no such member');
                } else {
                    unset($row);
                    $query = "select adminlist from weto_forums where id=$_POST[fid]";
                    if($result = $mysqli->query($query)) {
                        $row = $result->fetch_assoc();
                        $result->free();

                        $adminlist = $row['adminlist'] . $_POST['username'] . ',';
                        $query = "update weto_forums set adminlist='$adminlist' where id=$_POST[fid]";
                        if($result = $mysqli->query($query)) {
                            $mysqli->close();
                            exit('success');
                        } else {
                            $mysqli->close();
                            exit('update adminlist error');
                        }
                    } else {
                        $mysqli->close();
                        exit('query adminlist error');
                    }
                }
            } else {
                $mysqli->close();
                exit('query username error');
            }
        } else {
            $mysqli->close();;
            exit('username is empty');
        }
    }
} else {
    $tmpObj = new Template();

    $tmpObj->seo_arr['seotitle'] = $meta_arr['seotitle'];
    $tmpObj->seo_arr['keywords'] = $meta_arr['keywords'];
    $tmpObj->seo_arr['description'] = $meta_arr['description'];

    $query = "select id, bid, forumname as name, articles, posts, rank, seotitle, keywords, description, adminlist
              from weto_forums where closed=0 order by posts desc, rank asc";
    $result = $mysqli->query($query);
    $tmpObj->forums = array();
    while($row = $result->fetch_assoc()) {
        $tmpObj->forums[] = $row;
    }
    $result->free();
    $mysqli->close();

    $tmpObj->admin = true;
    $tmpObj->item = "forums";

    $tmpObj->rec_vegs = get_veg_price();

    $tmpObj->display('admin/index.tpl');
}