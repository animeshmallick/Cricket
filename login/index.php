<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
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
                'user_id': "-1",
                'user_name': "Unknown User",
                'user_type': "unknown",
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type = "text/css" href ="style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="../styles/style.css?version=<?php echo time();?>">
    <script src="script.js?version=<?php echo time();?>"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="/images/ball.png">
</head>
<?php
session_start();
include "../Common.php";
$common = new Common();
if($common->is_user_logged_in()){
    $common->redirect_to('Cricket/');
}else if($_SERVER["REQUEST_METHOD"] == "POST"){
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $response = $common->validate_login($phone, $password);
    if(!isset($response->error)){
        if($response->status == 'pending'){ ?>
            <body>
            <div class="container">
                <h2>Attention Required</h2>
                <div class="separator"></div>
                <p style="color:red;font-size: 1.5rem" id="message">
                    To activate your account send a Whatsapp/Text Message as "ACTIVATE <?php echo $response->ref_id; ?>" to +91 (9153217256) from your registered mobile number (<?php echo $response->phone; ?>).
                </p>
                <div class="register-link">
                    <button class="button">
                        <a class="link" href="https://wa.me/919153217256?text=ACTIVATE%20<?= $response->ref_id ?>" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp" style="font-size: 1.5rem; color: green;"></i> Send Message
                        </a>
                    </button>
                </div>
                <div class="separator"></div>
                <div class="register-link">
                    <button class="button"><a href="../index.php" class="link">Logout/Home</a></button>
                </div>
            </div>
            </body>
        <?php }else {
            if (rand(10,100) % 5 == 0)
                $common->setCookie("show_tour", 'yes');
            $_SESSION['ref_id'] = $response->ref_id;
            $_SESSION['user_account_type'] = strtoupper($response->type);
            $_SESSION['fname'] = $response->fname;
            $_SESSION['lname'] = $response->lname;
            $_SESSION['session_id'] = $response->session;
            $common->redirect_to('Cricket/');
        }
    }else{
        $common->redirect_to('Cricket/login/index.php?msg='.$response->error);
    }
}
else{
?>
<body>
<div class="container">
    <h2>Welcome Back</h2>
    <div class="separator"></div>
    <form id="loginForm" action="index.php" method="POST">
        <input class="input-field" type="tel" id="phone" name="phone" placeholder="Phone Number" required pattern="[0-9]{10}" title="Enter a 10-digit phone number">
        <input class="input-field" type="password" id="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Login</button>
    </form>
    <p style="color:red;font-size: 1.5rem" id="message"><?php echo $_GET['msg'] ?? '' ?></p>
    <div class="separator"></div>
    <div class="register-link">
        <p style="color:indianred;font-size: 1.3rem;margin-top: 2rem">Don't have an account? <a href="#" class="link">Register</a></p>
    </div>
</div>
</body>
</html>
<?php } ?>