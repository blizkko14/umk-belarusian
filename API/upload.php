<?php
require_once __DIR__ . '/config/database.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
    exit();
}

// Праверка наяўнасці файла
if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'Файл не загружаны']);
    exit();
}

$file = $_FILES['file'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$maxSize = 10 * 1024 * 1024; // 10 MB

// Праверка тыпу файла
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Недапушчальны тып файла']);
    exit();
}

// Праверка памеру
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'Файл занадта вялікі (макс. 10 MB)']);
    exit();
}

// Ствараем унікальнае імя файла
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newName = uniqid() . '_' . time() . '.' . $ext;
$uploadDir = __DIR__ . '/../uploads/';

// Ствараем папку, калі яе няма
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$uploadPath = $uploadDir . $newName;

// Перамяшчаем файл
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    $fileUrl = 'http://localhost/УМК/api/uploads/' . $newName;
    echo json_encode([
        'success' => true,
        'message' => 'Файл паспяхова загружаны',
        'data' => [
            'url' => $fileUrl,
            'name' => $file['name'],
            'type' => $file['type'],
            'size' => $file['size']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Памылка захавання файла']);
}
?>