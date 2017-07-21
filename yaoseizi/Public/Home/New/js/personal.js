$(function () {

    // 余额支出少于一元 弹窗
    $(".balance-btn").on("click",function(){
        if( parseFloat($(".balance-num").text()) < 1 ){
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
        if($('#balanceBtn').val().length == 0){
            $(".wrong-message").text("输入金额不能为空");
            return false;
        }else if(!moneyReg.test($('#balanceBtn').val().trim())){
            $(".wrong-message").text("请输入正确金额");
            return false;
        }else {
            //保存数据
            commitData.moneyTxt = $('#balanceBtn').val().trim();

            // 发送ajax请求，跟后台互动
            $.ajax({
                url: '',
                data: commitData,
                success: function () {
                    // 跳转下一步
                    location.href = ""
                }
            })
        }
    });
    if($("article").children(".takeNotes").length == 0){
        $(".record-message").find("p").text("暂时没有记录")
    }
});

