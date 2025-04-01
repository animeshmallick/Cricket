<?php
include "../../Common.php";
$common = new Common();
$series_id = $common->get_cookie('series_id');
$match_id = $common->get_cookie('match_id');
if(!$common->is_user_logged_in() || !isset($_GET['room']) || $series_id == null || $match_id == null){
    $common->redirect_to('Cricket/');
}else{
    $room = $_GET['room'];
    if(!isset($_GET['question_id'])){
        $question_id = rand(0, 4);
        $common->redirect_to('Cricket/place_bid/special/index.php?room='.$room.'&question_id='.$question_id);
    }else{
    $question_id = $_GET['question_id'];
    $amount_min = $room == 1 ? 1 : ($room == 2 ? 501 : 1501);
    $amount_max = $room == 1 ? 500 : ($room == 2 ? 1500 : 2500);
    $amount_default = $room == 1 ? 100 : ($room == 2 ? 700 : 2000);
    if (isset($_GET['amount']))
        $amount_default = floatval($_GET['amount']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Place Bid : Special</title>
    <script src="../../scripts/script.js?version=<?php echo time();?>"></script>
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
    <link rel="stylesheet" type = "text/css" href ="../../model_ui/header/style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="../../model_ui/footer/style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="../../model_ui/scorecard/style.css?version=<?php echo time();?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type = "text/css" href ="style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="../../styles/style.css?version=<?php echo time();?>">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <script src="../../model_ui/header/script.js?version=<?php echo time();?>"></script>
    <script src="script.js?version=<?php echo time();?>"></script>
    <script src="https://unpkg.com/shepherd.js@8"></script>
    <link rel="stylesheet" href="https://unpkg.com/shepherd.js@8/dist/css/shepherd.css">
</head>
<body onload="fill_header();fill_scorecard();fill_footer();fill_special_question(<?= $question_id ?>, true)">
<div id="header"></div>
<div id="scorecard"></div>
<div class="separator"></div>
<div class="container" id="bid_container">
    <div class="sub-title">Place new bid</div>

    <!-- Todo: Remove comment once the rooms are ready to handle requests
    <div class="play-container">
        <div class="sub-title">Select Room Based On Bid Amount</div>
        <div style="display: flex; justify-content: space-between">
            <a class="room <?php echo $room == 1 ? 'room-selected' : ''?>" href="index.php?question_id=<?php echo $_GET['question_id']; ?>&room=1" id="room_1"><span>&#8377;1 - &#8377;500</span></a>
            <a class="room disabled <?php echo $room == 2 ? 'room-selected' : ''?>" href="index.php?question_id=<?php echo $_GET['question_id']; ?>&room=2" id="room_1"><span>&#8377;500 - &#8377;1500</span></a>
            <a class="room disabled <?php echo $room == 3 ? 'room-selected' : ''?>" href="index.php?question_id=<?php echo $_GET['question_id']; ?>&room=3" id="room_1"><span>&#8377;1500 - &#8377;2500</span></a>
        </div>
    </div>
    -->

    <div class="bid-section">
        <form action="../../bid_placed/special.php" method="get" id="place-bid-form">
            <input type="hidden" name="bid_id" value="<?php echo $common->get_unique_bid_id('special'); ?>" hidden="hidden">
            <input type="hidden" name="room" value="<?php echo $room;?>">
            <input type="hidden" name="question_id" value="<?php echo $question_id;?>">
            <input type="hidden" name="bid_amount" id="bid_amount" value="<?php echo $amount_default;?>">
            <?php if($common->is_user_an_agent()){ ?>
                <label class="label" for="bid_name">Add name to this bid:</label>
                <input type="text" id="bid_name" name="bid_name" placeholder="Bid Name" required>
                <div class="separator"></div>
            <?php } ?>
            <div class="input-section">
                <span class="select-amount">Slide to Change Amount</span>
                <div class="slider-div" style="display: flex">
                    <input type="range" id="bidSlider" class="slider"
                       min="<?php echo $amount_min;?>"
                       max="<?php echo $amount_max;?>" step="1" value="<?php echo $amount_default;?>" name="bid_amount">
                </div>
                <div class="bid-amount" style="text-align: center">Bid Amount <span class="amount-span" id="bidAmount">₹0</span></div>
            </div>
            <div class="question-name" id="question_name">Question 1 here</div>
            <div class="slot-header">Choose your options</div>
            <div class="gap"></div>
            <div class="slots" id="slots">

            </div>
            <div id="placeBidBtn" class="place-bid-btn"><div>Place Bid</div></div>
        </form>
        <div class="change-question-btn" style="margin-bottom: 0.25rem">
            <?php
            $new_question_id = rand(0, 4);
            $trial = 0;
            while($new_question_id == $question_id && $trial < 50) {
                $new_question_id = rand(0, 4);
                $trial++;
            }
            ?>
            <a style="text-decoration: none; color: inherit;" onclick="redirect_to(`Cricket/place_bid/special/index.php?room=${<?= $room ?>}&question_id=${<?= $new_question_id ?>}`)">Change Question</a>
        </div>
        <div class="separator"></div>
        <div class="change-session-btn" style="margin-bottom: 0.25rem">
            <a style="text-decoration: none; color: inherit;" onclick="redirect_to(`Cricket/match/index.php?series_id=${getCookie('series_id')}&match_id=${getCookie('match_id')}`)">Change Session</a>
        </div>
        <div class="change-session-btn" id="show_all_bids" style="margin-bottom: 0.25rem">
            <a style="text-decoration: none; color: inherit;" onclick="redirect_to('Cricket/your_bids/')">Show Your Bids for this match</a>
        </div>
    </div>
</div>
<div class="separator"></div>
<div id="footer"></div>
<button class="refresh-btn" onclick="refreshPage(this)">🔄</button>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const bidSlider = document.getElementById('bidSlider');
    const bidAmount = document.getElementById('bidAmount');
    const bidInput = document.getElementById('bidInput');
    const placeBidBtn = document.getElementById('placeBidBtn');
    console.log(slots.length);
    function updateBidAmount(value) {
        bidAmount.textContent = '₹' + value;
        bidSlider.value = value;
    }

    bidSlider.addEventListener('input', () => {
        updateBidAmount(bidSlider.value);
    });
    bidSlider.addEventListener('change', () => {
        fill_special_question(urlParams.get('question_id'), false);
    });

    placeBidBtn.addEventListener('click', () => {
        const submitBtn = document.getElementById('placeBidBtn');
        submitBtn.disabled = true;
        submitBtn.classList.add('disabled');
        submitBtn.innerHTML = '<div>Placing Bid...</div>';
        submitBtn.style.backgroundColor = 'orange';
        document.getElementById('place-bid-form').submit()
    });

    // Slot Click Event: Standout Effect
    function changeOptionsUI() {
        let options = document.querySelectorAll('.slot');
        options.forEach(s => {
            s.classList.remove('active');
        });
        this.classList.add('active');
        this.children.item(0).checked = true;
    }
    updateBidAmount('<?php echo $amount_default;?>');

</script>
</body>
</html>
<?php }
} ?>