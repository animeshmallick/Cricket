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
$question_id = $_GET["question_id"];
$room = intval($_GET["room"]);
$amount = (float)$_GET['amount'];

$question = $common->get_special_question($question_id);
$scorecard = $common->get_scorecard_latest($series_id, $match_id);
if($question !== null && ($scorecard->innings == 1 || ($scorecard->team1_score->runs - $scorecard->team2_score->runs > 15))){
    $special_bids_all = $common->get_all_bids_from_match($series_id, $match_id, 'special', $room);
    $special_bids = array();
    foreach ($special_bids_all as $bid) {
        if ($bid->question_id == $question_id) {
            $special_bids[] = $bid;
        }
    }
    $x = 0;
    foreach ($special_bids as $bid)
        $x += $bid->amount;
    $y = array_fill(0, count($question->options), 0.0);
    foreach ($special_bids as $bid)
        $y[$bid->option_id] += (1 + $bid->rate) * $bid->amount;
    foreach ($y as $i => $v) {
        $y[$i] = max($x - $v, 0.0);
    }
    foreach ($y as $i => $v){
        $y[$i] = min($v/$amount, 1.5);
    }
    $flag = false;
    foreach ($y as $v){
        if ($v <= 0.0)
            $flag = true;
    }
    if ($flag) {
        $y = array_fill(0, count($question->options), 0.9);
    }
    $output = array(
        "question" => $question->question_name,
        "options" => $question->options,
        "rates" => $y,
        "collected" => $x
    );
    echo json_encode($output);
}
else {
    echo json_encode(array("error" => "Biding Closed For This Slot"));
}
?>