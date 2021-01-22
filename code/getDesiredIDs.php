<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $request_body = file_get_contents('php://input');
  $request_body=json_decode($request_body);
  $request_body=json_decode(json_encode($request_body),true);
  $payload="";
  $GeoQuery="";
  for($i=0;$i<count($request_body);$i++){
    if($request_body[$i]["id"]!=""){
      $payload=$payload.'{
          "id": "'.$request_body[$i]["id"].'",
          "type": "Sensor"
        },';
    }
  }
  $payload=rtrim($payload,", ");
  $GeoQuery=$request_body[count($request_body)-1]["geoQuery"];
  if($GeoQuery!=""){
      $payload='{"entities": ['.$payload.'],'.$GeoQuery.'}';
  }else{
      $payload='{"entities": ['.$payload.']}';
  }
  $ch=curl_init("http://10.48.0.9:1027/v2/op/query");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json","X-Auth-token: thismagickeyfororion","Fiware-Service: tourguide",'Fiware-ServicePath:/citySensors'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
  $result = curl_exec($ch);
  curl_close($ch);
  echo $result;



}
?>
