<?php
session_start();

// Ensure the user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'company') {
    header("Location: login.php");
    exit;
}

$company_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "flight_booking");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $address = $_POST['address'];
    $logo = $_FILES['logo']['name'];

    // Update query
    $update_query = "UPDATE users SET name = '$name', bio = '$bio', address = '$address'";

    // If a new logo is uploaded, process it
    if ($logo) {
        $logo_path = "uploads/$logo";
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path);
        $update_query .= ", logo_img = '$logo'";
    }

    $update_query .= " WHERE id = $company_id";

    if ($conn->query($update_query)) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . $conn->error;
    }
    $conn->close();
    header("Location: company_profile.php");
    exit;
}
?>
