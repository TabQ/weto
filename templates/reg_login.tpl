<div class="user_main">
    <div class="user_div">
        <{if($_GET['action'] == 'register')}>
        <form id="register_form" method="post">
            <div class="user_row">
                <div class="user_hint"><span id="register_msg"></span></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">用户名：</p></div>
                <div class="user_input"><input id="username" name="username" type="text" /></div>
                <div class="user_hint"><b style="color: red">&nbsp;*</b><span id="username_msg"></span></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">邮箱：</p></div>
                <div class="user_input"><input id="email" name="email" type="text" /></div>
                <div class="user_hint"><b style="color: red">&nbsp;*</b><span id="email_msg"></span></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">密码：</p></div>
                <div class="user_input"><input id="password" name="password" type="password" /></div>
                <div class="user_hint"><b style="color: red">&nbsp;*</b><span id="password_msg"></span></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">重复密码：</p></div>
                <div class="user_input"><input id="password2" name="password2" type="password" /></div>
                <div class="user_hint"><b style="color: red">&nbsp;*</b><span id="password2_msg"></span></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_button_block"><input type="submit" value="确  定" class="user_button" /></div>
                <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
                <div class="clear"></div>
            </div>
        </form>
        <{elseif($_GET['action'] == 'login')}>
        <form id="login_form" method="post">
            <div class="user_row">
                <div class="user_hint"><span id="login_msg"></span></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">用户名：</p></div>
                <div class="user_input"><input id="username" name="username" type="text" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">密码：</p></div>
                <div class="user_input"><input id="password" name="password" type="password" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_button_block"><input type="submit" value="确  定" class="user_button" /></div>
                <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
                <div class="clear"></div>
            </div>
        </form>
        <div class="user_row">
            <div class="user_hint"><a href="/forget.php">忘记密码</a></div>
            <div class="clear"></div>
        </div>
        <{/if}>
    </div>
</div>