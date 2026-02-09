<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT o.*, c.full_name as client_name, u.full_name as employee_name 
    FROM orders o 
    LEFT JOIN clients c ON o.client_id = c.id 
    LEFT JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order) {
    sendJsonResponse(true, '', ['order' => $order]);
} else {
    sendJsonResponse(false, 'Заказ не найден');
}
?>