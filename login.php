<?php
//Detect the current session
session_start();
//Include the Page Layout header
include("header.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Member Login</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa; /* Light background color */
    }
  .login-container {
      margin-top: 100px;
    }
  </style>
</head>
<body>

<div class="container login-container">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Member Login</h4>
        </div>
        <div class="card-body">
          <form action="checkLogin.php" method="post">
            <div class="form-group">
              <label for="email">Email Address:</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="password">Password:</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
            <div class="text-center mt-3">
              <a href="forgetPassword.php">Forget Password?</a>
            </div>
            <div class="text-center mt-2">
              Don't have an account? <a href="register.php">Sign Up</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
<?php
//Include the Page Layout footer
include("footer.php");
?>