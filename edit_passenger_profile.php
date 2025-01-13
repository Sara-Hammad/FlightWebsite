<?php
session_start();

// Ensure the user is logged in and is a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'passenger') {
    header("Location: login.php");
    exit;
}

$passenger_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "flight_booking");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $photo = $_FILES['photo']['name'];
    $passport_img = $_FILES['passport_img']['name'];

    // Update query
    $update_query = "UPDATE users SET name = '$name', email = '$email', tel = '$tel'";

    // Process uploaded files if provided
    if ($photo) {
        $photo_path = "uploads/$photo";
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
        $update_query .= ", photo = '$photo'";
    }

    if ($passport_img) {
        $passport_path = "uploads/$passport_img";
        move_uploaded_file($_FILES['passport_img']['tmp_name'], $passport_path);
        $update_query .= ", passport_img = '$passport_img'";
    }

    $update_query .= " WHERE id = $passenger_id";

    if ($conn->query($update_query)) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . $conn->error;
    }
    $conn->close();
    header("Location: passenger_profile.php");
    exit;
}
?>
