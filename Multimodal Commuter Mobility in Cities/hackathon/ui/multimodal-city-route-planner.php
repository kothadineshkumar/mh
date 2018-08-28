<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Delhi Multimodal Route planner</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	
	<?php
		$source    = isset($_REQUEST['source'])?$_REQUEST['source'].trim():'';
		$destination    = isset($_REQUEST['destination'])?$_REQUEST['destination'].trim():'';

		$getRoute = curl_init();

		$multimodalUrl = "http://119.81.208.134/api/movehack/metro/getroute?source=".curl_escape($getRoute,$source)."&destination=".curl_escape($getRoute,$destination);
		
		
			curl_setopt($getRoute,CURLOPT_URL,$multimodalUrl);
			curl_setopt($getRoute,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($getRoute,CURLOPT_CONNECTTIMEOUT, 4);
			curl_setopt($getRoute, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($getRoute, CURLOPT_SSL_VERIFYPEER, 0);
			$routeStr = curl_exec($getRoute);
			curl_close($getRoute);

			$dataRoute    = json_decode($routeStr,true);

			$route = $dataRoute["Routes"][0];

			$finalArrivalTime = date("h:i A", time());
			$len = sizeof($route["Steps"]);
			$routeStart = $route["Steps"][0]["Source"];
			$routeEnd = $route["Steps"][$len-1]["Destination"];
			//echo $multimodalUrl;
			//echo $routeStr;
			
	?>

</head>
<body>
	<div class="container" style="padding-top:20px;">
	<h2 style="color:#888;text-align: center;margin-bottom: 50px;">Delhi Multimodal Route planner</h2>
		<div class="row" style="max-width:650px; margin:auto; text-align:center;">
			<div class="col-md-4" style="margin-bottom:8px;"><input id="pac-input-src" type="text"
					placeholder="Enter your source"></div>
			<div class="col-md-1" style="text-align:center;margin-bottom:8px;">&#x2192;</div>
			<div class="col-md-4" style="margin-bottom:8px;"><input id="pac-input-dest" type="text"
					placeholder="Enter your destination"></div>

					<div class="col-md-3" style="margin-bottom:8px;"><button id="search-route-btn" type="button" class="btn btn-success">SEARCH</button></div>
		</div>

		<?php if($source != ''):?>

		<div class="row well well-lg" style="margin:0px; margin-top:50px; text-align:center; font-size:12px; padding:8px;">
			
			<div class="col-xs-4 col-sm-3">
				Total Distance<br><?php echo $route["Distance"];?> KM
			</div>
			<div class="col-xs-5 col-sm-3">
				Expected Time<br><?php echo $route["Time"];?> Minutes
			</div>
			<div class="col-xs-3 col-sm-3">
				Total Fare<br>Rs <?php echo $route["Fare"];?>
			</div>
			<div class="col-xs-12 col-sm-3">
				<button id="pay-btn" type="button" class="btn btn-danger">Get Pass</button>
			</div>
		</div>

		

		<div class="row well well-lg" style="font-size:12px;margin:0px;margin-top:10px;max-width: 550px;margin: auto;margin-top: 10px; padding:8px;">
			<div class="col-xs-12">
				<div style="display:inline-block;font-weight: bold;font-size:10px;border: solid 1px #777;padding: 4px;margin: 6px;">
					<?php echo $source?>	
				</div>

				<?php foreach($route["Steps"] as $key => $value):?>
						<br>
						<?php $metroColor = "#ccc";

						if(stripos($value["Name"],"red") !== FALSE){
							$metroColor = "#d25050";
						}

						if(stripos($value["Name"],"yellow") !== FALSE){
							$metroColor = "#f3ec0d";						
						}
						if(stripos($value["Name"],"blue") !== FALSE){
							$metroColor = "#6a9ddd";
						}
						if(stripos($value["Name"],"green") !== FALSE){
							$metroColor = "green";
						}
						if(stripos($value["Name"],"violet") !== FALSE){
							$metroColor = "violet";
						}
						if(stripos($value["Name"],"magenta") !== FALSE){
							$metroColor = "magenta";
						}
						if(stripos($value["Name"],"pink") !== FALSE){
							$metroColor = "#fa90c6";
						}
						if(stripos($value["Name"],"orange") !== FALSE){
							$metroColor = "orange";
						}
						
						$finalArrivalTime = $value["ArrivalTime"];
						?>


					<span class="badge"><?php echo $value["WalkingDistance"]; ?> KM WALK &#x2192;</span>
					<div style="display:inline-block;font-size:10px;border: solid 2px <?php echo $metroColor; ?>;padding: 4px;margin: 6px;">
						<?php echo $value["Type"]; ?> <b style="color:<?php echo $metroColor; ?>">(<?php echo $value["Name"]; ?>)</b><br>
						<span style="font-size:10px;">						
							<?php echo $value["Source"]; ?>(<?php echo $value["DepartureTime"]; ?>) <span>&#x2192;</span> <?php echo $value["Destination"]; ?>(<?php echo $value["ArrivalTime"]; ?>)<br>

						</span>
					</div>					
				<?php endforeach; ?>
				<br>
				<div style="display:inline-block;font-weight: bold;font-size:10px;border: solid 1px #777;padding: 4px;margin: 6px;">
					<?php echo $destination?>	
				</div>
			</div>
		<div>	
		
		<?php endif; ?>
	</div>
	<script type="text/javascript" src="https://www.confirmtkt.com/booking/assets/js/jquery.min.js">
		</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
		</script>
		 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1kDGB32MiWixb0o_FiLsew5H-0v_1l9s&libraries=places&callback=initMap" async defer></script>
<script>
	function initMap() {
		var srcInput = document.getElementById('pac-input-src');
		var destInput = document.getElementById('pac-input-dest');

		var delhiBounds = new google.maps.LatLngBounds(new google.maps.LatLng(28.933599, 77.517041), new google.maps.LatLng(28.375186, 76.689519));

	  var options = {
            bounds: delhiBounds,
          types: ['geocode'],
          componentRestrictions: {country: 'IN'}
        };

		var autocompleteSrc = new google.maps.places.Autocomplete(srcInput,options);
		var autocompleteDest = new google.maps.places.Autocomplete(destInput,options);
		
		var srcAddress = '';
		var destAddress = '';

		autocompleteSrc.addListener('place_changed', function() {
		debugger;
		var place = autocompleteSrc.getPlace();
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }
		  
          if (place.address_components) {
            srcAddress = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }
		});

		autocompleteDest.addListener('place_changed', function() {
			var place = autocompleteDest.getPlace();
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }
		  
          if (place.address_components) {
            destAddress = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }
		});

		$('#search-route-btn').click(function(){
			window.location.href = "/hackathon/multimodal-city-route-planner.php?source="+srcAddress+"&destination="+destAddress;
		});

		$('#pay-btn').click(function(){
			var prebookingId = Math.floor((Math.random() * 10000000000) + 1);
			var fare = "<?php echo $route["Fare"];?>";
			var passExpireDt = "<?php echo (date("F d, Y", time())).' '.$finalArrivalTime ?>";
			var routeStart = "<?php echo $routeStart; ?>";
			var routeEnd = "<?php echo $routeEnd; ?>";
			debugger;
			window.location.href = "https://www.confirmtkt.com/paymenthandler/sandbox/payupayment.php?prebookingid="+prebookingId+"&amount="+fare+"&firstname=hackathon&phone=9103081131&email=hackathon@confirmtkt.com&passexpdt="+passExpireDt+"&routeStart="+routeStart+"&routeEnd="+routeEnd;
		});
	}

	
</script>
</body>
</html>