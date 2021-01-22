<?php
session_start();
if (!empty($_SESSION["access_token"] )) {
//   $Sub_paylaod= $_POST['checkbox'];
  $access_token=$_SESSION["access_token"];
  $USERNAME=$_SESSION["USERNAME"];
  $PASSWORD=$_SESSION["PASSWORD"];
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_body = file_get_contents('php://input');
    $request_body=json_decode($request_body,true);
    $appid=$request_body[1]["appid"];
    //print_r($appid);
    $request_body=$request_body[0];
    $appname=$request_body["appname"];
    $request_body=json_encode($request_body);
    $request_body=addslashes($request_body);
    $message="'[$request_body]'";
    //print_r($message);

    //echo $message
    $random=rand();
		$test1='{
		  "id": "91ad451.f6e52b8",
		  "label": "Sheet '.$USERNAME.'",
		  "nodes": [ ],
		  "configs": [

		    {
		        "id": "d35866e6.40251'.$random.$USERNAME.'",
		        "type": "function",
		        "z": "66348422.066494",
		        "name": "",
		        "func": "msg.headers = {};msg.payload='.$message.';return msg;",
		        "outputs": 1,
		        "noerr": 0,
		        "x": 430,
		        "y": 380,
		        "wires": [
		            [
		                "8ef3242d.f1b5c8'.$random.$USERNAME.'"
		            ]
		        ]
		    },
		    {
		        "id": "8ef3242d.f1b5c8'.$random.$USERNAME.'",
		        "type": "http request",
		        "z": "66348422.066494",
		        "name": "",
		        "method": "POST",
		        "ret": "obj",
		        "url": "http://10.48.0.17/calculations.php",
		        "tls": "",
		        "x": 730,
		        "y": 380,
		        "wires": [
		            [
		                "784c5a17.4f59b4'.$random.$USERNAME.'"
		            ]
		        ]
		    },
		    {
		        "id": "eb58b28.6b271d'.$random.$USERNAME.'",
		        "type": "http in",
		        "z": "66348422.066494",
		        "name": "",
		        "url": "/'.$appname.'",
		        "method": "get",
		        "upload": false,
		        "swaggerDoc": "",
		        "x": 110,
		        "y": 380,
		        "wires": [
		            [
		                "d35866e6.40251'.$random.$USERNAME.'"
		            ]
		        ]
		    },
		    {
		        "id": "784c5a17.4f59b4'.$random.$USERNAME.'",
		        "type": "http response",
		        "z": "66348422.066494",
		        "name": "",
		        "statusCode": "",
		        "headers": {},
		        "x": 1250,
		        "y": 380,
		        "wires": []
		    }
		]
  } ';
		$ch=curl_init("http://10.48.0.6:1881/flow/$appid");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	  curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json","X-Auth-token: thisismagickeyfornodered","Node-RED-Deployment-Type: flows"));
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt( $ch, CURLOPT_POSTFIELDS, $test1 );

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
