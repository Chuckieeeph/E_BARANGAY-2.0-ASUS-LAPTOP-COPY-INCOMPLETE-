<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('secretary');
require_once __DIR__ . '/../config/database.php';

$fullname = $_SESSION['fullname'] ?? 'Barangay Secretary';

// Initials
$nameParts = array_values(array_filter(explode(' ', trim($fullname))));
$firstTwo = array_slice($nameParts, 0, 2);
$initials = '';
foreach ($firstTwo as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}

// Handle approve / decline
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $con->prepare("UPDATE users SET validation_status='validated' WHERE user_id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}
if (isset($_GET['decline'])) {
    $id = intval($_GET['decline']);
    $stmt = $con->prepare("UPDATE users SET validation_status='unvalidated', valid_id=NULL WHERE user_id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}

// Fetch resident data
$res = $con->query("SELECT user_id, fullname, email, validation_status, valid_id 
                    FROM users 
                    WHERE role='resident' 
                    ORDER BY user_id DESC");
?>

<link rel="stylesheet" href="/e-barangay/assets/css/secretary_dashboard.css">

<header class="header">
  <div class="logo">e-BARANGAY</div>
  <div class="system-title">Barangay Cantil Resident & Clearance Management System</div>
</header>

<div class="dashboard-container">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="profile-section">
      <div class="avatar"><?= htmlspecialchars($initials) ?></div>
      <div class="profile-info">
        <span class="fullname"><?= htmlspecialchars($fullname) ?></span>
        <span class="role">Secretary</span>
      </div>
    </div>

    <nav class="nav-menu">
      <a href="/e-barangay/secretary/dashboard.php">Dashboard</a>
      <a href="/e-barangay/secretary/residents.php">Residents</a>
      <a class="active" href="/e-barangay/secretary/verify_residents.php">Validate Residents</a>
      <a href="/e-barangay/secretary/clearance_requests.php">Clearance Requests</a>
      <a href="/e-barangay/secretary/cases.php">Reported Cases</a>
    </nav>

    <div class="logout-section">
      <a class="logout-btn" href="/e-barangay/public/logout.php">Logout</a>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main-content">

    <h2>Verify Resident IDs</h2>

    <div class="card">
      <h3>Resident ID Verification</h3>
      <p class="muted">Review submitted Valid IDs and approve/decline requests.</p>

      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Valid ID</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>

        <?php if ($res && $res->num_rows > 0): ?>
          <?php while($r = $res->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($r['fullname']) ?></td>
              <td><?= htmlspecialchars($r['email']) ?></td>
              <td><?= htmlspecialchars($r['validation_status']) ?></td>
              <td>
                <?php if ($r['valid_id']): ?>
                  <a target="_blank" 
                     href="/e-barangay/public/uploads/valid_ids/<?= htmlspecialchars($r['valid_id']) ?>">
                     View ID
                  </a>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>

              <td>
                <?php if ($r['validation_status'] === 'pending'): ?>
                  <a class="btn small approve" href="?approve=<?= $r['user_id'] ?>">Approve</a>
                  <a class="btn small reject" href="?decline=<?= $r['user_id'] ?>" style="margin-left:8px;">Decline</a>
                <?php else: ?>
                  <span class="small" style="opacity:0.8;">No actions</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>

        <?php else: ?>
          <tr>
            <td colspan="5" style="text-align:center;">No resident records found.</td>
          </tr>
        <?php endif; ?>

        </tbody>
      </table>
    </div>

  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
