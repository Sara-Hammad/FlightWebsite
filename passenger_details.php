<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Save passenger data to database
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $tel = $_POST['tel'];
    $photo = $_FILES['photo']['name'];
    $passport_img = $_FILES['passport']['name'];

    $account_balance = 0.00;

    // Save file uploads
    move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/$photo");
    move_uploaded_file($_FILES['passport']['tmp_name'], "uploads/$passport_img");

    // Insert into database
    $conn = new mysqli("localhost", "root", "", "flight_booking");
    $sql = "INSERT INTO users (type, name, email, password, tel, photo, passport_img, account_balance)
            VALUES ('passenger', '$name', '$email', '$password', '$tel', '$photo', '$passport_img', $account_balance)";
    $conn->query($sql);
    $conn->close();

    echo "Passenger registered successfully!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passenger Details</title>
    <link rel="stylesheet" href="passenger_details.css">

</head>
<body>
    <h1>Additional Details (Passenger)</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="name" value="<?php echo $_GET['name']; ?>">
        <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>">
        <input type="hidden" name="password" value="<?php echo $_GET['password']; ?>">
        <input type="hidden" name="tel" value="<?php echo $_GET['tel']; ?>">

        <label for="photo">Photo:</label><br>
        <input type="file" id="photo" name="photo" required><br><br>

        <label for="passport">Passport Image:</label><br>
        <input type="file" id="passport" name="passport" required><br><br>

        <label for="account_balance">Initial Balance (in $):</label><br>
        <input type="number" id="account_balance" name="account_balance"><br><br>


        <button type="submit">Submit</button>
    </form>
</body>
</html>
