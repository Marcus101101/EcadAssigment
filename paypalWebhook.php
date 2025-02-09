<?php
include 'mysql_conn.php';

// Capture webhook data
$raw_data = file_get_contents("php://input");
$data = json_decode($raw_data, true);

if ($data['event_type'] == "PAYMENT.CAPTURE.COMPLETED") {
    $order_id = $data['resource']['id'];

    // Check if order already exists
    $stmt = $conn->prepare("SELECT OrderID FROM OrderData WHERE OrderID = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    if ($stmt->fetch()) {
        http_response_code(200);
        exit();
    }
    $stmt->close();

    // Mark order as paid
    $stmt = $conn->prepare("UPDATE OrderData SET OrderStatus=2 WHERE OrderID=?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $stmt->close();

    // Get ShopCartID
    $stmt = $conn->prepare("SELECT ShopCartID FROM OrderData WHERE OrderID=?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartRow = $result->fetch_assoc();
    $cartID = $cartRow['ShopCartID'];
    $stmt->close();

    // Deduct inventory
    $qry = "SELECT ProductID, Quantity FROM ShopCartItem WHERE ShopCartID=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $cartID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $updateQry = "UPDATE Product SET Quantity = Quantity - ? WHERE ProductID=?";
        $updateStmt = $conn->prepare($updateQry);
        $updateStmt->bind_param("ii", $row['Quantity'], $row['ProductID']);
        $updateStmt->execute();
        $updateStmt->close();
    }
    $stmt->close();

    http_response_code(200);
} else {
    http_response_code(400);
}

?>