<?php
session_start();
if (!empty($_SESSION["access_token"] )) {
  $access_token=$_SESSION["access_token"];
  $USERNAME=$_SESSION["USERNAME"];
  $PASSWORD=$_SESSION["PASSWORD"];
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_body = file_get_contents('php://input');
    $phpobj=json_decode($request_body,true);
    $message="";
    ////////////////////////////////////////////////////////////////////////////////////////////

        $message='{
                      "type": "application",
                      "id": "'.$USERNAME.'",
                      "'.$phpobj['appname'].'": {
                        "value": "'.$phpobj['appdescription'].'",
                        "type": "Text",
                        "metadata":{
                                    "appid":{
                                            "type": "Text",
                                            "value": "'.$phpobj['appid'].'"
                                            }
                                  }
                      }

                  }';

      $ch = curl_init("http://10.48.0.9:1027/v2/op/update");
      $payload = '{
        "actionType": "append",
        "entities": ['.$message.']
           }';
      $service_path=$phpobj['App_scope'];
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json","Fiware-Service: tourguide","X-Auth-token: thismagickeyfororion","Fiware-ServicePath:/applications/$service_path"));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );

      curl_setopt($ch, CURLOPT_HEADER,true);
      # Send request.
      $result = curl_exec($ch);
      curl_close($ch);


  }

  }else{
      //echo "you must login first ";
      header("Location: index.php");
  }


?>
