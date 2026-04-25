<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Метод не поддерживается. Используйте POST.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

if (empty($input['username']) || empty($input['password'])) {
    sendResponse(false, 'Увядзіце лагін і пароль');
}

try {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT id, username, name, role FROM users WHERE username = ? AND password = SHA2(?, 256)");
    $stmt->execute([$input['username'], $input['password']]);
    
    if ($stmt->rowCount() > 0) {
        sendResponse(true, 'Уваход выкананы', ['user' => $stmt->fetch()]);
    } else {
        sendResponse(false, 'Няправільны лагін або пароль');
    }
} catch (PDOException $e) {
    sendResponse(false, 'Памылка сервера: ' . $e->getMessage());
}
?>