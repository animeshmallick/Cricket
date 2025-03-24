<?php
include "../Common.php";
$common = new Common();
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $common->is_user_logged_in() && $common->is_user_an_admin()){
    $ref_id = $common->get_cookie('ref_id');
    ?>
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
    <body onload="fill_header();fill_all_wallet_transaction_tickets();fill_footer();">
    <div id="header"></div>
    <div class="w-full grid grid-cols-1 md:grid-cols-2" style="padding: 0 1.2rem; background: linear-gradient(90deg, steelblue, rebeccapurple);border-radius: 1rem">
        <div class="title">All Wallet Transaction Tickets</div>
        <div id="transactionContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Transaction cards will be inserted here dynamically -->
        </div>
    </div>
    <div class="separator"></div>
    <div id="footer"></div>
    </body>
    </html>
<?php } else {
    $common->logout();
    $common->redirect_to('Cricket/');
}