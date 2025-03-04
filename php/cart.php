<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "sk_online_store"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch cart items
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $cartItems = [];
    $result = $conn->query("SELECT * FROM cart_items");

    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }

    echo json_encode(["cartItems" => $cartItems]);
    exit;
}



if (isset($_POST['update_item'])) {
    $item_id = $_POST['item_id'];
    $new_quantity = $_POST['quantity'];

    $updateQuery = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $updateQuery->bind_param("ii", $new_quantity, $item_id);
    $updateQuery->execute();
    echo "Quantity updated!";
}


// Add or update product in cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image = $_POST['image'];

    // Check if product already exists in cart
    $checkCart = $conn->prepare("SELECT quantity FROM cart_items WHERE product_id = ?");
    $checkCart->bind_param("i", $product_id);
    $checkCart->execute();
    $checkCart->store_result();

    if ($checkCart->num_rows > 0) {
        // If product exists, update quantity
        $checkCart->bind_result($existingQuantity);
        $checkCart->fetch();
        $newQuantity = $existingQuantity + $quantity;

        $updateCart = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE product_id = ?");
        $updateCart->bind_param("ii", $newQuantity, $product_id);
        $updateCart->execute();
        echo "Cart updated!";
    } else {
        // Insert new product
        $stmt = $conn->prepare("INSERT INTO cart_items (product_id, name, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdis", $product_id, $name, $price, $quantity, $image);
        $stmt->execute();
        echo "Product added to cart!";
    }
}

// Remove item from cart
if (isset($_POST['remove_item'])) {
    $cart_item_id = $_POST['remove_item'];
    $conn->query("DELETE FROM cart_items WHERE id = $cart_item_id");
    echo "Item removed from cart!";
}



$conn->close();

?>
