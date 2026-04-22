<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(false, 'Метод не поддерживается. Используйте GET.');
}

try {
    $db = (new Database())->getConnection();
    
    $query = "SELECT m.id, m.title, m.material_type, m.content_url, m.image_data, m.test_id, m.created_at,
                     t.title as test_title
              FROM ce_materials m
              LEFT JOIN tests t ON m.test_id = t.id
              ORDER BY m.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    sendResponse(true, 'Материалы ЦЭ получены', ['materials' => $stmt->fetchAll()]);
    
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка сервера: ' . $e->getMessage());
}
?>