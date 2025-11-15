<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');

require_once __DIR__ . '/../config/database.php';

// --- Fetch live counts from DB (use your actual table/column names) ---
$pendingCount = 0;
$verifyCount  = 0;
$residentCount = 0;

try {
    // pending clearance requests
    $q = $con->query("SELECT COUNT(*) AS total FROM clearance_requests WHERE status='pending'");
    if ($q) $pendingCount = (int)($q->fetch_assoc()['total'] ?? 0);

    // residents for verification
    $q = $con->query("SELECT COUNT(*) AS total FROM users WHERE role='resident' AND validation_status='pending'");
    if ($q) $verifyCount = (int)($q->fetch_assoc()['total'] ?? 0);

    // total registered residents
    $q = $con->query("SELECT COUNT(*) AS total FROM users WHERE role='resident'");
    if ($q) $residentCount = (int)($q->fetch_assoc()['total'] ?? 0);

} catch (Exception $e) {
    // avoid exposing DB errors on screen in production; log if needed
    error_log("Dashboard count error: " . $e->getMessage());
}
?>
<link rel="stylesheet" href="../assets/css/admin_dashboard.css">
<style>
/* Temporary reset to remove conflicting default styles */
body {
  background: #f4f6f9;
  font-family: "Segoe UI", sans-serif;
  margin: 0;
  padding: 0;
}
header {
  display: none; /* Hide the broken placeholder header */
}
footer {
  text-align: center;
}
.btn-create-secretary {
  display: inline-block;
  background-color: #007bff;
  color: #fff;
  padding: 10px 16px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  transition: background 0.2s ease;
}
.btn-create-secretary:hover { background-color: #0056b3; }
</style>

<div class="dashboard-container">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo"><h2>e-Barangay</h2></div>

    <div class="profile">
      <div class="avatar"><?= strtoupper(substr($_SESSION['fullname'] ?? 'A',0,1)); ?></div>
      <div class="profile-info">
        <h4><?= htmlspecialchars($_SESSION['fullname']); ?></h4>
        <span><?= ucfirst(htmlspecialchars($_SESSION['role'])); ?></span>
      </div>
    </div>

    <nav class="menu">
      <a class="active" href="dashboard.php">Dashboard</a>
      <a href="residents.php">Residents</a>
      <a href="certifications.php">Certifications</a>
      <a href="user_management.php">User Management</a>
      <a href="barangay_details.php">Barangay Details</a>
      <a href="audit_trail.php">Audit Trail</a>
      <a href="create_secretary.php">Create Secretary</a>
      <a href="/e-barangay/public/logout.php" class="logout">Logout</a>
    </nav>
  </aside>

  <!-- Content -->
  <main class="main-content">
    <h2>Admin Dashboard</h2>
    <p class="muted">Real-time barangay system overview</p>

    <!-- optional CTA -->
    <div style="margin-bottom:20px;">
      <a href="create_secretary.php" class="btn-create-secretary">+ Create Secretary Account</a>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <h3 id="pending-clearances"><?= (int)$pendingCount ?></h3>
        <p>Pending Clearance Requests</p>
      </div>

      <div class="stat-card">
        <h3 id="pending-verifications"><?= (int)$verifyCount ?></h3>
        <p>Residents for Verification</p>
      </div>

      <div class="stat-card">
        <h3 id="total-residents"><?= (int)$residentCount ?></h3>
        <p>Total Registered Residents</p>
      </div>
    </div>

  </main>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<!-- Optional: auto-refresh counts via AJAX (requires /api/dashboard_counts.php) -->
<script>

function refreshCounts() {
  fetch('/e-barangay/api/dashboard_counts.php')
    .then(r => r.json())
    .then(data => {
      document.getElementById('pending-clearances').textContent = data.pending_clearances ?? 0;
      document.getElementById('pending-verifications').textContent = data.pending_verifications ?? 0;
      document.getElementById('total-residents').textContent = data.total_residents ?? 0;
    })
    .catch(err => console.error('Failed to refresh counts', err));
}

// start immediately and poll
refreshCounts();
setInterval(refreshCounts, 5000);

</script>

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
