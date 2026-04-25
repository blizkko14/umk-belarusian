<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Метод не поддерживается. Используйте POST.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

if (empty($input['id']) || empty($input['title']) || empty($input['content'])) {
    sendResponse(false, 'Не все обязательные данные переданы');
}

$id = intval($input['id']);
$title = trim($input['title']);
$content = $input['content'];
$class_id = isset($input['class_id']) ? intval($input['class_id']) : null;
$attachments = isset($input['attachments']) ? $input['attachments'] : null;
$linked_test_id = isset($input['linked_test_id']) && $input['linked_test_id'] ? intval($input['linked_test_id']) : null;

try {
    $db = (new Database())->getConnection();
    
    $query = "UPDATE conspects SET 
              title = :title, 
              content = :content, 
              attachments = :attachments,
              linked_test_id = :linked_test_id";

    if ($class_id) {
        $query .= ", class_id = :class_id";
    }
    $query .= " WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':attachments', $attachments);
    $stmt->bindParam(':linked_test_id', $linked_test_id);
    
    if ($class_id) {
        $stmt->bindParam(':class_id', $class_id);
    }
    
    $stmt->execute();
    
    sendResponse(true, 'Конспект обновлен');
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>