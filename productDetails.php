<?php
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a container, 90% width of viewport -->
<div style='width:90%; margin:auto;'>

<?php
$pid=$_GET["pid"]; // Read Product ID from query string
if(!isset($pid)){
   die("Error: Missing product id");
}
// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");
$qry = "SELECT *, Quantity, offered, OfferStartDate, OfferEndDate, OfferedPrice,Price from product where ProductID=?"; // Changed this to include Quantity
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $pid); 	// "i" - integer
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// To Do 1:  Display Product information. Starting ....
while ($row = $result->fetch_array()) { // start while loop {
   
    echo "<div class='row'>"; // Start a new row

    // Left column - display the product's image
        $img = "./Images/products/$row[ProductImage]";
    echo "<div class='col-sm-3' style='vertical-align:top; padding:5px;'>";
    echo "<p><img src=$img /></p>";
     echo "</div>";


   // Right column - display the product's name, description, and prices
      echo "<div class='col-sm-9' style='padding:5px; display:inline-block; vertical-align:top;'>";
    echo "<h1>$row[ProductTitle]</h1>";
         echo "<p>$row[ProductDesc]</p>";

    // Right column - display the product's specification,
    $qry = "SELECT s.SpecName, ps.SpecVal FROM productspec ps
            INNER JOIN specification s ON ps.SpecID=s.SpecID
            WHERE ps.ProductID=?
            ORDER BY ps.priority";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $pid); // "i" - integer
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();
    while ($row2 = $result2->fetch_array()) {
        echo $row2["SpecName"].": ".$row2["SpecVal"]."<br />";
    }
    // Right column - display the product's price
       $formattedPrice = number_format($row["Price"], 2);
        $offerStartDate = strtotime($row['OfferStartDate']);
      $offerEndDate = strtotime($row['OfferEndDate']);
      $today = strtotime(date('Y-m-d'));

    echo  "<p>";
        if ($row['offered'] == 1 && $offerStartDate <= $today && $offerEndDate >= $today) { // start if
             echo "<span class='on-offer'>On Offer</span>";
            echo "<p>Price Before Offer: <strike>S$" . $formattedPrice . "</strike></p>";
            echo "<p>Price: <span class='price' style='color: red; font-weight:bold;'>S$" . number_format($row['OfferedPrice'], 2) . "</span></p>";

         } else { // else statement for offer check
             echo "Price: <span class='price'>S$" . $formattedPrice . "</span>";

        } // end if for offer check
    echo  "</p>";

    // Start of out of stock check
    if($row['Quantity'] > 0) { // start if
        // To Do 2:  Create a Form for adding the product to shopping cart. Starting ....
        ?>
        <form action='cartFunctions.php' method='post'>
            <input type='hidden' name='action' value='add' />
            <input type='hidden' name='product_id' value='<?php echo $pid; ?>' />
            Quantity: <input type='number' name='quantity' value='1'
                            min='1' max='10' style='width:40px' required />
            <button type='submit' class="search-button">Add to Cart</button>
        </form>
        <?php
    // To Do 2:  Ending ....
     } else { // else statement for out of stock check
         ?>
        <span class='out-of-stock'>Out of Stock</span>
        <button disabled class="search-button">Add to Cart</button>
     <?php
      } // end if for out of stock check
    echo "</div>"; // End of right column
       
 echo "</div>"; // End of Row
} // end of while loop


$conn->close(); // Close database connnection
echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>