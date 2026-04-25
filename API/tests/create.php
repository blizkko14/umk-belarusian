<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Метод не поддерживается. Используйте POST.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

if (empty($input['title']) || empty($input['created_by'])) {
    sendResponse(false, 'Не все обязательные данные переданы');
}

$title = trim($input['title']);
$time_limit = isset($input['time_limit']) ? intval($input['time_limit']) : 20;
$created_by = intval($input['created_by']);
$class_id = isset($input['class_id']) && $input['class_id'] !== '' ? intval($input['class_id']) : null;

try {
    $db = (new Database())->getConnection();
    
    $query = "INSERT INTO tests (class_id, title, time_limit, created_by) 
              VALUES (:class_id, :title, :time_limit, :created_by)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':class_id', $class_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':time_limit', $time_limit);
    $stmt->bindParam(':created_by', $created_by);
    $stmt->execute();
    
    sendResponse(true, 'Тест создан', ['id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>