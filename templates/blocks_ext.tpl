<{include "./templates/header.tpl"}>

<div id="frame">
    <div id="main" style="min-height: 800px;">
        <div class="main_row">
            <div class="one_block" style="border: solid goldenrod 1px; border-radius: 6px; margin-top: 12px;">
                <div class="div_item_curr">
                    已开通版面：
                </div>
                <{foreach($this->forums_arr as $forum)}>
                <div class="div_item">
                    <a href="forum.php?fid=<{echo $forum['id']}>" title="<{echo $forum['forumname']}>"><{echo $forum['forumname']}></a>
                </div>
                <{/foreach}>
                <div class="clear"></div>
            </div>
            <div class="one_block" style="border: solid goldenrod 1px; border-radius: 6px; margin-top: 12px;">
                <div class="div_item_curr">
                    申请中版面：
                </div>
                <{foreach($this->preforums_arr as $preforum)}>
                <div class="div_item">
                    <span><{echo $preforum['name']}></span>
                    <span class="supporters" style="color:red;">(<{echo count($preforum['supporters'])}>/50)</span>
                    <div style="display: none;position: absolute;background: #f6f6f6;">
                        <table border="1" style="text-align: center;">
                            <tr><td>用户名</td><td>时间</td></tr>
                            <{foreach($preforum['supporters'] as $supporter)}>
                            <tr><td><{echo $supporter['username']}></td><td><{echo date('Y-m-d H:i:s',$supporter['time'])}></td></tr>
                            <{/foreach}>
                        </table>
                    </div>
                    <input type="hidden" value="<{echo $preforum['name']}>" />
                    <input type="image" src="/templates/img/agree.gif" title="支持" alt="支持" value="<{echo $preforum['id']}>" />
                    <input type="hidden" value="<{echo $preforum['proposer']}>" />
                </div>
                <{/foreach}>
                <div class="clear"></div>
            </div>
            <div class="one_block" style="border: solid goldenrod 1px; border-radius: 6px; margin-top: 12px;">
                <form id="preforum_form" method="post">
                    <div class="user_row" style="margin: 20px 12px;">
                        <div style="float: left;"><p style="text-align: right;margin-top: 6px;">名称：</p></div>
                        <div class="user_input"><input id="name" name="name" type="text" style="height:26px;" /></div>
                        <div class="user_hint">&nbsp;&nbsp;<span id="name_msg" style="color: red;"></span></div>
                        <div class="user_input"><input type="submit" value="确  定" style="margin-left: 12px;width: 60px;height:26px;" /></div>
                        <div class="clear"></div>
                        <p style="margin: 20px 0;color: #6495ed;">请填写将要开通的版面名称（同一版面其他人可同时申请，最先达目标者先开通），<b style="color: red;">10</b>天内达到<b style="color: red;">50</b>人支持者系统自动开通，申请者自动成为版主。</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $(function () {
        $('#preforum_form input').click(function() {
            $('#name_msg').empty();
        });
        $('#preforum_form').submit(function(e) {
            var name_val = $.trim($('#name').val());
            if(name_val == '') {
                alert('名称不能为空');
                return false;
            }
            $.post("blocks_ext.php?bid=<{echo $_GET['bid']}>", {
                name: name_val,
            }, function(data, textStatus) {
                var obj = eval('('+decodeURI(data)+')');
                if(obj.code == 1) {
                    alert(obj.msg);
                    window.location.href = "blocks_ext.php?bid=<{echo $_GET['bid']}>";
                } else if(obj.code == -1) {
                    $('#name_msg').html(obj.msg);
                    window.location.href = "user.php?action=login";
                } else {
                    $('#name_msg').html(obj.msg);
                }
            });

            return e.preventDefault();
        });
        $('input[type=image]').click(function() {
            <{if(!isset($_SESSION['username']))}>
            alert('请登陆');
            window.location.href = "user.php?action=login";
            return false;
            <{/if}>
            if(confirm("您确定支持该版面吗？") == true) {
                var pfid = $(this).val();
                var proposer = $(this).next().val();
                var name = $(this).prev().val();
                $.post("support.php?pfid="+pfid, {
                    bid: <{echo $_GET['bid']}>,
                    proposer: $(this).next().val(),
                    name: $(this).prev().val()
                }, function(data, textStatus) {
                    var obj = eval('('+decodeURI(data)+')');
                    if(obj.code == 1) {
                        window.location.href = "blocks_ext.php?bid=<{echo $_GET['bid']}>";
                    } else if(obj.code == 2) {
                        alert(obj.msg);
                        window.location.href = "forum.php?fid="+obj.fid;
                    } else {
                        alert(obj.msg);
                    }
                });
            }
        });
        $('.supporters').hover(function() {
            $(this).next().show();
        }, function() {
            $(this).next().hide();
        });
    });
</script>

<{include "./templates/footer.tpl"}>