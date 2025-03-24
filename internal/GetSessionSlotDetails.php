<?php
header('Content-Type: application/json');
include "../Common.php";
include "SlotScores.php";
include "Data.php";
$data = new Data();
$common = new Common();
$scores = new Scores($data);

$match_id = $_GET["match_id"];
$series_id = $_GET["series_id"];
$session = $_GET["session"];
$room = intval($_GET["room"]);
$amount = (float)$_GET['amount'];

$scorecard = $common->get_scorecard_latest($series_id, $match_id);
$run = $session[1] == 1 ? $scorecard->team1_score->runs : $scorecard->team2_score->runs;
if ($session[1] == 1){
    if($scorecard->innings == 1)
        $balls = $common->get_total_balls($scorecard->over);
    else
        $balls = 120;
}else{
    if($scorecard->innings == 1)
        $balls = 0;
    else
        $balls = $common->get_total_balls($scorecard->over);
}
if($session[0] == 'a')
    $balls_left = 36 - $balls;
if($session[0] == 'b')
    $balls_left = 60 - $balls;
if($session[0] == 'c')
    $balls_left = 96 - $balls;
if($session[0] == 'd')
    $balls_left = 120 - $balls;

if($common->is_eligible_for_session_bid($session, $scorecard->over_id) && $balls_left > 6){
    $predicted_runs = $scores->get_slot_runs($session[1], $scorecard, $session[0]);
    $rates = $common->get_rates($series_id, $match_id, $session, $room, $amount, $predicted_runs);

    $output = array(
        "predicted_runs_a" => (int)($predicted_runs - 1.5),
        "predicted_runs_b" => (int)($predicted_runs + 1.5),
        "rate_1" => $rates[0],
        "rate_2" => $rates[1],
        "rate_3" => $rates[2],
        "runs" => $run,
        "balls_left" => $balls_left,
        'session_name' => "Innings ".$session[1]." : Over ".
            ($session[0] == 'a' ? '1 to 6' : ($session[0] == 'b' ? '7 to 10' : ($session[0] == 'c' ? '11 to 16' : ($session[0] == 'd' ? '17 to 20' : ' - - '))))
    );
    echo json_encode($output);
}
else {
    echo json_encode(array("error" => "Biding Closed For This Slot"));
}
?>