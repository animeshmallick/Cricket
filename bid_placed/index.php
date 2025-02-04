<?php
include "../Common.php";
$common = new Common();
$slot = $_POST["slot"];
$session = $_POST["session"];
$amount = floatval($_POST["amount"]);
$bid_id = (int)$_POST["bid_id"];
$bid_name = $_POST["bid_name"] ?? "";
$room = $_POST['room'];

$series_id = $common->get_cookie("series_id");
$match_id = $common->get_cookie("match_id");
$match_name = $common->get_cookie("match_name");

if ($_SERVER["REQUEST_METHOD"] == "POST" && $common->is_user_logged_in() &&
    isset($_POST["amount"]) && isset($_POST["slot"]) && isset($_POST["session"])) { ?>
    <html lang="en">
    <head>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-Z91TWPR0DM"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-Z91TWPR0DM');
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
        <script src="../model_ui/header/script.js"></script>
        <script src="script.js"></script>
        <script src="../scripts/script.js"></script>
    </head>
    <body onload="fill_header();
        fill_scorecard();
        fill_footer();">
    <div id="header"></div>
    <?php
    if ($common->is_user_logged_in() && $common->isValidSession($session)) {
        $bid_bookie_response = $common->get_session_bid_bookie_details($series_id, $match_id, $session, $amount, $room);
        if(!isset($bid_bookie_response->error)){
            $rate = $slot == 'x' ? $bid_bookie_response->rate_1 :
                ($slot == 'y' ? $bid_bookie_response->rate_2 :
                    ($slot == 'z' ? $bid_bookie_response->rate_3 : 0));
            $bid_runs_string = $slot == 'x' ? "Runs 0 to ".($bid_bookie_response->predicted_runs_a - 1) :
                ($slot == 'y' ? "Runs [".$bid_bookie_response->predicted_runs_a." to ".$bid_bookie_response->predicted_runs_b."]" :
                    ($slot == 'z' ? "Runs ".($bid_bookie_response->predicted_runs_b + 1)." or more" : 0));
            $bid_runs_string .= ' end of '.$common->get_end_over_from_session($session)."th Over";
            $run_min = $slot == 'x' ? 0 : ($slot == 'y' ? $bid_bookie_response->predicted_runs_a : ($slot == 'z' ? $bid_bookie_response->predicted_runs_b + 1 : 490));
            $run_max = $slot == 'x' ? $bid_bookie_response->predicted_runs_a - 1 : ($slot == 'y' ? $bid_bookie_response->predicted_runs_b : ($slot == 'z' ? 490 : -1));
            $ref_id = $common->get_cookie('ref_id');
            $bid_place_response = $common->insert_new_session_bid_to_db($bid_id, $ref_id, $series_id, $match_id, $session,
                $slot, $run_min, $run_max, $rate, $amount, $bid_name, $room);
            $bid_place_response = json_decode($bid_place_response);
            if($bid_place_response->recharge_status){
                $status = true;
                $status_msg_1 = $bid_runs_string;
                $status_msg_2 = "PUT &#8377;".$amount." Take &#8377;".floor(($amount * $rate));
                $status_msg_3 = "Agent's Refund &#8377;".floor((int)$amount/10);
                if ($common->is_user_an_agent()) {
                    $common->recharge_user($common->get_unique_recharge_id(),
                        "bidder_refund_agent_".$bid_id, $ref_id, floor($amount / 10));
                }
            } else {
                $status = false;
                $status_msg_1 = $bid_place_response->recharge_msg;
                $status_msg_2 = "Bid Amount &#8377;".$amount;
                $status_msg_3 = " -- ";
            }
        } else {
            $status = false;
            $status_msg_1 = "Bidding Placed Failed";
            $status_msg_2 = "Bid Amount &#8377;".$amount;
            $status_msg_3 = $bid_bookie_response->error;
        }
    } elseif ($common->is_user_logged_in() && $session == 'winner') {
        $bid_bookie_response = $common->get_match_winner_bid_bookie_details($series_id, $match_id, $amount, $room);
        if(!isset($bid_bookie_response->error)) {
            $rate = $slot == 'T1' ? $bid_bookie_response->rate_1 : ($slot == 'T2' ? $bid_bookie_response->rate_2 : 0);
            $msg = $slot == 'T1' ? $bid_bookie_response->team1 : ($slot == 'T2' ? $bid_bookie_response->team2 : '0');
            $ref_id = $common->get_cookie('ref_id');
            $refund = 0;
            $bid_place_response = $common->insert_new_winner_bid_to_db($bid_id, $ref_id, $series_id, $match_id, $slot,
                $rate, $amount, $bid_name, $room);
            $bid_place_response = json_decode($bid_place_response);
            if ($bid_place_response->recharge_status) {
                $status = true;
                $status_msg_1 = $msg;
                $status_msg_2 = "PUT &#8377;".$amount." & Take &#8377;".floor((int)($amount * $rate));
                $status_msg_3 = "You got refund of &#8377;".floor((int)$amount/10);
                if ($common->is_user_an_agent()) {
                    $common->recharge_user($common->get_unique_recharge_id(),
                        "bidder_refund_agent_".$bid_id, $ref_id, $amount);
                }
            } else {
                $status = false;
                $status_msg_1 = $bid_place_response->recharge_msg;;
                $status_msg_2 = "Bid Amount &#8377;".$amount;
                $status_msg_3 = " -- ";
            }
        } else {
            $status = false;
            $status_msg_1 = "Error Response from Local Server";
            $status_msg_2 = "Bid Amount &#8377;".$amount;
            $status_msg_3 = " -- ";
        }
    } else {
        $status = false;
        $status_msg_1 = "";
        $status_msg_2 = "";
        $status_msg_3 = "";
    }
    if ($status){
    ?>
    <div id="header"></div>
    <div class="confirm_bid_container">
        <div class="bid-success-title"><p class="confirm">&#9989; Placed</p></div>
        <div class="bid_details_success"><span><?php echo $status_msg_1;?></span></div>
        <div class="bid_details_success"><span><?php echo $status_msg_2;?></span></div>
        <?php if ($common->is_user_an_agent()){?>
            <div class="bid_details_success"><span><?php echo $status_msg_3;?></span></div>
        <?php }
        } else { ?>
        <div class="confirm_bid_container">
            <div class="bid-failure-title"><p class="confirm">&#10060; Failed</p></div>
            <div class="bid_details_failure"><span><?php echo $status_msg_1;?></span></div>
            <div class="bid_details_failure"><span><?php echo $status_msg_2;?></span></div>
            <?php if ($common->is_user_an_agent()){?>
                <div class="bid_details_failure"><span><?php echo $status_msg_3;?></span></div>
            <?php   }
            } ?>
            <div class="separator"></div>
            <a class="button" style="margin-left: 12.5%; width: 75%" href="../match/index.php?series_id=<?php echo $series_id; ?>&match_id=<?php echo $match_id;?>&match_name=<?php echo $match_name;?>">New Bid</a>
            <div class="separator"></div>
            <a class="button secondary" style="margin-left: 12.5%; width: 75%" href="../dashboard/index.php">Dashboard</a>
        </div>
    </div>
    <div class="separator"></div>
    <div id="scorecard"></div>
    <div class="separator"></div>
    <div id="footer"></div>
    </body>
    </html>
<?php } else
{
    $common->redirect_to(".../index.php");
}