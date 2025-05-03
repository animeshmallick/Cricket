<?php
session_start();
include "../Common.php";
$common = new Common();
$settlement_required = false;
if ($common->is_user_logged_in() && $common->is_user_an_admin()){
    $series_id = $common->get_cookie("series_id");
    $match_id = $common->get_cookie("match_id");
    $session = $_GET['session'];
    $all_bids = $common->get_all_bids($series_id, $match_id, 'session');
    $all_matches= $common->get_all_matches();
    $all_users = $common->get_all_users();
    $all_bids_new = array();
    foreach ($all_bids as $bid) {
        if ($bid->session == $session[0] && $bid->innings == $session[1]) {
            $all_bids_new[] = $bid;
        }
    }
    usort($all_bids_new, function($a, $b) {
        return strcmp($a->timestamp,$b->timestamp) * -1;
    });
    ?>
<html lang="">
<head>
    <title>Admin : Match Session Dashboard</title>
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
    <link rel="icon" type="image/x-icon" href="/images/ball.png">
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
</head>
<body onload="fill_header('<?= $_SESSION['customer_id']?>');fill_footer()">
    <div id="header"></div>
    <div class="scorecard-container">
        <div class="sub-title"><?php echo $common->get_match_name_match_id($all_matches,
                                            $common->get_cookie('match_id'),
                                            $common->get_cookie('series_id')); ?></div>
        <div class="sub-title">All Bids On Session <?php echo $session; ?></div>
        <table>
            <tbody>
    <?php
            foreach ($all_bids_new as $bid) { ?>
                    <tr id="<?= $bid->id ?>" min="<?= $bid->runs_min ?>" max="<?= $bid->runs_max ?>" class="bid-row">
                        <td><?php echo $common->get_user_from_users($all_users, $bid->ref_id)." @ ".$bid->timestamp; ?></td>
                        <td>
                            <?php if ($bid->slot == 'x')
                                echo 'Runs '.$bid->runs_max." or Less";
                            else if($bid->slot == 'y')
                                echo "Runs ".$bid->runs_min." to ".$bid->runs_max;
                            else if($bid->slot == 'z')
                                echo "Runs ".$bid->runs_min." or More";
                            $result=explode(" VS ", $common->get_match_name_match_id($all_matches, $bid->match_id, $bid->series_id));
                            if($bid->slot=="T1")
                                echo $result[0]." Wins";
                            if($bid->slot=="T2")
                                echo $result[1]." Wins";
                            ?>
                        </td>
                        <?php $amount_string = '₹'.$bid->amount." && ₹".(int)($bid->amount * (1 + $bid->rate));
                        if($bid->status=="placed")
                            $amount_string = str_replace('&&', "may return", $amount_string);
                        else if($bid->status=="cancel")
                            $amount_string = '₹'.$bid->amount.' Cancelled';
                        else if($bid->status=="win")
                            $amount_string = str_replace('&&', "became", $amount_string);
                        else if($bid->status=="loss")
                            $amount_string = str_replace('&&', "failed to", $amount_string);
                        else
                            $amount_string = "BID Status Invalid";
                        ?>
                        <td><?php echo $amount_string; ?></td>
                        <td><?php echo $bid->status; ?></td>
                        <td><?php echo $bid->room; ?></td>
                        <?php if ($bid->status == "placed")
                            $settlement_required = true;
                        ?>
                    </tr>

<?php } ?>
            </tbody>
        </table>
        <?php
        if($settlement_required){ ?>
            <a onclick="settle_bid_all('<?=$_SESSION['customer_id']?>', 'session')" class="button" style="padding: 1rem 0.5rem; margin: 0" href="#">Settle Session</a>
        <?php } ?>
        <a class="button" href="admin_match_dashboard.php">Go Back</a>
    </div>
    <div class="separator"></div>
    <div id="footer"></div>
</body>
</html>
<?php } else {
        $common->redirect_to('Cricket/');
    } ?>