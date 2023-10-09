<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONLINE FOOD DELIVERY</title>
    <link rel="stylesheet" type="text/css" href="../../asset/css/login_register.css">
</head>
<body>
    <div class="container">
        <h1>Sign Up</h1>
        <form method="post" action="../controllers/register_process.php">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            
            <label for="email">Email:</label>
            <input type="email" name="email" required="" style="margin-bottom: 10px; height: 28px; width: 100%; border-radius: 1px;">
            
            <input type="submit" value="Sign Up" style="margin-top: 10px; width: 102%; height: 45px;">
        </form>
    </div>
</body>
</html>
