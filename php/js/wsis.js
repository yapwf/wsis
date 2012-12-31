

var userLocation = new Object();

function errorHandler( err ) {
  //alert(err);
}

function showDistanceFilters(coords) {
	facets['coords'] = coords;
	$("#distanceSection").show();
	$("#distanceSort").show();
}


function success_handler(position) {
	var coords = position.coords.latitude + "," + position.coords.longitude;
    showDistanceFilters(coords);
    $.cookie("coords", coords, { expires: 3 });
    

}

function checkLocation() {
	if ($.cookie("coords")) {
		showDistanceFilters($.cookie("coords"));
	} else {
		if(navigator.geolocation) {
		    // Geolocation supported. Do something here.
		    var geolocation = navigator.geolocation;
			geolocation.getCurrentPosition(success_handler, errorHandler);	
		}
	}
}

function clickDateFilter(searchDate, index) {
		//if date not included
		if(facets['date'].value.indexOf(searchDate) < 0) {
			facets['date'].value += searchDate + ",";
			$("#date-x-" + index).show();			
			$("#date" + index).addClass("selected");
		} else {
			facets['date'].value = facets['date'].value.replace(searchDate + ",", "");
			$("#date-x-" + index).hide();
			$("#date" + index).removeClass("selected");
		}
		search();
	}

	function clickFilter(type, value, index) {
		if (facets[type].value.indexOf(value) < 0) {
			addFilter(type,value,index);
			$("#" + type + index).addClass("selected");
		}
		else {
			removeFilter(type,value,index);
			$("#" + type + index).removeClass("selected");
		}
	}

	function addFilter(type, value, index) {
		facets[type].value = value;
		//parameters += "&" + type + "=" + value;
		for (var i=0; i<facets[type].max; i++) {
			if (i != index) {
				$("#" + type + i).hide();
			} else {
				$("#" + type + "-x-" + i).show();
			}
		}
		search();

	}

	function removeFilter(type, value, index) {
		facets[type].value = "";
		$("#" + type + "-x-" + index).hide();
		for (var i=0; i<facets[type].max; i++) {
			$("#" + type + i).show();
		}
		search();
	}