<?php
session_start();
if (!empty($_SESSION["access_token"] )) {
  $access_token=$_SESSION["access_token"];
  $USERNAME=$_SESSION["USERNAME"];
  $PASSWORD=$_SESSION["PASSWORD"];
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_body = file_get_contents('php://input');
    $phpobj=json_decode($request_body,true);
    //echo $request_body;
    $Date = date("Y/m/d");

    $message="";
    ////////////////////////////////////////////////////////////////////////////////////////////
      for ($x = 0; $x < count($phpobj); $x++) {


        $message=$message.','.'{
                                "name": "'.$phpobj[$x].'",
                                "type": "DateTime",
                                "value": "'.$Date.'"
                                }';

      }
      $message = substr($message,1);
      echo $message;


      $ch = curl_init( "10.48.0.9:1027/v1/updateContext" );
      $payload = '{
            "contextElements": [
                {
                    "type": "subscription",
                    "isPattern": "false",
                    "id": "'.$USERNAME.'",
                    "attributes": ['.$message.']
                }
            ],
            "updateAction": "APPEND"
        }';

      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json","Fiware-Service: tourguide","X-Auth-token: thismagickeyfororion",'Fiware-ServicePath:/subscribers'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );

      curl_setopt($ch, CURLOPT_HEADER,true);
      # Send request.
      $result = curl_exec($ch);
      curl_close($ch);
      echo $result;


  }

  }else{
      //echo "you must login first ";
      header("Location: index.php");
  }


?>
