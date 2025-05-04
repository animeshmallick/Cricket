<?php
session_start();
include "../Common.php";
$common = new Common();
if(!$common->is_user_logged_in()){
    $common->redirect_to('Cricket/');
}else{
    //$common->setCookie('series_id', "");
    //$common->setCookie('match_id', "");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home : <?= $_SESSION['fname']." ".$_SESSION['lname'] ?></title>
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
                'user_name': '<?=$_SESSION['fname']?> <?=$_SESSION['lname']?>',
                'user_type': '<?=$_SESSION['user_account_type']?>',
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
    <link rel="icon" type="image/x-icon" href="../images/ball.png">
    <link rel="stylesheet" type = "text/css" href ="../model_ui/header/style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="../model_ui/footer/style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="../styles/style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="style.css?version=<?php echo time();?>">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <script src="../model_ui/header/script.js?version=<?php echo time();?>"></script>
    <script src="script.js?version=<?php echo time();?>"></script>
</head>
<body onload="fill_header('<?= $_SESSION['customer_id']?>');fill_footer(); homeOnload('<?= $_SESSION['customer_id']?>'); triggerPartyPopper(); show_banner()">
<div class="banner" id="banner">
    <div class="title">Notice</div>
    <div style="display: flex">
        <div style="display: block; width: 90%">
            <ol>
                <li>Add Money and get 25% extra bonus (upto ₹100)</li>
                <div class="separator"></div>
                <li>Offer valid only for today</li>
            </ol>
        </div>
        <button style="width: 10%" class="close-banner" onclick="closeBanner()">✖ <span id="banner_time"></span></button>
    </div>
</div>
    <div id="header"></div>
    <section id="matches" class="matches">
        <div class="container">
            <div style="display: flex; justify-content: space-between">
                <div style="font-weight: bold; color: royalblue; margin-top: 0.5rem; font-size: 1.5rem">Select Match</div>
                <div class="change-session-btn" onclick="redirect_to('Cricket/ipl_points_table/')">IPL Points Table</div>
            </div>
            <div class="separator"></div>
            <ul id="match-list" class="match-list">
            </ul>
        </div>
    </section>
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
<?php } ?>