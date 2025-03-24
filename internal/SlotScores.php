<?php
class Scores {
    private Data $datahelper;
    function __construct(Data $datahelper){
        $this->datahelper = $datahelper;
    }

    public function get_curr_balls($scorecard, $innings): int{
        if ($scorecard->innings == 1) {
            $over = floor($scorecard->team1_score->over * 10);
        } else {
            $over = floor($scorecard->team2_score->over * 10);
        }
        return ($over/10)*6 + $over%10;
    }

    public function get_curr_runs($bid_innings, $scorecard): int{
        return $bid_innings == 1 ? $scorecard->team1_score->runs : $scorecard->team2_score->runs;
    }
    public function get_r1($curr_runs, $curr_balls, $slot): float{
        if($curr_balls == 0 || $curr_runs == 0){
            return $this->datahelper->get_default_runs($slot);}
        return ($curr_runs * 1.0 / $curr_balls) * $this->datahelper->get_maxballs_for_slot($slot);
    }

    public function get_r2_without_wickets($runs, $balls, $slot): float{
        if($balls == 0 || $runs == 0)
            return $this->datahelper->get_default_runs($slot);
        return ((($runs * 1.0) / $balls) + 0.15) * $this->datahelper->get_maxballs_for_slot($slot);
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
        $curr_balls_played = $this->get_curr_balls($scorecard, $bid_innings);
        $curr_runs = $this->get_curr_runs($bid_innings, $scorecard);
        $r1 = $this->get_r1($curr_runs, $curr_balls_played, $slot);
        $r2 = $this->get_r2_without_wickets($curr_runs, $curr_balls_played%120, $slot);
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