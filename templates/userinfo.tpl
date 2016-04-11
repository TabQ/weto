<{include "./templates/header.tpl"}>

<div id="frame">
    <div class="user_main">
        <form id="userinfo" method="post" action="/userinfo.php">
            <div class="user_row">
                <div class="user_hint"><span id="userinfo_msg"><{echo $this->result}></span></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div style="float:left;"><p>用户名：</p></div>
                <div class="user_input"><p><{echo $_SESSION['username']}></p></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div><img id="user_avatar" src="/templates/img/avatar/<{echo $this->userinfo['avatar']}>.png" width="128" height="128" /></div>
                <div style="margin:10px 36px;"><input type="button" id="random" value="随机选择" /><input type="hidden" id="index" name="index" value="<{echo $this->userinfo['avatar']}>" /></div>
            </div>
            <div class="user_row">
                <div style="float:left;margin-top:6px;"><p>昵称：</p></div>
                <div class="user_input"><input id="nickname" name="nickname" type="text" style="width:350px;height:26px;" value="<{echo $this->userinfo['nickname']}>" />（十六个中文字符或50个英文字符/符号）</div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div style="float:left;"><input type="submit" value="确  定" class="user_button" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row"><a href="/chg_pwd.php">修改密码</a></div>
        </form>
    </div>
    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $(function() {
        $('input').click(function() {
            $('#userinfo_msg').empty();
        });
        $('#random').click(function() {
            var index = Math.round(Math.random() * <{echo $this->max_avatar}>);
            $('#index').val(index);
            $('#user_avatar').attr('src', '/templates/img/avatar/' + index + '.png');
        });
    });
</script>

<{include "./templates/footer.tpl"}>