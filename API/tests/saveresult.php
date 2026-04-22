<?php
require_once __DIR__ . '/../config/database.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (empty($input['test_id']) || empty($input['student_name']) || empty($input['class'])) {
    sendResponse(false, 'Не все данные');
}

$score = intval($input['score'] ?? 0);
$max = intval($input['max_score'] ?? 1);
$percent = $max > 0 ? round(($score / $max) * 100, 2) : 0;

try {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("INSERT INTO test_results (test_id, student_name, class, score, max_score, percentage) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$input['test_id'], $input['student_name'], $input['class'], $score, $max, $percent]);
    sendResponse(true, 'Результат сохранен', ['id' => $db->lastInsertId(), 'percentage' => $percent]);
} catch (PDOException $e) {
    sendResponse(false, 'Ошибка: ' . $e->getMessage());
}
?>