<?php
session_start();

// Ensure the user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'company') {
    header("Location: login.php");
    exit;
}

$company_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "flight_booking");

// Fetch company details
$company_query = "SELECT * FROM users WHERE id = $company_id";
$company_result = $conn->query($company_query);
$company = $company_result->fetch_assoc();

// Fetch company flights
$flights_query = "SELECT * FROM flights WHERE company_id = $company_id";
$flights_result = $conn->query($flights_query);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="company_profile.css">

    <title>Company Profile</title>
</head>
<body>
    <h1>Company Profile</h1>
    <img src="uploads/<?php echo htmlspecialchars($company['logo_img']); ?>" alt="Logo" width="100"><br>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($company['name']); ?></p>
    <p><strong>Bio:</strong> <?php echo htmlspecialchars($company['bio']); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($company['address']); ?></p>

    <h2>Flights List</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Itinerary</th>
            <th>Passengers (Registered / Pending)</th>
        </tr>
        <?php while ($flight = $flights_result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $flight['id']; ?></td>
            <td><?php echo htmlspecialchars($flight['name']); ?></td>
            <td><?php echo htmlspecialchars($flight['itinerary']); ?></td>
            <td><?php echo $flight['registered'] . " / " . $flight['pending']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <h2>Edit Profile</h2>
    <form method="post" action="edit_company_profile.php" enctype="multipart/form-data">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($company['name']); ?>" required><br><br>
        
        <label for="bio">Bio:</label><br>
        <textarea id="bio" name="bio" required><?php echo htmlspecialchars($company['bio']); ?></textarea><br><br>
        
        <label for="address">Address:</label><br>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($company['address']); ?>" required><br><br>
        
        <label for="logo">Logo:</label><br>
        <input type="file" id="logo" name="logo"><br><br>
        
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
