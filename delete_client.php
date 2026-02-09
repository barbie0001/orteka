<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$id = $_POST['id'] ?? 0;

if (empty($id)) {
    sendJsonResponse(false, 'Не указан ID клиента');
    exit;
}

// Проверяем, есть ли у клиента связанные записи
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE client_id = ?");
$stmt->execute([$id]);
$appointments_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE client_id = ?");
$stmt->execute([$id]);
$orders_count = $stmt->fetchColumn();

if ($appointments_count > 0 || $orders_count > 0) {
    sendJsonResponse(false, 'Нельзя удалить клиента, у которого есть записи на прием или заказы');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    sendJsonResponse(true, 'Клиент успешно удален');
} else {
    sendJsonResponse(false, 'Клиент не найден');
}
?>