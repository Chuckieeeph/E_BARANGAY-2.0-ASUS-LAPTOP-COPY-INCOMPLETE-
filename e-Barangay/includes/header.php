<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
$fullname = $_SESSION['fullname'] ?? null;
$role = $_SESSION['role'] ?? null;
$validation_status = $_SESSION['validation_status'] ?? null;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>e-BARANGAY</title>
  <link rel="stylesheet" href="/e-barangay/public/assets/css/ui.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
  <header class="header card">
    <div class="brand">
      <?php if (file_exists(__DIR__ . '/../public/assets/images/logo.png')): ?>
        <img src="/e-barangay/public/assets/images/logo.png" alt="logo">
      <?php else: ?>
        <div style="width:40px;height:40px;border-radius:8px;background:linear-gradient(90deg,#0b74de,#0b61b3);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">EB</div>
      <?php endif; ?>
      <div>
        <h1>e-BARANGAY</h1>
        <div class="small">Barangay Resident & Clearance Management</div>
      </div>
    </div>

    <div class="actions">
      <?php if($fullname): ?>
        <div class="small text-muted">Signed in as <strong><?php echo htmlspecialchars($fullname); ?></strong></div>
        <div style="width:8px;"></div>
        <a class="btn secondary" href="/e-barangay/public/logout.php">Logout</a>
      <?php else: ?>
        <a class="btn" href="/e-barangay/public/login.php">Login</a>
      <?php endif;?>
    </div>
  </header>
  <main class="app">
