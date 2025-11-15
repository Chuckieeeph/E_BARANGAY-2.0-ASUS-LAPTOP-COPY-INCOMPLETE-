<?php
require_once __DIR__ . '/../config/database.php';
session_start();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $password = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';
    $role = 'resident'; // default role for registration
    $profilePhoto = null;

    // ✅ 1. Basic validation
    if (!$fullname || !$email || !$dob || !$gender || !$address || !$contact || !$password || !$cpassword) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    } elseif ($password !== $cpassword) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    // ✅ 2. Check if email exists
    if (empty($errors)) {
        $stmt = $con->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already registered.";
        }
        $stmt->close();
    }

    // ✅ 3. Handle profile photo upload (optional)
    if (empty($errors) && isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/profile_photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid('profile_', true) . '.' . $ext;
        $targetPath = $uploadDir . $newFilename;

        if (move_uploaded_file($_FILES['profile']['tmp_name'], $targetPath)) {
            $profilePhoto = 'uploads/profile_photos/' . $newFilename;
        } else {
            $errors[] = "Failed to upload profile photo.";
        }
    }

    // ✅ 4. Insert into database
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("
            INSERT INTO users (fullname, email, dob, gender, address, contact, password, role, profile_photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssssss", $fullname, $email, $dob, $gender, $address, $contact, $hashedPassword, $role, $profilePhoto);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Account created successfully! You can now log in.";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registration Result</title>
<link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
<div class="container">
  <div class="auth-box">
    <h2>Registration Status</h2>

    <?php if ($errors): ?>
      <div class="alert alert-error">
        <?= implode("<br>", $errors); ?>
      </div>
      <a href="register.php" class="btn">Go Back</a>
    <?php else: ?>
      <div class="alert alert-success">
        Account created successfully!
      </div>
      <a href="index.php" class="btn">Proceed to Login</a>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
