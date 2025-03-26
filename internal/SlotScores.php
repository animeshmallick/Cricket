<?php
class Scores {
    private Data $datahelper;
    function __construct(Data $datahelper){
        $this->datahelper = $datahelper;
    }

    public function get_curr_rr($scorecard, $innings): int{
        if ($innings == 1) {
            $over = $scorecard->team1_score->over;
            $x = $over * 10;
            $y = floor($x / 10);
            $z = $x % 10;
            return $scorecard->team1_score->runs / ($y + $z/6);
        } else {
            $over = $scorecard->team2_score->over;
            $x = $over * 10;
            $y = floor($x / 10);
            $z = $x % 10;
            return $scorecard->team2_score->runs / ($y + $z/6);
        }
    }

    public function get_curr_runs($bid_innings, $scorecard): int{
        return $bid_innings == 1 ? $scorecard->team1_score->runs : $scorecard->team2_score->runs;
    }
    public function get_r1($curr_runs, $curr_rr, $slot): float{
        if($curr_rr == 0 || $curr_runs == 0){
            return $this->datahelper->get_default_runs($slot);}
        return ($curr_runs * $curr_rr) * $this->datahelper->get_maxballs_for_slot($slot) / 6;
    }

    public function get_r2_without_wickets($runs, $curr_rr, $slot): float{
        if($curr_rr == 0 || $runs == 0)
            return $this->datahelper->get_default_runs($slot);
        return $runs * ($curr_rr + 1) * ($this->datahelper->get_maxballs_for_slot($slot) / 6);
    }

    public function update_r2_with_wickets($r2, $scorecard, $bid_innings): float{
        $r2 -= $bid_innings == 1 ? ($scorecard->team1_score->wickets * $this->datahelper->get_wicket_multiplier()) :
            ($scorecard->team2_score->wickets * $this->datahelper->get_wicket_multiplier());
        foreach($scorecard->this_over as $ball){
            if(in_array("W",str_split($ball)))
                return $r2;
        }
        return $r2 + 3;
    }

    public function get_r($r1, $r2, $curr_balls, $slot): float{
        $x = $slot == 'a' ? 0 : ($slot == 'b' ? 36 : ($slot == 'c' ? 60 : 96));
        return $r2 - (($r2 - $r1) * ($curr_balls - $x) / ($this->datahelper->get_maxballs_for_slot($slot) - $x));
    }
    public function get_slot_runs($bid_innings, $scorecard, $slot): float{
        $curr_rr = $this->get_curr_rr($scorecard, $bid_innings);
        $curr_runs = $this->get_curr_runs($bid_innings, $scorecard);
        $r1 = $this->get_r1($curr_runs, $curr_rr, $slot);
        $r2 = $this->get_r2_without_wickets($curr_runs, $curr_rr, $slot);
        $r2 = max($r1, $this->update_r2_with_wickets($r2, $scorecard, $bid_innings));
        return min(
                max(
                    $this->get_r($r1, $r2, $curr_balls_played, $slot),
            $slot == 'a' ? 33 : ($slot == 'b' ? 60 : ($slot == 'c' ? 96 : 120))),
            $slot == 'a' ? 75 : ($slot == 'b' ? 130 : ($slot == 'c' ? 200 : 300))
        );
    }

    private function get_valid_balls($this_over): int
    {
        $count = 0;
        for ($i=0; $i < count($this_over); $i++){
            if(str_contains($this_over[$i],'w') || str_contains($this_over[$i], 'nb'))
                continue;
            $count++;
        }
        return $count;
    }
}
?>