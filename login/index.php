<?php
include "../Common.php";
$common = new Common();
if($common->is_user_logged_in()){
    $common->redirect_to('Cricket/');
}else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BQY4C789R1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-BQY4C789R1');
    </script>
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
    <div class="separator"></div>
    <form id="loginForm" onsubmit="return validateForm(event)">
        <input class="input-field" type="tel" id="phone" name="phone" placeholder="Phone Number" required pattern="[0-9]{10}" title="Enter a 10-digit phone number">
        <input class="input-field" type="password" id="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Login</button>
    </form>
    <p style="color:red;font-size: 1.5rem" id="message"></p>
    <div class="separator"></div>
    <div class="register-link">
        <p style="color:indianred;font-size: 1.3rem;margin-top: 2rem">Don't have an account? <a href="#" class="link">Register</a></p>
    </div>
</div>
</body>
</html>
<?php } ?>