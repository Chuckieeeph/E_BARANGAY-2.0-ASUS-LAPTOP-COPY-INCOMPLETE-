<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resident');
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$user_id = $_SESSION['user_id'];
$success = $error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purpose = trim($_POST['purpose'] ?? '');
    if ($purpose === '') $error = "Purpose cannot be empty.";
    else {
        $stmt = $con->prepare("INSERT INTO clearance_requests (user_id, purpose) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $purpose);
        if ($stmt->execute()) $success = "Request submitted.";
        else $error = "Failed to submit request.";
    }
}
?>

<aside class="sidebar card">
  <div class="profile">
    <div class="avatar"><?= strtoupper(substr($_SESSION['fullname'] ?? 'R',0,1)); ?></div>
    <div>
      <div style="font-weight:700;"><?php echo htmlspecialchars($_SESSION['fullname']); ?></div>
      <div class="small text-muted"><?= htmlspecialchars($_SESSION['role'] ?? 'resident'); ?></div>
    </div>
  </div>
  <nav class="nav">
    <a href="/e-barangay/resident/dashboard.php">Dashboard</a>
    <a class="active" href="/e-barangay/resident/request_clearance.php">Request Clearance</a>
    <a href="/e-barangay/resident/my_clearances.php">My Requests</a>
    <a href="/e-barangay/public/logout.php">Logout</a>
  </nav>
</aside>

<section class="card">
  <div class="page-header">
    <div>
      <h2>Request Barangay Clearance</h2>
      <div class="muted">Provide a short purpose for your request.</div>
    </div>
  </div>

  <?php if($success): ?><div class="alert success"><?=htmlspecialchars($success)?></div><?php endif; ?>
  <?php if($error): ?><div class="alert danger"><?=htmlspecialchars($error)?></div><?php endif; ?>

  <form method="post">
    <div class="form-row full">
      <label class="small">Purpose</label>
      <input class="input" type="text" name="purpose" placeholder="e.g., For employment, passport application..." required>
    </div>

    <div class="row">
      <button class="btn" type="submit">Submit Request</button>
      <a class="btn secondary" href="/e-barangay/resident/dashboard.php">Cancel</a>
    </div>
  </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
