<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];

// Статистика для текущего пользователя
$stats = [];

// Общее количество записей на сегодня
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE() AND status = 'запланирован'");
$stats['today_appointments'] = $stmt->fetchColumn();

// Активные заказы
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE status IN ('новый', 'в работе')");
$stats['active_orders'] = $stmt->fetchColumn();

// Новые клиенты за последние 30 дней
$stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
$stats['new_clients'] = $stmt->fetchColumn();

// Завершенные заказы за сегодня
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE status = 'выдан' AND DATE(updated_at) = CURDATE()");
$stats['completed_orders_today'] = $stmt->fetchColumn();

// Ближайшие записи для текущего пользователя
$stmt = $pdo->prepare("
    SELECT a.*, c.full_name as client_name 
    FROM appointments a 
    LEFT JOIN clients c ON a.client_id = c.id 
    WHERE a.user_id = ? AND a.appointment_date >= CURDATE() AND a.status = 'запланирован'
    ORDER BY a.appointment_date, a.appointment_time 
    LIMIT 5
");
$stmt->execute([$user_id]);
$stats['upcoming_appointments'] = $stmt->fetchAll();

sendJsonResponse(true, '', ['stats' => $stats]);
?>