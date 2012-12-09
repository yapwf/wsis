<?php 

include("esSearchHelper.php");
  
  function createTableRow($label, $value) {
  	if ($value != null && $value != "") {
  		echo "<tr><td>$label</td><td>";
  		
  		echo $value;
  		
  		echo "</td></tr>";
  	}
  }

  function convertInToCm($inches) {
  	$cm = $inches * 2.54;
  	return number_format($cm, 1, '.', '');
  }

  function printSnowFlakes($count) {
  	for ($i=0; $i<$count; $i++) {
  		echo "<img src='../images/snowflake-med.png' />";
  	}
  }

  function printSuns($count) {
  	for ($i=0; $i<$count; $i++) {
  		echo "<img src='../images/sun-med.png'/>";
  	}
  }

  function displayRecommendationWidget($rec) {
	?>
	<div class="span2 recResultDetail">
		<?php 
			$dtime = new DateTime($rec['date']);
			$displayDate = $dtime->format('l, M j');
		?>
		<a href="resort-detail?resort=<?=$rec['resort'] ?>&date=<?=$rec['date'] ?>"><?= $displayDate ?></a>
		<br/>
		<img src="../images/snowflake<?= $rec['powder']['rating'] ?>.png"/><br/>
		<img src="../images/bluebird<?= $rec['bluebird']['rating'] ?>.png"/><br/>
	</div>
	<?php 
}

if (isset($_GET['resort'])) {
  $resort = $_GET['resort'];
  $date = $_GET['date'];

  //$documentPath = "http://localhost:9200/recommendations/recommendations/whitepass_2012-11-20";
  $documentPath = "http://localhost:9200/recommendations/recommendations/" . $resort . "_" . $date;
  $json_string = file_get_contents($documentPath);
  $parsedJson = json_decode($json_string);
  if ($parsedJson->{'exists'} == 'true') {
	  $parsedJson = $parsedJson->{'_source'};

	  $resortName = $parsedJson->{'resort_name'};
	  $resortDetailsPath = "http://localhost:9200/resorts/resorts/${resort}";
	  //echo $resortDetailsPath;
	  $json_string = file_get_contents($resortDetailsPath);
  	  $parsedResort = json_decode($json_string);
  	  $resortInfo = $parsedResort->{'_source'};

	  $date = $parsedJson->{'date'}; 
	  $dt = new DateTime($date);
	  $dateFormatted = $dt->format('l, F j, Y');

	  $state = $parsedJson->{'state'};
	  $latitude = $parsedJson->{'latitude'};
	  $longitude = $parsedJson->{'longitude'};
	  $powderRating = $parsedJson->{'powder'}->{'rating'};

	  $isDomestic = True;
	  if (strcmp("F", $resortInfo->{'domestic'}) == 0) {
	  	$isDomestic = False;
	  }
	  //$snowForecast = $parsedJson->{''};

	  echo "<h2>$resortName, $state</h2>";
	  echo "<p><a target='new' href='" . $resortInfo->{'resort_website'} . "'>" . $resortInfo->{'resort_website'} . "</a></p>";
	?>

	<h3>Recommendations for <?=$dateFormatted ?></h3>
	Precipitation Potential: 
	<?php 
		if ($isDomestic) {
			echo $parsedJson->{'powder'}->{'snow_new'} . "\" of fresh snow (last 24 hours), "
			 . $parsedJson->{'powder'}->{'snow_forecast'} . "\" of more snow during the day, and "
			 . $parsedJson->{'powder'}->{'snow_prev'} . "\" the last 72 hours. ";
		} else {
			echo convertInToCm($parsedJson->{'powder'}->{'snow_new'}) . " cm of fresh snow (last 24 hours), "
			 . convertInToCm($parsedJson->{'powder'}->{'snow_forecast'}) . " cm of more snow during the day, and "
			 . convertInToCm($parsedJson->{'powder'}->{'snow_prev'}) . " cm the last 72 hours. ";
		}
	?>
	<br/><br/>
	<table>
	<tr>
		<td><h4>Powder</h4></td>
		<td><?php printSnowFlakes($powderRating); ?></td>
	</tr>
	<tr>
		<td><h4>Bluebird</h4></td>
		<td><?php printSuns($parsedJson->{'bluebird'}->{'rating'}); ?></td>
	</tr>
	</table>
	

	<i>NOAA Weather Summary</i>: <?=$parsedJson->{'bluebird'}->{'weather_summary'} ?><br/>
		
	<div style="clear:both;">
	<h4>Recommendations based on the following</h4>
	<?php 
		echo "<a target='new' href='http://forecast.weather.gov/MapClick.php?unit=0&lg=english&FcstType=text&lat=" . 
			$resortInfo->{'latitude'} . "&lon=" . $resortInfo->{'longitude'} . "'>$resortName NOAA Forecast</a><br/>\n";
		echo "<a target='new' href='http://www.snow-forecast.com/resorts/" . $resortInfo->{'snowforecast_id'} . "/6day/mid'/>" . 
			"$resortName Snow-Forecast.com Weather</a>";
	?>

<div class="divider"></div>

<h4>Mountain Stats</h4>
	<table>
	<?php 
		$unitStr = " (m, approx)";
		if ($isDomestic) {
			$unitStr = " (ft, approx)";
		}
		createTableRow("Base Elevation" . $unitStr, $resortInfo->{'base_elevation'});
		createTableRow("Summit Elevation" . $unitStr, $resortInfo->{'summit_elevation'});
		createTableRow("Latitude", $resortInfo->{'latitude'});
		createTableRow("Longitude", $resortInfo->{'longitude'});
	?>

	</table>

<div class="divider"></div>

<h4>Future Recommendations for <?=$resortName ?></h4>
	
	<?php
	 	//Retrieve the additional Forecasted Date Info
		$requestAttributes = array (
			"resort" => $resort, 
			"dateMin" => $date,
			"sortDate" => "asc",
			"size" => 5
		);
		$results = search($requestAttributes);

		foreach ($results["hits"]["hits"] as $rec) {
			$rec = $rec["_source"];
			displayRecommendationWidget($rec);
		}
	?>

<div class="divider"> </div>
<h4>Previous Recommendations for <?=$resortName ?></h4>
<?php
	//Retrieve the additional Forecasted Date Info
	$requestAttributes = array (
		"resort" => $resort, 
		"dateMax" => $date,
		"sortDate" => "desc",
		"size" => 3
	);
	$results = search($requestAttributes);

	foreach ($results["hits"]["hits"] as $rec) {
		$rec = $rec["_source"];
		displayRecommendationWidget($rec);
	}

?>




<?php
	}

	else {
		echo "Invalid Page";
	}

} else {
	?>
		<h2>Resorts Listing</h2>
		See below for our complete list of all supported ski resorts. We are always adding more resorts and we 
		apologize if your resort of choice is not yet available. If there is something that we have missed, please 
		feel free to give us some <a href="feedback">feedback</a>.

		<div style="clear:both"></div>
	<?php
	$states = getStates();
	//echo json_encode(getStates());
	foreach ($states["facets"]["State"]["terms"] as $state) {
		 $stateName = $state["term"];
		 echo "<div class='stateSummary'>";
		 echo "<h3>". $stateName . "</h3>";
		 $resorts = getResortsForState($stateName);
		 $today = date("Y-m-d");
		 foreach($resorts["hits"]["hits"] as $resort) {
		 	$resortid = $resort["_id"];
		 	echo "<a href='resort-detail?resort=$resortid&date=$today'>" . $resort["fields"]["name"] . "</a><br/>\n";
		 }
		 echo "</div>";
 	}
}

//}

?>