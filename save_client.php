<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

$id = $_POST['id'] ?? null;
$full_name = $_POST['full_name'];
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$medical_history = $_POST['medical_history'] ?? '';

if (empty($full_name)) {
    sendJsonResponse(false, 'Укажите ФИО клиента');
    exit;
}

if ($id) {
    // Обновление
    $stmt = $pdo->prepare("UPDATE clients SET full_name = ?, phone = ?, email = ?, address = ?, medical_history = ? WHERE id = ?");
    $stmt->execute([$full_name, $phone, $email, $address, $medical_history, $id]);
    sendJsonResponse(true, 'Клиент успешно обновлен');
} else {
    // Добавление
    $stmt = $pdo->prepare("INSERT INTO clients (full_name, phone, email, address, medical_history) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $phone, $email, $address, $medical_history]);
    sendJsonResponse(true, 'Клиент успешно добавлен');
}
?>