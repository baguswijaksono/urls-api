<?php

require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'];

    // Prepare an SQL statement to fetch user data based on the provided token
    $stmt = $conn->prepare("SELECT id, orurl, shurl FROM `urls` WHERE token = ?");

    // Bind parameters to prevent SQL injection
    $stmt->bind_param("s", $token);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch all the data from the result set
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        // Output the result as JSON
        echo json_encode($rows);
    } else {
        http_response_code(404); // Not Found
        $response = array("error" => "User not found");
        echo json_encode($response);
    }
} else {
    http_response_code(405); // Method Not Allowed
    $response = array("error" => "Method not allowed");
    echo json_encode($response);
}
?>
