<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Включаем отладку
error_reporting(E_ALL);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Метод не поддерживается'], JSON_UNESCAPED_UNICODE);
    exit();
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Файл не выбран или ошибка загрузки'], JSON_UNESCAPED_UNICODE);
    exit();
}

$file = $_FILES['file'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Проверка по расширению (более надёжно, чем MIME)
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'];

if (!in_array($ext, $allowedExtensions)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Недопустимый тип файла. Разрешены: ' . implode(', ', $allowedExtensions)
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$newName = uniqid() . '_' . time() . '.' . $ext;
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/UMK/uploads/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$uploadPath = $uploadDir . $newName;

if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    $fileUrl = 'http://localhost/UMK/uploads/' . $newName;
    echo json_encode([
        'success' => true,
        'message' => 'Файл успешно загружен',
        'data' => [
            'url' => $fileUrl,
            'name' => $file['name'],
            'type' => $file['type'],
            'size' => $file['size']
        ]
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Ошибка сохранения файла на сервер'
    ], JSON_UNESCAPED_UNICODE);
}
?>