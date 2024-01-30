<?php

require_once('../db.php');

if (isset($_GET['id'])) {
    $short_url = $_GET['id'];

    // Prepare an SQL statement to retrieve the original URL
    $stmt = $conn->prepare("SELECT * FROM urls WHERE id = ?");
    $stmt->bind_param("s", $short_url);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        http_response_code(200); // OK
        echo json_encode($row);

    } else {
        http_response_code(404); // Not Found
        $response = array("error" => "Short URL not found");
        echo json_encode($response);
    }
} else {
    http_response_code(400);
    $response = array("error" => "Short URL not provided");
    echo json_encode($response);
}
?>