<!DOCTYPE HTML>
<html>
<head>
	<title>CC Tracker</title>
	<meta charset="UTF-8" />
	<!--<meta http-equiv="refresh" content="60" />-->
	<link rel="stylesheet" type="text/css" href="pids.css" />

	<!--Accuweather stylesheets cut down for necessary styles-->
	<link rel="stylesheet" href="pages.css" />
	<link rel="stylesheet" href="icons.css" />

	<script type="text/javascript" src="jquery-1.4.3.js"></script>
	<script type="text/javascript" src="slides.jquery.js"></script>

	<script type="text/javascript">
		$(function(){
			$('#rail-status').slides({
				play: 5000,
				preload: true
			});
		});
	</script>
</head>

<body id="forecast-extended" class="forecast">
<div id="wrapper">
<?php
$traindata = @simplexml_load_file("http://api.wmata.com/StationPrediction.svc/GetPrediction/B07?api_key=p3qdqjq843eyvf25vahctshb");
$track2 = array();
if (isset($traindata->Trains->AIMPredictionTrainInfo))
{
	foreach ($traindata->Trains->AIMPredictionTrainInfo as $train)
	{
		//$attr_check = $train->DestinationCode->attributes("i",true); //!isset($attr_check['nil'])
		if ($train->Group == 2)
		{
			$x = array();
			$x['Line'] = (string)$train->Line;
			$x['Car'] = (string)$train->Car;
			$x['Destination'] = (string)$train->Destination;
			$x['Min'] = (string)$train->Min;
			$track2[] = $x;
			unset($x);
		}
	}
}
$track2 = array_pad($track2,3,array('Line' => '', 'Car' => '', 'Destination' => '', 'Min' => ''));
?>
<table id="pids">
	<thead>
		<tr>
			<th>LN</th>
			<th>CAR</th>
			<th>DEST</th>
			<th>MIN</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($track2 as $train)
	{
?>	<tr>
			<td><?php echo($train['Line']);?></td>
			<td><?php echo($train['Car']);?></td>
			<td><?php echo($train['Destination']);?></td>
			<td><?php echo($train['Min']);?></td>
	</tr>
	<?php }
?></tbody>
</table>

<div id="mid-wrapper">
<div id="rail-status"><div class="slides_container">
<?php
$incidentdata = @simplexml_load_file("http://api.wmata.com/Incidents.svc/Incidents?api_key=p3qdqjq843eyvf25vahctshb");
if (count($incidentdata->Incidents->Incident) == 0)
{
	?><p>There are no rail incidents at this time.</p><?php
}
else
{
	foreach ($incidentdata->Incidents->Incident as $incident)
	{
		echo("<div>\n");
		$linesaffected = explode(';',(string)$incident->LinesAffected);
		echo("<p class=\"line\">");
		foreach ($linesaffected as $line)
		{
			/*switch ($line)
			{
				case "RD":
					echo("<span class=\"red\">&bull;</span>");
					break;
				case "GR":
					echo("<span class=\"green\">&bull;</span>");
					break;
				case "BL":
					echo("<span class=\"blue\">&bull;</span>");
					break;
				case "OR":
					echo("<span class=\"orange\">&bull;</span>");
					break;
				case "YL":
					echo("<span class=\"yellow\">&bull;</span>");
					break;
			}*/
			if ($line != "") {echo("<span class=\"".trim($line)."\">&bull;</span>");}
			//print_r("-".$line.":".ord($line)."-");
		}
		unset($linesaffected);
		echo("</p>\n");
		echo("<p>" . (string)$incident->Description . "</p>\n");
		echo("</div>\n");
	}
}
?>
</div></div>

<div id="weather">
<?php
$current = "";
$accuweather = new DOMDocument();
@$accuweather->loadHTMLFile("http://www.accuweather.com/en/us/takoma-park-md/20912/current-weather/2135463");
if ($accuweather->getElementById('detail-now')) {$current = $accuweather->saveXML($accuweather->getElementById('detail-now'));}
$current = str_replace("&#13;","",$current);
//$current = str_replace("src=\"/adc2010/images/icons-wind/","src=\"http://www.accuweather.com/adc2010/images/icons-wind/",$current);
echo($current);
?>
</div>
</div>
</div>

<div id="bus-status"><p>Bus information is not yet available.</p></div>

<div id="clock">
<?php date_default_timezone_set('America/New_York'); ?>
<p class="time"><?php echo(date("g:i A")); ?></p>
<p class="date"><?php echo(date("n/j/Y, l")); ?></p>
<!--<p class="ip"><?php //echo($_SERVER['HTTP_HOST']); ?></p>-->
<p id="countdown" class="ip"></p>
<script type="text/javascript">
var count = 60;
var counter = document.getElementById('countdown');
var interval = setInterval("countdown()",1000);
function countdown() {
	if (count < 1) {
		counter.style.color = '#ff0000';
		if (count % 10 == 0) {location.reload();}
	}
	counter.innerHTML = count;
	count--;
}
</script>
</div>
<?php
/*To add:
	*Weather highs and lows for the day
	*Bus information
*/
?>
</div>
</body>
</html>
