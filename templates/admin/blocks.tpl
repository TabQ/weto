<div class="user_main" style="height: auto;">
    <form id="admin_blocks_form" method="post">
        <div class="user_row">
            <div class="user_label"><p style="text-align: right">名称：</p></div>
            <div class="user_input"><input id="name" name="name" type="text" /></div>
            <div class="clear"></div>
        </div>
        <div class="user_row">
            <div class="user_label"><p style="text-align: right">Rank:</p></div>
            <div class="user_input"><input id="rank" name="rank" type="text" />&nbsp;数字</div>
            <div class="clear"></div>
        </div>
        <div class="user_row">
            <div class="user_label"><p style="text-align: right">Seotitle:</p></div>
            <div class="user_input"><input id="seotitle" name="seotitle" type="text" class="admin_input" /></div>
            <div class="clear"></div>
        </div>
        <div class="user_row">
            <div class="user_label"><p style="text-align: right">Keywords:</p></div>
            <div class="user_input"><input id="keywords" name="keywords" type="text" class="admin_input" /></div>
            <div class="clear"></div>
        </div>
        <div class="user_row">
            <div class="user_label"><p style="text-align: right">Description:</p></div>
            <div class="user_input"><textarea id="description" name="description" style="width:500px;height:50px"></textarea></div>
            <div class="clear"></div>
        </div>
        <div class="user_row">
            <div class="user_button_block"><input type="submit" value="确  定" class="user_button" /></div>
            <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
            <div class="clear"></div>
        </div>
    </form>
    <{foreach($this->blocks as $block)}>
    <form id="edit_blocks_form_<{echo $block['id']}>" method="post">
        <div class="admin_item">
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">名称：</p></div>
                <div class="user_input"><input id="name" name="name" type="text" value="<{echo $block['name']}>" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">Rank:</p></div>
                <div class="user_input"><input id="rank" name="rank" type="text" value="<{echo $block['rank']}>" />&nbsp;数字</div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">Seotitle:</p></div>
                <div class="user_input"><input id="seotitle" name="seotitle" type="text" class="admin_input" value="<{echo $block['seotitle']}>" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">Keywords:</p></div>
                <div class="user_input"><input id="keywords" name="keywords" type="text" class="admin_input" value="<{echo $block['keywords']}>" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">Description:</p></div>
                <div class="user_input">
                    <textarea id="description" name="description" style="width:500px;height:50px"><{echo $block['description']}></textarea>
                </div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <input type="hidden" id="bid" name="bid" value="<{echo $block['id']}>" />
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
        $('#admin_blocks_form').submit(function(e) {
            $(this).find('input[type=submit]').attr('disabled', 'disabled');
            $(this).find('input[type=reset]').attr('disabled', 'disabled');
            $.post('/admin/blocks.php?action=add', {
                name: $(this).find('#name').val(),
                rank: $(this).find('#rank').val(),
                seotitle: $(this).find('#seotitle').val(),
                keywords: $(this).find('#keywords').val(),
                description: $(this).find('#description').val()
            }, function(data, textStatus) {
                alert(data);
                window.location.href = "/admin/blocks.php";
            });
            return e.preventDefault();
        });
        $('form[id^=edit_blocks_form_]').submit(function(e) {
            $(this).find('input[type=submit]').attr('disabled', 'disabled');
            $(this).find('input[type=reset]').attr('disabled', 'disabled');
            $.post('/admin/blocks.php?action=edit', {
                id: $(this).find('#bid').val(),
                name: $(this).find('#name').val(),
                rank: $(this).find('#rank').val(),
                seotitle: $(this).find('#seotitle').val(),
                keywords: $(this).find('#keywords').val(),
                description: $(this).find('#description').val()
            }, function(data, textStatus) {
                alert(data);
                window.location.href = "/admin/blocks.php";
            });
            return e.preventDefault();
        });
    });
</script>