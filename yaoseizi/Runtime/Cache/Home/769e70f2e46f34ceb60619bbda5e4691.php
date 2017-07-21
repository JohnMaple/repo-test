<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no">
    <title>猜骰子</title>
    <link rel="stylesheet" href="/Public/Home/New/css/style.css">
    <script src="/Public/Home/New/js/jquery.min.js"></script>
    <script src="/Public/Home/New/js/flexible.js"></script>
</head>
<body class="index">
<div class="index-content-wrap content-wrap">
    <div class="index-content content">

        <ul class="header clearfix">
            <li>
                <p class="font-26">距离<span id="next-period"><?php echo ($award_info["nex_periods"]); ?></span>期开奖时间</p>
                <p class="fs-1" id="time-left"><?php echo ($award_info["wait_time"]); ?>秒</p>
            </li>
            <li>
                <!-- <p>上期开奖结果</p>
                 <p class="fs-1">第<span id="last-period"><?php echo ($award_info["pre_periods"]); ?></span>期：<span id="last-point"><?php echo ($award_info["pre_dot"]); ?></span>点，<span
                         id="last-hilomp"><?php echo ($award_info["pre_big_dot"]); ?></span></p>-->
                <a href="<?php echo U('Index/record');?>">
                    <p style="color: white">开奖记录</p>
                    <p style="text-decoration:underline" class="fs-1"><span  id="last-hilomp"><?php echo ($pre_award_str); ?></span></p>
                </a>
            </li>
        </ul>
     <div class="scrollbar" style="height:22px">
            <p><?php echo ($rollinfo); ?></p>
        </div>

        <!--     <ul class="function-zone clearfix">-->
        <!-- <?php if($member["mobile"] == ''): ?><li class="history-record fl"><a href="<?php echo U('Personal/index');?>"></a></li><?php endif; ?> -->
        <!--<li class="lottery-explain fr"></li>-->
        <!--  </ul>-->
        <div class="trend-wrapper" style="margin-bottom: 0.2rem;">
            <div>
               
                <input type="hidden" value="<?php echo ($pre_award_str1); ?>"
                       id="J_trend" style="width: 700px"/>
            </div>
            <div>
                <canvas width="600" id="canvas" height="120"
                        style="background-color: rgba(255, 255, 255, 1);">你的浏览器不支持HTML5 canvas
                    ，请使用 google chrome 浏览器
                    打开.
                </canvas>
            </div>
        </div>
        <div class="lottery-zone">
            <div class="lottery-result-img">
                <img src="/Public/Home/New/images/dice-1.png" alt="1">
                <img src="/Public/Home/New/images/dice-2.png" alt="1">
                <img src="/Public/Home/New/images/dice-3.png" alt="1">
                <img src="/Public/Home/New/images/dice-4.png" alt="1">
                <img src="/Public/Home/New/images/dice-5.png" alt="1">
                <img src="/Public/Home/New/images/dice-6.png" alt="1">
                <img src="/Public/Home/New/images/dice.gif" style="display: none;">
            </div>
            <div class="tips">
                <p>本次押注：<span class="fs-1"><?php echo ($my["dot"]); ?></span></p>
                <p>筹码数：<span class="fs-1"><?php echo ($my["money"]); ?></span></p>
                <p>我的余额：<span class="fs-1"><?php echo ($member["money"]); ?></span></p>

            </div>
            <div class="recharge"></div>
        </div>


        <!--05.19-->
        <div class="bet-zone">
            <ul class="bet">
                <!-- <li class="bet-himlop">
                    <a href="javascript:;" data-dot="8">
                        <em>大</em><br>
                        <span>1赔1.9</span>
                    </a>
                </li> -->
                <li class="bet-himlop">
                    <a href="javascript:;" data-dot="7">
                        <em>小</em><br>
                        <span>1赔1.9</span>
                    </a>
                </li>
                <li class="bet-point">
                    <ul>
                        <li>
                            <a href="javascript:;" data-dot="1">
                                <em>1</em><br>
                                <span>1赔5</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" data-dot="2">
                                <em>2</em><br>
                                <span>1赔5</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" data-dot="3">
                                <em>3</em> <br>
                                <span>1赔5</span>
                            </a>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <a href="javascript:;" data-dot="4">
                                <em>4</em> <br>
                                <span>1赔5</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" data-dot="5">
                                <em>5</em> <br>
                                <span>1赔5</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" data-dot="6">
                                <em>6</em><br>
                                <span>1赔5</span>
                            </a>
                        </li>
                    </ul>


                </li>
                <!-- <li class="bet-himlop">
                    <a href="javascript:;" data-dot="7">
                        <em>小</em><br>
                        <span>1赔1.9</span>
                    </a>
                </li> -->
                <li class="bet-himlop">
                    <a href="javascript:;" data-dot="8">
                        <em>大</em><br>
                        <span>1赔1.9</span>
                    </a>
                </li>
            </ul>

        </div>

    </div>

