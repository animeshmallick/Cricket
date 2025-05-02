<?php
session_start();
include "../Common.php";
$common = new Common();
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $common->is_user_an_admin()){ ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Admin - ActivateUser</title>
        <script src="../scripts/script.js?version=<?php echo time();?>"></script>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-BQY4C789R1"></script>
        <script>
            if(!window.location.hostname.includes("localhost")){
                window.dataLayer = window.dataLayer || [];
                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config', 'G-BQY4C789R1');
                gtag('set', {
                    'user_id': <?=$_SESSION['customer_id']?>,
                    'user_name': <?=$_SESSION['fname']?> + " " + <?=$_SESSION['lname']?>,
                    'user_type': <?=$_SESSION['user_account_type']?>,
                    'browser_details': navigator.userAgent
                })
                gtag('event', 'page_view', {
                    'page_title': document.title,
                    'page_path': window.location.pathname
                });
                let startTime = new Date().getTime();
                window.addEventListener('beforeunload', function () {
                    let timeSpent = Math.round((new Date().getTime() - startTime) / 1000);
                    gtag('event', 'time_on_page', {
                        'event_category': 'User Engagement',
                        'event_label': 'Page Duration',
                        'value': timeSpent
                    });
                });
            }
        </script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/x-icon" href="/images/ball.png">
        <link rel="stylesheet" type = "text/css" href ="../model_ui/header/style.css?version=<?php echo time();?>">
        <link rel="stylesheet" type = "text/css" href ="../model_ui/footer/style.css?version=<?php echo time();?>">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" type = "text/css" href ="style.css?version=<?php echo time();?>">
        <link rel="stylesheet" type = "text/css" href ="../styles/style.css?version=<?php echo time();?>">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
        <script src="../model_ui/header/script.js?version=<?php echo time();?>"></script>
        <script src="script.js?version=<?php echo time();?>"></script>
    </head>
    <body onload="fill_header('<?= $_SESSION['customer_id']?>');fill_footer();">
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
    $response = $common->activate_user($_SESSION['customer_id'], $phone, $otp);
    header('Location:activate_user.php?msg='.$response->status);
} else {
    header("Location: ../index.php");
}