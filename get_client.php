<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if ($client) {
    sendJsonResponse(true, '', ['client' => $client]);
} else {
    sendJsonResponse(false, 'Клиент не найден');
}
?>