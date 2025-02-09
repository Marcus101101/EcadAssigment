<?php
session_start();
include 'mysql_conn.php';

// Ensure user is logged in
if (!isset($_SESSION["ShopperID"])) {
    header("Location: login.php");
    exit();
}

$shopperID = $_SESSION["ShopperID"];

// Get cart items from session
$qry = "SELECT ShopCartID FROM ShopCart WHERE ShopperID=? AND OrderPlaced=0";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $shopperID);
$stmt->execute();
$result = $stmt->get_result();
$cartRow = $result->fetch_assoc();
$cartID = $cartRow['ShopCartID'] ?? null;
$stmt->close();

if (!$cartID) {
    echo "<h2>Your cart is empty!</h2><a href='index.php'>Continue Shopping</a>";
    exit();
}

// Retrieve cart items
$qry = "SELECT sci.ProductID, sci.Name, sci.Price, sci.Quantity, p.Quantity AS Stock 
        FROM ShopCartItem sci
        JOIN Product p ON sci.ProductID = p.ProductID
        WHERE sci.ShopCartID=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $cartID);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total price
$subtotal = 0;
$items = [];
while ($row = $result->fetch_assoc()) {
    if ($row['Quantity'] > $row['stock_quantity']) {
        echo "<p>Insufficient stock for {$row['Name']}!</p>";
        exit();
    }
    $items[] = $row;
    $subtotal += $row['Price'] * $row['Quantity'];
}
$stmt->close();

// Get GST rate from database
$gstQry = "SELECT TaxRate FROM GST ORDER BY EffectiveDate DESC LIMIT 1";
$gstResult = $conn->query($gstQry);
$gstRow = $gstResult->fetch_assoc();
$gstRate = $gstRow['TaxRate'] / 100;

$delivery_mode = $_POST['delivery_mode'] ?? 'normal';
$delivery_charge = ($delivery_mode == 'express') ? 10.00 : 5.00;
$gst = $subtotal * $gstRate;
$total = $subtotal + $gst + $delivery_charge;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID&currency=SGD"></script>
</head>
<body>
    <h2>Checkout</h2>
    <ul>
        <?php foreach ($items as $item): ?>
            <li><?= $item['Name'] ?> - SGD <?= number_format($item['Price'], 2) ?> x <?= $item['Quantity'] ?></li>
        <?php endforeach; ?>
    </ul>
    <p>Subtotal: SGD <?= number_format($subtotal, 2) ?></p>
    <p>GST (<?= $gstRate * 100 ?>%): SGD <?= number_format($gst, 2) ?></p>
    <p>Delivery Charge: SGD <?= number_format($delivery_charge, 2) ?></p>
    <h3>Total Payable: SGD <?= number_format($total, 2) ?></h3>

    <div id="paypal-button-container"></div>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: { value: '<?= number_format($total, 2) ?>' }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    window.location.href = "order_confirmation.php?order_id=" + details.id;
                });
            }
        }).render('#paypal-button-container');
    </script>   
</body>
</html>