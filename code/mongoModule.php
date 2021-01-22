
<?php
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$query = file_get_contents('php://input');
		//file_put_contents('php://stderr', print_r($query, TRUE));
		$query=json_decode($query);
		$query=json_decode(json_encode($query),true);
		$client = new MongoDB\Client("mongodb://mongo:27017");
		$document = $client->{'orion-tourguide'}->entities;
		$cursor = $document->find($query);
		$result=json_encode(iterator_to_array($cursor));
		$result=json_decode($result);
		$result=json_decode(json_encode($result),true);
		$ids= array();
		for($i=0;$i<count($result);$i++){
			$this_id=$result[$i]['_id']['id'];
			$a=array("id"=>$this_id);
			array_push($ids,$a);
		}
		$ids=json_encode($ids);
		#file_put_contents('php://stdout', print_r($ids, TRUE));
		print_r($ids);
}
//var_dump($cursor);
?>
