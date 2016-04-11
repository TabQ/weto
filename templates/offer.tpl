<{include "./templates/header.tpl"}>

<div id="frame">
    <div class="user_main">
        <form id="offer_form" method="post" action="offer.php?action=add_detail">
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">名称：</p></div>
                <div class="user_input">
                    <select id="name" name="name">
                        <{foreach($this->names as $item)}>
                        <option value="<{echo $item['id']}>"><{echo $item['name']}></option>
                        <{/foreach}>
                    </select>
                </div>
                <div class="user_input">
                    <label for="add_name">&nbsp;&nbsp;&nbsp;我要增加：</label><input id="add_name" name="add_name" />
                    <input type="button" id="confirm_add_name" value="确定" />
                </div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">等级：</p></div>
                <div class="user_input">
                    <select id="rate" name="rate">
                        <{foreach($this->rates as $item)}>
                        <option value="<{echo $item['id']}>"><{echo $item['name']}></option>
                        <{/foreach}>
                    </select>
                </div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">价格：</p></div>
                <div class="user_input">
                    <input id="price" name="price" type="text" />&nbsp;元/
                    <select id="unit" name="unit">
                        <{foreach($this->units as $item)}>
                        <option <{if($item['id'] == 1)}>selected="selected"<{/if}> value="<{echo $item['id']}>"><{echo $item['name']}></option>
                        <{/foreach}>
                    </select>
                </div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_label"><p style="text-align: right">时间：</p></div>
                <div class="user_input"><input id="time" name="time" value="<{echo date('Y-m-d')}>" /></div>
                <div class="clear"></div>
            </div>
            <div class="user_row">
                <div class="user_button_block"><input type="submit" value="确  定" class="user_button" /></div>
                <div class="user_button_block"><input type="reset" value="重  置" class="user_button" /></div>
                <div class="clear"></div>
            </div>
        </form>
        <br /><p>最近5条新增信息：</p><br />
        <table border="1" width="806">
            <tr><th>名称</th><th>等级</th><th>价格</th><th>时间</th><th>发布者</th></tr>
            <{foreach($this->goods as $item)}>
            <tr><td><{echo $item['nname']}></td><td><{echo $item['rname']}></td><td><{echo $item['price']}>元/<{echo $item['uname']}></td><td><{echo date('Y-m-d',$item['time'])}></td><td><{echo $item['username']}></td></tr>
            <{/foreach}>
        </table>
    </div>
    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $(function () {
        $('#confirm_add_name').click(function () {
            if($.trim($('#add_name').val()) == '') {
                alert('名称不能为空');
                return false;
            }
            $.post("offer.php?action=add_name", {
                name: $.trim($('#add_name').val())
            }, function(data, textStatus) {
                var obj = eval('('+decodeURI(data)+')');
                if(obj.code == 1) {
                    alert(obj.msg);
                    window.location.href = "offer.php";
                } else {
                    alert(obj.msg);
                }
            });
        });
    });
</script>

<{include "./templates/footer.tpl"}>