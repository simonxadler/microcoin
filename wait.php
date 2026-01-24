<?php
include "config.php";
include "check.php";

$wallet = $_GET['wallet'] ?? '';
$time_remaining = 0;

// حساب الوقت المتبقي
$file = "claims.txt";
if (file_exists($file)) {
    $lines = file($file);
    foreach ($lines as $line) {
        list($savedWallet, $time) = explode("|", trim($line));
        if ($savedWallet == $wallet) {
            $time_remaining = $timer - (time() - $time);
            if ($time_remaining < 0) $time_remaining = 0;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $site_name; ?> - Wait</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<div class="container py-4">

    <!-- ===== TOP ADS ===== -->
    <div class="row justify-content-center text-center mb-3">
        <div class="col-12 col-md-6 mb-2"><?php echo $ad_wait_1; ?></div>
        <div class="col-12 col-md-6 mb-2"><?php echo $ad_wait_2; ?></div>
    </div>

    <!-- ===== WAIT CARD ===== -->
    <div class="d-flex justify-content-center my-4">
        <div class="card shadow-lg p-4 text-center" style="width:100%; max-width:420px;">
            <h3 class="mb-3">Please wait before your next claim</h3>
            <div id="countdown" class="display-4 mb-3"><?php echo $time_remaining; ?></div>
            <a id="claim-btn" href="index.php" class="btn btn-primary w-100" style="display:none;">Return to Claim</a>
        </div>
    </div>

    <!-- ===== BOTTOM ADS ===== -->
    <div class="row justify-content-center text-center mt-3">
        <div class="col-12 col-md-6 mb-2"><?php echo $ad_wait_3; ?></div>
        <div class="col-12 col-md-6 mb-2"><?php echo $ad_wait_4; ?></div>
    </div>

</div>

<script>
// عداد تنازلي بالثواني
let time = <?php echo $time_remaining; ?>;
const countdownEl = document.getElementById('countdown');
const claimBtn = document.getElementById('claim-btn');

let interval = setInterval(() => {
    if(time > 0){
        countdownEl.textContent = time;
        time--;
    } else {
        clearInterval(interval);
        countdownEl.style.display = 'none';
        claimBtn.style.display = 'block';
    }
}, 1000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
