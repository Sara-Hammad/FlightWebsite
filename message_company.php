<?php
session_start();

// Ensure the user is logged in as a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'passenger') {
    header("Location: login.php");
    exit;
}

$sender_id = $_SESSION['user_id'];
$company_id = $_POST['company_id'];
$message = $_POST['message'];

$conn = new mysqli("localhost", "root", "", "flight_booking");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Escape inputs
$message = $conn->real_escape_string($message);
$company_id = (int)$company_id;

// Save the message
$message_query = "INSERT INTO messages (sender_id, receiver_id, content) 
                  VALUES ($sender_id, $company_id, '$message')";

if ($conn->query($message_query)) {
    echo "Message sent successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
header("Location: passenger_home.php");
exit;
?>
