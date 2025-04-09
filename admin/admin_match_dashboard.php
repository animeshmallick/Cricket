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
function is_user_an_admin($all_users, string $ref_id): bool
{
    foreach ($all_users as $user){
        if ($user->ref_id == $ref_id && $user->type == 'admin')
            return true;
    }
    return false;
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
    $session_a1['given'] = 0;

    $session_b1 = $session_a1;
    $session_c1 = $session_a1;
    $session_d1 = $session_a1;
    $session_a2 = $session_a1;
    $session_b2 = $session_a1;
    $session_c2 = $session_a1;
    $session_d2 = $session_a1;
    $session_winner = $session_a1;
    $session_special = $session_a1;
    $total_c = 0;
    $total_d = 0;
    foreach ($all_bids_session as $bid){
        if(isset($user_bids[$bid->ref_id])){
            $user_bids[$bid->ref_id]['count']++;
            $user_bids[$bid->ref_id]['collected'] += $bid->amount;
            if ($bid->status == 'win')
                $user_bids[$bid->ref_id]['given'] += (int)($bid->amount * (1 + $bid->rate));
        }else{
            $obj = array();
            $obj['ref_id'] = $bid->ref_id;
            $obj['count'] = 1;
            $obj['collected'] = $bid->amount;
            if ($bid->status == 'win')
                $obj['given'] = (int)($bid->amount * (1 + $bid->rate));
            else
                $obj['given'] = 0;
            $user_bids[$bid->ref_id] = $obj;
        }
        if(!is_user_an_admin($all_users, $bid->ref_id)) {
            $total_c += (int)$bid->amount;

            if ($bid->status == 'win')
                $total_d += (int)((int)$bid->amount * (1 + $bid->rate));
            if ($bid->session . $bid->innings == 'a1') {
                $session_a1['count'] += 1;
                $session_a1['collected'] += $bid->amount;
                if ($bid->status == 'win')
                    $session_a1['given'] += (int)($bid->amount * (1 + $bid->rate));
                continue;
            }
            if ($bid->session . $bid->innings == 'b1') {
                $session_b1['count'] += 1;
                $session_b1['collected'] += $bid->amount;
                if ($bid->status == 'win')
                    $session_b1['given'] += (int)($bid->amount * (1 + $bid->rate));
                continue;
            }
            if ($bid->session . $bid->innings == 'c1') {
                $session_c1['count'] += 1;
                $session_c1['collected'] += $bid->amount;
                if ($bid->status == 'win')
                    $session_c1['given'] += (int)($bid->amount * (1 + $bid->rate));
                continue;
            }
            if ($bid->session . $bid->innings == 'd1') {
                $session_d1['count'] += 1;
                $session_d1['collected'] += $bid->amount;
                if ($bid->status == 'win')
                    $session_d1['given'] += (int)($bid->amount * (1 + $bid->rate));
                continue;
            }
            if ($bid->session . $bid->innings == 'a2') {
                $session_a2['count'] += 1;
                $session_a2['collected'] += $bid->amount;
                if ($bid->status == 'win')
                    $session_a2['given'] += (int)($bid->amount * (1 + $bid->rate));
                continue;
            }
            if ($bid->session . $bid->innings == 'b2') {
                $session_b2['count'] += 1;
                $session_b2['collected'] += $bid->amount;
                if ($bid->status == 'win')
                    $session_b2['given'] += (int)($bid->amount * (1 + $bid->rate));
                continue;
            }
            if ($bid->session . $bid->innings == 'c2') {
                $session_c2['count'] += 1;
                $session_c2['collected'] += $bid->amount;
                if ($bid->status == 'win')
                    $session_c2['given'] += (int)($bid->amount * (1 + $bid->rate));
                continue;
            }
            if ($bid->session . $bid->innings == 'd2') {
                $session_d2['count'] += 1;
                $session_d2['collected'] += $bid->amount;
                if ($bid->status == 'win')
                    $session_d2['given'] += (int)($bid->amount * (1 + $bid->rate));
                continue;
            }
        }
        if ($bid->session . $bid->innings == 'a1')
            $session_a1['count'] += 1;
        if ($bid->session . $bid->innings == 'b1')
            $session_b1['count'] += 1;
        if ($bid->session . $bid->innings == 'c1')
            $session_c1['count'] += 1;
        if ($bid->session . $bid->innings == 'd1')
            $session_d1['count'] += 1;
    }
    foreach ($all_bids_winner as $bid){
        if(isset($user_bids[$bid->ref_id])){
            $user_bids[$bid->ref_id]['count']++;
            $user_bids[$bid->ref_id]['collected'] += $bid->amount;
            if ($bid->status == 'win')
                $user_bids[$bid->ref_id]['given'] += (int)($bid->amount * (1 + $bid->rate));
        }else{
            $obj = array();
            $obj['ref_id'] = $bid->ref_id;
            $obj['count'] = 1;
            $obj['collected'] = $bid->amount;
            if ($bid->status == 'win')
                $obj['given'] = (int)($bid->amount * (1 + $bid->rate));
            else
                $obj['given'] = 0;
            $user_bids[$bid->ref_id] = $obj;
        }
        $session_winner['count'] += 1;
        if(!is_user_an_admin($all_users, $bid->ref_id)) {
            $total_c += (int)$bid->amount;
            if ($bid->status == 'win')
                $total_d += (int)($bid->amount * (1 + $bid->rate));
            $session_winner['collected'] += $bid->amount;
            if ($bid->status == 'win')
                $session_winner['given'] += (int)($bid->amount * (1 + $bid->rate));
        }
    }
    foreach ($all_bids_special as $bid){
        if(isset($user_bids[$bid->ref_id])){
            $user_bids[$bid->ref_id]['count']++;
            $user_bids[$bid->ref_id]['collected'] += $bid->amount;
            if ($bid->status == 'win')
                $user_bids[$bid->ref_id]['given'] += (int)($bid->amount * (1 + $bid->rate));
        }else{
            $obj = array();
            $obj['ref_id'] = $bid->ref_id;
            $obj['count'] = 1;
            if(!is_user_an_admin($all_users, $bid->ref_id)) {
                $obj['collected'] = $bid->amount;
                if ($bid->status == 'win')
                    $obj['given'] = (int)($bid->amount * (1 + $bid->rate));
                else
                    $obj['given'] = 0;
            }else{
                $obj['collected'] = 0;
                $obj['given'] = 0;
            }
            $user_bids[$bid->ref_id] = $obj;
        }
        $session_special['count'] += 1;
        if(!is_user_an_admin($all_users, $bid->ref_id)) {
            $total_c += (int)$bid->amount;
            if ($bid->status == 'win')
                $total_d += (int)($bid->amount * (1 + $bid->rate));
            $session_special['collected'] += $bid->amount;
            if ($bid->status == 'win')
                $session_special['given'] += (int)($bid->amount * (1 + $bid->rate));
        }
    }
    ?>
    <html lang="">
    <head>
        <title>Admin - MatchDashboard</title>
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
    <body onload="fill_header();fill_footer()">
    <div id="header"></div>
    <div class="bid_container" style="background: transparent">
        <a class="button" href="../match/index.php?series_id=<?= $series_id?>&match_id=<?= $match_id ?>">Go To Match Page</a>
        <div class="separator"></div>
        <table>
            <thead>
            <div class="sub-title">Players Bid</div>
            <tr>
                <th>User</th>
                <th>Bids Placed</th>
                <th>Collected</th>
                <th>Given</th>
                <th>Profit</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($user_bids as $bid){ ?>
                <tr>
                    <td><?php echo get_name($all_users, $bid['ref_id'])?></td>
                    <td><?php echo $bid['count']?></td>
                    <td><?php echo $bid['collected']?></td>
                    <td><?php echo $bid['given']?></td>
                    <td><?php echo ($bid['given'] - $bid['collected']); ?></td>
                </tr>
            <?php } ?>
            <thead>
            <tr>
                <th>Total</th>
                <th><?php echo count($all_bids_session) + count($all_bids_winner) + count($all_bids_special)?></th>
                <th><?php echo $total_c?></th>
                <th><?php echo $total_d?></th>
                <th><?= $total_c - $total_d ?></th>
            </tr>
            </thead>
            </tbody>
        </table>
        <?php if($total_c > 0){ ?>
            <div class="sub-title"><?php echo "Total: ₹".$total_c." - ₹".$total_d." = ₹".($total_c - $total_d)." (".round((($total_c - $total_d)/$total_c*100.0),2)."%)"?></div>
        <?php } ?>
        <div class="separator"></div>
        <div class="match-detail">
            <div class="sub-title">1st Innings</div>
            <div style="display: flex">
                <div class="bid_container" style="width: 50%;" onclick="redirect_to('Cricket/admin/admin_match_session_dashboard.php?session=a1')">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids : <?php echo $session_a1['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collect : <?php echo "₹".$session_a1['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given: <?php echo "₹".$session_a1['given']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_a1['collected'] - $session_a1['given']); ?></div>
                </div>
                <div class="bid_container" style="width: 50%"  onclick="redirect_to('Cricket/admin/admin_match_session_dashboard.php?session=b1')">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids : <?php echo $session_b1['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collect : <?php echo "₹".$session_b1['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given: <?php echo "₹".$session_b1['given']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_b1['collected'] - $session_b1['given']); ?></div>
                </div>
            </div>
            <div class="separator"></div>
            <div style="display: flex">
                <div class="bid_container" style="width: 50%;"  onclick="redirect_to('Cricket/admin/admin_match_session_dashboard.php?session=c1')">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids : <?php echo $session_c1['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collect : <?php echo "₹".$session_c1['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given: <?php echo "₹".$session_c1['given']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_c1['collected'] - $session_c1['given']); ?></div>
                </div>
                <div class="bid_container" style="width: 50%"  onclick="redirect_to('Cricket/admin/admin_match_session_dashboard.php?session=d1')">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids : <?php echo $session_d1['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collect : <?php echo "₹".$session_d1['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given: <?php echo "₹".$session_d1['given']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_d1['collected'] - $session_d1['given']); ?></div>
                </div>
            </div>
            <div class="separator"></div>
            <div class="sub-title">2nd Innings</div>
            <div style="display: flex">
                <div class="bid_container" style="width: 50%;"  onclick="redirect_to('Cricket/admin/admin_match_session_dashboard.php?session=a2')">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids : <?php echo $session_a2['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collect : <?php echo "₹".$session_a2['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given: <?php echo "₹".$session_a2['given']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_a2['collected'] - $session_a2['given']); ?></div>
                </div>
                <div class="bid_container" style="width: 50%"  onclick="redirect_to('Cricket/admin/admin_match_session_dashboard.php?session=b2')">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids : <?php echo $session_b2['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collect : <?php echo "₹".$session_b2['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given: <?php echo "₹".$session_b2['given']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_b2['collected'] - $session_b2['given']); ?></div>
                </div>
            </div>
            <div class="separator"></div>
            <div style="display: flex">
                <div class="bid_container" style="width: 50%;"  onclick="redirect_to('Cricket/admin/admin_match_session_dashboard.php?session=c2')">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids : <?php echo $session_c2['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collect : <?php echo "₹".$session_c2['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given: <?php echo "₹".$session_c2['given']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_c2['collected'] - $session_c2['given']); ?></div>
                </div>
                <div class="bid_container" style="width: 50%"  onclick="redirect_to('Cricket/admin/admin_match_session_dashboard.php?session=d2')">
                    <div class="match-detail" style="padding: 0.3rem 1rem">Bids : <?php echo $session_d2['count']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Collect : <?php echo "₹".$session_d2['collected']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Given: <?php echo "₹".$session_d2['given']; ?></div>
                    <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_d2['collected'] - $session_d2['given']); ?></div>
                </div>
            </div>
            <div class="separator"></div>
            <div class="separator"></div>
            <div class="sub-title">Winner</div>
            <div class="bid_container" onclick="redirect_to('Cricket/admin/admin_match_winner_dashboard.php?session=winner')">
                <div class="match-detail" style="padding: 0.5rem 1rem">Bids : <?php echo $session_winner['count']; ?></div>
                <div class="match-detail" style="padding: 0.5rem 1rem">Collect : <?php echo "₹".$session_winner['collected']; ?></div>
                <div class="match-detail" style="padding: 0.5rem 1rem">Given: <?php echo "₹".$session_winner['given']; ?></div>
                <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_winner['collected'] - $session_winner['given']); ?></div>
            </div>
            <div class="separator"></div>
            <div class="sub-title">Specials</div>
            <div class="bid_container" onclick="redirect_to('Cricket/admin/admin_match_special_dashboard.php?session=special')">
                <div class="match-detail" style="padding: 0.5rem 1rem">Bids : <?php echo $session_special['count']; ?></div>
                <div class="match-detail" style="padding: 0.5rem 1rem">Collect : <?php echo "₹".$session_special['collected']; ?></div>
                <div class="match-detail" style="padding: 0.5rem 1rem">Given: <?php echo "₹".$session_special['given']; ?></div>
                <div class="match-detail" style="padding: 0.3rem 1rem">Profit : <?php echo "₹".($session_special['collected'] - $session_special['given']); ?></div>
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