<?php
require 'db_conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'])) {
    echo json_encode(['error' => 'Invalid input.']);
    exit();
}

$email = $data['email'];

try {

    $query = "
    INSERT INTO result (email, updated_at)
    VALUES (?, NOW())
    ";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Preparation failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);

    if (!$stmt->execute()) {
        throw new Exception("Execution failed: " . $stmt->error);
    }

    echo json_encode(['message' => 'Result saved successfully!']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}
?>
