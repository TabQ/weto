<{include "./templates/header.tpl"}>

<div id="frame">
    <div id="main">
        <div class="main_row"><p><{echo $this->nname}>平均价格走势：</p></div>
        <div class="main_row"><canvas id="veg_chart" width="806" height="400"></canvas></div>
        <div class="main_row"><p>今日蔬菜平均价格（<{echo $this->rec_time}> 发布）：</p></div>
        <div class="main_row">
            <table id="veg_table" border="1" width="806" style="text-align: center;">
                <tr><th>名称</th><th>等级</th><th>价格</th><th>时间</th><th>发布者</th><th>走势</th></tr>
                <{foreach($this->goods as $good)}>
                <tr>
                    <td><{echo $good['nname']}></td>
                    <td><{echo $good['rname']}></td>
                    <td><{echo $good['price']}> 元/<{echo $good['uname']}></td>
                    <td><{echo date('Y-m-d',$good['time'])}></td>
                    <td><{echo $good['username']}></td>
                    <td><label><input <{if($good['id'] == $this->nid)}>checked="checked"<{/if}> type="radio" value="<{echo $good['id']}>" /> 走势</label></td>
                </tr>
                <{/foreach}>
            </table>
        </div>
    </div>
    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<script type="text/javascript" src="/templates/thirdparty/chartjs/Chart.js"></script>
<script type="text/javascript">
    $(function() {
        var data = <{echo $this->get_nid_chart_data}>;
        var ctx = $('#veg_chart').get(0).getContext("2d");
        var vet_chart = new Chart(ctx).Line(data, {reponsive: true});

        $('input[type=radio]').click(function() {
            window.location.href = "vegetables.php?nid=" + $(this).val();
        });
    });
</script>

<{include "./templates/footer.tpl"}>