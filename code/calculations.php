<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$request_body = file_get_contents('php://input');
	$payload=json_decode($request_body,true);
	$globalmessage="";
	$globaloperations=0;
	$tagmessage=array();
	$appname=$payload[0]["appname"];
/////////////////////////////////////////////////////////////////////colors
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}
function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
////////////////////////////////////////////////////////////////////functions
function calculate_H_AVERAGE_7D($attr,$ids){
	  $today = date("Y-m-d");
		$stop_date = date('Y-m-d', strtotime($today . ' -7 day'));
		//$new_time = date("Y-m-d H:i:s", strtotime('+5 hours'))
		if($attr=='temperature'){
				$UOM='Celsius';
		}
		elseif ($attr=='pressure') {
				$UOM='Pa';
		}
		elseif ($attr=='ambientLight') {
				$UOM='Lux';
		}
		$message="['HOUR','$attr'],";
		$array = array();
		$dates_array = array();
		$dates_count = array();
		for ($x = 0; $x < count($ids); $x++) {
			$ch = curl_init( "http://10.48.0.14:8667/STH/v1/contextEntities/type/Sensor/id/$ids[$x]/attributes/$attr?aggrMethod=sum&aggrPeriod=day&dateFrom=$stop_date" );
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: tourguide","X-Auth-token: thismagickeyforcomet",'Fiware-ServicePath: /citySensors'));
			$result = curl_exec($ch);
			//echo $result;
			curl_close($ch);
			$payload=json_decode($result,true);
			$array[$ids[$x]]=$payload;
		}
		for ($x = 0; $x < count($ids); $x++) {
					$current_Sensor=$ids[$x];
					$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
					for($k=0;$k<$origin_count;$k++){
						$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
						$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

						for ($y = 0; $y < count($points); $y++) {
							$YMD_date = substr($_id_date,0,10);
							$offset=$points[$y]['offset'];
							$offset--;
							$YMD_date = date('Y-m-d', strtotime($YMD_date ."+$offset day"));
							$samples=$points[$y]['samples'];
							$sum=$points[$y]['sum'];
							if (empty($results[$YMD_date])){
									$results[$YMD_date]=($sum/$samples);
							}else{
									$results[$YMD_date]=$results[$YMD_date]+($sum/$samples);
							}
						}
					}
		}
		foreach ($results as $YMD_date=>$value) {
						$results[$YMD_date]=$value/count($ids);
						$message=$message."['$YMD_date',$results[$YMD_date]],";
		}
		//print_r($results);
		$color=random_color();

		$message=substr($message, 0, -1);
		$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">
			google.charts.load(\'current\', {\'packages\':[\'corechart\']});
			google.charts.setOnLoadCallback(drawChart);
			function drawChart() {
				var data = google.visualization.arrayToDataTable(['.$message.']);
				var options = {
					vAxis: {
						title: \''.$UOM.'\'
					},
					series: {
											0: { color: \'#'.$color.'\' }
					},
					title: \'Average '.$attr.' for the last 7 Days\',
					curveType: \'function\',
				};

				var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

				chart.draw(data, options);
			}
		</script>';
		$GLOBALS['globaloperations']++;
  }
function calculate_H_MAX_7D($attr,$ids){
		  $today = date("Y-m-d");
			$stop_date = date('Y-m-d', strtotime($today . ' -7 day'));
			//$new_time = date("Y-m-d H:i:s", strtotime('+5 hours'))
			if($attr=='temperature'){
					$UOM='Celsius';
			}
			elseif ($attr=='pressure') {
					$UOM='Pa';
			}
			elseif ($attr=='ambientLight') {
					$UOM='Lux';
			}
			$message="['HOUR','$attr'],";
			$array = array();
			$dates_array = array();
			$dates_count = array();
			for ($x = 0; $x < count($ids); $x++) {
				$ch = curl_init( "http://10.48.0.14:8667/STH/v1/contextEntities/type/Sensor/id/$ids[$x]/attributes/$attr?aggrMethod=max&aggrPeriod=day&dateFrom=$stop_date" );
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: tourguide","X-Auth-token: thismagickeyforcomet",'Fiware-ServicePath: /citySensors'));
				$result = curl_exec($ch);
				//echo $result;
				curl_close($ch);
				$payload=json_decode($result,true);
				$array[$ids[$x]]=$payload;
			}
			for ($x = 0; $x < count($ids); $x++) {
						$current_Sensor=$ids[$x];
						$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
						for($k=0;$k<$origin_count;$k++){
							$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
							$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

							for ($y = 0; $y < count($points); $y++) {
								$YMD_date = substr($_id_date,0,10);
								$offset=$points[$y]['offset'];
								$offset--;
								$YMD_date = date('Y-m-d', strtotime($YMD_date ."+$offset day"));
								$samples=$points[$y]['samples'];
								$max=$points[$y]['max'];
								if (empty($results[$YMD_date])){
										$results[$YMD_date]=$max;
								}else{
											if ($max>=$results[$YMD_date]){
													$results[$YMD_date]=$max;
											}
								}
							}
						}
			}
			foreach ($results as $YMD_date=>$value) {

							$message=$message."['$YMD_date',$results[$YMD_date]],";
			}
			$color=random_color();

			$message=substr($message, 0, -1);
			$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">
				google.charts.load(\'current\', {\'packages\':[\'corechart\']});
				google.charts.setOnLoadCallback(drawChart);
				function drawChart() {
					var data = google.visualization.arrayToDataTable(['.$message.']);
					var options = {
						vAxis: {
							title: \''.$UOM.'\'
						},
						series: {
							0: { color: \'#'.$color.'\' }
						},
						title: \'Maximum '.$attr.' for the last 7 Days\',
						curveType: \'function\',
					};

					var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

					chart.draw(data, options);
				}
			</script>';
			$GLOBALS['globaloperations']++;
	  }
function calculate_H_MIN_7D($attr,$ids){
				  $today = date("Y-m-d");
					$stop_date = date('Y-m-d', strtotime($today . ' -7 day'));
					//$new_time = date("Y-m-d H:i:s", strtotime('+5 hours'))
					if($attr=='temperature'){
							$UOM='Celsius';
					}
					elseif ($attr=='pressure') {
							$UOM='Pa';
					}
					elseif ($attr=='ambientLight') {
							$UOM='Lux';
					}
					$message="['HOUR','$attr'],";
					$array = array();
					$dates_array = array();
					$dates_count = array();
					for ($x = 0; $x < count($ids); $x++) {
						$ch = curl_init( "http://10.48.0.14:8667/STH/v1/contextEntities/type/Sensor/id/$ids[$x]/attributes/$attr?aggrMethod=min&aggrPeriod=day&dateFrom=$stop_date" );
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: tourguide","X-Auth-token: thismagickeyforcomet",'Fiware-ServicePath: /citySensors'));
						$result = curl_exec($ch);
						//echo $result;
						curl_close($ch);
						$payload=json_decode($result,true);
						$array[$ids[$x]]=$payload;
					}
					for ($x = 0; $x < count($ids); $x++) {
								$current_Sensor=$ids[$x];
								$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
								for($k=0;$k<$origin_count;$k++){
									$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
									$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

									for ($y = 0; $y < count($points); $y++) {
										$YMD_date = substr($_id_date,0,10);
										$offset=$points[$y]['offset'];
										$offset--;
										$YMD_date = date('Y-m-d', strtotime($YMD_date ."+$offset day"));
										$samples=$points[$y]['samples'];
										$min=$points[$y]['min'];
										if (empty($results[$YMD_date])){
												$results[$YMD_date]=$min;
										}else{
													if ($min<=$results[$YMD_date]){
															$results[$YMD_date]=$min;
													}
										}
									}
								}
					}
					foreach ($results as $YMD_date=>$value) {

									$message=$message."['$YMD_date',$results[$YMD_date]],";
					}
					//print_r($results);
					$color=random_color();

					$message=substr($message, 0, -1);
					$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">
						google.charts.load(\'current\', {\'packages\':[\'corechart\']});
						google.charts.setOnLoadCallback(drawChart);
						function drawChart() {
							var data = google.visualization.arrayToDataTable(['.$message.']);
							var options = {
								vAxis: {
									title: \''.$UOM.'\'
								},
								series: {
									0: { color: \'#'.$color.'\' }
								},
								title: \'Minimum '.$attr.' for the last 7 Days\',
								curveType: \'function\',
							};

							var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

							chart.draw(data, options);
						}
					</script>';
					$GLOBALS['globaloperations']++;
			  }
function calculate_H_AVERAGE_24H($attr,$ids){
	$today = date("Y-m-d H:i:s");
	$stop_date = date('Y-m-dTH:i:s', strtotime($today . ' -24 hour'));

	if($attr=='temperature'){
			$UOM='Celsius';
	}
	elseif ($attr=='pressure') {
			$UOM='Pa';
	}
	elseif ($attr=='ambientLight') {
			$UOM='Lux';
	}
	$message="['HOUR','$attr'],";
	$array = array();
	$dates_array = array();
	$dates_count = array();
	for ($x = 0; $x < count($ids); $x++) {
		$ch = curl_init( "http://10.48.0.14:8667/STH/v1/contextEntities/type/Sensor/id/$ids[$x]/attributes/$attr?aggrMethod=sum&aggrPeriod=hour&dateFrom=$stop_date" );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: tourguide","X-Auth-token: thismagickeyforcomet",'Fiware-ServicePath: /citySensors'));
		$result = curl_exec($ch);
		curl_close($ch);
		$payload=json_decode($result,true);
		$array[$ids[$x]]=$payload;
	}
	for ($x = 0; $x < count($ids); $x++) {
				$current_Sensor=$ids[$x];
				$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
				for($k=0;$k<$origin_count;$k++){
					$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
					$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

					for ($y = 0; $y < count($points); $y++) {
						$YMD_date = $_id_date;
						$offset=$points[$y]['offset'];
						$T_purposes[$current_Sensor][$attr][$_id_date][$offset]=1;
						$YMD_date = date('g:i a', strtotime($YMD_date ."+$offset hour"));
						$samples=$points[$y]['samples'];
						if($samples>0){
							if (empty($count_contri[$YMD_date])){
									$count_contri[$YMD_date]=1;
							}else{
									$count_contri[$YMD_date]++;
							}
						}
						$sum=$points[$y]['sum'];
						if (empty($results[$YMD_date])){
								$results[$YMD_date]=($sum/$samples);
						}else{
								$results[$YMD_date]=$results[$YMD_date]+($sum/$samples);
						}
					}
				}
	}
	foreach ($results as $YMD_date=>$value) {
					$results[$YMD_date]=$value/$count_contri[$YMD_date];
					$message=$message."['$YMD_date',$results[$YMD_date]],";
	}
	//print_r($results);
	$color=random_color();
	$message=substr($message, 0, -1);
	$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">
		google.charts.load(\'current\', {\'packages\':[\'corechart\']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable(['.$message.']);
			var options = {
				vAxis: {
					title: \''.$UOM.'\'
				},
				series: {
					0: { color: \'#'.$color.'\' }
				},
				title: \'Average '.$attr.' for the last 24 Hours\',
				curveType: \'function\',

			};

			var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

			chart.draw(data, options);
		}
	</script>';
	$GLOBALS['globaloperations']++;
}
function calculate_H_MIN_24H($attr,$ids){
	$today = date("Y-m-d H:i:s");
	$stop_date = date('Y-m-dTH:i:s', strtotime($today . ' -24 hour'));

	if($attr=='temperature'){
			$UOM='Celsius';
	}
	elseif ($attr=='pressure') {
			$UOM='Pa';
	}
	elseif ($attr=='ambientLight') {
			$UOM='Lux';
	}
	$message="['HOUR','$attr'],";
	$array = array();
	$dates_array = array();
	$dates_count = array();
	for ($x = 0; $x < count($ids); $x++) {
		$ch = curl_init( "http://10.48.0.14:8667/STH/v1/contextEntities/type/Sensor/id/$ids[$x]/attributes/$attr?aggrMethod=min&aggrPeriod=hour&dateFrom=$stop_date" );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: tourguide","X-Auth-token: thismagickeyforcomet",'Fiware-ServicePath: /citySensors'));
		$result = curl_exec($ch);
		curl_close($ch);
		$payload=json_decode($result,true);
		$array[$ids[$x]]=$payload;
	}
	for ($x = 0; $x < count($ids); $x++) {
				$current_Sensor=$ids[$x];
				$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
				for($k=0;$k<$origin_count;$k++){
					$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
					$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

					for ($y = 0; $y < count($points); $y++) {
						$YMD_date = $_id_date;
						$offset=$points[$y]['offset'];
						$T_purposes[$current_Sensor][$attr][$_id_date][$offset]=1;
						$YMD_date = date('g:i a', strtotime($YMD_date ."+$offset hour"));
						$samples=$points[$y]['samples'];
						$min=$points[$y]['min'];
						if (empty($results[$YMD_date])){
								$results[$YMD_date]=$min;
						}else{
									if($min<=$results[$YMD_date]){
										$results[$YMD_date]=$min;
									}
						}
					}
				}
	}
	foreach ($results as $YMD_date=>$value) {
					//$results[$YMD_date]=$value/count($ids);
					$message=$message."['$YMD_date',$results[$YMD_date]],";
	}
	//print_r($results);
	$color=random_color();

	$message=substr($message, 0, -1);
	$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">
		google.charts.load(\'current\', {\'packages\':[\'corechart\']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable(['.$message.']);
			var options = {
				vAxis: {
					title: \''.$UOM.'\'
				},
				series: {
					0: { color: \'#'.$color.'\' }
				},
				title: \'Minimum '.$attr.' for the last 24 Hours\',
				curveType: \'function\',
			};

			var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

			chart.draw(data, options);
		}
	</script>';
	$GLOBALS['globaloperations']++;
}
function calculate_H_MAX_24H($attr,$ids){
	$today = date("Y-m-d H:i:s");
	$stop_date = date('Y-m-dTH:i:s', strtotime($today . ' -24 hour'));

	if($attr=='temperature'){
			$UOM='Celsius';
	}
	elseif ($attr=='pressure') {
			$UOM='Pa';
	}
	elseif ($attr=='ambientLight') {
			$UOM='Lux';
	}

	$message="['HOUR','$attr'],";
	$array = array();
	$dates_array = array();
	$dates_count = array();
	for ($x = 0; $x < count($ids); $x++) {
		$ch = curl_init( "http://10.48.0.14:8667/STH/v1/contextEntities/type/Sensor/id/$ids[$x]/attributes/$attr?aggrMethod=max&aggrPeriod=hour&dateFrom=$stop_date" );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: tourguide","X-Auth-token: thismagickeyforcomet",'Fiware-ServicePath: /citySensors'));
		$result = curl_exec($ch);
		curl_close($ch);
		$payload=json_decode($result,true);
		$array[$ids[$x]]=$payload;
	}
	for ($x = 0; $x < count($ids); $x++) {
				$current_Sensor=$ids[$x];
				$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
				for($k=0;$k<$origin_count;$k++){
					$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
					$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

					for ($y = 0; $y < count($points); $y++) {
						$YMD_date = $_id_date;
						$offset=$points[$y]['offset'];
						$T_purposes[$current_Sensor][$attr][$_id_date][$offset]=1;
						$YMD_date = date('g:i a', strtotime($YMD_date ."+$offset hour"));
						$samples=$points[$y]['samples'];
						$max=$points[$y]['max'];
						if (empty($results[$YMD_date])){
								$results[$YMD_date]=$max;
						}else{
									if($max>=$results[$YMD_date]){
										$results[$YMD_date]=$max;
									}
						}
					}
				}
	}
	foreach ($results as $YMD_date=>$value) {
					//$results[$YMD_date]=$value/count($ids);
					$message=$message."['$YMD_date',$results[$YMD_date]],";
	}
	//print_r($results);
	$color=random_color();
	$message=substr($message, 0, -1);
	$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">
		google.charts.load(\'current\', {\'packages\':[\'corechart\']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable(['.$message.']);
			var options = {
				series: {
					0: { color: \'#'.$color.'\' }
				},
				vAxis: {
					title: \''.$UOM.'\'
				},
				title: \'Maximum '.$attr.' for the last 24 Hours\',
				curveType: \'function\',
			};

			var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

			chart.draw(data, options);
		}
	</script>';
	$GLOBALS['globaloperations']++;
}
function calculate_LIVE($attr,$ids){

		if($attr=='temperature'){
				$UOM='Celsius';
		}
		elseif ($attr=='pressure') {
				$UOM='Pa';
		}
		elseif ($attr=='ambientLight') {
				$UOM='Lux';
		}
	$message="['Sensor','$attr', { role: 'style' } ],";
	$array = array();
	$dates_LIVE = array();
	for ($x = 0; $x < count($ids); $x++) {
		$ch = curl_init( "http://10.48.0.14:8667/STH/v1/contextEntities/type/Sensor/id/$ids[$x]/attributes/$attr?lastN=1");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: tourguide","X-Auth-token: thismagickeyforcomet",'Fiware-ServicePath: /citySensors'));
		$result = curl_exec($ch);
		//echo $result;
		curl_close($ch);
		$payload=json_decode($result,true);
		$array[$ids[$x]]=$payload;

	}
	for ($x = 0; $x < count($ids); $x++) {
				$count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
				for ($y = 0; $y < $count; $y++) {
					$attrValue=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$y]["attrValue"] ;
					if (!array_key_exists($rest, $dates_array)) {
							$dates_LIVE[$ids[$x]]=$attrValue;
					}

				}
	}

	foreach ($dates_LIVE as $id=>$value) {
					$color=random_color();
					$message=$message."['$id',$value,'$color'],";

	}

	$message=substr($message, 0, -1);
	$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">

    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable(['.$message.']);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
				vAxis: {
					title: \''.$UOM.'\'
				},

        title: "Maximum '.$attr.' for the last 24 Hours",
        bar: {groupWidth: "10%"},
				colors: [\'#'.$color.'\'],
				legend: { position: "none" },

      };
      var chart = new google.visualization.ColumnChart(document.getElementById('.$GLOBALS['globaloperations'].'));
      chart.draw(data, options);
  }
  </script>';
  $GLOBALS['globaloperations']++;
}
function calculate_H_MAX_24H_Column($attr,$ids){
	$today = date("Y-m-d H:i:s");
	$stop_date = date('Y-m-dTH:i:s', strtotime($today . ' -24 hour'));

	if($attr=='temperature'){
			$UOM='Celsius';
	}
	elseif ($attr=='pressure') {
			$UOM='Pa';
	}
	elseif ($attr=='ambientLight') {
			$UOM='Lux';
	}

	$message="['Sensor','$attr', { role: 'style' } ],";
	$array = array();
	$dates_array = array();
	$dates_count = array();
	for ($x = 0; $x < count($ids); $x++) {
		$ch = curl_init( "http://10.48.0.14:8667/STH/v1/contextEntities/type/Sensor/id/$ids[$x]/attributes/$attr?aggrMethod=max&aggrPeriod=hour&dateFrom=$stop_date" );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: tourguide","X-Auth-token: thismagickeyforcomet",'Fiware-ServicePath: /citySensors'));
		$result = curl_exec($ch);
		curl_close($ch);
		$payload=json_decode($result,true);
		$array[$ids[$x]]=$payload;
	}
	for ($x = 0; $x < count($ids); $x++) {
				$current_Sensor=$ids[$x];
				$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
				for($k=0;$k<$origin_count;$k++){
					$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
					$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

					for ($y = 0; $y < count($points); $y++) {
						$YMD_date = $_id_date;
						$offset=$points[$y]['offset'];
						$T_purposes[$current_Sensor][$attr][$_id_date][$offset]=1;
						$YMD_date = date('g:i a', strtotime($YMD_date ."+$offset hour"));
						$samples=$points[$y]['samples'];
						$max=$points[$y]['max'];
						if (empty($results[$YMD_date])){
								$results[$YMD_date]=$max;
						}else{
									if($max>=$results[$YMD_date]){
										$results[$YMD_date]=$max;
									}
						}
					}
				}
	}
	$color=random_color();
	foreach ($results as $YMD_date=>$value) {

					$message=$message."['$YMD_date',$results[$YMD_date],'$color'],";
	}
	$message=substr($message, 0, -1);
	$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">

    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable(['.$message.']);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
				vAxis: {
					title: \''.$UOM.'\'
				},
        title: "Maximum '.$attr.' for the last 24 Hours",
        bar: {groupWidth: "10%"},
				colors: [\'#'.$color.'\'],


      };
      var chart = new google.visualization.ColumnChart(document.getElementById('.$GLOBALS['globaloperations'].'));
      chart.draw(data, options);
  }
  </script>';
  $GLOBALS['globaloperations']++;
}
function calculate_H_MIN_24H_Column($attr,$ids){
	$today = date("Y-m-d H:i:s");
	$stop_date = date('Y-m-dTH:i:s', strtotime($today . ' -24 hour'));

	if($attr=='temperature'){
			$UOM='Celsius';
	}
	elseif ($attr=='pressure') {
			$UOM='Pa';
	}
	elseif ($attr=='ambientLight') {
			$UOM='Lux';
	}

	$message="['Sensor','$attr', { role: 'style' } ],";
	$array = array();
	$dates_array = array();
	$dates_count = array();
	for ($x = 0; $x < count($ids); $x++) {
		$ch = curl_init( "http://10.48.0.14:8667/STH/v1/contextEntities/type/Sensor/id/$ids[$x]/attributes/$attr?aggrMethod=min&aggrPeriod=hour&dateFrom=$stop_date" );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: tourguide","X-Auth-token: thismagickeyforcomet",'Fiware-ServicePath: /citySensors'));
		$result = curl_exec($ch);
		curl_close($ch);
		$payload=json_decode($result,true);
		$array[$ids[$x]]=$payload;
	}
	for ($x = 0; $x < count($ids); $x++) {
				$current_Sensor=$ids[$x];
				$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
				for($k=0;$k<$origin_count;$k++){
					$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
					$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

					for ($y = 0; $y < count($points); $y++) {
						$YMD_date = $_id_date;
						$offset=$points[$y]['offset'];
						$T_purposes[$current_Sensor][$attr][$_id_date][$offset]=1;
						$YMD_date = date('g:i a', strtotime($YMD_date ."+$offset hour"));
						$samples=$points[$y]['samples'];
						$min=$points[$y]['min'];
						if (empty($results[$YMD_date])){
								$results[$YMD_date]=$min;
						}else{
									if($min<=$results[$YMD_date]){
										$results[$YMD_date]=$min;
									}
						}
					}
				}
	}
	$color=random_color();
	foreach ($results as $YMD_date=>$value) {

					$message=$message."['$YMD_date',$results[$YMD_date],'$color'],";
	}
	$message=substr($message, 0, -1);
	$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">

    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable(['.$message.']);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
				vAxis: {
					title: \''.$UOM.'\'
				},
        title: "Minimum '.$attr.' for the last 24 Hours",
        bar: {groupWidth: "10%"},
				colors: [\'#'.$color.'\'],


      };
      var chart = new google.visualization.ColumnChart(document.getElementById('.$GLOBALS['globaloperations'].'));
      chart.draw(data, options);
  }
  </script>';
  $GLOBALS['globaloperations']++;
}
function calculate_H_AVERAGE_24H_Column($attr,$ids){
	$today = date("Y-m-d H:i:s");
	$stop_date = date('Y-m-dTH:i:s', strtotime($today . ' -24 hour'));

	if($attr=='temperature'){
			$UOM='Celsius';
	}
	elseif ($attr=='pressure') {
			$UOM='Pa';
	}
	elseif ($attr=='ambientLight') {
			$UOM='Lux';
	}
	$message="['Sensor','$attr', { role: 'style' } ],";
	$array = array();
	$dates_array = array();
	$dates_count = array();
	for ($x = 0; $x < count($ids); $x++) {
		$ch = curl_init( "http://10.48.0.14:8667/STH/v1/contextEntities/type/Sensor/id/$ids[$x]/attributes/$attr?aggrMethod=sum&aggrPeriod=hour&dateFrom=$stop_date" );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: tourguide","X-Auth-token: thismagickeyforcomet",'Fiware-ServicePath: /citySensors'));
		$result = curl_exec($ch);
		curl_close($ch);
		$payload=json_decode($result,true);
		$array[$ids[$x]]=$payload;
	}
	for ($x = 0; $x < count($ids); $x++) {
				$current_Sensor=$ids[$x];
				$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
				for($k=0;$k<$origin_count;$k++){
					$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
					$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

					for ($y = 0; $y < count($points); $y++) {
						$YMD_date = $_id_date;
						$offset=$points[$y]['offset'];
						$T_purposes[$current_Sensor][$attr][$_id_date][$offset]=1;
						$YMD_date = date('g:i a', strtotime($YMD_date ."+$offset hour"));
						$samples=$points[$y]['samples'];
						if($samples>0){
							if (empty($count_contri[$YMD_date])){
									$count_contri[$YMD_date]=1;
							}else{
									$count_contri[$YMD_date]++;
							}
						}
						$sum=$points[$y]['sum'];
						if (empty($results[$YMD_date])){
								$results[$YMD_date]=($sum/$samples);
						}else{
								$results[$YMD_date]=$results[$YMD_date]+($sum/$samples);
						}
					}
				}
	}
	$color=random_color();
	foreach ($results as $YMD_date=>$value) {
					$results[$YMD_date]=($results[$YMD_date]/$count_contri[$YMD_date]);
					$message=$message."['$YMD_date',$results[$YMD_date],'$color'],";
	}
	$message=substr($message, 0, -1);
	$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">
  google.charts.load("current", {packages:["corechart"]});
  google.charts.setOnLoadCallback(drawChart);
  function drawChart() {
      var data = google.visualization.arrayToDataTable(['.$message.']);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
				vAxis: {
					title: \''.$UOM.'\'
				},
        title: "Average '.$attr.' for the last 24 Hours",
        bar: {groupWidth: "10%"},
				colors: [\'#'.$color.'\'],


      };
      var chart = new google.visualization.ColumnChart(document.getElementById('.$GLOBALS['globaloperations'].'));
      chart.draw(data, options);
  }
  </script>';
  $GLOBALS['globaloperations']++;
}
/////////////////////////////////////////////////////////////////////BEGIN
for ($x = 0; $x < count($payload[0]["info"]); $x++) {
			   $attr = $payload[0]["info"][$x]["attribute"];
			   $operation = $payload[0]["info"][$x]["operation"];
			   $ids=$payload[0]["info"][$x]["ids"];

				 if($operation=="HMAX"){
					 calculate_H_MAX_24H($attr,$ids);
				 }
				 elseif($operation=="HMIN_column"){
					 calculate_H_MIN_24H_Column($attr,$ids);
				 }
				 elseif($operation=="HMAX_column"){
					 calculate_H_MAX_24H_Column($attr,$ids);
				 }
				 elseif($operation=="HAVERAGE_column"){
					 calculate_H_AVERAGE_24H_Column($attr,$ids);
				 }
				 elseif($operation=="HMIN"){
					 calculate_H_MIN_24H($attr,$ids);
				 }
				 elseif($operation=="HAVERAGE"){
					 calculate_H_AVERAGE_24H($attr,$ids);
				 }
				 elseif($operation=="LIVE"){
					 calculate_LIVE($attr,$ids);
				 }
				 elseif($operation=="HAVERAGE_7D"){
					 calculate_H_AVERAGE_7D($attr,$ids);
				 }
				 elseif($operation=="HMIN_7D"){
					 calculate_H_MIN_7D($attr,$ids);
				 }
				 elseif($operation=="HMAX_7D"){
					 calculate_H_MAX_7D($attr,$ids);
				 }
	}
	/////////////////////////////////////////////////////////////////////////END
	/////////////////////////////////////////////////////////////////////////PAGE construct
  $divs="";
	for ($x = 0; $x < count($payload[0]["info"]); $x++) {
				$divs=$divs.'<div id="'.$x.'" style="width:100%; height: 800px;float:right;"></div>';
	}

	echo '<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
	#P { text-align: center }
	body,h1,h2,h3,h4,h5 {font-family: "Poppins", sans-serif}
  body {font-size:16px;}
  .w3-half img{margin-bottom:-6px;margin-top:16px;opacity:0.8;cursor:pointer}
  .w3-half img:hover{opacity:1}
	</style>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

		'.$globalmessage.'
	</head>
	<body>
	<nav class="w3-sidebar w3-red  w3-top w3-large w3-padding" style="z-index:3;width:250px;font-weight:bold;background-color: DodgerBlue!important;" id="mySidebar"><br>
	<div class="w3-container" style=" margin-left:-5px!important;">
			<h3 class="w3-padding-64" style=" margin-left:-5px!important;">
					<b id="show_username" style=" margin-left:-5px!important;">USERNAME</b>
			</h3>
	</div>
		<div class="w3-bar-block " style="z-index:3;">
			<a href="/Mysubscription.php" onclick="w3_close()" class="w3-bar-item w3-button w3-hover-white"style="position:absolute; bottom:0;width:90%; overflow: hidden;">Back</a>
		</div>
	</nav>
	<div id="kouta" style="width:85%;float:right;">
		<div id="p"><h1 class="w3-xxxlarge w3-text-red"style="color: DodgerBlue!important;margin-top:80px;"><b>'.$appname.'</b></h1></div>
		'.$divs.'
	</div>
	</body>
	</html>
';}

?>
