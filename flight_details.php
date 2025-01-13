<?php
session_start();

// Ensure the user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'company') {
    header("Location: login.php");
    exit;
}

$flight_id = $_GET['id'];
$conn = new mysqli("localhost", "root", "", "flight_booking");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch flight details
$flight_query = "SELECT * FROM flights WHERE id = $flight_id";
$flight_result = $conn->query($flight_query);
$flight = $flight_result->fetch_assoc();

// Fetch pending passengers with their details
$pending_query = "
    SELECT b.id AS booking_id, u.id AS passenger_id, u.name AS passenger_name 
    FROM bookings b 
    JOIN users u ON b.passenger_id = u.id 
    WHERE b.flight_id = $flight_id AND b.status = 'pending'";
$pending_result = $conn->query($pending_query);

// Fetch registered passengers with their details
$registered_query = "
    SELECT u.id AS passenger_id, u.name AS passenger_name 
    FROM bookings b 
    JOIN users u ON b.passenger_id = u.id 
    WHERE b.flight_id = $flight_id AND b.status = 'registered'";
$registered_result = $conn->query($registered_query);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="flight_details.css">
    <title>Flight Details</title>
</head>
<body>
    <h1>Flight Details</h1>
    <p><strong>ID:</strong> <?php echo $flight['id']; ?></p>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($flight['name']); ?></p>
    <p><strong>Itinerary:</strong> <?php echo htmlspecialchars($flight['itinerary']); ?></p>
    <p><strong>Fees:</strong> <?php echo $flight['fees']; ?></p>
    <p><strong>Start Time:</strong> <?php echo $flight['start_time']; ?></p>
    <p><strong>End Time:</strong> <?php echo $flight['end_time']; ?></p>

    <h2>Pending Passengers</h2>
    <?php if ($pending_result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>Passenger ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
            <?php while ($pending = $pending_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $pending['passenger_id']; ?></td>
                <td><?php echo htmlspecialchars($pending['passenger_name']); ?></td>
                <td>
                    <form method="post" action="approve_passenger.php">
                        <input type="hidden" name="booking_id" value="<?php echo $pending['booking_id']; ?>">
                        <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>">
                        <button type="submit">Approve</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No pending passengers.</p>
    <?php } ?>

    <h2>Registered Passengers</h2>
    <?php if ($registered_result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>Passenger ID</th>
                <th>Name</th>
            </tr>
            <?php while ($registered = $registered_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $registered['passenger_id']; ?></td>
                <td><?php echo htmlspecialchars($registered['passenger_name']); ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No registered passengers.</p>
    <?php } ?>

    <a href="company_home.php">Back to Home</a>
</body>
</html>
