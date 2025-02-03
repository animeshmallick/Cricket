<?php
    include "../../Common.php";
    $common = new Common();
    $header_sub_text = "Hi, " . $common->get_cookie('fname') . " " . $common->get_cookie('lname');
?>
<div class="navbar">
    <div id='side-bar-icon' class="hamburger" onclick="w3_open()">&#9776;</div>
    <div onclick="redirect_to('Cricket/home/')">
        <div class="title">CricketT20</div>
        <span><?php echo $header_sub_text;?></span>
    </div>
    <div class="balance-container">
        <span class="balance-title">Balance</span>
        <div id="balance" class="balance">&#8377;1000</div>
    </div>

    <nav class="w3-sidebar w3-bar-block w3-animate-left w3-top" style="font-size: 1rem;z-index:3;width:75%;display:none;left:0;margin: 0;padding: 0; background-image: url('../images/stadium2.png')" id="side-bar-container">
        <div class="nav-title">Controls</div>
        <div class="separator"></div>
        <a class="nav-link" href="../home/">Home</a>
        <a class="nav-link" onclick="logout()">Logout</a>
        <div class="separator"></div>
        <a class="nav-link" onclick="w3_close()">Close</a>
        <div class="separator"></div>
    </nav>
</div>