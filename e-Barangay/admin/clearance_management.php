<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
require_once __DIR__ . '/../includes/db_connect.php';

$result = $conn->query("SELECT cr.*, u.fullname FROM clearance_requests cr JOIN users u ON cr.user_id=u.user_id ORDER BY cr.request_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Clearance Monitoring</title>
</head>
<body>
<h2>All Clearance Requests</h2>

<table border="1" cellpadding="6">
<tr>
    <th>ID</th>
    <th>Resident</th>
    <th>Purpose</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['request_id']; ?></td>
    <td><?= $row['fullname']; ?></td>
    <td><?= $row['purpose']; ?></td>
    <td><?= $row['status']; ?></td>
    <td><?= $row['requested_date']; ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
