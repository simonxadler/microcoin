<?php
function verify_footer_protection() {

    $mainFile = __DIR__ . '/index.php';

    // إذا اختفى الملف الرئيسي = تعطيل مباشر
    if (!file_exists($mainFile)) {
        header("Location: /license-error.html");
        exit;
    }

    $content = file_get_contents($mainFile);

    // التوقيعات الإلزامية
    $required = [
        '<a href="https://99makemoneyonline.com/home/scripts/" class="text-warning text-decoration-none fw-semibold ms-1">',
        'Carthage Faucet Script',
        'CARTHAGE-LICENSE-2026'
    ];

    foreach ($required as $item) {
        if (strpos($content, $item) === false) {
            header("Location: https://99makemoneyonline.com/home/copyright/");
            exit;
        }
    }
}
