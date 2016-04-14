<{include "./templates/header.tpl"}>

<div id="frame">
    <{include "./templates/reg_login.tpl"}>
    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $(function() {
        var verified_arr = {"username": false, "email": false, "password": false, "password2": false};
        var ajax_arr = {'username': false, 'email': false};
        $('#register_form input[id]').keypress(function () {
            var id = $(this).attr('id');
            var msg_id = '#' + id + '_msg';
            verified_arr[id] = false;
            $(msg_id).removeClass('user_msg_error').removeClass('user_msg_correct').removeClass('user_msg_wait').html('');
        }).blur(function () {
            var id = $(this).attr('id');
            var msg_id = '#' + id + '_msg';
            if(check_input_value(this)) {
                $(msg_id).removeClass('user_msg_error').removeClass('user_msg_correct').addClass('user_msg_wait').html(' 正在查询信息...');
                if(id == 'username' || id == 'email') {
                    var this_val = trim($(this).val());
                    var param = new Object();
                    param[id] = this_val;
                    $.post('user.php?which=' + id, param, function(data, textStatus) {
                        if(data == 'success') {
                            ajax_arr[id] = true;
                            $(msg_id).removeClass('user_msg_error').removeClass('user_msg_wait').addClass('user_msg_correct').html(' 正确');
                        } else {
                            verified_arr[id] = false;
                            ajax_arr[id] = false;
                            $(msg_id).removeClass('user_msg_wait').removeClass('user_msg_correct').addClass('user_msg_error').html(' ' + data);
                        }
                    });
                } else {
                    $(msg_id).removeClass('user_msg_error').removeClass('user_msg_wait').addClass('user_msg_correct').html(' 正确');
                }
            }
        });
        function check_input_value(element) {
            var id = $(element).attr('id');
            var input_val = trim($(element).val());
            switch(id) {
                case 'username':
                    var reg_str = /^[a-z0-9_-]{3,16}$/i;
                    var msg = ' 用户名由3-16个英文字母、数字及特殊字符-_组成';
                    return check_item(id, input_val, reg_str, msg);
                case 'email':
                    var reg_str = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
                    var msg = ' 邮箱地址未验证成功';
                    return check_item(id, input_val, reg_str, msg);
                case 'password':case 'password2':
                var reg_str = /^[a-zA-Z0-9!@#%&_\-\$]{5,20}$/;
                var msg = ' 密码由5-20位英文字母、数字及特殊字符!@#$%&_-组成';
                return check_item(id, input_val, reg_str, msg);
                default:
                    $.post('user.php?which=error', {error: '未知域id：' + id + '提交'});
                    return false;
            }
        };
        function check_item(id, input_val, reg_str, msg) {
            if(id == 'password2') {
                if(input_val != $('#password').val()) {
                    verified_arr[id] = false;
                    $('#' + id + '_msg').removeClass('user_msg_correct').removeClass('user_msg_wait').addClass('user_msg_error').html(' 两次密码输入不一致');
                    return false;
                }
            }

            if(input_val.match(reg_str)) {
                verified_arr[id] = true;
                return true;
            } else {
                if(input_val != '' && (id != 'password' || id != 'password2')) {
                    var error_msg_prefix = id == 'username' ? "未匹配的用户名：" : "未匹配的email地址：";
                    $.post('user.php?which=error', {error: error_msg_prefix + input_val});
                }
                verified_arr[id] = false;
                $('#' + id + '_msg').removeClass('user_msg_correct').removeClass('user_msg_wait').addClass('user_msg_error').html(msg);
                return false;
            }
        };
        $('#register_form').submit(function(e) {
            var verified = true;
            for(var key in verified_arr) {
                if(!verified_arr[key]) {
                    verified = false;
                    break;
                }
            }
            if(verified && ajax_arr['username'] && ajax_arr['email']) {
                $.post('user.php?which=register', {
                    username: trim($('#username').val()),
                    email: trim($('#email').val()),
                    password: trim($('#password').val())
                }, function(data, textStatus) {
                    if(data == 'success') {
                        $('#register_msg').removeClass('user_msg_error').addClass('user_msg_correct').html(' 注册成功');
                        window.location.href = 'index.php';
                    } else {
                        $('#register_msg').removeClass('user_msg_correct').addClass('user_msg_error').html(' ' + data);
                    }
                });
            } else {
                $('#register_form input[id]').each(function() {
                    check_input_value(this);
                });
                return false;
            }

            return e.preventDefault();
        });
        $('#login_form').submit(function(e) {
            if(trim($('#login_form #username').val()) == '' || trim($('#login_form #password').val()) == '') {
                $('#login_msg').removeClass('user_msg_correct').addClass('user_msg_error').html(' 用户名和密码不能为空');
                return false;
            } else {
                $.post('user.php?which=login', {
                    username: trim($('#username').val()),
                    password: trim($('#password').val())
                }, function(data, textStatus) {
                    if(data == 'success') {
                        $('#login_msg').removeClass('user_msg_error').addClass('user_msg_correct').html('登陆成功');
                        window.location.href = '<{echo $this->jump_url}>';
                    } else {
                        $('#login_msg').removeClass('user_msg_correct').addClass('user_msg_error').html(' ' + data);
                    }
                })
            }

            return e.preventDefault();
        });
        function trim(str){ //删除左右两端的空格
            return str.replace(/(^\s*)|(\s*$)/g, "");
        };
    });
</script>

<{include "./templates/footer.tpl"}>