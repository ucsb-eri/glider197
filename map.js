var map;
// var infoWindow = new google.maps.InfoWindow();

google.maps.event.addDomListener(window, "load", function() {

	map = new google.maps.Map(document.getElementById("map"), {
		center: {lat: 34.389550, lng:-119.724733},
		scrollwheel: false,
    	suppressInfoWindows: false,
	});

	var kmlPath = "http://glider197.eri.ucsb.edu/var/test.kml";
	//var kmlPath = "http://fablio-mini.eri.ucsb.edu/test/sites/glider197/var/test.kml";
	var urlSuffix = (new Date).getTime().toString();
	var layer = new google.maps.KmlLayer(kmlPath + '?' + urlSuffix, {
		map: map,
		suppressInfoWindows: true,
	});

	layer.addListener("click", function(event) {
		var cdata = event.featureData.description;
		var description = cdata.replace("<![CDATA[", "").replace("]]>", "");
		console.log(cdata);
		document.getElementById("content-window").innerHTML = description;
	});

	var listener = google.maps.event.addListener(map, "idle", function() {
		if (map.getZoom() > 14) map.setZoom(14);
		google.maps.event.removeListener(listener);
	});

});
