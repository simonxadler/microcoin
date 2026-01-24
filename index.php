<?php 
include "config.php";
include "referrals.php";

// معالجة رابط الإحالة
$referrer_wallet = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$user_wallet = isset($_GET['wallet']) ? trim($_GET['wallet']) : '';

// توليد كابتشا
$num1 = rand(1, 10);
$num2 = rand(1, 10);
$captcha_answer = $num1 + $num2;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $site_name; ?></title>

<!-- Bootstrap CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.referral-input {
    cursor: pointer;
}
.stats-badge {
    background: #e7f3ff;
    color: #0d6efd;
    padding: 8px 15px;
    border-radius: 20px;
    display: inline-block;
    margin: 5px;
    font-size: 14px;
    font-weight: 500;
}
</style>

</head>
<body class="bg-dark text-light">

<div class="container py-4">

    <!-- ===== TOP AD ===== -->
    <div class="row justify-content-center text-center mb-3">
        <div class="col-12"><?php echo $ad_top_1; ?></div>
    </div>

    <!-- ===== MAIN CARD ===== -->
    <div class="d-flex justify-content-center my-4">
        <div class="card shadow-lg p-4" style="width:100%; max-width:420px;">
            <h3 class="text-center mb-3"><?php echo $site_name; ?></h3>

            <form method="GET" action="index.php" id="walletForm">
                <div class="mb-3">
                    <input type="text" class="form-control" name="wallet" 
                           id="walletInput"
                           placeholder="Enter your FaucetPay Email" 
                           value="<?php echo htmlspecialchars($user_wallet); ?>" 
                           required>
                </div>
                
                <?php if (!empty($referrer_wallet)): ?>
                <input type="hidden" name="ref" value="<?php echo htmlspecialchars($referrer_wallet); ?>">
                <div class="alert alert-info small mb-3">
                    🎁 You were referred! Your referrer will earn <?php echo $referral_commission; ?>% commission.
                </div>
                <?php endif; ?>

                <?php if (empty($user_wallet)): ?>
                <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
                <?php endif; ?>
            </form>

            <p class="text-center mt-3 small text-muted">
                Reward: <?php echo $reward; ?> Satoshi <?php echo $currency; ?>
            </p>
        </div>
    </div>

    <!-- ===== AD BETWEEN CLAIM AND CAPTCHA ===== -->
    <?php if (!empty($user_wallet)): ?>
    <div class="row justify-content-center text-center my-3">
        <div class="col-12"><?php echo $ad_wait_1; ?></div>
    </div>

    <!-- ===== CAPTCHA & CLAIM FORM ===== -->
    <div class="d-flex justify-content-center my-4">
        <div class="card shadow-lg p-4" style="width:100%; max-width:420px;">
            <h5 class="text-center mb-3">🔐 Security Check</h5>
            
            <form method="POST" action="process.php">
                <input type="hidden" name="wallet" value="<?php echo htmlspecialchars($user_wallet); ?>">
                <?php if (!empty($referrer_wallet)): ?>
                <input type="hidden" name="ref" value="<?php echo htmlspecialchars($referrer_wallet); ?>">
                <?php endif; ?>
                
                <!-- Simple Math Captcha -->
                <div class="mb-3">
                    <label class="form-label">Solve this simple math:</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-primary text-white fw-bold">
                            <?php echo $num1; ?> + <?php echo $num2; ?> =
                        </span>
                        <input type="number" class="form-control form-control-lg text-center" 
                               name="captcha_input" 
                               placeholder="?" 
                               required 
                               autocomplete="off">
                        <input type="hidden" name="captcha_answer" value="<?php echo base64_encode($captcha_answer); ?>">
                    </div>
                    <small class="text-muted d-block mt-2">Enter the answer to verify you're human</small>
                </div>
                
                <button type="submit" class="btn btn-success btn-lg w-100">
                    ✅ Verify & Claim Now
                </button>
            </form>
        </div>
    </div>

    <!-- ===== AD BETWEEN CAPTCHA AND REFERRAL ===== -->
    <div class="row justify-content-center text-center my-3">
        <div class="col-12"><?php echo $ad_bottom_1; ?></div>
    </div>

    <!-- ===== REFERRAL SECTION ===== -->
    <?php 
        $referral_count = getReferralCount($user_wallet);
        $referral_link = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?ref=".urlencode($user_wallet);
    ?>
    <div class="d-flex justify-content-center my-4">
        <div class="card shadow-lg p-4" style="width:100%; max-width:420px;">
            <h5 class="text-center mb-3">🎁 Your Referral Program</h5>
            
            <div class="text-center mb-3">
                <span class="stats-badge">
                    <strong><?php echo $referral_commission; ?>%</strong> Commission
                </span>
                <span class="stats-badge">
                    <strong><?php echo $referral_count; ?></strong> Referrals
                </span>
            </div>

            <p class="small text-center mb-2 text-muted">
                Share your link and earn <?php echo $referral_commission; ?>% from every claim!
            </p>
            
            <input type="text" 
                   class="form-control referral-input" 
                   value="<?php echo htmlspecialchars($referral_link); ?>" 
                   readonly 
                   onclick="this.select(); document.execCommand('copy'); alert('Link copied!');">
            
            <p class="text-center small mt-2 mb-0 text-muted">
                💡 Click the link to copy
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===== RECENT CLAIMS SECTION ===== -->
    <?php
    $recent_claims = [];
    if (file_exists("claims.txt")) {
        $claims = file("claims.txt", FILE_IGNORE_NEW_LINES);
        $recent_claims = array_slice(array_reverse($claims), 0, 10);
    }
    
    if (count($recent_claims) > 0):
    ?>
    <div class="d-flex justify-content-center my-4">
        <div class="card shadow-lg p-4" style="width:100%; max-width:420px;">
            <h5 class="text-center mb-3">📋 Recent Claims</h5>
            
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr class="text-muted small">
                            <th>Wallet</th>
                            <th class="text-end">Time</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php foreach ($recent_claims as $claim): 
                            $parts = explode("|", $claim);
                            if (count($parts) == 2):
                                $claim_wallet = $parts[0];
                                $claim_time = $parts[1];
                                
                                if (strlen($claim_wallet) > 20) {
                                    $display_wallet = substr($claim_wallet, 0, 8).'...'.substr($claim_wallet, -6);
                                } else {
                                    $display_wallet = $claim_wallet;
                                }
                                
                                $time_ago = time() - $claim_time;
                                if ($time_ago < 60) {
                                    $time_display = $time_ago.'s ago';
                                } elseif ($time_ago < 3600) {
                                    $time_display = floor($time_ago / 60).'m ago';
                                } elseif ($time_ago < 86400) {
                                    $time_display = floor($time_ago / 3600).'h ago';
                                } else {
                                    $time_display = floor($time_ago / 86400).'d ago';
                                }
                        ?>
                        <tr>
                            <td class="text-truncate" style="max-width: 200px;">
                                <?php echo htmlspecialchars($display_wallet); ?>
                            </td>
                            <td class="text-end text-muted">
                                <?php echo $time_display; ?>
                            </td>
                        </tr>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
            
            <p class="text-center small mt-3 mb-0 text-muted">
                Total Claims: <strong><?php echo count(file("claims.txt", FILE_IGNORE_NEW_LINES)); ?></strong>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===== BOTTOM AD ===== -->
    <div class="row justify-content-center text-center mt-3">
        <div class="col-12"><?php echo $ad_bottom_2; ?></div>
    </div>

</div>

<footer class="bg-dark text-light mt-5 py-3">
  <div class="container text-center small">
    <span>
    <a class="text-primary text-decoration-none fw-semibold">
    <?php echo $site_name . " © " . date('Y'); ?>
</a>
      Using
      <a href="https://99makemoneyonline.com/home/scripts/" class="text-warning text-decoration-none fw-semibold ms-1">
  Carthage Faucet Script
</a>

<!-- CARTHAGE-LICENSE-2026 -->
    </span>
  </div>
</footer>

<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>