<?php
session_start();
require_once 'config.php';
checkAuth();
checkAdmin();

$pdo = getDBConnection();

$id = $_POST['id'] ?? null;
$full_name = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$role = $_POST['role'] ?? 'employee';
$password = $_POST['password'] ?? '';
$is_active = isset($_POST['is_active']) ? 1 : 0;

// Валидация
if (empty($full_name) || empty($username) || empty($email)) {
    sendJsonResponse(false, 'Заполните обязательные поля');
    exit;
}

if (!validateEmail($email)) {
    sendJsonResponse(false, 'Некорректный email');
    exit;
}

if ($phone && !validatePhone($phone)) {
    sendJsonResponse(false, 'Некорректный номер телефона');
    exit;
}

// Проверка уникальности username и email
$sql = "SELECT id FROM users WHERE (username = ? OR email = ?)";
$params = [$username, $email];

if ($id) {
    $sql .= " AND id != ?";
    $params[] = $id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

if ($stmt->fetch()) {
    sendJsonResponse(false, 'Пользователь с таким логином или email уже существует');
    exit;
}

if ($id) {
    // Обновление существующего сотрудника
    if (!empty($password)) {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone = ?, role = ?, is_active = ?, password = ? WHERE id = ?");
        $stmt->execute([$full_name, $username, $email, $phone, $role, $is_active, $password, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone = ?, role = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$full_name, $username, $email, $phone, $role, $is_active, $id]);
    }
    sendJsonResponse(true, 'Сотрудник успешно обновлен');
} else {
    // Добавление нового сотрудника
    if (empty($password)) {
        sendJsonResponse(false, 'Для нового сотрудника необходимо указать пароль');
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, phone, role, password, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $username, $email, $phone, $role, $password, $is_active]);
    
    $new_id = $pdo->lastInsertId();
    sendJsonResponse(true, 'Сотрудник успешно добавлен', ['id' => $new_id]);
}
?>