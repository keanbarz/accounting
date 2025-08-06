<?php
// Replace with your actual database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sample data to insert
 $last_name = $_POST["lastname"] ;
 $first_name = $_POST["firstname"] ;
 $email = $_POST["email"] ;
 $pass = $_POST["password"] ;

// Prepare and execute SQL query
$stmt = $conn->prepare("INSERT INTO register (LastName, FirstName, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $last_name, $first_name, $email, $pass);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    $message =  "Registered successfully!";
    header("Location: " . $_SERVER["HTTP_REFERER"] . "?message=" . urlencode($message));
        exit;
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
