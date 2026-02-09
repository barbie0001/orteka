<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$id = $_POST['id'] ?? 0;

if (empty($id)) {
    sendJsonResponse(false, 'Не указан ID заказа');
    exit;
}

// Проверяем существование
if (!checkRecordExists('orders', $id)) {
    sendJsonResponse(false, 'Заказ не найден');
    exit;
}

// Удаляем
$stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
$stmt->execute([$id]);

sendJsonResponse(true, 'Заказ успешно удален');
?>