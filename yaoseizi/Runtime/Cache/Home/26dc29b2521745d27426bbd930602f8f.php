<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="format-detection" content="telephone=no" />
	<meta name="format-detection" content="telphone=no, email=no"/>
    <!--<meta name="viewport" id="viewport" content="width=device-width, initial-scale=1">-->
    <title>个人中心</title>
    <script src="/Public/Home/js/jquery-2.1.4.js"></script>
    <script src="/Public/Home/js/flexiblem.js"></script>
    <script src="/Public/Home/js/personal.js"></script>
    <link rel="stylesheet" href="/Public/Home/css/reset.css">
    <link rel="stylesheet" href="/Public/Home/css/personal.css">
    <script>
        var rechage_url = '<?php echo U("Personal/rechage");?>';
        var sendSMS_url = '<?php echo U("Personal/sendsms");?>';
        var add_mobile_url = '<?php echo U("Personal/mobile_num");?>';
    </script>
</head>
<body>
    <article>
        <section class="header">
            <div class="head-bg">
                <div class="head-pic"></div>
            </div>
            <h3 class="user-name font-16">匿名</h3>
            <p class="font-13">我的ID：<span class="number"><?php echo ($my_id); ?></span></p>
        </section>
        <section class="balance">
            <div class="my-balance">
                <p class="balance-txt font-16"><span class="balance-pic"></span>我的余额：<span class="balance-num font-13"><?php echo ($member["money"]); ?></span></p>
                <a href="#" class="balance-btn font-13">余额取出</a>
            </div>
        </section>
        <section class="record font-13">
			<!-- <a id="mobile_btn">
				<p class="">
				<span class="custom-mobile-pic left"></span>我的账号（完善资料领取 6 元奖励）<span class="right"></span></p>
			</a> -->
			<?php if($member["mobile"] == ''): ?><!-- <a id="mobile_btn">
					<p class="">
					<span class="custom-mobile-pic left"></span>我的账号<span class="right"></span></p>
				</a> -->
			<?php else: ?>
				<a>
					<p class="">
					<span class="custom-mobile-pic left"></span>我的账号（<?php echo ($member["mobile"]); ?>）</p>
				</a><?php endif; ?>
            <a href="<?php echo U('Personal/cathectic');?>">
                <p class="cathectic-txt"><span class="cathectic-pic left"></span>投注记录<span class="right"></span></p>
            </a>
            <a href="<?php echo U('Personal/deposit');?>">
                <p class="kiting-txt"><span class="kiting-pic left"></span>提现记录<span class="right"></span></p>
            </a>
            <a href="<?php echo U('Index/record');?>">
                <p class="reward-txt"><span class="reward-pic left"></span>开奖记录<span class="right"></span></p>
            </a>
            <a href="<?php echo U('Personal/commision');?>">
                <p class="commission-txt"><span class="commission-pic left"></span>佣金记录<span class="right"></span></p>
            </a>
            <a href="<?php echo U('Personal/cService');?>">
                <p class="custom-service-txt"><span class="custom-service-pic left"></span>联系客服<span class="right"></span></p>
            </a>
            
        </section>
    </article>
    <nav>
        <ul class="font-10">
            <li>
                <a href="<?php echo U('Index/index');?>">
                    <div class="nav-pic bottom-left"></div>
                    <h3 >猜大小</h3>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Partake/index');?>">
                    <div class="nav-pic bottom-center"></div>
                    <h3>分享赚钱</h3>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Personal/index');?>">
                    <div class="nav-pic bottom-right"></div>
                    <h3>个人中心</h3>
                </a>
            </li>
        </ul>
    </nav>
    <section class="message-modal">
        <div class="opacity"></div>
        <div class="message font-13">
            <h3 class="font-16">信息<span class="close-btn font-30"></span></h3>
            <p>最少20元才能取出</p>
            <div class="true-btn font-16">确定</div>
        </div>
    </section>
    <section class="balance-modal">
        <div class="opacity"></div>
        <div class="balance-pop-up font-13">
            <h3 class="font-16">提现<span class="close-btn font-30"></span></h3>
            <form action="">
                <div class="balance-t">
                    <p class="balance-txt font-13">金额：</p>
                    <p class="balance-num font-13"><?php echo ($member["money"]); ?></p>
                </div>
                <div class="balance-b fn-clear fn-13">
                    <label class="fn-left" for="balanceBtn">提现：</label>
                    <input class="fn-left" id="balanceBtn" type="text" placeholder="请输入金额">
                </div>
                <input class="pop-up-btn fn-16" type="button" value="确定">
            </form>
            <p class="wrong-message font-10"></p>
        </div>
    </section>
    <div class="lottery-explain-popup popup-bg popup"><!--<a href="<?php echo U('Index/game');?>"></a>-->
        <div class="lottery-explain-container">
            <h3>请长按以下二维码添加客服联系提现！！</h3>
            <p><img src="/Public/Home/images/kefu.png"></p>
        </div>

    </div>

    <div class="popupmask" style="display:none;">
        <div class="popup1">
            <div class="popup-header">
                <h4 style="font-size:22px;">我的账号</h4>
                <span class="pop-close" style="font-size:22px;">X</span>
            </div>
            <div class="popup-content">
                <div class="popup-input-wrap">
                    <input type="number" id="mobile_num" placeholder="请输入您的手机号" style="font-size:22px;">
                </div>
                <div class="popup-input-wrap">
                    <input type="text" id="code" placeholder="请输入您的验证码" style="font-size:22px;">
                    <button id="pop-validate" style="font-size:22px;">获取验证码</button>
                </div>
                <button class="popup-submit" style="font-size:22px;">确定</button>
            </div>
        </div>
        <div class="mask" ></div>
    </div>
</body>
</html>