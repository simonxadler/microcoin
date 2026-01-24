<?php
require_once __DIR__ . '/security_footer.php';
verify_footer_protection();


$site_name = "Carthage Faucet";
$reward = 100; // satoshi
$referral_commission = 20; // نسبة عمولة الإحالة

$faucetpay_api = "";
$currency = "LTC";

$timer = 10; // بالثواني بين كل مطالبة

// إعلان الأعلى
$ad_top_1 = '<iframe src="https://zerads.com/ad/ad.php?width=300&ref=8213" marginwidth="0" marginheight="0" width="300" height="250" scrolling="no" border="0" frameborder="0"></iframe>';

// إعلان بين المطالبة والإحالة
$ad_wait_1 = '<iframe src="https://zerads.com/ad/ad.php?width=300&ref=8213" marginwidth="0" marginheight="0" width="300" height="250" scrolling="no" border="0" frameborder="0"></iframe>';

// إعلان بين الإحالة وآخر المطالبات
$ad_bottom_1 = '<iframe src="https://zerads.com/ad/ad.php?width=300&ref=8213" marginwidth="0" marginheight="0" width="300" height="250" scrolling="no" border="0" frameborder="0"></iframe>';

// إعلان الأسفل
$ad_bottom_2 = '<iframe src="https://zerads.com/ad/ad.php?width=300&ref=8213" marginwidth="0" marginheight="0" width="300" height="250" scrolling="no" border="0" frameborder="0"></iframe>';

// إعلانات صفحة الانتظار (اختياري - إذا كنت تستخدمها)
$ad_wait_2 = '<iframe src="https://zerads.com/ad/ad.php?width=300&ref=8213" marginwidth="0" marginheight="0" width="300" height="250" scrolling="no" border="0" frameborder="0"></iframe>';
$ad_wait_3 = '<iframe src="https://zerads.com/ad/ad.php?width=300&ref=8213" marginwidth="0" marginheight="0" width="300" height="250" scrolling="no" border="0" frameborder="0"></iframe>';
$ad_wait_4 = '<iframe src="https://zerads.com/ad/ad.php?width=300&ref=8213" marginwidth="0" marginheight="0" width="300" height="250" scrolling="no" border="0" frameborder="0"></iframe>';