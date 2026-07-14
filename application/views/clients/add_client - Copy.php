<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php $getUpdateId = $this->uri->segment('3'); // Update Segment ?>

<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1>Client Information</h1>
		</div>
	</div>
	<?php if(empty($getUpdateId)): ?>
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div>
					<h4 class="line-head">Add Client</h4>
					<span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Clients" href="<?php echo base_url('clients');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span>
				</div>
				<div style="clear:both;"></div>
				<form class="form-horizontal" method="post" name="add_client" id="add_client" action="<?php echo base_url('clients/addclient');?>">
					<div class="form-group">
						<label class="control-label col-md-3">Client Name : <span class="required-star">*</span></label>
						<div class="col-md-4">
							<input class="form-control" type="text" name="client_name" id="client_name" placeholder="Enter Client Name" value="<?php echo set_value('client_name'); ?>">
							<?php echo form_error('client_name'); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3">Department: <span class="required-star">*</span></label>
						<div class="col-md-4">
					    	<select class="form-control" id="department" name="department">
								<option value="" selected="selected">Choose Department</option>
								<option value="Architectural">Architectural</option>
								<option value="Structural">Structural</option>
								<option value="MEP">MEP</option>
								<option value="3D Visualization">3D Visualization</option>
								<!-- <option value="Middle East">Middle East</option> -->
							</select>

						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3">Client Email : 
							<!--<span class="required-star">*</span>-->
						</label>
						<div class="col-md-4">
							<input class="form-control" placeholder="Enter email address" type="email" name="client_email" id="client_email" value="<?php echo set_value('client_email'); ?>">
							<?php echo form_error('client_email'); ?>
						</div>
					</div>
                    
                     <div class="form-group">
						<label class="control-label col-md-3">Country : <span class="required-star">*</span></label>
                            <div class="col-md-4">
							<select class="form-control form-select" id="country" name="client_country">
								<option value="" selected disabled>Select a country</option>
								<option value="Afghanistan">Afghanistan</option>
								<option value="Albania">Albania</option>
								<option value="Algeria">Algeria</option>
								<option value="Andorra">Andorra</option>
								<option value="Angola">Angola</option>
								<option value="Argentina">Argentina</option>
								<option value="Armenia">Armenia</option>
								<option value="Australia">Australia</option>
								<option value="Austria">Austria</option>
								<option value="Azerbaijan">Azerbaijan</option>
								<option value="Bahamas">Bahamas</option>
								<option value="Bahrain">Bahrain</option>
								<option value="Bangladesh">Bangladesh</option>
								<option value="Barbados">Barbados</option>
								<option value="Belarus">Belarus</option>
								<option value="Belgium">Belgium</option>
								<option value="Belize">Belize</option>
								<option value="Benin">Benin</option>
								<option value="Bhutan">Bhutan</option>
								<option value="Bolivia">Bolivia</option>
								<option value="Botswana">Botswana</option>
								<option value="Brazil">Brazil</option>
								<option value="Brunei">Brunei</option>
								<option value="Bulgaria">Bulgaria</option>
								<option value="Burkina Faso">Burkina Faso</option>
								<option value="Burundi">Burundi</option>
								<option value="Cabo Verde">Cabo Verde</option>
								<option value="Cambodia">Cambodia</option>
								<option value="Cameroon">Cameroon</option>
								<option value="Canada">Canada</option>
								<option value="Central African Republic">Central African Republic</option>
								<option value="Chad">Chad</option>
								<option value="Chile">Chile</option>
								<option value="China">China</option>
								<option value="Colombia">Colombia</option>
								<option value="Comoros">Comoros</option>
								<option value="Congo (Congo-Brazzaville)">Congo (Congo-Brazzaville)</option>
								<option value="Costa Rica">Costa Rica</option>
								<option value="Croatia">Croatia</option>
								<option value="Cuba">Cuba</option>
								<option value="Cyprus">Cyprus</option>
								<option value="Czechia (Czech Republic)">Czechia (Czech Republic)</option>
								<option value="Denmark">Denmark</option>
								<option value="Djibouti">Djibouti</option>
								<option value="Dominica">Dominica</option>
								<option value="Dominican Republic">Dominican Republic</option>
								<option value="East Timor (Timor-Leste)">East Timor (Timor-Leste)</option>
								<option value="Ecuador">Ecuador</option>
								<option value="Egypt">Egypt</option>
								<option value="El Salvador">El Salvador</option>
								<option value="Equatorial Guinea">Equatorial Guinea</option>
								<option value="Eritrea">Eritrea</option>
								<option value="Estonia">Estonia</option>
								<option value="Ethiopia">Ethiopia</option>
								<option value="Fiji">Fiji</option>
								<option value="Finland">Finland</option>
								<option value="France">France</option>
								<option value="Gabon">Gabon</option>
								<option value="Gambia">Gambia</option>
								<option value="Georgia">Georgia</option>
								<option value="Germany">Germany</option>
								<option value="Ghana">Ghana</option>
								<option value="Greece">Greece</option>
								<option value="Grenada">Grenada</option>
								<option value="Guatemala">Guatemala</option>
								<option value="Guinea">Guinea</option>
								<option value="Guinea-Bissau">Guinea-Bissau</option>
								<option value="Guyana">Guyana</option>
								<option value="Haiti">Haiti</option>
								<option value="Honduras">Honduras</option>
								<option value="Hungary">Hungary</option>
								<option value="Iceland">Iceland</option>
								<option value="India">India</option>
								<option value="Indonesia">Indonesia</option>
								<option value="Iran">Iran</option>
								<option value="Iraq">Iraq</option>
								<option value="Ireland">Ireland</option>
								<option value="Israel">Israel</option>
								<option value="Italy">Italy</option>
								<option value="Jamaica">Jamaica</option>
								<option value="Japan">Japan</option>
								<option value="Jordan">Jordan</option>
								<option value="Kazakhstan">Kazakhstan</option>
								<option value="Kenya">Kenya</option>
								<option value="Kiribati">Kiribati</option>
								<option value="Korea (North)">Korea (North)</option>
								<option value="Korea (South)">Korea (South)</option>
								<option value="Kosovo">Kosovo</option>
								<option value="Kuwait">Kuwait</option>
								<option value="Kyrgyzstan">Kyrgyzstan</option>
								<option value="Laos">Laos</option>
								<option value="Latvia">Latvia</option>
								<option value="Lebanon">Lebanon</option>
								<option value="Lesotho">Lesotho</option>
								<option value="Liberia">Liberia</option>
								<option value="Libya">Libya</option>
								<option value="Liechtenstein">Liechtenstein</option>
								<option value="Lithuania">Lithuania</option>
								<option value="Luxembourg">Luxembourg</option>
								<option value="Madagascar">Madagascar</option>
								<option value="Malawi">Malawi</option>
								<option value="Malaysia">Malaysia</option>
								<option value="Maldives">Maldives</option>
								<option value="Mali">Mali</option>
								<option value="Malta">Malta</option>
								<option value="Marshall Islands">Marshall Islands</option>
								<option value="Mauritania">Mauritania</option>
								<option value="Mauritius">Mauritius</option>
								<option value="Mexico">Mexico</option>
								<option value="Micronesia">Micronesia</option>
								<option value="Moldova">Moldova</option>
								<option value="Monaco">Monaco</option>
								<option value="Mongolia">Mongolia</option>
								<option value="Montenegro">Montenegro</option>
								<option value="Morocco">Morocco</option>
								<option value="Mozambique">Mozambique</option>
								<option value="Myanmar (Burma)">Myanmar (Burma)</option>
								<option value="Namibia">Namibia</option>
								<option value="Nauru">Nauru</option>
								<option value="Nepal">Nepal</option>
								<option value="Netherlands">Netherlands</option>
								<option value="New Zealand">New Zealand</option>
								<option value="Nicaragua">Nicaragua</option>
								<option value="Niger">Niger</option>
								<option value="Nigeria">Nigeria</option>
								<option value="North Macedonia">North Macedonia</option>
								<option value="Norway">Norway</option>
								<option value="Oman">Oman</option>
								<option value="Pakistan">Pakistan</option>
								<option value="Palau">Palau</option>
								<option value="Palestine">Palestine</option>
								<option value="Panama">Panama</option>
								<option value="Papua New Guinea">Papua New Guinea</option>
								<option value="Paraguay">Paraguay</option>
								<option value="Peru">Peru</option>
								<option value="Philippines">Philippines</option>
								<option value="Poland">Poland</option>
								<option value="Portugal">Portugal</option>
								<option value="Qatar">Qatar</option>
								<option value="Romania">Romania</option>
								<option value="Russia">Russia</option>
								<option value="Rwanda">Rwanda</option>
								<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
								<option value="Saint Lucia">Saint Lucia</option>
								<option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
								<option value="Samoa">Samoa</option>
								<option value="San Marino">San Marino</option>
								<option value="Sao Tome and Principe">Sao Tome and Principe</option>
								<option value="Saudi Arabia">Saudi Arabia</option>
								<option value="Senegal">Senegal</option>
								<option value="Serbia">Serbia</option>
								<option value="Seychelles">Seychelles</option>
								<option value="Sierra Leone">Sierra Leone</option>
								<option value="Singapore">Singapore</option>
								<option value="Slovakia">Slovakia</option>
								<option value="Slovenia">Slovenia</option>
								<option value="Solomon Islands">Solomon Islands</option>
								<option value="Somalia">Somalia</option>
								<option value="South Africa">South Africa</option>
								<option value="South Sudan">South Sudan</option>
								<option value="Spain">Spain</option>
								<option value="Sri Lanka">Sri Lanka</option>
								<option value="Sudan">Sudan</option>
								<option value="Suriname">Suriname</option>
								<option value="Sweden">Sweden</option>
								<option value="Switzerland">Switzerland</option>
								<option value="Syria">Syria</option>
								<option value="Taiwan">Taiwan</option>
								<option value="Tajikistan">Tajikistan</option>
								<option value="Tanzania">Tanzania</option>
								<option value="Thailand">Thailand</option>
								<option value="Togo">Togo</option>
								<option value="Tonga">Tonga</option>
								<option value="Trinidad and Tobago">Trinidad and Tobago</option>
								<option value="Tunisia">Tunisia</option>
								<option value="Turkey">Turkey</option>
								<option value="Turkmenistan">Turkmenistan</option>
								<option value="Tuvalu">Tuvalu</option>
								<option value="Uganda">Uganda</option>
								<option value="Ukraine">Ukraine</option>
								<option value="United Arab Emirates">United Arab Emirates</option>
								<option value="United Kingdom">United Kingdom</option>
								<option value="United States of America">United States of America</option>
								<option value="Uruguay">Uruguay</option>
								<option value="Uzbekistan">Uzbekistan</option>
								<option value="Vanuatu">Vanuatu</option>
								<option value="Vatican City (Holy See)">Vatican City (Holy See)</option>
								<option value="Venezuela">Venezuela</option>
								<option value="Vietnam">Vietnam</option>
								<option value="Yemen">Yemen</option>
								<option value="Holy See (Vatican City)">Holy See (Vatican City)</option>
								<option value="Palestine">Palestine</option>

							</select>    
						</div>						
					 </div>
                    
                     <div class="form-group">
						<label class="control-label col-md-3">State : <span class="required-star">*</span></label>
						<div class="col-md-4">							
                             
                            <select class="form-control form-select" id="state" name="client_state">
								<option value="" selected disabled>Select a State</option>
								<option value="Alabama">Alabama</option>
								<option value="Alaska">Alaska</option>
								<option value="Arizona">Arizona</option>
								<option value="Arkansas">Arkansas</option>
								<option value="California">California</option>
								<option value="Colorado">Colorado</option>
								<option value="Connecticut">Connecticut</option>
								<option value="Delaware">Delaware</option>
								<option value="District Of Columbia">District Of Columbia</option>
								<option value="Florida">Florida</option>
								<option value="Georgia">Georgia</option>
								<option value="Hawaii">Hawaii</option>
								<option value="Idaho">Idaho</option>
								<option value="Illinois">Illinois</option>
								<option value="Indiana">Indiana</option>
								<option value="Iowa">Iowa</option>
								<option value="Kansas">Kansas</option>
								<option value="Kentucky">Kentucky</option>
								<option value="Louisiana">Louisiana</option>
								<option value="Maine">Maine</option>
								<option value="Maryland">Maryland</option>
								<option value="Massachusetts">Massachusetts</option>
								<option value="Michigan">Michigan</option>
								<option value="Minnesota">Minnesota</option>
								<option value="Mississippi">Mississippi</option>
								<option value="Missouri">Missouri</option>
								<option value="Montana">Montana</option>
								<option value="Nebraska">Nebraska</option>
								<option value="Nevada">Nevada</option>
								<option value="New Hampshire">New Hampshire</option>
								<option value="New Jersey">New Jersey</option>
								<option value="New Mexico">New Mexico</option>
								<option value="New York">New York</option>
								<option value="North Carolina">North Carolina</option>
								<option value="North Dakota">North Dakota</option>
								<option value="Ohio">Ohio</option>
								<option value="Oklahoma">Oklahoma</option>
								<option value="Oregon">Oregon</option>
								<option value="Pennsylvania">Pennsylvania</option>
								<option value="Rhode Island">Rhode Island</option>
								<option value="South Carolina">South Carolina</option>
								<option value="South Dakota">South Dakota</option>
								<option value="Tennessee">Tennessee</option>
								<option value="Texas">Texas</option>
								<option value="Utah">Utah</option>
								<option value="Vermont">Vermont</option>
								<option value="Virginia">Virginia</option>
								<option value="Washington">Washington</option>
								<option value="West Virginia">West Virginia</option>
								<option value="Wisconsin">Wisconsin</option>
								<option value="Wyoming">Wyoming</option>
								<option value="Badakhshan">Badakhshan</option>
								<option value="Badghis">Badghis</option>
								<option value="Baghlan">Baghlan</option>
								<option value="Balkh">Balkh</option>
								<option value="Bamyan">Bamyan</option>
								<option value="Daykundi">Daykundi</option>
								<option value="Farah">Farah</option>
								<option value="Faryab">Faryab</option>
								<option value="Ghaziabad">Ghaziabad</option>
								<option value="Ghazni">Ghazni</option>
								<option value="Helmand">Helmand</option>
								<option value="Herat">Herat</option>
								<option value="Jowzjan">Jowzjan</option>
								<option value="Kabul">Kabul</option>
								<option value="Kandahar">Kandahar</option>
								<option value="Kapisa">Kapisa</option>
								<option value="Khost">Khost</option>
								<option value="Kunar">Kunar</option>
								<option value="Kunduz">Kunduz</option>
								<option value="Laghman">Laghman</option>
								<option value="Logar">Logar</option>
								<option value="Nangarhar">Nangarhar</option>
								<option value="Nimroz">Nimroz</option>
								<option value="Nuristan">Nuristan</option>
								<option value="Paktia">Paktia</option>
								<option value="Paktika">Paktika</option>
								<option value="Panjshir">Panjshir</option>
								<option value="Parwan">Parwan</option>
								<option value="Samangan">Samangan</option>
								<option value="Sar-e Pol">Sar-e Pol</option>
								<option value="Takhar">Takhar</option>
								<option value="Urozgan">Urozgan</option>
								<option value="Wardak">Wardak</option>
								<option value="Zabul">Zabul</option>
								<option value="New South Wales">New South Wales</option>
								<option value="Victoria">Victoria</option>
								<option value="Queensland">Queensland</option>
								<option value="South Australia">South Australia</option>
								<option value="Western Australia">Western Australia</option>
								<option value="Tasmania">Tasmania</option>
								<option value="Australian Capital Territory">Australian Capital Territory</option>
								<option value="Northern Territory">Northern Territory</option>
								<option value="Capital Governorate">Capital Governorate</option>
								<option value="Muharraq Governorate">Muharraq Governorate</option>
								<option value="Northern Governorate">Northern Governorate</option>
								<option value="Southern Governorate">Southern Governorate</option>
								<option value="Central Governorate">Central Governorate</option>
								<option value="Alberta">Alberta</option>
								<option value="British Columbia">British Columbia</option>
								<option value="Manitoba">Manitoba</option>
								<option value="New Brunswick">New Brunswick</option>
								<option value="Newfoundland and Labrador">Newfoundland and Labrador</option>
								<option value="Northwest Territories">Northwest Territories</option>
								<option value="Nova Scotia">Nova Scotia</option>
								<option value="Nunavut">Nunavut</option>
								<option value="Ontario">Ontario</option>
								<option value="Prince Edward Island">Prince Edward Island</option>
								<option value="Quebec">Quebec</option>
								<option value="Saskatchewan">Saskatchewan</option>
								<option value="Yukon">Yukon</option>
								<option value="Anhui">Anhui</option>
								<option value="Beijing">Beijing</option>
								<option value="Chongqing">Chongqing</option>
								<option value="Fujian">Fujian</option>
								<option value="Gansu">Gansu</option>
								<option value="Guangdong">Guangdong</option>
								<option value="Guangxi">Guangxi</option>
								<option value="Guizhou">Guizhou</option>
								<option value="Hainan">Hainan</option>
								<option value="Hebei">Hebei</option>
								<option value="Heilongjiang">Heilongjiang</option>
								<option value="Henan">Henan</option>
								<option value="Hubei">Hubei</option>
								<option value="Hunan">Hunan</option>
								<option value="Jiangsu">Jiangsu</option>
								<option value="Jiangxi">Jiangxi</option>
								<option value="Jilin">Jilin</option>
								<option value="Liaoning">Liaoning</option>
								<option value="Macau">Macau</option>
								<option value="Ningxia">Ningxia</option>
								<option value="Qinghai">Qinghai</option>
								<option value="Shaanxi">Shaanxi</option>
								<option value="Shandong">Shandong</option>
								<option value="Shanghai">Shanghai</option>
								<option value="Shanxi">Shanxi</option>
								<option value="Sichuan">Sichuan</option>
								<option value="Taiwan">Taiwan</option>
								<option value="Tianjin">Tianjin</option>
								<option value="Tibet">Tibet</option>
								<option value="Xinjiang">Xinjiang</option>
								<option value="Yunnan">Yunnan</option>
								<option value="Zhejiang">Zhejiang</option>
								<option value="Choluteca">Choluteca</option>
								<option value="Comayagua">Comayagua</option>
								<option value="Distrito Central">Distrito Central</option>
								<option value="Gracias a Dios">Gracias a Dios</option>
								<option value="La Paz">La Paz</option>
								<option value="Lempira">Lempira</option>
								<option value="Ocotepeque">Ocotepeque</option>
								<option value="Olancho">Olancho</option>
								<option value="Yoro">Yoro</option>
								<option value="La Libertad">La Libertad</option>
								<option value="La Paz">La Paz</option>
								<option value="Andhra Pradesh">Andhra Pradesh</option>
								<option value="Arunachal Pradesh">Arunachal Pradesh</option>
								<option value="Assam">Assam</option>
								<option value="Bihar">Bihar</option>
								<option value="Chhattisgarh">Chhattisgarh</option>
								<option value="Goa">Goa</option>
								<option value="Gujarat">Gujarat</option>
								<option value="Haryana">Haryana</option>
								<option value="Himachal Pradesh">Himachal Pradesh</option>
								<option value="Jharkhand">Jharkhand</option>
								<option value="Karnataka">Karnataka</option>
								<option value="Kerala">Kerala</option>
								<option value="Madhya Pradesh">Madhya Pradesh</option>
								<option value="Maharashtra">Maharashtra</option>
								<option value="Manipur">Manipur</option>
								<option value="Meghalaya">Meghalaya</option>
								<option value="Mizoram">Mizoram</option>
								<option value="Nagaland">Nagaland</option>
								<option value="Odisha">Odisha</option>
								<option value="Punjab">Punjab</option>
								<option value="Rajasthan">Rajasthan</option>
								<option value="Sikkim">Sikkim</option>
								<option value="Tamil Nadu">Tamil Nadu</option>
								<option value="Telangana">Telangana</option>
								<option value="Tripura">Tripura</option>
								<option value="Uttar Pradesh">Uttar Pradesh</option>
								<option value="Uttarakhand">Uttarakhand</option>
								<option value="West Bengal">West Bengal</option>
								<option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
								<option value="Chandigarh">Chandigarh</option>
								<option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
								<option value="Lakshadweep">Lakshadweep</option>
								<option value="Delhi">Delhi</option>
								<option value="Puducherry">Puducherry</option>
								<option value="Ladakh">Ladakh</option>
								<option value="Jammu and Kashmir">Jammu and Kashmir</option>
								<option value="Connacht">Connacht</option>
								<option value="Leinster">Leinster</option>
								<option value="Munster">Munster</option>
								<option value="Ulster">Ulster</option>
								<option value="Aichi">Aichi</option>
								<option value="Akita">Akita</option>
								<option value="Aomori">Aomori</option>
								<option value="Chiba">Chiba</option>
								<option value="Ehime">Ehime</option>
								<option value="Fukui">Fukui</option>
								<option value="Fukuoka">Fukuoka</option>
								<option value="Fukushima">Fukushima</option>
								<option value="Gifu">Gifu</option>
								<option value="Gunma">Gunma</option>
								<option value="Hiroshima">Hiroshima</option>
								<option value="Hokkaido">Hokkaido</option>
								<option value="Hyogo">Hyogo</option>
								<option value="Ibaraki">Ibaraki</option>
								<option value="Ishikawa">Ishikawa</option>
								<option value="Iwate">Iwate</option>
								<option value="Kagawa">Kagawa</option>
								<option value="Kagoshima">Kagoshima</option>
								<option value="Kanagawa">Kanagawa</option>
								<option value="Kochi">Kochi</option>
								<option value="Kumamoto">Kumamoto</option>
								<option value="Kyoto">Kyoto</option>
								<option value="Mie">Mie</option>
								<option value="Miyagi">Miyagi</option>
								<option value="Miyazaki">Miyazaki</option>
								<option value="Nagano">Nagano</option>
								<option value="Nagasaki">Nagasaki</option>
								<option value="Nara">Nara</option>
								<option value="Niigata">Niigata</option>
								<option value="Oita">Oita</option>
								<option value="Okayama">Okayama</option>
								<option value="Okinawa">Okinawa</option>
								<option value="Osaka">Osaka</option>
								<option value="Saga">Saga</option>
								<option value="Saitama">Saitama</option>
								<option value="Shiga">Shiga</option>
								<option value="Shimane">Shimane</option>
								<option value="Shizuoka">Shizuoka</option>
								<option value="Tochigi">Tochigi</option>
								<option value="Tokushima">Tokushima</option>
								<option value="Tokyo">Tokyo</option>
								<option value="Tottori">Tottori</option>
								<option value="Toyama">Toyama</option>
								<option value="Wakayama">Wakayama</option>
								<option value="Yamagata">Yamagata</option>
								<option value="Yamaguchi">Yamaguchi</option>
								<option value="Yamanashi">Yamanashi</option>
								<option value="Al Asimah">Al Asimah</option>
								<option value="Al Ahmadi">Al Ahmadi</option>
								<option value="Mubarak Al-Kabeer">Mubarak Al-Kabeer</option>
								<option value="Jahra">Jahra</option>
								<option value="Hawalli">Hawalli</option>
								<option value="Farwaniyah">Farwaniyah</option>
								<option value="Aguascalientes">Aguascalientes</option>
								<option value="Baja California">Baja California</option>
								<option value="Baja California Sur">Baja California Sur</option>
								<option value="Campeche">Campeche</option>
								<option value="Chiapas">Chiapas</option>
								<option value="Chihuahua">Chihuahua</option>
								<option value="Coahuila">Coahuila</option>
								<option value="Colima">Colima</option>
								<option value="Durango">Durango</option>
								<option value="Guanajuato">Guanajuato</option>
								<option value="Guerrero">Guerrero</option>
								<option value="Hidalgo">Hidalgo</option>
								<option value="Jalisco">Jalisco</option>
								<option value="Mexico State">Mexico State</option>
								<option value="Mexico City">Mexico City</option>
								<option value="Morelos">Morelos</option>
								<option value="Nayarit">Nayarit</option>
								<option value="Oaxaca">Oaxaca</option>
								<option value="Puebla">Puebla</option>
								<option value="Quintana Roo">Quintana Roo</option>
								<option value="Sinaloa">Sinaloa</option>
								<option value="Sonora">Sonora</option>
								<option value="Tabasco">Tabasco</option>
								<option value="Tamaulipas">Tamaulipas</option>
								<option value="Tlaxcala">Tlaxcala</option>
								<option value="Veracruz">Veracruz</option>
								<option value="Zacatecas">Zacatecas</option>
								<option value="Ad Dawhah">Ad Dawhah</option>
								<option value="Al Khawr">Al Khawr</option>
								<option value="Al Rayyan">Al Rayyan</option>
								<option value="Al Wakrah">Al Wakrah</option>
								<option value="Umm Salal">Umm Salal</option>
								<option value="Madinat ash Shamal">Madinat ash Shamal</option>
								<option value="Al Shahaniya">Al Shahaniya</option>
								<option value="Jian">Jian</option>
								<option value="Seoul">Seoul</option>
								<option value="Busan">Busan</option>
								<option value="Incheon">Incheon</option>
								<option value="Daegu">Daegu</option>
								<option value="Daejeon">Daejeon</option>
								<option value="Gwangju">Gwangju</option>
								<option value="Ulsan">Ulsan</option>
								<option value="Gyeonggi">Gyeonggi</option>
								<option value="Gangwon">Gangwon</option>
								<option value="Chungcheongbuk-do">Chungcheongbuk-do</option>
								<option value="Chungcheongnam-do">Chungcheongnam-do</option>
								<option value="Jeollabuk-do">Jeollabuk-do</option>
								<option value="Jeollanam-do">Jeollanam-do</option>
								<option value="Gyeongsangbuk-do">Gyeongsangbuk-do</option>
								<option value="Gyeongsangnam-do">Gyeongsangnam-do</option>
								<option value="Jeju-do">Jeju-do</option>
								<option value="Taipei City">Taipei City</option>
								<option value="New Taipei City">New Taipei City</option>
								<option value="Taichung City">Taichung City</option>
								<option value="Tainan City">Tainan City</option>
								<option value="Kaohsiung City">Kaohsiung City</option>
								<option value="Taoyuan City">Taoyuan City</option>
								<option value="Keelung City">Keelung City</option>
								<option value="Hsinchu City">Hsinchu City</option>
								<option value="Chia Yi City">Chia Yi City</option>
								<option value="Taipei County">Taipei County</option>
								<option value="Taichung County">Taichung County</option>
								<option value="Tainan County">Tainan County</option>
								<option value="Kaohsiung County">Kaohsiung County</option>
								<option value="Taoyuan County">Taoyuan County</option>
								<option value="Hsinchu County">Hsinchu County</option>
								<option value="Chia Yi County">Chia Yi County</option>
								<option value="Keelung County">Keelung County</option>
								<option value="Hsinchu City">Hsinchu City</option>
								<option value="Miaoli County">Miaoli County</option>
								<option value="Changhua County">Changhua County</option>
								<option value="Nantou County">Nantou County</option>
								<option value="Yunlin County">Yunlin County</option>
								<option value="Chiayi County">Chiayi County</option>
								<option value="Pingtung County">Pingtung County</option>
								<option value="Taitung County">Taitung County</option>
								<option value="Hualien County">Hualien County</option>
								<option value="Penghu County">Penghu County</option>
								<option value="Kinmen County">Kinmen County</option>
								<option value="Lienchiang County">Lienchiang County</option>
								<option value="Adana">Adana</option>
								<option value="Adiyaman">Adiyaman</option>
								<option value="Afyonkarahisar">Afyonkarahisar</option>
								<option value="Agri">Agri</option>
								<option value="Aksaray">Aksaray</option>
								<option value="Amasya">Amasya</option>
								<option value="Ankara">Ankara</option>
								<option value="Antalya">Antalya</option>
								<option value="Ardahan">Ardahan</option>
								<option value="Artvin">Artvin</option>
								<option value="Aydin">Aydin</option>
								<option value="Balikesir">Balikesir</option>
								<option value="Bartin">Bartin</option>
								<option value="Batman">Batman</option>
								<option value="Bayburt">Bayburt</option>
								<option value="Bilecik">Bilecik</option>
								<option value="Bitlis">Bitlis</option>
								<option value="Bolu">Bolu</option>
								<option value="Burdur">Burdur</option>
								<option value="Bursa">Bursa</option>
								<option value="Denizli">Denizli</option>
								<option value="Diyarbakir">Diyarbakir</option>
								<option value="Edirne">Edirne</option>
								<option value="Elazig">Elazig</option>
								<option value="Erzincan">Erzincan</option>
								<option value="Erzurum">Erzurum</option>
								<option value="Eskisehir">Eskisehir</option>
								<option value="Gaziantep">Gaziantep</option>
								<option value="Giresun">Giresun</option>
								<option value="Hakkari">Hakkari</option>
								<option value="Hatay">Hatay</option>
								<option value="Igdir">Igdir</option>
								<option value="Isparta">Isparta</option>
								<option value="Istanbul">Istanbul</option>
								<option value="Izmir">Izmir</option>
								<option value="Kahramanmaras">Kahramanmaras</option>
								<option value="Karaman">Karaman</option>
								<option value="Kars">Kars</option>
								<option value="Kastamonu">Kastamonu</option>
								<option value="Kayseri">Kayseri</option>
								<option value="Kirikkale">Kirikkale</option>
								<option value="Kirklareli">Kirklareli</option>
								<option value="Kirsehir">Kirsehir</option>
								<option value="Kocaeli">Kocaeli</option>
								<option value="Konya">Konya</option>
								<option value="Malatya">Malatya</option>
								<option value="Manisa">Manisa</option>
								<option value="Mardin">Mardin</option>
								<option value="Mersin">Mersin</option>
								<option value="Mugla">Mugla</option>
								<option value="Mus">Mus</option>
								<option value="Nevsehir">Nevsehir</option>
								<option value="Nigde">Nigde</option>
								<option value="Ordu">Ordu</option>
								<option value="Osmaniye">Osmaniye</option>
								<option value="Rize">Rize</option>
								<option value="Sakarya">Sakarya</option>
								<option value="Samsun">Samsun</option>
								<option value="Siirt">Siirt</option>
								<option value="Sinop">Sinop</option>
								<option value="Sivas">Sivas</option>
								<option value="Sanliurfa">Sanliurfa</option>
								<option value="Sirnak">Sirnak</option>
								<option value="Tekirdag">Tekirdag</option>
								<option value="Tokat">Tokat</option>
								<option value="Trabzon">Trabzon</option>
								<option value="Tunceli">Tunceli</option>
								<option value="Usak">Usak</option>
								<option value="Van">Van</option>
								<option value="Yalova">Yalova</option>
								<option value="Yozgat">Yozgat</option>
								<option value="Zonguldak">Zonguldak</option>
								<option value="Abu Dhabi">Abu Dhabi</option>
								<option value="Dubai" >Dubai</option>
								<option value="Sharjah">Sharjah</option>
								<option value="Ajman">Ajman</option>
								<option value="Umm Al-Quwain">Umm Al-Quwain</option>
								<option value="Fujairah">Fujairah</option>
								<option value="Ras Al Khaimah">Ras Al Khaimah</option>
								<option value="England">England</option>
								<option value="Scotland">Scotland</option>
								<option value="Wales">Wales</option>
								<option value="Northern Ireland">Northern Ireland</option>
								<option value="Central District">Central District</option>
								<option value="Haifa District">Haifa District</option>
								<option value="Jerusalem District">Jerusalem District</option>
								<option value="Northern District">Northern District</option>
								<option value="Southern District">Southern District</option>
								<option value="Tel Aviv District">Tel Aviv District</option>
								<option value="Antigua and Barbuda">Antigua and Barbuda</option>
								<option value="Bahamas">Bahamas</option>
								<option value="Barbados">Barbados</option>
								<option value="Cuba">Cuba</option>
								<option value="Dominica">Dominica</option>
								<option value="Grenada">Grenada</option>
								<option value="Haiti">Haiti</option>
								<option value="Jamaica">Jamaica</option>
								<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
								<option value="Saint Lucia">Saint Lucia</option>
								<option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
								<option value="Trinidad and Tobago">Trinidad and Tobago</option>
								<option value="Guadeloupe">Guadeloupe</option>
								<option value="Martinique">Martinique</option>
								<option value="Aruba">Aruba</option>
								<option value="Bonaire">Bonaire</option>
								<option value="Sint Eustatius">Sint Eustatius</option>
								<option value="Saba">Saba</option>
								<option value="Saint Barthelemy">Saint Barthelemy</option>
								<option value="Saint Martin">Saint Martin</option>
								<option value="British Virgin Islands">British Virgin Islands</option>
								<option value="Anguilla">Anguilla</option>
								<option value="Montserrat">Montserrat</option>
								<option value="Antigua and Barbuda">Antigua and Barbuda</option>
								<option value="Bahamas">Bahamas</option>
								<option value="Barbados">Barbados</option>
								<option value="Cuba">Cuba</option>
								<option value="Dominica">Dominica</option>
								<option value="Grenada">Grenada</option>
								<option value="Haiti">Haiti</option>
								<option value="Jamaica">Jamaica</option>
								<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
								<option value="Saint Lucia">Saint Lucia</option>
								<option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
								<option value="Trinidad and Tobago">Trinidad and Tobago</option>
								<option value="Guadeloupe">Guadeloupe</option>
								<option value="Martinique">Martinique</option>
								<option value="Aruba">Aruba</option>
								<option value="Curacao">Curacao</option>
								<option value="Sint Maarten">Sint Maarten</option>
								<option value="Bonaire">Bonaire</option>
								<option value="Sint Eustatius">Sint Eustatius</option>
								<option value="Saba">Saba</option>
								<option value="British Virgin Islands">British Virgin Islands</option>
								<option value="Anguilla">Anguilla</option>
								<option value="Montserrat">Montserrat</option>
								<option value="Saint Barthelemy">Saint Barthelemy</option>
								<option value="Saint Martin">Saint Martin</option>
								<option value="Drenthe">Drenthe</option>
								<option value="Flevoland">Flevoland</option>
								<option value="Friesland">Friesland</option>
								<option value="Gelderland">Gelderland</option>
								<option value="Groningen">Groningen</option>
								<option value="Limburg">Limburg</option>
								<option value="North Brabant">North Brabant</option>
								<option value="North Holland">North Holland</option>
								<option value="Overijssel">Overijssel</option>
								<option value="South Holland">South Holland</option>
								<option value="Utrecht">Utrecht</option>
								<option value="Zeeland">Zeeland</option>
								<option value="Central Region">Central Region</option>
								<option value="East Region">East Region</option>
								<option value="North Region">North Region</option>
								<option value="North-East Region">North-East Region</option>
								<option value="West Region">West Region</option>
								<option value="Auckland">Auckland</option>
								<option value="Bay of Plenty">Bay of Plenty</option>
								<option value="Canterbury">Canterbury</option>
								<option value="Gisborne">Gisborne</option>
								<option value="Hawke's Bay">Hawke's Bay</option>
								<option value="Manawatu-Whanganui">Manawatu-Whanganui</option>
								<option value="Marlborough">Marlborough</option>
								<option value="Nelson">Nelson</option>
								<option value="Northland">Northland</option>
								<option value="Otago">Otago</option>
								<option value="Southland">Southland</option>
								<option value="Tasman">Tasman</option>
								<option value="Taranaki">Taranaki</option>
								<option value="Wairarapa">Wairarapa</option>
								<option value="Wellington">Wellington</option>
								<option value="West Coast">West Coast</option>
								<option value="Other">Other</option>
							</select>
						</div>
					</div>
                    
                    <div class="form-group">
						<label class="control-label col-md-3">City : <span class="required-star">*</span></label>
						<div class="col-md-4">
							<input class="form-control" type="text" name="client_city" id="client_city" placeholder="Enter City Name" value="">
						</div>
					</div>
                    
					<div class="form-group">
						<label class="control-label col-md-3">Contact Number : </label>
						<div class="col-md-4">
							<input class="form-control" type="text" name="client_contact_num" id="client_contact_num" placeholder="Enter Contact Number" value="<?php echo set_value('client_contact_num'); ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3">Description : <span class="required-star">*</span> </label>
						<div class="col-md-4">
							<textarea class="form-control" name="client_desc" id="client_desc" placeholder="Enter Client Description" rows="3"><?php echo set_value('client_desc'); ?></textarea>
						</div>
					</div>
					<div class="card-footer">
						<div class="row">
							<div class="col-md-8 col-md-offset-3">
								<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Create</button>
								   <a class="btn btn-default icon-btn" href="<?php echo base_url('clients');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php else: ?>
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div>
					<h4 class="line-head">Update Client</h4>
					<span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Clients" href="<?php echo base_url('clients');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span>
				</div>
				<div style="clear:both;"></div>

				<?php foreach($updateClient as $key => $getClient) { 	 }   ?>

				<form class="form-horizontal" method="post" name="add_client" id="add_client" action="<?php echo base_url('clients/updateclient');?>">
					<input type="hidden" name="client_Id" id="client_Id" value="<?php echo $getClient->client_Id; ?>" />
					<div class="form-group">
						<label class="control-label col-md-3">Client Name : <span class="required-star">*</span></label>
						<div class="col-md-4">
							<input class="form-control" type="text" name="client_name" id="client_name" placeholder="Enter Client Name" value="<?php echo $getClient->client_name; ?>">
							<?php echo form_error('client_name'); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3">Department: <span class="required-star">*</span></label>
						<div class="col-md-4">
					    	<select class="form-control" id="department" name="department">
								<option value="" selected="selected">Choose Department</option>
								<option value="Architectural" <?php echo ($getClient->department ==  'Architectural') ? ' selected="selected"' : '';?>>Architectural</option>
								<option value="Structural" <?php echo ($getClient->department ==  'Structural') ? ' selected="selected"' : '';?>>Structural</option>
								<option value="MEP" <?php echo ($getClient->department ==  'MEP') ? ' selected="selected"' : '';?>>MEP</option>
								<option value="3D Visualization" <?php echo ($getClient->department ==  '3D Visualization') ? ' selected="selected"' : '';?>>3D Visualization</option>
								<!-- <option value="Middle East" <?php echo ($getClient->department ==  'Middle East') ? ' selected="selected"' : '';?>>Middle East</option> -->								
							</select>

						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3">Client Email :
							<!--<span class="required-star">*</span>-->
						</label>
						<div class="col-md-4">
							<input class="form-control" placeholder="Enter email address" type="email" name="client_email" id="client_email" value="<?php echo $getClient->client_email; ?>">
							<?php echo form_error('client_email'); ?>
						</div>
					</div>
                    <!-- Country , State and city -->
                    
                   <div class="form-group">
    <label class="control-label col-md-3">Country : <span class="required-star">*</span></label>
    <div class="col-md-4">
        <select class="form-control form-select" id="country" name="client_country">
            <option value="" disabled <?php if(empty($getClient->client_country)) echo 'selected'; ?>>Select a country</option>
            <?php
            $countries = [
                "Afghanistan","Albania","Algeria","Andorra","Angola","Argentina","Armenia","Australia","Austria","Azerbaijan",
                "Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia",
                "Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cabo Verde","Cambodia","Cameroon","Canada",
                "Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Congo-Brazzaville)","Costa Rica","Croatia","Cuba",
                "Cyprus","Czechia (Czech Republic)","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor-Leste)","Ecuador","Egypt","El Salvador",
                "Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia","Georgia",
                "Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras",
                "Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica",
                "Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea (North)","Korea (South)","Kosovo","Kuwait","Kyrgyzstan",
                "Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Madagascar",
                "Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia",
                "Moldova","Monaco","Mongolia","Montenegro","Morocco","Mozambique","Myanmar (Burma)","Namibia","Nauru","Nepal",
                "Netherlands","New Zealand","Nicaragua","Niger","Nigeria","North Macedonia","Norway","Oman","Pakistan","Palau",
                "Palestine","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania",
                "Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal",
                "Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Sudan",
                "Spain","Sri Lanka","Sudan","Suriname","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania",
                "Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine",
                "United Arab Emirates","United Kingdom","United States of America","Uruguay","Uzbekistan","Vanuatu","Vatican City (Holy See)","Venezuela","Vietnam","Yemen",
                "Holy See (Vatican City)","Palestine"
            ];
            foreach($countries as $country) {
                $selected = ($getClient->client_country == $country) ? 'selected' : '';
                echo "<option value=\"{$country}\" {$selected}>{$country}</option>";
            }
            ?>
        </select>    
    </div>						
