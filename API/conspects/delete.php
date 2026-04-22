<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Метод не поддерживается. Используйте POST.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

if (empty($input['id'])) {
    sendResponse(false, 'Не указан ID конспекта');
}

$id = intval($input['id']);

try {
    $db = (new Database())->getConnection();
    
    // Спачатку правяраем, ці існуе такі канспект
    $checkStmt = $db->prepare("SELECT id, title FROM conspects WHERE id = :id");
    $checkStmt->bindParam(':id', $id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        sendResponse(false, 'Конспект с указанным ID не найден');
    }
    
    $conspect = $checkStmt->fetch();
    
    // Выдаляем канспект
    $query = "DELETE FROM conspects WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    sendResponse(true, 'Конспект "' . $conspect['title'] . '" успешно удален');
    
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка сервера: ' . $e->getMessage());
}
?>