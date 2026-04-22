<?php
require_once __DIR__ . '/../config/database.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (empty($input['id']) || empty($input['name'])) {
    sendResponse(false, 'Не указан ID или название');
}

try {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("UPDATE topics SET name = ? WHERE id = ?");
    $stmt->execute([$input['name'], $input['id']]);
    sendResponse(true, 'Тема обновлена');
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>