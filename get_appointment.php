<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

// Получаем параметры фильтрации
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$status = $_GET['status'] ?? '';
$employee_id = $_GET['employee_id'] ?? '';
$client_id = $_GET['client_id'] ?? '';

// Строим запрос с фильтрами
$sql = "
    SELECT a.*, c.full_name as client_name, u.full_name as employee_name 
    FROM appointments a 
    LEFT JOIN clients c ON a.client_id = c.id 
    LEFT JOIN users u ON a.user_id = u.id 
    WHERE 1=1
";

$params = [];

if (!empty($date_from)) {
    $sql .= " AND a.appointment_date >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $sql .= " AND a.appointment_date <= ?";
    $params[] = $date_to;
}

if (!empty($status)) {
    $sql .= " AND a.status = ?";
    $params[] = $status;
}

if (!empty($employee_id)) {
    $sql .= " AND a.user_id = ?";
    $params[] = $employee_id;
}

if (!empty($client_id)) {
    $sql .= " AND a.client_id = ?";
    $params[] = $client_id;
}

$sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

sendJsonResponse(true, '', ['appointments' => $appointments]);
?>