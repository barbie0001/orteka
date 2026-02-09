<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

$date_from = $_GET['date_from'] ?? date('Y-m-d');
$date_to = $_GET['date_to'] ?? date('Y-m-d', strtotime('+7 days'));
$employee_id = $_GET['employee_id'] ?? '';

$sql = "
    SELECT s.*, u.full_name as employee_name 
    FROM schedules s 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE s.work_date BETWEEN ? AND ?
";

$params = [$date_from, $date_to];

if (!empty($employee_id)) {
    $sql .= " AND s.user_id = ?";
    $params[] = $employee_id;
}

$sql .= " ORDER BY s.work_date, s.start_time";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$schedules = $stmt->fetchAll();

sendJsonResponse(true, '', ['schedules' => $schedules]);
?>