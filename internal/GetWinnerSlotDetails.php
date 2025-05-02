<?php
session_start();
header('Content-Type: application/json');
include "../Common.php";
include "SlotScores.php";
include "Data.php";
$data = new Data();
$common = new Common();
$scores = new Scores($data);

$match_id = $_GET["match_id"];
$series_id = $_GET["series_id"];
$amount = (float)$_GET['amount'];
$room = intval($_GET['room']);

$scorecard = $common->get_scorecard_latest($series_id, $match_id);

$all_bids = $common->get_all_bids_from_match($series_id, $match_id, 'winner', $room);
$rates = $common->get_winner_rates($all_bids, $amount);

if ($scorecard->innings == 1 || ($scorecard->balls_played < 114 && ($scorecard->team2_score->runs == 0 || ($scorecard->team1_score->runs - $scorecard->team2_score->runs) > 15) && $scorecard->team2_score->wickets < 10)) {
    $output = array(
        "team_a" => $scorecard->teams[0],
        "team_b" => $scorecard->teams[1],
        "rate_1" => $rates[0],
        "rate_2" => $rates[1],
        "target" => $scorecard->teams[1] . " needs " . ($scorecard->team1_score->runs - $scorecard->team2_score->runs + 1) . " runs in "
            . (120 - $scorecard->balls_played) . " balls",
        "innings" => $scorecard->innings
    );
}else{
    $output = array("error" => "Session Closed for Bidding");
}
echo json_encode($output);
?>