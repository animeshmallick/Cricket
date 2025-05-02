<?php
session_start();
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
        <title>Bid Placed Confirmation</title>
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
                gtag('event', 'purchase', {
                    'value': <?= $amount ?>,
                    'currency': 'INR',
                    'transaction_id': '<?= $bid_id ?>',
                    'user_id': <?=$_SESSION['customer_id']?>,
                    'user_name': <?=$_SESSION['fname']?> + " " + <?=$_SESSION['lname']?>,
                    'series_id': '<?= $series_id ?>',
                    'match_id': '<?= $match_id ?>',
                    'session': '<?= $session ?>',
                    'type': `<?= $common->isValidSession($session) ? "session" : ($session == "winner" ? "winner" : "--") ?>`,
                    'room': '<?= $room ?>'
                })
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
    <body onload="fill_header('<?= $_SESSION['customer_id']?>');
        fill_scorecard('<?=$_SESSION['customer_id']?>');
        fill_footer();
        triggerPartyPopper()">
    <div id="header"></div>
    <?php
    if ($common->is_user_logged_in() && $common->isValidSession($session)) {
        $bid_bookie_response = $common->get_session_bid_bookie_details($series_id, $match_id, $session, $amount, $room);
        if(!isset($bid_bookie_response->error)){
            $rate = $slot == 'x' ? $bid_bookie_response->rate_1 : ($slot == 'y' ? $bid_bookie_response->rate_2 : 0);
            $bid_runs_string = $slot == 'x' ? "Runs Less Than ".$bid_bookie_response->predicted_runs :
                ($slot == 'y' ? "Runs More than ".$bid_bookie_response->predicted_runs : 0);
            $bid_runs_string .= ' by end of '.$common->get_end_over_from_session($session)."th Over";
            $run_min = $slot == 'x' ? 0 : ($slot == 'y' ? $bid_bookie_response->predicted_runs + 1 : 999);
            $run_max = $slot == 'x' ? $bid_bookie_response->predicted_runs - 1 : ($slot == 'y' ? 999 : 0);
            $ref_id = $_SESSION['customer_id'];
            $session_id = $_SESSION['session_id'];
            $bid_place_response = $common->insert_new_session_bid_to_db($bid_id, $ref_id, $series_id, $match_id, $session,
                $slot, $run_min, $run_max, $rate, $amount, $bid_name, $room, $session_id);
            $bid_place_response = json_decode($bid_place_response);
            if($bid_place_response != null && $bid_place_response->recharge_status){
                $status = true;
                $status_msg_1 = $bid_runs_string;
                $status_msg_2 = "PUT &#8377;".$amount." Take &#8377;".floor(($amount * (1 + $rate)));
                $status_msg_3 = "Agent's Refund &#8377;".floor((int)$amount/10);
                if ($common->is_user_an_agent()) {
                    $common->recharge_user($common->get_unique_recharge_id(),
                        "bidder_refund_agent_".$bid_id, $ref_id, floor($amount / 10));
                }
            } else {
                $status = false;
                $status_msg_1 = isset($bid_place_response->recharge_msg) ? $bid_place_response->recharge_msg : "Bid Rejected";
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
            $rate = $slot == 'x' ? $bid_bookie_response->rate_1 : ($slot == 'y' ? $bid_bookie_response->rate_2 : 0);
            $team = $slot == 'x' ? $bid_bookie_response->team_a : ($slot == 'y' ? $bid_bookie_response->team_b : '0');
            $ref_id = $_SESSION['customer_id'];
            $refund = 0;
            $session_id = $_SESSION['session_id'];
            $bid_place_response = $common->insert_new_winner_bid_to_db($bid_id, $ref_id, $series_id, $match_id, $slot,
                $rate, $amount, $bid_name, $room, $session_id);
            $bid_place_response = json_decode($bid_place_response);
            if ($bid_place_response->recharge_status) {
                $status = true;
                $status_msg_1 = $team. "Wins the match";
                $status_msg_2 = "PUT &#8377;".$amount." & Take &#8377;".floor((int)($amount * (1 + $rate)));
                $status_msg_3 = "You got refund of &#8377;".floor((int)$amount/20);
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
            <?php if($session  == 'winner'){ ?>
                <a class="button" style="margin-left: 12.5%; width: 75%" href="../place_bid/winner/index.php?session=<?= $session ?>&room=<?= $room ?>">New Bid</a>
            <?php } else if($common->isValidSession($session)) { ?>
                <a class="button" style="margin-left: 12.5%; width: 75%" href="../place_bid/session/index.php?session=<?= $session ?>&room=<?= $room ?>">New Bid</a>
            <?php } ?>
            <div class="separator"></div>
            <button class="button" style="margin-left: 12.5%; width: 75%" onclick="redirect_to('Cricket/your_bids/')">Dashboard</button>
        </div>
    </div>
    <div class="separator"></div>
    <div id="scorecard"></div>
    <div class="separator"></div>
    <div id="footer"></div>
    <canvas id="confetti"></canvas>
    <script>
        const canvas = document.getElementById("confetti");
        const ctx = canvas.getContext("2d");
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        class Confetti {
            constructor() {
                this.x = Math.random() * canvas.width; // Random start position
                this.y = Math.random() * canvas.height * -1; // Start above screen
                this.size = Math.random() * 12 + 6;
                this.speedY = Math.random() * 3 + 4; // Fall speed
                this.speedX = Math.random() * 3 - 1.5; // Random initial left/right drift
                this.swing = Math.random() * 5 + 2; // Random swing range
                this.angle = Math.random() * 360;
                this.rotationSpeed = Math.random() * 5;
                this.opacity = 1;
                this.fadeRate = Math.random() * 0.01 + 0.002;
                this.shape = Math.random() > 0.5 ? "circle" : "rect";
                this.color = `hsl(${Math.random() * 360}, 100%, 60%)`;

                this.drift = Math.random() * 0.06 - 0.03; // Unique random drift per popper
            }

            update() {
                this.y += this.speedY;
                this.x += Math.sin(this.y / 30) * this.swing;
                this.angle += this.rotationSpeed;
                if (this.y > canvas.height * 0.8) {
                    this.opacity -= this.fadeRate;
                }
            }

            draw() {
                ctx.save();
                ctx.globalAlpha = this.opacity;
                ctx.translate(this.x, this.y);
                ctx.rotate((this.angle * Math.PI) / 180);
                ctx.fillStyle = this.color;

                if (this.shape === "rect") {
                    ctx.fillRect(-this.size / 2, -this.size / 2, this.size, this.size);
                } else {
                    ctx.beginPath();
                    ctx.arc(0, 0, this.size / 2, 0, Math.PI * 2);
                    ctx.fill();
                }

                ctx.restore();
            }
        }

        let confettiArray = [];
        let animationFrame;

        function createConfetti() {
            confettiArray = [];
            for (let i = 0; i < 500; i++) {
                confettiArray.push(new Confetti());
            }
        }

        function animateConfetti() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            confettiArray = confettiArray.filter((confetti) => confetti.opacity > 0);
            confettiArray.forEach((confetti) => {
                confetti.update();
                confetti.draw();
            });

            if (confettiArray.length > 0) {
                animationFrame = requestAnimationFrame(animateConfetti);
            }
        }

        function triggerPartyPopper() {
            createConfetti();
            animateConfetti();
            setTimeout(() => {canvas.remove();}, 3500);
        }
    </script>
    </body>
    </html>
<?php } else
{
    $common->redirect_to(".../index.php");
}