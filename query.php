<?php
include "Database.php";
$connect = array( 
	'host' => 'localhost', 
	'user' => 'test', 
	'password' => 'test', 
	'dbname' => 'webd4950'
);
$db = new Database($connect);
$db->setTable('listings');

$query = array( 
	'eq' => array( 'City' => 'SPARTA' ) 
);

$result = $db->find($query)->run();

foreach($result as $row) {
	print_r($row);
}
/*
$result = $db->find($query)->delete()->run();

$query['values'] = array('city' => 'SPARTA', 'county' => 'WHITE');

$result = $db->change($query)->run();

$result = $db->create($query)->run();
*/
?>