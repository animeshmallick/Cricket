<?php
session_start();
include "../Common.php";
$common = new Common();
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $common->is_user_logged_in() && $common->is_user_an_admin()){
    $ref_id = $_SESSION['ref_id'];
    ?>
    <html lang="en">
    <head>
        <title>Admin : View Tickets</title>
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
                    'user_id': <?=$_SESSION['ref_id']?>,
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
        <link rel="stylesheet" type = "text/css" href ="../model_ui/scorecard/style.css?version=<?php echo time();?>">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" type = "text/css" href ="style.css?version=<?php echo time();?>">
        <link rel="stylesheet" type = "text/css" href ="../styles/style.css?version=<?php echo time();?>">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
        <script src="../model_ui/header/script.js?version=<?php echo time();?>"></script>
        <script src="script.js?version=<?php echo time();?>"></script>
    </head>
    <body onload="fill_header('<?= $_SESSION['ref_id']?>');fill_all_wallet_transaction_tickets('<?=$_SESSION['ref_id']?>');fill_footer();">
    <div id="header"></div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Resolved</th>
                <th>Amount Taken</th>
                <th>Amount Given</th>
                <th>Net Amount</th>
            </tr>
        </thead>
        <tbody id="admin_ticket_table"></tbody>
    </table>
    <div class="separator"></div>
    <div class="w-full grid grid-cols-1 md:grid-cols-2" style="padding: 0 1.2rem; background: linear-gradient(90deg, steelblue, rebeccapurple);border-radius: 1rem">
        <div class="title">All Wallet Transaction Tickets</div>
        <form action="" method="get">
            <select name="transaction-type" required onchange="filter_tickets(this.value)">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="add">Add</option>
                <option value="withdraw">Withdraw</option>
                <option value="open" selected>Open</option>
                <option value="closed">Closed</option>
                <option value="all">All</option>
            </select>
        </form>
        <div class="sub-title"><span id="ticket_count"></span> Tickets Found</div>
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