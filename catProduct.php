﻿<?php
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a container, 60% width of viewport -->
<div style='width:60%; margin:auto;'>
    <!-- Display Page Header - Category's name is read
         from the query string passed from previous page -->
    <div class="row" style="padding:5px">
        <div class="col-12">
            <span class="page-title"><?php echo "$_GET[catName]"; ?></span>
        </div>
    </div>

    <?php
    // Include the PHP file that establishes database connection handle: $conn
    include_once("mysql_conn.php");

    // To Do:  Starting ....
    $cid=$_GET["cid"]; //Read category ID from query string
    // Form SQL to retrieve list of products asscociated to the Category ID
    $qry = "SELECT p.ProductID, p.ProductTitle, p.ProductImage, p.Price, p.Quantity,
                   p.offered, p.OfferStartDate, p.OfferEndDate, p.OfferedPrice
            FROM CatProduct cp INNER JOIN product p ON cp.ProductID=p.ProductID
            WHERE cp.CategoryID=?" ;
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $cid); // i - integer
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // Display each product in a row
    while ($row = $result->fetch_array()) {
        echo "<div class='row' style='padding:5px'>"; // Start a new row

        // Left column - display a text link showing the product's name,
        //				 display the selling price in red in a new paragraph
        $product = "productDetails.php?pid=$row[ProductID]";
        $formattedPrice = number_format($row["Price"], 2);
         $formattedOfferedPrice = number_format($row["OfferedPrice"], 2);
        echo "<div class='col-8'>"; // 67% of row width
        echo "<p><a href=$product>$row[ProductTitle]</a>";

          // On Offer Logic
          if ($row['offered'] == 1):
                 echo "<span style='color: red; font-weight: bold; margin-left: 5px; display: inline-block;'>On Offer</span>";
          endif;

          echo "</p>";
          if ($row['offered'] == 1):
                echo "Price: <strike>S$ $formattedPrice</strike>
                    <span style='font-weight: bold; color: red;'>
                     S$ $formattedOfferedPrice</span>";
           else:
                 echo "Price:<span style='font-weight: bold; color: red;'>
                     S$ $formattedPrice</span>";
          endif;

        echo "</div>";

        // Right column - display the product's image
        $img = "./Images/products/$row[ProductImage]";
        echo "<div class='col-4'>"; // 33% of row width
        echo "<img src='$img' />";
        echo "</div>";

        echo "</div>"; // End of a row
    }

    // To Do:  Ending ....

    $conn->close(); // Close database connnection
    echo "</div>"; // End of container
    include("footer.php"); // Include the Page Layout footer
    ?>