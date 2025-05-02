<?php
session_start();
include "../Common.php";
$common = new Common();
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $common->is_user_logged_in()){
    $ref_id = $_SESSION['customer_id'];
    ?>
    <html lang="en">
    <head>
        <title>My Wallet Tickets : <?= $_SESSION['fname']." ".$_SESSION['lname'] ?></title>
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
                    'user_id': <?= $_SESSION['customer_id']?>,
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
        <script>
            function changeBtn(value){
                if (value === 'add') {
                    document.getElementById('create-ticket-btn').innerHTML = "Add Money";
                    document.getElementById('max_withdraw_div').style.display = "none";
                }
                if (value === 'withdraw') {
                    document.getElementById('create-ticket-btn').innerHTML = "Withdraw";
                    document.getElementById('max_withdraw_div').style.display = "block";
                }
            }
        </script>
    </head>
    <body onload="fill_header('<?= $_SESSION['customer_id']?>');fill_wallet_transaction_tickets('<?= $_SESSION['customer_id']?>');fill_footer();">
    <div id="header"></div>
    <div class="main_container">
        <div class="sub-title">My Wallet Transaction</div>
        <form action="index.php" method="POST" name='ticket_form' id="ticket_form" onsubmit="return validate_ticket_form()">
            <div class="container">
                <div class="sub-title" id="max_withdraw_div" style="display: none">Max Amount To Withdraw ₹<span id="max_withdraw_amount"></span></div>
                <div class="separator"></div>
                <input type="text" name="transaction_id" value="<?php echo $common->get_unique_recharge_id();?>" hidden="hidden">
                <select name="transaction-type" id="transaction_type" required onchange="changeBtn(this.value)">
                    <option value="add">Add Money</option>
                    <option value="withdraw">Withdraw</option>
                </select>
                <input type="number" name="amount" placeholder="Enter amount" required>
                <button class="button" id='create-ticket-btn' type="submit">Add Money</button>
            </div>
        </form>
    </div>
    <div class="separator"></div>
    <div id="popup" class="popup">
        <div class="popup-content">
            <h3>Edit Ticket Amount</h3>
            <div style="display: none" id="tran_id"></div>
            <input type="number" id="newAmount" />
            <button onclick="updateTicketAmount('<?= $_SESSION['customer_id']?>')">Save</button>
            <button onclick="closeEditTicketPopup()">Cancel</button>
        </div>
    </div>
    <div class="w-full grid grid-cols-1 md:grid-cols-2" style="padding: 0 1.2rem; background: linear-gradient(90deg, steelblue, rebeccapurple);border-radius: 1rem">
        <div class="title">Your Wallet Transaction Tickets</div>
        <div id="transactionContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Transaction cards will be inserted here dynamically -->
        </div>
    </div>
    <div class="separator"></div>
    <div id="footer"></div>
    </body>
    </html>
<?php } else if($_SERVER['REQUEST_METHOD'] === 'POST' && $common->is_user_logged_in()) {
    $transaction_id = intval($_POST['transaction_id']);
    $amount = floatval($_POST['amount']);
    $transaction_type = $_POST['transaction-type'];
    $ref_id = $_SESSION['customer_id'];
    $response = $common->save_transaction_ticket($transaction_id, $ref_id, $transaction_type, $amount);
    header('Location:index.php');
} else {
    $common->logout();
    $common->redirect_to('Cricket/');
}