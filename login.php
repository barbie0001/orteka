<?php
session_start();
header('Content-Type: application/json');

// Подключение к базе данных
$host = 'localhost';
$dbname = 'orteka';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit;
}

// Получение данных из формы
$input_username = $_POST['username'] ?? '';
$input_password = $_POST['password'] ?? '';

// Проверка наличия данных
if (empty($input_username) || empty($input_password)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit;
}

// Поиск пользователя в базе данных
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
$stmt->execute([$input_username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ПРОСТАЯ ПРОВЕРКА ПАРОЛЯ (без хеширования)
if ($user && $user['password'] === $input_password) {
    // Успешная авторизация
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    
    // Обновление времени последнего входа
    $update_stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $update_stmt->execute([$user['id']]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Успешный вход в систему',
        'redirect' => 'dashboard.php'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Неверное имя пользователя или пароль']);
}
?>