<?php
include "../Common.php";
$common = new Common();
if($common->is_user_logged_in()){
    $common->redirect_to('CricketT20/');
}else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type = "text/css" href ="style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="../styles/style.css?version=<?php echo time();?>">
    <script src="script.js"></script>
    <script src="../scripts/script.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Page</title>

</head>
<body>

<div class="container">
    <h2>Welcome Back</h2>
    <form id="loginForm" onsubmit="return validateForm(event)">
        <input class="input-field" type="tel" id="phone" name="phone" placeholder="Phone Number" required pattern="[0-9]{10}" title="Enter a 10-digit phone number">
        <input class="input-field" type="password" id="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Login</button>
    </form>
    <p style ="color:red" id="message"></p>
    <a href="#" class="link">Forgot Password?</a>
    <div class="register-link">
        <p style="color:indianred;">Don't have an account? <a href="#" class="link">Register</a></p>
    </div>
</div>
</body>
</html>
<?php } ?>