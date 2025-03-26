<?php
include "Common.php";
$common = new Common();
if($common->is_user_logged_in()){
    $common->redirect_to('Cricket/home/');
}else{
    $common->clear_all_cookies();
?>
    <!DOCTYPE html>
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
        <title>Cricket Login</title>
        <link rel="stylesheet" href="styles/style.css?version=<?php echo time(); ?>">
        <style>
            body {
                margin: 0.5rem;
                padding: 0.5rem;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: steelblue;
                font-family: Arial, sans-serif;
                color: #f5f5f5;
                text-align: center;
                animation: fadeIn 2s ease-in;
                overflow: hidden;
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .container {
                background-color: lightslategray;
                padding: 0.5rem;
                border-radius: 10px;
                transform: scale(0.9);
                animation: popIn 1s ease-out forwards, fadeColor 5s infinite alternate ease-in-out;
                width: 350px;
                height: 350px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                box-shadow: 0px 1rem 1rem rgba(0, 0, 0, 0.5);
            }
            @keyframes popIn {
                from { transform: scale(0.5); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            @keyframes fadeColor {
                0% {
                    background-color: rgba(255, 87, 34, 0.6);
                }
                50% {
                    background-color: rgba(255, 193, 7, 0.6);
                }
                100% {
                    background-color: rgba(76, 175, 80, 0.6);
                }
            }
            .login-btn {
                display: inline-block;
                padding: 12px 25px;
                margin-top: 20px;
                font-size: 18px;
                color: white;
                background: linear-gradient(135deg, #ff5722, #ff9800);
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: 0.3s;
                animation: slideIn 1s ease-out forwards;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            }
            .login-btn:hover {
                background: linear-gradient(135deg, #e64a19, #f57c00);
                box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.5);
            }
            @keyframes slideIn {
                from { transform: translateY(50px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            .ball {
                position: absolute;
                width: 2rem;
                height: 2rem;
                background: radial-gradient(circle, #d32f2f, #b71c1c);
                border-radius: 50%;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
            }
        </style>
        <script src="scripts/script.js?version=<?php echo time();?>"></script>
    </head>
    <body>
    <div class="ball"></div>
    <div class="container">
        <p style="font-size: 2.5rem">Welcome to CricketT20</p>
        <div class="separator"></div>
        <p style="font-size: 1.5rem">Login To Start Bidding</p>
        <div class="separator"></div>
        <button class="login-btn" onclick="redirect_to('Cricket/login')">Login</button>
        <button class="button" onclick="redirect_to('Cricket/register')">Register</button>
        <div class="separator"></div>
    </div>

    <script>
        function getRandomPosition() {
            let x = Math.floor(Math.random() * window.innerWidth);
            if(x % 2 === 0)
                x = (x % 25) * -1;
            else
                x = x % 25;
            let y = Math.floor(Math.random() * window.innerHeight);
            if(y % 2 === 0)
                y = (y % 25) * -1;
            else
                y = y % 25;
            return { x, y };
        }

        function moveBall() {
            const ball = document.querySelector('.ball');
            const { x, y } = getRandomPosition();
            ball.style.left = `${ball.getBoundingClientRect().left + x}px`;
            ball.style.top = `${ball.getBoundingClientRect().top + y}px`;
        }

        // Move the ball every 2 seconds
        setInterval(moveBall, 200);

        // Initial ball position
        moveBall();
    </script>
    </body>
    </html>

<?php } ?>