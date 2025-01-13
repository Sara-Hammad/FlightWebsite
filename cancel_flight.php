<?php
session_start();

// Ensure the user is logged in as a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'company') {
    header("Location: login.php");
    exit;
}

// Validate and retrieve the flight ID from the GET parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid flight ID.");
}
$flight_id = (int)$_GET['id'];

$conn = new mysqli("localhost", "root", "", "flight_booking");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the flight details
$company_id = $_SESSION['user_id'];
$flight_query = "SELECT * FROM flights WHERE id = $flight_id AND company_id = $company_id";
$flight_result = $conn->query($flight_query);

if ($flight_result->num_rows == 0) {
    die("Flight not found or you do not have permission to cancel this flight.");
}

$flight = $flight_result->fetch_assoc();

// Update the flight status to "cancelled"
$cancel_flight_query = "UPDATE flights SET completed = 1 WHERE id = $flight_id";
if ($conn->query($cancel_flight_query)) {
    // Refund all passengers who are registered for this flight
    $refund_query = "
        UPDATE users u
        JOIN bookings b ON u.id = b.passenger_id
        SET u.account_balance = u.account_balance + {$flight['fees']}
        WHERE b.flight_id = $flight_id AND b.status = 'registered'";
    $conn->query($refund_query);

    // Mark all bookings for this flight as "cancelled"
    $cancel_bookings_query = "UPDATE bookings SET status = 'cancelled' WHERE flight_id = $flight_id";
    $conn->query($cancel_bookings_query);

    echo "Flight successfully cancelled and passengers refunded.";
} else {
    echo "Error cancelling flight: " . $conn->error;
}

$conn->close();
header("Location: company_home.php");
exit;
?>
