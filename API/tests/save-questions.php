<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Метод не поддерживается. Используйте POST.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

if (empty($input['test_id']) || empty($input['questions'])) {
    sendResponse(false, 'Не все данные переданы');
}

$test_id = intval($input['test_id']);
$questions = $input['questions'];

try {
    $db = (new Database())->getConnection();
    
    // Удаляем старые вопросы этого теста
    $deleteStmt = $db->prepare("DELETE FROM questions WHERE test_id = :test_id");
    $deleteStmt->bindParam(':test_id', $test_id);
    $deleteStmt->execute();
    
    // Добавляем новые вопросы
    foreach ($questions as $index => $q) {
        $query = "INSERT INTO questions (test_id, question_text, question_type, correct_text_answer, order_num) 
                  VALUES (:test_id, :text, :type, :correct_answer, :order)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':test_id', $test_id);
        $stmt->bindParam(':text', $q['text']);
        $stmt->bindParam(':type', $q['type']);
        $stmt->bindParam(':correct_answer', $q['correctAnswer']);
        $stmt->bindParam(':order', $index);
        $stmt->execute();
        
        $questionId = $db->lastInsertId();
        
        // Сохраняем ответы для вопросов с выбором
        if ($q['type'] !== 'text' && isset($q['answers'])) {
            foreach ($q['answers'] as $aIndex => $answer) {
                $answerQuery = "INSERT INTO answers (question_id, answer_text, is_correct, order_num) 
                                VALUES (:qid, :text, :correct, :order)";
                $answerStmt = $db->prepare($answerQuery);
                $answerStmt->bindParam(':qid', $questionId);
                $answerStmt->bindParam(':text', $answer['text']);
                $answerStmt->bindParam(':correct', $answer['correct'], PDO::PARAM_BOOL);
                $answerStmt->bindParam(':order', $aIndex);
                $answerStmt->execute();
            }
        }
    }
    
    sendResponse(true, 'Вопросы сохранены');
    
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>