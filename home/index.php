<?php
include "../Common.php";
$common = new Common();
if(!$common->is_user_logged_in()){
    $common->redirect_to('CricketT20/');
}else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Matches</title>
    <link rel="stylesheet" type = "text/css" href ="../model_ui/header/style.css?version=<?php echo time();?>">
    <link rel="stylesheet" type = "text/css" href ="style.css?version=<?php echo time();?>">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <script src="../model_ui/header/script.js"></script>
    <script src="script.js"></script>
    <script src="../scripts/script.js"></script>
</head>
<body onload="fill_header()">
<div id="header"></div>
<section id="matches" class="matches">
    <div class="container">
        <h2>Select Match</h2>
        <ul id="match-list" class="match-list">
        </ul>
    </div>
</section>
</body>
</html>
<?php } ?>