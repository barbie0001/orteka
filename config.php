<?php
// Конфигурация базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'orteka');
define('DB_USER', 'root');
define('DB_PASS', '');

// Функция для подключения к базе данных
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        return null;
    }
}

// Функция для отправки JSON ответа
function sendJsonResponse($success, $message, $additionalData = []) {
    $response = array_merge(['success' => $success, 'message' => $message], $additionalData);
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Проверка авторизации
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.html');
        exit;
    }
}

// Проверка прав администратора
function checkAdmin() {
    if ($_SESSION['role'] !== 'admin') {
        header('Location: dashboard.php');
        exit;
    }
}

// Функция для валидации email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Функция для валидации телефона
function validatePhone($phone) {
    return preg_match('/^\+?[0-9\s\-\(\)]+$/', $phone);
}

// Функция для получения русской даты
function formatDate($date) {
    $months = [
        '01' => 'января', '02' => 'февраля', '03' => 'марта', '04' => 'апреля',
        '05' => 'мая', '06' => 'июня', '07' => 'июля', '08' => 'августа',
        '09' => 'сентября', '10' => 'октября', '11' => 'ноября', '12' => 'декабря'
    ];
    
    $date_parts = explode('-', $date);
    if (count($date_parts) === 3) {
        return (int)$date_parts[2] . ' ' . $months[$date_parts[1]] . ' ' . $date_parts[0];
    }
    return $date;
}

// Функция для проверки существования записи
function checkRecordExists($table, $id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() > 0;
}
?>