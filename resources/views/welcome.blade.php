<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>HPEMG</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
         <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css"
   integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
   crossorigin=""/>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
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

.box {
  float: left;
  height: 20px;
  width: 20px;
  clear: both;
  border: 1px solid gray;
  border-radius: 7px;
}

</style>
        <!-- JS -->
        <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
        <script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lodash@4.17.19/lodash.min.js"></script>


<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"
   integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
   crossorigin=""></script>

    </head>
    <body>
        <div class="container">
            <h1>Texas Hospice Data</h1>
            <div class="row">
                <div class="col-sm-4">
                    <div id="legend"></div>
                </div>
                <div class="col-sm-7 offset-sm-1" style="padding-top: 15px;">
                    <div id="mapid"></div>
                </div>
            </div>
            <div class="row" style="padding-top: 15px;">
                <div class="col-sm-12">
                    <div id="cluster"></div>
                </div>
            </div>
        </div>

<script type="text/javascript" src="js/counties.js"></script>

<script>
    var countyCluster = [];
    var countyData = <?php echo json_encode($countyData ) ?>;
    var old_e = "";

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
                        color = "#c98efb";
                        break;
                    case 1:
                        color = "#FFFFFF";
                        break;
                    case 2:
                        color = "#fc8f99";
                        break;
                    case 3:
                        color = "#238198";
                        break;
                    case 4:
                        color = "#f7941d";
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
        if (old_e != "") {
            geojson.resetStyle(old_e.target);
        }
        old_e = e;

        layer = e.target;
        thisCountyData = _.find(countyData, {'county_name': layer.feature.properties.name.toUpperCase()})
        highlightFeature(e)
        thisClusterData = _.filter(countyData, {'cluster_num': thisCountyData.cluster_num})

        clusterHTML = clusterTemplate({thisClusterData:thisClusterData});
        document.querySelector('#cluster').innerHTML = clusterHTML;

        var table = $('#cluster-table').DataTable( {
            "bLengthChange": false,
            searching: false,
            paging: false,
            select: {
                style: 'single'
            }
        } );

        $('#cluster-table tbody').on( 'click', 'tr', function () {

            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
            else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        } );

        $('#button').click( function () {
            table.row('.selected').remove().draw( false );
        } );

        legendHTML = legendTemplate({county:thisCountyData});
        document.querySelector('#legend').innerHTML = legendHTML;
        var table = $('#legend-table').DataTable( {
            searching: false,
            paging: false,
            ordering: false,
            info: false
        } );
        //showData(thisCountyData)
    }

    function resetHighlight(e) {
        if (old_e == "" || old_e.target.feature.properties.name != e.target.feature.properties.name) {
        geojson.resetStyle(e.target);
        info.update();
        }
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

    function numberWithCommas(value,decimals) {
        var numDecimals = decimals ?? 2;
        return value.toFixed(numDecimals).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function showData(thisCountyData) {
        legend.innerHTML = '<h4> <div class="box" style="margin-top:6px;margin-right:8px;background-color:' + getColor(thisCountyData.county_name) +'"></div>' + thisCountyData.county_name + ' County</h4>' +
            '<div class="row bg-secondary text-light" style="font-size: smaller;"><div class="col-sm-8">2016 Pop:</div><div class="col-sm-4 text-right">'+numberWithCommas(thisCountyData.pop_2016,0)+'</div></div>'+
            '<div class="row bg-light text-dark" style="font-size: smaller;"><div class="col-sm-8">Days in Hospices:</div><div class="col-sm-4 text-right">'+numberWithCommas(thisCountyData.total_days,0)+'</div></div>'+
            '<div class="row bg-secondary text-light" style="font-size: smaller;"><div class="col-sm-8">Per Capita Medicare Payments:</div><div class="col-sm-4 text-right">$'+numberWithCommas(thisCountyData.medicare_payment_per_2016_capita)+'</div></div>'+
            '<div class="row bg-light text-dark" style="font-size: smaller;"><div class="col-sm-8">Per Capita Medicare Charge:</div><div class="col-sm-4 text-right">$'+numberWithCommas(thisCountyData.charge_amount_per_2016_capita)+'</div></div>'+
            '<div class="row bg-secondary text-light" style="font-size: smaller;"><div class="col-sm-8">Hospices per 100k:</div><div class="col-sm-4 text-right">'+numberWithCommas(thisCountyData.numhospices_per_2016_capita*100000)+'</div></div>'+
            '<div class="row bg-light text-dark" style="font-size: smaller;"><div class="col-sm-6">Hospice Beneficiaries:</div><div class="col-sm-6 text-right">'+numberWithCommas(thisCountyData.hospice_beneficiaries)+'</div></div>';
    }

	var mymap = L.map('mapid').setView([31.25,-99.9018], 6);

	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
			'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		id: 'mapbox.streets'
	}).addTo(mymap);

    var geojson = L.geoJson(countyGeoData, {style: style, onEachFeature: onEachFeature}).addTo(mymap);

    var info = L.control();

    var legend = document.getElementById('legend');

    info.onAdd = function (mymap) {
        this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
        this.update();
        return this._div;
    };

    // method that we will use to update the control based on feature properties passed
    info.update = function (props,thisCountyData) {
        this._div.innerHTML = (props ?
            '<h4> <div class="box" style="margin-top:5px;margin-right:8px;background-color:' + getColor(thisCountyData.county_name) +'"></div>' + props.name + ' County</h4><b>Population: ' + numberWithCommas(thisCountyData.pop_2016,0) +'</b>'+
            '<br/>Click for more information'
            : 'Hover over a county');
    };
    info.addTo(mymap);
