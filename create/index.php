<?php

require_once('../db.php');

if (isset($_POST['token']) && isset($_POST['original_url']) && isset($_POST['short_url'])) {
    $token = $_POST['token'];
    $original_url = $_POST['original_url'];
    $short_url = $_POST['short_url'];

    // Prepare an SQL statement to check if the token exists in the users table
    $checkTokenQuery = "SELECT id FROM users WHERE token = ?";
    $stmt = $conn->prepare($checkTokenQuery);

    // Bind the token parameter
    $stmt->bind_param("s", $token);
    
    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token exists, proceed with URL insertion
        $insertStmt = $conn->prepare("INSERT INTO urls (token, orurl, shurl) VALUES (?, ?, ?)");

        // Bind parameters to prevent SQL injection
        $insertStmt->bind_param("sss",$token, $original_url, $short_url);

        // Execute the insertion statement
        if ($insertStmt->execute()) {
            http_response_code(200);
            $response = array("message" => "URL created successfully");
            echo json_encode($response);
        } else {
            http_response_code(500); // Internal Server Error
            $response = array("error" => "Database error. URL registration failed.");
            echo json_encode($response);
        }
    } else {
        // Token does not exist in the users table
        http_response_code(403); // Forbidden
        $response = array("error" => "Invalid token");
        echo json_encode($response);
    }
} else {
    // Incomplete data provided
    http_response_code(400);
    $missingFields = array();
    if (!isset($_POST['token'])) {
        $missingFields[] = 'token';
    }
    if (!isset($_POST['original_url'])) {
        $missingFields[] = 'original_url';
    }
    if (!isset($_POST['short_url'])) {
        $missingFields[] = 'short_url';
    }

    $response = array("error" => "Incomplete data provided", "missing_fields" => $missingFields);
    echo json_encode($response);
}
?>
