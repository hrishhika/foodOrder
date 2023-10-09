<?php
// Establish a database connection (replace with your actual database credentials)
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "foodOrder";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to retrieve data
$sql = "SELECT products.product_name, products.product_price, users.username FROM cart 
        JOIN products ON cart.product_id = products.product_id 
        JOIN users ON cart.id = users.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Information</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Cart Information</h2>

<table>
    <tr>
        <th>Product Name</th>
        <th>Product Price</th>
        <th>Username</th>
    </tr>
    <?php
    // Output data from the query
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['product_name']}</td>
                    <td>{$row['product_price']}</td>
                    <td>{$row['username']}</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No results found</td></tr>";
    }
    ?>
</table>

<?php
// Close the database connection
$conn->close();
?>

</body>
</html>
