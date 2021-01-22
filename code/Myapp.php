<?php
session_start();
if (!empty($_SESSION["access_token"] )) {
//   $Sub_paylaod= $_POST['checkbox'];
  $access_token=$_SESSION["access_token"];
  $USERNAME=$_SESSION["USERNAME"];
  $PASSWORD=$_SESSION["PASSWORD"];

}else{
    //echo "you must login first ";
    header("Location: index.php");
  }
?>
<!DOCTYPE html>
<html lang="en">
<title>My Applications</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.css">

<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
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
#App_scope{
  background-image: url('');
  background-position: 10px 12px;
  background-repeat: no-repeat;
  padding: 5px 20px 5px 40px;
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
    <a href="devPortal.php" onclick="w3_close()" class="w3-bar-item w3-button w3-hover-white"style="position:absolute; bottom:0;width:90%; overflow: hidden;">Back</a>
  </div>
</nav>



<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:340px;margin-right:40px">
  <div class="w3-container" id="services" style="margin-top:75px;">
    <h1 class="w3-xxxlarge w3-text-red"style="color: DodgerBlue!important;"><b>My Applications.</b></h1>
    <div id="mydiv"  class="autocomplete" style="width:600px;border-radius: 25px!important;">
      <div id="p" style="text-align: center!important;"></div>
      <br>
      <div class="custom-select" style="width:100%;margin : 0 auto;display: block;height:42px!important;border-radius: 25px!important;">
        <select id="App_scope" placeholder="e.g: myapp" style="height:42px;width:100%;border-radius: 25px!important;"onchange="myFunction()" >
          <option value="" disabled selected>Select Application Scope</option>
          <option  value="enviromental_monitoring">Enviromental Monitoring</option>
          <option  value="assisted_living">Assisted Living</option>
        </select>
      </div>
    </div><br>
    <input type="text" id="myInput" onkeyup="filter()" placeholder="Search for Keywords.." title="Type in a name" style="height:42px!important;width:600px;border-radius: 25px!important;">

    <div id="container"style="width:50%;height:50%;">
        <div id="div1"style="width:100%;">

                <div class="autocomplete"  style="width:100%;" >

                  <form id="form1" >

                    <div data-simplebar id=results class="autocomplete" style="width:600px!important;height:500px!important;"><br><br><br><br>

                    </div>

                  </form>

                </div>

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

<body>
</html>





<!--Make sure the form has the autocomplete function switched off:-->
<script>
function myFunction(){
  var service_path=document.getElementById("App_scope").value;
  var Username = "<?php echo $USERNAME; ?>";
  Apps_to_retrieve={Username:Username,service_path:service_path};
  Apps_to_retrieve=JSON.stringify(Apps_to_retrieve);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      payload = this.responseText;
      fiter_results=[];

      Draw_results(payload);
    }
  };


  xhttp.open("POST", "Apps_to_retrieve.php", true);
  xhttp.send(Apps_to_retrieve);


}
</script>

<script type="text/javascript">
    var Username = "<?php echo $USERNAME; ?>";
    document.getElementById("show_username").innerHTML =Username;

</script>
<script src="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.js"></script>

<script>
function Draw_results(payload){
  document.getElementById("results").innerHTML = "";
  var obj;
  obj=payload;
  //obj=JSON.stringify(obj);
  if(obj.search("error")==-1){
    obj=JSON.parse(obj);
    for (var key in obj) {
      if(key!="id"& key!="type"){

          var label = document.createElement("label");
          label.id=key;
          document.getElementById("results").appendChild(label);
          label.appendChild(document.createElement('br'));
          label.appendChild(document.createTextNode("Application Name:  "));
          var Appname=document.createTextNode(key);
          var span = document.createElement('span');
          span.style.fontSize = "20px";
          span.style.color = "DodgerBlue";
          span.appendChild(Appname);
          label.appendChild(span);
          label.appendChild(document.createElement('br'));
          label.appendChild(document.createTextNode("Application Description:  "));
          fiter_results[key]=obj[key].value;
          var App_description=document.createTextNode(obj[key].value);
          var span = document.createElement('span');
          span.style.fontSize = "20px";
          span.style.color = "DodgerBlue";
          span.appendChild(App_description);
          label.appendChild(span);
          label.appendChild(document.createElement('br'));
          label.appendChild(document.createTextNode("Application ID:  "));
          var App_id=document.createTextNode(obj[key].metadata.appid.value);
          var span = document.createElement('span');
          span.style.fontSize = "20px";
          span.style.color = "DodgerBlue";
          span.appendChild(App_id);
          label.appendChild(span);
          label.appendChild(document.createElement('br'));
          document.getElementById("results").appendChild(document.createElement('br'));
      }
    }
  }
}
</script>
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
