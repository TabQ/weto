<{include "./templates/header.tpl"}>

<div id="frame">
    <div class="user_main">
        <div class="user_div">
            <form id="chg_pwd_form" method="post">
                <div class="user_row">
                    <div class="user_hint"><span id="chg_pwd_msg"></span></div>
                    <div class="clear"></div>
                </div>
                <div class="user_row">
                    <div class="user_label"><p style="text-align: right">原始密码：</p></div>
                    <div class="user_input"><input id="old_pwd" name="old_pwd" type="password" /></div>
                    <div class="clear"></div>
                </div>
                <div class="user_row">
                    <div class="user_label"><p style="text-align: right">新密码：</p></div>
                    <div class="user_input"><input id="new_pwd" name="new_pwd" type="password" /></div>
                    <div class="clear"></div>
                </div>
                <div class="user_row">
                    <div class="user_label"><p style="text-align: right">重复密码：</p></div>
                    <div class="user_input"><input id="new_pwd2" name="new_pwd2" type="password" /></div>
                    <div class="clear"></div>
                </div>
                <div class="user_row">
                    <div style="float:left;"><input type="submit" value="确  定" class="user_button" /></div>
                    <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>
    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $(function() {
        $('input').click(function() {
            $('#chg_pwd_msg').empty();
        });
        $('#chg_pwd_form').submit(function(e) {
            if($.trim($('#old_pwd').val()) == '' || $.trim($('#new_pwd').val()) == '' || $.trim($('#new_pwd2').val()) == '') {
                $('#chg_pwd_msg').removeClass('user_msg_correct').addClass('user_msg_error').html('密码不能为空');
                return false;
            }

            $.post('chg_pwd.php', {
                old_pwd: $('#old_pwd').val(),
                new_pwd: $('#new_pwd').val(),
                new_pwd2: $('#new_pwd2').val()
            }, function(data, textStatus) {
                var obj = eval('('+decodeURI(data)+')');
                if(obj.code == 1) {
                    $('#chg_pwd_msg').removeClass('user_msg_error').addClass('user_msg_correct').html(obj.msg);
                    window.location.href = 'logout.php';
                } else {
                    $('#chg_pwd_msg').removeClass('user_msg_correct').addClass('user_msg_error').html(obj.msg);
                }
            });

            return e.preventDefault();
        });
    });
</script>

<{include "./templates/footer.tpl"}>