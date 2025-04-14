<?php
include "../Common.php";
$common = new Common();
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $common->is_user_logged_in()){
    $ref_id = $common->get_cookie('ref_id');
    $user = $common->get_user_details_from_users($ref_id);

    $tickets = $common->get_tickets($ref_id);
    $bids = $common->get_bids($ref_id);
    $total_added = 0;
    $total_withdrawn = 0;
    $total_bid_placed_amount = 0;
    $total_bid_win_amount = 0;
    $unique_matches_played = [];
    foreach ($tickets as $ticket) {
        if ($ticket->transaction_type == "add" && $ticket->status == "settled")
            $total_added += $ticket->amount;
        if ($ticket->transaction_type == "withdraw" && $ticket->status == "settled")
            $total_withdrawn += $ticket->amount;
    }
    foreach ($bids as $bid) {
        if ($bid->status == "win")
            $total_bid_placed_amount += (1 + $bid->rate) * $bid->amount;
        $total_bid_placed_amount += $bid->amount;
        $unique_matches_played[] = $bid->series_id . '&&' . $bid->match_id;
    }
    $unique_matches_played = array_unique($unique_matches_played);
    $eligible_for_promotion = false;
    if (count($unique_matches_played) >= 20 && $total_added > 2000 && $total_withdrawn > 1000 &&
        $total_bid_placed_amount > 25000 &&$total_added * 0.75 > $total_withdrawn && !isset($user->promotion_id))
            $eligible_for_promotion = true;
    ?>
    <html lang="en">
    <head>
        <title>My Account : <?= $common->get_Cookie('fname')." ".$common->get_Cookie('lname') ?></title>
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
                    'user_id': getCookie('ref_id'),
                    'user_name': getCookie('fname') + " " + getCookie('lname'),
                    'user_type': getCookie('user_type'),
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
    <body onload="fill_header();fill_footer();">
    <div id="header"></div>
    <div class="promotion-card-container">
        <div class="title" style="font-size: 1.5rem">Account Details</div>
        <div class="card">
            <div class="sub-title" style="text-align: left; padding: 0.75rem 0.3rem">Total Added ₹<?= floor($total_added) ?> (<?= floor($total_added/20)?>%)</div>
        </div>
        <div class="card">
            <div class="sub-title" style="text-align: left; padding: 0.75rem 0.3rem">Total Withdrawn ₹<?= floor($total_withdrawn) ?> (<?= floor($total_withdrawn/10)?>%)</div>
        </div>
        <div class="card">
            <div class="sub-title" style="text-align: left; padding: 0.75rem 0.3rem">Max Withdraw 75% of added: <?= $total_added * 0.75 > $total_withdrawn ? 'YES' : 'NO'?></div>
        </div>
        <div class="separator"></div>
        <div class="card">
            <div class="sub-title" style="text-align: left; padding: 0.75rem 0.3rem">Total Bid Placed ₹<?= floor($total_bid_placed_amount) ?> (<?= floor($total_bid_placed_amount/250)?>%)</div>
        </div>
        <div class="card">
            <div class="sub-title" style="text-align: left; padding: 0.75rem 0.3rem">Matches Played <?= count($unique_matches_played) ?> (<?= floor(count($unique_matches_played)*5)?>%)</div>
        </div>
        <div class="separator"></div>
        <div class="change-session-btn" style="margin-bottom: 0.25rem">
            <a style="text-decoration: none; color: inherit;" onclick="apply_for_promotion(<?= (bool)$eligible_for_promotion ?>)">Apply For Promotion</a>
        </div>
    </div>

    <div id="footer"></div>
    </body>

    </html>
<?php } else {
    $common->logout();
    $common->redirect_to('Cricket/');
}