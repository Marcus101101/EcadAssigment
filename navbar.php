<?php
//Display guest welcome message, Login and Registration links
//when shopper has yet to login,
$content2 = "<li class='nav-item'>
		     <a class='nav-link' href='register.php'>Sign Up</a></li>
			 <li class='nav-item'>
		     <a class='nav-link' href='login.php'>Login</a></li>";

if(isset($_SESSION["ShopperName"])) { 
    //To Do 1 (Practical 2) - 
    //Display a greeting message, Change Password and logout links 
    //after shopper has logged in.
    
	$content2 =  "<li class='nav-item'>
	              <li class='nav-item'>
	              <a class='nav-link' href='logout.php'>Logout</a></li>";
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Little Sprouts</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="promotion.php">Promotions</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="category.php">Product Categories</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="search.php">Product Search</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="shoppingCart.php">Shopping Cart</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="register.php">Sign Up</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="login.php">Login</a>
      </li>
    </ul>
  </div>
</nav>