<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// Prepare response array
$response = [
    'pending_clearances'      => 0,
    'pending_verifications'   => 0,
    'total_residents'         => 0
];

try {
    // 1. Pending clearance requests
    $q = $con->query("SELECT COUNT(*) AS total FROM clearance_requests WHERE status='pending'");
    if ($q) {
        $response['pending_clearances'] = (int)($q->fetch_assoc()['total'] ?? 0);
    }

    // 2. Residents with pending validation
    $q = $con->query("SELECT COUNT(*) AS total FROM users WHERE role='resident' AND validation_status='pending'");
    if ($q) {
        $response['pending_verifications'] = (int)($q->fetch_assoc()['total'] ?? 0);
    }

    // 3. Total registered residents
    $q = $con->query("SELECT COUNT(*) AS total FROM users WHERE role='resident'");
    if ($q) {
        $response['total_residents'] = (int)($q->fetch_assoc()['total'] ?? 0);
    }

} catch (Exception $e) {
    error_log("API error: " . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
?>
