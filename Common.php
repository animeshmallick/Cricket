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
        return $this->get_cookie('ref_id') != null &&
            $this->get_cookie('ref_id') != "null" &&
            strlen($this->get_cookie('ref_id')) > 0;
    }

    public function logout(): void
    {
        $this->clear_all_cookies();
    }
    function clear_all_cookies(): void
    {
        $this->delete_cookie('ref_id');
        $this->delete_cookie('fname');
        $this->delete_cookie('lname');
        $this->delete_cookie('user_type');
        $this->delete_cookie('show_tour');
        $this->delete_cookie('match_id');
        $this->delete_cookie('series_id');
        $this->delete_cookie('ghost_ref_id');
        $this->delete_cookie('ghost_fname');
        $this->delete_cookie('ghost_lname');
        $this->delete_cookie('ghost_mode');
    }
    function delete_cookie($name): void
    {
        setcookie($name, "", time() - 3600, "/");
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
        setcookie($cookie_name, $cookie_value, time() + (15*60), "/");

    }

    public function get_scorecard_latest($series_id, $match_id)
    {
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_scorecard/' . $series_id . '/' . $match_id;
        return json_decode($this->get_response_from_url($url));
    }

    private function get_response_from_url($url): string
    {
        $ref_id = $this->get_cookie('ref_id');
        if ($ref_id == null || $ref_id == "null" || strlen($ref_id) == 0)
            $ref_id = 'Unknown';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return response as string
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['ref_id: '.$ref_id]);
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
            $eligible_overID = ($bid_innings * 100) + 15;
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
        $x = 100;
        $a = 0;
        $b = 0;
        if(isset($book->error))
            return [0.1, 0.1];
        else if (isset($book->msg) && str_contains($book->msg, "No Bids")){
            $x = min(100, $amount);
        }else {
            $x = $book->collected;
            $ai = 0;
            $flag = false;
            for ($i = min($r-36,0); $i < $r; $i++) {
                $tmp = $book->runs[$i];
                if($tmp > 0){
                    $a += $tmp;
                    $ai++;
                    $flag = true;
                }else{
                    if($flag)
                        $ai++;
                }
            }
            if($ai > 0)
                $a /= $ai;
            else
                $a = 0;
            $bi = 0;
            $flag = false;
            for ($i = $r + 1; $i < min(count($book->runs),$r+36); $i++) {
                $tmp = $book->runs[$i];
                if($tmp > 0){
                    $b += $tmp;
                    $bi++;
                    $flag = true;
                }else{
                    if($flag)
                        $bi++;
                }
            }
            if($bi > 0)
                $b /= $bi;
            else
                $b = 0;
        }
        $rates = $this->calculate_rates($x, $a, $b, $book->count ?? 0, $amount, true);
        return $rates;
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
                                                 string $bid_name, string $room, string $session_id): bool|string
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
            'room' => $room,
            'session_id' => $session_id
        );
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/save_user_bid';
        $json_bid_data = json_encode($bid_data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($json_bid_data), 'ref_id: '.$ref_id));
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

    function get_all_bids_from_match(string $series_id, string $match_id, string $type, int $room)
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_match_bids/" . $series_id . "/" . $match_id . "/" . $type . "/" . $room;
        return json_decode($this->get_response_from_url($url));
    }

    public function get_winner_rates($all_bids, $amount): array
    {
        $x = 80;
        $a = 0.0;
        $b = 0.0;
        foreach ($all_bids as $bid) {
            $x += (float)($bid->amount);
        }

        foreach ($all_bids as $bid) {
            if ($bid->slot == 'x')
                $a += (float)($bid->amount * (1 + $bid->rate));
        }

        foreach ($all_bids as $bid) {
            if ($bid->slot == 'y')
                $b += (float)($bid->amount * (1 + $bid->rate));
        }
        $rates = $this->calculate_rates($x, $a, $b, count($all_bids), $amount, true);

        // TODO: Change Rate Based on Wins
        $rates[0] = max($rates[0], 0);
        $rates[1] = max($rates[1], 0);

        return $rates;
    }

    public function get_match_winner_bid_bookie_details(string $series_id, $match_id, int $amount, int $room)
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . "Cricket/internal/GetWinnerSlotDetails.php?match_id=" . $match_id . "&series_id=" . $series_id . "&amount=" . $amount . "&room=" . $room;
        $response = $this->get_response_from_url($url);
        return json_decode($response);
    }
    public function get_special_bid_bookie_details(string $series_id, $match_id, int $amount, int $room, int $question_id)
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . "Cricket/internal/GetSpecialSlotDetails.php?match_id=" . $match_id . "&series_id=" . $series_id . "&amount=" . $amount . "&room=" . $room . "&question_id=" . $question_id;
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json_bid_data), 'ref_id: '.$ref_id));
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
    public function get_all_users_with_balance()
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_all_users?with_balance=true";
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
    public function get_user_details_from_users($ref_id)
    {
        $all_users = $this->get_all_users();
        foreach ($all_users as $user) {
            if ($user->ref_id == $ref_id) {
                return $user;
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

    public function insert_new_user(mixed $fname, mixed $lname, mixed $phone, mixed $password, mixed $ref_id, string $status): bool
    {
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/save_new_user';
        $data = array(
            "id" => $ref_id,
            "fname" => $fname,
            "lname" => $lname,
            "password" => $password,
            "phone" => $phone,
            "ref_id" => $ref_id,
            "status" => $status,
            "type" => 'user'
        );
        $json_data = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($json_data), 'ref_id: '.$ref_id));
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

    public function validate_unique_ref_id(int $ref_id): bool
    {
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/validate_ref_id/' . $ref_id;
        return json_decode($this->get_response_from_url($url))->result == true;
    }

    public function get_total_balls($over): int
    {
        $x = $over * 10;
        $y = floor($x / 10);
        $z = $x % 10;
        return $y * 6 + $z;
    }

    public function save_transaction_ticket(int $transaction_id, string $ref_id, mixed $transaction_type, float $amount)
    {
        $transaction_data = array(
            "id" => $transaction_id,
            "ref_id" => $ref_id,
            "transaction_type" => $transaction_type,
            "amount" => $amount,
            "status" => "placed"
        );
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/save_new_transaction';
        $json_transaction_data = json_encode($transaction_data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json_transaction_data), 'ref_id: '.$ref_id));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_transaction_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            return false;
        }
        curl_close($ch);
        return $response;
    }

    public function get_transaction_tickets(string $ref_id)
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_transaction_tickets/" . $ref_id;
        return json_decode($this->get_response_from_url($url));
    }

    public function activate_user(string $ref_id, string $phone, string $otp)
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/activate_user/" .$ref_id. "/" .$phone. "/" .$otp;
        return json_decode($this->get_response_from_url($url));
    }

    public function update_user_profile(mixed $fname, mixed $lname, mixed $password)
    {
        $ref_id = $this->get_cookie('ref_id');
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/update_profile/" .$ref_id. "/" .$fname. "/" .$lname. "/" .$password;
        return json_decode($this->get_response_from_url($url));
    }

    public function get_special_question(mixed $question_id)
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_special_questions/" .$question_id;
        return json_decode($this->get_response_from_url($url));
    }
    public function insert_new_special_bid_to_db(int $bid_id, string $ref_id, string $series_id, string $match_id, int $question_id,
                                                 string $question_value, int $option_id, string $option_value, float $rate, float $amount,
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
            "question_id" => $question_id,
            "question_value" => $question_value,
            "option_id" => $option_id,
            "option_value" => $option_value,
            "rate" => $rate,
            "amount" => $amount,
            "status" => "placed",
            'bid_name' => $bid_name,
            'room' => $room,
            'type' => 'special'
        );
        $url = 'https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/save_user_bid';
        $json_bid_data = json_encode($bid_data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json_bid_data), 'ref_id: '.$ref_id));
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

    private function calculate_rates($x, $a, $b, $count, $amount, $flag): array
    {
        //$x -= min($x * $count == 0 ? 0.3 : (0.05 * $count), 300);
        $x -= min($x * 0.25, 500);

        /*
        if($count > 2 && $count < 6)
            $x -= 25;
        if($count > 5 && $count < 9)
            $x -= 50;
        if($count > 8 && $count < 13)
            $x -= 75;
        if($count > 12)
            $x -= 100;
        */

        $ga = max((($x - $a)), 0);
        $gb = max((($x - $b)), 0);

        $r1=max(min($ga/$amount,1.25),0);
        $r2=max(min($gb/$amount,1.25),0);

        try {
            if ($r1 + $r2 < 0.25 && $flag) {
                $flag = !$flag;
                return $this->calculate_rates($x + min($amount * 0.5, 50), $a, $b, $count, $amount, $flag);
            } else if($r1 == 0 && $r2 == 0 && !$flag){
                return [0.25, 0.25];
            } else if ($r1 + $r2 > 1.5) {
                $f = 1.5 / ($r1 + $r2);
            } else {
                $f = 1;
            }
        }catch (DivisionByZeroError $e){
            return [0.2, 0.2];
        }
        $r1 *= $f;
        $r2 *= $f;

        return [min($r1, 1.25), min($r2, 1.25)];
    }

    public function get_tickets(string $ref_id)
    {
        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_wallet_transaction_tickets/" . $ref_id;
        return json_decode($this->get_response_from_url($url));
    }

    public function get_bids(string $ref_id): array
    {
        $all_bids = [];

        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_bids/" . $ref_id . "/session";
        $bids = json_decode($this->get_response_from_url($url));
        $all_bids = array_merge($all_bids, $bids);

        $url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_bids/" . $ref_id . "/winner";
        $bids = json_decode($this->get_response_from_url($url));
        $all_bids = array_merge($all_bids, $bids);

        return $all_bids;
    }
}
