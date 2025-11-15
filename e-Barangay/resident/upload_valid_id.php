<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resident');
require_once __DIR__ . '/../config/database.php';

$user_id = $_SESSION['user_id'];

$success = $error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['valid_id'])) {
    $file = $_FILES['valid_id'];
    $upload_dir = __DIR__ . '/../public/uploads/valid_ids/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'id_' . $user_id . '_' . time() . '.' . $ext;
    $target = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $stmt = $con->prepare("UPDATE users SET valid_id=?, validation_status='pending' WHERE user_id=?");
        $stmt->bind_param("si", $filename, $user_id);
        $stmt->execute();
        $success = "✅ ID uploaded successfully. Please wait for the secretary to verify your ID.";
        $_SESSION['validation_status'] = 'pending';
    } else {
        $error = "❌ Upload failed. Please try again.";
    }
}
?>

<link rel="stylesheet" href="/e-barangay/assets/css/upload_valid_id.css">

<div class="upload-layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="profile">
      <div class="avatar"><?= strtoupper(substr($_SESSION['fullname'] ?? 'R',0,1)); ?></div>
      <div>
        <div class="fullname"><?= htmlspecialchars($_SESSION['fullname']); ?></div>
        <div class="role"><?= htmlspecialchars($_SESSION['role'] ?? 'resident'); ?></div>
      </div>
    </div>

    <nav class="menu">
      <a href="/e-barangay/resident/dashboard.php">Dashboard</a>
      <a class="active" href="/e-barangay/resident/upload_valid_id.php">Upload Valid ID</a>
      <a href="/e-barangay/public/logout.php" class="logout">Logout</a>
    </nav>
  </aside>

  <!-- Main Section -->
  <main class="main-content">
    <div class="page-header">
      <h2>Upload Valid ID</h2>
      <p class="subtitle">Accepted formats: JPG, PNG, or PDF (Max size: 2MB)</p>
    </div>

    <?php if($success): ?>
      <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php elseif($error): ?>
      <div class="alert danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="upload-form">
      <div class="form-group">
        <label for="valid_id" class="form-label">Select Valid ID</label>
        <input class="input-file" type="file" id="valid_id" name="valid_id" accept="image/*,application/pdf" required>
      </div>

      <div class="form-actions">
        <button class="btn primary" type="submit">Upload ID</button>
        <a class="btn secondary" href="/e-barangay/resident/dashboard.php">Back</a>
      </div>
    </form>
  </main>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
