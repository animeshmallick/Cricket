<?php
include "../Common.php";
$common = new Common();
$ref_id = $common->get_cookie('ref_id');
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($common->is_user_an_admin() || $common->is_user_an_agent()) && $common->is_user_logged_in()){ ?>
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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>All Matches</title>
        <link rel="stylesheet" type = "text/css" href ="../model_ui/header/style.css?version=<?php echo time();?>">
        <link rel="stylesheet" type = "text/css" href ="../model_ui/footer/style.css?version=<?php echo time();?>">
        <link rel="stylesheet" type = "text/css" href ="../model_ui/scorecard/style.css?version=<?php echo time();?>">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" type = "text/css" href ="style.css?version=<?php echo time();?>">
        <link rel="stylesheet" type = "text/css" href ="../styles/style.css?version=<?php echo time();?>">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
        <script src="../model_ui/header/script.js?version=<?php echo time();?>"></script>
        <script src="script.js?version=<?php echo time();?>"></script>
        <script src="../scripts/script.js?version=<?php echo time();?>"></script>
    </head>
    <body onload="fill_header();fill_footer();">
    <div id="header"></div>
    <div class="main_container">
        <div class="sub-title">Recharge User Balance</div>
        <form action="recharge.php" method="POST">
            <input type="text" name="recharge_id" value="<?php echo $common->get_unique_recharge_id();?>" hidden="hidden">
            <label class="label" for="phone">Phone Number:</label>
            <input type="number" placeholder="Phone Number" id="phone" name="phone" required>
            <div class="gap"></div>
            <label class="label" for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" placeholder="Amount" required>
            <input type="submit" class="button" value="Recharge">
        </form>
        <p class="error" id="msg"><?php if(isset($_GET['msg'])) { echo $_GET['msg']; } ?></p>
    </div>
    <div class="separator"></div>
    <div id="footer"></div>
    </body>
    </html>
<?php } else if($_SERVER['REQUEST_METHOD'] === 'POST' && ($common->is_user_an_admin() || $common->is_user_an_agent()) && $common->is_user_logged_in()) {
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];
    $recharge_id = $_POST['recharge_id'];
    $from_ref_id = $common->get_cookie('ref_id');
    $user = $common->get_user_from_phone($phone);
    if (!isset($user->error)){
        $to_ref_id = $user->ref_id;
        $response = $common->recharge_user($recharge_id, $from_ref_id, $to_ref_id, $amount);
        header('Location:recharge.php?msg='.$response->recharge_msg);
    }else{
        header('Location:recharge.php?msg='.$user->error);
    }
} else {
    $common->logout();
    $common->redirect_to('Cricket/');
}