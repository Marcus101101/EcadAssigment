<?php
// Detect the current session
session_start();
// Include the Page Layout header
include("header.php"); 

// Reading inputs entered in previous page
$email = $_POST["email"];
$pwd = $_POST["password"];


// Include database connection (you need to create this file)
include("mysql_conn.php");
// To Do 1 (Practical 2): Validate login credentials with the database

// Prepare SQL query to retrieve the shopper record based on the email
$query = "SELECT * FROM shopper WHERE email = ?";

// Prepare statement to prevent SQL injection
if ($stmt = $conn->prepare($query)) {
    // Bind the email to the query
    $stmt->bind_param("s", $email);

    // Execute the query
    $stmt->execute();

    // Get the result of the query
    $result = $stmt->get_result();

    // Check if a record exists with the provided email
    if ($result->num_rows > 0) {
        // Fetch the shopper's details
        //$shopper = $result->fetch_assoc();
        $row1 = $result->fetch_array();
        // Verify the password (assuming the password is hashed in the database)
        if ($pwd == $row1['Password']) {
            // Save shopper's info in session variables
            $_SESSION["ShopperName"] = $row1['Name']; // Assuming 'Name' is a column in the shopper table
            $_SESSION["ShopperID"] = $row1['ShopperID']; // Assuming 'ShopperID' is a column for shopper ID
             // To Do 2 (Practical 4): Get active shopping cart
            // You may implement this part based on your shopping cart logic
            $shopperID = $_SESSION["ShopperID"];
            $qry = "SELECT ShopCartID FROM ShopCart WHERE ShopperID = ? AND OrderPlaced = 0";
            $cartStmt = $conn->prepare($qry);
            $cartStmt->bind_param("i", $shopperID);
            $cartStmt->execute();
            $cartResult = $cartStmt->get_result();

            // If an active cart is found, update session variables
            if ($cartResult->num_rows > 0) {
                $cartRow = $cartResult->fetch_assoc();
                $_SESSION["Cart"] = $cartRow["ShopCartID"];

                // Count the number of uncheckout items in the ShopCartItem table
                $shopCartID = $_SESSION["Cart"];
                $countQry = "SELECT COUNT(*) AS ItemCount FROM ShopCartItem WHERE ShopCartID = ?";
                $countStmt = $conn->prepare($countQry);
                $countStmt->bind_param("i", $shopCartID);
                $countStmt->execute();
                $countResult = $countStmt->get_result();
                $countRow = $countResult->fetch_assoc();
                
                // Update session variable "NumCartItem"
                $_SESSION["NumCartItem"] = $countRow["ItemCount"];
                $countStmt->close();
            } else {
                // If there is no active shopping cart, set NumCartItem to 0
                $_SESSION["NumCartItem"] = 0;
            }

            $cartStmt->close();
            // Redirect to home page

            // Redirect to home page
            header("Location: index.php");
            exit();
        } else {
            // Incorrect password
            echo "<h3 style='color:red'>Invalid Login Credentials</h3>";
        }
    } else {
        // No such email found
        echo "<h3 style='color:red'>Invalid Login Credentials</h3>";
    }
} else {
    // SQL error
    echo "<h3 style='color:red'>Error preparing SQL statement.</h3>";
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Include the Page Layout footer
include("footer.php");
?>