</div>
</div>
<div class="popup">
    <!--骰子开奖结束时显示-->
    <div class="lottery-result-tips popup-bg">
        <div class="popup-wrap">
            <div class="popup-tittle">
                信息
                <span class="popup-close"></span>
            </div>
            <div class="popup-container">
                <p>第<span id="period-all"></span>期:<span id="tips-period-result"></span></p>
                <p class="bet-result">本期没有投注，下期加油</p>
                <div class="popup-close-btn">确定</div>
            </div>
        </div>
    </div>
    <!--投注成功提示-->
    <div class="lottery-success-tips popup-bg">
        <div class="popup-wrap">
            <div class="popup-tittle">
                信息
                <span class="popup-close"></span>
            </div>
            <div class="popup-container">
                <p class="bet-result res_msg">投注成功，祝您好运！</p>
                <div class="popup-close-btn">确定</div>
            </div>
        </div>
    </div>
    <!--充值页面-->
    <div class="recharge-popup popup-bg">
        <div class="mask"></div>
        <div class="recharge-popup-wrapper"> <!-- 07.04 + -->
            <div class="recharge-amounts">
                <ul class="recharge-amount-list">
                    <li class="recharge-amount-20" data-money="20"><a href="#"></a></li>
                    <li class="recharge-amount-50" data-money="50"><a href="#"></a></li>
                    <li class="recharge-amount-100" data-money="100"><a href="#"></a></li>
                    <li class="recharge-amount-200" data-money="200"><a href="#"></a></li>
                    <li class="recharge-amount-500" data-money="500"><a href="#"></a></li>
                    <li class="recharge-amount-1000" data-money="1000"><a href="#"></a></li>
                </ul>
            </div>
            <!--<p class="bet-active">本次押注：<span id="bet-point-dot">无</span></p>-->
            <p class="bet-balance"><span></span>账户余额：<?php echo ($member["money"]); ?></p>
            <!--<div class="bet-buttons">--> <!-- 07.04 - -->
            <!--<div>请选择金额</div>--> <!-- 07.04 - -->
            <div class="button-out popup-close-btn">退出</div>
            <!--</div>--> <!-- 07.04 - -->

        </div>

    </div>
    <!-- 07.04 + 下注页面 -->
    <div class="bet-popup popup-bg">
        <div class="mask"></div>
        <div class="bet-popup-wrapper">
            <div class="bet-amounts">
                <ul class="bet-amount-list">
                    <li class="bet-amount-2" data-money="2"><a href="#"></a></li>
                    <li class="bet-amount-5" data-money="5"><a href="#"></a></li>
                    <li class="bet-amount-10" data-money="10"><a href="#"></a></li>
                    <li class="bet-amount-20" data-money="20"><a href="#"></a></li>
                    <li class="bet-amount-50" data-money="50"><a href="#"></a></li>
                    <li class="bet-amount-100" data-money="100"><a href="#"></a></li>
                    <li class="bet-amount-200" data-money="200"><a href="#"></a></li>
                    <li class="bet-amount-500" data-money="500"><a href="#"></a></li>
                </ul>
            </div>
            <p class="bet-active">本次押注：<span id="recharge-point-dot">无</span></p>
            <p class="bet-balance"><span></span>账户余额：<?php echo ($member["money"]); ?></p>
            <!--<div class="bet-buttons">--> <!-- 07.04 - -->
            <!--<div>请选择金额</div>--> <!-- 07.04 - -->
            <div class="button-out popup-close-btn">退出</div>
            <!--</div>--> <!-- 07.04 - -->

        </div>

    </div>
    <!--游戏说明-->
    <div class="lottery-explain-popup popup-bg">
        <div class="lottery-explain-container">
            <h3>玩法规则</h3>
            <p>用户直接下注：大、小、点数。</p>
            <p class="warning">注：只出一个中奖结果</p>
            <p>买中开出的结果，用户可获得相应赔率的金额</p>
            <p>大：4点至6点</p>
            <p>小：1点至3点</p>
            <p>具体请参照骰子游戏下方赔率</p>
            <p>本游戏为计时开奖，每分钟开一盘，投注以实际支付完成时间为准，开奖前3秒不能购买当期，直接跳转购买下期。点数由电脑随机统一开奖，可和朋友一起娱乐，切莫沉迷。</p>
        </div>
    </div>
