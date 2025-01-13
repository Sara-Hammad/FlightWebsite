<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <form method="post" action="register_handler.php">
        <h1>Register</h1>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <label for="tel">Telephone:</label>
        <input type="text" id="tel" name="tel" required>
        
        <label for="type">Register as:</label>
        <select id="type" name="type" required>
            <option value="company">Company</option>
            <option value="passenger">Passenger</option>
        </select>
        
        <button type="submit">Next</button>
    </form>
</body>
</html>
