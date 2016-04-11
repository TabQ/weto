<div class="main_row">
    <div class="one_block" style="border: solid goldenrod 1px; border-radius: 6px;">
        <{if(is_null($this->fid))}>
        <div class="div_item_curr">
            全  部
        </div>
        <{else}>
        <div class="div_item">
            <a href="blocks.php?bid=<{echo $this->bid}>" title="">全  部</a>
        </div>
        <{/if}>
        <{foreach($this->forums_arr as $forum)}>
        <{if($this->fid == $forum['id'])}>
        <div class="div_item_curr">
            <{echo $forum['forumname']}>
        </div>
        <{else}>
        <div class="div_item">
            <a href="blocks.php?bid=<{echo $this->bid}>&fid=<{echo $forum['id']}>" title="<{echo $forum['forumname']}>"><{echo $forum['forumname']}></a>
        </div>
        <{/if}>
        <{/foreach}>
        <div class="div_item"><a href="blocks_ext.php?bid=<{echo $this->bid}>">我要开通 >></a></div>
        <div class="clear"></div>
    </div>
</div>