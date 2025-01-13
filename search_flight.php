<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "flight_booking");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$flights_result = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $from = $conn->real_escape_string($_POST['from']);
    $to = $conn->real_escape_string($_POST['to']);

    // Search for flights excluding canceled ones
    $search_query = "
        SELECT * FROM flights 
        WHERE completed = 0 
          AND itinerary LIKE '%$from%' 
          AND itinerary LIKE '%$to%'";
    $flights_result = $conn->query($search_query);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="search_flight.css">
    <title>Search a Flight</title>
</head>
<body>
    <h1>Search a Flight</h1>
    <form method="post">
        <label for="from">From:</label><br>
        <input type="text" id="from" name="from" required><br><br>

        <label for="to">To:</label><br>
        <input type="text" id="to" name="to" required><br><br>

        <button type="submit">Search</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') { ?>
        <h2>Available Flights</h2>
        <?php if ($flights_result->num_rows > 0) { ?>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Itinerary</th>
                    <th>Fees</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Details</th>
                </tr>
                <?php while ($flight = $flights_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $flight['id']; ?></td>
                    <td><?php echo htmlspecialchars($flight['name']); ?></td>
                    <td><?php echo htmlspecialchars($flight['itinerary']); ?></td>
                    <td><?php echo $flight['fees']; ?></td>
                    <td><?php echo $flight['start_time']; ?></td>
                    <td><?php echo $flight['end_time']; ?></td>
                    <td><a href="flight_info.php?id=<?php echo $flight['id']; ?>">View Info</a></td>
                </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No flights found matching your search.</p>
        <?php } ?>
    <?php } ?>
</body>
</html>
