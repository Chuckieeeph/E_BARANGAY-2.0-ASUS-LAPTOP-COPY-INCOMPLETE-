<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resident');

$fullname = $_SESSION['fullname'] ?? 'Resident';
$validation = $_SESSION['validation_status'] ?? 'unvalidated';
?>

<link rel="stylesheet" href="/e-barangay/assets/css/resident_dashboard.css">

<div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <img src="/e-barangay/assets/images/logo.jpg" class="logo">
            <h3>e-Barangay</h3>
            <small>Brgy. Cantil</small>
        </div>

        <nav class="menu">
            <a class="active" href="/e-barangay/resident/dashboard.php">Dashboard</a>

            <?php if ($validation === 'validated'): ?>
                <a href="/e-barangay/resident/request_clearance.php">Clearance Requests</a>
                <a href="/e-barangay/resident/my_clearances.php">My Documents</a>
            <?php else: ?>
                <a href="/e-barangay/resident/upload_valid_id.php">Upload Valid ID</a>
            <?php endif; ?>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main">

        <!-- Top Bar (Fixed Header) -->
        <header class="topbar">
            <div class="left">
                <h2>Welcome, <span><?= htmlspecialchars($fullname); ?></span></h2>
            </div>
            <div class="right">
                <a href="/e-barangay/public/logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <!-- Alert Message -->
        <?php if ($validation !== 'validated'): ?>
            <div class="alert warning">
                <strong>⚠️ Not Validated</strong><br>
                Upload a valid ID to unlock clearance requests.
                <br><br>
                <a class="btn small" href="/e-barangay/resident/upload_valid_id.php">
                    Verify Now
                </a>
            </div>
        <?php else: ?>
            <div class="alert success">
                ✅ Account Validated — You can now request clearances.
            </div>
        <?php endif; ?>

        <!-- Dashboard Cards -->
        <div class="cards">
            <div class="card">
                <h2>Validation Status</h2>
                <p class="<?= ($validation == 'validated') ? 'ok' : 'bad' ?>">
                    <?= ucfirst($validation) ?>
                </p>
            </div>

            <div class="card">
                <h2>Clearance Requests</h2>
                <p>0</p>
            </div>

            <div class="card">
                <h2>Pending Requests</h2>
                <p>0</p>
            </div>

            <div class="card">
                <h2>Cases Reported</h2>
                <p>0</p>
            </div>
        </div>
    </main>
</div>

<script>
function refreshCounts() {
  fetch('/e-Barangay/api/dashboard_counts.php')
    .then(r => r.json())
    .then(data => {
      if (document.getElementById('pending-clearances'))
          document.getElementById('pending-clearances').textContent = data.pending_clearances;

      if (document.getElementById('pending-verifications'))
          document.getElementById('pending-verifications').textContent = data.pending_verifications;

      if (document.getElementById('total-residents'))
          document.getElementById('total-residents').textContent = data.total_residents;
    })
    .catch(err => console.error("Error refreshing counts:", err));
}

// First load + every 5s
refreshCounts();
setInterval(refreshCounts, 5000);
</script>
