<?php
require_once __DIR__ . '/../config/database.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (empty($input['id'])) sendResponse(false, 'Не указан ID');

try {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("DELETE FROM topics WHERE id = ?");
    $stmt->execute([$input['id']]);
    sendResponse(true, 'Тема удалена');
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>