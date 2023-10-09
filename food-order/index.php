<?php
session_start();

$host = "localhost"; 
$dbname = "foodOrder"; 
$username = "root"; 
$password = "root"; 

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Function to search for products
function searchProducts($keyword) {
    global $mysqli;
    $search_query = "SELECT * FROM products WHERE product_name LIKE ?";
    $stmt = $mysqli->prepare($search_query);
    $keyword = "%" . $keyword . "%";
    $stmt->bind_param("s", $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to fetch all products
function getAllProducts() {
    global $mysqli;
    $select_all_query = "SELECT * FROM products";
    $result = $mysqli->query($select_all_query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Add an item to the cart
if (isset($_POST['add_to_cart']) && isset($_SESSION['user_id'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $quantity = $_POST['quantity'];

    $item = [
        'product_id' => $product_id,
        'product_name' => $product_name,
        'product_price' => $product_price,
        'quantity' => $quantity,
    ];

    $_SESSION['cart'][] = $item;
}

// Remove an item from the cart
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    unset($_SESSION['cart'][$index]);
}

// Update the quantity of an item in the cart
if (isset($_POST['update_cart'])) {
    $item_indices = $_POST['item_index'];
    $quantities = $_POST['quantity'];

    foreach ($item_indices as $index => $item_index) {
        $quantity = $quantities[$index];
        if ($quantity > 0) {
            $_SESSION['cart'][$item_index]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$item_index]);
        }
    }
}

// Checkout and save cart data to the database
if (isset($_POST['checkout'])) {
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        
        // Insert data into the database
        $insert_query = "INSERT INTO cart (product_id, quantity) VALUES (?, ?)";
        $stmt = $mysqli->prepare($insert_query);
        $stmt->bind_param("ii", $product_id, $quantity);
        $stmt->execute();
    }

    // Clear the cart after checkout
    $_SESSION['cart'] = [];
}

// Fetch all products initially or after a search
if (isset($_GET['search_keyword']) && !empty($_GET['search_keyword'])) {
    $search_keyword = $_GET['search_keyword'];
    $productData = searchProducts($search_keyword);
} else {
    $productData = getAllProducts();
}

// Calculate the total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $item_total = $item['product_price'] * $item['quantity'];
    $total += $item_total;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online food delivery</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Add your custom CSS file if needed -->
    <link rel="stylesheet" type="text/css" href="./asset/css/index.css">
</head>
<body>
   
    <nav class="navbar navbar-expand-lg navbar-light bg-light" >
        <div class="container" id="nav">
            <!-- Your Logo -->
            <a class="navbar-brand" href="index.php"><img src="./asset/images/logo1.png" class="logo" alt="Your Logo"></a>

            <!-- Navbar Toggle Button for Mobile -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" id="product"href="#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./asset/includes/cart.php">Cart</a>
                    </li>
                    <?php
                    // Check if the user is logged in
                    if (isset($_SESSION['user_id'])) {
                        // User is logged in, display a logout button
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="./app/controllers/logout.php">Logout</a>';
                        echo '</li>';
                    } else {
                        // User is not logged in, display login and signup links
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="./app/auth/login.php">Login</a>';
                        echo '</li>';
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="./app/auth/register.php">Sign Up</a>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" id="search">
        <!-- Product search form -->
        <form method="get" class="mt-3">
            <div class="input-group mb-3">
                <input type="text" name="search_keyword" class="form-control" placeholder="Search Products" aria-label="Search Products" aria-describedby="search-button">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit" id="search-button">Search</button>
                </div>
            </div>
        </form>
        
        <!-- Product list -->
        <h2>Food menu</h2>
        <div class="row mt-4" id="products">
            <?php
            // Display products with images, names, prices, and add to cart buttons
            foreach ($productData as $product) {
                echo "<div class='col-md-4 mb-4'>";
                echo "<div class='card'>";
                if (!empty($product['product_image'])) {
                    echo "<img src='" . $product['product_image'] . "' alt='" . $product['product_name'] . "' class='card-img-top'>";
                }
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . $product['product_name'] . "</h5>";
                echo "<p class='card-text'>Rs " . $product['product_price'] . "</p>";
                if (isset($_SESSION['user_id'])) {
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='product_id' value='" . $product['product_id'] . "'>";
                    echo "<input type='hidden' name='product_name' value='" . $product['product_name'] . "'>";
                    echo "<input type='hidden' name='product_price' value='" . $product['product_price'] . "'>";
                    echo "<input type='number' name='quantity' value='1' min='1' class='form-control mb-2'>";
                    echo "<input type='hidden' name='item_index' value='$index'>"; // Add item index
                    echo "<button type='submit' name='add_to_cart' class='btn btn-primary btn-block' id='addtocart'>Add to Cart</button>";
                    echo "</form>";
                } else {
                    echo "<p>Login to add to cart.</p>";
                }
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
    
    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>