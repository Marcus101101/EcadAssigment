<?php
session_start(); // Detect the current session

//Read the data input from the previous page
$name = $_POST["name"];
$address = $_POST["address"];
$country = $_POST["country"];
$phone = $_POST["phone"];
$email = $_POST["email"];
$password = $_POST["password"];

//include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");

//Define the INSERT SQL statement
$qry = "INSERT INTO Shopper (Name, Address, Country, Phone, Email, Password) 
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare ($qry);
// "ssssssss" - 6 string parameters
$stmt->bind_param("ssssss", $name, $address, $country, $phone, $email, $password);

if ($stmt->execute()) { //SQL statement executed succesfully
    // retrieve the shopper ID assigned to the new shopper
    $qry = "SELECT LAST_INSERT_ID() AS ShopperID";
    $result = $conn->query($qry); //Execute the SQL and get the returned result
    while ($row = $result->fetch_array()) {
        $_SESSION["ShopperID"] = $row["ShopperID"];
    }

    //Succesful message and ShopperID
    $Message = "Registration succesful!<br />
                Your ShopperID is $_SESSION[ShopperID]<br />";
    //Save the shopper name in a session variable
    $_Session["ShopperName"] = $name;
}
else {
    $Message ="<h3 style='color:red'>Error in inserting record</h3>";
}

// Release the resource allocated for prepared statement
$stmt->close();
//close database connection
$conn->close();

//Display page layout header with updated session state and links
include("header.php");
//Display message
echo $Message;
// Display Page layout footer
include("footer.php");
?>