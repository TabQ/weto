<{include "./templates/header.tpl"}>

<div id="frame">
    <{include "./templates/home.tpl"}>
    <{include "./templates/right_side.tpl"}>
    <div class="clear"></div>
</div>

<script type="text/javascript" src="/templates/js/SlideTrans.js"></script>
<script type="text/javascript">
    var nums = [], timer, n = $$("idSlider2").getElementsByTagName("li").length,
            st = new SlideTrans("idContainer2", "idSlider2", n, {
                onStart: function(){//设置按钮样式
		forEach(nums, function(o, i){ o.className = st.Index == i ? "on" : ""; })
    }
    });
    for(var i = 1; i <= n; AddNum(i++)){};
    function AddNum(i){
        var num = $$("idNum").appendChild(document.createElement("li"));
        num.innerHTML = i--;
        num.onmouseover = function(){
            timer = setTimeout(function(){ num.className = "on"; st.Auto = false; st.Run(i); }, 200);
        }
        num.onmouseout = function(){ clearTimeout(timer); num.className = ""; st.Auto = true; st.Run(); }
        nums[i] = num;
    }
    st.Run();
</script>

<{include "./templates/footer.tpl"}>