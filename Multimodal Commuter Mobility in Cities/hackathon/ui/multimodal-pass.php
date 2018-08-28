<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>ConfirmTkt Pass</title>
	<?php
		$passID = isset($_GET["passid"])?$_GET["passid"]:'';
		$passExpireDt = isset($_GET["passexpdt"])?$_GET["passexpdt"]:'';
		$routeStart = isset($_GET["routeStart"])?$_GET["routeStart"]:'';
		$routeEnd = isset($_GET["routeEnd"])?$_GET["routeEnd"]:'';
	?>

	<style>
		td,th{
			padding: 8px;
		}
	</style>
</head>
<body>
	
	<div class="container">
		<h2 style="color:#888;text-align: center;margin-bottom: 50px; margin-top:20px;">Delhi Route Pass</h2>

		<img src="qr-code.png" alt="qr-code" style="max-width:150px; margin:auto; margin-bottom:20px; display:block;">


		<table border="1" style="max-width: 550px; width: 100%; margin:auto;">
			<tr><th>Pass Id</th><th>Route</th><th>Valid Date</th></tr>
			<tr><td><?php echo $passID;?></td><td style="font-size:12px;"><b><?php echo $routeStart."</b> to <b>".$routeEnd;?></b></td><td><?php echo $passExpireDt;?></td></tr>
		</table>
	</div>

	<script type="text/javascript" src="https://www.confirmtkt.com/booking/assets/js/jquery.min.js">
		</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
		</script>
	</body>
</html>