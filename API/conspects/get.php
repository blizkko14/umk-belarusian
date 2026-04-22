<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(false, 'Метод не поддерживается. Используйте GET.');
}

$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    $db = (new Database())->getConnection();
    
    if ($id > 0) {
        $query = "SELECT c.*, u.name as teacher_name 
                  FROM conspects c
                  JOIN users u ON c.created_by = u.id
                  WHERE c.id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $conspect = $stmt->fetch();
        
        if ($conspect) {
            sendResponse(true, 'Конспект получен', ['conspect' => $conspect]);
        } else {
            sendResponse(false, 'Конспект не найден');
        }
    } else if ($class_id > 0) {
        $query = "SELECT c.id, c.title, c.created_at, u.name as teacher_name
                  FROM conspects c
                  JOIN users u ON c.created_by = u.id
                  WHERE c.class_id = :class_id
                  ORDER BY c.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':class_id', $class_id);
        $stmt->execute();
        sendResponse(true, 'Конспекты получены', ['conspects' => $stmt->fetchAll()]);
    } else {
        sendResponse(false, 'Укажите class_id или id');
    }
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка сервера: ' . $e->getMessage());
}
?>