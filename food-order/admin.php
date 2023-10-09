
<?php
$host = "localhost"; 
$dbname = "foodOrder"; 
$username = "root"; 
$password = "root"; 

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// echo "Connected successfully";
?>

<?php
// Start session
session_start();

// Function to handle image upload and return the image URL
function uploadImage() {
    // Check if a file was uploaded
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['product_image'];

        // Get the file name and extension
        $fileExtension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);

        // Generate a unique file name to prevent overwriting
        $uniqueFileName = uniqid('product_image_') . '.' . $fileExtension;

        // Move the uploaded file to the current directory
        if (move_uploaded_file($uploadedFile['tmp_name'], $uniqueFileName)) {
            // Return the URL of the uploaded image (assuming it's in the same directory as the script)
            return $uniqueFileName;
        }
    }

    // If no file was uploaded or there was an error, return an empty string
    return '';
}

// CRUD operations for admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            // Handle product creation
            $product_name = $_POST['product_name'];
            $product_price = $_POST['product_price'];
            $product_image = uploadImage(); // Handle image upload and get the image URL

            $insert_query = "INSERT INTO products (product_name, product_price, product_image) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($insert_query);
            $stmt->bind_param("sds", $product_name, $product_price, $product_image);
            $stmt->execute();
            header("Location: ./admin.php");
        } elseif ($action === 'update') {
            // Handle product update
            $product_id = $_POST['product_id'];
            $product_name = $_POST['product_name'];
            $product_price = $_POST['product_price'];
            $product_image = uploadImage(); // Handle image upload and get the image URL

            $update_query = "UPDATE products SET product_name = ?, product_price = ?, product_image = ? WHERE product_id = ?";
            $stmt = $mysqli->prepare($update_query);
            $stmt->bind_param("sdsi", $product_name, $product_price, $product_image, $product_id);
            $stmt->execute();
            header("Location: ./admin.php");
        } elseif ($action === 'delete') {
            // Handle product deletion
            $product_id = $_POST['product_id'];

            $delete_query = "DELETE FROM products WHERE product_id = ?";
            $stmt = $mysqli->prepare($delete_query);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            header("Location: ./admin.php");
        }
    }
}

// Fetch all products
$select_all_query = "SELECT * FROM products";
$result = $mysqli->query($select_all_query);

// Fetch and store all products in $productData
$productData = [];
while ($row = $result->fetch_assoc()) {
    $productData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" type="text/css" href="./asset/css/admin.css"> -->
    <style>
        /* Custom CSS for symmetric alignment */
        .wide-form {
            max-width: 100%;
        }
        .product-card {
            max-width: 30rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./orderDetails.php">Order</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid mt-5">
        <!-- Product management forms -->
        <div class="row justify-content-center mt-5">
            <!-- Create product form -->
            <div class="col-md-4">
                <form method="post" enctype="multipart/form-data" class="wide-form">
                    <input type="hidden" name="action" value="create">
                    <h2 class="text-center">Create Product</h2>
                    <div class="form-group">
                        <label for="product_name">Product Name:</label>
                        <input type="text" name="product_name" id="product_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="product_price">Product Price:</label>
                        <input type="number" name="product_price" id="product_price" step="0.01" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="product_image">Product Image:</label>
                        <input type="file" name="product_image" id="product_image" accept="image/*" class="form-control" required>
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" value="Create Product" class="btn btn-primary">
                    </div>
                </form>
            </div>

            <!-- Update product form -->
            <div class="col-md-4">
                <form method="post" enctype="multipart/form-data" class="wide-form">
                    <input type="hidden" name="action" value="update">
                    <h2 class="text-center">Update Product</h2>
                    <div class="form-group">
                        <label for="update_product_id">Product ID:</label>
                        <input type="number" name="product_id" id="update_product_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="update_product_name">Product Name:</label>
                        <input type="text" name="product_name" id="update_product_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="update_product_price">Product Price:</label>
                        <input type="number" name="product_price" id="update_product_price" step="0.01" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="update_product_image">Product Image:</label>
                        <input type="file" name="product_image" id="update_product_image" accept="image/*" class="form-control">
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" value="Update Product" class="btn btn-primary">
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <form method="post" class="wide-form">
                    <input type="hidden" name="action" value="delete">
                    <h2 class="text-center">Delete Product</h2>
                    <div class="form-group">
                        <label for="delete_product_id">Product ID:</label>
                        <input type="number" name="product_id" id="delete_product_id" class="form-control" required>
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" value="Delete Product" class="btn btn-danger">
                    </div>
                </form>
            </div>
        </div>

        <!-- Product list -->
        <div class="row mt-5 justify-content-center">
            <?php
            // Display products with images, edit, and delete options
            foreach ($productData as $product) {
                echo "<div class='col-md-4 mb-3'>";
                echo "<div class='card product-card'>";
                if (!empty($product['product_image'])) {
                    echo "<img src='" . $product['product_image'] . "' alt='Product Image' class='card-img-top'>";
                }
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>Product ID: " . $product['product_id'] . "</h5>";
                echo "<p class='card-text'>Name: " . $product['product_name'] . "</p>";
                echo "<p class='card-text'>Price: Rs  " . $product['product_price'] . "</p>";
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
