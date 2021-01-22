<?php
session_start();
if (!empty($_SESSION["access_token"] )) {
//   $Sub_paylaod= $_POST['checkbox'];
  $access_token=$_SESSION["access_token"];
  $USERNAME=$_SESSION["USERNAME"];
  $PASSWORD=$_SESSION["PASSWORD"];
  //echo "User: ".$USERNAME."<br><br><br>";
  $ch=curl_init("http://10.48.0.9:1027/v2/entities/$USERNAME");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_HTTPHEADER,array("Fiware-ServicePath: /appSubscribers","X-Auth-token: thismagickeyfororion","Fiware-Service: tourguide"));
  $list = curl_exec($ch);
  curl_close($ch);
  //echo $list;
  $subcontentphp=json_decode($list,true);

}else{
    //echo "you must login first ";
    header("Location: index.php");
  }
?>
<!DOCTYPE html>
<html lang="en">
<title>Subscriptions to Applications.</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.css">

<style>

body,h1,h2,h3,h4,h5 {font-family: "Poppins", sans-serif}
body {font-size:16px;}
.w3-half img{margin-bottom:-6px;margin-top:16px;opacity:0.8;cursor:pointer}
.w3-half img:hover{opacity:1}
#myInput {
  background-image: url('http://icons.iconarchive.com/icons/jommans/briefness/16/Search-icon.png');
  background-position: 10px 12px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}
</style>
<body>

<!-- Sidebar/menu -->
<nav class="w3-sidebar w3-red w3-collapse w3-top w3-large w3-padding" style="z-index:3;width:300px;font-weight:bold;background-color: DodgerBlue!important;" id="mySidebar"><br>
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-button w3-hide-large w3-display-topleft" style="width:100%;font-size:22px">Close Menu</a>
  <div class="w3-container">
    <h3 class="w3-padding-64"><b id="show_username"></b></h3>
  </div>
  <div class="w3-bar-block">
    <a href="ConPortal.php" onclick="w3_close()" class="w3-bar-item w3-button w3-hover-white"style="position:absolute; bottom:0;width:90%; overflow: hidden;">Back</a>
  </div>
</nav>

<!-- Top menu on small screens -->
<header class="w3-container w3-top w3-hide-large w3-red w3-xlarge w3-padding">
  <a href="javascript:void(0)" class="w3-button w3-red w3-margin-right" onclick="w3_open()">☰</a>
  <span>Company Name</span>
</header>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:340px;margin-right:40px">
  <div class="w3-container" id="services" style="margin-top:75px;">
    <h1 class="w3-xxxlarge w3-text-red"style="color: DodgerBlue!important;"><b>My subscriptions to Applications.</b></h1>
    <br>
    <input type="text" id="myInput" onkeyup="filter()" placeholder="Search for Keywords.." title="Type in a name" style="height:42px!important;width:650px;border-radius: 25px!important;">
    <div id="container"style="width:60%;height:100%;">
        <div id="div1"style="width:100%;">

                <div class="autocomplete"  style="width:100%;" >

                  <form id="form1" >

                    <div data-simplebar id=results class="autocomplete" style="width:800px!important;height:500px!important;"><br><br><br><br>

                    </div>

                  </form>

                </div>

        </div>

    </div>
    <input  id="123" value=<?php echo $list;?> style="display: none;">
  </div>
  <!-- Header -->


  <!-- Photo grid (modal) -->
  <div class="w3-row-padding">
    <div class="w3-half">
    <p></p>
    </div>

    <div class="w3-half">

    </div>
  </div>

  <!-- Modal for full size images on click-->
  <div id="modal01" class="w3-modal w3-black" style="padding-top:0" onclick="this.style.display='none'">
    <span class="w3-button w3-black w3-xxlarge w3-display-topright"></span>
    <div class="w3-modal-content w3-animate-zoom w3-center w3-transparent w3-padding-64">
      <img id="img01" class="w3-image">
      <p id="caption"></p>
    </div>
  </div>




<!-- End page content -->
</div>
position: fixed;bottom: 0;
<!-- W3.CSS Container -->
<div class="w3-light-grey w3-container w3-padding-32" style="background-color: rgb(38, 43, 48);
    color: #f2f2f2;
    padding-left:1000px;
    text-align: right;
    height: 50px;
    width: 100%;
    position: fixed;
    bottom: 0;
    margin-left: 200px"><p style="left: -200px;top: -10px;position: relative;">Copyright 2018 | Intelligent Systems Laboratory</p>
</div>

<script type="text/javascript">
    var Username = "<?php echo $USERNAME; ?>";
    document.getElementById("show_username").innerHTML =Username;

</script>
<script>
// Script to open and close sidebar

function w3_open() {
    document.getElementById("mySidebar").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}

function w3_close() {
    document.getElementById("mySidebar").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}

</script>
<script>
var fiter_results=new Array();

</script>
<script>
  document.getElementById("results").innerHTML = "";
  var payload;
  payload=document.getElementById("123").value;

  if(payload.search("error")==-1){
      obj = JSON.parse(payload);
      for (var key in obj) {
        if(key!="id"& key!="type"){

            var label = document.createElement("label");
            label.id=key;
            document.getElementById("results").appendChild(label);
            fiter_results[key]=key;
            label.appendChild(document.createElement('br'));
            label.appendChild(document.createTextNode("Application Name:  "));
            var Appname=document.createTextNode(key);
            var span = document.createElement('span');
            span.style.fontSize = "20px";
            span.style.color = "DodgerBlue";
            span.appendChild(Appname);
            label.appendChild(span);
            label.appendChild(document.createElement('br'));
            label.appendChild(document.createTextNode("Subscription Date:  "));
            var Appname=document.createTextNode(obj[key].value);
            var span = document.createElement('span');
            span.style.fontSize = "20px";
            span.style.color = "DodgerBlue";
            span.appendChild(Appname);
            label.appendChild(span);
            label.appendChild(document.createElement('br'));
            var createA = document.createElement('a');
            var createAText = document.createTextNode("Application's Endpoint");
            var span = document.createElement('span');
            span.style.fontSize = "20px";
            span.style.color = "DodgerBlue";
            span.appendChild(createAText);
            createA.setAttribute('href', "http://35.189.119.210:32000/redirect.php/"+key);
            createA.appendChild(span);
            label.appendChild(createA);
            label.appendChild(document.createElement('br'));
            label.appendChild(document.createElement('br'));
            document.getElementById("results").appendChild(document.createElement('br'));
        }
      }
  }

</script>
<script src="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.js"></script>


<script>
function filter() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    var filter_splited = filter.split(" ");
    var found=new Array();
    for (i = 0; i < filter_splited.length; i++) {
        if(filter_splited[i]!=""){
          for (var key in fiter_results) {
            a = fiter_results[key];
            if (a.toUpperCase().indexOf(filter_splited[i]) > -1) {
                found[key]=1;
            }
          }

        }
    }
    for(var key in fiter_results){

      if(found[key]!=1){

        document.getElementById(key).style.display = 'none';
      }else{
        document.getElementById(key).style.display = 'block'  ;
      }
    }
    if(filter==""){
        for(var key in fiter_results){
        document.getElementById(key).style.display = 'block'  ;
        }
    }

}
</script>
</body>
</html>
