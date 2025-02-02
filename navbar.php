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

<nav class="navbar navbar-expand-md"  style="display:inline-block; vertical-align:middle;"  >
    <div class="container-fluid" >
        <!-- Collapsible part of the navbar -->
         <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <!-- Left Justifed menu items -->
             <ul class="navbar-nav me-auto"   >
                <li class="nav-item" >
                    <a class="nav-link" href="promotion.php">Promotions</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link" href="category.php">Product Categories</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link" href="search.php">Product Search</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link" href="shoppingCart.php">Shopping Cart</a>
                </li>
                
            </ul>
          </div>

           <!-- Right Justified menu items -->
            <ul class="navbar-nav ms-auto" >
                    <?php echo $content2; ?>
            </ul>
    </div>
</nav>