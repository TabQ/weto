<{include "./templates/header.tpl"}>

<div id="frame">
    <div id="main" style="min-height: 800px;">
        <div class="main_row">
            <div class="one_block" style="border: solid goldenrod 1px; border-radius: 6px; margin-top: 12px;">
                <table border="2px;">
                    <tr><td>名称</td><td>积分</td><td>文章数</td><td>帖子数</td><td>时间</td></tr>
                    <{foreach($this->supporters as $supporter)}>
                    <tr><td><{echo $supporter['username']}></td><td><{echo $supporter['credits']}></td><td><{echo $supporter['articles']}></td><td><{echo $supporter['posts']}></td><td><{echo date('Y-m-d H:i:s', $supporter['time'])}></td></tr>
                    <{/foreach}>
                </table>
            </div>
        </div>
    </div>
    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<{include "./templates/footer.tpl"}>