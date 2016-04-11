<div class="action_page">
    <{if($this->can_post)}>
    <div style="float: left"><input type="button" id="post" value="发贴" style="margin: 4px;" /></div>
    <{/if}>
    <{if($this->can_reply)}>
    <div style="float: left">
    <input type="button" id="reply" value="回复" style="margin: 4px;" />
    </div>
    <{/if}>
    <div style="float: right; margin-top: 8px;">
        <{if($this->page['cur_page'] == 1)}>
        首页&nbsp;上一页
        <{else}>
        <a href="<{echo $this->page['page_url']}>p=1">首页</a>&nbsp;<a href="<{echo $this->page['page_url']}>p=<{echo $this->page['cur_page']-1}>">上一页</a>
        <{/if}>
        &nbsp;<{echo $this->page['cur_page']}>&nbsp;
        <{for($i = $this->page['cur_page'] + 1; $i <= $this->page['cur_page'] + 9; $i++)}>
        <{if($i > $this->page['total_page'])}>
        <{break}>
        <{else}>
        <a href="<{echo $this->page['page_url']}>p=<{echo $i}>"><{echo $i}></a>
        <{/if}>
        <{/for}>
        <{if($this->page['cur_page'] == $this->page['total_page'])}>
        下一页&nbsp;尾页
        <{else}>
        <a href="<{echo $this->page['page_url']}>p=<{echo $this->page['cur_page']+1}>">下一页</a>&nbsp;<a href="<{echo $this->page['page_url']}>p=<{echo $this->page['total_page']+1}>">尾页</a>
        <{/if}>
    </div>
    <div class="clear"></div>
</div>