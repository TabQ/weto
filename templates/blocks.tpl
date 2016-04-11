<{include "./templates/header.tpl"}>

<div id="frame">
    <div id="main">
        <{include "./templates/forums_list.tpl"}>

        <{include "./templates/action_page.tpl"}>
        <{include "./templates/articles_list.tpl"}>
        <{include "./templates/action_page.tpl"}>
    </div>
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
    });
</script>

<{include "./templates/footer.tpl"}>