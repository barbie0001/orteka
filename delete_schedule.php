<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$id = $_POST['id'] ?? 0;

if (empty($id)) {
    sendJsonResponse(false, 'Не указан ID графика');
    exit;
}

// Проверяем существование
if (!checkRecordExists('schedules', $id)) {
    sendJsonResponse(false, 'График не найден');
    exit;
}

// Удаляем
$stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ?");
$stmt->execute([$id]);

sendJsonResponse(true, 'График успешно удален');
?>