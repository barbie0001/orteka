<?php
session_start();
require_once 'config.php';
checkAuth();
checkAdmin();

$pdo = getDBConnection();
$id = $_POST['id'] ?? 0;

if (empty($id)) {
    sendJsonResponse(false, 'Не указан ID сотрудника');
    exit;
}

// Нельзя удалить себя
if ($id == $_SESSION['user_id']) {
    sendJsonResponse(false, 'Нельзя удалить свой собственный аккаунт');
    exit;
}

// Проверяем, есть ли у сотрудника связанные записи
$appointments_count = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE user_id = ?")->execute([$id])->fetchColumn();
$orders_count = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?")->execute([$id])->fetchColumn();

if ($appointments_count > 0 || $orders_count > 0) {
    // Вместо удаления деактивируем
    $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
    $stmt->execute([$id]);
    sendJsonResponse(true, 'Сотрудник деактивирован (есть связанные записи)');
} else {
    // Удаляем если нет связанных записей
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        sendJsonResponse(true, 'Сотрудник успешно удален');
    } else {
        sendJsonResponse(false, 'Сотрудник не найден');
    }
}
?>