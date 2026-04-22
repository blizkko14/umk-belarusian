<?php
require_once __DIR__ . '/../config/database.php';

$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
if (!$class_id) sendResponse(false, 'Не указан class_id');

try {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT * FROM tests WHERE class_id = ? ORDER BY created_at DESC");
    $stmt->execute([$class_id]);
    sendResponse(true, 'Тесты получены', ['tests' => $stmt->fetchAll()]);
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>