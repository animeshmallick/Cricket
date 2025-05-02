<?php
session_start();
include "../Common.php";
$common = new Common();

$series_id = $common->get_cookie("series_id");
$match_id = $common->get_cookie("match_id");
$match_name = $common->get_cookie("match_name");

if ($_SERVER["REQUEST_METHOD"] == "GET" && $common->is_user_logged_in()) {
    $status = false;
    $status_msg_1 = "";
    $status_msg_2 = "";
    $status_msg_3 = "";
    $status_msg_4 = "";

    $option = (int)$_GET["option"];
    $amount = floatval($_GET["bid_amount"]);
    $bid_id = (int)$_GET["bid_id"];
    $bid_name = $_GET["bid_name"] ?? "";
    $room = $_GET['room'];
    $ref_id = $_SESSION["ref_id"];
    $question_id = $_GET['question_id'];

    $bookie_response = $common->get_special_bid_bookie_details($series_id, $match_id, $amount, $room, $question_id);
    if(!isset($bookie_response->error)){
        $rate = $bookie_response->rates[$option];
        $response = $common->insert_new_special_bid_to_db($bid_id, $ref_id, $series_id, $match_id, $question_id,
            $bookie_response->question, $option, $bookie_response->options[$option], $rate, $amount, $bid_name, $room);
        $response =json_decode($response);
        if ($response->recharge_status) {
            $status = true;
            $status_msg_1 = $bookie_response->question;
            $status_msg_2 = $bookie_response->options[$option];
            $status_msg_3 = "PUT &#8377;".$amount." Take &#8377;".floor(($amount * (1 + $rate)));
            if ($common->is_user_an_agent()) {
                $status_msg_4 = "Agent's Refund &#8377;".floor((int)$amount/10);
                $common->recharge_user($common->get_unique_recharge_id(),
                    "bidder_refund_agent_".$bid_id, $ref_id, floor($amount / 10));
            }
        } else {
            $status = false;
            $status_msg_1 = $response->recharge_msg;
            $status_msg_2 = "Bid Amount &#8377;".$amount;
            $status_msg_3 = " -- ";
        }
    } ?>
    <html lang="en">
    <head>
        <title>Place Bid Confirmation</title>
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
                    'type': `special`,
                    'room': '<?= $room ?>',
                    'question': {
                        'question_name': '<?= $status_msg_1 ?>',
                        'answer': '<?= $status_msg_2  ?>'
                    }
                })
                let startTime = new Date().getTime();
                window.addEventListener('beforeunload', function () {
                    let timeSpent = Math.round((new Date().getTime() - startTime) / 1000);
                    gtag('event', 'time_on_page', {
                        'event_category': 'User Engagement',
                        'event_label': document.title,
                        'value': timeSpent
                    });
                });
            }
        </script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <body onload="fill_header('<?= $_SESSION['customer_id']?>');
        fill_scorecard('<?=$_SESSION['customer_id']?>');
        fill_footer();
        triggerPartyPopper()">
    <div id="header"></div>

    <?php if($status){ ?>
        <div class="confirm_bid_container">
            <div class="bid-success-title"><p class="confirm">&#9989; Placed</p></div>
            <div class="bid_details_success"><span><?php echo $status_msg_1;?></span></div>
            <div class="bid_details_success"><span><?php echo $status_msg_2;?></span></div>
            <div class="bid_details_success"><span><?php echo $status_msg_3;?></span></div>
            <?php if ($common->is_user_an_agent()){?>
                <div class="bid_details_success"><span><?php echo $status_msg_4;?></span></div>
            <?php }
    } else { ?>
        <div class="confirm_bid_container">
            <div class="bid-failure-title"><p class="confirm">&#10060; Failed</p></div>
            <div class="bid_details_failure"><span><?php echo $status_msg_1;?></span></div>
            <div class="bid_details_failure"><span><?php echo $status_msg_2;?></span></div>
            <div class="bid_details_success"><span><?php echo $status_msg_3;?></span></div>
            <?php if ($common->is_user_an_agent()){?>
                <div class="bid_details_failure"><span><?php echo $status_msg_4;?></span></div>
            <?php   }
    } ?>
    <div class="separator"></div>
    <a class="button" style="margin-left: 12.5%; width: 75%" href="../place_bid/special/index.php?room=<?= $room ?>">New Bid</a>
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
<?php } else {
    echo "Invalid request.";
}