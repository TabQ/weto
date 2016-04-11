<div class="user_main" style="height: auto;">
    <form id="admin_forums_form" method="post">
        <div class="user_row">
            <div class="user_label"><p style="text-align: right">名称：</p></div>
            <div class="user_input"><input id="name" type="text" /></div>
            <div class="user_label"><p style="text-align: right">Bid:</p></div>
            <div class="user_input"><input id="bid" type="text" />&nbsp;数字</div>
            <div class="user_label"><p style="text-align: right">Rank:</p></div>
            <div class="user_input"><input id="rank" type="text" />&nbsp;数字</div>
            <div class="clear"></div>
        </div>
        <div class="user_row">
            <div class="user_label"><p style="text-align: right">Seotitle:</p></div>
            <div class="user_input"><input id="seotitle" type="text" class="admin_input" /></div>
            <div class="clear"></div>
        </div>
        <div class="user_row">
            <div class="user_label"><p style="text-align: right">Keywords:</p></div>
            <div class="user_input"><input id="keywords" type="text" class="admin_input" /></div>
            <div class="clear"></div>
        </div>
        <div class="user_row">
            <div class="user_label"><p style="text-align: right">Adminlist:</p></div>
            <div class="user_input"><input id="adminlist" type="text" class="admin_input" disabled="disabled" /></div>
            <div class="clear"></div>
        </div>
        <div class="user_row">
            <div class="user_label"><p style="text-align: right">Description:</p></div>
            <div class="user_input"><textarea id="description" style="width:500px;height:50px"></textarea></div>
            <div class="clear"></div>
        </div>
        <div class="user_row">
            <input type="hidden" id="fid" value="" />
            <input type="hidden" id="action" value="add" />
            <div class="user_button_block"><input type="submit" value="确  定" class="user_button" /></div>
            <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
            <div class="clear"></div>
        </div>
    </form>
    <{foreach($this->forums as $forum)}>
    <form id="admin_forums_form" method="post">
        <div class="admin_item">
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">名称：</p></div>
                <div class="user_input"><input id="name" type="text" value="<{echo $forum['name']}>" /></div>
                <div class="user_label"><p style="text-align: right">Bid:</p></div>
                <div class="user_input"><input id="bid" type="text" value="<{echo $forum['bid']}>" />&nbsp;数字</div>
                <div class="user_label"><p style="text-align: right">Rank:</p></div>
                <div class="user_input"><input id="rank" type="text" value="<{echo $forum['rank']}>" />&nbsp;数字</div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">Posts:</p></div>
                <div class="user_input"><input id="posts" type="text" value="<{echo $forum['posts']}>" disabled="disabled" /></div>
                <div class="user_label"><p style="text-align: right">Articles:</p></div>
                <div class="user_input"><input id="articles" type="text" value="<{echo $forum['articles']}>" disabled="disabled" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">Seotitle:</p></div>
                <div class="user_input"><input id="seotitle" type="text" class="admin_input" value="<{echo $forum['seotitle']}>" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">Keywords:</p></div>
                <div class="user_input"><input id="keywords" type="text" class="admin_input" value="<{echo $forum['keywords']}>" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">Adminlist:</p></div>
                <div class="user_input"><input id="adminlist" type="text" class="admin_input" style="width:450px;" disabled="disabled" value="<{echo $forum['adminlist']}>" /></div>
                <div class="user_input">&nbsp;<input type="text" /><input type="button" value="添 加" /><input type="hidden" id="fid" value="<{echo $forum['id']}>" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">Description:</p></div>
                <div class="user_input"><textarea id="description" style="width:500px;height:50px"><{echo $forum['description']}></textarea></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <input type="hidden" id="action" value="edit" />
                <div class="user_button_block"><input type="submit" value="确  定" class="user_button" /></div>
                <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
                <div class="clear"></div>
            </div>
        </div>
    </form>
    <{/foreach}>
</div>

<script type="text/javascript">
    $(function() {
        $('form').submit(function(e) {
            $(this).find('input[type=submit]').attr('disabled', 'disabled');
            $(this).find('input[type=reset]').attr('disabled', 'disabled');
            $.post('/admin/forums.php', {
                name: $(this).find('#name').val(),
                bid: $(this).find('#bid').val(),
                rank: $(this).find('#rank').val(),
                seotitle: $(this).find('#seotitle').val(),
                keywords: $(this).find('#keywords').val(),
                description: $(this).find('#description').val(),
                fid: $(this).find('#fid').val(),
                action: $(this).find('#action').val()
            }, function (data, textStatus) {
                alert(data);
                window.location.href = "/admin/forums.php";
            });
            return e.preventDefault();
        });
        $('input[type=button]').click(function() {
            $(this).attr('disabled', 'disabled');
            $.post('/admin/forums.php', {
                username: $(this).prev().val(),
                fid: $(this).next().val()
            }, function (data, textStatus) {
                alert(data);
                window.location.href = "/admin/forums.php";
            });
        });
    });
</script>