<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// Fetch the user's ID from the session
$userId = $_SESSION["id"];

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

include("partials/connect.php");

// Initialize variables for form field values
$productName = $productPrice = $productDescription = "";

// Check if the form for uploading products has been submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Define the allowed file types
    $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg'];

    // Define the upload directory where product images will be stored
    $uploadDirectory = "./uploads/"; // Create a 'uploads' directory in your project

    // Check if the directory exists, if not, create it
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // Process the uploaded product image
    $productImage = $_FILES["productImage"];
    $productName = $_POST["productName"];
    $productPrice = $_POST["productPrice"];
    $productDescription = $_POST["productDescription"];

    // Check for file upload errors
    if ($productImage["error"] !== UPLOAD_ERR_OK) {
        $errorMessage = "File upload error: " . $productImage["error"];
    } else {
        // Check if the uploaded file type is allowed
        if (in_array($productImage['type'], $allowedFileTypes)) {
            // Generate a unique file name to avoid overwriting
            $uniqueFilename = uniqid() . "_" . $productImage["name"];

            // Move the uploaded file to the upload directory
            $targetFilePath = $uploadDirectory . $uniqueFilename;

            if (move_uploaded_file($productImage["tmp_name"], $targetFilePath)) {
                // Insert product details into the 'tb_products' table (or your table name)
                $userId = $_SESSION["id"];
                $insertQuery = "INSERT INTO `tb_products` (`id`, `user_id`, `name`, `img`, `price`, `description`)
                VALUES (null, '$userId', '$productName', '$targetFilePath', '$productPrice', '$productDescription')";


                if (mysqli_query($con, $insertQuery)) {
                    // Product uploaded successfully
                    $successMessage = "Product uploaded successfully.";
                    // Clear the form field values after successful upload
                    $productName = $productPrice = $productDescription = "";
                } else {
                    // Handle database insert error
                    $errorMessage = "Error: " . mysqli_error($con);
                }
            } else {
                // Handle file upload error
                $errorMessage = "Error moving uploaded file to target directory.";
            }
        } else {
            // File type not allowed
            $errorMessage = "File type not allowed. Please upload a jpeg, png, or jpg image.";
        }
    }
}

?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - <?= $_SESSION["username"] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <style>
        /* Add custom CSS styles here to further customize your welcome page */
        .welcome-container {
            text-align: center;
            padding: 20px;
        }

        .product-upload-form {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php require("partials/nav.php") ?>
    <div class="container welcome-container">
        <h1>Welcome, <?= $_SESSION["username"] ?>!</h1>
        <p>Upload and manage your products below:</p>

        <!-- Product Upload Form -->
        <form action="welcome.php" method="post" enctype="multipart/form-data" class="product-upload-form">
            <div class="mb-3">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="productName" required value="<?php echo $productName; ?>">
            </div>
            <div class="mb-3">
                <label for="productImage" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="productImage" name="productImage" required>
            </div>
            <div class="mb-3">
                <label for="productPrice" class="form-label">Product Price</label>
                <input type="number" class="form-control" id="productPrice" name="productPrice" required value="<?php echo $productPrice; ?>">
            </div>
            <div class="mb-3">
                <label for="productDescription" class="form-label">Product Description</label>
                <textarea class="form-control" id="productDescription" name="productDescription" rows="3" required><?php echo $productDescription; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Upload Product</button>
        </form>


        <?php
        if (isset($errorMessage)) {
            echo '<div class="alert alert-danger mt-3">' . $errorMessage . '</div>';
        }
        ?>
    </div>
    <!-- Rest of your HTML code before the closing </div> tag of .welcome-container -->

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Product Uploaded Successfully</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Your product has been successfully added.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- ... (the rest of your body content) ... -->
    <!-- JavaScript to trigger the success modal -->
    <script>
        <?php
        if (isset($successMessage)) {
            echo 'document.addEventListener("DOMContentLoaded", function() {
            var successModal = document.getElementById("successModal");
            var modal = new bootstrap.Modal(successModal);
            modal.show();
        });';
        }
        ?>
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>