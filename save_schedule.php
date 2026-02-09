<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

$id = $_POST['id'] ?? null;
$user_id = $_POST['user_id'] ?? '';
$work_date = $_POST['work_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$notes = $_POST['notes'] ?? '';

// Валидация
if (empty($user_id) || empty($work_date) || empty($start_time) || empty($end_time)) {
    sendJsonResponse(false, 'Заполните обязательные поля');
    exit;
}

// Проверяем корректность времени
if (strtotime($end_time) <= strtotime($start_time)) {
    sendJsonResponse(false, 'Время окончания должно быть позже времени начала');
    exit;
}

// Проверяем пересечение с другими записями того же сотрудника
if ($id) {
    $check_sql = "SELECT id FROM schedules WHERE user_id = ? AND work_date = ? AND id != ? AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?))";
    $check_params = [$user_id, $work_date, $id, $end_time, $start_time, $end_time, $start_time];
} else {
    $check_sql = "SELECT id FROM schedules WHERE user_id = ? AND work_date = ? AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?))";
    $check_params = [$user_id, $work_date, $end_time, $start_time, $end_time, $start_time];
}

$check_stmt = $pdo->prepare($check_sql);
$check_stmt->execute($check_params);

if ($check_stmt->fetch()) {
    sendJsonResponse(false, 'Это время пересекается с другим графиком сотрудника');
    exit;
}

if ($id) {
    // Обновление
    $stmt = $pdo->prepare("UPDATE schedules SET user_id = ?, work_date = ?, start_time = ?, end_time = ?, notes = ? WHERE id = ?");
    $stmt->execute([$user_id, $work_date, $start_time, $end_time, $notes, $id]);
    sendJsonResponse(true, 'График успешно обновлен');
} else {
    // Добавление
    $stmt = $pdo->prepare("INSERT INTO schedules (user_id, work_date, start_time, end_time, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $work_date, $start_time, $end_time, $notes]);
    $new_id = $pdo->lastInsertId();
    sendJsonResponse(true, 'График успешно добавлен', ['id' => $new_id]);
}
?>