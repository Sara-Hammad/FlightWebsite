<?php
session_start();

// Ensure the user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'company') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $id = $_POST['id'];
    $itinerary = $_POST['itinerary'];
    $fees = $_POST['fees'];
    $max_passengers = $_POST['max_passengers'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "flight_booking");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert the flight into the database
    $sql = "INSERT INTO flights (id, name, company_id, itinerary, fees, max_passengers, start_time, end_time)
            VALUES ('$id', '$name', $company_id, '$itinerary', $fees, $max_passengers, '$start_time', '$end_time')";

    if ($conn->query($sql)) {
        echo "Flight added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
    header("Location: company_home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="add_flight.php">
    <title>Add Flight</title>
</head>
<body>
    <h1>Add Flight</h1>
    <form method="post">
        <label for="name">Flight Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="id">Flight ID:</label><br>
        <input type="text" id="id" name="id" required><br><br>

        <label for="itinerary">Itinerary (e.g., City1->City2->City3):</label><br>
        <input type="text" id="itinerary" name="itinerary" required><br><br>

        <label for="fees">Fees (in $):</label><br>
        <input type="number" id="fees" name="fees" step="0.01" required><br><br>

        <label for="max_passengers">Maximum Passengers:</label><br>
        <input type="number" id="max_passengers" name="max_passengers" required><br><br>

        <label for="start_time">Start Time:</label><br>
        <input type="datetime-local" id="start_time" name="start_time" required><br><br>

        <label for="end_time">End Time:</label><br>
        <input type="datetime-local" id="end_time" name="end_time" required><br><br>

        <button type="submit">Add Flight</button>
    </form>
</body>
</html>
