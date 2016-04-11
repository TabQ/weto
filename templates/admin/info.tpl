<div id="main">
    <div class="main_row"><p>基本信息：</p></div>
    <div class="main_row">
        <div class="one_block">
            <form id="admin_home_form" method="post">
                <div class="user_row">
                    <div class="user_label"><p style="text-align: right">Title:&nbsp;</p></div>
                    <div class="user_input"><input id="home_title" name="home_title" type="text" value="<{echo $this->title}>" /></div>
                    <div class="clear"></div>
                </div>
                <div class="user_row">
                    <div class="user_label"><p style="text-align: right">Keywords:&nbsp;</p></div>
                    <div class="user_input"><input id="home_keywords" name="home_keywords" type="text" class="admin_input" value="<{echo $this->keywords}>" /></div>
                    <div class="clear"></div>
                </div>
                <div class="user_row">
                    <div class="user_label"><p style="text-align: right">Description:&nbsp;</p></div>
                    <div class="user_input"><textarea id="home_des" name="home_des" style="width:500px;height:200px"><{echo $this->description}></textarea></div>
                    <div class="clear"></div>
                </div>
                <div class="user_row">
                    <div class="user_button_block"><input type="submit" value="确  定" class="user_button" /></div>
                    <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>
    <div class="main_row"><p>预设各版面置顶贴：</p></div>
    <div class="main_row">
        <div class="one_block">
            <form id="pretop_form" method="post">
                <div class="user_row">
                    <div class="user_label"><p style="text-align: right;">Title:&nbsp;</p></div>
                    <div class="user_input"><input style="width: 500px;" id="title" name="title" type="text" value="<{echo $this->pretops['title']}>" /></div>
                    <div class="clear"></div>
                </div>
                <div class="uer_row">
                    <div class="user_label"><p style="text-align: right">Message:&nbsp;</p></div>
                    <div class="user_input"><textarea id="message" name="message" style="width:500px;height:200px"><{echo $this->pretops['message']}></textarea></div>
                    <div class="clear"></div>
                </div>
                <div class="user_row">
                    <div class="user_button_block"><input type="submit" value="确  定" class="user_button" /></div>
                    <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('#admin_home_form').submit(function(e) {
            $.post("/admin/index.php", {
                action: 'site',
                home_title: $('#home_title').val(),
                home_keywords: $('#home_keywords').val(),
                home_des: $('#home_des').val()
            }, function(data, textStatus) {
                alert(data);
                window.location.href = "/admin/index.php";
            });
            return e.preventDefault();
        });
        $('#pretop_form').submit(function(e) {
            $.post("/admin/index.php", {
                action: 'pretops',
                title: $('#title').val(),
                message: $('#message').val()
            }, function(data, textStatus) {
                alert(data);
                window.location.href = "/admin/index.php";
            });
            return e.preventDefault();
        });
    });
</script>