</div>
                    
                     <div class="form-group">
						<label class="control-label col-md-3">State : <span class="required-star">*</span></label>
						<div class="col-md-4">							
                             
                            <select class="form-control form-select" id="state" name="client_state">
								<option value="" disabled <?php if(empty($getClient->client_state)) echo 'selected'; ?>>Select a State</option>
								<?php
								$states = [
									"Alabama","Alaska","Arizona","Arkansas","California","Colorado","Connecticut","Delaware","District Of Columbia","Florida",
									"Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts",
									"Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico",
									"New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina",
									"South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming",
									"Badakhshan","Badghis","Baghlan","Balkh","Bamyan","Daykundi","Farah","Faryab","Ghaziabad","Ghazni","Helmand","Herat",
									"Jowzjan","Kabul","Kandahar","Kapisa","Khost","Kunar","Kunduz","Laghman","Logar","Nangarhar","Nimroz","Nuristan","Paktia",
									"Paktika","Panjshir","Parwan","Samangan","Sar-e Pol","Takhar","Urozgan","Wardak","Zabul","New South Wales","Victoria",
									"Queensland","South Australia","Western Australia","Tasmania","Australian Capital Territory","Northern Territory",
									"Capital Governorate","Muharraq Governorate","Northern Governorate","Southern Governorate","Central Governorate",
									"Alberta","British Columbia","Manitoba","New Brunswick","Newfoundland and Labrador","Northwest Territories","Nova Scotia",
									"Nunavut","Ontario","Prince Edward Island","Quebec","Saskatchewan","Yukon","Anhui","Beijing","Chongqing","Fujian","Gansu",
									"Guangdong","Guangxi","Guizhou","Hainan","Hebei","Heilongjiang","Henan","Hubei","Hunan","Jiangsu","Jiangxi","Jilin",
									"Liaoning","Macau","Ningxia","Qinghai","Shaanxi","Shandong","Shanghai","Shanxi","Sichuan","Taiwan","Tianjin","Tibet",
									"Xinjiang","Yunnan","Zhejiang","Choluteca","Comayagua","Distrito Central","Gracias a Dios","La Paz","Lempira","Ocotepeque",
									"Olancho","Yoro","La Libertad","Andhra Pradesh","Arunachal Pradesh","Assam","Bihar","Chhattisgarh","Goa","Gujarat",
									"Haryana","Himachal Pradesh","Jharkhand","Karnataka","Kerala","Madhya Pradesh","Maharashtra","Manipur","Meghalaya",
									"Mizoram","Nagaland","Odisha","Punjab","Rajasthan","Sikkim","Tamil Nadu","Telangana","Tripura","Uttar Pradesh",
									"Uttarakhand","West Bengal","Andaman and Nicobar Islands","Chandigarh","Lakshadweep","Delhi","Puducherry","Ladakh",
									"Jammu and Kashmir","Connacht","Leinster","Munster","Ulster","Aichi","Akita","Aomori","Chiba","Ehime","Fukui","Fukuoka",
									"Fukushima","Gifu","Gunma","Hiroshima","Hokkaido","Hyogo","Ibaraki","Ishikawa","Iwate","Kagawa","Kagoshima","Kanagawa",
									"Kochi","Kumamoto","Kyoto","Mie","Miyagi","Miyazaki","Nagano","Nagasaki","Nara","Niigata","Oita","Okayama","Okinawa",
									"Osaka","Saga","Saitama","Shiga","Shimane","Shizuoka","Tochigi","Tokushima","Tokyo","Tottori","Toyama","Wakayama",
									"Yamagata","Yamaguchi","Yamanashi","Al Asimah","Al Ahmadi","Mubarak Al-Kabeer","Jahra","Hawalli","Farwaniyah",
									"Aguascalientes","Baja California","Baja California Sur","Campeche","Chiapas","Chihuahua","Coahuila","Colima","Durango",
									"Guanajuato","Guerrero","Hidalgo","Jalisco","Mexico State","Mexico City","Morelos","Nayarit","Oaxaca","Puebla",
									"Quintana Roo","Sinaloa","Sonora","Tabasco","Tamaulipas","Tlaxcala","Veracruz","Zacatecas","Ad Dawhah","Al Khawr",
									"Al Rayyan","Al Wakrah","Umm Salal","Madinat ash Shamal","Al Shahaniya","Jian","Seoul","Busan","Incheon","Daegu",
									"Daejeon","Gwangju","Ulsan","Gyeonggi","Gangwon","Chungcheongbuk-do","Chungcheongnam-do","Jeollabuk-do",
									"Jeollanam-do","Gyeongsangbuk-do","Gyeongsangnam-do","Jeju-do","Taipei City","New Taipei City","Taichung City",
									"Tainan City","Kaohsiung City","Taoyuan City","Keelung City","Hsinchu City","Chia Yi City","Taipei County",
									"Taichung County","Tainan County","Kaohsiung County","Taoyuan County","Hsinchu County","Chia Yi County",
									"Keelung County","Miaoli County","Changhua County","Nantou County","Yunlin County","Chiayi County","Pingtung County",
									"Taitung County","Hualien County","Penghu County","Kinmen County","Lienchiang County","Adana","Adiyaman",
									"Afyonkarahisar","Agri","Aksaray","Amasya","Ankara","Antalya","Ardahan","Artvin","Aydin","Balikesir","Bartin",
									"Batman","Bayburt","Bilecik","Bitlis","Bolu","Burdur","Bursa","Denizli","Diyarbakir","Edirne","Elazig","Erzincan",
									"Erzurum","Eskisehir","Gaziantep","Giresun","Hakkari","Hatay","Igdir","Isparta","Istanbul","Izmir","Kahramanmaras",
									"Karaman","Kars","Kastamonu","Kayseri","Kirikkale","Kirklareli","Kirsehir","Kocaeli","Konya","Malatya","Manisa",
									"Mardin","Mersin","Mugla","Mus","Nevsehir","Nigde","Ordu","Osmaniye","Rize","Sakarya","Samsun","Siirt","Sinop",
									"Sivas","Sanliurfa","Sirnak","Tekirdag","Tokat","Trabzon","Tunceli","Usak","Van","Yalova","Yozgat","Zonguldak",
									"Abu Dhabi","Dubai","Sharjah","Ajman","Umm Al-Quwain","Fujairah","Ras Al Khaimah","England","Scotland","Wales",
									"Northern Ireland","Central District","Haifa District","Jerusalem District","Northern District","Southern District",
									"Tel Aviv District","Antigua and Barbuda","Bahamas","Barbados","Cuba","Dominica","Grenada","Haiti","Jamaica",
									"Saint Kitts and Nevis","Saint Lucia","Saint Vincent and the Grenadines","Trinidad and Tobago","Guadeloupe",
									"Martinique","Aruba","Bonaire","Sint Eustatius","Saba","Saint Barthelemy","Saint Martin","British Virgin Islands",
									"Anguilla","Montserrat","Curacao","Sint Maarten","Drenthe","Flevoland","Friesland","Gelderland","Groningen",
									"Limburg","North Brabant","North Holland","Overijssel","South Holland","Utrecht","Zeeland","Central Region",
									"East Region","North Region","North-East Region","West Region","Auckland","Bay of Plenty","Canterbury","Gisborne",
									"Hawke's Bay","Manawatu-Whanganui","Marlborough","Nelson","Northland","Otago","Southland","Tasman","Taranaki",
									"Wairarapa","Wellington","West Coast","Other"
								];
								foreach($states as $state) {
									$selected = ($getClient->client_state == $state) ? 'selected' : '';
									echo "<option value=\"{$state}\" {$selected}>{$state}</option>";
								}
								?>
							</select>
						</div>
					</div>
                    
                    <div class="form-group">
						<label class="control-label col-md-3">City : <span class="required-star">*</span></label>
						<div class="col-md-4">
							<input class="form-control" type="text" name="client_city" id="client_city" placeholder="Enter City Name" value="<?php echo $getClient->client_city; ?>">
						</div>
					</div>
                    
                    <!-- Country , State and city end -->
					<div class="form-group">
						<label class="control-label col-md-3">Contact Number : </label>
						<div class="col-md-4">
							<input class="form-control" type="text" name="client_contact_num" id="client_contact_num" placeholder="Enter Contact Number" value="<?php echo $getClient->client_contact_num; ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3">Description : <span class="required-star">*</span> </label>
						<div class="col-md-4">
							<textarea class="form-control" name="client_desc" id="client_desc" placeholder="Enter Client Description" rows="3"><?php echo $getClient->client_desc; ?></textarea>
						</div>
					</div>

					<div class="card-footer">
						<div class="row">
							<div class="col-md-8 col-md-offset-3">
								<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update</button>
								   <a class="btn btn-default icon-btn" href="<?php echo base_url('clients');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>
<!-- Organizatoin form validation -->


<script type="text/javascript" language="javascript">
	// Wait for the DOM to be ready
	$(function() {
		$("form[name='add_client']").validate({
			rules: {
				client_name: {
					required: true
				},
				department: {
					required: true
				},
				//client_email        		 : { required : true },
				client_desc: {
					required: true
				},
                client_desc: {
					required: true
				},
                client_country: {
					required: true
				},
                client_state: {
					required: true
				},
                client_city: {
					required: true
				},

			},
			messages: {
				client_name: "Please Enter Client Name",
				department	: "Please Choose Department",
				client_desc: "Please Enter Client Description",
                client_country: "Please Choose Country Name",
                client_state: "Please Choose State Name",
                client_city: "Please Enter City Name",
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	});
	$('#department').select2();	 // Autosuggest list
</script>
<!-- Organizatoin form validation -->
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->