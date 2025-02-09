<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Little Sprouts Hub</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Link to compiled Bootstrap JavaScript downloaded -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- Site specific Cascading Stylesheet -->
    <link rel="stylesheet" href="css/site.css">
</head>
<body>
	<div class="container">
		<!-- 1st Row -->
		<div class="row" >	
            <div class="col-sm-12" >
				<a href="index.php"   style="display:inline-block; vertical-align:middle; margin-right:0px;">
            	<img src="Images/Logo/logo1.png" alt="Logo"
                     class="img-fluid" style="width: 25%; vertical-align:middle;"/></a>
               
               <div style="display:inline-block; vertical-align:middle; padding:0px; margin:0px;"><?php include("navbar.php"); ?> </div>
                 <p style="color:#F7BE81;  margin-left:0px;" >
                   <?php if(isset($_SESSION["ShopperName"])) { 
                
                     echo "Welcome <b>$_SESSION[ShopperName]</b>";  
                     if (isset($_SESSION["NumCartItem"])) {
                         echo ", " . $_SESSION["NumCartItem"] . " item(s) in shopping cart";
                     }
                    } else {
                         echo  "Welcome Guest";
                    }

                   ?>
                   </p>
			</div>	
		</div>
	</div>
</body>
</html>