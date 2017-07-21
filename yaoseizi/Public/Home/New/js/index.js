/**
 * Created by ZZ on 2017/5/8.
 */

$(function () {
    var popup = $(".popup");
    popup.show();
    popup.children().hide();
    

    
    $(".lottery-explain").on("click", function () {
        $(".lottery-explain-popup").show();
        $(".lottery-explain-popup").on("click", function () {
            $(".popup-bg").hide();
        });
    });
    

    
    $(".mask").click(function () {
        window.location.reload()
        $(".popup-bg").hide();
    });
    
    
    var popup_close = $(".popup-close");
    popup_close.click(function () {
        window.location.reload()
        $(".popup-bg").hide();
    });
    var popup_close_btn =$(".popup-close-btn");
    popup_close_btn.click(function () {
        window.location.reload()
        $(".popup-bg").hide();
    });

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

//公共滚动
$(function () {
    var notice = $(".scrollbar p");
    var noticeWidth = notice.width();
    var clientWidth = document.documentElement.getBoundingClientRect().width;
    notice.css({left: clientWidth + "px"});
    setInterval(function () {
        notice.animate({left:"-" + noticeWidth + "px"}, 20*noticeWidth, 'linear', function () {
            notice.css({left: clientWidth + "px"})
        } )
    }, 0)
});
