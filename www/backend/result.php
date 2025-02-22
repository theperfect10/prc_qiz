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
    INSERT INTO result (email, qindex, time_remaining, updated_at)
    VALUES (?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE
    time_remaining = VALUES(time_remaining),
    updated_at = NOW()
";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $email, $questionNumber, $timeRemaining);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    echo json_encode(['message' => 'Result saved successfully.']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}
?>
