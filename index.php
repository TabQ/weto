<?php
include "config.php";
include "template.class.php";
include_once "functions.php";

session_start();

$start_time = time() - 30 * 24 * 3600;

$mysqli = new mysqli($dbArr['server'], $dbArr['user'], $dbArr['password'], $dbArr['db_name']);
if($mysqli->connect_errno) {
    log_and_jump(__FILE__, __LINE__, "数据库连接失败：$mysqli->connect_error", "errorpages/500.html");
    $mysqli->close();
    exit;
}

$tmpObj = new Template();

// 已入选过的article id
$ids_arr = array();

// 获取首页图片
$home_imgs = $tmpObj->top_imgs = $tmpObj->w_imgs = array();
$query = "select wa.id, fid, forumname, title from weto_articles wa, weto_forums wf where top=0 and attachment=1 and deleted=0 and lastpost>$start_time and closed=0
          and wa.fid=wf.id order by replies desc limit 7";
if($result = $mysqli->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $home_imgs[] = $row;
    }
    $result->free();
} else {
    log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
    $mysqli->close();
    exit;
}
$total = count($home_imgs);
for($i=0; $i<$total; $i++) {
    $query = "select path from weto_attachments wt, weto_posts wp where wp.aid=".$home_imgs[$i]['id']." and firstpost=1 and deleted=0 and wp.id=wt.pid limit 1";
    if($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $result->free();
        if($i < 3) {
            $row['path'] = create_home_image(true, SITE_ROOT . $row['path']);
            if($row['path'] != false) {
                $home_imgs[$i]['path'] = $row['path'];
                $tmpObj->top_imgs[] = $home_imgs[$i];

                $ids_arr[] = $home_imgs[$i]['id'];
            }
        } else {
            $row['path'] = create_home_image(false, SITE_ROOT . $row['path'], 192, 144);
            if($row['path'] != false) {
                $home_imgs[$i]['path'] = $row['path'];
                $tmpObj->w_imgs[] = $home_imgs[$i];

                $ids_arr[] = $home_imgs[$i]['id'];
            }
        }
    } else {
        log_and_jump(__FILE__, __LINE__, "查询错误：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    }
}

$tmpObj->local_hot = array('id'=>'', 'title'=>'', 'message'=>'', 'url' => '#');
// 获取本地热点
$query = "select wa.id, wa.fid, title, message from weto_articles wa, weto_posts wp, weto_forums wf where wf.forumname='本地热点' and wf.closed=0 and wf.id=wa.fid
and wa.deleted=0 and wa.id=wp.aid and wa.lastpost>$start_time and wp.firstpost=1 and wp.deleted=0 and wa.top=0 and wa.digest=1 order by wa.replies desc limit 1";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    $result->free();

    if(!is_null($row)) {
        $tmpObj->local_hot['id'] = $row['id'];
        $tmpObj->local_hot['title'] = htmlspecialchars($row['title']);
        $row['message'] = htmlspecialchars($row['message']);
        $tmpObj->local_hot['message'] = mb_substr($row['message'], 0, 300) . ' …';
        $tmpObj->local_hot['url'] = "/forum.php?fid=$row[fid]";

        $ids_arr[] = $row['id'];
    } else {
        $query = "select id from weto_forums where forumname='本地热点' and closed=0 limit 1";
        if($result = $mysqli->query($query)) {
            while($row = $result->fetch_assoc()) {
                $tmpObj->local_hot['url'] = "/forum.php?fid=$row[id]";
            }
            $result->free();
        } else {
            log_output(__FILE__, __LINE__, "查询错误：$query");
        }
    }

} else {
    log_output(__FILE__, __LINE__, "查询错误：$query");
}
// 获取民生热点
$tmpObj->life_hot = array('id'=>'', 'title'=>'', 'message'=>'', 'url' => '#');
$query = "select wa.id, wa.fid, title, message from weto_articles wa, weto_posts wp, weto_forums wf where wf.forumname='民生热点' and wf.closed=0 and wf.id=wa.fid
and wa.deleted=0 and wa.id=wp.aid and wa.lastpost>$start_time and wp.firstpost=1 and wp.deleted=0 and wa.top=0 and wa.digest=1 order by wa.replies desc limit 1";
if($result = $mysqli->query($query)) {
    $row = $result->fetch_assoc();
    $result->free();

    if(!is_null($row)) {
        $tmpObj->life_hot['id'] = $row['id'];
        $tmpObj->life_hot['title'] = htmlspecialchars($row['title']);
        $row['message'] = htmlspecialchars($row['message']);
        $tmpObj->life_hot['message'] = mb_substr($row['message'], 0, 300) . ' …';
        $tmpObj->life_hot['url'] = "/forum.php?fid=$row[fid]";

        $ids_arr[] = $row['id'];
    } else {
        $query = "select id from weto_forums where forumname='民生热点' and closed=0 limit 1";
        if($result = $mysqli->query($query)) {
            while($row = $result->fetch_assoc()) {
                $tmpObj->life_hot['url'] = "/forum.php?fid=$row[id]";
            }
            $result->free();
        } else {
            log_output(__FILE__, __LINE__, "查询错误：$query");
        }
    }

} else {
    log_output(__FILE__, __LINE__, "查询错误：$query");
}

// 业主新闻
$tmpObj->owner_news = array();
$query = "select wf.forumname, wa.id, wa.fid, wa.title from weto_articles wa, weto_forums wf where wf.bid=6 and wf.closed=0
and wf.id=wa.fid and wa.deleted=0 and wa.top=0 and wa.lastpost>$start_time order by replies desc limit 10";
if($result = $mysqli->query($query)) {
    while($row = $result->fetch_assoc()) {
        $row['title'] = htmlspecialchars($row['title']);
        $tmpObj->owner_news[] = $row;
    }
    $result->free();
} else {
    log_output(__FILE__, __LINE__, "查询错误：$query");
    $mysqli->close();
    exit;
}

// 主十大
$tmpObj->top10_rr = array();
$query = "select wf.forumname, wa.id, wa.fid, wa.title from weto_articles wa, weto_forums wf where wf.bid in(1,2,3,4) and wf.closed=0
and wf.id=wa.fid and wa.deleted=0 and wa.top=0 and wa.lastpost>$start_time order by replies desc limit 10";
if($result = $mysqli->query($query)) {
    while($row = $result->fetch_assoc()) {
        $row['title'] = htmlspecialchars($row['title']);
        $tmpObj->top10_arr[] = $row;

        $ids_arr[] = $row['id'];
    }
    $result->free();
} else {
    log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
    $mysqli->close();
    exit;
}

// 各版块十大
$ids_str = implode(',', $ids_arr);
for($bid = 1; $bid <= 6; $bid++) {
    if($bid <=4 ) {
        $tmpObj->topics_arr[$bid] = array();
        if(!empty($ids_str)) {
            $query = "select wf.forumname, wa.id, wa.fid, wa.title from weto_articles wa, weto_forums wf where wf.bid=$bid and  wf.closed=0 and wf.id=wa.fid
and wa.deleted=0 and wa.top=0 and wa.id not in($ids_str) and wa.lastpost>$start_time order by replies desc limit 10";
            if($result = $mysqli->query($query)) {
                while($row = $result->fetch_assoc()) {
                    $row['title'] = htmlspecialchars($row['title']);
                    $tmpObj->topics_arr[$bid][] = $row;
                }
                $result->free();
            } else {
                log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
                $mysqli->close();
                exit;
            }
        }
    }

    $tmpObj->boards_arr[$bid] = array();
    $query = "select wf.id, wf.forumname from weto_blocks wb, weto_forums wf where wf.bid=$bid and wf.closed=0 and wf.bid=wb.id
and wb.closed=0 order by posts desc, wf.rank asc limit 10";
    if($result = $mysqli->query($query)) {
        while($row = $result->fetch_assoc()) {
            $tmpObj->boards_arr[$bid][] = $row;
        }
        $result->free();
    } else {
        log_and_jump(__FILE__, __LINE__, "查询出错：$query", "errorpages/opterror.html");
        $mysqli->close();
        exit;
    }
}

$mysqli->close();

$db_seo_arr = get_seo('weto_site', 1);
$tmpObj->seo_arr = $db_seo_arr === false ? $meta_arr : $db_seo_arr;

$tmpObj->rec_vegs = get_veg_price();

$tmpObj->display('index.tpl');

function create_home_image($top, $src_file, $forced_width = 398, $forced_height = 270, $r = 0xF2, $g = 0xF2, $b = 0xF2) {
    $dir = SITE_ROOT . '/uploads/home_images/';
    !file_exists($dir) && mkdir($dir, 0664, true);
    $des_url = $top ? '/uploads/home_images/' . 'top_' . basename($src_file) : '/uploads/home_images/' . basename($src_file);
    $des_file = SITE_ROOT . $des_url;

    if(scale_image($src_file, $des_file, $forced_width, $forced_height, $r, $g, $b)) {
        return $des_url;
    } else {
        return false;
    }
}