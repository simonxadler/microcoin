<?php

include "referrals.php";

if (isset($_GET['ref']) && isset($_GET['wallet'])) {
    $referrer = trim($_GET['ref']);
    $referred = trim($_GET['wallet']);
    saveReferral($referrer, $referred);
}

function canClaim($wallet, $timer) {
    $file = "claims.txt";
    if (!file_exists($file)) return true;

    $lines = file($file);
    foreach ($lines as $line) {
        list($savedWallet, $time) = explode("|", trim($line));
        if ($savedWallet == $wallet) {
            if (time() - $time < $timer) {
                return false;
            }
        }
    }
    return true;
}