<?php

include('givesto.php');

//$_GET['input'] = "[['John','Paul','George','Ringo'],['Elmo','Oscar','Big Bird','Bert'],['Larry','Curly','Moe']]";
/*$a = [['John','Paul','George','Ringo'],['Elmo','Oscar','Big Bird','Bert'],['Larry','Curly','Moe']];
$b = json_encode($a);
print_r($b);
print_r(json_decode($b));*/


$input = "[".str_replace("'",'"',$_GET['input'])."]";

$group_list = json_decode($input);
/*
print_r("\n\n\nGET['input']: ");
print_r($_GET['input']);
print_r("\n\n\ngroup_list\n\n\n");
print_r($group_list);
print_r("\n\n\n");*/
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
