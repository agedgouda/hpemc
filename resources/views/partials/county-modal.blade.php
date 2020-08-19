<script type="text/template" id="county-detail-template">
    <div class="modal-content">
      <div class="modal-body">
        <h2 style = "text-transform:capitalize;"><%- county.county_name.toLowerCase() %> County</h2>
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
                        <td>2016</td><td class="text-right"><%- numberWithCommas(county.pop_2016,0) %></td>
                    </tr>
                    <tr>
                        <td>2015</td><td class="text-right"><%- numberWithCommas(county.pop_2015,0) %></td>
                    </tr>
                    <tr>
                        <td>2014</td><td class="text-right"><%- numberWithCommas(county.pop_2014,0) %></td>
                    </tr>
                    <tr>
                        <td>2013</td><td class="text-right"><%- numberWithCommas(county.pop_2013,0) %></td>
                    </tr>
                    <tr>
                        <td>2012</td><td class="text-right"><%- numberWithCommas(county.pop_2012,0) %></td>
                    </tr>
                    <tr>
                        <td>2011</td><td class="text-right"><%- numberWithCommas(county.pop_2011,0) %></td>
                    </tr>
                    <tr>
                        <td>2010</td><td class="text-right"><%- numberWithCommas(county.pop_2010,0) %></td>
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
                            <td>Number of Hospices</td><td class="text-right"><%- numberWithCommas(county.num_hospices,0) %></td>
                        </tr>
                        <tr>
                            <td>hospice beneficiaries</td><td class="text-right"><%- numberWithCommas(county.hospice_beneficiaries,0) %></td>
                        </tr>
                        <tr>
                            <td>total days</td><td class="text-right"><%- numberWithCommas(county.total_days,0) %></td>
                        </tr>
                        <tr>
                            <td>total medicare payment amount</td><td class="text-right"><%- numberWithCommas(county.total_medicare_standard_payment_amount) %></td>
                        </tr>
                        <tr>
                            <td>per capita medicare payment</td><td class="text-right"><%- numberWithCommas(county.medicare_payment_per_2016_capita,0) %></td>
                        </tr>
                        <tr>
                            <td>total medicare charge amount</td><td class="text-right"><%- numberWithCommas(county.total_charge_amount,0) %></td>
                        </tr>
                        </tr>
                            <td>per capita medicare charge amount</td><td class="text-right"><%- numberWithCommas(county.charge_amount_per_2016_capita,0) %></td>
                        </tr>
                        <tr>
                            <td>home health visit hours per day</td><td class="text-right"><%- numberWithCommas(county.home_health_visit_hours_per_day,0) %></td>
                        </tr>
                        <tr>
                            <td>skilled nursing visit hours per day</td><td class="text-right"><%- numberWithCommas(county.skilled_nursing_visit_hours_per_day,0) %></td>
                        </tr>
                        <tr>
                            <td>social service visit hours per day</td><td class="text-right"><%- numberWithCommas(county.social_service_visit_hours_per_day,0) %></td>
                        </tr>
                        <tr>
                            <td>home health visit hours per day during week prior to death</td><td class="text-right"><%- numberWithCommas(county.home_health_visit_hours_per_day_during_week_prior_to_death,0) %></td>
                        </tr>
                        <tr>
                            <td>skilled nursing visit hours per day during week prior to death</td><td class="text-right"><%- numberWithCommas(county.skilled_nursing_visit_hours_per_day_during_week_prior_to_death,0) %></td>
                        </tr>
                        <tr>
                            <td>social service visit hours per day during week prior to death</td><td class="text-right"><%- numberWithCommas(county.social_service_visit_hours_per_day_during_week_prior_to_death,0) %></td>
                        </tr>
                        <tr>
                            <td>percent routine home care days</td><td class="text-right"><%- numberWithCommas(county.percent_routine_home_care_days,0) %></td>
                        <tr>
                            <td>numhospices per 2016 capita</td><td class="text-right"><%- numberWithCommas(county.numhospices_per_2016_capita,0) %></td>
                        </tr>
                        <tr>
                            <td>hospice beneficiaries per 2016 capita</td><td class="text-right"><%- numberWithCommas(county.hospice_beneficiaries_per_2016_capita,0) %></td>
                        </tr>
                        <tr>
                            <td>geriatric doctors per 2016 capita</td><td class="text-right"><%- numberWithCommas(county.geriatric_doctors_per_2016_capita,0) %></td>
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
                        <td>Total</td><td class="text-right"><%- numberWithCommas(county.total_doctors,0) %></td>
                    </tr>
                    <tr>
                        <td>Addiction Medicine</td><td class="text-right"><%- numberWithCommas(county.addiction_medicine,0) %></td>
                    </tr>
                    <tr>
                        <td>Allergy Immunology</td><td class="text-right"><%- numberWithCommas(county.allergy_immunology,0) %></td>
                    </tr>
                    <tr>
                        <td>Anesthesiology</td><td class="text-right"><%- numberWithCommas(county.anesthesiology,0) %></td>
                    </tr>
                    <tr>
                        <td>Anesthesiology Assistant</td><td class="text-right"><%- numberWithCommas(county.anesthesiology_assistant,0) %></td>
                    </tr>
                    <tr>
                        <td>Audiologist</td><td class="text-right"><%- numberWithCommas(county.audiologist,0) %></td>
                    </tr>
                    <tr>
                        <td>Cardiac Electrophysiology</td><td class="text-right"><%- numberWithCommas(county.cardiac_electrophysiology,0) %></td>
                    </tr>
                    <tr>
                        <td>Cardiac Surgery</td><td class="text-right"><%- numberWithCommas(county.cardiac_surgery,0) %></td>
                    </tr>
                    <tr>
                        <td>cardiovascular disease cardiology</td><td class="text-right"><%- numberWithCommas(county.cardiovascular_disease_cardiology,0) %></td>
                    </tr>
                    <tr>
                        <td>certified nurse midwife</td><td class="text-right"><%- numberWithCommas(county.certified_nurse_midwife,0) %></td>
                    </tr>
                    <tr>
                        <td>certified registered nurse anesthetist</td><td class="text-right"><%- numberWithCommas(county.certified_registered_nurse_anesthetist,0) %></td>
                    </tr>
                    <tr>
                        <td>chiropractic</td><td class="text-right"><%- numberWithCommas(county.chiropractic,0) %></td>
                    </tr>
                    <tr>
                        <td>clinical nurse specialist</td><td class="text-right"><%- numberWithCommas(county.clinical_nurse_specialist,0) %></td>
                    </tr>
                    <tr>
                        <td>clinical social worker</td><td class="text-right"><%- numberWithCommas(county.clinical_social_worker,0) %></td>
                    </tr>
                    <tr>
                        <td>colorectal surgery proctology</td><td class="text-right"><%- numberWithCommas(county.colorectal_surgery_proctology,0) %></td>
                    </tr>
                    <tr>
                        <td>critical care intensivists</td><td class="text-right"><%- numberWithCommas(county.critical_care_intensivists,0) %></td>
                    </tr>
                    <tr>
                        <td>dermatology</td><td class="text-right"><%- numberWithCommas(county.dermatology,0) %></td>
                    </tr>
                    <tr>
                        <td>diagnostic radiology</td><td class="text-right"><%- numberWithCommas(county.diagnostic_radiology,0) %></td>
                    </tr>
                    <tr>
                        <td>emergency medicine</td><td class="text-right"><%- numberWithCommas(county.emergency_medicine,0) %></td>
                    </tr>
                    <tr>
                        <td>endocrinology</td><td class="text-right"><%- numberWithCommas(county.endocrinology,0) %></td>
                    </tr>
                    <tr>
                        <td>family practice</td><td class="text-right"><%- numberWithCommas(county.family_practice,0) %></td>
                    </tr>
                    <tr>
                        <td>gastroenterology</td><td class="text-right"><%- numberWithCommas(county.gastroenterology,0) %></td>
                    </tr>
                    <tr>
                        <td>general practice</td><td class="text-right"><%- numberWithCommas(county.general_practice,0) %></td>
                    </tr>
                    <tr>
                        <td>general surgery</td><td class="text-right"><%- numberWithCommas(county.general_surgery,0) %></td>
                    </tr>
                    <tr>
                        <td>geriatric medicine</td><td class="text-right"><%- numberWithCommas(county.geriatric_medicine,0) %></td>
                    </tr>
                    <tr>
                        <td>geriatric psychiatry</td><td class="text-right"><%- numberWithCommas(county.geriatric_psychiatry,0) %></td>
                    </tr>
                    <tr>
                        <td>gynecological oncology</td><td class="text-right"><%- numberWithCommas(county.gynecological_oncology,0) %></td>
                    </tr>
                    <tr>
                        <td>hand surgery</td><td class="text-right"><%- numberWithCommas(county.hand_surgery,0) %></td>
                    </tr>
                    <tr>
                        <td>hematology</td><td class="text-right"><%- numberWithCommas(county.hematology,0) %></td>
                    </tr>
                    <tr>
                        <td>hematology oncology</td><td class="text-right"><%- numberWithCommas(county.hematology_oncology,0) %></td>
                    </tr>
                    <tr>
                        <td>hospice palliative care</td><td class="text-right"><%- numberWithCommas(county.hospice_palliative_care,0) %></td>
                    </tr>
                    <tr>
                        <td>infectious disease</td><td class="text-right"><%- numberWithCommas(county.infectious_disease,0) %></td>
                    </tr>
                    <tr>
                        <td>internal medicine</td><td class="text-right"><%- numberWithCommas(county.internal_medicine,0) %></td>
                    </tr>
                    <tr>
                        <td>interventional cardiology</td><td class="text-right"><%- numberWithCommas(county.interventional_cardiology,0) %></td>
                    </tr>
                    <tr>
                        <td>interventional pain management</td><td class="text-right"><%- numberWithCommas(county.interventional_pain_management,0) %></td>
                    </tr>
                    <tr>
                        <td>interventional radiology</td><td class="text-right"><%- numberWithCommas(county.interventional_radiology,0) %></td>
                    </tr>
                    <tr>
                        <td>maxillofacial surgery</td><td class="text-right"><%- numberWithCommas(county.maxillofacial_surgery,0) %></td>
                    </tr>
                    <tr>
                        <td>medical oncology</td><td class="text-right"><%- numberWithCommas(county.medical_oncology,0) %></td>
                    </tr>
                    <tr>
                        <td>nephrology</td><td class="text-right"><%- numberWithCommas(county.nephrology,0) %></td>
                    </tr>
                    <tr>
                        <td>neurology</td><td class="text-right"><%- numberWithCommas(county.neurology,0) %></td>
                    </tr>
                    <tr>
                        <td>neuropsychiatry</td><td class="text-right"><%- numberWithCommas(county.neuropsychiatry,0) %></td>
                    </tr>
                    <tr>
                        <td>neurosurgery</td><td class="text-right"><%- numberWithCommas(county.neurosurgery,0) %></td>
                    </tr>
                    <tr>
                        <td>nuclear medicine</td><td class="text-right"><%- numberWithCommas(county.nuclear_medicine,0) %></td>
                    </tr>
                    <tr>
                        <td>nurse practitioner</td><td class="text-right"><%- numberWithCommas(county.nurse_practitioner,0) %></td>
                    </tr>
                    <tr>
                        <td>obstetrics gynecology</td><td class="text-right"><%- numberWithCommas(county.obstetrics_gynecology,0) %></td>
                    </tr>
                    <tr>
                        <td>occupational therapy</td><td class="text-right"><%- numberWithCommas(county.occupational_therapy,0) %></td>
                    </tr>
                    <tr>
                        <td>ophthalmology</td><td class="text-right"><%- numberWithCommas(county.ophthalmology,0) %></td>
                    </tr>
                    <tr>
                        <td>optometry</td><td class="text-right"><%- numberWithCommas(county.optometry,0) %></td>
                    </tr>
                    <tr>
                        <td>oral surgery dentist only</td><td class="text-right"><%- numberWithCommas(county.oral_surgery_dentist_only,0) %></td>
                    </tr>
                    <tr>
                        <td>orthopedic surgery</td><td class="text-right"><%- numberWithCommas(county.orthopedic_surgery,0) %></td>
                    </tr>
                    <tr>
                        <td>osteopathic manipulative medicine</td><td class="text-right"><%- numberWithCommas(county.osteopathic_manipulative_medicine,0) %></td>
                    </tr>
                    <tr>
                        <td>otolaryngology</td><td class="text-right"><%- numberWithCommas(county.otolaryngology,0) %></td>
                    </tr>
                    <tr>
                        <td>pain management</td><td class="text-right"><%- numberWithCommas(county.pain_management,0) %></td>
                    </tr>
                    <tr>
                        <td>pathology</td><td class="text-right"><%- numberWithCommas(county.pathology,0) %></td>
                    </tr>
                    <tr>
                        <td>pediatric medicine</td><td class="text-right"><%- numberWithCommas(county.pediatric_medicine,0) %></td>
                    </tr>
                    <tr>
                        <td>peripheral vascular disease</td><td class="text-right"><%- numberWithCommas(county.peripheral_vascular_disease,0) %></td>
                    </tr>
                    <tr>
                        <td>physical medicine and rehabilitation</td><td class="text-right"><%- numberWithCommas(county.physical_medicine_and_rehabilitation,0) %></td>
                    </tr>
                    <tr>
                        <td>physical therapy</td><td class="text-right"><%- numberWithCommas(county.physical_therapy,0) %></td>
                    </tr>
                    <tr>
                        <td>physician assistant</td><td class="text-right"><%- numberWithCommas(county.physician_assistant,0) %></td>
                    </tr>
                    <tr>
                        <td>plastic and reconstructive surgery</td><td class="text-right"><%- numberWithCommas(county.plastic_and_reconstructive_surgery,0) %></td>
                    </tr>
                    <tr>
                        <td>podiatry</td><td class="text-right"><%- numberWithCommas(county.podiatry,0) %></td>
                    </tr>
                    <tr>
                        <td>preventative medicine</td><td class="text-right"><%- numberWithCommas(county.preventative_medicine,0) %></td>
                    </tr>
                    <tr>
                        <td>psychiatry</td><td class="text-right"><%- numberWithCommas(county.psychiatry,0) %></td>
                    </tr>
                    <tr>
                        <td>pulmonary disease</td><td class="text-right"><%- numberWithCommas(county.pulmonary_disease,0) %></td>
                    </tr>
                    <tr>
                        <td>radiation oncology</td><td class="text-right"><%- numberWithCommas(county.radiation_oncology,0) %></td>
                    </tr>
                    <tr>
                        <td>registered dietitian or nutrition professional</td><td class="text-right"><%- numberWithCommas(county.registered_dietitian_or_nutrition_professional,0) %></td>
                    </tr>
                    <tr>
                        <td>rheumatology</td><td class="text-right"><%- numberWithCommas(county.rheumatology,0) %></td>
                    </tr>
                    <tr>
                        <td>sleep laboratory medicine</td><td class="text-right"><%- numberWithCommas(county.sleep_laboratory_medicine,0) %></td>
                    </tr>
                    <tr>
                        <td>speech language pathologist</td><td class="text-right"><%- numberWithCommas(county.speech_language_pathologist,0) %></td>
                    </tr>
                    <tr>
                        <td>sports medicine</td><td class="text-right"><%- numberWithCommas(county.sports_medicine,0) %></td>
                    </tr>
                    <tr>
                        <td>surgical oncology</td><td class="text-right"><%- numberWithCommas(county.surgical_oncology,0) %></td>
                    </tr>
                    <tr>
                        <td>thoracic surgery</td><td class="text-right"><%- numberWithCommas(county.thoracic_surgery,0) %></td>
                    </tr>
                    <tr>
                        <td>undefined physician type</td><td class="text-right"><%- numberWithCommas(county.undefined_physician_type,0) %></td>
                    </tr>
                    <tr>
                        <td>urology</td><td class="text-right"><%- numberWithCommas(county.urology,0) %></td>
                    </tr>
                    <tr>
                        <td>vascular surgery</td><td class="text-right"><%- numberWithCommas(county.vascular_surgery,0) %></td>
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

