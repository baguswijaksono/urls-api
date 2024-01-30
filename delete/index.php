<?php

require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $id = $_POST['id'];

    // Prepare an SQL statement to check if the token exists in the users table
    $checkTokenQuery = "SELECT id FROM users WHERE token = ?";
    $stmt = $conn->prepare($checkTokenQuery);

    // Bind the token parameter
    $stmt->bind_param("s", $token);
    
    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token exists, proceed with URL deletion
        $deleteStmt = $conn->prepare("DELETE FROM urls WHERE id = ?");

        // Bind parameter to prevent SQL injection
        $deleteStmt->bind_param("s", $id);

        // Execute the deletion statement
        if ($deleteStmt->execute()) {
            http_response_code(200);
            $response = array("message" => "URL deleted successfully");
            echo json_encode($response);
        } else {
            http_response_code(500); // Internal Server Error
            $response = array("error" => "Database error. URL deletion failed.");
            echo json_encode($response);
        }
    } else {
        // Token does not exist in the users table
        http_response_code(403); // Forbidden
        $response = array("error" => "Invalid token");
        echo json_encode($response);
    }
} else {
    http_response_code(405); // Method Not Allowed
    $response = array("error" => "Method not allowed");
    echo json_encode($response);
}
?>
