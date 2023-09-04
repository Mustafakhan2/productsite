<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if it's a DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Read and decode the JSON data sent in the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if the 'deleteProduct' key exists in the data
    if (isset($data['deleteProduct'])) {
        // Include your database connection script
        include("partials/connect.php");

        // Sanitize and get the product ID to delete
        $productId = intval($data['deleteProduct']);

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
        } else {
            // Handle product not found error
            $response = array("success" => false, "message" => "Product not found.");
            echo json_encode($response);
            exit;
        }
    }
} else {
    // Handle invalid request method
    $response = array("success" => false, "message" => "Invalid request method.");
    echo json_encode($response);
    exit;
}
