<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calc_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	function calcLines($under, $events)
	{
		$val =1 ;
		$division = 1;
		for($i=0; $i<$under; $i++)
		{
			$val = $val * ($events - $i);
			$division = $division * ($i + 1);
		}

		$lines = $val / $division;
		return $lines; 
	}

    public function combination_count($n, $r)
    {
    	$v0 = 1;
    	for ($i = 2; $i <= $n; $i++) $v0 = $v0 * $i;
    
    	$v1 = 1;
    	for ($i = 2; $i <= $r; $i++) $v1 = $v1 * $i;
    
    	$v2 = 1;
    	for ($i = 2; $i <= ($n - $r); $i++) $v2 = $v2 * $i;
    
    	return $v0 / $v1 / $v2;
    }

	public function combination(&$output, $arr, $index, $n, $r, $target)
	{
		if($r == 0)
		{
			$newEntry = [];
			for($i=0; $i<$index; $i++)
				$newEntry[] = $arr[$i];

			$output[]= $newEntry;		
		}
		else if ($target == $n) 
		{
			return $output;
		}
		else {
			$arr[$index] = $target;
			$this->combination($output, $arr, $index + 1, $n, $r - 1, $target + 1);
			$this->combination($output, $arr, $index, $n, $r, $target + 1);
		}
		return $output;
	}

	function getGame($games, $gameNo)
	{
		foreach($games as $game)
		{
			if($game['no']==$gameNo)
				return $game;
		}
		return null;
	}

	function getGames($games, $gameNos, $idxs)
	{
		$rGames = [];
		foreach($idxs as $idx){
			$gameNo = $gameNos[$idx];
			$game = $this->getGame($games, $gameNo);
			if($game!=null)
				$rGames[]= $game;
		}  		
		return $rGames;
	}

	function calcPrizeSum($games, $gameNos, $under, $events)
	{
	   // if($events >= 16)return 1;
	    
		$combines = [];
		$arr = array_fill(0, $events, 0);
  		$this->combination($combines, $arr, 0, $events, $under, 0);

		$pzSum = 0;
		foreach($combines as $idxs)
		{
			$val = 1;
			$cGames = $this->getGames($games, $gameNos, $idxs);
			foreach($cGames as $game)  $val = $val * $game['pz'];
			$pzSum = $pzSum + $val;
		}
		return $pzSum;
	}



	function prepare_games($gamelist, $dbgames, $max, $panelPrize)
	{
		$rGames = [];
		foreach($gamelist as $no)
		{
			$dbgame = null;
			foreach($dbgames as $gm)
			{
				if($gm->game_no == $no)
				{
					$dbgame = $gm;
					break;
				}
			}
			if($dbgame == null) continue;

			$pz = $dbgame->prize;
			if(!$max)
			{
				if($dbgame->checked==0) $pz = 0;
				if($dbgame->checked==2) $pz = $panelPrize;
			}
			$rGames[] = ["no"=> $no, "pz"=> $pz, 'st'=>$dbgame->checked];
		}
		return $rGames;
	}

	public function calc_win($gamelist, $under, $dbgames, $amount, $max = true, $panelPrize = 1)
	{
		$games = $this->prepare_games($gamelist, $dbgames, $max, $panelPrize);
		$events = count($games);
		
		if(!$max)
		{
    		$woncount = 0 ;
    		foreach($games as $game)
    		{
    			if($game['st'] == 1)
    				$woncount ++;
    		}
    		if($woncount < $under)
    		    return 0;
		}
		

		$gameNos = [];
		foreach($games as $game)
			$gameNos[] = $game["no"];

		$lines = $this->calcLines($under, $events);
		$apl = $amount / $lines;

  		$prizeSum = $this->calcPrizeSum($games, $gameNos, $under, $events);
        // $prizeSum = 1;
		$win = $prizeSum * $apl;
		return $win;
	}


	public function calc_win_for_group($gamelist, $under, $dbgames, $amount, $max = true, $panelPrize = 1)
	{
		$lines = 1;
		$prizeSum = 1;
		foreach($gamelist as $group)
		{
			$games = $this->prepare_games($group['list'], $dbgames, $max, $panelPrize);
			$events = count($games);
			$lineGroup = $this->calcLines($group['under'], count($group['list']));
			$gameNos = [];
			foreach($games as $game)
				$gameNos[] = $game["no"];

			$prizeGroup = $this->calcPrizeSum($games, $gameNos, $group['under'], $events);
			$lines = $lines * $lineGroup;			
			$prizeSum = $prizeSum * $prizeGroup;
		}
		$apl = $amount / $lines;
		$win = $prizeSum * $apl;
		return $win;
	}	

}


