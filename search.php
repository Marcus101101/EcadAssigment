<?php
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
include("mysql_conn.php"); // Include database connection file
date_default_timezone_set('Asia/Singapore'); //set the timezone
?>

<!-- HTML Form to collect search keyword and submit it to the same page on server -->
<div style="width:80%; margin:auto;"> <!-- Container -->
<form name="frmSearch" method="get" action="">
    <div class="mb-3 row"> <!-- 1st row -->
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Product Search</span>
        </div>
    </div> <!-- End of 1st row -->
    <div class="mb-3 row"> <!-- 2nd row -->
        <label for="keywords" class="col-sm-3 col-form-label">Product Title or Description:</label>
        <div class="col-sm-6">
            <input class="form-control" name="keywords" id="keywords" type="search" value="<?php echo isset($_GET['keywords']) ? htmlspecialchars($_GET['keywords']) : ''; ?>" />
        </div>
      
    </div>  <!-- End of 2nd row -->
    <div class="mb-3 row"> <!-- 3rd row -->
    <label for="min_price" class="col-sm-3 col-form-label">Min Price:</label>
        <div class="col-sm-3">
            <input class="form-control" name="min_price" id="min_price" type="number" step="0.01" style="max-width: 80px;" min="0" max="99999" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>"/>
        </div>
           <label for="max_price" class="col-sm-1 col-form-label">Max Price:</label>
        <div class="col-sm-3">
            <input class="form-control" name="max_price" id="max_price" type="number" step="0.01" style="max-width: 80px;" min="0" max="99999" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>"/>
        </div>
    </div>  <!-- End of 3rd row -->
     <div class="mb-3 row"> <!-- 4th row -->
         <label for="on_offer" class="col-sm-3 col-form-label">On Offer:</label>
          <div class="col-sm-6">
             <input type="checkbox" name="on_offer" id="on_offer" value="1" <?php if(isset($_GET['on_offer']) && $_GET['on_offer'] == 1) echo 'checked'; ?> />

          </div>
      <div class="col-sm-3">
            <button type="submit"  class="search-button">Search</button>
      </div>
    </div>  <!-- End of 4th row -->
</form>

<?php
// The non-empty search keyword is sent to the server
if (isset($_GET["keywords"]) || isset($_GET["min_price"]) || isset($_GET["max_price"]) || isset($_GET["on_offer"])) {
    // Retrieve the keyword from the search form and sanitize it
    $keyword = $_GET['keywords'] ?? null;
    $minPrice = $_GET['min_price'] ?? null;
    $maxPrice = $_GET['max_price'] ?? null;
    $onOffer = $_GET['on_offer'] ?? null;

    $sql = "SELECT *, (CASE WHEN offered = 1 AND OfferStartDate <= CURDATE() AND OfferEndDate >= CURDATE() THEN OfferedPrice ELSE Price END ) AS displayPrice FROM product WHERE 1=1 ";  // Start with a 1=1 that always evaluates to true, for easier addition of conditional clauses.
    $params = [];
    $types = '';

    // Add conditions for Product Title or Description
     if($keyword !== null && $keyword !== "") {
          $sql .= " AND (ProductTitle LIKE ? OR ProductDesc LIKE ?)";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
            $types .= 'ss';
      }
     // Add conditions for Price Range
    if (($minPrice !== null && $minPrice !== "") && ($maxPrice !== null && $maxPrice !== "")) {
          $sql .= " AND (CASE WHEN offered = 1 AND OfferStartDate <= CURDATE() AND OfferEndDate >= CURDATE() THEN OfferedPrice ELSE Price END ) >= ? AND (CASE WHEN offered = 1 AND OfferStartDate <= CURDATE() AND OfferEndDate >= CURDATE() THEN OfferedPrice ELSE Price END )  <= ?";
          $params[] = $minPrice;
           $params[] = $maxPrice;
            $types .= 'dd';
      }
     else if ($minPrice !== null && $minPrice !== "") {
           $sql .= " AND (CASE WHEN offered = 1 AND OfferStartDate <= CURDATE() AND OfferEndDate >= CURDATE() THEN OfferedPrice ELSE Price END ) >= ?";
         $params[] = $minPrice;
           $types .= 'd';
      } else if ($maxPrice !== null && $maxPrice !== ""){
         $sql .= " AND (CASE WHEN offered = 1 AND OfferStartDate <= CURDATE() AND OfferEndDate >= CURDATE() THEN OfferedPrice ELSE Price END ) <= ?";
           $params[] = $maxPrice;
           $types .= 'd';
      }

     // Add conditions for "On Offer"
     if ($onOffer == 1) {
         $sql .= " AND offered = 1 AND UNIX_TIMESTAMP(OfferStartDate) <= UNIX_TIMESTAMP(CURDATE()) AND UNIX_TIMESTAMP(OfferEndDate) >= UNIX_TIMESTAMP(CURDATE()) AND (CASE WHEN offered = 1 AND OfferStartDate <= CURDATE() AND OfferEndDate >= CURDATE() THEN OfferedPrice ELSE Price END) <= ?";
        $params[] = $maxPrice;
          $types .= 'd';
      }
     // Prepare and Execute the query
    $stmt = $conn->prepare($sql);

    if (count($params) > 0) {
      $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are results
    if ($result->num_rows > 0) {
        // Start building the search results table
        echo "<table border='1' style='width:100%; margin-top:20px;'>
                <tr><th>Product Title</th> <th>Display Price</th></tr>";

        // Fetch and display each matching product as a row in the table
        while ($row = $result->fetch_assoc()) {
            $productTitle = htmlspecialchars($row['ProductTitle']); // Prevent XSS
            $productID = $row['ProductID']; // Assuming there's a ProductID column
             $displayPrice = htmlspecialchars($row['displayPrice']);
           echo "<tr><td><a href='productDetails.php?pid=$productID'>$productTitle</a></td><td>$displayPrice</td></tr>";
        }

        echo "</table>";
    } else {
        // If no results are found, display a message
        echo "<p>No products found matching your criteria.</p>";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();

echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>