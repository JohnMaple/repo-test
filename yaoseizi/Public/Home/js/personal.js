$(function () {
    $('.popup').hide()
    // 余额支出少于一元 弹窗
    $(".balance-btn").on("click",function(){
        if( parseFloat($(".balance-num").text()) < 20 ){
            $(".message-modal").show();
        }else{
            $(".balance-modal").show();
        }
    });

    // 关闭 弹窗
    $(".close-btn").on("click",function () {
        $(this).parent().parent().parent().hide();
    });

    // 确定 关闭弹窗
    $(".true-btn").on("click",function () {
        $(".close-btn").click();
    });

    // 提现 弹窗
    var $balanceNum = $(".balance-num");
    $("#balanceBtn").on("input" , function () {

        // 获得焦点后，重新输入提现金额
        $(this).focus(function () {
            $("#balanceBtn").val("");
        });

        // 失去焦点时，保留两位小数点
        $(this).blur(function () {
            if($(this).val() !== "" && !isNaN(Math.round($(this).val()).toFixed(2))){
                $(this).val(parseInt($(this).val()).toFixed(2));
            }
        });

        // 输入的金额大于可提现金额
        if(parseInt($(this).val()) > parseInt($balanceNum.text())){
            $(this).val(parseFloat($(".balance-num ").text()));
            $(this).blur();
        }
    });

    // 隐藏错误提示
    var t;
    function start() {
        t = setTimeout("$('.wrong-message').text('')" , 2000);
    }

    function stop() {
        clearTimeout(t);
    }

    var commitData = {};

    var moneyReg = /^[0-9]+/;

    $(".pop-up-btn").on("click" , function () {

        // 执行隐藏错误提示
        stop();

        if($(".wrong-message").text() != null){
            start();
        }

        // 检验数据
        if($('#balanceBtn').val() == 0){
            $(".wrong-message").text("输入金额不能为空");
            return false;
        }else if($('#balanceBtn').val() < 20){
            $(".wrong-message").text("提现金额最低为20元");
            return false;
        }else if(!moneyReg.test($('#balanceBtn').val().trim())){
            $(".wrong-message").text("请输入正确金额");
            return false;
        }else {
            //保存数据
            commitData.money = $('#balanceBtn').val().trim();

            // 发送ajax请求，跟后台互动
            $.ajax({
                type: "POST",
                url: rechage_url,
                data: commitData,
                dataType: "json",
                success: function(data){
                    if(data.status == 1){
                        window.location.href = data.msg;
                    }else if(data.status == 10){
                        $('.popupmask').show();
						$('.popupmask').css({'opacity': 1});
                    }else{
						
                        alert(data.msg);
                        window.location.reload();
                    }

                }
            });
        }
    });
    if($("article").children(".takeNotes").length == 0){
        $(".record-message").find("p").text("暂时没有记录")
    }






    //点击按钮弹出弹窗
    $('#mobile_btn').click(function(){
        $('.popupmask').show();
        $('.popupmask').css({'opacity': 1});
    });
    //点击遮罩 或者X按钮 关闭弹窗
    $('.mask').click(function(){
        $('.popupmask').hide();
        $('.popupmask').css({'opacity': '0'});
    })
    $('.pop-close').click(function(){
        $('.popupmask').hide();
        $('.popupmask').css({'opacity': '0'});
    })
    //验证码倒计时
    $('#pop-validate').click(function(){
		var mobile_num=$("#mobile_num").val();
        if(mobile_num != ""){
			var time = 60;//设置倒计时的时间
			var set_time = setInterval(function(){
				if (time > 0) {
					time--;
					$('#pop-validate').text(time);
					$('#pop-validate').prop({'disabled': true});
				} else {
					$('#pop-validate').text('重发验证码');
					$('#pop-validate').prop({'disabled': false});
					clearInterval(set_time);
				}
			}, 1000);
		}
        

    });


    $('#pop-validate').click(function(){
        var mobile_num=$("#mobile_num").val();
        if(mobile_num != ""){
            var data={"mobile":mobile_num};
            $.ajax({
                type:"POST",
                url:sendSMS_url,
                data:data,
                dataType:"json",
                success:function(rs){
                    //console.log(rs);
                    alert(rs.info);
                    
                },
                error:function(error){
                    console.log(error);
                }
            });
        }else{
            alert("手机号不能为空");
        }

    });


    $('.popup-submit').click(function(){
        var mobile_num=$("#mobile_num").val();
        var code=$("#code").val();
        if(mobile_num != ""){
            var data={"mobile":mobile_num,"code":code};
                    //console.log(forget_ajax_url);
            $.ajax({
                type:"POST",
                url:add_mobile_url,
                data:data,
                dataType:"json",
                success:function(rs){
                    //console.log(rs);
                    alert(rs.info);
					if(rs.status == 1){
						location.href="";
					}
                    
                },
                error:function(error){
                    console.log(error);
                }
            });
        }else{
            alert("手机号不能为空");
        }

    });

});

