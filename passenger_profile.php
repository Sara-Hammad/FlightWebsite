<?php
session_start();

// Ensure the user is logged in and is a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'passenger') {
    header("Location: login.php");
    exit;
}

$passenger_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "flight_booking");

// Fetch passenger details
$passenger_query = "SELECT * FROM users WHERE id = $passenger_id";
$passenger_result = $conn->query($passenger_query);
$passenger = $passenger_result->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Profile</title>
    <link rel="stylesheet" href="passenger_profile.css">
</head>
<body>
    <h1>Your Profile</h1>
    
    <!-- Profile Container -->
    <div class="profile-container">
        <!-- Profile Photo -->
        <div class="profile-box">
            <img src="uploads/<?php echo htmlspecialchars($passenger['photo']); ?>" class="icons" alt="Profile Photo">
            
        </div>
        
        <!-- Passport Photo -->
        <div class="profile-box">
            <img src="uploads/<?php echo htmlspecialchars($passenger['passport_img']); ?>" class="icons" alt="Passport Photo">
           
        </div>
    </div>

    <!-- Edit Profile Form -->
    <form method="post" action="edit_passenger_profile.php" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($passenger['name']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($passenger['email']); ?>" required>

        <label for="tel">Telephone:</label>
        <input type="text" id="tel" name="tel" value="<?php echo htmlspecialchars($passenger['tel']); ?>" required>

        <label for="photo">Update Photo:</label>
        <input type="file" id="photo" name="photo">

        <label for="passport_img">Update Passport Image:</label>
        <input type="file" id="passport_img" name="passport_img">

        <button type="submit">Save Changes</button>
    </form>
</body>
</html>

