/**
 * Created by Administrator on 2017/4/21.
 */


//公告滚动
$(function () {
    var notice = $(".notice-bar li");
    var noticeWidth = notice.width();
    var clientWidth = document.documentElement.getBoundingClientRect().width;
    notice.css({left: clientWidth + "px"});
    setInterval(function () {
        notice.animate({left:"-" + noticeWidth + "px"}, 100*noticeWidth, 'linear', function () {
            notice.css({left: clientWidth + "px"})
        } )
    }, 0)
});

// 弹窗事件
$(function () {
    var popup = $(".popup");
    popup.show();
    popup.children().hide();

    $(".recharge").on("click", function () {
        $(".bet-popup").show();
        $("#bet-point").text("无");
    });

    $(".lottery-explain").on("click", function () {
        $(".lottery-explain-popup").show();
        $(".lottery-explain-popup").on("click", function () {
            $(".popup-bg").hide();
        });
    });






    var popup_close = $(".popup-close");
    popup_close.click(function () {
        $(".popup-bg").hide();
    });
    var popup_close_btn =$(".popup-close-btn");
    popup_close_btn.click(function () {
        window.location.reload();
    });
});

//开奖&&倒计时
/*var period_all = "";
var next_period = "";
var last_period = "";
var lottery_result_point = "";      //定义开奖结果 全局变量*/

$(function () {

    var clearCount = 0;
    var timeCount = 0;
    var interval2;
    //var lottery_result_point;
    var hilomp;

    function countdown() {
        var date = new Date();
        var seconds = date.getSeconds();
        var minutes = date.getMinutes();
        var hours = date.getHours();alert(hours)
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();

        next_period = hours * 60 + minutes + 1;     //下一期
        last_period = hours * 60 + minutes;         //上一期
        //总期数：1704220001期
        period_all = keep2(year) + keep2(month) + keep2(day) + next_period;

        timeCount = 59 - seconds;   //开奖倒计时
        //保留四位数
        function keep4(str) {
            var new_str = "0000" + str;
            return new_str.substr(-4);
        }
        //保留二位数
        function keep2(str) {
            var new_str = "0000" + str;
            return new_str.substr(-2);
        }
        //开奖时间
        if (timeCount > 56) {       //57,58,59,0秒后开奖
            $("#time-left").text("正在开奖中......");
			clearCount = 0;
			function count4(){
				var count = Math.floor(Math.random() * 6);      //Math.random是0-1 所以*6
                lottery_results.eq(count).show().siblings().hide(); //显示当前图片，其它兄弟图片隐藏
                clearCount++;
                if(clearCount == 4){
                	clearInterval(interval2);
                }
			}
			count4();
            interval2 = setInterval(count4, 250);       //每秒调用4次，图片切换速度更快，动画效果更好
            return;
        }
        if (timeCount == 56) {
            //开奖结果
            lottery_result_point = Math.floor(Math.random() * 6);
            lottery_results.eq(lottery_result_point).show().siblings().hide();

            // 开奖大小
            hilomp = lottery_result_point > 2 ? "大":"小";                //当前图片位置>2(相当于是3)
            $("#time-left").text(lottery_result_point + 1 + "点，" + hilomp);


            //提示弹窗
            $(".lottery-result-tips").show();
            setTimeout(function () {
                $(".lottery-result-tips").hide();countdown();
                interval = setInterval(countdown, 1000);
                window.location.reload();
            }, 20000);
            $("#period-all").text(period_all-1);
            $("#tips-period-result").text(lottery_result_point + 1 + "点，" + hilomp);

            /*var num = parseInt($("#last-period").text());     //取input框中的值+1 后 在赋给这个 开奖期数框
             $("#last-period").text(num);   //开奖结束后*/

            //暂停倒数
            clearInterval(interval);

            //alert(module);
            /* 【module后台客服切换的是哪个模式】1-自动模式；2-手动模式1：庄家赢；3-手动模式2：庄家输；4-手动模式3：开固定号*/
            if(module==1){     //自动默认 开随机数
                $.ajax({
                    url:url,
                    type:'post',
                    dataType:'json',
                    data:{period_all:period_all-1,lottery_result_point:lottery_result_point,timeCount:timeCount},
                    success:function(data){
                        $("#last-period").text(data.qishu);     //上期开奖结果
                        $("#last-point").text(data.num);      //上期开的1-6
                        $("#last-hilomp").text(data.dot);     //上期开的大小
                    }
                });
            }else if(module==2){ //手动1 庄家赢模式
                $.ajax({
                    url:url,
                    type:'post',
                    dataType:'json',
                    data:{period_all:period_all-1,timeCount:timeCount},
                    success:function(){
                        $("#last-period").text(data.qishu);     //上期开奖结果
                    }
                });
            }else if(module==3){ //手动2 庄家输模式
                $.ajax({
                    url:url,
                    type:'post',
                    dataType:'json',
                    data:{period_all:period_all-1,timeCount:timeCount},
                    success:function(){
                        $("#last-period").text(data.qishu);     //上期开奖结果
                    }
                });
            }/*else if(module==4){ //手动3 开固定号模式
                //alert('dddd');
                $.ajax({
                    url:url1,
                    type:'post',
                    dataType:'json',
                    data:{period_all:period_all-1,timeCount:timeCount},
                    success:function(){
                        $("#last-period").text(data.qishu);     //上期开奖结果
                    }
                });
            }*/



            return;
        }

        $("#time-left").text(keep2(timeCount)+"秒");         //左上角 2点，小


        //下期期数
        $("#next-period").text(keep4(next_period));             //【6】后端做好的数据，放到这，传给html页面
        //上期期数
        //$("#last-period").text(keep4(last_period));
        //上期点数、大小
       // $("#last-point").text(lottery_result_point+1);
        //$("#last-hilomp").text(hilomp);


    }


   // countdown();
 //   var interval = setInterval(countdown, 1000);            //每秒钟调用一次，用于开奖倒计时，刷新开奖结果


    var popup_close = $(".lottery-result-tips .popup-close");   //弹出开奖结果和右上角的x
    popup_close.click(function () {
        window.location.reload()
      //  countdown();
       // interval = setInterval(countdown, 1000);

    });
    var popup_close_btn =$(".lottery-result-tips .popup-close-btn");    //弹出开奖结果和确定按钮
    popup_close_btn.click(function () {
        window.location.reload()
      //  $(".popup-bg").hide();
      //  countdown();
      //  interval = setInterval(countdown, 1000);
    });



});