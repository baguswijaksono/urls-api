<?php

require_once('../db.php');

if (isset($_GET['short_url'])) {
    $short_url = $_GET['short_url'];

    // Prepare an SQL statement to retrieve the original URL
    $stmt = $conn->prepare("SELECT orurl FROM urls WHERE shurl = ?");
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