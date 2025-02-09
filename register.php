<?php 
// Detect the current session
session_start(); 
// Include the Page Layout header
include("header.php"); 
?>
<!DOCTYPE html>
<html>
<head>
  <title>Membership Registration</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
  .registration-container {
      margin-top: 50px;
    }
  </style>
</head>
<body>
<div class="container registration-container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Membership Registration</h4>
        </div>
        <div class="card-body">
          <form name="register" action="addMember.php" method="post" onsubmit="return validateForm()">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>
              <div class="form-group col-md-6">
                <label for="email">Email Address:</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
            </div>
            <div class="form-group">
              <label for="address">Address:</label>
              <textarea class="form-control" id="address" name="address" rows="3"></textarea>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="country">Country:</label>
                <input type="text" class="form-control" id="country" name="country">
              </div>
              <div class="form-group col-md-6">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="form-group col-md-6">
                <label for="password2">Retype Password:</label>
                <input type="password" class="form-control" id="password2" name="password2" required>
              </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
function validateForm()
{
    // To Do 1 - Check if password matched
    if (document.register.password.value != document.register.password2.value) {
        alert("Password not matched!");
        return false; // cancel submission
    }
	// To Do 2 - Check if telephone number entered correctly
	//           Singapore telephone number consists of 8 digits,
	//           start with 6, 8 or 9
    if (document.register.phone.value != "") {
        var str = document.register.phone.value;
        if (str.length != 8) {
            alert("Please enter a 8-digit phone number.");
            return false; // cancel submission
        }
        else if(str.substr(0,1) != "6" &&
                str.substr(0,1) != "8" &&
                str.substr(0,1) != "9" ) {
            alert("Phone number in Singapore should start with 6, 8 or 9.");
            return false;
        }
    }
    return true;  // No error found
}
</script>

</body>
</html>
<?php 
// Include the Page Layout footer
include("footer.php"); 
?>