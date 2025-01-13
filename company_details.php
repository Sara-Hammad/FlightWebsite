<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $tel = $_POST['tel'];
    $bio = $_POST['bio'];
    $address = $_POST['address'];
    $location = $_POST['location'];
    $logo = $_FILES['logo']['name'];
    // Default account balance
    $account_balance = 0.00;

    // Save file upload
    move_uploaded_file($_FILES['logo']['tmp_name'], "uploads/$logo");

    // Insert into database
    $conn = new mysqli("localhost", "root", "", "flight_booking");
    $sql = "INSERT INTO users (type, name, email, password, tel, bio, address, location, logo_img, account_balance)
            VALUES ('company', '$name', '$email', '$password', '$tel', '$bio', '$address', '$location', '$logo', $account_balance)";
    $conn->query($sql);
    $conn->close();

    echo "Company registered successfully!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Details</title>
    <link rel="stylesheet" href="company_details.css">

</head>
<body>
    <h1>Additional Details (Company)</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="name" value="<?php echo $_GET['name']; ?>">
        <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>">
        <input type="hidden" name="password" value="<?php echo $_GET['password']; ?>">
        <input type="hidden" name="tel" value="<?php echo $_GET['tel']; ?>">

        <label for="bio">Bio:</label><br>
        <textarea id="bio" name="bio" required></textarea><br><br>

        <label for="address">Address:</label><br>
        <input type="text" id="address" name="address" required><br><br>

        <label for="location">Location (optional):</label><br>
        <input type="text" id="location" name="location"><br><br>

        <label for="logo">Logo:</label><br>
        <input type="file" id="logo" name="logo" required><br><br>
        
        <label for="account_balance">Initial Balance (in $):</label><br>
        <input type="number" id="account_balance" name="account_balance"><br><br>



        <button type="submit">Submit</button>
    </form>
</body>
</html>
