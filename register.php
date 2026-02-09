<?php
session_start();
header('Content-Type: application/json');

// Подключение к базе данных
$host = 'localhost';
$dbname = 'orteka';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit;
}

// Получение данных из формы
$full_name = $_POST['full_name'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';

// Проверка наличия обязательных данных
if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все обязательные поля']);
    exit;
}

// Проверка уникальности имени пользователя и email
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
$existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_user) {
    echo json_encode(['success' => false, 'message' => 'Пользователь с таким именем или email уже существует']);
    exit;
}

// Хеширование пароля
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Вставка нового пользователя в базу данных
$stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, full_name, phone, role) VALUES (?, ?, ?, ?, ?, 'employee')");

try {
    $stmt->execute([$username, $email, $password_hash, $full_name, $phone]);
    echo json_encode(['success' => true, 'message' => 'Регистрация прошла успешно. Теперь вы можете войти в систему.']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка при регистрации: ' . $e->getMessage()]);
}
?>