<?php
// admin/create_secretary.php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
require_once __DIR__ . '/../config/database.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (!$fullname || !$email || !$password) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $con->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "This email is already registered.";
        }
        $stmt->close();
    }

    // Insert new secretary
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("INSERT INTO users (role, fullname, email, password, validation_status) VALUES ('secretary', ?, ?, ?, 'validated')");
        $stmt->bind_param("sss", $fullname, $email, $hash);

        if ($stmt->execute()) {
            $success = "Secretary account created successfully.";
        } else {
            $errors[] = "Database error: " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Create Secretary Account | e-Barangay</title>
<link rel="stylesheet" href="../assets/css/admin_dashboard.css">
<style>
body {
  background: #f4f6f9;
  font-family: "Segoe UI", sans-serif;
  margin: 0;
  padding: 40px;
  color: #333;
}
.container {
  max-width: 450px;
  margin: 0 auto;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  padding: 25px 30px;
}
h2 {
  margin-bottom: 20px;
  text-align: center;
  color: #007bff;
}
label {
  display: block;
  margin-top: 10px;
  font-weight: 500;
}
input[type="text"],
input[type="email"],
input[type="password"] {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 6px;
}
.btn {
  display: inline-block;
  padding: 10px 14px;
  margin-top: 15px;
  border: none;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  cursor: pointer;
}
.btn-primary {
  background-color: #007bff;
  color: #fff;
}
.btn-primary:hover {
  background-color: #0056b3;
}
.btn-secondary {
  background-color: #6c757d;
  color: #fff;
  margin-left: 8px;
}
.alert {
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 10px;
  font-size: 0.95em;
}
.alert-error {
  background: #f8d7da;
  color: #842029;
  border: 1px solid #f5c2c7;
}
.alert-success {
  background: #d1e7dd;
  color: #0f5132;
  border: 1px solid #badbcc;
}
</style>
</head>

<body>
<div class="container">
  <h2>Create Secretary Account</h2>

  <?php if ($errors): ?>
    <div class="alert alert-error"><?= implode("<br>", $errors); ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success; ?></div>
  <?php endif; ?>

  <form method="post" novalidate>
    <label>Full Name
      <input type="text" name="fullname" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required>
    </label>

    <label>Email
      <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </label>

    <label>Password
      <input type="password" name="password" required>
    </label>

    <div>
      <input type="submit" class="btn btn-primary" value="Create Secretary">
      <a class="btn btn-secondary" href="dashboard.php">Back</a>
    </div>
  </form>
</div>
</body>
</html>
