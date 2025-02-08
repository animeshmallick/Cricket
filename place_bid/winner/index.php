<?php
include "../../Common.php";
$common = new Common();
if(!$common->is_user_logged_in() || !isset($_GET['room']) || !isset($_GET['session'])){
    $common->redirect_to('Cricket/');
}else{
    $room = $_GET['room'];
    $session = $_GET['session'];
    $amount_min = $room == 1 ? 1 : ($room == 2 ? 501 : 1501);
    $amount_max = $room == 1 ? 500 : ($room == 2 ? 1500 : 2500);
    $amount_default = $room == 1 ? 199 : ($room == 2 ? 699 : 1999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Matches</title>
    <link rel="stylesheet" type = "text/css" href ="../../model_ui/header/style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="../../model_ui/footer/style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="../../model_ui/scorecard/style.css?version=<?php echo time();?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type = "text/css" href ="style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="../../styles/style.css?version=<?php echo time();?>">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <script src="../../model_ui/header/script.js"></script>
    <script src="script.js"></script>
    <script src="../../scripts/script.js"></script>
</head>
<body onload="fill_header();fill_scorecard();fill_footer();update_winner_slots(true);">
<div id="header"></div>
<div id="scorecard"></div>
<div class="separator"></div>
<div class="container" id="bid_container">
    <div class="sub-title">Place new bid</div>
    <div class="rooms-container">
        <div class="sub-title">Select Room Based On Bid Amount</div>
        <div style="display: flex; justify-content: space-between">
            <a class="room <?php echo $room == 1 ? 'room-selected' : ''?>" href="index.php?session=<?php echo $_GET['session']; ?>&room=1"><span>&#8377;1 - &#8377;500</span></a>
            <a class="room <?php echo $room == 2 ? 'room-selected' : ''?>" href="index.php?session=<?php echo $_GET['session']; ?>&room=2"><span>&#8377;500 - &#8377;1500</span></a>
            <a class="room <?php echo $room == 3 ? 'room-selected' : ''?>" href="index.php?session=<?php echo $_GET['session']; ?>&room=3"><span>&#8377;1500 - &#8377;2500</span></a>
        </div>
    </div>
    <div class="bid-section">
        <form action="../../bid_placed/index.php" method="post" id="place-bid-form">
            <input type="hidden" name="bid_id" value="<?php echo $common->get_unique_bid_id('session'); ?>" hidden="hidden">
            <input type="hidden" name="room" value="<?php echo $room;?>">
            <input type="hidden" name="session" value="<?php echo $session;?>">
            <input type="hidden" name="bid_amount" id="bid_amount" value="<?php echo $amount_default;?>">
            <div class="input-section">
                <span class="select-amount">Slide to Change Amount</span>
                <div class="slider-div" style="display: flex">
                    <input type="range" id="bidSlider" class="slider"
                       min="<?php echo $amount_min;?>"
                       max="<?php echo $amount_max;?>" step="1" value="<?php echo $amount_default;?>" name="amount">
                </div>
                <div class="bid-amount" style="text-align: center">Bid Amount <span class="amount-span" id="bidAmount">₹0</span></div>
            </div>
            <div class="slots">
                <div class="slot-header">Choose your slot</div>
                <div class="balls-remaining-container" style="width: 98%; margin-bottom: 0.3rem; background-color: wheat">Session : Match Winner</div>
                <div style="display: flex">
                    <div class="balls-remaining-container"><span id="balls_remaining"></span></div>
                </div>
                <div class="slot" id="slot_a">
                    <input type="radio" name="slot" id="slot_x" value="x" style="display: none">
                    <span class="slot-line" id="slot_a_runs">50 Runs or less</span>
                    <div class="separator"></div>
                    <span class="slot-line" id="slot_a_amount">Put ₹100 get ₹200</span>
                </div>
                <div class="slot" id="slot_b">
                    <input type="radio" name="slot" id="slot_y" value="y" style="display: none">
                    <span class="slot-line" id="slot_b_runs">51 to 55 runs</span>
                    <div class="separator"></div>
                    <span class="slot-line" id="slot_b_amount"> Put ₹100 get ₹200</span>
                </div>
                <div id="placeBidBtn" class="place-bid-btn"><div>Place Bid</div></div>
            </div>
        </form>
        <div class="change-session-btn" style="margin-bottom: 0.25rem">
            <a style="text-decoration: none; color: inherit;" onclick="redirect_to(`Cricket/match/index.php?series_id=${getCookie('series_id')}&match_id=${getCookie('match_id')}`)">Change Session</a>
        </div>
        <div class="change-session-btn" style="margin-bottom: 0.25rem">
            <a style="text-decoration: none; color: inherit;" onclick="redirect_to('Cricket/your_bids/')">Show Your Bids for this match</a>
        </div>
    </div>
</div>
<div class="separator"></div>
<div id="footer"></div>

<script>
    const bidSlider = document.getElementById('bidSlider');
    const bidAmount = document.getElementById('bidAmount');
    const bidInput = document.getElementById('bidInput');
    const placeBidBtn = document.getElementById('placeBidBtn');
    const slots = document.querySelectorAll('.slot');

    function updateBidAmount(value) {
        bidAmount.textContent = '₹' + value;
        bidSlider.value = value;
    }

    bidSlider.addEventListener('input', () => {
        updateBidAmount(bidSlider.value);
    });
    bidSlider.addEventListener('change', () => {
        update_winner_slots(false);
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
    slots.forEach((slot) => {
        slot.addEventListener('click', () => {
            slots.forEach(s => s.classList.remove('active'));
            slot.classList.add('active');
            slot.children.item(0).checked = true;
        });
    });
    updateBidAmount('<?php echo $amount_default;?>');

</script>
</body>
</html>
<?php } ?>