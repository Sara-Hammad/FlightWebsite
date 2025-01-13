<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $tel = $_POST['tel'];
    $type = $_POST['type'];

    // Redirect to the next page based on user type
    if ($type == 'company') {
        header("Location: company_details.php?name=$name&email=$email&tel=$tel&password=$password");
    } elseif ($type == 'passenger') {
        header("Location: passenger_details.php?name=$name&email=$email&tel=$tel&password=$password");
    }
    exit;
}
?>
