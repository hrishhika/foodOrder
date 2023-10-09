<?php
session_start();

$host = "localhost"; 
$dbname = "foodOrder"; // Replace with your database name
$username = "root"; // Replace with your database username
$password = "root"; // Replace with your database password

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Process registration form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash the password
    $email = $_POST["email"];

    // Insert user data into the database
    $insert_query = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($insert_query);
    $stmt->bind_param("sss", $username, $password, $email);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful. You can now log in.";
        header("Location: ../auth/login.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: ../auth/register.php");
        exit();
    }
}

$mysqli->close();
?>
