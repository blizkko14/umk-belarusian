<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(false, 'Метод не поддерживается. Используйте GET.');
}

$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : null;
if (!$class_id) sendResponse(false, 'Не указан class_id');

try {
    $db = (new Database())->getConnection();
    
    $query = "SELECT t.id, t.title, t.time_limit, t.created_at, u.name as teacher_name
              FROM tests t
              JOIN users u ON t.created_by = u.id
              WHERE t.class_id = :class_id
              ORDER BY t.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':class_id', $class_id);
    $stmt->execute();
    
    sendResponse(true, 'Тесты получены', ['tests' => $stmt->fetchAll()]);
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>