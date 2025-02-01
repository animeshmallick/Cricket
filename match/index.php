<!DOCTYPE html>
<?php
include "../Common.php";
$common = new Common();
if ($common->is_user_logged_in()) {
    $match_id = $_GET['match_id'];
    $series_id = $_GET['series_id'];
    //$common->set_cookie('match_id', $match_id);
    //$common->set_cookie('series_id', $series_id);
?>
<html lang="">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Z91TWPR0DM"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-Z91TWPR0DM');
    </script>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../styles/style.css?version=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="../styles/scorecard_style.css?version=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <title>Match Page</title>
    <link rel="icon" type="image/x-icon" href="../cricket.ico">
    <script src="../scripts.js?version=<?php echo time(); ?>"></script>
</head>
<body onload="fill_header();">
<div id="header"></div>
<div id="scorecard">Loading Scorecard</div>
</body>
</html>
<?php
} else {
    $common->logout();
    header("Location: http://localhost/CricketT20/");
}?>