<?php
session_start();
require_once 'config.php';
checkAuth();
checkAdmin();

$pdo = getDBConnection();
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if ($employee) {
    // Не возвращаем пароль
    unset($employee['password']);
    sendJsonResponse(true, '', ['employee' => $employee]);
} else {
    sendJsonResponse(false, 'Сотрудник не найден');
}
?>