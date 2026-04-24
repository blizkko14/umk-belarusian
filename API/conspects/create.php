<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Метод не поддерживается. Используйте POST.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

if (empty($input['title']) || empty($input['content']) || empty($input['class_id']) || empty($input['created_by'])) {
    sendResponse(false, 'Не все обязательные данные переданы');
}

$title = trim($input['title']);
$content = $input['content'];
$class_id = intval($input['class_id']);
$created_by = intval($input['created_by']);
$attachments = isset($input['attachments']) ? $input['attachments'] : null;

try {
    $db = (new Database())->getConnection();
    
    // Правяраем, ці існуе клас (табліца classes)
    $checkStmt = $db->prepare("SELECT id FROM classes WHERE id = :class_id");
    $checkStmt->bindParam(':class_id', $class_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        sendResponse(false, 'Указанный класс не существует');
    }
    
    $query = "INSERT INTO conspects (class_id, title, content, attachments, created_by) 
              VALUES (:class_id, :title, :content, :attachments, :created_by)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':class_id', $class_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':attachments', $attachments);
    $stmt->bindParam(':created_by', $created_by);
    $stmt->execute();
    
    $newId = $db->lastInsertId();
    
    sendResponse(true, 'Конспект успешно создан', ['id' => $newId]);
    
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка сервера: ' . $e->getMessage());
}
?>