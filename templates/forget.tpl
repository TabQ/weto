<{include "./templates/header.tpl"}>

<div id="frame">
    <div class="user_main">
        <form id="forget_form" method="post">
            <div class="user_row">
                <div class="user_hint"><span id="forget_msg"></span></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">用户名：</p></div>
                <div class="user_input"><input id="username" name="username" type="text" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">邮箱：</p></div>
                <div class="user_input"><input id="email" name="email" type="text" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_button_block"><input type="submit" value="确  定" class="user_button" /></div>
                <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
                <div class="clear"></div>
            </div>
        </form>
    </div>
    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $(function() {
        $('input').click(function() {
            $('#forget_msg').empty();
        });
        $('#forget_form').submit(function(e) {
            if($.trim($('#username').val()) == '' || $.trim($('#email').val()) == '') {
                $('#forget_msg').html('用户名和邮箱不能为空');
                return false;
            }

            $.post('forget.php', {
                username: $('#username').val(),
                email: $('#email').val()
            }, function(data, textStatus) {
                var obj = eval('('+decodeURI(data)+')');
                if(obj.code == 1) {
                    $('#forget_msg').html(obj.msg);
                    window.location.href = 'reset_pwd.php?username='+obj.username;
                } else {
                    $('#forget_msg').html(obj.msg);
                }
            });

            return e.preventDefault();
        });
    });
</script>

<{include "./templates/footer.tpl"}>