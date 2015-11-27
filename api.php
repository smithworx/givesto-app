<?php

include('givesto.php');

$groups = explode("\n", $_GET['input']);
$group_list = [];
foreach($groups as $k => $group_string){
	$group_list[$k] = explode(",", $group_string);
}

if(is_null($group_list)){
	// bad input
	http_response_code(400);
	exit();
} 

$result = generate_gives_to($group_list, false);
if($result===false) {
	console.log("Incomplete Solution - Try adding another group");
	http_response_code(400);
	exit();
}

http_response_code(200);
print_r($result);
