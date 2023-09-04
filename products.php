<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

include("partials/connect.php");

// Initialize variables for form field values and product list
$productName = $productPrice = $productDescription = "";
$productList = []; // An array to store fetched products

// Check if the form for uploading products has been submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the uploaded product image and insert it into the database (your existing code)
    // ...

    // After inserting the product, you may want to refresh the product list
    // Fetch and display the updated product list
}

// Check for database connection errors
if (mysqli_connect_errno()) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch and display user's uploaded products
$userId = $_SESSION["id"]; // Assuming you store the user's ID in the session

$query = "SELECT * FROM `tb_products` WHERE user_id = $userId";

$result = mysqli_query($con, $query);

// Check for query execution errors
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Handle product deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["deleteProduct"])) {
    $productId = $_POST["deleteProduct"];

    // Fetch the product's image file path from the database
    $query = "SELECT img FROM `tb_products` WHERE id = $productId";
    $result = mysqli_query($con, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $imageFilePath = $row["img"];

        // Delete the product from the database
        $deleteQuery = "DELETE FROM `tb_products` WHERE id = $productId";
        if (mysqli_query($con, $deleteQuery)) {
            // Product deleted from the database, now delete the image file
            if (unlink($imageFilePath)) {
                // Image file deleted successfully
                $response = array("success" => true, "message" => "Product deleted successfully.");
                echo json_encode($response);
                exit;
            } else {
                // Handle image file deletion error
                $response = array("success" => false, "message" => "Error deleting the image file.");
                echo json_encode($response);
                exit;
            }
        } else {
            // Handle database deletion error
            $response = array("success" => false, "message" => "Error deleting the product from the database.");
            echo json_encode($response);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <style>
        /* Add custom CSS styles for product display */
        .product-card {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            background-color: #fff;
        }

        .product-card img {
            max-width: 100%;
            max-height: 200px;
            /* Set the maximum height for the images */
            height: auto;
        }

        .product-description {
            font-size: 16px;
        }
    </style>
</head>

<body>
    <?php require("partials/nav.php") ?>
    <div class="container welcome-container">
        <h1>Welcome, <?= $_SESSION["username"] ?>!</h1>
        <p>The products you have added are below</p>

        <!-- Display Uploaded Products -->
        <div class="row mt-5">
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="col-md-4 mb-4">';
                echo '<div class="product-card">';
                echo '<img src="' . $row["img"] . '" class="card-img-top" alt="Product Image">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $row["name"] . '</h5>';
                echo '<p class="product-description">' . $row["description"] . '</p>';
                echo '<p class="product-description">Price: $' . $row["price"] . '</p>';

                // Add a delete button for each product
                echo '<form method="POST" class="delete-form">';
                echo '<input type="hidden" name="deleteProduct" value="' . $row["id"] . '">';
                echo '<button type="button" class="btn btn-danger delete-button">Delete</button>';
                echo '</form>';

                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <script>
        // Add JavaScript for handling product deletion without page refresh
        document.addEventListener("DOMContentLoaded", function() {
            var deleteButtons = document.querySelectorAll(".delete-button");

            deleteButtons.forEach(function(button) {
                button.addEventListener("click", function() {
                    var productId = button.closest(".delete-form").querySelector('input[name="deleteProduct"]').value;
                    if (confirm("Are you sure you want to delete this product?")) {
                        // Create a DELETE request using fetch
                        fetch("your_delete_product_script.php", {
                                method: "DELETE",
                                body: JSON.stringify({
                                    deleteProduct: productId
                                }),
                                headers: {
                                    "Content-Type": "application/json",
                                },
                            })
                            .then(function(response) {
                                if (!response.ok) {
                                    throw new Error("Network response was not ok");
                                }
                                return response.json();
                            })
                            .then(function(data) {
                                if (data.success) {
                                    // Product deleted successfully, remove it from the page
                                    button.closest(".col-md-4").remove();
                                } else {
                                    alert("Error deleting product: " + data.message);
                                }
                            })
                            .catch(function(error) {
                                console.error("There was a problem with the fetch operation:", error);
                            });
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

</body>

</html>