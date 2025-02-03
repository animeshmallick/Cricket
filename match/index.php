<?php
include "../Common.php";
$common = new Common();
if(!$common->is_user_logged_in() || !isset($_GET['series_id']) || !isset($_GET['match_id'])){
    $common->redirect_to('Cricket/');
}else{
    $series_id = $_GET['series_id'];
    $match_id = $_GET['match_id'];
    $common->setCookie('series_id', $series_id);
    $common->setCookie('match_id', $match_id);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
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
    <body onload="fill_header();fill_scorecard();fill_footer();">
        <div id="header"></div>
        <div id="scorecard"></div>
        <div class="separator"></div>
        <div class="container">
            <div class="sub-title">Select Bid Session</div>
            <!-- Innings 1 -->
            <div class="section-box">
                <div class="section-header">Innings 1</div>
                <div style="display: flex">
                    <div class="clickable-button disabled" id='a1' style="margin-left: 0.6rem;margin-right: 0.3rem" onclick="redirect_to('Cricket/place_bid_session/index.php?session=a1&room=1')">
                        <div style="display: block">Session 1</div>
                        <div style="text-decoration: none; color: inherit;">(0-6 overs)</div>
                    </div>
                    <div class="clickable-button disabled" id='b1' style="margin-left: 0.3rem;margin-right: 0.6rem" onclick="redirect_to('Cricket/place_bid_session/index.php?session=b1&room=1')">
                        <div style="display: block">Session 2</div>
                        <div style="text-decoration: none; color: inherit;">(7-10 overs)</div>
                    </div>
                </div>
                <div style="display: flex">
                    <div class="clickable-button disabled" id='c1' style="margin-left: 0.6rem;margin-right: 0.3rem" onclick="redirect_to('Cricket/place_bid_session/index.php?session=c1&room=1')">
                        <div style="display: block">Session 3</div>
                        <div style="text-decoration: none; color: inherit;">(11-16 overs)</div>
                    </div>
                    <div class="clickable-button disabled" id='d1' style="margin-left: 0.3rem;margin-right: 0.6rem" onclick="redirect_to('Cricket/place_bid_session/index.php?session=d1&room=1')">
                        <div style="display: block">Session 4</div>
                        <div style="text-decoration: none; color: inherit;">(17-20 overs)</div>
                    </div>
                </div>
            </div>
            <!-- Innings 2 -->
            <div class="section-box">
                <div class="section-header">Innings 2</div>
                <div style="display: flex">
                    <div class="clickable-button disabled" id='a2' style="margin-left: 0.6rem;margin-right: 0.3rem" onclick="redirect_to('Cricket/place_bid_session/index.php?session=a2&room=1')">
                        <div style="display: block">Session 1</div>
                        <div style="text-decoration: none; color: inherit;">(0-6 overs)</div>
                    </div>
                    <div class="clickable-button disabled" id='b2' style="margin-left: 0.3rem;margin-right: 0.6rem" onclick="redirect_to('Cricket/place_bid_session/index.php?session=b2&room=1')">
                        <div style="display: block">Session 2</div>
                        <div style="text-decoration: none; color: inherit;">(7-10 overs)</div>
                    </div>
                </div>
            </div>

            <!-- Match Biding -->
            <div class="section-box">
                <div class="section-header">Match Biding</div>
                <div style="display: flex">
                    <div class="clickable-button disabled" id='winner' style="margin-left: 0.6rem;margin-right: 0.3rem" onclick="redirect_to('Cricket/place_bid_winner/index.php?session=winner&room=1')">
                        <div style="display: block;padding: 0.5rem">Who Will Win?</div>
                    </div>
                    <div class="clickable-button disabled" id='session' style="margin-left: 0.3rem;margin-right: 0.6rem" onclick="redirect_to('Cricket/place_bid_special/index.php?session=special')">
                        <div style="display: block;padding: 0.5rem"">Special Bids</div>
                    </div>
                </div>
            </div>
        <!-- Go Back Button -->
        <div class="go-back-button">
            <a style="text-decoration: none; color: inherit" href="../home/">Go Back</a>
        </div>
        <div class="separator"></div>
        <div id="footer"></div>
    </body>
    </html>
<?php } ?>