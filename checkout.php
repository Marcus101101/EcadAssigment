<?php
session_start();
include 'header.php';
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
$insufficientStock = false; // Flag to check if any item is out of stock
while ($row = $result->fetch_assoc()) {
    if ($row['Quantity'] > $row['Stock']) {
        $insufficientStock = true; // Set flag if stock is insufficient
        echo "<div class='alert alert-danger text-center mt-4'>⚠️ Insufficient stock for <strong>{$row['Name']}</strong>! (Only {$row['Stock']} left in stock)</div>";
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

// Default delivery charge
$delivery_charge = 5.00; // Default to "Normal Delivery"
$gst = $subtotal * $gstRate;
$total = $subtotal + $gst + $delivery_charge;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AeWzOvUz2lcRr8qobH4KwGp3fnnKn31YFX9dx3vE6UZlMsCkh-Qi4-4BIsd6IagfXxAD-BGu97p0Z7HQ&currency=SGD"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.checkout.css">
</head>
<body>
    <div class="container mt-5">
        <div class="checkout-container">
            <h2 class="checkout-header">Secure Checkout</h2>

            <h5>Order Summary</h5>
            <ul class="list-group mb-3">
                <?php foreach ($items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= $item['Name'] ?>
                        <span class="text-muted">SGD <?= number_format($item['Price'], 2) ?> x <?= $item['Quantity'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="order-summary">
                <p><strong>Subtotal:</strong> SGD <?= number_format($subtotal, 2) ?></p>
                <p><strong>GST (<?= $gstRate * 100 ?>%):</strong> SGD <?= number_format($gst, 2) ?></p>

                <!-- Delivery Mode Selection -->
                <label for="delivery_mode"><strong>Select Delivery Mode:</strong></label>
                <select id="delivery_mode" class="form-select mb-3">
                    <option value="normal" selected>Normal Delivery (SGD 5.00, 2 working days)</option>
                    <option value="express">Express Delivery (SGD 10.00, 24 hours)</option>
                </select>

                <p><strong>Delivery Charge:</strong> SGD <span id="delivery_charge"><?= number_format($delivery_charge, 2) ?></span></p>
                <hr>
                <h4 class="total-amount">Total Payable: SGD <span id="total_price"><?= number_format($total, 2) ?></span></h4>
            </div>

            <!-- PayPal Button Container -->
            <div class="pay-button paypal-btn-container">
                <div id="paypal-button-container"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
        const insufficientStock = <?= $insufficientStock ? 'true' : 'false' ?>;
        if (insufficientStock) {
            document.getElementById("paypal-button-container").innerHTML = 
                "<div class='alert alert-warning text-center'>⚠️ Please update your cart before checking out.</div>";
        } else {
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
        }
    });
    </script>
</body>
</html>