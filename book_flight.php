<?php
session_start();

// Ensure the user is logged in as a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'passenger') {
    header("Location: login.php");
    exit;
}

$passenger_id = $_SESSION['user_id'];
$flight_id = $_POST['flight_id'];
$payment_type = $_POST['payment_type'];

$conn = new mysqli("localhost", "root", "", "flight_booking");

// Check if the passenger has enough balance (if payment type is "account")
if ($payment_type === 'account') {
    $balance_query = "SELECT account_balance FROM users WHERE id = $passenger_id";
    $balance_result = $conn->query($balance_query);
    $balance = $balance_result->fetch_assoc()['account_balance'];

    $flight_query = "SELECT fees FROM flights WHERE id = $flight_id";
    $flight_result = $conn->query($flight_query);
    $fees = $flight_result->fetch_assoc()['fees'];

    if ($balance < $fees) {
        echo "Insufficient balance to book this flight.";
        exit;
    }

    // Deduct fees from the account balance
    $update_balance_query = "UPDATE users SET account_balance = account_balance - $fees WHERE id = $passenger_id";
    $conn->query($update_balance_query);
}

// Add the booking
$booking_query = "INSERT INTO bookings (flight_id, passenger_id, status, payment_method) 
                  VALUES ($flight_id, $passenger_id, 'pending', '$payment_type')";
$conn->query($booking_query);

$conn->close();
echo "Flight booked successfully! Please wait for confirmation.";
header("Location: passenger_home.php");
exit;
?>
