<{include "../templates/header.tpl"}>

<div id="frame">
    <{if($this->item == 'blocks')}>
        <{include "../templates/admin/blocks.tpl"}>
    <{elseif($this->item == 'forums')}>
        <{include "../templates/admin/forums.tpl"}>
    <{else}>
        <{include "../templates/admin/info.tpl"}>
    <{/if}>

    <{include "../templates/right_side.tpl"}>
    <div class="clear"></div>
</div>