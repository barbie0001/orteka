<?php
session_start();
require_once 'config.php';
checkAuth();
checkAdmin();

$pdo = getDBConnection();

$id = $_POST['id'] ?? 0;
$status = $_POST['status'] ?? 0;

if (empty($id)) {
    sendJsonResponse(false, 'Не указан ID сотрудника');
    exit;
}

// Нельзя деактивировать себя
if ($id == $_SESSION['user_id'] && $status == 0) {
    sendJsonResponse(false, 'Нельзя деактивировать свой собственный аккаунт');
    exit;
}

// Обновляем статус
$stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
$stmt->execute([$status, $id]);

if ($stmt->rowCount() > 0) {
    $action = $status == 1 ? 'активирован' : 'деактивирован';
    sendJsonResponse(true, "Сотрудник успешно $action");
} else {
    sendJsonResponse(false, 'Сотрудник не найден');
}
?>