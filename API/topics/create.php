<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Используйте POST.');
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (empty($input['name']) || empty($input['class_id']) || empty($input['created_by'])) {
    sendResponse(false, 'Не все данные переданы');
}

try {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("INSERT INTO topics (class_id, name, created_by) VALUES (?, ?, ?)");
    $stmt->execute([$input['class_id'], $input['name'], $input['created_by']]);
    sendResponse(true, 'Тема создана', ['id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>