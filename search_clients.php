<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

$name = $_GET['name'] ?? '';
$phone = $_GET['phone'] ?? '';
$email = $_GET['email'] ?? '';

$sql = "SELECT * FROM clients WHERE 1=1";
$params = [];

if (!empty($name)) {
    $sql .= " AND (full_name LIKE ?)";
    $params[] = "%$name%";
}

if (!empty($phone)) {
    $sql .= " AND phone LIKE ?";
    $params[] = "%$phone%";
}

if (!empty($email)) {
    $sql .= " AND email LIKE ?";
    $params[] = "%$email%";
}

$sql .= " ORDER BY full_name LIMIT 50";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clients = $stmt->fetchAll();

sendJsonResponse(true, '', ['clients' => $clients]);
?>