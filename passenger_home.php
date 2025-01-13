<?php
session_start();

// Ensure the user is logged in and is a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'passenger') {
    header("Location: login.php");
    exit;
}

$passenger_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "flight_booking");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch passenger details
$passenger_query = "SELECT * FROM users WHERE id = $passenger_id";
$passenger_result = $conn->query($passenger_query);
$passenger = $passenger_result->fetch_assoc();

// Fetch completed flights
$completed_flights_query = "
    SELECT f.id, f.name, f.itinerary, f.start_time, f.end_time
    FROM flights f
    JOIN bookings b ON f.id = b.flight_id
    WHERE b.passenger_id = $passenger_id 
      AND b.status = 'registered' 
      AND f.end_time < NOW()";
$completed_flights_result = $conn->query($completed_flights_query);

// Fetch current flights
$current_flights_query = "
    SELECT f.id, f.name, f.itinerary, f.start_time, f.end_time
    FROM flights f
    JOIN bookings b ON f.id = b.flight_id
    WHERE b.passenger_id = $passenger_id 
      AND b.status = 'registered' 
      AND f.end_time >= NOW()";
$current_flights_result = $conn->query($current_flights_query);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Home</title>
    <link rel="stylesheet" href="passenger_home.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($passenger['name']); ?></h1>
    <img src="uploads/<?php echo htmlspecialchars($passenger['photo']); ?>" alt="Profile Image" width="100"><br>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($passenger['email']); ?></p>
    <p><strong>Telephone:</strong> <?php echo htmlspecialchars($passenger['tel']); ?></p>

    <h2>Menu</h2>
    <ul>
        <li><a href="passenger_profile.php">View Profile</a></li>
        <li><a href="search_flight.php">Search Flights</a></li>
        <li><a href="?tab=messages">Messages</a></li>
        
    </ul>

    <!-- Completed Flights -->
    <h2>Completed Flights</h2>
    <?php if ($completed_flights_result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Itinerary</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
            <?php while ($flight = $completed_flights_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $flight['id']; ?></td>
                <td><?php echo htmlspecialchars($flight['name']); ?></td>
                <td><?php echo htmlspecialchars($flight['itinerary']); ?></td>
                <td><?php echo $flight['start_time']; ?></td>
                <td><?php echo $flight['end_time']; ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No completed flights.</p>
    <?php } ?>

    <!-- Current Flights -->
    <h2>Current Flights</h2>
    <?php if ($current_flights_result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Itinerary</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
            <?php while ($flight = $current_flights_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $flight['id']; ?></td>
                <td><?php echo htmlspecialchars($flight['name']); ?></td>
                <td><?php echo htmlspecialchars($flight['itinerary']); ?></td>
                <td><?php echo $flight['start_time']; ?></td>
                <td><?php echo $flight['end_time']; ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No current flights.</p>
    <?php } ?>

    <!-- Messages Tab -->
    <?php if (isset($_GET['tab']) && $_GET['tab'] == 'messages') { ?>
        <h2>Your Messages</h2>
        <?php
        $conn = new mysqli("localhost", "root", "", "flight_booking");

        // Fetch messages for the passenger
        $messages_query = "
            SELECT m.*, u.name AS company_name 
            FROM messages m 
            JOIN users u ON u.id = IF(m.sender_id = $passenger_id, m.receiver_id, m.sender_id)
            WHERE m.sender_id = $passenger_id OR m.receiver_id = $passenger_id
            ORDER BY m.timestamp DESC";
        $messages_result = $conn->query($messages_query);

        if ($messages_result->num_rows > 0) { ?>
            <table border="1">
                <tr>
                    <th>From</th>
                    <th>Message</th>
                    <th>Timestamp</th>
                </tr>
                <?php while ($message = $messages_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($message['sender_id'] == $passenger_id ? 'You' : $message['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($message['content']); ?></td>
                    <td><?php echo $message['timestamp']; ?></td>
                </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No messages found.</p>
        <?php } ?>

        <h3>Send a New Message</h3>
        <form method="post" action="message_company.php">
            <label for="company_id">Select Company:</label><br>
            <select id="company_id" name="company_id" required>
                <?php
                // Fetch all companies
                $companies_query = "SELECT id, name FROM users WHERE type = 'company'";
                $companies_result = $conn->query($companies_query);
                while ($company = $companies_result->fetch_assoc()) { ?>
                    <option value="<?php echo $company['id']; ?>"><?php echo htmlspecialchars($company['name']); ?></option>
                <?php } ?>
            </select><br><br>

            <textarea name="message" placeholder="Write your message here..." required></textarea><br><br>
            <button type="submit">Send Message</button>
        </form>
        <?php $conn->close(); ?>
    <?php } ?>
</body>
</html>
