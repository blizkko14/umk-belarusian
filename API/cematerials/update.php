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
if (empty($input['id']) || empty($input['title']) || empty($input['material_type'])) {
    sendResponse(false, 'Не все обязательные данные переданы (id, title, material_type)');
}

$id = intval($input['id']);
$title = trim($input['title']);
$material_type = $input['material_type'];
$content_url = isset($input['content_url']) ? trim($input['content_url']) : null;
$image_data = isset($input['image_data']) ? $input['image_data'] : null;
$test_id = isset($input['test_id']) ? intval($input['test_id']) : null;

// Праверка тыпу матэрыялу
if (!in_array($material_type, ['test', 'link', 'image'])) {
    sendResponse(false, 'Неверный тип материала. Допустимые значения: test, link, image');
}

try {
    $db = (new Database())->getConnection();
    
    $query = "UPDATE ce_materials SET 
              title = :title,
              material_type = :material_type,
              content_url = :content_url,
              image_data = :image_data,
              test_id = :test_id
              WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':material_type', $material_type);
    $stmt->bindParam(':content_url', $content_url);
    $stmt->bindParam(':image_data', $image_data);
    $stmt->bindParam(':test_id', $test_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        sendResponse(true, 'Материал ЦЭ успешно обновлен');
    } else {
        sendResponse(true, 'Материал ЦЭ не изменился или не найден');
    }
    
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка сервера: ' . $e->getMessage());
}
?>