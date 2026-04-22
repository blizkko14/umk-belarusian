<?php
require_once __DIR__ . '/../config/database.php';

$test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;
if (!$test_id) sendResponse(false, 'Не указан test_id');

try {
    $db = (new Database())->getConnection();
    
    // Тест
    $stmt = $db->prepare("SELECT title, time_limit FROM tests WHERE id = ?");
    $stmt->execute([$test_id]);
    $test = $stmt->fetch();
    
    // Вопросы
    $stmt = $db->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY order_num");
    $stmt->execute([$test_id]);
    $questions = $stmt->fetchAll();
    
    // Ответы к каждому вопросу
    foreach ($questions as &$q) {
        $stmt = $db->prepare("SELECT id, answer_text, is_correct FROM answers WHERE question_id = ?");
        $stmt->execute([$q['id']]);
        $q['answers'] = $stmt->fetchAll();
    }
    
    sendResponse(true, 'Вопросы получены', ['test' => $test, 'questions' => $questions]);
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>