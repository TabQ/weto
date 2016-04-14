<?php
define('SITE_ROOT', str_replace('\\', '/', dirname(__FILE__)));
define('FORBIDDEN_TIME', 7 * 24 * 3600);
define('PREFORUM_OVERDUE', 10 * 24 * 3600);
define('PREADMINS_OVERDUE', 10 * 24 * 3600);
define('ITEMS_PER_PAGE', 50);
define('MAX_AVATAR_INDEX', 15);
define('MAX_SUPPORTERS', 50);       // 申请版面最大支持者数
define('MAX_SUPPORT_PREADMINS', 30);    // 申请版主最大支持者数
define('MAX_ADMINS', 3);            // 版主最多数目

$_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
$site_folder = SITE_ROOT == $_SERVER['DOCUMENT_ROOT'] ? '' : substr(SITE_ROOT, strlen($_SERVER['DOCUMENT_ROOT']) + 1);
define('SITE_FOLDER', $site_folder);

$dbArr = array(
    'server'    => 'localhost',
    'user'      => 'tabq111_root',
    'password'  => 'lhl12345',
    'db_name'   => 'tabq111_weto',
);

$meta_arr = array(
    'seotitle'      => '微兔社区',
    'keywords'      => '微兔社区',
    'description'   => '微兔社区',
);

$chart_arr = array(
    'fillColor'         => "rgba(255,255,255,0)",
    'strokeColor'       => "rgba(241,158,194,1)",
    'pointColor'        => "rgba(241,158,194,1)",
    'pointStrokeColor'  => "#fff"
);