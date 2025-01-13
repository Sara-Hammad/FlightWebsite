<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$flight_id = $_GET['id'];
$conn = new mysqli("localhost", "root", "", "flight_booking");

// Fetch flight details
$flight_query = "SELECT f.*, u.name AS company_name 
                 FROM flights f 
                 JOIN users u ON f.company_id = u.id 
                 WHERE f.id = $flight_id";
$flight_result = $conn->query($flight_query);
$flight = $flight_result->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="flight_info.css">
    <title>Flight Info</title>
</head>
<body>
    <h1>Flight Information</h1>
    <p><strong>ID:</strong> <?php echo $flight['id']; ?></p>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($flight['name']); ?></p>
    <p><strong>Itinerary:</strong> <?php echo htmlspecialchars($flight['itinerary']); ?></p>
    <p><strong>Fees:</strong> <?php echo $flight['fees']; ?></p>
    <p><strong>Start Time:</strong> <?php echo $flight['start_time']; ?></p>
    <p><strong>End Time:</strong> <?php echo $flight['end_time']; ?></p>
    <p><strong>Company:</strong> <?php echo htmlspecialchars($flight['company_name']); ?></p>

    <h2>Book This Flight</h2>
    <form method="post" action="book_flight.php">
        <input type="hidden" name="flight_id" value="<?php echo $flight['id']; ?>">
        <label for="payment_type">Payment Type:</label><br>
        <select id="payment_type" name="payment_type" required>
            <option value="account">From Account $</option>
            <option value="cash">Cash</option>
        </select><br><br>
        <button type="submit">Book Flight</button>
    </form>

    <h2>Message the Company</h2>
    <form method="post" action="message_company.php">
        <input type="hidden" name="company_id" value="<?php echo $flight['company_id']; ?>">
        <textarea name="message" placeholder="Write your message here..." required></textarea><br><br>
        <button type="submit">Send Message</button>
    </form>
</body>
</html>
