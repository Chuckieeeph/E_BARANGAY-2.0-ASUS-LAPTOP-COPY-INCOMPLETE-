<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resident');
require_once __DIR__ . '/../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM clearance_requests WHERE user_id = $user_id ORDER BY request_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>My Clearance Requests</title>
</head>
<body>
<h2>My Clearance Requests</h2>

<table border="1" cellpadding="6">
<tr>
    <th>ID</th>
    <th>Purpose</th>
    <th>Status</th>
    <th>Date Requested</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo $row['request_id']; ?></td>
    <td><?php echo $row['purpose']; ?></td>
    <td><?php echo $row['status']; ?></td>
    <td><?php echo $row['requested_date']; ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
