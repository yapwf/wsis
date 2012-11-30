<?php

function addSearchParam(&$mustTerms, $parameter) {
	if (isset($_GET[$parameter])) {
		$term = createTerm($parameter, $_GET[$parameter]);
		array_push($mustTerms, $term);
	}
}

function buildQuery() {
	$match_all = array ( "match_all" => array() );

	$mustTerms = array();
	//addSearchParam($mustTerms, "date");
	addSearchParam($mustTerms, "region");
	addSearchParam($mustTerms, "state");
	addSearchParam($mustTerms, "rating");
	addSearchParam($mustTerms, "resort");

	if (isset($_GET['dateStart'])) {
		array_push($mustTerms, 
			array(
				"range" => array( 
					"date" => array(
						"gte" => $_GET['dateStart']
					)
				)
			) 
		);
	}
	if (isset($_GET['dateMin'])) {
		array_push($mustTerms, 
			array(
				"range" => array( 
					"date" => array(
						"gt" => $_GET['dateMin']
					)
				)
			) 
		);
	}
	if (isset($_GET['dateMax'])) {
		array_push($mustTerms, 
			array(
				"range" => array( 
					"date" => array(
						"lt" => $_GET['dateMax']
					)
				)
			) 
		);
	}

	$shouldTerms = array();

	if (isset($_GET["date"])) {
		$searchDates = explode(",", $_GET["date"]);
		foreach ($searchDates as $searchDate) {
			array_push($shouldTerms, createTerm("date", $searchDate));
		}
	}

	$mustNotTerms = array();
	createExcludeTerm($mustNotTerms, "regionx", "region");
	createExcludeTerm($mustNotTerms, "powderx", "powder.rating");
	createExcludeTerm($mustNotTerms, "bluebirdx", "bluebird.rating");
	createExcludeTerm($mustNotTerms, "statex", "state");
	/*
	if (isset($_GET["regionx"])) {
		$regions = explode(",", $_GET["regionx"]);
		foreach ($regions as $region) {
			array_push($mustNotTerms, createTerm("region", $region));
		}	
	}
	if (isset($_GET["regionx"])) {
		$regions = explode(",", $_GET["regionx"]);
		foreach ($regions as $region) {
			array_push($mustNotTerms, createTerm("region", $region));
		}	
	}*/

	$query = array(
			"bool" => array(
				"must" => $mustTerms,
				"must_not" => $mustNotTerms,
				"should" => $shouldTerms,
				"minimum_number_should_match" => 1
				)
			);
	// /print json_encode($query);
	//$query = array ( "query" => $match_all );
	return $query;
}

function createExcludeTerm(&$excludeArray, $parameter, $termName) {
	if (isset($_GET[$parameter]) && $_GET[$parameter] != "") {
		$regions = explode(",", $_GET[$parameter]);
		foreach ($regions as $region) {
			array_push($excludeArray, createTerm($termName, $region));
		}	
	}
}

function createTerm($name, $value) {
	
	$term = array(
			"term" => array(
				$name => $value
			)
		); 
	return $term;
}

function addSortTerm(&$sort, $term, $order) {
	array_push($sort, 
		array($term => array(
			"order" => $order
		)));
}

function buildSort() {
	$sort = array();

	if (isset($_GET['sortDate'])) {
		addSortTerm($sort, "date", $_GET['sortDate']);
	}

	addSortTerm($sort, "powder.rating", "desc");
	addSortTerm($sort, "bluebird.rating", "desc");
	addSortTerm($sort, "powder.snow_new", "desc");
	addSortTerm($sort, "powder.snow_forecast", "desc");

	return $sort;	
}

function buildFacets() {
	$facets = array(
		"Date" => array(
			"terms" => array(
				"field" => "date",
				"order" => "term"
			)
		),
		"Region" => array(
			"terms" => array(
				"field" => "region"
			)
		),
		"PowderRating" => array(
			"terms" => array(
				"field" => "powder.rating", 
				"order" => "reverse_term"
			)
		),
		"BluebirdRating" => array(
			"terms" => array(
				"field" => "bluebird.rating",
				"order" => "reverse_term"
			)
		),
		"State" => array(
			"terms" => array(
				"field" => "state"
			)
		)
	);
	return $facets;
}

function search($_GET) {
	$queryArray = array();
	if (isset($_GET['facet'])) {
		$queryArray = array (
			"query" => buildQuery(), 
			"facets" => buildFacets(), 
			"size" => "0"
		);
	} else {
		$queryArray = array (
			"query" => buildQuery(), 
			"sort" => buildSort()
		);
	}
		//"sort" => buildSort()
		//, "facets" => buildFacets()
	//);

	if (isset($_GET['size']) && is_numeric($pageSize = $_GET['size'])) {
		if (intval($pageSize) > 30) {
			$pageSize = 30;
		}
		$queryArray['size'] = $pageSize;
	}

	$queryObject = json_encode($queryArray);
	//print $queryObject;

	$ch = curl_init("http://localhost:9200/recommendations/_search");                                                                      
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "XGET");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $queryObject);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
	    'Content-Type: application/json',                                                                                
	    'Content-Length: ' . strlen($queryObject))                                                                       
	);      
	$esResult = curl_exec($ch);

	$results = json_decode($esResult, true);

	return $results;
}

?>