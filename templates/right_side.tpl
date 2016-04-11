<div id="right_side">
    <div id="user_info">
        <{if(isset($_SESSION['username']))}>
        <div id="user_info_avatar">
            <a href="/userinfo.php"><img src="/templates/img/avatar/<{echo $_SESSION['avatar']}>.png" width="128" height="128" /></a>
        </div>
        <div class="user_row" style="text-align: center;overflow: hidden; text-overflow: clip; white-space: nowrap;">
            <a href="/userinfo.php"><p><{echo $_SESSION['username']}></p></a>
        </div>
        <div class="user_row">
            <div class="user_action_block"><a class="user_action" href="/userinfo.php">个人中心</a></div>
            <div class="user_action_block"><a class="user_action" href="/logout.php">退 出</a></div>
            <div class="clear"></div>
        </div>
        <{else}>
        <div id="user_info_avatar"><img src="/templates/img/avatar/default.png" width="128" height="128" /></div>
        <div class="user_row">
            <div class="user_action_block"><a class="user_action" href="user.php?action=login">登 陆</a></div>
            <div class="user_action_block"><a class="user_action" href="user.php?action=register">注 册</a></div>
            <div class="clear"></div>
        </div>
        <{/if}>
    </div>
    <div id="life_query">
        <p style="border-bottom: 1px solid; padding-bottom: 4px; font-size: 13px;"><a href="vegetables.php" title="菏泽每日蔬菜价格"><b>今日菜价：</b></a></p>
        <{if(is_array($this->rec_vegs))}>
        <{foreach($this->rec_vegs as $item)}>
        <p>
            <a href="vegetables.php?nid=<{echo $item['id']}>" title="菏泽<{echo $item['nname']}>价格">
                <span style="float:left;"><{echo $item['nname']}></span>
                <span style="float:right;"><{echo $item['price']}> 元/<{echo $item['uname']}></span>
            </a>
        </p>
        <{/foreach}>
        <{/if}>
        <p><a href="vegetables.php" title="菏泽菜价">更多...</a></p>
    </div>
</div>