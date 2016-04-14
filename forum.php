<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/2/1
 * Time: 13:25
 * url: forum.php?fid=123[&action=new]
 */
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();
$_SESSION['jump_url'] = $_SERVER['REQUEST_URI'];    // 记录跳转地址

if(!isset($_GET['fid'])) {
    log_and_jump(__FILE__, __LINE__, "访问非法页面：" . $_SERVER['REQUEST_URI'], "errorpages/404.html");
    exit;
}
$fid = $_GET['fid'];

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
    $mysqli->close();
    exit;
}
// 判断fid是否有效并获取forumname
$query = "select forumname from weto_forums where id = $fid and closed = 0 limit 1";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    $result->free();
    if(is_null($row)) {
        log_and_jump(__FILE__, __LINE__, "访问非法页面：" . $_SERVER['REQUEST_URI'], "errorpages/404.html");
        $mysqli->close();
        exit;
    } else {
        $forumname = $row['forumname'];
    }
} else {
    log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
    $mysqli->close();
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!isset($_SESSION['username'])) {
        header('Location: user.php?action=login');
        $mysqli->close();
        exit;
    }

    if(can_access($mysqli) != 1) {
        $mysqli->close();
        exit;
    }

    // 为90起航主机怪异行为做处理
    $_POST['new_article'] = stripslashes($_POST['new_article']);

    $attachment = 0;
    if(preg_match_all('/<img[^\/]+?src="(\/uploads\/.+?)".*?\/>/i', $_POST['new_article'], $matches, PREG_SET_ORDER)) {
        $_POST['new_article'] = preg_replace('/<img[^\/]+?src="(\/uploads\/.+?)".*?\/>/i', '<p><a href="$1"><img src="$1" width="400px" height="300px" /></a></p><br />', $_POST['new_article']);
        $attachment = 1;
    }

    if(!get_magic_quotes_gpc()) {
        $title = addslashes($_POST['forum_title']);
        $content = addslashes($_POST['new_article']);
    } else {
        $title = $_POST['forum_title'];
        $content = $_POST['new_article'];
    }

    $uid = $_SESSION['uid'];
    $username = $_SESSION['username'];
    $now = time();
    $query = "insert into weto_articles(fid, title, uid, author, attachment, createtime, edittime, lastpost, lastposter)
              values($fid, '$title', $uid, '$username', $attachment, $now, $now, $now, '$username')";
    if($result = $mysqli->query($query)) {
        $aid = $mysqli->insert_id;
        $query = "insert into weto_posts(aid, firstpost, uid, author, message, createtime, edittime, attachment)
                  values($aid, 1, $uid, '$username', '$content', $now, $now, $attachment)";
        if($result = $mysqli->query($query)) {
            $pid = $mysqli->insert_id;
            // 更新weto_forums
            $query = "update weto_forums set articles=articles+1 where id=$fid";
            if(!$result = $mysqli->query($query)) {
                log_output(__FILE__, __LINE__, "更新失败：$query");
            }
            // 更新weto_users
            $query = "update weto_users set articles=articles+1, credits=credits+10 where id=$uid";
            if(!$result = $mysqli->query($query)) {
                log_output(__FILE__, __LINE__, "更新失败：$query");
            }
            // 插入附件
            if($attachment) {
                foreach($matches as $match) {
                    $query = "insert into weto_attachments(aid, pid, path, uid) values($aid, $pid, '$match[1]', $uid)";
                    if(!$result = $mysqli->query($query)) {
                        log_and_jump(__FILE__, __LINE__, "插入weto_attachments表出错：$query", "errorpages/opterror.html");
                        $mysqli->close();
                        exit;
                    }
                }
            }

            $mysqli->close();
            header("Location: forum.php?fid=$fid");
            exit;
        } else {
            log_and_jump(__FILE__, __LINE__, "插入weto_posts表出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
    } else {
        log_and_jump(__FILE__, __LINE__, "插入weto_articles表出错：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    }
} else {
    $tmpObj = new Template();

    $tmpObj->new_article = false;
    if(isset($_GET['action']) && $_GET['action'] == 'new') {
        if(!isset($_SESSION['username'])) {
            header('Location: user.php?action=login');
            $mysqli->close();
            exit;
        }

        if(can_access($mysqli) != 1) {
            $mysqli->close();
            exit;
        } else {
            $tmpObj->new_article = true;
        }
    }

    if(!$tmpObj->new_article) {         // display页面， 获取article list for this $fid
        // 关闭已过期版主申请者
        $now = time();
        $query = "update weto_preadmins set overdue=1 where fid=$fid and time+".PREADMINS_OVERDUE." <= $now";
        if(!$result = $mysqli->query($query)) {
            log_and_jump(__FILE__, __LINE__, "更新失败：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
        // 获取版主名单
        $adminlist_str = '';
        $adminlist = array();
        $query = "select adminlist from weto_forums where id=$fid and closed=0 limit 1";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            if(!is_null($row)) {
                $adminlist_str = $row['adminlist'];
                trim($row['adminlist'], ',') != '' && $adminlist = explode(',', trim($row['adminlist'], ','));
            }
        } else {
            log_output(__FILE__, __LINE__, "查询出错：$query");
        }
        $tmpObj->adminlist_str = $adminlist_str;
        $tmpObj->adminlist = $adminlist;
        // 获取版主申请名单
        $preadmins = array();
        $query = "select id, username from weto_preadmins where fid=$fid and overdue=0 order by time";
        if($result = $mysqli->query($query)) {
            while($row = $result->fetch_assoc()) {
                $preadmins[] = $row;
            }
            $result->free();
        } else {
            log_output(__FILE__, __LINE__, "查询出错：$query");
        }
        // 获取版主申请支持者名单
        foreach($preadmins as &$value) {
            if(!empty($value)) {
                $query = "select username, time from weto_support_preadmins where paid=$value[id] order by time";
                if($result = $mysqli->query($query)) {
                    while($row = $result->fetch_assoc()) {
                        $value['supporters'][] = $row;
                    }
                    $result->free();
                } else {
                    log_output(__FILE__, __LINE__, "查询出错：$query");
                }
            }
        }
        $tmpObj->preadmins = $preadmins;
        // 获取page_arr
        $page_url = "forum.php?fid=$fid&";
        $page_arr = array(
            'page_url'      => $page_url,
            'cur_page'      => 1,
            'total_page'    => 1
        );
        $query = "select count(*) as count from weto_articles where fid = $fid and deleted = 0";
        if(get_page_para($query, $mysqli, $page_arr)) {
            $tmpObj->page = $page_arr;
        } else {
            log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }

        $tmpObj->articles = array();
        $limit = " limit " . ($page_arr['cur_page'] - 1) * ITEMS_PER_PAGE. ", " . ITEMS_PER_PAGE;
        $query = "select wa.id, fid, forumname, title, author, views, replies, top, digest, attachment, createtime, lastpost, lastposter
                  from weto_articles wa, weto_forums wf where fid = $fid and deleted = 0 and closed = 0 and wa.fid = wf.id
                  order by top desc, createtime desc" . $limit;
        if($result = $mysqli->query($query)) {
            while($row = $result->fetch_assoc()) {
                $row['title'] = htmlspecialchars($row['title']);
                $tmpObj->articles[] = $row;
            }
            $result->free();
        } else {
            log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }

        $tmpObj->can_post = true;
        $tmpObj->can_reply = false;
    }

    $db_seo_arr = get_seo('weto_forums', $fid);
    $tmpObj->seo_arr = $db_seo_arr === false ? $meta_arr : $db_seo_arr;
    $tmpObj->fid = $fid;
    $tmpObj->forumname = $forumname;

    $tmpObj->rec_vegs = get_veg_price();

    $tmpObj->display('forum.tpl');
}

$mysqli->close();