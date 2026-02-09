<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

$id = $_POST['id'] ?? 0;
$status = $_POST['status'] ?? '';

if (empty($id) || empty($status)) {
    sendJsonResponse(false, 'Не указаны параметры');
    exit;
}

// Проверяем существование записи
if (!checkRecordExists('appointments', $id)) {
    sendJsonResponse(false, 'Запись не найдена');
    exit;
}

// Проверяем валидность статуса
$valid_statuses = ['запланирован', 'завершен', 'отменен', 'перенесен'];
if (!in_array($status, $valid_statuses)) {
    sendJsonResponse(false, 'Неверный статус');
    exit;
}

// Обновляем статус
$stmt = $pdo->prepare("UPDATE appointments SET status = ?, updated_at = NOW() WHERE id = ?");
$stmt->execute([$status, $id]);

// Получаем обновленные данные для ответа
$stmt = $pdo->prepare("
    SELECT a.*, c.full_name as client_name, u.full_name as employee_name 
    FROM appointments a 
    LEFT JOIN clients c ON a.client_id = c.id 
    LEFT JOIN users u ON a.user_id = u.id 
    WHERE a.id = ?
");
$stmt->execute([$id]);
$appointment = $stmt->fetch();

sendJsonResponse(true, 'Статус успешно обновлен', ['appointment' => $appointment]);
?>