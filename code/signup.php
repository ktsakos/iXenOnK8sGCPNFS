

<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<?php
// define variables and set to empty values
$nameErr = $emailErr = $passwordErr = $passwordAGErr = "";
$name = $email = $password = $passwordAG = "";
$passok=false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = test_input($_POST["name"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
      $nameErr = "Only letters and white space allowed";
    }
  }
  if (empty($_POST["password"])) {
    $passwordErr = "password is required";
  } else {
    $password = test_input($_POST["password"]);
  }
  if (empty($_POST["passwordAG"])) {
    $passwordAGErr = "password is required";
  } else {
    $passwordAG = test_input($_POST["passwordAG"]);
  }
  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format";
    }
  }

}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>

<h2>PHP Form Validation Example</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  Name:<br>
  <input type="text" name="name" value="<?php echo $name;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  E-mail:<br>
  <input type="text" name="email" value="<?php echo $email;?>">
  <span class="error">* <?php echo $emailErr;?></span>
  <br><br>
  password:<br>
  <input type="text" name="password" value="<?php echo $password;?>">
  <span class="error">* <?php echo $passwordErr;?></span>
  <br><br>
  password(again):<br>
  <input type="text" name="passwordAG" value="<?php echo $passwordAG;?>">
  <span class="error">* <?php echo $passwordAGErr;?></span>

  <span class="error"><?php if($password!=$passwordAG){echo "passwords need to be the same ";$passok=false;}else{$passok=true;}?></span>
  <br><br>

  <input type="submit" name="submit" value="Submit">
</form>

<?php
ini_set("display_errors", true); error_reporting(E_ALL);
echo "<h2>Your Input:</h2>";
echo $name;
echo "<br>";
echo $email;
echo "<br>";
echo $password;
echo "<br>";
echo $passwordAG;
echo "<br>";
echo "<br>";echo "<br>";
$unique=true;

if($passok==true & $password!="" & $emailErr==""& $email!="" & $name!="" & $nameErr==""){

  $encoded_pass=hash_hmac('sha1',$password, 'nodejs_idm');
  echo "ready to insert";


  $servername = 'mysql-db';
  $username = 'root';
  $password = 'secret';
  $dbname = 'idm';



    // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  $sql = "SELECT username, email FROM user";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "username: " . $row["username"]. " - email: " . $row["email"]. " <br>";
        if($row["username"]==$name | $row["email"]==$email){
          $unique=false;
        }
    }
  } else {
    echo "0 results";
  }
  if($unique==true){

        $sql = "INSERT INTO user ( id, username, description,website,image,gravatar,email,password,date_password,enabled,admin,extra,scope,starters_tour_ended) VALUES
        ('$name','$name','user','NULL','default','0','$email','$encoded_pass','2017-07-28T17','1','0','{}','NULL','0')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
  }else{
    echo "duplicate";
  }
  $conn->close();
}
?>

</body>
</html>
