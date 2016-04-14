<div class="user_main">
    <form id="edit_form" method="post" action="article.php?aid=<{echo $this->post['aid']}>&pid=<{echo $this->post['id']}>&action=edit">
        <{if($this->firstpost)}>
        <p>标题：</p>
        <div class="user_row">
            <input id="title" name="title" type="text" style="width: 400px; height: 20px;" value="<{echo $this->title}>" /><span>（不超过80个字）</span>
        </div>
        <p>内容：</p>
        <{/if}>
        <div class="user_row">
            <textarea id="message" name="message" style="width:700px; height:500px"><{echo $this->post['message']}></textarea>
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
        var options = {
            items: ['fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', '|', 'emoticons', 'image'],
            allowImageRemote: false,
            uploadJson: '/upload_json.php'
        };
        var editor = K.create('textarea[name="message"]', options);
    });
</script>