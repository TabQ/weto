<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/2/8
 * Time: 12:33
 * Note: 身份验证（编辑限于发贴者自己；删除限于发贴者或管理员；置顶、精华操作限于管理员）
 */
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();

if(!isset($_GET['aid'])) {
    log_and_jump(__FILE__, __LINE__, "访问非法页面：" . $_SERVER['REQUEST_URI'], "errorpages/404.html");
    exit;
}
$aid = $_GET['aid'];

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
    $mysqli->close();
    exit;
}
// 判断aid是否有效
$query = "select count(*) as count from weto_articles wa, weto_forums wf where wa.id = $aid and wa.fid = wf.id and closed = 0 and deleted = 0";
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

    $now = time();

    if($_GET['action'] == 'edit') {
        // 身份验证（编辑只允许发贴者自己）
        $is_author = is_author($mysqli);
        if($is_author == 0) {
            log_and_jump(__FILE__, __LINE__, "$_SESSION[username]非法操作：$_SERVER[REQUEST_URI]", "errorpages/noaccess.html");
            $mysqli->close();
            exit;
        } elseif($is_author == -1) {
            $mysqli->close();
            exit;
        }

        // 编辑article title
        if(!empty($_POST['title'])) {
            if(!get_magic_quotes_gpc()) {
                $_POST['title'] = addslashes($_POST['title']);
            }
            $query = "update weto_articles set title='$_POST[title]', edittime=$now where id=$aid";
            if(!$result = $mysqli->query($query)) {
                log_and_jump(__FILE__, __LINE__, "更新weto_articles表出错：$query", "errorpages/opterror.html");
                $mysqli->close();
                exit;
            }
        }

        // 为90起航主机怪异行为做处理
        $_POST['message'] = stripslashes($_POST['message']);

        $attachment = 0;
        if(preg_match_all('/<img[^\/]+?src="(\/uploads\/.+?)".*?\/>/i', $_POST['message'], $matches, PREG_SET_ORDER)) {
            $_POST['message'] = preg_replace('/<img[^\/]+?src="(\/uploads\/.+?)".*?\/>/i', '<p><a href="$1"><img src="$1" width="400px" height="300px" /></a></p><br />', $_POST['message']);
            $attachment = 1;
        }
        if(!get_magic_quotes_gpc()) {
            $content = addslashes($_POST['message']);
        } else {
            $content = $_POST['message'];
        }
        $query = "update weto_posts set message='$content', edittime=$now, attachment=$attachment where id=$_GET[pid]";
        if($result = $mysqli->query($query)) {
            // 更新附件
            if($attachment) {
                $query = "delete from weto_attachments where aid=$_GET[aid] and pid=$_GET[pid]";
                if($result = $mysqli->query($query)) {
                    // 插入附件
                    foreach($matches as $match) {
                        $query = "insert into weto_attachments(aid, pid, path, uid) values($aid, $_GET[pid], '$match[1]', $_SESSION[uid])";
                        if(!$result = $mysqli->query($query)) {
                            log_and_jump(__FILE__, __LINE__, "插入weto_attachments表出错：$query", "errorpages/opterror.html");
                            $mysqli->close();
                            exit;
                        }
                    }

                } else {
                    log_and_jump(__FILE__, __LINE__, "删除失败：$query", "errorpages/opterror.html");
                    $mysqli->close();
                    exit;
                }
            }

            header("Location: /article.php?aid=$aid");
            $mysqli->close();
            exit;

        } else {
            log_and_jump(__FILE__, __LINE__, "更新失败：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
    }

    // 为90起航主机怪异行为做处理
    $_POST['message'] = stripslashes($_POST['message']);

    // 发表回复
    $attachment = 0;
    if(preg_match_all('/<img[^\/]+?src="(\/uploads\/.+?)".*?\/>/i', $_POST['message'], $outs, PREG_SET_ORDER)) {
        $_POST['message'] = preg_replace('/<img[^\/]+?src="(\/uploads\/.+?)".*?\/>/i', '<p><a href="$1"><img src="$1" width="400px" height="300px" /></a></p><br />', $_POST['message']);
        $attachment = 1;
    }

    if(!get_magic_quotes_gpc()) {
        $content = addslashes($_POST['message']);
    } else {
        $content = $_POST['message'];
    }

    $uid = $_SESSION['uid'];
    $username = $_SESSION['username'];
    $query = "insert into weto_posts(aid, uid, author, message, createtime, edittime, attachment)
              values($aid, $uid, '$username', '$content', $now, $now, $attachment)";
    if($result = $mysqli->query($query)) {
        // 插入附件
        if($attachment) {
            $pid = $mysqli->insert_id;
            foreach($outs as $out) {
                $query = "insert into weto_attachments(aid, pid, path, uid) values($aid, $pid, '$out[1]', $uid)";
                if(!$result = $mysqli->query($query)) {
                    log_and_jump(__FILE__, __LINE__, "插入weto_attachments表出错：$query", "errorpages/opterror.html");
                    $mysqli->close();
                    exit;
                }
            }
        }
        // 更新articles
        $query = "update weto_articles set replies=replies+1, lastpost=$now, lastposter='$username' where id=$aid";
        if(!$result = $mysqli->query($query)) {
            log_output(__FILE__, __LINE__, "更新weto_articles表出错：$query");
        }
        // 更新posts of forums
        $query = "update weto_forums set posts=posts+1 where id=(select fid from weto_articles where id=$aid)";
        if(!$result = $mysqli->query($query)) {
            log_output(__FILE__, __LINE__, "更新weto_forums表出错：$query");
        }
        // 更新weto_users
        $query = "update weto_users set posts=posts+1, credits=credits+2 where id=$uid";
        if(!$result = $mysqli->query($query)) {
            log_output(__FILE__, __LINE__, "更新失败：$query");
        }

        $mysqli->close();
        header("Location: article.php?aid=$aid");
        exit;
    } else {
        log_and_jump(__FILE__, __LINE__, "插入/更新weto_posts表出错：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    }
} else {
    $tmpObj = new Template();

    // 如果登陆
    if(isset($_SESSION['username'])) {
        $is_admin = is_admin($mysqli);
    }

    if(isset($_GET['action']) && in_array($_GET['action'], array('new', 'edit', 'delete', 'top', 'notop', 'digest', 'nodigest'))) {
        if(!isset($_SESSION['username'])) {
            header('Location: user.php?action=login');
            $mysqli->close();
            exit;
        }

        if(can_access($mysqli) != 1) {
            $mysqli->close();
            exit;
        } else {
            $tmpObj->action = $_GET['action'];
        }

        // 身份验证
        if($_GET['action'] == 'edit') {                     // 编辑限于发贴者自己

            $is_author = is_author($mysqli);
            if($is_author == 0) {
                log_and_jump(__FILE__, __LINE__, "$_SESSION[username]非法操作：$_SERVER[REQUEST_URI]", "errorpages/noaccess.html");
                $mysqli->close();
                exit;
            } elseif($is_author == -1) {
                $mysqli->close();
                exit;
            }

        } elseif($_GET['action'] == 'delete') {           // 删除限于发贴者或管理员

            $is_author = is_author($mysqli);
            if($is_author == 1 || $is_admin == 1) {
                // 通过
            } elseif($is_author == 0 || $is_admin == 0) {
                log_and_jump(__FILE__, __LINE__, "$_SESSION[username]非法操作：$_SERVER[REQUEST_URI]", "errorpages/noaccess.html");
                $mysqli->close();
                exit;
            } elseif($is_author == -1 || $is_admin == -1) {
                $mysqli->close();
                exit;
            }

        } elseif(in_array($_GET['action'], array('top', 'notop', 'digest', 'nodigest'))) {      // 置顶、精华操作限于管理员

            if($is_admin == 0) {
                log_and_jump(__FILE__, __LINE__, "$_SESSION[username]非法操作：$_SERVER[REQUEST_URI]", "errorpages/noaccess.html");
                $mysqli->close();
                exit;
            } elseif($is_admin == -1) {
                $mysqli->close();
                exit;
            }
            // 置顶与精华定义为互斥操作
            $query = "select top, digest from weto_articles where id=$aid limit 1";
            if($result = $mysqli->query($query)) {
                $row = $result->fetch_assoc();
                $result->free();
                if(($_GET['action'] == 'top' && $row['digest'] == 1) || ($_GET['action'] == 'digest' && $row['top'] == 1)) {
                    log_and_jump(__FILE__, __LINE__, $_SESSION['username']."试图执行非法操作：".$_SERVER['REQUEST_URI'], "errorpages/noaccess.html");
                    $mysqli->close();
                    exit;
                }
            } else {
                log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
                $mysqli->close();
                exit;
            }
        }
    }

    $location = "Location: /article.php?aid=$aid";      // 成功跳转地址
    $now = time();      // 记录时刻

    if(!isset($tmpObj->action)) {           // display页面，获取article_list
        // 更新articles中的views
        $query = "update weto_articles set views=views+1 where id=$aid";
        if(!$result = $mysqli->query($query)) {
            log_output(__FILE__, __LINE__, "更新weto_articles表出错：$query");
        }
        // 获取article
        $query = "select wa.id, fid, forumname, title, author, views, replies, top, digest, createtime, edittime
from weto_articles wa, weto_forums wf where wa.id = $aid and deleted = 0 and wa.fid = wf.id and closed = 0";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            if(is_null($row)) {
                log_and_jump(__FILE__, __LINE__, "访问非法页面：" . $_SERVER['REQUEST_URI'], "errorpages/404.html");
                $mysqli->close();
                exit;
            }

            $tmpObj->can_post = true;
            $tmpObj->can_reply = true;
            $tmpObj->fid = $row['fid'];

            $row['title'] = htmlspecialchars($row['title']);

            $tmpObj->article = $row;

            // 获取page_arr
            $page_url = "article.php?aid=$aid&";
            $page_arr = array(
                'page_url'      => $page_url,
                'cur_page'      => 1,
                'total_page'    => 1
            );
            $query = "select count(*) as count from weto_posts where aid = $aid and deleted = 0";
            if(get_page_para($query, $mysqli, $page_arr)) {
                $tmpObj->page = $page_arr;
            } else {
                log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
                $mysqli->close();
                exit;
            }

            $tmpObj->posts = array();
            $limit = " limit " . ($page_arr['cur_page'] - 1) * ITEMS_PER_PAGE . ", " . ITEMS_PER_PAGE;
            $query = "select wp.id, firstpost, wp.author, message, wu.avatar, wu.nickname, wu.credits, wu.digests, wu.posts, wu.articles,
                      wp.createtime, wp.edittime from weto_articles wa, weto_posts wp, weto_users wu where wp.aid = $aid
                      and wa.id = wp.aid and wa.deleted = 0 and wp.uid=wu.id and wp.deleted = 0 order by wp.createtime asc" . $limit;
            if($result = $mysqli->query($query)) {
                $index = 0;
                while($row = $result->fetch_assoc()) {
                    $row['nickname'] = htmlspecialchars($row['nickname']);
                    $row['edited'] = $row['createtime'] == $row['edittime'] ? false : true;
                    $row['index'] = ++$index;

                    if(isset($_SESSION['username'])) {
                        $is_this_author = $row['author'] == $_SESSION['username'] ? true : false;
                        $row['can_edit'] = $is_this_author;
                        // 删除限于发贴者或管理员
                        if($is_this_author || $is_admin == 1) {
                            $row['can_delete'] = true;
                        } else {
                            $row['can_delete'] = false;
                        }

                        if($row['firstpost'] == 1) {
                            $row['top_digest'] = $is_admin == 1 ? true : false;
                        } else {
                            $row['top_digest'] = false;
                        }
                    } else {
                        $row['can_edit'] = $row['can_delete'] = $row['top_digest'] = false;
                    }

                    $tmpObj->posts[] = $row;
                }
                $result->free();

            } else {
                log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
                $mysqli->close();
                exit;
            }
        } else {
            log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
    } elseif($tmpObj->action == 'new') {        // 发表回复
        // 先引用原文
        $query = "select wa.id, title, views, replies, wa.author, message, avatar, nickname, credits, digests, posts, wa.createtime, wa.edittime from weto_articles wa, weto_posts wp, weto_users wu
where wa.id = $aid and wa.id = wp.aid and wa.uid = wu.id and wa.deleted = 0 and wp.deleted = 0 and wp.firstpost = 1";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            if(is_null($row)) {
                log_and_jump(__FILE__, __LINE__, "访问非法页面：" . $_SERVER['REQUEST_URI'], "errorpages/404.html");
                $mysqli->close();
                exit;
            }

            $tmpObj->article = $row;
        } else {
            log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
    } elseif($tmpObj->action == 'edit') {       // 编辑回复
        $query = "select id, aid, firstpost, message from weto_posts where id=$_GET[pid] and deleted=0 limit 1";
        if($result = $mysqli->query($query)) {
            $tmpObj->post = $row = $result->fetch_assoc();
            $tmpObj->post['message'] = htmlspecialchars(stripslashes($tmpObj->post['message']));
            $result->free();

            $tmpObj->firstpost = false;
            // 首贴
            if($row['firstpost'] == 1) {
                $query = "select title from weto_articles where id=$aid and deleted=0 limit 1";
                if($result = $mysqli->query($query)) {
                    $row = $result->fetch_assoc();
                    $tmpObj->title = htmlspecialchars(stripslashes($row['title']));
                    $result->free();
                    $tmpObj->firstpost = true;
                } else {
                    log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
                    $mysqli->close();
                    exit;
                }
            }
        } else {
            log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
    } elseif($_GET['action'] == 'delete') {
        // 首先获取fid及digest（是否删除的是精华文章）
        $digest = 0;
        $query = "select fid, digest from weto_articles where id=$_GET[aid] limit 1";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            if (!is_null($row)) {
                $fid = $row['fid'];
                $digest = $row['digest'];
            }
        } else {
            log_output(__FILE__, __LINE__, "查询出错：$query");
        }
        // 然后获取uid
        $query = "select uid from weto_posts where id=$_GET[pid] limit 1";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            if(!is_null($row)) {
                $uid = $row['uid'];
            }
        } else {
            log_output(__FILE__, __LINE__, "查询出错：$query");
        }
        // 查询是否首贴
        $firstpost = false;
        $query = "select firstpost from weto_posts where id=$_GET[pid] limit 1";
        if($result = $mysqli->query($query)) {
            $row = $result->fetch_assoc();
            $result->free();
            $row['firstpost'] == 1 && $firstpost = true;
        } else {
            log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
        /***删除的是文章***/
        if($firstpost) {
            // firstly query replies of this article
            $replies = 0;
            $query = "select replies from weto_articles where id=$aid limit 1";
            if($result = $mysqli->query($query)) {
                $row = $result->fetch_assoc();
                $result->free();
                if(!is_null($row) && !empty($fid)) {
                    // 更新weto_forums
                    $replies = $row['replies'];
                    $query = "update weto_forums set articles=articles-1, posts=posts-$replies where id=$fid";
                    if(!$result = $mysqli->query($query)) {
                        log_output(__FILE__, __LINE__, "更新失败：$query");
                    }
                }
            } else {
                log_output(__FILE__, __LINE__, "查询失败：$query");
            }
            // 更新weto_users
            $self_replies = 0;
            if(!empty($uid)) {
                $query = "select count(*) as count from weto_posts where deleted=0 and firstpost=0 and uid=$uid and aid=$aid";
                if($result = $mysqli->query($query)) {          // 先查询楼主自已回复自己的帖子数
                    $row = $result->fetch_assoc();
                    $result->free();
                    if(!is_null($row)) {
                        $self_replies = $row['count'];
                    }
                } else {
                    log_output(__FILE__, __LINE__, "查询失败：$query");
                }
                if($digest) {
                    $query = "update weto_users set articles=articles-1, posts=posts-$self_replies, digests=digests-1, credits=credits-100-10-2*$self_replies where id=$uid";
                } else {
                    $query = "update weto_users set articles=articles-1, posts=posts-$self_replies, credits=credits-10-2*$self_replies where id=$uid";
                }
                if(!$result = $mysqli->query($query)) {
                    log_output(__FILE__, __LINE__, "更新失败：$query");
                }
            }
            // 更新weto_posts
            $query = "update weto_posts set deleted=1 where aid=$aid";
            if(!$result = $mysqli->query($query)) {
                log_output(__FILE__, __LINE__, "更新失败：$query");
            }
            // 更新weto_articles
            $query = "update weto_articles set deleted=1 where id=$aid";
            if(!$result = $mysqli->query($query)) {
                log_and_jump(__FILE__, __LINE__, "更新出错：$query", "errorpages/opterror.html");
                $mysqli->close();
                exit;
            }

        } else {        /***删除的是帖子***/
            // 更新weto_articles
            $query = "update weto_articles set replies=replies-1 where id=$aid";
            if(!$result = $mysqli->query($query)) {
                log_output(__FILE__, __LINE__, "更新失败：$query");
            }
            // 先查询是否是最后回复并更新相应article中的lastpost及lastposter
            $query = "select id, author, createtime from weto_posts where aid=$aid and deleted=0 order by createtime desc limit 2";
            if($result = $mysqli->query($query)) {
                $lastpost_arr = array();
                while($row = $result->fetch_assoc()) {
                    $lastpost_arr[] = $row;
                }
                $result->free();
                if(count($lastpost_arr) == 2 && $lastpost_arr[0]['id'] == $_GET['pid']) {
                    $query = "update weto_articles set lastpost=".$lastpost_arr[1]['createtime'].", lastposter='".$lastpost_arr[1]['author']."' where id=$aid";
                    if(!$result = $mysqli->query($query)) {
                        log_output(__FILE__, __LINE__, "更新weto_articles出错：$query");
                    }
                }
            } else {
                log_output(__FILE__, __LINE__, "查询出错：$query");
            }
            // 更新weto_forums
            if(!empty($fid)) {
                $query = "update weto_forums set posts=posts-1 where id=$fid";
                if(!$result = $mysqli->query($query)) {
                    log_output(__FILE__, __LINE__, "更新失败：$query");
                }
            }
            // 更新weto_users
            if(!empty($uid)) {
                $query = "update weto_users set posts=posts-1, credits=credits-2 where id=$uid";
                if(!$result = $mysqli->query($query)) {
                    log_output(__FILE__, __LINE__, "更新失败：$query");
                }
            }
            // 更新weto_posts
            $query = "update weto_posts set deleted=1 where id=$_GET[pid]";
            if(!$result = $mysqli->query($query)) {
                log_and_jump(__FILE__, __LINE__, "更新出错：$query", "errorpages/opterror.html");
                $mysqli->close();
                exit;
            }
        }
        // 记录操作
        $query = "insert into weto_action(type, aid, pid, uid, time, isadmin) values('delete', $aid, $_GET[pid], $_SESSION[uid], $now, $is_admin)";
        if(!$result = $mysqli->query($query)) {
            log_output(__FILE__, __LINE__, "插入失败：$query");
        }

        // 成功，跳转
        if($firstpost) {
            if(!empty($fid)) {
                $location = "Location: /forum.php?fid=$fid";
            } else {
                $location = 'Location: /index.php';
            }
        }
        header($location);
        $mysqli->close();
        exit;
    } elseif($_GET['action'] == 'top') {
        $query = "update weto_articles set top=1 where id=$aid";
        if($result = $mysqli->query($query)) {
            header($location);
            // 记录操作
            $query = "insert into weto_action(type, aid, uid, time, isadmin) values('top', $aid, $_SESSION[uid], $now, 1)";
            if(!$result = $mysqli->query($query)) {
                log_output(__FILE__, __LINE__, "插入失败：$query");
            }

            $mysqli->close();
            exit;
        } else {
            log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
    } elseif($_GET['action'] == 'notop') {
        $query = "update weto_articles set top=0 where id=$aid";
        if($result = $mysqli->query($query)) {
            header($location);
            // 记录操作
            $query = "insert into weto_action(type, aid, uid, time, isadmin) values('notop', $aid, $_SESSION[uid], $now, 1)";
            if(!$result = $mysqli->query($query)) {
                log_output(__FILE__, __LINE__, "插入失败：$query");
            }

            $mysqli->close();
            exit;
        } else {
            log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
    } elseif($_GET['action'] == 'digest') {
        $query = "update weto_articles set digest=1 where id=$aid";
        if($result = $mysqli->query($query)) {
            // 查找uid
            $query = "select uid from weto_articles where id=$aid limit 1";
            if($result = $mysqli->query($query)) {
                $row = $result->fetch_assoc();
                $result->free();
                if(!is_null($row)) {
                    // 更新weto_users
                    $query = "update weto_users set digests=digests+1, credits=credits+100 where id=$row[uid]";
                    if(!$result = $mysqli->query($query)) {
                        log_output(__FILE__, __LINE__, "更新失败：$query");
                    }
                }
            } else {
                log_output(__FILE__, __LINE__, "查询失败：$query");
            }
            // 记录操作
            $query = "insert into weto_action(type, aid, uid, time, isadmin) values('digest', $aid, $_SESSION[uid], $now, 1)";
            if(!$result = $mysqli->query($query)) {
                log_output(__FILE__, __LINE__, "插入失败：$query");
            }

            header($location);
            $mysqli->close();
            exit;
        } else {
            log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
    } elseif($_GET['action'] == 'nodigest') {
        $query = "update weto_articles set digest=0 where id=$aid";
        if($result = $mysqli->query($query)) {
            // 查找uid
            $query = "select uid from weto_articles where id=$aid limit 1";
            if($result = $mysqli->query($query)) {
                $row = $result->fetch_assoc();
                $result->free();
                if(!is_null($row)) {
                    // 更新weto_users
                    $query = "update weto_users set digests=digests-1, credits=credits-100 where id=$row[uid]";
                    if(!$result = $mysqli->query($query)) {
                        log_output(__FILE__, __LINE__, "更新失败：$query");
                    }
                }
            } else {
                log_output(__FILE__, __LINE__, "查询失败：$query");
            }
            // 记录操作
            $query = "insert into weto_action(type, aid, uid, time, isadmin) values('nodigest', $aid, $_SESSION[uid], $now, 1)";
            if(!$result = $mysqli->query($query)) {
                log_output(__FILE__, __LINE__, "插入失败：$query");
            }

            header($location);
            $mysqli->close();
            exit;
        } else {
            log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
            $mysqli->close();
            exit;
        }
    }

    $tmpObj->seo_arr = $meta_arr;

    $tmpObj->rec_vegs = get_veg_price();

    $tmpObj->display('article.tpl');
}

$mysqli->close();

/**
 * @param $mysqli
 * @return int
 */
function is_author($mysqli) {

    $query = "select author from weto_posts where id=$_GET[pid] limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();

        if(is_null($row)) {
            log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/404.html");
            return -1;
        } elseif(isset($_SESSION['username']) && $_SESSION['username'] == $row['author']) {
            return 1;
        } else {
            return 0;
        }

    } else {
        log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
        return -1;
    }
}

function is_admin($mysqli) {
    global $aid;
    $query = "select adminlist from weto_articles wa, weto_forums wf where wa.id=$aid and wa.deleted=0 and wa.fid=wf.id and wf.closed=0 limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
        $admin_list = explode(',', trim($row['adminlist'], ','));
        // admin及system作为系统管理员
        !in_array('admin', $admin_list) && $admin_list[] = 'admin';
        !in_array('system', $admin_list) && $admin_list[] = 'system';

        if(isset($_SESSION['username']) && in_array($_SESSION['username'], $admin_list)) {
            return 1;
        } else {
            return 0;
        }

    } else {
        log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
        return -1;
    }
}