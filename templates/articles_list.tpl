<div id="articles_header">
    <div class="article_category">类别</div>
    <div class="article_name">版面</div>
    <div class="article_subject">主题</div>
    <div class="article_create_time">发贴时间</div>
    <div class="article_author">作者</div>
    <div class="article_reply_read">回复/查看</div>
    <div class="article_reply_time">最新回复</div>
    <div class="article_reply_author">作者</div>
    <div class="clear"></div>
</div>
<{foreach($this->articles as $article)}>
    <div class="article_item">
        <div class="article_category">
            <a href="/article.php?aid=<{echo $article['id']}>">
                <{if(!empty($article['top']))}><img src="/templates/img/top.png" title="置顶" />
                <{elseif(!empty($article['digest']))}><img src="/templates/img/digest.png" title="精华" />
                <{elseif($article['replies'] > 100)}><img src="/templates/img/hot.png" title="主题" />
                <{else}><img src="/templates/img/msg.png" title="主题" />
                <{/if}>
            </a>
        </div>
        <div class="article_name">
            <a href="/forum.php?fid=<{echo $article['fid']}>" title="<{echo $article['forumname']}>"><{echo $article['forumname']}></a>
        </div>
        <div class="article_subject">
            <a href="/article.php?aid=<{echo $article['id']}>"<{if($article['replies'] > 100 || !empty($article['top']))}>style="color:red;"<{/if}>title="<{echo $article['title']}>"><{echo $article['title']}></a>
        </div>
        <div class="article_create_time"><{echo date('Y-m-d', $article['createtime'])}></div>
        <div class="article_author"><{echo $article['author']}></div>
        <div class="article_reply_read"><{echo $article['replies'].'/'.$article['views']}></div>
        <div class="article_reply_time"><{echo date('Y-m-d', $article['lastpost'])}></div>
        <div class="article_reply_author"><{echo $article['lastposter']}></div>
        <div class="clear"></div>
    </div>
<{/foreach}>