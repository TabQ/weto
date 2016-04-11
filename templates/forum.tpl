<{include "./templates/header.tpl"}>

<div id="frame">
    <{if($this->new_article)}>
        <{include "./templates/new_article.tpl"}>
    <{else}>
        <div id="main">
            <div class="main_row">
                <div class="one_block" style="border: solid goldenrod 1px; border-radius: 6px;">
                    <div class="div_item_curr"><{echo $this->forumname}></div>
                    <{if(!empty($this->adminlist))}>
                    <div class="div_item">版主：</div>
                    <{foreach($this->adminlist as $admin)}>
                    <div class="div_item"><{echo $admin}></div>
                    <{/foreach}>
                    <{/if}>
                    <{if(count($this->adminlist) < MAX_ADMINS)}>
                    <div class="clear"></div>
                    <div class="div_item"><a href="#" id="apply">版主申请</a></div>
                    <{foreach($this->preadmins as $preadmin)}>
                    <div class="div_item">
                        <span><{echo $preadmin['username']}></span>
                        <{if(isset($preadmin['supporters']))}>
                        <span class="supporters" style="color:red;">(<{echo count($preadmin['supporters'])}>/30)</span>
                        <div style="display: none;position: absolute;background: #f6f6f6;">
                            <table border="1" style="text-align: center;">
                                <tr><td>用户名</td><td>时间</td></tr>
                                <{foreach($preadmin['supporters'] as $supporter)}>
                                <tr><td><{echo $supporter['username']}></td><td><{echo date('Y-m-d H:i:s',$supporter['time'])}></td></tr>
                                <{/foreach}>
                            </table>
                        </div>
                        <{/if}>
                        <input type="image" src="/templates/img/agree.gif" title="支持" alt="支持" value="<{echo $preadmin['id']}>" />
                        <input type="hidden" value="<{echo $preadmin['username']}>" />
                    </div>
                    <{/foreach}>
                    <{/if}>
                    <div class="clear"></div>
                </div>
            </div>
            <{include "./templates/action_page.tpl"}>
            <{include "./templates/articles_list.tpl"}>
            <{include "./templates/action_page.tpl"}>
        </div>
    <{/if}>

    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $(function () {
        $('input[id=post]').click(function() {
            <{if(is_null($this->fid))}>
            alert('请选择一个版面');
            <{else}>
            window.location.href = "forum.php?fid=<{echo $this->fid}>&action=new";
            <{/if}>
        });
        $('#apply').click(function() {
            <{if(!isset($_SESSION['username']))}>
            alert('请登陆');
            window.location.href = "user.php?action=login";
            return false;
            <{/if}>
            if(confirm("申请版主需要在10天内达到30人支持，您确定申请该版版主吗？") == true) {
                $.post("apply.php", {
                    fid: <{echo $this->fid}>
                }, function(data, textStatus) {
                    var obj = eval('('+decodeURI(data)+')');
                    if(obj.code == 1) {
                        window.location.href = "forum.php?fid=<{echo $this->fid}>";
                    } else if(obj.code == -1) {
                        alert(obj.msg);
                        window.location.href = "user.php?action=login";
                    } else {
                        alert(obj.msg);
                    }
                });
            }
        });
        $('input[type=image]').click(function() {
            <{if(!isset($_SESSION['username']))}>
            alert('请登陆');
            window.location.href = "user.php?action=login";
            return false;
            <{/if}>
            if(confirm("您确定支持该版面吗？") == true) {
                var paid = $(this).val();
                var fid = <{echo $_GET['fid']}>;
                var adminlist = '<{echo $this->adminlist_str}>';
                var preadmin = $(this).next().val();
                $.post('support_preadmins.php?paid='+paid, {
                    fid: fid,
                    adminlist: adminlist,
                    preadmin: preadmin
                }, function(data, textStatus) {
                    var obj = eval('('+decodeURI(data)+')');
                    if(obj.code == 1) {
                        window.location.href = "forum.php?fid=<{echo $this->fid}>";
                    } else if(obj.code == 2) {
                        alert(obj.msg);
                        window.location.href = "forum.php?fid=<{echo $this->fid}>";
                    } else if(obj.code == -1) {
                        alert(obj.msg);
                        window.location.href = "user.php?action=login";
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