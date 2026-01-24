<?php
// حفظ الإحالة
function saveReferral($referrer, $referred) {
    $referrer = trim($referrer);
    $referred = trim($referred);
    
    if ($referrer == $referred) {
        return false;
    }
    
    if (file_exists("referrals.txt")) {
        $referrals = file("referrals.txt", FILE_IGNORE_NEW_LINES);
        foreach ($referrals as $line) {
            $parts = explode("|", $line);
            if (isset($parts[1]) && $parts[1] == $referred) {
                return false;
            }
        }
    }
    
    file_put_contents("referrals.txt", $referrer."|".$referred."|".time().PHP_EOL, FILE_APPEND);
    return true;
}

// الحصول على المُحيل
function getReferrer($wallet) {
    if (!file_exists("referrals.txt")) {
        return null;
    }
    
    $referrals = file("referrals.txt", FILE_IGNORE_NEW_LINES);
    foreach ($referrals as $line) {
        $parts = explode("|", $line);
        if (isset($parts[1]) && $parts[1] == $wallet) {
            return $parts[0];
        }
    }
    
    return null;
}

// عدد الإحالات
function getReferralCount($wallet) {
    if (!file_exists("referrals.txt")) {
        return 0;
    }
    
    $count = 0;
    $referrals = file("referrals.txt", FILE_IGNORE_NEW_LINES);
    foreach ($referrals as $line) {
        $parts = explode("|", $line);
        if (isset($parts[0]) && $parts[0] == $wallet) {
            $count++;
        }
    }
    
    return $count;
}
?>