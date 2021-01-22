<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {


  $request_body = file_get_contents('php://input');
  $phpobj=json_decode($request_body,true);
//  print_r($phpobj);
  /*EstimoteTelemetryFullPacket(macAddress=EstimoteMacAddress(address=F8:FB:E0:6E:F9:A3),
  rssi=-45,
  timestamp=172544516002162,
  identifier=ACF28C79FAE433D13D18BAF7650B5B1B,
  magnetometer=EstimoteMagnetometer(xAxis=-0.0078125, yAxis=-0.0078125, zAxis=-0.0078125),
  ambientLightInLux=353894.39999999997,
  uptime=EstimoteUptimeDuration(value=198, timeUnit=MINUTES),
  temperatureInCelsiusDegrees=30.25,
  batteryVoltageInMilliVolts=3036,
  batteryLevelPercentage=84,
  acceleration=EstimoteAcceleration(xAxis=-0.047244094488188976, yAxis=-0.015748031496062992, zAxis=0.9921259842519685),
  motionState=false,
  currentMotionDuration=EstimoteMotionDuration(value=3, timeUnit=HOURS),
  previousMotionDuration=EstimoteMotionDuration(value=3, timeUnit=SECONDS),
  gpio=EstimoteGpio(pin0=true, pin1=true, pin2=true, pin3=true),
  pressure=1.677721599609375E7)*/

$id=$phpobj["identifier"];
$ch = curl_init( "http://10.48.0.9:1027/v2/op/update" );
   /*$payload = '{
     "temperature": {
       "value": '.$phpobj["temperatureInCelsiusDegrees"].',
       "type": "Number"
     },
     "pressure": {
       "value": '.$phpobj["pressure"].',
       "type": "Number"
     },
     "ambientLight": {
       "value": '.$phpobj["ambientLightInLux"].',
       "type": "Number"
     }
   }';*/
  $payload= '{
  "actionType": "update",
  "entities": [
    {
      "type": "Sensor",
      "id": "'.$id.'",
		"ambientLight": {
        "type": "Number",
        "value": '.$phpobj["ambientLightInLux"].',
        "metadata": {
            "UOM": {
                "type": "Text",
                "value": "Lux"
            }
        }
    },
    "pressure": {
        "type": "Number",
        "value": '.$phpobj["pressure"].',
        "metadata": {
            "UOM": {
                "type": "Text",
                "value": "pa"
            }
        }
    },
    "temperature": {
        "type": "Number",
        "value": '.$phpobj["temperatureInCelsiusDegrees"].',
        "metadata": {
            "UOM": {
                "type": "Text",
                "value": "Celsius"
            }
        }
    }
    }

  ]
}';

   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json","X-Auth-token: thismagickeyfororion","Fiware-Service: tourguide",'Fiware-ServicePath:/citySensors'));
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );

   curl_setopt($ch, CURLOPT_HEADER,true);

   # Send request.
   $result = curl_exec($ch);

   curl_close($ch);
   echo $result;

}
?>
