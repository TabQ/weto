<{include "./templates/header.tpl"}>

<div id="frame">
    <{if(isset($this->action) && $this->action == 'new')}>
        <{include "./templates/new_reply.tpl"}>
    <{elseif(isset($this->action) && $this->action == 'edit')}>
        <{include "./templates/edit_reply.tpl"}>
    <{else}>
        <div id="main">
            <{include "./templates/action_page.tpl"}>
            <{include "./templates/replies_list.tpl"}>
            <{include "./templates/action_page.tpl"}>
        </div>
    <{/if}>

    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $(function() {
        $('input[id=post]').click(function() {
            <{if(is_null($this->fid))}>
                alert('请选择一个版面');
            <{else}>
                window.location.href = "forum.php?fid=<{echo $this->fid}>&action=new";
            <{/if}>
        });
        $('input[id=reply]').click(function() {
            window.location.href="article.php?aid=<{echo $this->article['id']}>&action=new";
        });
    });
</script>

<{include "./templates/footer.tpl"}>