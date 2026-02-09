<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

$id = $_POST['id'] ?? null;
$client_id = $_POST['client_id'];
$user_id = $_POST['user_id'];
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];
$appointment_type = $_POST['appointment_type'];

// Проверка данных
if (empty($client_id) || empty($user_id) || empty($appointment_date) || empty($appointment_time)) {
    sendJsonResponse(false, 'Заполните все обязательные поля');
    exit;
}

if ($id) {
    // Обновление существующей записи
    $stmt = $pdo->prepare("UPDATE appointments SET client_id = ?, user_id = ?, appointment_date = ?, appointment_time = ?, appointment_type = ? WHERE id = ?");
    $stmt->execute([$client_id, $user_id, $appointment_date, $appointment_time, $appointment_type, $id]);
    sendJsonResponse(true, 'Запись успешно обновлена');
} else {
    // Добавление новой записи
    $stmt = $pdo->prepare("INSERT INTO appointments (client_id, user_id, appointment_date, appointment_time, appointment_type, status) VALUES (?, ?, ?, ?, ?, 'запланирован')");
    $stmt->execute([$client_id, $user_id, $appointment_date, $appointment_time, $appointment_type]);
    sendJsonResponse(true, 'Запись успешно создана');
}
?>