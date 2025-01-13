<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "flight_booking");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Save user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['type'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect based on user type
            if ($user['type'] == 'company') {
                header("Location: company_home.php");
            } else {
                header("Location: passenger_home.php");
            }
            exit;
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with this email!";
    }
    $conn->close();
}
?>
