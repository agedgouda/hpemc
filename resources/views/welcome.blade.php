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
                        color = "#fc8f99";
                        break;
                    case 2:
                        color = "#f7941d";
                        break;
                    case 3:
                        color = "#238198";
                        break;
                    case 4:
                        color = "#FFFFFF";
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
        <td>Hospice Beneficiaries</td><td class="text-right"><%- numberWithCommas(county.hospice_beneficiaries,0) %></td>
      </tr>
    </tbody>
  </table>
  <div class="row">
    <div class="text-center col-sm-12">
        <button class="btn btn-outline-secondary mt-3" data-toggle="modal" data-target="#countyDetailModal" data-county="<%- JSON. stringify(county) %>" >MORE</button>
    </div>
</div>

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
        <td class="text-right"><%- numberWithCommas(cluster.hospice_beneficiaries,0) %></td>
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
    var county_map = "";
</script>

<!-- Modal -->
<div class="modal fade" id="countyDetailModal" tabindex="-1" role="dialog" aria-labelledby="countyDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
  <script>
    $(document).ready(function() {
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
    } );

    </script>

    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="countyDetailModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="row">
            <div class="col-sm-6">
                <table id="modal-pop-table" class="stripe">
                    <thead>
                        <tr>
                            <th>Population</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>2016</td><td class="text-right"><div id="modal-pop_2016"></div></td>
                    </tr>
                    <tr>
                        <td>2015</td><td class="text-right"><div id="modal-pop_2015"></div></td>
                    </tr>
                    <tr>
                        <td>2014</td><td class="text-right"><div id="modal-pop_2014"></div></td>
                    </tr>
                    <tr>
                        <td>2013</td><td class="text-right"><div id="modal-pop_2013"></div></td>
                    </tr>
                    <tr>
                        <td>2012</td><td class="text-right"><div id="modal-pop_2012"></div></td>
                    </tr>
                    <tr>
                        <td>2011</td><td class="text-right"><div id="modal-pop_2011"></div></td>
                    </tr>
                    <tr>
                        <td>2010</td><td class="text-right"><div id="modal-pop_2010"></div></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-6">
                <div id="modal-county-map"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table id="modal-data-table" class="stripe">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody style="text-transform: capitalize;">
                        <tr>
                            <td>Number of Hospices</td><td class="text-right"><div id="modal-num_hospices"></div></td>
                        </tr>
                        <tr>
                            <td>hospice beneficiaries</td><td class="text-right"><div id="modal-hospice_beneficiaries"></div></td>
                        </tr>
                        <tr>
                            <td>total days</td><td class="text-right"><div id="modal-total_days"></div></td>
                        </tr>
                        <tr>
                            <td>total medicare payment amount</td><td class="text-right"><div id="modal-total_medicare_standard_payment_amount"></div></td>
                        </tr>
                        <tr>
                            <td>per capita medicare payment</td><td class="text-right"><div id="modal-medicare_payment_per_2016_capita"></div></td>
                        </tr>
                        <tr>
                            <td>total medicare charge amount</td><td class="text-right"><div id="modal-total_charge_amount"></div></td>
                        </tr>
                        </tr>
                            <td>per capita medicare charge amount</td><td class="text-right"><div id="modal-charge_amount_per_2016_capita"></div></td>
                        </tr>
                        <tr>
                            <td>home health visit hours per day</td><td class="text-right"><div id="modal-home_health_visit_hours_per_day"></div></td>
                        </tr>
                        <tr>
                            <td>skilled nursing visit hours per day</td><td class="text-right"><div id="modal-skilled_nursing_visit_hours_per_day"></div></td>
                        </tr>
                        <tr>
                            <td>social service visit hours per day</td><td class="text-right"><div id="modal-social_service_visit_hours_per_day"></div></td>
                        </tr>
                        <tr>
                            <td>home health visit hours per day during week prior to death</td><td class="text-right"><div id="modal-home_health_visit_hours_per_day_during_week_prior_to_death"></div></td>
                        </tr>
                        <tr>
                            <td>skilled nursing visit hours per day during week prior to death</td><td class="text-right"><div id="modal-skilled_nursing_visit_hours_per_day_during_week_prior_to_death"></div></td>
                        </tr>
                        <tr>
                            <td>social service visit hours per day during week prior to death</td><td class="text-right"><div id="modal-social_service_visit_hours_per_day_during_week_prior_to_death"></div></td>
                        </tr>
                        <tr>
                            <td>percent routine home care days</td><td class="text-right"><div id="modal-percent_routine_home_care_days"></div></td>
                        <tr>
                            <td>numhospices per 2016 capita</td><td class="text-right"><div id="modal-numhospices_per_2016_capita"></div></td>
                        </tr>
                        <tr>
                            <td>hospice beneficiaries per 2016 capita</td><td class="text-right"><div id="modal-hospice_beneficiaries_per_2016_capita"></div></td>
                        </tr>
                        <tr>
                            <td>geriatric doctors per 2016 capita</td><td class="text-right"><div id="modal-geriatric_doctors_per_2016_capita"></div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-sm-12">
                <table id="modal-doc-table" class="stripe">
                    <thead>
                        <tr>
                            <th>Medical Specialty</th>
                            <th>Number of Specialists</th>
                        </tr>
                    </thead>
                    <tbody style="text-transform: capitalize;">
                    <tr>
                        <td>Total</td><td class="text-right"><div id="modal-total_doctors"></div></td>
                    </tr>
                    <tr>
                        <td>Addiction Medicine</td><td class="text-right"><div id="modal-addiction_medicine"></div></td>
                    </tr>
                    <tr>
                        <td>Allergy Immunology</td><td class="text-right"><div id="modal-allergy_immunology"></div></td>
                    </tr>
                    <tr>
                        <td>Anesthesiology</td><td class="text-right"><div id="modal-anesthesiology"></div></td>
                    </tr>
                    <tr>
                        <td>Anesthesiology Assistant</td><td class="text-right"><div id="modal-anesthesiology_assistant"></div></td>
                    </tr>
                    <tr>
                        <td>Audiologist</td><td class="text-right"><div id="modal-audiologist"></div></td>
                    </tr>
                    <tr>
                        <td>Cardiac Electrophysiology</td><td class="text-right"><div id="modal-cardiac_electrophysiology"></div></td>
                    </tr>
                    <tr>
                        <td>Cardiac Surgery</td><td class="text-right"><div id="modal-cardiac_surgery"></div></td>
                    </tr>
                    <tr>
                        <td>cardiovascular disease cardiology</td><td class="text-right"><div id="modal-cardiovascular_disease_cardiology"></div></td>
                    </tr>
                    <tr>
                        <td>certified nurse midwife</td><td class="text-right"><div id="modal-certified_nurse_midwife"></div></td>
                    </tr>
                    <tr>
                        <td>certified registered nurse anesthetist</td><td class="text-right"><div id="modal-certified_registered_nurse_anesthetist"></div></td>
                    </tr>
                    <tr>
                        <td>chiropractic</td><td class="text-right"><div id="modal-chiropractic"></div></td>
                    </tr>
                    <tr>
                        <td>clinical nurse specialist</td><td class="text-right"><div id="modal-clinical_nurse_specialist"></div></td>
                    </tr>
                    <tr>
                        <td>clinical social worker</td><td class="text-right"><div id="modal-clinical_social_worker"></div></td>
                    </tr>
                    <tr>
                        <td>colorectal surgery proctology</td><td class="text-right"><div id="modal-colorectal_surgery_proctology"></div></td>
                    </tr>
                    <tr>
                        <td>critical care intensivists</td><td class="text-right"><div id="modal-critical_care_intensivists"></div></td>
                    </tr>
                    <tr>
                        <td>dermatology</td><td class="text-right"><div id="modal-dermatology"></div></td>
                    </tr>
                    <tr>
                        <td>diagnostic radiology</td><td class="text-right"><div id="modal-diagnostic_radiology"></div></td>
                    </tr>
                    <tr>
                        <td>emergency medicine</td><td class="text-right"><div id="modal-emergency_medicine"></div></td>
                    </tr>
                    <tr>
                        <td>endocrinology</td><td class="text-right"><div id="modal-endocrinology"></div></td>
                    </tr>
                    <tr>
                        <td>family practice</td><td class="text-right"><div id="modal-family_practice"></div></td>
                    </tr>
                    <tr>
                        <td>gastroenterology</td><td class="text-right"><div id="modal-gastroenterology"></div></td>
                    </tr>
                    <tr>
                        <td>general practice</td><td class="text-right"><div id="modal-general_practice"></div></td>
                    </tr>
                    <tr>
                        <td>general surgery</td><td class="text-right"><div id="modal-general_surgery"></div></td>
                    </tr>
                    <tr>
                        <td>geriatric medicine</td><td class="text-right"><div id="modal-geriatric_medicine"></div></td>
                    </tr>
                    <tr>
                        <td>geriatric psychiatry</td><td class="text-right"><div id="modal-geriatric_psychiatry"></div></td>
                    </tr>
                    <tr>
                        <td>gynecological oncology</td><td class="text-right"><div id="modal-gynecological_oncology"></div></td>
                    </tr>
                    <tr>
                        <td>hand surgery</td><td class="text-right"><div id="modal-hand_surgery"></div></td>
                    </tr>
                    <tr>
                        <td>hematology</td><td class="text-right"><div id="modal-hematology"></div></td>
                    </tr>
                    <tr>
                        <td>hematology oncology</td><td class="text-right"><div id="modal-hematology_oncology"></div></td>
                    </tr>
                    <tr>
                        <td>hospice palliative care</td><td class="text-right"><div id="modal-hospice_palliative_care"></div></td>
                    </tr>
                    <tr>
                        <td>infectious disease</td><td class="text-right"><div id="modal-infectious_disease"></div></td>
                    </tr>
                    <tr>
                        <td>internal medicine</td><td class="text-right"><div id="modal-internal_medicine"></div></td>
                    </tr>
                    <tr>
                        <td>interventional cardiology</td><td class="text-right"><div id="modal-interventional_cardiology"></div></td>
                    </tr>
                    <tr>
                        <td>interventional pain management</td><td class="text-right"><div id="modal-interventional_pain_management"></div></td>
                    </tr>
                    <tr>
                        <td>interventional radiology</td><td class="text-right"><div id="modal-interventional_radiology"></div></td>
                    </tr>
                    <tr>
                        <td>maxillofacial surgery</td><td class="text-right"><div id="modal-maxillofacial_surgery"></div></td>
                    </tr>
                    <tr>
                        <td>medical oncology</td><td class="text-right"><div id="modal-medical_oncology"></div></td>
                    </tr>
                    <tr>
                        <td>nephrology</td><td class="text-right"><div id="modal-nephrology"></div></td>
                    </tr>
                    <tr>
                        <td>neurology</td><td class="text-right"><div id="modal-neurology"></div></td>
                    </tr>
                    <tr>
                        <td>neuropsychiatry</td><td class="text-right"><div id="modal-neuropsychiatry"></div></td>
                    </tr>
                    <tr>
                        <td>neurosurgery</td><td class="text-right"><div id="modal-neurosurgery"></div></td>
                    </tr>
                    <tr>
                        <td>nuclear medicine</td><td class="text-right"><div id="modal-nuclear_medicine"></div></td>
                    </tr>
                    <tr>
                        <td>nurse practitioner</td><td class="text-right"><div id="modal-nurse_practitioner"></div></td>
                    </tr>
                    <tr>
                        <td>obstetrics gynecology</td><td class="text-right"><div id="modal-obstetrics_gynecology"></div></td>
                    </tr>
                    <tr>
                        <td>occupational therapy</td><td class="text-right"><div id="modal-occupational_therapy"></div></td>
                    </tr>
                    <tr>
                        <td>ophthalmology</td><td class="text-right"><div id="modal-ophthalmology"></div></td>
                    </tr>
                    <tr>
                        <td>optometry</td><td class="text-right"><div id="modal-optometry"></div></td>
                    </tr>
                    <tr>
                        <td>oral surgery dentist only</td><td class="text-right"><div id="modal-oral_surgery_dentist_only"></div></td>
                    </tr>
                    <tr>
                        <td>orthopedic surgery</td><td class="text-right"><div id="modal-orthopedic_surgery"></div></td>
                    </tr>
                    <tr>
                        <td>osteopathic manipulative medicine</td><td class="text-right"><div id="modal-osteopathic_manipulative_medicine"></div></td>
                    </tr>
                    <tr>
                        <td>otolaryngology</td><td class="text-right"><div id="modal-otolaryngology"></div></td>
                    </tr>
                    <tr>
                        <td>pain management</td><td class="text-right"><div id="modal-pain_management"></div></td>
                    </tr>
                    <tr>
                        <td>pathology</td><td class="text-right"><div id="modal-pathology"></div></td>
                    </tr>
                    <tr>
                        <td>pediatric medicine</td><td class="text-right"><div id="modal-pediatric_medicine"></div></td>
                    </tr>
                    <tr>
                        <td>peripheral vascular disease</td><td class="text-right"><div id="modal-peripheral_vascular_disease"></div></td>
                    </tr>
                    <tr>
                        <td>physical medicine and rehabilitation</td><td class="text-right"><div id="modal-physical_medicine_and_rehabilitation"></div></td>
                    </tr>
                    <tr>
                        <td>physical therapy</td><td class="text-right"><div id="modal-physical_therapy"></div></td>
                    </tr>
                    <tr>
                        <td>physician assistant</td><td class="text-right"><div id="modal-physician_assistant"></div></td>
                    </tr>
                    <tr>
                        <td>plastic and reconstructive surgery</td><td class="text-right"><div id="modal-plastic_and_reconstructive_surgery"></div></td>
                    </tr>
                    <tr>
                        <td>podiatry</td><td class="text-right"><div id="modal-podiatry"></div></td>
                    </tr>
                    <tr>
                        <td>preventative medicine</td><td class="text-right"><div id="modal-preventative_medicine"></div></td>
                    </tr>
                    <tr>
                        <td>psychiatry</td><td class="text-right"><div id="modal-psychiatry"></div></td>
                    </tr>
                    <tr>
                        <td>pulmonary disease</td><td class="text-right"><div id="modal-pulmonary_disease"></div></td>
                    </tr>
                    <tr>
                        <td>radiation oncology</td><td class="text-right"><div id="modal-radiation_oncology"></div></td>
                    </tr>
                    <tr>
                        <td>registered dietitian or nutrition professional</td><td class="text-right"><div id="modal-registered_dietitian_or_nutrition_professional"></div></td>
                    </tr>
                    <tr>
                        <td>rheumatology</td><td class="text-right"><div id="modal-rheumatology"></div></td>
                    </tr>
                    <tr>
                        <td>sleep laboratory medicine</td><td class="text-right"><div id="modal-sleep_laboratory_medicine"></div></td>
                    </tr>
                    <tr>
                        <td>speech language pathologist</td><td class="text-right"><div id="modal-speech_language_pathologist"></div></td>
                    </tr>
                    <tr>
                        <td>sports medicine</td><td class="text-right"><div id="modal-sports_medicine"></div></td>
                    </tr>
                    <tr>
                        <td>surgical oncology</td><td class="text-right"><div id="modal-surgical_oncology"></div></td>
                    </tr>
                    <tr>
                        <td>thoracic surgery</td><td class="text-right"><div id="modal-thoracic_surgery"></div></td>
                    </tr>
                    <tr>
                        <td>undefined physician type</td><td class="text-right"><div id="modal-undefined_physician_type"></div></td>
                    </tr>
                    <tr>
                        <td>urology</td><td class="text-right"><div id="modal-urology"></div></td>
                    </tr>
                    <tr>
                        <td>vascular surgery</td><td class="text-right"><div id="modal-vascular_surgery"></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
    $('#countyDetailModal').on('show.bs.modal', function(e) {
        var county = $(e.relatedTarget).data('county');
        var modal = $(this)
        modal.find('.modal-title').text(_.startCase(_.toLower(county.county_name)));
        modal.find('#modal-population').text(numberWithCommas(county.pop_2016,0));
        modal.find('#modal-pop_2010').text(numberWithCommas(county.pop_2010,0));
        modal.find('#modal-pop_2011').text(numberWithCommas(county.pop_2011,0));
        modal.find('#modal-pop_2012').text(numberWithCommas(county.pop_2012,0));
        modal.find('#modal-pop_2013').text(numberWithCommas(county.pop_2013,0));
        modal.find('#modal-pop_2014').text(numberWithCommas(county.pop_2014,0));
        modal.find('#modal-pop_2015').text(numberWithCommas(county.pop_2015,0));
        modal.find('#modal-pop_2016').text(numberWithCommas(county.pop_2016,0));
        modal.find('#modal-addiction_medicine').text(numberWithCommas(county.addiction_medicine,0));
        modal.find('#modal-allergy_immunology').text(numberWithCommas(county.allergy_immunology,0));
        modal.find('#modal-anesthesiology').text(numberWithCommas(county.anesthesiology,0));
        modal.find('#modal-anesthesiology_assistant').text(numberWithCommas(county.anesthesiology_assistant,0));
        modal.find('#modal-audiologist').text(numberWithCommas(county.audiologist,0));
        modal.find('#modal-cardiac_electrophysiology').text(numberWithCommas(county.cardiac_electrophysiology,0));
        modal.find('#modal-cardiac_surgery').text(numberWithCommas(county.cardiac_surgery,0));
        modal.find('#modal-cardiovascular_disease_cardiology').text(numberWithCommas(county.cardiovascular_disease_cardiology,0));
        modal.find('#modal-certified_nurse_midwife').text(numberWithCommas(county.certified_nurse_midwife,0));
        modal.find('#modal-certified_registered_nurse_anesthetist').text(numberWithCommas(county.certified_registered_nurse_anesthetist,0));
        modal.find('#modal-chiropractic').text(numberWithCommas(county.chiropractic,0));
        modal.find('#modal-clinical_nurse_specialist').text(numberWithCommas(county.clinical_nurse_specialist,0));
        modal.find('#modal-clinical_social_worker').text(numberWithCommas(county.clinical_social_worker,0));
        modal.find('#modal-colorectal_surgery_proctology').text(numberWithCommas(county.colorectal_surgery_proctology,0));
        modal.find('#modal-critical_care_intensivists').text(numberWithCommas(county.critical_care_intensivists,0));
        modal.find('#modal-dermatology').text(numberWithCommas(county.dermatology,0));
        modal.find('#modal-diagnostic_radiology').text(numberWithCommas(county.diagnostic_radiology,0));
        modal.find('#modal-emergency_medicine').text(numberWithCommas(county.emergency_medicine,0));
        modal.find('#modal-endocrinology').text(numberWithCommas(county.endocrinology,0));
        modal.find('#modal-family_practice').text(numberWithCommas(county.family_practice,0));
        modal.find('#modal-gastroenterology').text(numberWithCommas(county.gastroenterology,0));
        modal.find('#modal-general_practice').text(numberWithCommas(county.general_practice,0));
        modal.find('#modal-general_surgery').text(numberWithCommas(county.general_surgery,0));
        modal.find('#modal-geriatric_medicine').text(numberWithCommas(county.geriatric_medicine,0));
        modal.find('#modal-geriatric_psychiatry').text(numberWithCommas(county.geriatric_psychiatry,0));
        modal.find('#modal-gynecological_oncology').text(numberWithCommas(county.gynecological_oncology,0));
        modal.find('#modal-hand_surgery').text(numberWithCommas(county.hand_surgery,0));
        modal.find('#modal-hematology').text(numberWithCommas(county.hematology,0));
        modal.find('#modal-hematology_oncology').text(numberWithCommas(county.hematology_oncology,0));
        modal.find('#modal-hospice_palliative_care').text(numberWithCommas(county.hospice_palliative_care,0));
        modal.find('#modal-infectious_disease').text(numberWithCommas(county.infectious_disease,0));
        modal.find('#modal-internal_medicine').text(numberWithCommas(county.internal_medicine,0));
        modal.find('#modal-interventional_cardiology').text(numberWithCommas(county.interventional_cardiology,0));
        modal.find('#modal-interventional_pain_management').text(numberWithCommas(county.interventional_pain_management,0));
        modal.find('#modal-interventional_radiology').text(numberWithCommas(county.interventional_radiology,0));
        modal.find('#modal-maxillofacial_surgery').text(numberWithCommas(county.maxillofacial_surgery,0));
        modal.find('#modal-medical_oncology').text(numberWithCommas(county.medical_oncology,0));
        modal.find('#modal-nephrology').text(numberWithCommas(county.nephrology,0));
        modal.find('#modal-neurology').text(numberWithCommas(county.neurology,0));
        modal.find('#modal-neuropsychiatry').text(numberWithCommas(county.neuropsychiatry,0));
        modal.find('#modal-neurosurgery').text(numberWithCommas(county.neurosurgery,0));
        modal.find('#modal-nuclear_medicine').text(numberWithCommas(county.nuclear_medicine,0));
        modal.find('#modal-nurse_practitioner').text(numberWithCommas(county.nurse_practitioner,0));
        modal.find('#modal-obstetrics_gynecology').text(numberWithCommas(county.obstetrics_gynecology,0));
        modal.find('#modal-occupational_therapy').text(numberWithCommas(county.occupational_therapy,0));
        modal.find('#modal-ophthalmology').text(numberWithCommas(county.ophthalmology,0));
        modal.find('#modal-optometry').text(numberWithCommas(county.optometry,0));
        modal.find('#modal-oral_surgery_dentist_only').text(numberWithCommas(county.oral_surgery_dentist_only,0));
        modal.find('#modal-orthopedic_surgery').text(numberWithCommas(county.orthopedic_surgery,0));
        modal.find('#modal-osteopathic_manipulative_medicine').text(numberWithCommas(county.osteopathic_manipulative_medicine,0));
        modal.find('#modal-otolaryngology').text(numberWithCommas(county.otolaryngology,0));
        modal.find('#modal-pain_management').text(numberWithCommas(county.pain_management,0));
        modal.find('#modal-pathology').text(numberWithCommas(county.pathology,0));
        modal.find('#modal-pediatric_medicine').text(numberWithCommas(county.pediatric_medicine,0));
        modal.find('#modal-peripheral_vascular_disease').text(numberWithCommas(county.peripheral_vascular_disease,0));
        modal.find('#modal-physical_medicine_and_rehabilitation').text(numberWithCommas(county.physical_medicine_and_rehabilitation,0));
        modal.find('#modal-physical_therapy').text(numberWithCommas(county.physical_therapy,0));
        modal.find('#modal-physician_assistant').text(numberWithCommas(county.physician_assistant,0));
        modal.find('#modal-plastic_and_reconstructive_surgery').text(numberWithCommas(county.plastic_and_reconstructive_surgery,0));
        modal.find('#modal-podiatry').text(numberWithCommas(county.podiatry,0));
        modal.find('#modal-preventative_medicine').text(numberWithCommas(county.preventative_medicine,0));
        modal.find('#modal-psychiatry').text(numberWithCommas(county.psychiatry,0));
        modal.find('#modal-pulmonary_disease').text(numberWithCommas(county.pulmonary_disease,0));
        modal.find('#modal-radiation_oncology').text(numberWithCommas(county.radiation_oncology,0));
        modal.find('#modal-registered_dietitian_or_nutrition_professional').text(numberWithCommas(county.registered_dietitian_or_nutrition_professional,0));
        modal.find('#modal-rheumatology').text(numberWithCommas(county.rheumatology,0));
        modal.find('#modal-sleep_laboratory_medicine').text(numberWithCommas(county.sleep_laboratory_medicine,0));
        modal.find('#modal-speech_language_pathologist').text(numberWithCommas(county.speech_language_pathologist,0));
        modal.find('#modal-sports_medicine').text(numberWithCommas(county.sports_medicine,0));
        modal.find('#modal-surgical_oncology').text(numberWithCommas(county.surgical_oncology,0));
        modal.find('#modal-thoracic_surgery').text(numberWithCommas(county.thoracic_surgery,0));
        modal.find('#modal-undefined_physician_type').text(numberWithCommas(county.undefined_physician_type,0));
        modal.find('#modal-urology').text(numberWithCommas(county.urology,0));
        modal.find('#modal-vascular_surgery').text(numberWithCommas(county.vascular_surgery,0));
        modal.find('#modal-total_doctors').text(numberWithCommas(county.total_doctors,0));
        modal.find('#modal-hospice_beneficiaries').text(numberWithCommas(county.hospice_beneficiaries,0));
        modal.find('#modal-total_days').text(numberWithCommas(county.total_days,0));
        modal.find('#modal-total_medicare_standard_payment_amount').text(numberWithCommas(county.total_medicare_standard_payment_amount,0));
        modal.find('#modal-total_charge_amount').text(numberWithCommas(county.total_charge_amount,0));
        modal.find('#modal-home_health_visit_hours_per_day').text(numberWithCommas(county.home_health_visit_hours_per_day,0));
        modal.find('#modal-skilled_nursing_visit_hours_per_day').text(numberWithCommas(county.skilled_nursing_visit_hours_per_day,0));
        modal.find('#modal-social_service_visit_hours_per_day').text(numberWithCommas(county.social_service_visit_hours_per_day,0));
        modal.find('#modal-home_health_visit_hours_per_day_during_week_prior_to_death').text(numberWithCommas(county.home_health_visit_hours_per_day_during_week_prior_to_death,0));
        modal.find('#modal-skilled_nursing_visit_hours_per_day_during_week_prior_to_death').text(numberWithCommas(county.skilled_nursing_visit_hours_per_day_during_week_prior_to_death,0));
        modal.find('#modal-social_service_visit_hours_per_day_during_week_prior_to_death').text(numberWithCommas(county.social_service_visit_hours_per_day_during_week_prior_to_death,0));
        modal.find('#modal-num_hospices').text(numberWithCommas(county.num_hospices,0));
        modal.find('#modal-percent_routine_home_care_days').text(numberWithCommas(county.percent_routine_home_care_days,0));
        modal.find('#modal-medicare_payment_per_2016_capita').text(numberWithCommas(county.medicare_payment_per_2016_capita,0));
        modal.find('#modal-charge_amount_per_2016_capita').text(numberWithCommas(county.charge_amount_per_2016_capita,0));
        modal.find('#modal-numhospices_per_2016_capita').text(numberWithCommas(county.numhospices_per_2016_capita,0));
        modal.find('#modal-hospice_beneficiaries_per_2016_capita').text(numberWithCommas(county.hospice_beneficiaries_per_2016_capita,0));
        modal.find('#modal-geriatric_doctors_per_2016_capita').text(numberWithCommas(county.geriatric_doctors_per_2016_capita,0));

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

    });

</script>
    </body>
</html>
