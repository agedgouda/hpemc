<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>HPEMG</title>

        <link rel="shortcut icon" href="https://hpemg.com/wp-content/uploads/2018/08/favicon-32x32.png" />

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
    #modal-county-map { height: 368px; }

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

    .leaflet-top .leaflet-control {
        margin-top: 5px;
    }

</style>
        <!-- JS, Popper.js, and jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lodash@4.17.19/lodash.min.js"></script>


    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"
        integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
        crossorigin=""></script>
    </head>
    <body>
    @include('partials.county-modal')





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




<script type="text/template" id="info-template">
<h5 class="text-center">Population</h5>
<div class="row mt-1">
    <div class="col-sm-1 offset-sm-1">
        <div class="box" style="background-color:<%- getColor(.2) %>"></div>
    </div>
    <div class="col-sm-4">
        70k-450k
    </div>
    <div class="col-sm-1">
        <div class="box" style="background-color:<%- getColor(.4) %>"></div>
    </div>
    <div class="col-sm-4">
        25k-70k
    </div>
</div>
<div class="row mt-1">
    <div class="col-sm-1 offset-sm-1">
        <div class="box" style="background-color:<%- getColor(.6) %>"></div>
    </div>
    <div class="col-sm-4">
        13k-25k
    </div>
    <div class="col-sm-1">
        <div class="box" style="background-color:<%- getColor(.8) %>"></div>
    </div>
    <div class="col-sm-4">
         5k-13k
    </div>
</div>
<div class="row mt-1">
    <div class="col-sm-1 offset-sm-1">
        <div class="box" style="background-color:<%- getColor(1) %>"></div>
    </div>
    <div class="col-sm-4">
        <5k
    </div>
</div>
</script>

