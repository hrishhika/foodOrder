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

// Process login form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check user credentials
    $select_query = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($select_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
    
            $_SESSION['user_id'] = $user["id"];
            $_SESSION['username'] = $user["username"];
            $_SESSION['message'] = "Login successful. Welcome, " . $user["username"] . "!";
            header("Location: ../../index.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password. Please try again.";
            header("Location: ../auth/login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid username or password. Please try again.";
        header("Location: ../auth/login.php");
        exit();
    }
}
$mysqli->close();
?>
