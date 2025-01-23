<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
include("mysql_conn.php"); // Include database connection file
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
        <label for="keywords" class="col-sm-3 col-form-label">Product Title:</label>
        <div class="col-sm-6">
            <input class="form-control" name="keywords" id="keywords" type="search" />
        </div>
        <div class="col-sm-3">
            <button type="submit">Search</button>
        </div>
    </div>  <!-- End of 2nd row -->
</form>

<?php
// The non-empty search keyword is sent to the server
if (isset($_GET["keywords"]) && ($_GET['keywords']) != "") {
    // Retrieve the keyword from the search form and sanitize it
    $keyword = ($_GET['keywords']);

    // Prepare SQL query to search for products by title
    $sql = "SELECT * FROM product WHERE ProductTitle LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $keyword . "%";
    $stmt->bind_param("s", $searchTerm);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are results
    if ($result->num_rows > 0) {
        // Start building the search results table
        echo "<table border='1' style='width:100%; margin-top:20px;'>
                <tr><th>Product Title</th></tr>";

        // Fetch and display each matching product as a row in the table
        while ($row = $result->fetch_assoc()) {
            $productTitle = htmlspecialchars($row['ProductTitle']); // Prevent XSS
            $productID = $row['ProductID']; // Assuming there's a ProductID column
            echo "<tr><td><a href='productDetails.php?pid=$productID'>$productTitle</a></td></tr>";
        }

        echo "</table>";
    } else {
        // If no results are found, display a message
        echo "<p>No products found for '$keyword'.</p>";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();

echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>
