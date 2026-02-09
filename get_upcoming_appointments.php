<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];

// Получаем ближайшие записи на прием
$stmt = $pdo->prepare("
    SELECT a.*, c.full_name as client_name 
    FROM appointments a 
    LEFT JOIN clients c ON a.client_id = c.id 
    WHERE a.user_id = ? 
    AND a.appointment_date >= CURDATE() 
    AND a.status = 'запланирован'
    ORDER BY a.appointment_date, a.appointment_time 
    LIMIT 5
");
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();

sendJsonResponse(true, '', ['appointments' => $appointments]);
?>