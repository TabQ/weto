<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta name="description" content="<{echo $this->seo_arr['description']}>" />
    <meta name="keywords" content="<{echo $this->seo_arr['keywords']}>" />
    <title><{echo $this->seo_arr['seotitle']}></title>
    <link href="/templates/css/style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/templates/js/jquery-1.3.1.min.js"></script>
</head>

<body>
<div id="top_bar">
    <div style="width: 300px;">
        <a href="#" title="微兔社区"><p><b>微兔社区</b></p><p style="font-size: 29px;margin-left:2px;margin-top:-12px;"><b>we</b> are <b>to</b>gether</p></a>
    </div>
</div>
<div id="top_nav">
    <{if(empty($this->admin))}>
    <ul class="nav">
        <li><a href="/index.php">首页</a></li>
        <li><a href="/blocks.php?bid=1">业主之家</a></li>
        <li><a href="/blocks.php?bid=2">娱乐健身</a></li>
        <li><a href="/blocks.php?bid=3">社会信息</a></li>
        <li><a href="/blocks.php?bid=4">本地信息</a></li>
        <li><a href="/blocks.php?bid=6">业主广场</a></li>
    </ul>
    <{else}>
    <ul class="nav">
        <li><a href="/admin/index.php" class="curr" class="first">基本信息</a></li>
        <li><a href="/admin/blocks.php">版块设置</a></li>
        <li><a href="/admin/forums.php" class="last">论坛设置</a></li>
    </ul>
    <{/if}>
</div>