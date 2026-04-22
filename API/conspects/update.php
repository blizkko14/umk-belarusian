<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Метод не поддерживается. Используйте POST.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

// Праверка абавязковых палёў
if (empty($input['id']) || empty($input['title']) || empty($input['content'])) {
    sendResponse(false, 'Не все обязательные данные переданы (id, title, content)');
}

$id = intval($input['id']);
$title = trim($input['title']);
$content = $input['content'];
$class_id = isset($input['class_id']) ? intval($input['class_id']) : null;

try {
    $db = (new Database())->getConnection();
    
    // Правяраем, ці існуе канспект
    $checkStmt = $db->prepare("SELECT id FROM conspects WHERE id = :id");
    $checkStmt->bindParam(':id', $id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        sendResponse(false, 'Конспект не найден');
    }
    
    // Будуем запыт у залежнасці ад таго, ці мяняецца class_id
    if ($class_id) {
        // Правяраем, ці існуе новая тэма
        $classCheck = $db->prepare("SELECT id FROM classs WHERE id = :class_id");
        $classCheck->bindParam(':class_id', $class_id);
        $classCheck->execute();
        
        if ($classCheck->rowCount() === 0) {
            sendResponse(false, 'Указанная тема не существует');
        }
        
        $query = "UPDATE conspects SET 
                  class_id = :class_id,
                  title = :title, 
                  content = :content 
                  WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':class_id', $class_id);
    } else {
        $query = "UPDATE conspects SET 
                  title = :title, 
                  content = :content 
                  WHERE id = :id";
        $stmt = $db->prepare($query);
    }
    
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->execute();
    
    sendResponse(true, 'Конспект успешно обновлен');
    
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка сервера: ' . $e->getMessage());
}
?>