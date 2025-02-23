<?php
require 'db_conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['score'])) {
    echo json_encode(['error' => 'Invalid input.']);
    exit();
}

$email = $data['email'];
$score = (int)$data['score'];

try {

    $query = "
    INSERT INTO result (email, score, updated_at)
    VALUES (?, ?, NOW())
    ON DUPLICATE KEY UPDATE
    score = VALUES(score), updated_at = NOW()
    ";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Preparation failed: " . $conn->error);
    }

    $stmt->bind_param("si", $email, $score);

    if (!$stmt->execute()) {
        throw new Exception("Execution failed: " . $stmt->error);
    }

    echo json_encode(['message' => 'Result saved successfully!']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}
?>
