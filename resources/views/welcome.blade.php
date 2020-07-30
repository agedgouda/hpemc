<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>HPEMC</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
         <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css"
   integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
   crossorigin=""/>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<style>
.dot {
  height: 25px;
  width: 25px;
  border-radius: 50%;
  display: inline-block;
}

#mapid { height: 580px; }


.info {
    padding: 6px 8px;
    font: 14px/16px Arial, Helvetica, sans-serif;
    background: white;
    background: rgba(255,255,255,0.8);
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
    border-radius: 5px;
}
.info h4 {
    margin: 0 0 5px;
    color: #777;
}



</style>
        <!-- JS -->
        <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lodash@4.17.19/lodash.min.js"></script>
        <script>
        /*
        $(document).ready( function () {
            $('#la-results').DataTable({
                "searching": false,
                "order": [[ 0, "desc" ]]
            });
        });
        */

        </script>

<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"
   integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
   crossorigin=""></script>

    </head>
    <body>
        <div class="container">
            <h1>Texas Hospice Data</h1>


<div id="mapid"></div>
        </div>

 <script type="text/javascript" src="js/counties.js"></script>

<script>
    var countyCluster = [];
    var countyData = <?php echo json_encode($countyData ) ?>;

    var rgbToHex = function (rgb) {
        rgb = Math.round(rgb);
        var hex = Number(rgb).toString(16);
        if (hex.length < 2) {
            hex = "0" + hex;
        }
        return hex;
    };

    function getColor(nameKey){

        for (var i=0; i < countyData.length; i++) {
            color = "#FFFFFF";
            if (countyData[i].county_name == nameKey.toUpperCase()) {
                switch (countyData[i].cluster_num) {
                    case 0:
                        color = "#FFBB11";
                        break;
                    case 1:
                        color = "#FFFFFF";
                        break;
                    case 2:
                        color = "#0000FF";
                        break;
                    case 3:
                        color = "#00FF00";
                        break;
                    case 4:
                        color = "#FF0000";
                        break;
                    }
                return color;
            }
        }
    }
	function style(feature) {
		return {
			weight: 2,
			opacity: 1,
			color: 'white',
			dashArray: '3',
			fillOpacity: 0.3,
			fillColor: getColor(feature.properties.name)
		};
	}

    function onEachFeature(feature, layer) {
        layer.on({
            mouseover: highlightFeature,
            mouseout: resetHighlight,
            click: zoomToFeature
        });
    }

    function zoomToFeature(e) {
        mymap.fitBounds(e.target.getBounds());
    }
    function resetHighlight(e) {
        geojson.resetStyle(e.target);
        info.update();
    }
    function highlightFeature(e) {
        var layer = e.target;

        layer.setStyle({
            weight: 2,
            color: '#FFF',
            dashArray: '',
            fillOpacity: 0.7
        });

        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
            layer.bringToFront();
        }

        thisCountyData = _.find(countyData, {'county_name': layer.feature.properties.name.toUpperCase()})
        info.update(layer.feature.properties,thisCountyData);
    }

    function numberWithCommas(value) {
        return value.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }



	var mymap = L.map('mapid').setView([30.5,-99.9018], 6);

	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
			'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		id: 'mapbox.streets'
	}).addTo(mymap);

    var geojson = L.geoJson(countyGeoData, {style: style, onEachFeature: onEachFeature}).addTo(mymap);

    var info = L.control();

    info.onAdd = function (mymap) {
        this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
        this.update();
        return this._div;
    };

    // method that we will use to update the control based on feature properties passed
    info.update = function (props,thisCountyData) {
        this._div.innerHTML =  (props ?
            '<h4>' + props.name + ' County</h4><b>Cluster: ' + thisCountyData.cluster_num +'</b>' +
            '<br/>2016 Population: '+numberWithCommas(thisCountyData.pop_2016)+
            '<br/>Average Age: '+numberWithCommas(thisCountyData.average_age)+
            '<br/>Percent Routine Home Health Days: '+numberWithCommas(thisCountyData.percent_routine_home_health_days)+'%'+
            '<p>Total Days in Hospices: '+numberWithCommas(thisCountyData.total_days)+
            '<p><b>Medicare Payments</b></br> Total: $'+numberWithCommas(thisCountyData.total_medicare_payment)+'<br/>Per capita: $'+numberWithCommas(thisCountyData.medicare_payment_per_capita)+
            '<p><b>Charge Amount</b></br> Total: $'+numberWithCommas(thisCountyData.total_charge_amount)+'<br/>Per capita: $'+numberWithCommas(thisCountyData.charge_amount_per_capita)+
            '<p><b>Number of Hospices</b></br> Total: $'+numberWithCommas(thisCountyData.num_hospices)+'<br/>Per capita: $'+numberWithCommas(thisCountyData.num_hospices_per_capita)+
            '<p><b>Number of Hospice Beneficiaries</b></br> Total: $'+numberWithCommas(thisCountyData.hospice_beneficiaries)+'<br/>Per capita: $'+numberWithCommas(thisCountyData.hospice_beneficiaries_per_capita)
            : 'Hover over a county');
    };
    info.addTo(mymap);
</script>





    </body>
</html>




