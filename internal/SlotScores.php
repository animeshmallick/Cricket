<?php
class Scores {
    private Data $datahelper;
    function __construct(Data $datahelper){
        $this->datahelper = $datahelper;
    }

    public function get_curr_rr($scorecard, $innings): float{
        $x = floor($scorecard->balls_played / 6);
        $y = $scorecard->balls_played % 6;
        if ($innings == 1)
            return $scorecard->team1_score->runs / ($x + floatval($y)/6);
        else
            return $scorecard->team2_score->runs / ($x + floatval($y)/6);
    }

    public function get_curr_runs($bid_innings, $scorecard): int{
        return $bid_innings == 1 ? $scorecard->team1_score->runs : $scorecard->team2_score->runs;
    }
    public function get_r1(int $curr_runs, float $curr_rr, $curr_wkts, $slot): float{
        if($curr_rr == 0){
            return $this->datahelper->get_default_runs($slot);}
        if ($curr_wkts >= 8)
            return $curr_runs * 1.05;
        return ($curr_rr) * ($this->datahelper->get_maxballs_for_slot($slot) / 6);
    }

    public function get_r2(float $curr_rr,int $wkts, $slot): float{
        if($curr_rr == 0)
            return $this->datahelper->get_default_runs($slot);
        $factor = 1.25;
        if($wkts >= 2)
            $factor = 1;
        if($wkts >= 5)
            $factor = 0.8;
        if($wkts >= 7)
            $factor = 0.5;
        if($wkts >= 9)
            $factor = 0.25;

        if($curr_rr > 12)
            $factor = ($curr_rr - 12) / 2.0;

        return ($curr_rr + $factor) * ($this->datahelper->get_maxballs_for_slot($slot) / 6);
    }

    public function update_r2_with_wickets($r2, $scorecard): float{
        $wkts = 0;
        foreach($scorecard->this_over as $ball){
            if(in_array("W",str_split($ball)))
                $wkts++;
        }
        return $r2 - ($wkts * $this->datahelper->get_wicket_multiplier());
    }

    public function get_r($r1, $r2): int{
        return floor(($r1 + $r2) / 2);
        //$x = $slot == 'a' ? 0 : ($slot == 'b' ? 36 : ($slot == 'c' ? 60 : 96));
        //return $r2 - (($r2 - $r1) * ($curr_balls - $x) / ($this->datahelper->get_maxballs_for_slot($slot) - $x));
    }
    public function get_slot_runs($bid_innings, $scorecard, $slot): float{
        $curr_runs = $this->get_curr_runs($bid_innings, $scorecard);
        $curr_rr = $this->get_curr_rr($scorecard, $bid_innings);
        $curr_wkts = $bid_innings == 1 ? $scorecard->team1_score->wickets : $scorecard->team2_score->wickets;
        $r1 = $this->get_r1($curr_runs, $curr_rr, $curr_wkts, $slot);
        $r2 = $this->get_r2($curr_rr, $curr_wkts, $slot);
        $r2 = max($r1, $this->update_r2_with_wickets($r2, $scorecard));
        return min(
                max(
                    $this->get_r($r1, $r2),
            $slot == 'a' ? 45 - (5 * $curr_wkts) : ($slot == 'b' ? 69 : ($slot == 'c' ? 105 : 120))),
            $slot == 'a' ? 90 : ($slot == 'b' ? 160 : ($slot == 'c' ? 225 : 320))
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

    private function get_balls_played($scorecard, $bid_innings): int
    {
        if($bid_innings == 1){
            $over = $scorecard->team1_score->over;
        } else {
            $over = $scorecard->team2_score->over;
        }
        $x = $over * 10;
        $y = floor($x / 10);
        $z = $x % 10;
        return ($y * 6) + $z;
    }
}
?>