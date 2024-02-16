<?php

require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare an SQL statement to fetch user data based on the provided email
    $stmt = $conn->prepare("SELECT password, token , username FROM users WHERE email = ?");

    // Bind parameters to prevent SQL injection
    $stmt->bind_param("s", $email);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        // Verify the provided password with the hashed password from the database
        if (password_verify($password, $hashedPassword)) {
            $userToken = $row['token'];
            $userName = $row['username'];

            http_response_code(200);
            $response = array("user_token" => $userToken,"user_name" => $userName);
            echo json_encode($response);
        } else {
            http_response_code(401); // Unauthorized
            $response = array("error" => "Invalid email or password");
            echo json_encode($response);
        }
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
