<?php
require_once __DIR__ . '/../config/database.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (empty($input['title']) || empty($input['class_id']) || empty($input['created_by'])) {
    sendResponse(false, 'Не все данные');
}

try {
    $db = (new Database())->getConnection();
    $time = isset($input['time_limit']) ? intval($input['time_limit']) : 20;
    $stmt = $db->prepare("INSERT INTO tests (class_id, title, time_limit, created_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$input['class_id'], $input['title'], $time, $input['created_by']]);
    sendResponse(true, 'Тест создан', ['id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>