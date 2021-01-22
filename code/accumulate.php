<?php

  $request_body = file_get_contents("php://input");
  $f_headers=array();
  $proxy_header="X-Auth-token: thismagickeyforcygnus";
  $headers = apache_request_headers();

  foreach ($headers as $header => $value) {
    if(strcmp($header,"X-Auth-Token")!=0){
      array_push($f_headers,"$header: $value");
    }
  }
  array_push($f_headers,$proxy_header);
  //$headers["X-Auth-token"]="thisismagickeyforcomet";
  //print_r($headers);
  //print_r($f_headers);
  $time_start = microtime(true);
  $ch=curl_init("http://10.48.0.11:5051/notify");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$f_headers);
  curl_setopt( $ch, CURLOPT_POSTFIELDS, $request_body);
  #file_put_contents('php://stdout', print_r("Result1:".$result."\n", TRUE));
  $result = curl_exec($ch);
  #file_put_contents('php://stdout', print_r("Result2:".$result."\n", TRUE));
  curl_close($ch);
  $time_end = microtime(true);

  $time = $time_end - $time_start;

?>
