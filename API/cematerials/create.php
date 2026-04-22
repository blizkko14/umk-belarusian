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
if (empty($input['title']) || empty($input['material_type']) || empty($input['created_by'])) {
    sendResponse(false, 'Не все обязательные данные переданы (title, material_type, created_by)');
}

$title = trim($input['title']);
$material_type = $input['material_type'];
$created_by = intval($input['created_by']);
$content_url = isset($input['content_url']) ? trim($input['content_url']) : null;
$image_data = isset($input['image_data']) ? $input['image_data'] : null;
$test_id = isset($input['test_id']) ? intval($input['test_id']) : null;

// Праверка тыпу матэрыялу
if (!in_array($material_type, ['test', 'link', 'image'])) {
    sendResponse(false, 'Неверный тип материала. Допустимые значения: test, link, image');
}

// Дадатковая праверка для спасылкі
if ($material_type === 'link' && empty($content_url)) {
    sendResponse(false, 'Для типа "link" необходимо указать content_url');
}

// Дадатковая праверка для выявы
if ($material_type === 'image' && empty($image_data)) {
    sendResponse(false, 'Для типа "image" необходимо передать image_data');
}

// Дадатковая праверка для тэста
if ($material_type === 'test' && empty($test_id)) {
    sendResponse(false, 'Для типа "test" необходимо указать test_id');
}

try {
    $db = (new Database())->getConnection();
    
    $query = "INSERT INTO ce_materials (title, material_type, content_url, image_data, test_id, created_by) 
              VALUES (:title, :material_type, :content_url, :image_data, :test_id, :created_by)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':material_type', $material_type);
    $stmt->bindParam(':content_url', $content_url);
    $stmt->bindParam(':image_data', $image_data);
    $stmt->bindParam(':test_id', $test_id);
    $stmt->bindParam(':created_by', $created_by);
    $stmt->execute();
    
    $newId = $db->lastInsertId();
    
    sendResponse(true, 'Материал ЦЭ успешно добавлен', ['id' => $newId]);
    
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка сервера: ' . $e->getMessage());
}
?>