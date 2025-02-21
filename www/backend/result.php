<?php
require 'db_conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['questionNumber']) || !isset($data['timeRemaining'])) {
    echo json_encode(['error' => 'Invalid input.']);
    exit();
}

$email = $data['email'];
$questionNumber = $data['questionNumber'];
$timeRemaining = $data['timeRemaining'];

try {
    // Insert or update result
    $query = "
        INSERT INTO results (email, question_number, time_remaining, updated_at)
        VALUES (:email, :question_number, :time_remaining, NOW())
        ON DUPLICATE KEY UPDATE
        question_number = VALUES(question_number),
        time_remaining = VALUES(time_remaining),
        updated_at = NOW()
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':email' => $email,
        ':question_number' => $questionNumber,
        ':time_remaining' => $timeRemaining
    ]);

    echo json_encode(['message' => 'Result saved successfully.']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}
?>
