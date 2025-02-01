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
}