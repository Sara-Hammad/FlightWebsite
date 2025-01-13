<?php
session_start();

// Ensure the user is logged in as a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'company') {
    header("Location: login.php");
    exit;
}

$booking_id = $_POST['booking_id'];
$flight_id = $_POST['flight_id'];

$conn = new mysqli("localhost", "root", "", "flight_booking");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update the passenger's status to 'registered'
$update_booking_query = "UPDATE bookings SET status = 'registered' WHERE id = $booking_id";
if ($conn->query($update_booking_query)) {
    // Fetch current pending count to ensure it doesn't go below 0
    $flight_query = "SELECT pending FROM flights WHERE id = $flight_id";
    $flight_result = $conn->query($flight_query);
    $flight = $flight_result->fetch_assoc();

    // Update counts only if pending > 0
    if ($flight['pending'] > 0) {
        $update_flight_query = "
            UPDATE flights 
            SET registered = registered + 1, pending = pending - 1 
            WHERE id = $flight_id";
        $conn->query($update_flight_query);
    }
    echo "Passenger approved successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
header("Location: flight_details.php?id=$flight_id");
exit;
?>
