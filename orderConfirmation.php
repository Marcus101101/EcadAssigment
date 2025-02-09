<?php
session_start();
include 'mysql_conn.php';

if (!isset($_GET['order_id'])) {
    echo "Invalid order!";
    exit();
}

$order_id = $_GET['order_id'];
$shopperID = $_SESSION["ShopperID"];

// Get the user's active cart
$qry = "SELECT ShopCartID FROM ShopCart WHERE ShopperID=? AND OrderPlaced=0";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $shopperID);
$stmt->execute();
$result = $stmt->get_result();
$cartRow = $result->fetch_assoc();
$cartID = $cartRow['ShopCartID'] ?? null;
$stmt->close();

if (!$cartID) {
    echo "No active cart found!";
    exit();
}

//Retrieve cart items
$qry = "SELECT ProductID, Quantity FROM ShopCartItem WHERE ShopCartID=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $cartID);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();

// Insert order details into the database
$qry = "INSERT INTO OrderData (ShopCartID, ShipName, ShipAddress, ShipCountry, ShipPhone, ShipEmail, OrderStatus) 
        SELECT ?, Name, Address, Country, Phone, Email, 1 FROM Shopper WHERE ShopperID=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("ii", $cartID, $shopperID);
$stmt->execute();
$stmt->close();

// Update inventory
foreach ($items as $item) {
    $qry = "UPDATE Product SET Quantity = Quantity - ? WHERE ProductID=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("ii", $item['Quantity'], $item['ProductID']);
    $stmt->execute();
    $stmt->close();
}

// Mark cart as checked out
$qry = "UPDATE ShopCart SET OrderPlaced=1 WHERE ShopCartID=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $cartID);
$stmt->execute();
$stmt->close();

// Clear cart
$qry = "DELETE FROM ShopCartItem WHERE ShopCartID=(SELECT ShopCartID FROM Shopcart WHERE ShopperID=?)";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $shopperID);
$stmt->execute();
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Confirmation</title>
    </head>
    <body>
        <h2>Order Confirmation</h2>
        <p>Thank you for your order! Your Order ID is <strong><?= $order_id ?></strong></p>
        <p>Total Amount: SGD <?= number_format($total, 2) ?></p>
        <p>Delivery Mode: <?= ucfirst($delivery_mode) ?></p>
        <a href="index.php">Continue Shopping</a>
    </body>
</html>
