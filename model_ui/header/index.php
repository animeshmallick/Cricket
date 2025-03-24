<?php
    include "../../Common.php";
    $common = new Common();
    $header_sub_text = "Hi, " . $common->get_cookie('fname') . " " . $common->get_cookie('lname');
?>
<div class="navbar">
    <div id='side-bar-icon' class="hamburger" onclick="w3_open()">&#9776;</div>
    <div style="display: flex; justify-content: space-between; width: 100%">
        <div style="margin-left: 1rem" onclick="redirect_to('Cricket/home/')">
            <div class="nav-title">CricketT20</div>
            <span><?php echo $header_sub_text;?></span>
        </div>
        <div class="balance-container" onclick="redirect_to('Cricket/wallet_transaction/index.php')">
            <span class="balance-title">Balance</span>
            <div id="balance" class="balance">&#8377;1000</div>
        </div>
    </div>

    <nav class="w3-sidebar w3-bar-block w3-animate-left w3-top" style="font-size: 1rem;z-index:3;width:75%;display:none;left:0;margin: 0;padding: 0; background-image: url('../../images/stadium2.png'), url('../images/stadium2.png')" id="side-bar-container">
        <div class="nav-title">Controls</div>
        <div class="separator"></div>
        <button class="nav-link" onclick="redirect_to('Cricket/')">Home</button>
        <?php if($common->get_cookie('match_id') != "" && $common->get_cookie('series_id') != ""){?>
            <button class="nav-link" onclick="redirect_to('Cricket/your_bids/')">Your Bids</button>
        <?php }?>
        <?php if($common->get_cookie('user_type') == 'admin') {?>
            <div class="sub-title">Admins Only</div>
            <?php if($common->get_cookie('match_id') != "" && $common->get_cookie('series_id') != ""){ ?>
                <button class="nav-link" onclick="redirect_to('Cricket/admin/admin_match_dashboard.php')">Admin Match Dashboard</button>
            <?php } ?>
            <button class="nav-link" onclick="redirect_to('Cricket/admin/activate_user.php')">Activate User</button>
            <button class="nav-link" onclick="redirect_to('Cricket/admin/recharge.php')">Recharge Wallet</button>
            <button class="nav-link" onclick="redirect_to('Cricket/admin/view_tickets.php')">Tickets</button>
            <div class="separator"></div>
        <?php }elseif ($common->get_cookie('user_type') == 'agent'){?>
            <div class="sub-title">Agents Only</div>
            <button class="nav-link" onclick="redirect_to('Cricket/admin/recharge.php')">Transfer Balance</button>
        <?php } ?>
        <button class="nav-link" onclick="logout();">Logout</button>
        <div class="separator"></div>
        <a class="nav-link" onclick="w3_close()">Close</a>
        <div class="separator"></div>
    </nav>
</div>