</div>
<div class="footer">
    <ul class="nav-bottom clearfix">
        <li class="active">
            <a href="#">
                <span class="nav-pic nav-pic-left"></span>
                <h3>猜大小</h3>
            </a>
        </li>
        <li>
            <a href="<?php echo U('Partake/index');?>">
                <span class="nav-pic nav-pic-center"></span>
                <h3>分享赚钱</h3>
            </a>
        </li>
        <li>
            <a href="<?php echo U('Personal/index');?>">
                <span class="nav-pic nav-pic-right"></span>
                <h3>个人中心</h3>
            </a>
        </li>
    </ul>
</div>

<script src="/Public/Home/New/js/index.js"></script>
<script>
    var c_width = $(document.body).width();
    //        每个格子的宽高
    var chess_width = c_width / 30;
    //        走势盘高
    var c_height = c_width / 30 * 6;
    $('#canvas').attr({width: c_width, height: c_height});
    var canvas;
    var context;
    var img_b = new Image();
    img_b.src = "/Public/Home/New/images/b.png";//蓝色图片
    var img_w = new Image();
    img_w.src = "/Public/Home/New/images/w.png";//红色图片
    //    console.log(img_b);
    var chessData = new Array(30);//这个为走势的二维数组用来保存棋盘信息，初始化0为没有走过的，1为红色，2为蓝色
    for (var x = 0; x < 30; x++) {
        chessData[x] = new Array(6);
        for (var y = 0; y < 6; y++) {
            chessData[x][y] = 0;
        }
    }
    function drawRect() {
        canvas = document.getElementById("canvas");
        context = canvas.getContext("2d");
        for (var i = 0; i <= c_width; i += chess_width) {
            context.beginPath();
            context.moveTo(0, i);
            context.lineTo(c_width, i);
            context.closePath();
            context.stroke();
            context.beginPath();
            context.moveTo(i, 0);
            context.lineTo(i, c_height);
            context.closePath();
            context.stroke();
        }
        var val = $("#J_trend").val();
        var a = val.split(",");
        var j = 0;
        var last_x = 26;
        var last_y = 0;
        var max_times = 5;
        var flag = 0;
		var max_y = 0;
		var maxfalg = 0; //每次的最大
        for (var i = 0; i < a.length; i++) {
//            第一个球
            if (i == 0) {
                if (a[0] == 1) {
                    drawChess(1, 26, 0);
                } else {
                    drawChess(2, 26, 0);
                }
                last_x = 26;
                last_y = 0;
            } else {
//                后面的球
                j = i - 1;
                if (a[i] == a[j]) {
                    last_x = last_x;
                    last_y = last_y + 1;
                    if (last_y > max_times) {
                   //     flag = 1;
                        last_x = last_x - 1;
                        last_y = last_y - 1;
                    }
					if(maxfalg == 1){
						last_x = last_x - 1;
						last_y = max_y;
					}
                    if (chessData[last_x][last_y] != 0 && maxfalg == 0) {
                        last_x = last_x - 1;
                        last_y = last_y - 1;
						max_y = last_y;
						maxfalg = 1;
                    }
                    if (a[i] == 1) {
                        drawChess(1, last_x, last_y);
                    } else {
                        drawChess(2, last_x, last_y);
                    }
                } else {
                    if (last_x < 1) {
                        console.log(1)
                        return;
                    }
                    if (flag == 1) {
                        max_times = max_times - 1;
                        flag = 0;
                    }
                    if (chessData[last_x][0] != 0) {
                        last_x = last_x - 1;
                    } else {
                        var max = last_x;
                        for (var k = max; k < 26; k++) {
                            if (chessData[k][0] == 0) {
                                max = max + 1;
                            }
                        }
                        last_x = max - 1;
                    }
                    last_y = 0;
                    if (a[i] == 1) {
                        drawChess(1, last_x, last_y);
                    } else {
                        drawChess(2, last_x, last_y);
                    }
                }
            }
        }
    }
    function drawChess(chess, x, y) {
        if  (chess == 1) {
            context.beginPath();
            context.fillStyle = "#00f";
            context.arc((x+0.5) * chess_width , (y+0.5) * chess_width, chess_width / 2 - 2,0,2*Math.PI );
            context.closePath();
            context.fill();
            chessData[x][y] = 1;
        } else  {
            context.beginPath();
            context.fillStyle = "#F00";
            context.arc((x+0.5) * chess_width , (y+0.5) * chess_width, chess_width / 2 - 2,0,2*Math.PI );
            context.closePath();
            context.fill();
            chessData[x][y] = 2;
        }
    }
    $(function () {
        drawRect();
    })
