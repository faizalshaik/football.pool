<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calc_api extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Africa/Lagos');
		ini_set('memory_limit', '1024M'); // or you could use 1G		
	}

	private function reply($status, $message, $data)
	{
		$result = array('status'=>$status, 'message'=>$message, 'data'=>$data);
		echo json_encode($result);
	}

	private function calcLines($under, $events)
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
	


	function combination(&$output, $arr, $index, $n, $r, $target)
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


	// public function test()
	// {
	// 	$events = 5;
	// 	$under = 3;

	// 	$out = [];
	// 	$arr = array_fill(0, $events, 0);

	// 	$output = $this->combination($out, $arr, 0, $events, $under, 0);
	// 	echo json_encode($output);
	// }

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

	public function calcWin()
	{
		$request = json_decode(file_get_contents('php://input'), true);
		
		$games = $request['games'];
		$events = count($games);
		$under = $request['under'];
		if($events < $under)
			return $this->reply(400, "events can't less than under", null);

		$woncount = 0 ;
		foreach($games as $game)
		{
			if($game['win'] > 0)
				$woncount ++;
		}
		if($woncount < $under)
			return $this->reply(200, "game failed ", null);

		$lines = $this->calcLines($under, $events);
		$amount = $request['amt'];
		$apl = $amount / $lines;
		
		for($i=0; $i<count($games); $i++)
		{
			if($games[$i]['win']==0) 
				$games[$i]['pz'] = 0;
			if($games[$i]['win']==2)
				$games[$i]['pz'] = 1;
		}

		$gameNos = [];
		for($i=0; $i<$events; $i++)
			$gameNos[] = $i+1;
		$prizeSum = $this->calcPrizeSum($games, $gameNos, $under, $events);

		// $combines = [];
		// $arr = array_fill(0, $events, 0);
		// $this->combination($combines, $arr, 0, $events, $under, 0);
		
		// $win = 0;
		// foreach($combines as $idxs)
		// {
		// 	$val = 1;
		// 	foreach($idxs as $idx)  $val = $val * $games[$idx]['pz'];
		// 	$win = $win + $val;
		// }

		$win = $prizeSum * $apl;
		$this->reply(200, 'win', ['lines'=>$lines, 'amt'=>$amount, 
									'apl'=>number_format($apl,2), 
									'won'=>number_format($win,2)]);

	}



	public function calcWinGroup()
	{
		$request = json_decode(file_get_contents('php://input'), true);
		$games = $request['games'];
		$events = count($games);
		$under = $request['under'];
		if($events < $under)
			return $this->reply(400, "events can't less than under", null);

		$groups = $request['groups'];
		if(count($groups)==0)
			return $this->reply(400, "groups can't be empty", null);
		for($i=0; $i<count($games); $i++)
		{
			if($games[$i]['win']==0) $games[$i]['pz'] = 0;
		}

		// $woncount = 0 ;
		// foreach($games as $game)
		// {
		// 	if($game['win']>0)
		// 		$woncount ++;
		// }
		// if($woncount < $under)
		// 	return $this->reply(200, "game failed ", null);
		$amount = $request['amt'];

		$lines = 1;
		$prizeSum = 1;
		foreach($groups as $group)
		{
			$lineGroup = $this->calcLines($group['under'], count($group['games']));
			$prizeGroup = $this->calcPrizeSum($games, $group['games'], $group['under'], count($group['games']));

			$lines = $lines * $lineGroup;
			$prizeSum = $prizeSum * $prizeGroup;			
		}

		if($prizeSum==0)
			return $this->reply(200, "game failed ", null);

		$apl = $amount / $lines;
		$win = $prizeSum * $apl;


		$this->reply(200, 'win', ['lines'=>$lines, 'amt'=>$amount, 
									'apl'=>number_format($apl,2), 
									'won'=>number_format($win,2)]);

	}	

	


}