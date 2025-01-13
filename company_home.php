<?php
session_start();

// Ensure the user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'company') {
    header("Location: login.php");
    exit;
}

$company_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "flight_booking");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch company details
$company_query = "SELECT * FROM users WHERE id = $company_id";
$company_result = $conn->query($company_query);
$company = $company_result->fetch_assoc();

// Fetch active flights for the company (not canceled)
$flights_query = "SELECT * FROM flights WHERE company_id = $company_id AND completed = 0";
$flights_result = $conn->query($flights_query);

// Fetch canceled flights for the company
$canceled_flights_query = "SELECT * FROM flights WHERE company_id = $company_id AND completed = 1";
$canceled_flights_result = $conn->query($canceled_flights_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="company_home.css">
    <title>Company Home</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($company['name']); ?></h1>
    <img src="uploads/<?php echo htmlspecialchars($company['logo_img']); ?>" alt="Logo" width="100">
    
    <h2>Menu</h2>
    <ul>
        <li><a href="add_flight.php">Add Flight</a></li>
        <li><a href="company_profile.php">View Profile</a></li>
        <li><a href="?tab=messages">Messages</a></li>
        <link rel="stylesheet" href="styles.css">
    </ul>

    <!-- Display Active Flights -->
    <h2>Your Flights</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Itinerary</th>
            <th>Passengers (Registered / Pending)</th>
            <th>Actions</th>
        </tr>
        <?php while ($flight = $flights_result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $flight['id']; ?></td>
            <td><?php echo htmlspecialchars($flight['name']); ?></td>
            <td><?php echo htmlspecialchars($flight['itinerary']); ?></td>
            <?php
            // Dynamically calculate passenger counts
            $registered_query = "
                SELECT COUNT(*) AS registered 
                FROM bookings 
                WHERE flight_id = {$flight['id']} AND status = 'registered'";
            $registered_result = $conn->query($registered_query);
            $registered = $registered_result->fetch_assoc()['registered'];

            $pending_query = "
                SELECT COUNT(*) AS pending 
                FROM bookings 
                WHERE flight_id = {$flight['id']} AND status = 'pending'";
            $pending_result = $conn->query($pending_query);
            $pending = $pending_result->fetch_assoc()['pending'];
            ?>
            <td><?php echo $registered . " / " . max(0, $pending); ?></td>
            <td>
                <a href="flight_details.php?id=<?php echo $flight['id']; ?>">View Details</a> |
                <a href="cancel_flight.php?id=<?php echo $flight['id']; ?>" onclick="return confirm('Are you sure you want to cancel this flight?');">Cancel</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <!-- Display Canceled Flights -->
    <h2>Canceled Flights</h2>
    <?php if ($canceled_flights_result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Itinerary</th>
                <th>Passengers (Registered / Pending)</th>
                <th>Status</th>
            </tr>
            <?php while ($flight = $canceled_flights_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $flight['id']; ?></td>
                <td><?php echo htmlspecialchars($flight['name']); ?></td>
                <td><?php echo htmlspecialchars($flight['itinerary']); ?></td>
                <td>0 / 0</td>
                <td>Canceled</td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No canceled flights.</p>
    <?php } ?>

    <!-- Messages Tab -->
    <?php if (isset($_GET['tab']) && $_GET['tab'] == 'messages') { ?>
        <h2>Your Messages</h2>
        <?php
        // Fetch messages for the company
        $messages_query = "
            SELECT m.*, u.name AS passenger_name 
            FROM messages m 
            JOIN users u ON m.sender_id = u.id 
            WHERE m.receiver_id = $company_id
            ORDER BY m.timestamp DESC";
        $messages_result = $conn->query($messages_query);

        if ($messages_result->num_rows > 0) { ?>
            <table border="1">
                <tr>
                    <th>From</th>
                    <th>Message</th>
                    <th>Reply</th>
                </tr>
                <?php while ($message = $messages_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($message['passenger_name']); ?></td>
                    <td><?php echo htmlspecialchars($message['content']); ?></td>
                    <td>
                        <form method="post" action="reply_to_passenger.php">
                            <input type="hidden" name="passenger_id" value="<?php echo $message['sender_id']; ?>">
                            <textarea name="message" placeholder="Write your reply here..." required></textarea><br>
                            <button type="submit">Send Reply</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No messages found.</p>
        <?php }
    } ?>

    <?php $conn->close(); ?>
</body>
</html>
