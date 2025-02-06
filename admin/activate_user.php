<?php
include "../Common.php";
include "../data.php";
$data = new Data();
$common = new Common($data->get_path(), $data->get_amazon_api_endpoint());
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $common->is_user_an_admin()){ ?>
    <html>
    <head>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-Z91TWPR0DM"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-Z91TWPR0DM');
        </script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="../styles/style.css?version=<?php echo time(); ?>">
        <title>Activate New User</title>
        <link rel="icon" type="image/x-icon" href="../cricket.ico">
        <script src="../scripts.js?version=<?php echo time(); ?>"></script>
    </head>
    <body onload="fill_header();fill_footer();">
    <div id="header"></div>
    <div class="main_container">
        <div class="sub-title">Activate New User</div>
        <form action="activate_user.php" method="POST">
            <label class="label" for="phone">Phone Number:</label>
            <input type="number" placeholder="Phone Number" id="phone" name="phone" required>
            <div class="gap"></div>
            <label class="label" for="otp">OTP : </label>
            <input type="number" id="otp" name="otp" placeholder="OTP" required>
            <input type="submit" class="button" value="Activate User">
        </form>
        <p class="error" id="msg"><?php if(isset($_GET['msg'])) { echo $_GET['msg']; } ?></p>
    </div>
    <div class="separator"></div>
    <div id="footer"></div>
    </body>
    </html>
<?php } else if($_SERVER['REQUEST_METHOD'] === 'POST' && $common->is_user_an_admin() && $common->is_user_logged_in()) {
    $phone = $_POST['phone'];
    $otp = $_POST['otp'];
    $response = $common->activate_user($phone, $otp, $common->get_cookie($data->get_auth_cookie_name()));
    header('Location:activate_user.php?msg='.$response->status);
} else {
    header("Location: ".$data->get_path());
}