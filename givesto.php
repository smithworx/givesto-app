<?php

function generate_gives_to($group_list, $fancy_print = true, $attempt = 0){
	$recipients = [];
	$givesto = [];
	$attempt += 1;

	foreach($group_list as $k1=>$group){
		$possible_recipients = array_merge(
								array_slice($group_list,0,$k1),
								array_slice($group_list,$k1+1)
								);

		$possible_recipients = array_diff(
			flatten($possible_recipients),
			$recipients);	

		foreach($group as $k2=>$person){
			if(empty($possible_recipients)){
				if($attempt < 10){
					return generate_gives_to($group_list, $fancy_print, $attempt);
				} else {
					return false;
				}
			}
			$k = array_rand($possible_recipients);
			$givesto[$person] = $possible_recipients[$k];
			$recipients[] = $possible_recipients[$k];
			unset($possible_recipients[$k]);
		}
	}

	//echo fancy_print($givesto);
	//return $givesto;
	return $fancy_print ? fancy_print($givesto) : json_encode($givesto);
}

function fancy_print($givesto){
	$print = "Gift Giving List ".@date("Y")."\n";
	$print .= "------------------------\n";
	foreach($givesto as $giver => $recipient){
		$print .= strtoupper($giver)." gives to ".strtoupper($recipient)."\n";
	}
	$print .= "\n\n";
	return $print;
}

function flatten(array $array) {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = trim($a); });
    return $return;
}




