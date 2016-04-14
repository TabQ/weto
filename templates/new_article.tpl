<div class="user_main">
    <form id="forum_form" method="post" action="forum.php?fid=<{echo $this->fid}>">
        <p>标题：</p>
        <div class="user_row">
            <input id="forum_title" name="forum_title" type="text" style="width: 400px; height: 20px;" /><span>（不超过80个字）</span>
        </div>
        <p>内容：</p>
        <div class="user_row">
            <textarea id="new_article" name="new_article" style="width:700px; height:500px"></textarea>
        </div>
        <div class="user_row">
            <div class="user_button_block"><input type="submit" value="确  定" class="user_button" id="confirm" /></div>
            <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
            <div class="clear"></div>
        </div>
    </form>
</div>

<script charset="utf-8" src="/templates/thirdparty/kindeditor/kindeditor.js"></script>
<script charset="utf-8" src="/templates/thirdparty/kindeditor/lang/zh_CN.js"></script>
<script type="text/javascript">
    KindEditor.ready(function(K) {
        $('#confirm').click(function() {
            if(K.trim($('#forum_title').val()) == '') {
                alert('标题不能为空');
                return false;
            } else if(K.trim($('#forum_title').val()).length > 80) {
                alert('标题不能超过80个字');
                return false;
            }
            editor.sync();
            if(K.trim($('#new_article').val()) == '') {
                alert('内容不能为空');
                return false;
            }
        });
        var options = {
            items: ['fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', '|', 'emoticons', 'image'],
            allowImageRemote: false,
            uploadJson: '/upload_json.php'
        };
        var editor = K.create('textarea[name="new_article"]', options);
    });
</script>