<?php

class Common
{
    public function is_user_an_agent(): bool
    {
        return $this->get_cookie('user_type') == "agent";
    }
    public function is_user_an_admin(): bool
    {
        return $this->get_cookie('user_type') == "admin";
    }
    public function get_cookie(string $name): string
    {
        return $_COOKIE[$name] ?? "";
    }
    public function is_user_logged_in(): bool
    {
        return $this->get_cookie('ref_id') != "";
    }

    public function logout()
    {
    }
    public function redirect_to(string $url)
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . $url;
        echo $url;

        header("Location: $url");
    }
    public function setCookie(string $cookie_name, string $cookie_value): void
    {
        setcookie($cookie_name, $cookie_value, time() + (3600), "/");
    }

    public function get_scorecard_latest($series_id, $match_id)
    {
        $url =  'https://om8zdfeo2h.execute-api.ap-south-1.amazonaws.com/scores/' . $series_id . '/' . $match_id . '/latest';
        return json_decode($this->get_response_from_url($url));
    }
    private function get_response_from_url($url): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return response as string
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    public function get_valid_balls(array $this_over): int
    {
        $count = 0;
        for ($i=0;$i<count($this_over);$i++) {
            if (str_contains($this_over[$i], 'w') || str_contains($this_over[$i], 'nb'))
                continue;
            $count++;
        }
        return $count;
    }
    public function is_eligible_for_session_bid($session, $current_over_id): bool {
        if (!in_array($session[1], [1,2]))
            return false;
        if(!in_array($session[0], ['a', 'b', 'c', 'd']))
            return false;
        if ($session == 'winner')
            return false;
        if (!$this->is_valid_session_slot($session))
            return false;
        $bid_innings =  $session[1];
        $session = $session[0];
        $eligible_overID = 0;
        if($session == 'a'){
            $eligible_overID = ($bid_innings * 100) + 6;}
        elseif($session == 'b'){
            $eligible_overID = ($bid_innings * 100) + 10;}
        elseif($session == 'c'){
            $eligible_overID = ($bid_innings * 100) + 16;}
        elseif ($session == 'd'){
            $eligible_overID = ($bid_innings * 100) + 20;}
        if($current_over_id == ""){
            $current_over_id = '999';
        }
        if((int)$current_over_id < $eligible_overID){
            return true;}
        else{
            return false;}
    }
    public function is_valid_session_slot(string $slot): bool
    {
        if (strlen($slot) != 2)
            return false;
        if ($slot[0] == 'a' || $slot[0] == 'b' || $slot[0] == 'c' || $slot[0] == 'd') {
            if ($slot[1] == 1 || $slot[1] == 2) {
                return true;
            }
        }
        return false;
    }
    public function get_rates(string $series_id, string $match_id, string $session, int $room, float $amount, float $r): array
    {
        $url = "https://om8zdfeo2h.execute-api.ap-south-1.amazonaws.com/get_session_bid_book/".$series_id."/".$match_id."/".$session."/".$room;
        $book = json_decode($this->get_response_from_url($url));
        if (isset($book->error))
            return [2.0, 2.0, 2.0];
        $x = $book->collected * 0.8 + $amount;
        $r1 = (int)($r - 1.5);
        $r2 = (int)($r + 1.5);
        $a = max($book->runs[$r1 - 1], $book->runs[$r1 - 2], $book->runs[$r1 - 3], $book->runs[$r1 - 4], $book->runs[$r1 - 5]);
        $b = 0;
        for($i = $r1; $i <= $r2; $i++)
            $b = max($b, $book->runs[$i]);
        $c = max($book->runs[$r2 + 1], $book->runs[$r2 + 2], $book->runs[$r2 + 3], $book->runs[$r2 + 4], $book->runs[$r2 + 5]);

        $ga = max(($x - $a), 0.1);
        $gb = max(($x - $b), 0.1);
        $gc = max(($x - $c), 0.1);
        $g = $ga + $gb + $gc;

        $ra = $ga/$g;
        $rb = $gb/$g;
        $rc = $gc/$g;

        $f = 6 / ($ra + $rb + $rc);

        $ra *= $f;
        $rb *= $f;
        $rc *= $f;

        return [$ra, $rb, $rc];
    }
    public function get_unique_bid_id(string $type): int
    {
        for ($i=0; $i<100; $i++){
            $new_bid_id = mt_rand(10000000, 99999999);
            if (isset($this->get_bid_from_bid_id($new_bid_id, $type)->error))
                return $new_bid_id;
        }
        return -1;
    }
    public function get_bid_from_bid_id($bid_id, $type)
    {
        $url = "https://om8zdfeo2h.execute-api.ap-south-1.amazonaws.com/get_bid/" . $type . "/" .$bid_id;
        return json_decode($this->get_response_from_url($url));
    }
    public function isValidSession($session){
        if (strlen($session) != 2)
            return false;
        if ($session[0] == 'a' || $session[0] == 'b' || $session[0] == 'c' || $session[0] == 'd') {
            if ($session[1] == 1 || $session[1] == 2) {
                return true;
            }
        }
        return false;
    }
    public function get_session_bid_bookie_details(string $series_id, $match_id, string $session, float $amount, int $room)
    {
        $url = "localhost/Cricket/internal/GetSessionSlotDetails.php?match_id=".$match_id."&series_id=".$series_id."&session=".$session."&amount=".$amount."&room=".$room;
        $response = $this->get_response_from_url($url);
        return json_decode($response);
    }
    public function insert_new_session_bid_to_db(int $bid_id, string $ref_id, string $series_id, string $match_id, string $session,
                                                 string $slot, int $runs_min, int $runs_max, float $rate, float $amount,
                                                 string $bid_name, string $room): bool|string
    {
        if ($rate == null)
            return false;
        $bid_data = array(
            "id" => $bid_id,
            "bid_id" => $bid_id,
            "ref_id" => $ref_id,
            "series_id" => $series_id,
            "match_id" => $match_id,
            "innings" => $session[1],
            "session" => $session[0],
            "slot" => $slot,
            "runs_min" => $runs_min,
            "runs_max" => $runs_max,
            "rate" => $rate,
            "amount" => $amount,
            "status" => "placed",
            'type' => 'session',
            'bid_name' => $bid_name,
            'room' => $room
        );
        $url = 'https://om8zdfeo2h.execute-api.ap-south-1.amazonaws.com/save_new_bid';
        $json_bid_data = json_encode($bid_data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($json_bid_data)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$json_bid_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            return false;
        }
        curl_close($ch);
        return $response;
    }

    public function get_end_over_from_session(string $session): int
    {
        if($session[0] == 'a')
            return 6;
        elseif($session[0] == 'b')
            return 10;
        elseif($session[0] == 'c')
            return 16;
        elseif($session[0] == 'd')
            return 20;
        return 0;
    }
}