<script>

    var str = document.querySelector('#info-template').textContent;
    var infoTemplate = _.template(str);
    str = document.querySelector('#county-detail-template').textContent;
    var countyDetailTemplate = _.template(str);


    var countyCluster = [];
    var countyData = <?php echo json_encode($countyData ) ?>;
    var old_e = "";


    var showCountyModal = function (county) {
        county = JSON.parse(county)
        var countyContent = countyDetailTemplate({county:county}); //"<p>I Am Added Dynamically </p>";

        $("#modal-body").html(countyContent);
        $('#modal').modal('show');

        $('#modal-pop-table').DataTable( {
            searching: false,
            paging: false,
            ordering: false,
            info: false
        });
        $('#modal-doc-table').DataTable( {
            "pageLength": 25,
            paging: false,
            ordering: false,
        } );
        $('#modal-data-table').DataTable( {
            searching: false,
            paging: false,
            ordering: false,
            info: false
        } );

        if (county_map !== "") {
            county_map.remove();
        }

        setTimeout(function() {
            county_map.invalidateSize();
        }, 200);

        county_map = L.map('modal-county-map').setView(JSON.parse(county.location), 10);
    	    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
		    maxZoom: 18,
		    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
			    '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		    id: 'mapbox.streets'
	    }).addTo(county_map);



    };

    var rgbToHex = function (rgb) {
        rgb = Math.round(rgb);
        var hex = Number(rgb).toString(16);
        if (hex.length < 2) {
            hex = "0" + hex;
        }
        return hex;
    };

    function getColor(score){
        color_score = (1/(score*5))
        console.log(color_score)
        red = rgbToHex(255*(1-color_score));
        color = "#FF"+red+"00";
        return color;
    }


    function getScoreLabel(score){
        var label = "Populations ";
        switch (score) {
            case .2:
                label += "between 70,000-450,000 People"
                break;
            case .4:
                label += "between 25,000-70,000 People"
                break;
            case .6:
                label += "between 13,000-25,000 People"
                break;
            case .8:
                label += "between 5,000-13,000 People"
                break;
            case 1:
                label += "less than 5,000 People"
                break;
        }

        return label;
    }


    function getColorForCounty(nameKey){
        for (var i=0; i < countyData.length; i++) {
            color = "#FFFFFF";
            if (countyData[i].county_name == nameKey.toUpperCase()) {
                color = getColor(countyData[i].score);
                return color;
            }
        }
    }

    function getColor2(nameKey){
        for (var i=0; i < countyData.length; i++) {
            color = "#FFFFFF";
            if (countyData[i].county_name == nameKey.toUpperCase()) {
                switch (countyData[i].cluster_num) {
                    case 0:
                        color = "#FFFFFF";
                        break;
                    case 1:
                        color = "#fc8f99";
                        break;
                    case 2:
                        color = "#f7941d";
                        break;
                    case 3:
                        color = "#238198";
                        break;
                    case 4:
                        color = "#c98efb";
                        break;
                    }
                return color;
            }
        }
    }

    function getScoreColor(score){
        green =  rgbToHex(255*score);
        red = rgbToHex(255*(1-score));
        color = "#"+red+green+"00";
        return color;
    }

	function style(feature) {
		return {
			weight: 2,
			opacity: 1,
			color: 'white',
			dashArray: '3',
			fillOpacity: 0.3,
			fillColor: getColorForCounty(feature.properties.name)
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
        thisClusterData = _.filter(countyData, {'score': thisCountyData.score})

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
            thisCountyData = _.find(countyData, {'county_name': JSON.parse($(this).data().county) });
            showCountyModal(JSON.stringify(thisCountyData))
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
            //info.update();
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

        thisCountyData = _.find(countyData, {'county_name': layer.feature.properties.name.toUpperCase()});
    }

    function numberWithCommas(value,decimals) {
        var numDecimals = decimals ?? 2;
        return value.toFixed(numDecimals).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function showData(thisCountyData) {
        legend.innerHTML = '<h4> <div class="box" style="margin-top:6px;margin-right:8px;background-color:' + getScoreColor(thisCountyData.score) +'"></div>' + thisCountyData.county_name + ' County</h4>' +
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
        //this.update();
        infoHTML = infoTemplate();
        this._div.innerHTML = infoHTML;
        return this._div;
    };

    // method that we will use to update the control based on feature properties passed
    /*info.update = function (props,thisCountyData) {
        this._div.innerHTML = (props ?
            '<h4> <div class="box" style="margin-top:5px;margin-right:8px;background-color:' + getScoreColor(thisCountyData.score) +'"></div>' + props.name + ' County</h4><b>Population: ' + numberWithCommas(thisCountyData.pop_2016,0) +'</b>'+
            '<br/>Click for more information'
            : 'Hover over a county');
    };*/
    info.addTo(mymap);
</script>


<script type="text/template" id="legend-template">

  <table id="legend-table" class="stripe">
    <thead>
        <tr>
            <th><div class="box" style="margin-top:3px;margin-right:8px;background-color:<%- getColorForCounty(county.county_name) %>"></div><%- _.startCase(_.toLower(county.county_name)) %> County</th>
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
        <td>Hospice Beneficiaries</td><td class="text-right"><%- numberWithCommas(county.hospice_beneficiaries,0) %></td>
      </tr>
    </tbody>
  </table>
  <div class="row">
    <div class="text-center col-sm-12">
        <button class="btn btn-outline-secondary mt-3 open-county-details" onClick="showCountyModal('<%- JSON. stringify(county) %>') ">MORE</button>
    </div>
</div>

</script>

<script type="text/template" id="grid-template">
  <h2>Other Counties with <%- getScoreLabel(thisClusterData[0].score) %></h2>
  <table id="cluster-table" class="table table-striped table-bordered nowrap" style="cursor:pointer;width:100%" >
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
      <tr style="font-size: 14px;" data-county="<%- JSON.stringify(cluster.county_name) %>">
        <td><%- _.startCase(_.toLower(cluster.county_name)) %></td>
        <td class="text-right"><%- numberWithCommas(cluster.pop_2016,0) %></td>
        <td class="text-right"><%- numberWithCommas(cluster.total_days,0) %></td>
        <td class="text-right">$<%- numberWithCommas(cluster.medicare_payment_per_2016_capita) %></td>
        <td class="text-right">$<%- numberWithCommas(cluster.charge_amount_per_2016_capita) %></td>
        <td class="text-right"><%- numberWithCommas(cluster.numhospices_per_2016_capita*100000) %></td>
        <td class="text-right"><%- numberWithCommas(cluster.hospice_beneficiaries,0) %></td>
      </tr>
    <% }) %>
    </tbody>
  </table>
</script>
<script>
    str = document.querySelector('#grid-template').textContent;
    var clusterTemplate = _.template(str);
    str = document.querySelector('#legend-template').textContent;
    var legendTemplate = _.template(str);
    var county_map = "";
</script>

<!-- Modal -->

<div id="modal" class="modal fade" role='dialog'>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body" id= "modal-body">
                <p>Here the description starts here........</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
      </div>
  </div>









    </body>
</html>



