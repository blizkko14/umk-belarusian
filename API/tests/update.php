<?php
require_once __DIR__ . '/../config/database.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (empty($input['id']) || empty($input['title'])) {
    sendResponse(false, 'Не указан ID или название');
}

try {
    $db = (new Database())->getConnection();
    $time = isset($input['time_limit']) ? intval($input['time_limit']) : 20;
    $stmt = $db->prepare("UPDATE tests SET title = ?, time_limit = ? WHERE id = ?");
    $stmt->execute([$input['title'], $time, $input['id']]);
    sendResponse(true, 'Тест обновлен');
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>