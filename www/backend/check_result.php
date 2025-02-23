<?php
require 'db_conn.php';

// Get JSON data from request body
$data = json_decode(file_get_contents("php://input"), true);

// Validate the JSON input
if (!isset($data['email'])) {
    echo json_encode(['error' => 'Invalid input.']);
    exit();
}

$email = $data['email'];

try {
    // Query to check if the user has a result
    $query = "SELECT score FROM result WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Preparation failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("s", $email);

    // Execute the query
    if (!$stmt->execute()) {
        throw new Exception("Execution failed: " . $stmt->error);
    }

    // Fetch the result
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['attempted' => true, 'score' => $row['score']]);
    } else {
        echo json_encode(['attempted' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}
?>
