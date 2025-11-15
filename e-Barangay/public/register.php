<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Registration | e-Barangay</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

<div class="container">

    <div class="auth-box">
        <img src="assets/images/logo.jpg" class="seal" alt="Roxas Seal">

        <h2>Resident Registration</h2>
        <p class="subtext">Barangay Cantil · Roxas, Oriental Mindoro</p>

        <form action="process_register.php" method="POST" enctype="multipart/form-data">

            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="fullname" placeholder="Juan Dela Cruz" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="example@gmail.com" required>
            </div>

            <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" required>
            </div>

            <div class="input-group">
                <label>Sex</label>
                <select name="gender" required>
                    <option value="" disabled selected>Select</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>

            <div class="input-group">
                <label>Complete Address</label>
                <input type="text" name="address"
                       placeholder="Block/Street, Barangay Cantil, Roxas Oriental Mindoro"
                       required>
            </div>

            <div class="input-group">
                <label>Contact Number</label>
                <input type="text" name="contact" placeholder="09XXXXXXXXX" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" minlength="8" required>
            </div>

            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="cpassword" minlength="8" required>
            </div>

            <div class="input-group">
                <label>Profile Photo (optional)</label>
                <input type="file" name="profile" accept="image/*">
            </div>

            <button type="submit" class="btn">Create Account</button>

            <p class="redirect">Already have an account?
                <a href="index.php">Login here</a>
            </p>
        </form>

    </div>
</div>

</body>
</html>
