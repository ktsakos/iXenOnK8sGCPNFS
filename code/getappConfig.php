<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $request_body = file_get_contents('php://input');
  file_put_contents('php://stderr', print_r($request_body, TRUE));
  $ch=curl_init("http://10.48.0.6:1881/flow/$request_body");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($ch, CURLOPT_HTTPHEADER,array("X-Auth-token: thisismagickeyfornodered"));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);
  echo  $result;

}


?>
