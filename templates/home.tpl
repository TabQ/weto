<div id="main">
    <div class="main_row">
        <div class="container" id="idContainer2">
            <ul id="idSlider2">
                <{foreach($this->top_imgs as $item)}>
                <li><a href="/article.php?aid=<{echo $item['id']}>"> <img src="<{echo $item['path']}>" alt="<{echo $item['title']}>" /> </a></li>
                <{/foreach}>
            </ul>
            <ul class="num" id="idNum">
            </ul>
        </div>
        <div id="hot_block">
            <div class="sub_hot">
                <div class="article">
                    <h3><a href="/article.php?aid=<{echo $this->local_hot['id']}>" title="<{echo $this->local_hot['title']}>"><{echo $this->local_hot['title']}></a></h3>
                    <p><a href="/article.php?aid=<{echo $this->local_hot['id']}>"><{echo $this->local_hot['message']}></a></p>
                </div>
                <div class="bottom_more"><div style="float: right;"><a href="<{echo $this->local_hot['url']}>">更多本地热点</a></div></div>
            </div>
            <div class="sub_hot">
                <div class="article">
                    <h3><a href="/article.php?aid=<{echo $this->life_hot['id']}>" title="<{echo $this->life_hot['title']}>"><{echo $this->life_hot['title']}></a></h3>
                    <p><a href="/article.php?aid=<{echo $this->life_hot['id']}>"><{echo $this->life_hot['message']}></a></p>
                </div>
                <div class="bottom_more"><div style="float: right;"><a href="<{echo $this->life_hot['url']}>">更多民生热点</a></div></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="main_row">
        <div id="top10">
            <h3></h3>
            <ul>
                <{if(!empty($this->top10_arr))}>
                <{foreach($this->top10_arr as $item)}>
                <li>
                    <div>
                        <a href="/forum.php?fid=<{echo $item['fid']}>" title="<{echo $item['forumname']}>">[<{echo $item['forumname']}>]</a>
                        <a href="/article.php?aid=<{echo $item['id']}>" title="<{echo $item['title']}>"><{echo $item['title']}></a>
                    </div>
                </li>
                <{/foreach}>
                <{/if}>
            </ul>
        </div>
        <div class="owners_news">
            <div class="news_title">
                <h2><a href="blocks.php?bid=6" title="业主新闻">业主新闻</a></h2>
                <div style="float: right; margin: 5px;"><a href="blocks.php?bid=6" title="业主新闻">更多</a></div>
            </div>
            <div class="news_list">
                <ul class="newslist14px">
                    <{foreach($this->owner_news as $item)}>
                    <li class="article_list">
                        <a href="/forum.php?fid=<{echo $item['fid']}>" title="<{echo $item['forumname']}>">[<{echo $item['forumname']}>]</a>
                        <a href="/article.php?aid=<{echo $item['id']}>" title="<{echo $item['title']}>"><{echo $item['title']}></a>
                    </li>
                    <{/foreach}>
                </ul>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="main_row">
        <div id="pictures">
            <h3>精彩贴图</h3>
            <ul>
                <{foreach($this->w_imgs as $item)}>
                <li>
                    <a href="article.php?aid=<{echo $item['id']}>"><img src="<{echo $item['path']}>" /></a>
                <br />
                <a class="board" href="forum.php?fid=<{echo $item['fid']}>">[<{echo $item['forumname']}>]</a>
                <br />
                <a class="title" href="article.php?aid=<{echo $item['id']}>" title="<{echo $item['title']}>"><{echo $item['title']}></a>
                </li>
                <{/foreach}>
            </ul>
        </div>
    </div>

    <div class="main_row">
        <div class="half_block_boards">
            <div class="block_header">
                <div style="float: left;"><a href="/blocks.php?bid=1">业主之家</a></div>
                <div style="float: right;"><a href="/blocks.php?bid=1">更多</a></div>
            </div>
            <div class="topics">
                <ul>
                    <{foreach($this->topics_arr[1] as $item)}>
                    <li class="article_list">
                        <a href="/forum.php?fid=<{echo $item['fid']}>">[<{echo $item['forumname']}>]</a>
                        <a href="/article.php?aid=<{echo $item['id']}>" title="<{echo $item['title']}>"><{echo $item['title']}></a>
                    </li>
                    <{/foreach}>
                </ul>
            </div>
            <div class="boards">
                <{foreach($this->boards_arr[1] as $item)}>
                <div class="div_item"><a href="/forum.php?fid=<{echo $item['id']}>">[<{echo $item['forumname']}>]</a></div>
                <{/foreach}>
                <div class="div_item"><a href="/blocks.php?bid=1">更多...</a></div>
            </div>
        </div>
        <div class="half_block_boards">
            <div class="block_header">
                <div style="float: left;"><a href="/blocks.php?bid=2">休闲娱乐</a></div>
                <div style="float: right;"><a href="/blocks.php?bid=2">更多</a></div>
            </div>
            <div class="topics">
                <ul>
                    <{foreach($this->topics_arr[2] as $item)}>
                    <li class="article_list">
                        <a href="/forum.php?fid=<{echo $item['fid']}>">[<{echo $item['forumname']}>]</a>
                        <a href="/article.php?aid=<{echo $item['id']}>" title="<{echo $item['title']}>"><{echo $item['title']}></a>
                    </li>
                    <{/foreach}>
                </ul>
            </div>
            <div class="boards">
                <{foreach($this->boards_arr[2] as $item)}>
                <div class="div_item"><a href="/forum.php?fid=<{echo $item['id']}>">[<{echo $item['forumname']}>]</a></div>
                <{/foreach}>
                <div class="div_item"><a href="/blocks.php?bid=2">更多...</a></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="main_row">
        <div class="half_block_boards">
            <div class="block_header">
                <div style="float: left;"><a href="/blocks.php?bid=3">社会信息</a></div>
                <div style="float: right;"><a href="/blocks.php?bid=3">更多</a></div>
            </div>
            <div class="topics">
                <ul>
                    <{foreach($this->topics_arr[3] as $item)}>
                    <li class="article_list">
                        <a href="/forum.php?fid=<{echo $item['fid']}>">[<{echo $item['forumname']}>]</a>
                        <a href="/article.php?aid=<{echo $item['id']}>" title="<{echo $item['title']}>"><{echo $item['title']}></a>
                    </li>
                    <{/foreach}>
                </ul>
            </div>
            <div class="boards">
                <{foreach($this->boards_arr[3] as $item)}>
                <div class="div_item"><a href="/forum.php?fid=<{echo $item['id']}>">[<{echo $item['forumname']}>]</a></div>
                <{/foreach}>
                <div class="div_item"><a href="/blocks.php?bid=3">更多...</a></div>
            </div>
        </div>
        <div class="half_block_boards">
            <div class="block_header">
                <div style="float: left;"><a href="/blocks.php?bid=4">本地信息</a></div>
                <div style="float: right;"><a href="/blocks.php?bid=4">更多</a></div>
            </div>
            <div class="topics">
                <ul>
                    <{foreach($this->topics_arr[4] as $item)}>
                    <li class="article_list">
                        <a href="/forum.php?fid=<{echo $item['fid']}>">[<{echo $item['forumname']}>]</a>
                        <a href="/article.php?aid=<{echo $item['id']}>" title="<{echo $item['title']}>"><{echo $item['title']}></a>
                    </li>
                    <{/foreach}>
                </ul>
            </div>
            <div class="boards">
                <{foreach($this->boards_arr[4] as $item)}>
                <div class="div_item"><a href="/forum.php?fid=<{echo $item['id']}>">[<{echo $item['forumname']}>]</a></div>
                <{/foreach}>
                <div class="div_item"><a href="/blocks.php?bid=4">更多...</a></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="main_row">
        <div class="one_block">
            <div class="block_header">
                <div style="float: left;"><a href="/blocks.php?bid=6">业主广场</a></div>
                <div style="float: right;"><a href="/blocks.php?bid=6">更多</a></div>
            </div>
            <{foreach($this->boards_arr[6] as $item)}>
            <div class="div_item"><a href="/forum.php?fid=<{echo $item['id']}>">[<{echo $item['forumname']}>]</a></div>
            <{/foreach}>
            <div class="div_item"><a href="/blocks.php?bid=6">更多...</a></div>
        </div>
    </div>
    <div class="main_row">
        <div class="one_block">
            <div class="block_header">
                <div style="float: left;"><a href="/blocks.php?bid=5">为您服务</a></div>
                <div style="float: right;"><a href="/blocks.php?bid=5">更多</a></div>
            </div>
            <{foreach($this->boards_arr[5] as $item)}>
            <div class="div_item"><a href="/forum.php?fid=<{echo $item['id']}>">[<{echo $item['forumname']}>]</a></div>
            <{/foreach}>
            <div class="div_item"><a href="/blocks.php?bid=5">更多...</a></div>
        </div>
    </div>
</div>