</script>

<script type="text/template" id="legend-template">

  <table id="legend-table" class="stripe">
    <thead>
        <tr>
            <th><div class="box" style="margin-top:3px;margin-right:8px;background-color:<%- getColor(county.county_name) %>"></div><%- _.startCase(_.toLower(county.county_name)) %> County</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
      <tr>
        <td>Population</td><td class="text-right"><%- numberWithCommas(county.pop_2016,0) %></td>
      </tr>
      <tr>
        <td>Days in Hospices</td><td class="text-right"><%- numberWithCommas(county.total_days,0) %></td>
      </tr>
      <tr>
        <td>Per Capita Medicare Payments</td><td class="text-right">$<%- numberWithCommas(county.medicare_payment_per_2016_capita) %></td>
      </tr>
      <tr>
        <td>Per Capita Medicare Charge</td><td class="text-right">$<%- numberWithCommas(county.charge_amount_per_2016_capita) %></td>
      </tr>
      <tr>
        <td>Hospices per 100K</td><td class="text-right"><%- numberWithCommas(county.numhospices_per_2016_capita*100000) %></td>
      </tr>
      <tr>
        <td>Hospice Beneficiaries</td><td class="text-right"><%- numberWithCommas(county.hospice_beneficiaries) %></td>
      </tr>
    </tbody>
  </table>
  <div class="row"><div class="text-center col-sm-12"><button class="btn btn-outline-secondary mt-3" type="submit">MORE</button></div></div>

</script>

<script type="text/template" id="grid-template">
  <h2>Other <span class="box" style="display: inline-block;float: none;background-color:<%- getColor(thisClusterData[0].county_name) %>"></span> Counties</h2>
  <table id="cluster-table" class="display" style="cursor:pointer" >
    <thead>
        <tr style="font-size: 14px;">
            <th>County</th>
            <th>Population</th>
            <th>Days in Hospices</th>
            <th class="text-center">Medicare Payments<br />Per Capita</th>
            <th class="text-center">Medicare Charge<br />Per Capita</th>
            <th>Hospices Per 100K</th>
            <th>Hospice Beneficiaries</th>
    </thead>
    <tbody>
    <% thisClusterData.forEach((cluster) => { %>
      <tr style="font-size: 14px;">
        <td><%- _.startCase(_.toLower(cluster.county_name)) %></td>
        <td class="text-right"><%- numberWithCommas(cluster.pop_2016,0) %></td>
        <td class="text-right"><%- numberWithCommas(cluster.total_days,0) %></td>
        <td class="text-right">$<%- numberWithCommas(cluster.medicare_payment_per_2016_capita) %></td>
        <td class="text-right">$<%- numberWithCommas(cluster.charge_amount_per_2016_capita) %></td>
        <td class="text-right"><%- numberWithCommas(cluster.numhospices_per_2016_capita*100000) %></td>
        <td class="text-right"><%- numberWithCommas(cluster.hospice_beneficiaries) %></td>
      </tr>
    <% }) %>
    </tbody>
  </table>
</script>
<script>
    var str = document.querySelector('#grid-template').textContent;
    var clusterTemplate = _.template(str);
    str = document.querySelector('#legend-template').textContent;
    var legendTemplate = _.template(str);
</script>

    </body>
</html>
