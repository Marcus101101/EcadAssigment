<?php
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a container, 60% width of viewport -->
<div style='width:60%; margin:auto;'>
    <!-- Display Page Header -->
    <div class="row" style="padding:5px">
        <div class="col-12">
            <span class="page-title">Current Promotions</span>
        </div>
    </div>

    <?php
    // Include the PHP file that establishes database connection handle: $conn
    include_once("mysql_conn.php");

    // Form SQL to retrieve list of products currently on offer, ordered alphabetically
    $qry = "SELECT p.ProductID, p.ProductTitle, p.ProductImage, p.Price, p.OfferedPrice
            FROM product p
            WHERE p.offered = 1 AND p.OfferStartDate <= CURDATE() AND p.OfferEndDate >= CURDATE()
            ORDER BY p.ProductTitle ASC"; // Added ORDER BY clause
    $stmt = $conn->prepare($qry);
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
        $formattedOfferPrice = number_format($row["OfferedPrice"], 2);

        echo "<div class='col-8'>"; // 67% of row width
        echo "<p><a href=$product>$row[ProductTitle]</a></p>";
        echo "Price: <strike>S$ $formattedPrice</strike> <span style='font-weight: bold; color: red;'>S$ $formattedOfferPrice</span>";
        echo "</div>";

        // Right column - display the product's image
        $img = "./Images/products/$row[ProductImage]";
        echo "<div class='col-4'>"; // 33% of row width
        echo "<img src='$img' />";
        echo "</div>";

        echo "</div>"; // End of a row
    }
    $conn->close(); // Close database connnection
    ?>
</div> <!-- End of container -->

<?php
include("footer.php"); // Include the Page Layout footer
?>