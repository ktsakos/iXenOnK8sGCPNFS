<?php
session_start();
if (!empty($_SESSION["access_token"] )) {
//   $Sub_paylaod= $_POST['checkbox'];
  $access_token=$_SESSION["access_token"];
  $USERNAME=$_SESSION["USERNAME"];
  $PASSWORD=$_SESSION["PASSWORD"];
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_body = file_get_contents('php://input');
    $phpobj=json_decode($request_body,true);
    $service_path=$phpobj['service_path'];
    $ch=curl_init("http://10.48.0.9:1027/v2/entities/$USERNAME");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_HTTPHEADER,array("Fiware-ServicePath: /applications/$service_path","Fiware-Service: tourguide","X-Auth-token: thismagickeyfororion"));
    $list = curl_exec($ch);
    curl_close($ch);
    print_r($list);
  }


}else{
    //echo "you must login first ";
    header("Location: index.php");
  }
?>
