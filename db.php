<?php
$dbConfig = array(
    "servername" => "localhost",
    "username" => "root",
    "password" => "",
    "dbname" => "urls"
);

$conn = new mysqli($dbConfig['servername'], $dbConfig['username'], $dbConfig['password'], $dbConfig['dbname']);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit();
}

function getUrlIdByShortUrl($shurl) {
    global $conn; 

    // Prepare an SQL statement to retrieve the URL ID based on the short URL
    $stmt = $conn->prepare("SELECT id FROM urls WHERE shurl = ?");

    // Bind parameter to prevent SQL injection
    $stmt->bind_param("s", $shurl);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id']; // Return the URL ID
    } else {
        return null; // Short URL not found
    }
}

?>
