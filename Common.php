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
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_scores/' . $series_id . '/' . $match_id . '/latest';
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
        for ($i = 0; $i < count($this_over); $i++) {
            if (str_contains($this_over[$i], 'w') || str_contains($this_over[$i], 'nb'))
                continue;
            $count++;
        }
        return $count;
    }

    public function is_eligible_for_session_bid($session, $current_over_id): bool
    {
        if (!in_array($session[1], [1, 2]))
            return false;
        if (!in_array($session[0], ['a', 'b', 'c', 'd']))
            return false;
        if ($session == 'winner')
            return false;
        if (!$this->is_valid_session_slot($session))
            return false;
        $bid_innings = $session[1];
        $session = $session[0];
        $eligible_overID = 0;
        if ($session == 'a') {
            $eligible_overID = ($bid_innings * 100) + 6;
        } elseif ($session == 'b') {
            $eligible_overID = ($bid_innings * 100) + 10;
        } elseif ($session == 'c') {
            $eligible_overID = ($bid_innings * 100) + 16;
        } elseif ($session == 'd') {
            $eligible_overID = ($bid_innings * 100) + 20;
        }
        if ($current_over_id == "") {
            $current_over_id = '999';
        }
        if ((int)$current_over_id < $eligible_overID) {
            return true;
        } else {
            return false;
        }
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
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_session_bid_book/" . $series_id . "/" . $match_id . "/" . $session . "/" . $room;
        $book = json_decode($this->get_response_from_url($url));
        if (isset($book->error))
            return [2.0, 2.0, 2.0];
        $x = $book->collected * 0.8 + $amount;
        $r1 = (int)($r - 1.5);
        $r2 = (int)($r + 1.5);
        $a = max($book->runs[$r1 - 1], $book->runs[$r1 - 2], $book->runs[$r1 - 3], $book->runs[$r1 - 4], $book->runs[$r1 - 5]);
        $b = 0;
        for ($i = $r1; $i <= $r2; $i++)
            $b = max($b, $book->runs[$i]);
        $c = max($book->runs[$r2 + 1], $book->runs[$r2 + 2], $book->runs[$r2 + 3], $book->runs[$r2 + 4], $book->runs[$r2 + 5]);

        $ga = max(($x - $a), 0.1);
        $gb = max(($x - $b), 0.1);
        $gc = max(($x - $c), 0.1);
        $g = $ga + $gb + $gc;

        $ra = $ga / $g;
        $rb = $gb / $g;
        $rc = $gc / $g;

        $f = 6 / ($ra + $rb + $rc);

        $ra *= $f;
        $rb *= $f;
        $rc *= $f;

        return [$ra, $rb, $rc];
    }

    public function get_unique_bid_id(string $type): int
    {
        for ($i = 0; $i < 100; $i++) {
            $new_bid_id = mt_rand(10000000, 99999999);
            if (isset($this->get_bid_from_bid_id($new_bid_id, $type)->error))
                return $new_bid_id;
        }
        return -1;
    }

    public function get_bid_from_bid_id($bid_id, $type)
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_bid_details/" . $type . "/" . $bid_id;
        return json_decode($this->get_response_from_url($url));
    }

    public function isValidSession($session): bool
    {
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
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . "Cricket/internal/GetSessionSlotDetails.php?match_id=" . $match_id . "&series_id=" . $series_id . "&session=" . $session . "&amount=" . $amount . "&room=" . $room;
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
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/save_user_bid';
        $json_bid_data = json_encode($bid_data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json_bid_data)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_bid_data);
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
        if ($session[0] == 'a')
            return 6;
        elseif ($session[0] == 'b')
            return 10;
        elseif ($session[0] == 'c')
            return 16;
        elseif ($session[0] == 'd')
            return 20;
        return 0;
    }

    function get_all_bids_from_match(string $series_id, string $match_id, string $type, int $room): array
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_match_bids/" . $series_id . "/" . $match_id . "/" . $type . "/" . $room;
        return json_decode($this->get_response_from_url($url));
    }

    public function get_winner_rates($all_bids, $amount): array
    {
        $x = 0.0;
        $a = 0.0;
        $b = 0.0;
        foreach ($all_bids as $bid) {
            $x += (float)($bid->amount);
        }
        $x = $x - ($x / 100) + $amount;

        foreach ($all_bids as $bid) {
            if ($bid->slot == 'x')
                $a += (float)($bid->amount);
        }

        foreach ($all_bids as $bid) {
            if ($bid->slot == 'y')
                $b += (float)($bid->amount);
        }

        $ga = max(($x - $a), 0.0);
        $gb = max(($x - $b), 0.0);
        $g = $ga + $gb;


        $ra = $ga / $g;
        $rb = $gb / $g;

        $f = 4 / ($ra + $rb);

        $ra *= $f;
        $rb *= $f;

        return [$ra, $rb];
    }

    public function get_match_winner_bid_bookie_details(string $series_id, $match_id, int $amount, int $room)
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . "Cricket/internal/GetWinnerSlotDetails.php?match_id=" . $match_id . "&series_id=" . $series_id . "&amount=" . $amount . "&room=" . $room;
        $response = $this->get_response_from_url($url);
        return json_decode($response);
    }

    public function insert_new_winner_bid_to_db($bid_id, $ref_id, $series_id, $match_id, $slot,
                                                $rate, $amount, $bid_name, $room): bool|string
    {
        $bid_data = array(
            "id" => $bid_id,
            "bid_id" => $bid_id,
            "ref_id" => $ref_id,
            "series_id" => $series_id,
            "match_id" => $match_id,
            "slot" => $slot,
            "rate" => $rate,
            "amount" => $amount,
            "status" => "placed",
            'type' => 'winner',
            'bid_name' => $bid_name,
            'room' => $room
        );
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/save_user_bid';
        $json_bid_data = json_encode($bid_data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json_bid_data)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_bid_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            return false;
        }
        curl_close($ch);
        return $response;
    }

    public function recharge_user($recharge_id, $from_ref_id, $to_ref_id, $amount)
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/recharge/" . $recharge_id . "/" . $from_ref_id . "/" . $to_ref_id . "/" . $amount;
        return json_decode($this->get_response_from_url($url));
    }

    public function get_unique_recharge_id(): int
    {
        for ($i = 0; $i < 100; $i++) {
            $new_recharge_id = mt_rand(10000000, 99999999);
            $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_recharge_details/" . $new_recharge_id;
            $recharge = json_decode($this->get_response_from_url($url));
            if (!isset($recharge->id))
                return $new_recharge_id;
        }
        return -1;
    }
    public function get_all_bids(string $series_id, string $match_id, string $type)
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_match_bids/" . $series_id . "/" . $match_id . "/" . $type . "/any";
        return json_decode($this->get_response_from_url($url));
    }
    public function get_all_users()
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_all_users";
        return json_decode($this->get_response_from_url($url));
    }
    public function get_all_matches(): array
    {
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_all_matches';
        $matches = json_decode($this->get_response_from_url($url));
        usort($matches, function($a, $b) {
            return strcmp($a->id,$b->id) * -1;
        });
        return $matches;
    }
    public function get_match_name_match_id($all_matches, $match_id, $series_id):string
    {
        foreach ($all_matches as $match){
            if($match->match_id==$match_id && $match->series_id==$series_id){
                return trim(explode('--', $match->match_name)[0]);
            }
        }
        return "";
    }
    public function get_user_from_users($all_users, $ref_id): ?string
    {
        foreach ($all_users as $user) {
            if ($user->ref_id == $ref_id) {
                return $user->fname." ".$user->lname;
            }
        }
        return null;
    }
    public function get_user_from_phone(mixed $phone)
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/login/phone/" . $phone;
        return json_decode($this->get_response_from_url($url));
    }

    public function validate_login(mixed $phone, mixed $password)
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/login/".$phone."/".$password;
        return json_decode($this->get_response_from_url($url));
    }

    public function is_new_phone_number(mixed $phone): bool
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/login/phone/".$phone;
        return !isset(json_decode($this->get_response_from_url($url))->id);
    }

    public function insert_new_user(mixed $fname, mixed $lname, mixed $phone, mixed $password, mixed $ref_id, mixed $email, mixed $parent_ref_id, string $status): bool
    {
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/save_new_user';
        $data = array(
            "id" => $ref_id,
            "email" => $email,
            "fname" => $fname,
            "lname" => $lname,
            "parent_ref_id" => $parent_ref_id,
            "password" => $password,
            "phone" => $phone,
            "ref_id" => $ref_id,
            "status" => $status,
            "type" => 'user',
        );
        $json_data = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($json_data)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            return false;
        }
        curl_close($ch);
        return true;
    }

    public function set_cookie(string $cookie_name, mixed $cookie_value): void
    {
        setcookie($cookie_name, $cookie_value, time() + (3600), "/");
    }

    public function validate_unique_ref_id(int $ref_id): bool
    {
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/validate_ref_id/' . $ref_id;
        return json_decode($this->get_response_from_url($url))->result == true;
    }
}