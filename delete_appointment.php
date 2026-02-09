<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$id = $_POST['id'] ?? 0;

if (empty($id)) {
    sendJsonResponse(false, 'Не указан ID записи');
    exit;
}

// Проверяем существование записи
if (!checkRecordExists('appointments', $id)) {
    sendJsonResponse(false, 'Запись не найдена');
    exit;
}

// Удаляем запись
$stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    sendJsonResponse(true, 'Запись успешно удалена');
} else {
    sendJsonResponse(false, 'Ошибка при удалении записи');
}
?>