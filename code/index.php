<?php 
session_start();
// define variables and set to empty values
$IDErr = $PassErr  = "";
$USERNAME = $PASSWORD = "";
$access_token = $refresh_token = "";
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (empty($_POST["USERNAME"])) {
    $IDErr = "*USERNAME is required";
  } else {
    $USERNAME = test_input($_POST["USERNAME"]);

  }

  if (empty($_POST["PASSWORD"])) {
    $PassErr = "*PASSWORD is required";
  } else {
    $PASSWORD = test_input($_POST["PASSWORD"]);

  }
  if($USERNAME!="" & $PASSWORD!=""){

      $ch = curl_init( "http://10.48.0.3:3005/oauth2/token" );

      $payload = 'grant_type=password&username='.$USERNAME.'&password='.$PASSWORD.'';

      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Basic MzY3MTUzMTAtYmM3NC00ZmMzLWEyY2QtNGIwNmYyOThlZWM0OmY5MTcyODliLTBiN2UtNGU1My1hYmM5LWQ1MzZlMGIwZWZjNw==",
      "Content-Type: application/x-www-form-urlencoded"));
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
      # Return response instead of printing.
      # Send request.

      $result = curl_exec($ch);
      curl_close($ch);
      $array = array();
      $payload=json_decode($result,true);

      foreach($payload as $x => $x_value) {


          if (is_string ( $x_value)==true){
      			$array[$x] = $x_value;

      		}

      		else{

            if(is_string($x_value)==true){
              foreach($x_value as $y => $y_value){
                  if($y=="value")
                  $array[$x] = $y_value;
             }
            }

      		}
      }
      foreach($array as $y => $y_value){
          if($y=="access_token"){
            $access_token=$y_value;
          }
          if($y=="refresh_token"){
            $refresh_token=$y_value;
          }
      }
      if($access_token!=""){

        $_SESSION["access_token"] = $access_token;
        $_SESSION["USERNAME"] = $USERNAME;
        $_SESSION["PASSWORD"] = $PASSWORD;
        $PassErr="";
        $IDErr="";
        $USERNAME="";
        $PASSWORD="";
        header("Location: portal.php");
      }else{
            $PassErr="Wrong credentials";
      }
  }
}
?>
<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8"/>

<style>
* {
  box-sizing: border-box;

}
body {
  font: 16px Arial;

}
.autocomplete {
  /*the container must be positioned relative:*/

    position:fixed;
    top: 40%;

    right: 40%;
    display: inline-block;
}
input {

  border: 1px solid transparent;
  background-color: #f1f1f1;
  padding: 10px;
  font-size: 16px;
}
input[type=text] {
  background-color: #f1f1f1;
  width: 100%;
}
input[type=submit] {
  background-color: DodgerBlue;
  color: #fff;
  cursor: pointer;
}
.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}
.autocomplete-items div {

  padding: 10px;
  cursor: pointer;
  background-color: #fff;
  border-bottom: 1px solid #d4d4d4;
}
.autocomplete-items div:hover {
  /*when hovering an item:*/
  background-color: #e9e9e9;
}
.autocomplete-active {
  /*when navigating through the items using the arrow keys:*/
  background-color: DodgerBlue !important;
  color: #ffffff;
}
.error {color: #FF0000;}
#username {
  background-image: url('http://icons.iconarchive.com/icons/icons-land/vista-people/16/Office-Customer-Male-Light-icon.png');
  background-position: 10px 12px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}
#pass {
  background-image: url('http://icons.iconarchive.com/icons/oxygen-icons.org/oxygen/16/Status-dialog-password-icon.png');
  background-position: 10px 12px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}
</style>
</head>
<body>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <div class="autocomplete" style="width:300px;   ">
      <h2>LOGIN PAGE</h2>
      USERNAME:
      <input id="username" type="text" name="USERNAME" value=<?php echo $USERNAME;?>>
      <span class="error"><?php echo $IDErr;?></span>

      <br><br>
      PASSWORD:
      <input id="pass"type="text" name="PASSWORD" value=<?php echo $PASSWORD;?>>
      <span class="error"><?php echo $PassErr;?></span>
      <br><br>
      <input type="submit" name="submit" value="Login">
    </div>
</form>
</body>
</html>
