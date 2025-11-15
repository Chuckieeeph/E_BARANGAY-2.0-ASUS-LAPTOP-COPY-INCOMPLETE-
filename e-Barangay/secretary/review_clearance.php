<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_role('secretary');
require_once __DIR__ . '/../includes/db_connect.php';

if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE clearance_requests SET status='approved', processed_date=NOW() WHERE request_id=$id");
}

if (isset($_GET['decline'])) {
    $id = intval($_GET['decline']);
    $conn->query("UPDATE clearance_requests SET status='declined', processed_date=NOW() WHERE request_id=$id");
}

$result = $conn->query("SELECT cr.*, u.fullname FROM clearance_requests cr JOIN users u ON cr.user_id=u.user_id ORDER BY cr.request_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Approve Clearance Requests</title>
</head>
<body>
<h2>Clearance Requests Review</h2>

<table border="1" cellpadding="6">
<tr>
    <th>ID</th>
    <th>Resident</th>
    <th>Purpose</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['request_id']; ?></td>
    <td><?= $row['fullname']; ?></td>
    <td><?= $row['purpose']; ?></td>
    <td><?= $row['status']; ?></td>
    <td>
        <?php if($row['status'] == 'pending'): ?>
            <a href="?approve=<?= $row['request_id']; ?>">Approve</a> |
            <a href="?decline=<?= $row['request_id']; ?>">Decline</a>
        <?php else: ?>
            No Action
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
