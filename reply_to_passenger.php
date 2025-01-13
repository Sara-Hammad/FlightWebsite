<?php
session_start();

// Ensure the user is logged in as a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'company') {
    header("Location: login.php");
    exit;
}

$company_id = $_SESSION['user_id'];
$passenger_id = $_POST['passenger_id'];
$message = $_POST['message'];

$conn = new mysqli("localhost", "root", "", "flight_booking");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Escape inputs
$message = $conn->real_escape_string($message);
$passenger_id = (int)$passenger_id;

// Insert the reply message into the messages table
$reply_query = "INSERT INTO messages (sender_id, receiver_id, content) 
                VALUES ($company_id, $passenger_id, '$message')";

if ($conn->query($reply_query)) {
    echo "Reply sent successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
header("Location: company_home.php?tab=messages");
exit;
?>
