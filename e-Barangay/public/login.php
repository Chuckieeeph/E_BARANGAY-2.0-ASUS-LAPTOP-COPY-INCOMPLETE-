<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $err = "Email and password required.";
    } else {
        $stmt = $con->prepare("SELECT user_id, role, fullname, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $role, $fullname, $hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role;
                $_SESSION['fullname'] = $fullname;

                if ($role === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } elseif ($role === 'secretary') {
                    header("Location: ../secretary/dashboard.php");
                } else {
                    header("Location: ../resident/dashboard.php");
                }
                exit;
            } else {
                $err = "Incorrect email or password.";
            }
        } else {
            $err = "Incorrect email or password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login • e-BARANGAY</title>
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="container">
    <div class="branding-section">
        <img src="assets/images/logo.jpg" class="seal" alt="Logo">
        <h1 class="brgy-name">Brgy. Cantil</h1>
        <p class="location">Roxas, Oriental Mindoro</p>
    </div>

    <div class="login-section">
        <div class="login-box">
            <h2>Login</h2>

            <?php if ($err): ?>
                <p class="error"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn-login">Login</button>
                <a class="register-link" href="register.php">Create new account</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
