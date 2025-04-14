<?php
include "../Common.php";
$common = new Common();
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $common->is_user_logged_in()){
    $users = $common->get_all_users_with_balance();

    $accounts = array();
    foreach ($users as $user) {
        if ($user->status == 'active' && $user->type != 'admin') {
            $ref_id = $user->ref_id;
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
                    $total_bid_win_amount += (1 + $bid->rate) * $bid->amount;
                $total_bid_placed_amount += $bid->amount;
                $unique_matches_played[] = $bid->series_id . '&&' . $bid->match_id;
            }
            $unique_matches_played = array_unique($unique_matches_played);
            $accounts[] = array(
                'name' => $user->fname . ' ' . $user->lname,
                'phone' => $user->phone,
                'balance' => $user->balance,
                'total_added' => $total_added,
                'total_withdrawn' => $total_withdrawn,
                'total_bid_placed_amount' => $total_bid_placed_amount,
                'total_bid_win_amount' => $total_bid_win_amount,
                'total_profit' => $total_bid_win_amount - $total_bid_placed_amount,
                'matches_played' => count($unique_matches_played)
            );
        }
    }
    usort($accounts, function ($a, $b) {
        return $b['total_bid_placed_amount'] - $a['total_bid_placed_amount'];
    });
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
    <?php foreach ($accounts as $account){ ?>
        <div class="promotion-card-container">
            <div class="title" style="font-size: 1.5rem"><?= $account['name'] ?></div>
            <div class="card">
                <div class="sub-title" style="text-align: left; padding: 0.25rem 0.1rem">Total Added ₹<?= floor($account['total_added']) ?></div>
            </div>
            <div class="card">
                <div class="sub-title" style="text-align: left; padding: 0.25rem 0.1rem">Total Withdrawn ₹<?= floor($account['total_withdrawn']) ?></div>
            </div>
            <div class="card">
                <div class="sub-title" style="text-align: left; padding: 0.25rem 0.1rem">Matches Played <?= $account['matches_played'] ?></div>
            </div>
            <div class="card">
                <div class="sub-title" style="text-align: left; padding: 0.25rem 0.1rem">Total Bid Placed ₹<?= floor($account['total_bid_placed_amount']) ?></div>
            </div>
            <div class="card">
                <div class="sub-title" style="text-align: left; padding: 0.25rem 0.1rem">Total Winnings ₹<?= floor($account['total_bid_win_amount']) ?></div>
            </div>
        </div>
        <div class="separator"></div>
        <div class="gap"></div>
    <?php } ?>

    <div id="footer"></div>
    </body>

    </html>
<?php } else {
    $common->logout();
    $common->redirect_to('Cricket/');
}