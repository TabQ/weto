<div class="main_row">
    <div class="one_block" style="min-height: 0px; border: solid #c2d5e3 2px;">
        <div id="reply_view">
            <div class="inner_text" style="width:72px;text-align: right;">查看：<{echo $this->article['views']}></div>
            <div class="inner_text" style="width:72px;">|&nbsp;回复：<{echo $this->article['replies']}></div>
            <div class="clear"></div>
        </div>
        <div id="article_title">
            <div class="inner_text" style="width: 632px;padding-left: 8px;"><b><{echo "[".$this->article['forumname']."]"}>&nbsp;<{echo $this->article['title']}></b></div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<{foreach($this->posts as $post)}>
<div class="main_row">
    <div class="one_block" style="border: solid #c2d5e3 2px;">
        <div id="poster">
            <div class="author_time"><div class="inner_text" style="width: 150px;margin-left: 12px;"><{echo $post['author']}></div></div>
            <div id="avatar">
                <img src="./templates/img/avatar/<{echo $post['avatar']}>.png" width="128" height="128" />
            </div>
            <div id="poster_info">
                <div class="user_detail"><{echo $post['nickname']}></div>
                <div class="user_detail">积分：<{echo $post['credits']}></div>
                <div class="user_detail">精华：<{echo $post['digests']}></div>
                <div class="user_detail">文章：<{echo $post['articles']}></div>
                <div class="user_detail">帖子：<{echo $post['posts']}></div>
            </div>
        </div>
        <div id="post_detail">
            <div class="author_time">
                <div class="inner_text" style="margin-left: 12px;">发表于：<{echo date('Y-m-d H:i:s', $post['createtime'])}></div>
                <{if($post['edited'])}>
                <div class="inner_text">编辑于：<{echo date('Y-m-d H:i:s', $post['edittime'])}></div>
                <{/if}>
                <div class="inner_text" style="float: right"><{echo $post['index']}>#</div>
                <div class="clear"></div>
            </div>
            <div id="post_content"><{echo $post['message']}></div>
            <div id="post_bottom">
                <{if($post['can_edit'])}>
                <input type="button" id="edit_reply" name="edit_reply" value="编辑" style="margin: 8px;" /><input type="hidden" id="pid" name="pid" value="<{echo $post['id']}>" />
                <{/if}>
                <{if($post['can_delete'])}>
                <input type="button" id="delete_reply" name="delete_reply" value="删除" style="margin: 8px;" /><input type="hidden" id="pid" name="pid" value="<{echo $post['id']}>" />
                <{/if}>
                <{if($post['top_digest'])}>
                <{if($this->article['top'] == 0 && $this->article['digest'] == 0)}>
                <input type="button" id="top" name="top" value="置顶" style="margin: 8px;" />
                <input type="button" id="digest" name="digest" value="精华" style="margin: 8px;" />
                <{elseif($this->article['top'] == 1)}>
                <input type="button" id="notop" name="notop" value="取消置顶" style="margin: 8px;" />
                <{elseif($this->article['digest'] == 1)}>
                <input type="button" id="nodigest" name="nodigest" value="取消精华" style="margin: 8px;" />
                <{/if}>
                <{/if}>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<{/foreach}>

<script type="text/javascript">
    $(function() {
        $('input[id=edit_reply]').click(function() {
            window.location.href="/article.php?aid=<{echo $this->article['id']}>&pid=" + $(this).next().val() + "&action=edit";
        });
        $('input[id=delete_reply]').click(function() {
            if(confirm("确定删除？") == true) {
                window.location.href="/article.php?aid=<{echo $this->article['id']}>&pid=" + $(this).next().val() + "&action=delete";
            }
        });
        $('input[id=top]').click(function() {
            window.location.href="/article.php?aid=<{echo $this->article['id']}>&action=top";
        });
        $('input[id=notop]').click(function() {
            window.location.href="/article.php?aid=<{echo $this->article['id']}>&action=notop";
        });
        $('input[id=digest]').click(function() {
            window.location.href="/article.php?aid=<{echo $this->article['id']}>&action=digest";
        });
        $('input[id=nodigest]').click(function() {
            window.location.href="/article.php?aid=<{echo $this->article['id']}>&action=nodigest";
        });
    });
</script>