</script>
<script>
    var charge = 0;
    var dot = 1;
    var wait = <?php echo ($award_info["wait_time"]); ?>;
    var jis;
    var lottery_results = $(".lottery-result-img").find("img");
    lottery_results.eq(<?php echo ($award_info['pre_dot'] - 1); ?>).show().siblings().hide();
    if (wait == 0) {
        jieshu();
    } else {
        jis = setInterval(function () {
            --wait;
            if (wait == 0) {
                clearInterval(jis);
                jieshu();
            } else {
                $("#time-left").text((wait) + "秒");
            }

        }, 1000)
    }


    function jieshu() {
        $("#time-left").text("正在开奖中......");
        /* function count4(){
         var count = Math.floor(Math.random() * 6);      //Math.random是0-1 所以*6
         lottery_results.eq(count).show().siblings().hide(); //显示当前图片，其它兄弟图片隐藏
         }
         count4();
         interval2 = setInterval(count4, 250);       //每秒调用4次，图片切换速度更快，动画效果更好*/
        lottery_results.eq(6).show().siblings().hide(); //显示当前图片，其它兄弟图片隐藏

        setTimeout(function () {
            $.ajax({
                type: "GET",
                url: "<?php echo U('Index/ajaxGetAward');?>",
                dataType: "json",
                success: function (data) {
                    lottery_result_point = data.dot - 1;
                    lottery_results.eq(lottery_result_point).show().siblings().hide();
                    //clearInterval(interval2);
                    //提示弹窗
                    $("#period-all").text(data.period);
                    $("#tips-period-result").text(data.res_dot);
                    $(".bet-result").text(data.msg);
                    $(".lottery-result-tips").show();
                }
            });
        }, 2000)
    }

    $("#yazhu").click(function () {


    })
    $(".recharge").on("click", function () {
        charge = 1;
        $(".recharge-popup").show();
    });
    var bet = $(".bet").find("a");
    bet.on("click", function () {
        charge = 0;
        dot = $(this).attr('data-dot');
        $(".bet-popup").show();
        $("#recharge-point-dot").text($(this).find("em").text());

    });
    $(".bet-amount-list li,.recharge-amount-list li").click(function () {
        if (charge) {
            var money = $(this).attr('data-money');
            $.ajax({
                type: "POST",
                url: "<?php echo U('Index/ajaxRecharge');?>",
                data: {money: money},
                dataType: "json",
                success: function (data) {
                    // document.write(JSON.stringify(data));return;
                    if (data.status == 0) {
                        alert(data.msg);
                    } else {
                        window.location.href = data.msg;
                    }

                }
            });
        } else {
            var money = $(this).attr('data-money');
            $.ajax({
                type: "POST",
                url: "<?php echo U('Index/ajaxYazhu');?>",
                data: {dot: dot, money: money},
                dataType: "json",
                success: function (data) {
                    //提示弹窗
                    $(".res_msg").text(data.msg);
                    $(".lottery-success-tips").show();
                }
            });
        }
    })
</script>

</body>
</html>