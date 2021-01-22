<?php
session_start();
if (!empty($_SESSION["access_token"] )) {
  $access_token=$_SESSION["access_token"];
  $USERNAME=$_SESSION["USERNAME"];
  $PASSWORD=$_SESSION["PASSWORD"];
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_body = file_get_contents('php://input');
    $phpobj=json_decode($request_body,true);
    $admintoken=array();
    $count=0;
      /////////////////////////////////////////////////////////////////////// GET ADMIN INTERNAL TOKEN.
    $ch = curl_init( "http://10.48.0.3:3005/v1/auth/tokens" );
    $payload = '{
    "name": "admin@test.com",
    "password": "1234"
    }';
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER,true);
    # Send request.
    $result = curl_exec($ch);
    curl_close($ch);
    list($headers, $content) = explode("\r\n\r\n",$result,2);
    $payload=json_decode($headers,true);

    // Print header
    foreach (explode("\r\n",$headers) as $hdr){
        if($hdr[0]=="X")
        {
          $admintoken=$hdr;

        }
    }
    $stop=strlen($admintoken);
    for($i = 17; $i<=$stop; $i++){
        $xauthtoken[$count]=$admintoken[$i];
        $count++;
    }
    $xauthtokenstring=implode("",$xauthtoken);//ADMIN INTERNAL TOKEN
//print_r($xauthtokenstring);
    ////////////////////////////////////////////////////////////////////// FIND LOGED USER INTERNAL TOKEN.
      $ch = curl_init( "http://10.48.0.3:3005/v1/auth/tokens" );
      $payload = '{
      "name": "'.$USERNAME.'",
      "password": "'.$PASSWORD.'"
      }';
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER,true);
      # Send request.
      $result = curl_exec($ch);
      curl_close($ch);
      list($headers, $content) = explode("\r\n\r\n",$result,2);
      $payload=json_decode($headers,true);

      // Print header
      foreach (explode("\r\n",$headers) as $hdr){
          if($hdr[0]=="X")
          {
            $XSubjecttoken=$hdr;//LOGED USER INTERNAL TOKEN
          }
      }
      //print_r($XSubjecttoken);
    //////////////////////////////////////////////////////////////////////   FIND LOGED USER ID.
    $ch=curl_init("http://10.48.0.3:3005/v1/auth/tokens");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-Auth-token: $xauthtokenstring",$XSubjecttoken));
    $result = curl_exec($ch);
    curl_close($ch);
    $payload=json_decode($result,true);

    foreach($payload as $x => $x_value) {
        if (is_string ( $x_value)==true){
        }else{
          foreach($x_value as $y => $y_value){
              if($y=="id")
              $userid=$y_value;//LOGED USER ID
          }
        }
    }
//print_r($userid);
////////////////////////////////////////////////////////////////////////  create user role.

$payload =
'{
  "role": {
    "name": "'.$USERNAME.'"
  }
}';
$ch = curl_init( "http://10.48.0.3:3005/v1/applications/8b7d8559-2b3c-44d9-9303-26d158d9f513/roles" );
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json","X-Auth-token: $xauthtokenstring"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
# Send request.
$result = curl_exec($ch);
curl_close($ch);
$payload=json_decode($result,true);
$roleid=$payload["role"]["id"];
//print_r($roleid);
////////////////////////////////////////////////////////////////////////  create user permission.
for ($x = 0; $x < count($phpobj); $x++) {
    $payload =
    '{
      "permission": {
        "name": "appPermision",
        "action": "GET",
        "resource": "/'.$phpobj[$x].'"
      }
    }';
    $ch = curl_init( "http://10.48.0.3:3005/v1/applications/8b7d8559-2b3c-44d9-9303-26d158d9f513/permissions" );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json","X-Auth-token: $xauthtokenstring"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
    # Send request.
    $result = curl_exec($ch);
    curl_close($ch);
    $payload=json_decode($result,true);
    $permmissions[$x]=$payload["permission"]["id"];
    //print_r($permmissions);

}
////////////////////////////////////////////////////////////////////////  assign permmisions to user role.
for ($x = 0; $x < count($permmissions); $x++) {

    $ch = curl_init( "http://10.48.0.3:3005/v1/applications/8b7d8559-2b3c-44d9-9303-26d158d9f513/roles/$roleid/permissions/$permmissions[$x]" );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json","X-Auth-token: $xauthtokenstring"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    # Send request.
    $result = curl_exec($ch);
    curl_close($ch);
    $payload=json_decode($result,true);

}
////////////////////////////////////////////////////////////////////////  assign role to a user.
$ch = curl_init( "http://10.48.0.3:3005/v1/applications/8b7d8559-2b3c-44d9-9303-26d158d9f513/users/$userid/roles/$roleid" );
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json","X-Auth-token: $xauthtokenstring"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
# Send request.
$result = curl_exec($ch);
curl_close($ch);
$payload=json_decode($result,true);

//print_r($payload);

///////////////////////////////////////////////////////////////////////


}
  }else{
      //echo "you must login first ";
      header("Location: index.php");
  }


?>
