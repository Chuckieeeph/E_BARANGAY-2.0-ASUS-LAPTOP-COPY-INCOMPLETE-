<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('secretary');
require_once __DIR__ . '/../config/database.php';

$fullname = $_SESSION['fullname'] ?? 'Barangay Secretary';

// ✅ Generate initials using only the first and second name (backward-compatible)
$nameParts = array_values(array_filter(explode(' ', trim($fullname))));
$firstTwo = array_slice($nameParts, 0, 2);
$initials = '';
foreach ($firstTwo as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}

// ✅ Fetch dashboard counts
$pending_clearances = $con->query("SELECT COUNT(*) AS total FROM clearance_requests WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
$pending_verifications = $con->query("SELECT COUNT(*) AS total FROM users WHERE role='resident' AND validation_status='pending'")->fetch_assoc()['total'] ?? 0;
$total_residents = $con->query("SELECT COUNT(*) AS total FROM users WHERE role='resident' AND validation_status='validated'")->fetch_assoc()['total'] ?? 0;

// ✅ Fetch pending residents for verification
$residents = $con->query("SELECT user_id, fullname, valid_id, validation_status FROM users WHERE role='resident' AND validation_status='pending' ORDER BY user_id DESC LIMIT 5");

// ✅ Fetch pending clearance requests (NEW)
$clearances = $con->query("
    SELECT cr.request_id, u.fullname, cr.purpose, cr.status
    FROM clearance_requests cr
    JOIN users u ON cr.resident_id = u.user_id
    WHERE cr.status = 'pending'
    ORDER BY cr.request_id DESC
    LIMIT 5
");

// ✅ Fetch recent reported cases (NEW)
$cases = $con->query("
    SELECT c.case_id, u.fullname, c.case_type, c.status
    FROM cases c
    JOIN users u ON c.resident_id = u.user_id
    ORDER BY c.case_id DESC
    LIMIT 5
");
?>

<link rel="stylesheet" href="/e-barangay/assets/css/secretary_dashboard.css">

<header class="header">
  <div class="logo">e-BARANGAY</div>
  <div class="system-title">Barangay Cantil Resident & Clearance Management System</div>
</header>

<div class="dashboard-container">
  <aside class="sidebar">
    <div class="profile-section">
      <div class="avatar"><?= htmlspecialchars($initials) ?></div>
      <div class="profile-info">
        <span class="fullname"><?= htmlspecialchars($fullname) ?></span>
        <span class="role">Secretary</span>
      </div>
    </div>

    <nav class="nav-menu">
      <a class="active" href="/e-barangay/secretary/dashboard.php">Dashboard</a>
      <a href="/e-barangay/secretary/residents.php">Residents</a>
      <a href="/e-barangay/secretary/clearance_requests.php">Clearance Requests</a>
      <a href="/e-barangay/secretary/cases.php">Reported Cases</a>
    </nav>

    <div class="logout-section">
      <a class="logout-btn" href="/e-barangay/public/logout.php">Logout</a>
    </div>
  </aside>

  <main class="main-content">
    <h2>Secretary Dashboard</h2>

    <div class="stats-grid">
      <div class="stat-card green">
        <h3 id="pending-clearances"><?= $pending_clearances ?></h3>
        <p>Pending Clearance Requests</p>
      </div>
      <div class="stat-card yellow">
        <h3 id="pending-verifications"><?= $pending_verifications ?></h3>
        <p>Residents for Verification</p>
      </div>
      <div class="stat-card blue">
        <h3 id="total-residents"><?= $total_residents ?></h3>
        <p>Total Registered Residents</p>
      </div>
    </div>

    <div class="card">
      <h3>Residents Pending ID Verification</h3>
      <table>
        <thead>
          <tr><th>Full Name</th><th>Valid ID</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if ($residents && $residents->num_rows > 0): ?>
            <?php while($r = $residents->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($r['fullname']) ?></td>
                <td><a href="/e-barangay/public/uploads/valid_ids/<?= htmlspecialchars($r['valid_id']) ?>" target="_blank">View ID</a></td>
                <td><?= htmlspecialchars($r['validation_status']) ?></td>
                <td>
                  <form method="post" action="verify_residents.php" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $r['user_id'] ?>">
                    <button name="action" value="approve" class="btn small approve">Approve</button>
                    <button name="action" value="reject" class="btn small reject">Reject</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">No pending verifications.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h3>Pending Clearance Requests</h3>
      <table>
        <thead>
          <tr><th>Resident</th><th>Purpose</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if ($clearances && $clearances->num_rows > 0): ?>
            <?php while($c = $clearances->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($c['fullname']) ?></td>
                <td><?= htmlspecialchars($c['purpose']) ?></td>
                <td><?= htmlspecialchars($c['status']) ?></td>
                <td>
                  <form method="post" action="review_clearance.php" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?= $c['request_id'] ?>">
                    <button name="action" value="approve" class="btn small approve">Approve</button>
                    <button name="action" value="reject" class="btn small reject">Reject</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">No pending clearance requests.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h3>Recent Reported Cases</h3>
      <table>
        <thead>
          <tr><th>Resident</th><th>Case Title</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php if ($cases && $cases->num_rows > 0): ?>
            <?php while($case = $cases->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($case['fullname']) ?></td>
                <td><?= htmlspecialchars($case['case_title']) ?></td>
                <td><?= htmlspecialchars($case['status']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="3" style="text-align:center;">No recent reported cases.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
