<?php


if(count($argv) != 4){
  echo 'USAGE -> php update.php PageId PreviousId NextId [-1 if doesn\'t exist]' ;
}else{
	// select a database
	require 'vendor/autoload.php';

	$client = new MongoClient();
	 
	$db = $client->test;   
	$PageCollection = $db->Pages;
	$idPage = new MongoId($argv[1]);

	if($argv[2]!='-1'){
		$PageCollection->update(array('_id' => $idPage), array('$set' => array('PreviousPage' => new MongoId($argv[2]))));
	}

	if($argv[3]!='-1'){
		$PageCollection->update(array('_id' => $idPage), array('$set' => array('NextPage' => new MongoId($argv[3]))));
	}
}

?>