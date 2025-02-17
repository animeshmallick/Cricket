<?php
include "../Common.php";
$common = new Common();
function get_name(array $all_users, string $ref_id): string
{
    foreach ($all_users as $user){
        if ($user->ref_id == $ref_id)
            return $user->fname." ".$user->lname;
    }
    return "";
}

if ($common->is_user_logged_in() && $common->is_user_an_admin()){
    $series_id = $common->get_cookie("series_id");
    $match_id = $common->get_cookie("match_id");
    $all_bids_session = $common->get_all_bids($series_id, $match_id, 'session');
    $all_bids_winner = $common->get_all_bids($series_id, $match_id, 'winner');
    $all_bids_special = $common->get_all_bids($series_id, $match_id, 'special');
    $all_users = $common->get_all_users();
    $user_bids = array();
    $session_a1 = array();
    $session_a1['count'] = 0;
    $session_a1['collected'] = 0;
    $session_a1['distributed_1'] = 0;
    $session_a1['distributed_2'] = 0;
    $session_a1['distributed_3'] = 0;
    $session_a1['given'] = 0;

    $session_b1 = $session_a1;
    $session_c1 = $session_a1;
    $session_d1 = $session_a1;
    $session_a2 = $session_a1;
    $session_b2 = $session_a1;
    $session_winner = $session_a1;
    $session_special = $session_a1;
    $total_c = 0;
    $total_d = 0;
    foreach ($all_bids_session as $bid){
        if(isset($user_bids[$bid->ref_id])){
            $user_bids[$bid->ref_id]['count']++;
            $user_bids[$bid->ref_id]['collected'] += $bid->amount;
            if ($bid->status == 'win')
                $user_bids[$bid->ref_id]['given'] += (int)($bid->amount * $bid->rate);
        }else{
            $obj = array();
            $obj['ref_id'] = $bid->ref_id;
            $obj['count'] = 1;
            $obj['collected'] = $bid->amount;
            if ($bid->status == 'win')
                $obj['given'] = (int)($bid->amount * $bid->rate);
            else
                $obj['given'] = 0;
            $user_bids[$bid->ref_id] = $obj;
        }
        $total_c += (int)$bid->amount;
        if ($bid->status == 'win')
            $total_d += (int)((int)$bid->amount * $bid->rate);
        if ($bid->session.$bid->innings == 'a1'){
            $session_a1['count'] += 1;
            $session_a1['collected'] += $bid->amount;
            if ($bid->slot == 'x')
                $session_a1['distributed_1'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'y')
                $session_a1['distributed_2'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'z')
                $session_a1['distributed_3'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->status == 'win')
                $session_a1['given'] += (int)($bid->amount * $bid->rate);
            continue;
        }
        if ($bid->session.$bid->innings == 'b1'){
            $session_b1['count'] += 1;
            $session_b1['collected'] += $bid->amount;
            if ($bid->slot == 'x')
                $session_b1['distributed_1'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'y')
                $session_b1['distributed_2'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'z')
                $session_b1['distributed_3'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->status == 'win')
                $session_b1['given'] += (int)($bid->amount * $bid->rate);
            continue;
        }
        if ($bid->session.$bid->innings == 'c1'){
            $session_c1['count'] += 1;
            $session_c1['collected'] += $bid->amount;
            if ($bid->slot == 'x')
                $session_c1['distributed_1'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'y')
                $session_c1['distributed_2'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'z')
                $session_c1['distributed_3'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->status == 'win')
                $session_c1['given'] += (int)($bid->amount * $bid->rate);
            continue;
        }
        if ($bid->session.$bid->innings == 'd1'){
            $session_d1['count'] += 1;
            $session_d1['collected'] += $bid->amount;
            if ($bid->slot == 'x')
                $session_d1['distributed_1'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'y')
                $session_d1['distributed_2'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'z')
                $session_d1['distributed_3'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->status == 'win')
                $session_d1['given'] += (int)($bid->amount * $bid->rate);
            continue;
        }
        if ($bid->session.$bid->innings == 'a2'){
            $session_a2['count'] += 1;
            $session_a2['collected'] += $bid->amount;
            if ($bid->slot == 'x')
                $session_a2['distributed_1'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'y')
                $session_a2['distributed_2'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'z')
                $session_a2['distributed_3'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->status == 'win')
                $session_a2['given'] += (int)($bid->amount * $bid->rate);
            continue;
        }
        if ($bid->session.$bid->innings == 'b2'){
            $session_b2['count'] += 1;
            $session_b2['collected'] += $bid->amount;
            if ($bid->slot == 'x')
                $session_b2['distributed_1'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'y')
                $session_b2['distributed_2'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->slot == 'z')
                $session_b2['distributed_3'] += (int)((int)$bid->amount * $bid->rate);
            if ($bid->status == 'win')
                $session_b2['given'] += (int)($bid->amount * $bid->rate);
            continue;
        }
    }
    foreach ($all_bids_winner as $bid){
        if(isset($user_bids[$bid->ref_id])){
            $user_bids[$bid->ref_id]['count']++;
            $user_bids[$bid->ref_id]['collected'] += $bid->amount;
            if ($bid->status == 'win')
                $user_bids[$bid->ref_id]['given'] += (int)($bid->amount * $bid->rate);
        }else{
            $obj = array();
            $obj['ref_id'] = $bid->ref_id;
            $obj['count'] = 1;
            $obj['collected'] = $bid->amount;
            if ($bid->status == 'win')
                $obj['given'] = (int)($bid->amount * $bid->rate);
            else
                $obj['given'] = 0;
            $user_bids[$bid->ref_id] = $obj;
        }
        $total_c += (int)$bid->amount;
        if ($bid->status == 'win')
            $total_d += (int)((int)$bid->amount * $bid->rate);
        $session_winner['count'] += 1;
        $session_winner['collected'] += $bid->amount;
        if ($bid->slot == 'T1')
            $session_winner['distributed_1'] += (int)((int)$bid->amount * $bid->rate);
        if ($bid->slot == 'T2')
            $session_winner['distributed_2'] += (int)((int)$bid->amount * $bid->rate);
        if ($bid->status == 'win')
            $session_winner['given'] += (int)($bid->amount * $bid->rate);
    }
    foreach ($all_bids_special as $bid){
        $total_c += (int)$bid->amount;
        if ($bid->status == 'win')
            $total_d += (int)((int)$bid->amount * $bid->rate);
        $session_special['count'] += 1;
        $session_special['collected'] += $bid->amount;
        if ($bid->slot == 'T1')
            $session_special['distributed_1'] += (int)((int)$bid->amount * $bid->rate);
        if ($bid->slot == 'T2')
            $session_special['distributed_2'] += (int)((int)$bid->amount * $bid->rate);
        if ($bid->status == 'win')
            $session_special['given'] += (int)($bid->amount * $bid->rate);
    }
    ?>
    <html lang="">
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
        <script src="../model_ui/header/script.js?version=<?php echo time();?>"></script>
        <script src="script.js?version=<?php echo time();?>"></script>
        <script src="../scripts/script.js?version=<?php echo time();?>"></script>
    </head>
    <body onload="fill_header();fill_footer()">
    <div id="header"></div>
    <div class="bid_container">
        <div class="sub-title"><?php echo $common->get_cookie('match_name');?></div>
        <div class="small-gap"></div>
        <table>
            <thead>
            <tr>
                <th>User</th>
                <th>Bids Placed</th>
                <th>Collected</th>
                <th>Given</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($user_bids as $bid){ ?>
                <tr>
                    <td><?php echo get_name($all_users, $bid['ref_id'])?></td>
                    <td><?php echo $bid['count']?></td>
                    <td><?php echo $bid['collected']?></td>
                    <td><?php echo $bid['given']?></td>
                </tr>
            <?php } ?>
            <thead>
            <tr>
                <th>Total</th>
                <th><?php echo count($all_bids_session) + count($all_bids_winner) + count($all_bids_special)?></th>
                <th><?php echo $total_c?></th>
                <th><?php echo $total_d?></th>
            </tr>
            </thead>
            </tbody>
        </table>
        <div class="sub-title"><?php echo "Total: ₹".$total_c." - ₹".$total_d." = ₹".($total_c - $total_d)?></div>
        <div class="match-detail">
            <div style="display: flex">
                <div class="bid_container" style="width: 50%;">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids Placed : <?php echo $session_a1['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collected : <?php echo "₹".$session_a1['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S1 : <?php echo "₹".$session_a1['distributed_1']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S2 : <?php echo "₹".$session_a1['distributed_2']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S3 : <?php echo "₹".$session_a1['distributed_3']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_a1['collected'] - $session_a1['given']); ?></div>
                    <a class="button" href="../admin/admin_match_session_dashboard.php?session=a1">Session A1</a>
                </div>
                <div class="bid_container" style="width: 50%">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids Placed : <?php echo $session_b1['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collected : <?php echo "₹".$session_b1['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S1 : <?php echo "₹".$session_b1['distributed_1']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S2 : <?php echo "₹".$session_b1['distributed_2']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S3 : <?php echo "₹".$session_b1['distributed_3']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_b1['collected'] - $session_b1['given']); ?></div>
                    <a class="button" href="../admin/admin_match_session_dashboard.php?session=b1">Session B1</a>
                </div>
            </div>
            <div class="separator"></div>
            <div style="display: flex">
                <div class="bid_container" style="width: 50%;">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids Placed : <?php echo $session_c1['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collected : <?php echo "₹".$session_c1['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S1 : <?php echo "₹".$session_c1['distributed_1']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S2 : <?php echo "₹".$session_c1['distributed_2']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S3 : <?php echo "₹".$session_c1['distributed_3']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_c1['collected'] - $session_c1['given']); ?></div>
                    <a class="button" href="../admin/admin_match_session_dashboard.php?session=c1">Session C1</a>
                </div>
                <div class="bid_container" style="width: 50%">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids Placed : <?php echo $session_d1['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collected : <?php echo "₹".$session_d1['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S1 : <?php echo "₹".$session_d1['distributed_1']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S2 : <?php echo "₹".$session_d1['distributed_2']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S3 : <?php echo "₹".$session_d1['distributed_3']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_d1['collected'] - $session_d1['given']); ?></div>
                    <a class="button" href="../admin/admin_match_session_dashboard.php?session=d1">Session D1</a>
                </div>
            </div>
            <div class="separator"></div>
            <div style="display: flex">
                <div class="bid_container" style="width: 50%;">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids Placed : <?php echo $session_a2['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collected : <?php echo "₹".$session_a2['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S1 : <?php echo "₹".$session_a2['distributed_1']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S2 : <?php echo "₹".$session_a2['distributed_2']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S3 : <?php echo "₹".$session_a2['distributed_3']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_a2['collected'] - $session_a2['given']); ?></div>
                    <a class="button" href="../admin/admin_match_session_dashboard.php?session=a2">Session A2</a>
                </div>
                <div class="bid_container" style="width: 50%">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids Placed : <?php echo $session_b2['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collected : <?php echo "₹".$session_b2['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S1 : <?php echo "₹".$session_b2['distributed_1']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S2 : <?php echo "₹".$session_b2['distributed_2']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given S3 : <?php echo "₹".$session_b2['distributed_3']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_b2['collected'] - $session_b2['given']); ?></div>
                    <a class="button" href="../admin/admin_match_session_dashboard.php?session=b2">Session B2</a>
                </div>
            </div>
            <div class="separator"></div>
            <div class="bid_container">
                <div class="match-detail" style="padding: 0.5rem 1rem">Bids Placed : <?php echo $session_winner['count']; ?></div>
                <div class="match-detail" style="padding: 0.5rem 1rem">Collected : <?php echo "₹".$session_winner['collected']; ?></div>
                <div class="match-detail" style="padding: 0.5rem 1rem">Given S1 : <?php echo "₹".$session_winner['distributed_1']; ?></div>
                <div class="match-detail" style="padding: 0.5rem 1rem">Given S2 : <?php echo "₹".$session_winner['distributed_2']; ?></div>
                <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_winner['collected'] - $session_winner['given']); ?></div>
                <a class="button" href="../admin/admin_match_winner_dashboard.php?session=winner">Winner</a>
            </div>
            <div class="separator"></div>
            <div class="bid_container">
                <div class="match-detail" style="padding: 0.5rem 1rem">Bids Placed : <?php echo $session_special['count']; ?></div>
                <div class="match-detail" style="padding: 0.5rem 1rem">Collected : <?php echo "₹".$session_special['collected']; ?></div>
                <div class="match-detail" style="padding: 0.5rem 1rem">Given S1 : <?php echo "₹".$session_special['distributed_1']; ?></div>
                <div class="match-detail" style="padding: 0.5rem 1rem">Given S2 : <?php echo "₹".$session_special['distributed_2']; ?></div>
                <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_special['collected'] - $session_special['given']); ?></div>
                <a class="button" href="../admin/admin_match_special_dashboard.php?session=winner">Special</a>
            </div>
        </div>
    </div>
    <div class="separator"></div>
    <div id="footer"></div>
    </body>
    </html>
<?php } else {
    $common->logout();
    $common->redirect_to('Cricket/');
}