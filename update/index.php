<?php

require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $id = $_POST['id'];
    $orurl = $_POST['orurl'];
    $shurl = $_POST['shurl'];
    $checkTokenQuery = "SELECT id FROM users WHERE token = ?";
    $stmt = $conn->prepare($checkTokenQuery);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $updateStmt = $conn->prepare("UPDATE `urls` SET `orurl` = ?, `shurl` = ? WHERE `urls`.`id` = ?;");
        $updateStmt->bind_param("ssi", $orurl, $shurl, $id);
        if ($updateStmt->execute()) {
            http_response_code(200);
            $response = array("message" => "URL updated successfully");
            echo json_encode($response);
        } else {
            http_response_code(500); // Internal Server Error
            $response = array("error" => "Database error. URL update failed.");
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
