<?php
// User-Agent
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

$mobilePattern = '/\b(Mobi(le)?|Android|iP(hone|ad|od)|BlackBerry|IEMobile|Opera Mini)\b/i';
$isMobile    = preg_match($mobilePattern, $ua);

if ($isMobile) {
   echo <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enable Desktop Mode</title>
  <style>
    body { font-family: sans-serif; text-align: center; padding: 80px 20px; background: #f5f5f5; }
    .btn  { display: inline-block; margin-top: 20px; padding: 12px 24px;
            background:rgb(177, 149, 62); color: #fff; text-decoration: none; border-radius: 4px; }
  </style>
</head>
<body>
  <h1>⚠️ Please enable Desktop Mode</h1>
  <p>On Browser for Android: open the menu (⋮) → check “Desktop site,” then click the button below.</p>
  <a href="" class="btn">Done—Reload</a>
</body>
</html>
HTML;
    exit;
}

// 3b) Kalau UA tidak mobile → redirect ke login
header("Location: pages/login/loginCustomer.php");
exit;
?>