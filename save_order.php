<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

$id = $_POST['id'] ?? null;
$client_id = $_POST['client_id'] ?? '';
$user_id = $_POST['user_id'] ?? '';
$order_type = $_POST['order_type'] ?? '';
$description = $_POST['description'] ?? '';
$status = $_POST['status'] ?? 'новый';
$total_amount = $_POST['total_amount'] ?? 0;
$paid_amount = $_POST['paid_amount'] ?? 0;
$completion_date = $_POST['completion_date'] ?? null;

// Валидация
if (empty($client_id) || empty($user_id) || empty($order_type)) {
    sendJsonResponse(false, 'Заполните обязательные поля');
    exit;
}

// Преобразуем суммы
$total_amount = floatval(str_replace(',', '.', $total_amount));
$paid_amount = floatval(str_replace(',', '.', $paid_amount));

if ($paid_amount > $total_amount) {
    sendJsonResponse(false, 'Оплаченная сумма не может превышать общую сумму');
    exit;
}

if ($id) {
    // Обновление
    $stmt = $pdo->prepare("
        UPDATE orders SET 
        client_id = ?, user_id = ?, order_type = ?, description = ?, 
        status = ?, total_amount = ?, paid_amount = ?, completion_date = ? 
        WHERE id = ?
    ");
    $stmt->execute([$client_id, $user_id, $order_type, $description, $status, $total_amount, $paid_amount, $completion_date, $id]);
    sendJsonResponse(true, 'Заказ успешно обновлен');
} else {
    // Добавление
    $stmt = $pdo->prepare("
        INSERT INTO orders (client_id, user_id, order_type, description, status, total_amount, paid_amount, completion_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$client_id, $user_id, $order_type, $description, $status, $total_amount, $paid_amount, $completion_date]);
    $new_id = $pdo->lastInsertId();
    sendJsonResponse(true, 'Заказ успешно создан', ['id' => $new_id]);
}
?>