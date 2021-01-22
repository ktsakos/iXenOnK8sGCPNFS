<?php
session_start();
if (!empty($_SESSION["access_token"] )) {
//   $Sub_paylaod= $_POST['checkbox'];
  $access_token=$_SESSION["access_token"];
  $USERNAME=$_SESSION["USERNAME"];
  $PASSWORD=$_SESSION["PASSWORD"];
//echo "User: ".$USERNAME."<br><br><br>";


}else{
    //echo "you must login first ";
    header("Location: index.php");
  }
?>
<!DOCTYPE html>
<html lang="en">
<title>Subscribe to Applications</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
/* width */
.container {
  display: block;
  position: relative;
  padding-left: 35px;
  font-size: 22px;
  cursor: pointer;

  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 7px;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
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
#App_scope{
  background-image: url('');
  background-position: 10px 12px;
  background-repeat: no-repeat;
  padding: 5px 20px 5px 40px;
}
body,h1,h2,h3,h4,h5 {font-family: "Poppins", sans-serif}
body {font-size:16px;}
input {
  border: 1px solid transparent;
  background-color: #f1f1f1;
  padding: 10px;
  font-size: 16px;
}
input[type=button] {
  background-color: DodgerBlue;
  color: #fff;
  cursor: pointer;
}
.w3-half img{margin-bottom:-6px;margin-top:16px;opacity:0.8;cursor:pointer}
.w3-half img:hover{opacity:1}

</style>
<body>

<!-- Sidebar/menu -->
<nav class="w3-sidebar w3-red w3-collapse w3-top w3-large w3-padding" style="z-index:3;width:300px;font-weight:bold;background-color: DodgerBlue!important;" id="mySidebar"><br>
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-button w3-hide-large w3-display-topleft" style="width:100%;font-size:22px">Close Menu</a>
  <div class="w3-container">
    <h3 class="w3-padding-64"><b id="show_username"></b></h3>
  </div>
  <div class="w3-bar-block">
    <a  onclick="appSub();" class="w3-bar-item w3-button w3-hover-white">Subscribe</a>
    <a href="ConPortal.php" onclick="w3_close()" class="w3-bar-item w3-button w3-hover-white"style="position:absolute; bottom:0;width:90%; overflow: hidden;">Back</a>
  </div>
</nav>



<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:340px;margin-right:40px">
  <div class="w3-container" id="services" style="margin-top:75px;">
    <h1 class="w3-xxxlarge w3-text-red"style="color: DodgerBlue!important;"><b>Subscribe to Applications.</b></h1>
    <div id="mydiv"  class="autocomplete" style="width:650px;border-radius: 25px!important;">
      <div id="p" style="text-align: center!important;"></div>
      <br>
      <div class="custom-select" style="width:100%;margin : 0 auto;display: block;height:42px!important;border-radius: 25px!important;">
        <select id="App_scope" placeholder="e.g: myapp" style="height:42px;width:100%;border-radius: 25px!important;"onchange="myFunction()" >
          <option value="" disabled selected>Select Application Scope</option>
          <option  value="enviromental_monitoring">Enviromental Monitoring</option>
          <option  value="assisted_living">Assisted Living</option>
        </select>
      </div>
    </div>
    <br>
    <input type="text" id="myInput" onkeyup="filter()" placeholder="Search for Keywords.." title="Type in a name" style="height:42px!important;width:650px;border-radius: 25px!important;">
    <div id="container"style="width:50%;height:50%;">
        <div id="div1"style="width:100%;">

              <br><br>

                <div class="autocomplete"  style="width:100%;" >

                  <form id="form1" action="javascript:Mashup();"  method="post" >


                  <div  data-simplebar id=results class="autocomplete" style="width:650px!important;height:500px!important;"><br><br><br><br>

                  </div>
                  </form>


                </div>
                <br><br>


        </div>
    </div>
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
function myFunction(){
  document.getElementById("results").innerHTML = "";

  var service_path=document.getElementById("App_scope").value;
  Apps_to_retrieve={service_path:service_path};
  Apps_to_retrieve=JSON.stringify(Apps_to_retrieve);

  var payload;
  fiter_results=[];
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      payload = this.responseText;
      obj = JSON.parse(payload);
      for (var i in obj) {
        for (var key in obj[i]) {
          temp=obj[i];
          if(key!="id"& key!="type"){
              var checkBox = document.createElement("input");
              var label = document.createElement("label");
              var span_custom = document.createElement('span');
              span_custom.classList.add('checkmark');
              checkBox.type = "checkbox";
              checkBox.value = key;
              checkBox.id=key+key;
              checkBox.name = "checkbox[]";
              checkBox.class = "messageCheckbox";
              checkBox.checked=false;
              label.id=key;
              label.value=temp[key].value;
              fiter_results[key]=temp[key].value;
              //label.style.display = 'block';
              //checkBox.style.display = 'block';
              label.classList.add('container');
              label.appendChild(checkBox);
              label.appendChild(span_custom);
              document.getElementById("results").appendChild(label);

              ////Add label values;
              label.appendChild(document.createTextNode("Application Name:  "));
              var Appname=document.createTextNode(key);
              var span = document.createElement('span');
              span.style.fontSize = "24px";
              span.style.color = "DodgerBlue";
              span.appendChild(Appname);
              label.appendChild(span);
              label.appendChild(document.createElement('br'));
              label.appendChild(document.createTextNode("Application Description:  "));
              var Appname=document.createTextNode(temp[key].value);
              var span = document.createElement('span');
              span.style.fontSize = "24px";
              span.style.color = "DodgerBlue";
              span.appendChild(Appname);
              label.appendChild(span);
              label.appendChild(document.createElement('br'));
              label.appendChild(document.createElement('br'));
              document.getElementById("results").appendChild(document.createElement('br'));}
          }

    }
    }
  };
  xhttp.open("POST", "RECVqueryApp.php", true);
  xhttp.send(Apps_to_retrieve);
}
</script>

<script>
function appSub() {
if (confirm("Press ok to subscribe")) {
    var choosen=new Array();
    var cboxes = document.getElementsByName('checkbox[]');
    var len = cboxes.length;
    for (var i=0; i<len; i++) {
      if(cboxes[i].checked)
        choosen.push(cboxes[i].value);
    }
    var objJON=JSON.stringify(choosen);
    var payload;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        payload = this.responseText;
        alert(payload);
      }
    };
    xhttp.open("POST", "CustomerSubCB.php", true);
    xhttp.send(objJON);
    getpermission(objJON);
  }
}

function getpermission(objJON) {

  var payload;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      payload = this.responseText;
    }
  };
  xhttp.open("POST", "CustomerPermission.php", true);
  xhttp.send(objJON);
}

</script>
<script src="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.js"></script>


<script>
var fiter_results=new Array();

</script>
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
        document.getElementById(key+key).style.display = 'none';
      }else{
        document.getElementById(key).style.display = 'block'  ;
        document.getElementById(key+key).style.display = 'block'  ;
      }
    }
    if(filter==""){
        for(var key in fiter_results){
        document.getElementById(key).style.display = 'block'  ;
        document.getElementById(key+key).style.display = 'block'  ;
        }
    }

}
</script>
</body>
</html>
