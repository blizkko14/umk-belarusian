<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Метод не поддерживается. Используйте POST.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

if (empty($input['id']) || empty($input['title']) || empty($input['content'])) {
    sendResponse(false, 'Не все обязательные данные переданы');
}

$id = intval($input['id']);
$title = trim($input['title']);
$content = $input['content'];
$class_id = isset($input['class_id']) ? intval($input['class_id']) : null;
$attachments = isset($input['attachments']) ? $input['attachments'] : null;

try {
    $db = (new Database())->getConnection();
    
    if ($class_id) {
        $query = "UPDATE conspects SET 
                  title = :title, 
                  content = :content, 
                  attachments = :attachments,
                  class_id = :class_id
                  WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':class_id', $class_id);
    } else {
        $query = "UPDATE conspects SET 
                  title = :title, 
                  content = :content, 
                  attachments = :attachments
                  WHERE id = :id";
        $stmt = $db->prepare($query);
    }
    
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':attachments', $attachments);
    $stmt->execute();
    
    sendResponse(true, 'Конспект обновлен');
    
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка сервера: ' . $e->getMessage());
}
?>