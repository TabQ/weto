<div id="main">
    <div class="main_row">
        <div class="one_block" style="min-height: 0px; border: solid #346fa5 1px;">
            <div id="reply_view">
                <div class="inner_text" style="width:72px;text-align: right;">查看：<{echo $this->article['views']}></div>
                <div class="inner_text" style="width:72px;">|&nbsp;回复：<{echo $this->article['replies']}></div>
                <div class="clear"></div>
            </div>
            <div id="article_title">
                <div class="inner_text" style="width: 632px;padding-left: 8px;"><b><{echo $this->article['title']}></b></div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="main_row">
        <div class="one_block" style="border: solid #346fa5 1px;">
            <div id="poster">
                <div class="author_time"><div class="inner_text" style="width: 150px;margin-left: 12px;"><{echo $this->article['author']}></div></div>
                <div id="avatar">
                    <img src="./templates/img/avatar/<{echo $this->article['avatar']}>.png" width="128" height="128" />
                </div>
                <div id="poster_info">
                    <div class="user_detail"><{echo $this->article['nickname']}></div>
                    <div class="user_detail">积分：<{echo $this->article['credits']}></div>
                    <div class="user_detail">精华：<{echo $this->article['digests']}></div>
                    <div class="user_detail">帖子：<{echo $this->article['posts']}></div>
                </div>
            </div>
            <div id="post_detail">
                <div class="author_time">
                    <div class="inner_text" style="margin-left: 12px;">发表于：<{echo date('Y-m-d H:i:s', $this->article['createtime'])}></div>
                    <{if($this->article['createtime'] != $this->article['edittime'])}>
                    <div class="inner_text">编辑于：<{echo date('Y-m-d H:i:s', $this->article['edittime'])}></div>
                    <{/if}>
                    <div class="clear"></div>
                </div>
                <div id="post_content"><{echo $this->article['message']}></div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <form id="reply_form" method="post" action="article.php?aid=<{echo $this->article['id']}>">
        <div class="user_row">
            <textarea id="message" name="message" style="width:700px; height:300px"></textarea>
        </div>
        <div class="user_row">
            <div class="user_button_block"><input type="submit" value="确  定" class="user_button" id="confirm"/></div>
            <div class="user_button_block"><input type="reset" value="重  置" class="user_button "/></div>
            <div class="clear"></div>
        </div>
    </form>
</div>

<script charset="utf-8" src="/templates/thirdparty/kindeditor/kindeditor.js"></script>
<script charset="utf-8" src="/templates/thirdparty/kindeditor/lang/zh_CN.js"></script>
<script type="text/javascript">
    KindEditor.ready(function(K) {
        $('#confirm').click(function() {
            editor.sync();
            if(K.trim($('#message').val()) == '') {
                alert('内容不能为空');
                return false;
            }
        });
        var options = {
            items: ['fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', '|', 'emoticons', 'image'],
            allowImageRemote: false,
            uploadJson: '/upload_json.php'
        };
        var editor = K.create('textarea[name="message"]', options);
    });
</script>