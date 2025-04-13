<?php
class Data {
	private int $default_runs_slotA;
	private int $default_runs_slotB;
	private int $default_runs_slotC;
	private int $default_runs_slotD;
	private int $balls_slotA;
	private int $balls_slotB;
	private int $balls_slotC;
	private int $balls_slotD;
    private int $wicket_multiplier;

    function __construct(){
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            $this->path = "http://localhost/t20/";
        }else {
            $this->path = "https://www.crickett20.in/T20/";
        }
		$this->default_runs_slotA = 48;
		$this->default_runs_slotB = 80;
		$this->default_runs_slotC = 135;
		$this->default_runs_slotD = 170;
		$this->balls_slotA = 36;
		$this->balls_slotB = 60;
		$this->balls_slotC = 90;
		$this->balls_slotD = 120;
        $this->wicket_multiplier = 6;
    }

    public function get_wicket_multiplier(): int
    { return $this->wicket_multiplier; }
	public function get_default_runs($slot){
        if($slot == 'a')
            return $this->default_runs_slotA;
        elseif($slot == 'b')
            return $this->default_runs_slotB;
        elseif($slot == 'c')
            return $this->default_runs_slotC;
        elseif($slot == 'd')
            return $this->default_runs_slotD;
    }
	public function get_maxballs_for_slot($slot) : int
	{ 
		switch($slot){
			case "a":{
				$maxballs = $this->balls_slotA;
				break;
			}

			case "b":{
				$maxballs = $this->balls_slotB;
				break;
			}

			case "c":{
				$maxballs = $this->balls_slotC;
				break;
			}

			case "d":{
				$maxballs = $this->balls_slotD;
				break;
			}

			default:{
				$maxballs = -1;
			}
		}
		return $maxballs;
	}
}
?>