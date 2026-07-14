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
								<option value="2D Auto CAD">2D Auto CAD</option>
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
								<option value="" disabled>Select a country</option>
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
								<option value="United States of America" selected>United States of America</option>
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
								<!-- US States -->
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
							</select>
						</div>
					</div>
					<script>
					// Country to states mapping (only a few examples for brevity, add more as needed)
					const countryStates = {
						"United States of America": [
							"Alabama","Alaska","Arizona","Arkansas","California","Colorado","Connecticut","Delaware","District Of Columbia","Florida","Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming"
						],
						"Canada": [
							"Alberta","British Columbia","Manitoba","New Brunswick","Newfoundland and Labrador","Northwest Territories","Nova Scotia","Nunavut","Ontario","Prince Edward Island","Quebec","Saskatchewan","Yukon"
						],
						"India": [
							"Andhra Pradesh","Arunachal Pradesh","Assam","Bihar","Chhattisgarh","Goa","Gujarat","Haryana","Himachal Pradesh","Jharkhand","Karnataka","Kerala","Madhya Pradesh","Maharashtra","Manipur","Meghalaya","Mizoram","Nagaland","Odisha","Punjab","Rajasthan","Sikkim","Tamil Nadu","Telangana","Tripura","Uttar Pradesh","Uttarakhand","West Bengal","Andaman and Nicobar Islands","Chandigarh","Dadra and Nagar Haveli and Daman and Diu","Lakshadweep","Delhi","Puducherry","Ladakh","Jammu and Kashmir"
						]
						// Add more countries and their states/provinces as needed
						"Australia": [
							"New South Wales", "Victoria", "Queensland", "Western Australia", "South Australia", "Tasmania", "Australian Capital Territory", "Northern Territory"
						],
						"United Kingdom": [
							"England", "Scotland", "Wales", "Northern Ireland"
						],
						"Germany": [
							"Baden-Württemberg", "Bavaria", "Berlin", "Brandenburg", "Bremen", "Hamburg", "Hesse", "Lower Saxony", "Mecklenburg-Vorpommern", "North Rhine-Westphalia", "Rhineland-Palatinate", "Saarland", "Saxony", "Saxony-Anhalt", "Schleswig-Holstein", "Thuringia"
						],
						"South Africa": [
							"Eastern Cape", "Free State", "Gauteng", "KwaZulu-Natal", "Limpopo", "Mpumalanga", "Northern Cape", "North West", "Western Cape"
						],
						"Brazil": [
							"Acre", "Alagoas", "Amapá", "Amazonas", "Bahia", "Ceará", "Distrito Federal", "Espírito Santo", "Goiás", "Maranhão", "Mato Grosso", "Mato Grosso do Sul", "Minas Gerais", "Pará", "Paraíba", "Paraná", "Pernambuco", "Piauí", "Rio de Janeiro", "Rio Grande do Norte", "Rio Grande do Sul", "Rondônia", "Roraima", "Santa Catarina", "São Paulo", "Sergipe", "Tocantins"
						],
						"Mexico": [
							"Aguascalientes", "Baja California", "Baja California Sur", "Campeche", "Chiapas", "Chihuahua", "Coahuila", "Colima", "Durango", "Guanajuato", "Guerrero", "Hidalgo", "Jalisco", "Mexico State", "Michoacán", "Morelos", "Nayarit", "Nuevo León", "Oaxaca", "Puebla", "Querétaro", "Quintana Roo", "San Luis Potosí", "Sinaloa", "Sonora", "Tabasco", "Tamaulipas", "Tlaxcala", "Veracruz", "Yucatán", "Zacatecas", "Mexico City"
						],
						"China": [
							"Anhui", "Beijing", "Chongqing", "Fujian", "Gansu", "Guangdong", "Guangxi", "Guizhou", "Hainan", "Hebei", "Heilongjiang", "Henan", "Hong Kong", "Hubei", "Hunan", "Inner Mongolia", "Jiangsu", "Jiangxi", "Jilin", "Liaoning", "Macau", "Ningxia", "Qinghai", "Shaanxi", "Shandong", "Shanghai", "Shanxi", "Sichuan", "Tianjin", "Tibet", "Xinjiang", "Yunnan", "Zhejiang"
						],
						"France": [
							"Auvergne-Rhône-Alpes", "Bourgogne-Franche-Comté", "Brittany", "Centre-Val de Loire", "Corsica", "Grand Est", "Hauts-de-France", "Île-de-France", "Normandy", "Nouvelle-Aquitaine", "Occitanie", "Pays de la Loire", "Provence-Alpes-Côte d'Azur"
						],
						"Russia": [
							"Adygea", "Altai Krai", "Altai Republic", "Amur Oblast", "Arkhangelsk Oblast", "Astrakhan Oblast", "Bashkortostan", "Belgorod Oblast", "Bryansk Oblast", "Buryatia", "Chechen Republic", "Chelyabinsk Oblast", "Chukotka Autonomous Okrug", "Chuvashia", "Dagestan", "Ingushetia", "Irkutsk Oblast", "Ivanovo Oblast", "Jewish Autonomous Oblast", "Kabardino-Balkaria", "Kaliningrad Oblast", "Kalmykia", "Kaluga Oblast", "Kamchatka Krai", "Karachay-Cherkessia", "Karelia", "Kemerovo Oblast", "Khabarovsk Krai", "Khakassia", "Khanty-Mansi Autonomous Okrug", "Kirov Oblast", "Komi Republic", "Kostroma Oblast", "Krasnodar Krai", "Krasnoyarsk Krai", "Kurgan Oblast", "Kursk Oblast", "Leningrad Oblast", "Lipetsk Oblast", "Magadan Oblast", "Mari El", "Mordovia", "Moscow", "Moscow Oblast", "Murmansk Oblast", "Nenets Autonomous Okrug", "Nizhny Novgorod Oblast", "North Ossetia–Alania", "Novgorod Oblast", "Novosibirsk Oblast", "Omsk Oblast", "Orenburg Oblast", "Oryol Oblast", "Penza Oblast", "Perm Krai", "Primorsky Krai", "Pskov Oblast", "Rostov Oblast", "Ryazan Oblast", "Saint Petersburg", "Sakha", "Sakhalin Oblast", "Samara Oblast", "Saratov Oblast", "Smolensk Oblast", "Stavropol Krai", "Sverdlovsk Oblast", "Tambov Oblast", "Tatarstan", "Tomsk Oblast", "Tula Oblast", "Tuva", "Tver Oblast", "Tyumen Oblast", "Udmurtia", "Ulyanovsk Oblast", "Vladimir Oblast", "Volgograd Oblast", "Vologda Oblast", "Voronezh Oblast", "Yamalo-Nenets Autonomous Okrug", "Yaroslavl Oblast", "Zabaykalsky Krai"
						],
						"Japan": [
							"Aichi", "Akita", "Aomori", "Chiba", "Ehime", "Fukui", "Fukuoka", "Fukushima", "Gifu", "Gunma", "Hiroshima", "Hokkaido", "Hyogo", "Ibaraki", "Ishikawa", "Iwate", "Kagawa", "Kagoshima", "Kanagawa", "Kochi", "Kumamoto", "Kyoto", "Mie", "Miyagi", "Miyazaki", "Nagano", "Nagasaki", "Nara", "Niigata", "Oita", "Okayama", "Okinawa", "Osaka", "Saga", "Saitama", "Shiga", "Shimane", "Shizuoka", "Tochigi", "Tokushima", "Tokyo", "Tottori", "Toyama", "Wakayama", "Yamagata", "Yamaguchi", "Yamanashi"
						]
						"Argentina": [
							"Buenos Aires", "Catamarca", "Chaco", "Chubut", "Córdoba", "Corrientes", "Entre Ríos", "Formosa", "Jujuy", "La Pampa", "La Rioja", "Mendoza", "Misiones", "Neuquén", "Río Negro", "Salta", "San Juan", "San Luis", "Santa Cruz", "Santa Fe", "Santiago del Estero", "Tierra del Fuego", "Tucumán"
						],
						"Egypt": [
							"Alexandria", "Aswan", "Asyut", "Beheira", "Beni Suef", "Cairo", "Dakahlia", "Damietta", "Faiyum", "Gharbia", "Giza", "Ismailia", "Kafr El Sheikh", "Luxor", "Matruh", "Minya", "Monufia", "New Valley", "North Sinai", "Port Said", "Qalyubia", "Qena", "Red Sea", "Sharqia", "Sohag", "South Sinai", "Suez"
						],
						"Turkey": [
							"Adana", "Adıyaman", "Afyonkarahisar", "Ağrı", "Aksaray", "Amasya", "Ankara", "Antalya", "Ardahan", "Artvin", "Aydın", "Balıkesir", "Bartın", "Batman", "Bayburt", "Bilecik", "Bingöl", "Bitlis", "Bolu", "Burdur", "Bursa", "Çanakkale", "Çankırı", "Çorum", "Denizli", "Diyarbakır", "Düzce", "Edirne", "Elazığ", "Erzincan", "Erzurum", "Eskişehir", "Gaziantep", "Giresun", "Gümüşhane", "Hakkari", "Hatay", "Iğdır", "Isparta", "İstanbul", "İzmir", "Kahramanmaraş", "Karabük", "Karaman", "Kars", "Kastamonu", "Kayseri", "Kilis", "Kırıkkale", "Kırklareli", "Kırşehir", "Kocaeli", "Konya", "Kütahya", "Malatya", "Manisa", "Mardin", "Mersin", "Muğla", "Muş", "Nevşehir", "Niğde", "Ordu", "Osmaniye", "Rize", "Sakarya", "Samsun", "Şanlıurfa", "Siirt", "Sinop", "Şırnak", "Sivas", "Tekirdağ", "Tokat", "Trabzon", "Tunceli", "Uşak", "Van", "Yalova", "Yozgat", "Zonguldak"
						],
						"Pakistan": [
							"Azad Kashmir", "Balochistan", "Gilgit-Baltistan", "Islamabad Capital Territory", "Khyber Pakhtunkhwa", "Punjab", "Sindh"
						],
						"Italy": [
							"Abruzzo", "Aosta Valley", "Apulia", "Basilicata", "Calabria", "Campania", "Emilia-Romagna", "Friuli Venezia Giulia", "Lazio", "Liguria", "Lombardy", "Marche", "Molise", "Piedmont", "Sardinia", "Sicily", "Trentino-Alto Adige", "Tuscany", "Umbria", "Veneto"
						],
						"Spain": [
							"Andalusia", "Aragon", "Asturias", "Balearic Islands", "Basque Country", "Canary Islands", "Cantabria", "Castile and León", "Castilla-La Mancha", "Catalonia", "Extremadura", "Galicia", "La Rioja", "Madrid", "Murcia", "Navarre", "Valencian Community", "Ceuta", "Melilla"
						],
						"Indonesia": [
							"Aceh", "Bali", "Banten", "Bengkulu", "Central Java", "Central Kalimantan", "Central Sulawesi", "East Java", "East Kalimantan", "East Nusa Tenggara", "Gorontalo", "Jakarta", "Jambi", "Lampung", "Maluku", "North Kalimantan", "North Maluku", "North Sulawesi", "North Sumatra", "Papua", "Riau", "Riau Islands", "Southeast Sulawesi", "South Kalimantan", "South Sulawesi", "South Sumatra", "West Java", "West Kalimantan", "West Nusa Tenggara", "West Papua", "West Sulawesi", "West Sumatra", "Yogyakarta"
						],
						"Saudi Arabia": [
							"Al Bahah", "Al Jawf", "Al Madinah", "Al-Qassim", "Asir", "Eastern Province", "Ha'il", "Jizan", "Makkah", "Najran", "Northern Borders", "Riyadh", "Tabuk"
						],
						"Ukraine": [
							"Vinnytsia", "Volyn", "Dnipropetrovsk", "Donetsk", "Zhytomyr", "Transcarpathia", "Zaporizhzhia", "Ivano-Frankivsk", "Kyiv", "Kirovohrad", "Luhansk", "Lviv", "Mykolaiv", "Odessa", "Poltava", "Rivne", "Sumy", "Ternopil", "Kharkiv", "Kherson", "Khmelnytskyi", "Cherkasy", "Chernivtsi", "Chernihiv", "Sevastopol", "Autonomous Republic of Crimea"
						],
						"Poland": [
							"Greater Poland", "Kuyavian-Pomeranian", "Lesser Poland", "Łódź", "Lower Silesian", "Lublin", "Lubusz", "Masovian", "Opole", "Podlaskie", "Pomeranian", "Silesian", "Świętokrzyskie", "Warmian-Masurian", "West Pomeranian", "Subcarpathian"
						],
						"Bangladesh": [
							"Barisal", "Chittagong", "Dhaka", "Khulna", "Mymensingh", "Rajshahi", "Rangpur", "Sylhet"
						],
						"South Korea": [
							"Busan", "Daegu", "Daejeon", "Gwangju", "Incheon", "Sejong", "Seoul", "Ulsan", "Gyeonggi", "Gangwon", "North Chungcheong", "South Chungcheong", "North Jeolla", "South Jeolla", "North Gyeongsang", "South Gyeongsang", "Jeju"
						],
						"Thailand": [
							"Bangkok", "Amnat Charoen", "Ang Thong", "Bueng Kan", "Buri Ram", "Chachoengsao", "Chai Nat", "Chaiyaphum", "Chanthaburi", "Chiang Mai", "Chiang Rai", "Chonburi", "Chumphon", "Kalasin", "Kamphaeng Phet", "Kanchanaburi", "Khon Kaen", "Krabi", "Lampang", "Lamphun", "Loei", "Lopburi", "Mae Hong Son", "Maha Sarakham", "Mukdahan", "Nakhon Nayok", "Nakhon Pathom", "Nakhon Phanom", "Nakhon Ratchasima", "Nakhon Sawan", "Nakhon Si Thammarat", "Nan", "Narathiwat", "Nong Bua Lamphu", "Nong Khai", "Nonthaburi", "Pathum Thani", "Pattani", "Phang Nga", "Phatthalung", "Phayao", "Phetchabun", "Phetchaburi", "Phichit", "Phitsanulok", "Phra Nakhon Si Ayutthaya", "Phrae", "Phuket", "Prachinburi", "Prachuap Khiri Khan", "Ranong", "Ratchaburi", "Rayong", "Roi Et", "Sa Kaeo", "Sakon Nakhon", "Samut Prakan", "Samut Sakhon", "Samut Songkhram", "Saraburi", "Satun", "Sing Buri", "Sisaket", "Songkhla", "Sukhothai", "Suphan Buri", "Surat Thani", "Surin", "Tak", "Trang", "Trat", "Ubon Ratchathani", "Udon Thani", "Uthai Thani", "Uttaradit", "Yala", "Yasothon"
						]
					};

					function updateStates() {
						const country = document.getElementById('country').value;
						const stateSelect = document.getElementById('state');
						const defaultOption = document.createElement('option');
						defaultOption.value = "";
						defaultOption.disabled = true;
						defaultOption.selected = true;
						defaultOption.textContent = "Select a State";
						stateSelect.innerHTML = "";
						stateSelect.appendChild(defaultOption);

						if(countryStates[country]) {
							countryStates[country].forEach(function(state) {
								const opt = document.createElement('option');
								opt.value = state;
								opt.textContent = state;
								stateSelect.appendChild(opt);
							});
						} else {
							const opt = document.createElement('option');
							opt.value = "Other";
							opt.textContent = "Other";
							stateSelect.appendChild(opt);
						}
					}

					document.addEventListener('DOMContentLoaded', function() {
						document.getElementById('country').addEventListener('change', updateStates);
						// Set default to USA and populate states
						document.getElementById('country').value = "United States of America";
						updateStates();
					});
					</script>
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
							&nbsp;&nbsp;<a class="btn btn-default icon-btn" href="<?php echo base_url('clients');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
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
								<option value="2D Auto CAD" <?php echo ($getClient->department ==  '2D Auto CAD') ? ' selected="selected"' : '';?>>2D Auto CAD</option>							
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
            <?php if(!empty($getClient->client_state)): ?>
                <option value="<?php echo htmlspecialchars($getClient->client_state); ?>" selected><?php echo htmlspecialchars($getClient->client_state); ?></option>
            <?php endif; ?>
        </select>
    </div>
</div>

<script type="text/javascript">
    // Country to states mapping (partial, add more as needed)
    var countryStateMap = {
        "United States of America": [
            "Alabama","Alaska","Arizona","Arkansas","California","Colorado","Connecticut","Delaware","District Of Columbia","Florida",
            "Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts",
            "Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico",
            "New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina",
            "South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming"
        ],
        "India": [
            "Andhra Pradesh","Arunachal Pradesh","Assam","Bihar","Chhattisgarh","Goa","Gujarat",
            "Haryana","Himachal Pradesh","Jharkhand","Karnataka","Kerala","Madhya Pradesh","Maharashtra","Manipur","Meghalaya",
            "Mizoram","Nagaland","Odisha","Punjab","Rajasthan","Sikkim","Tamil Nadu","Telangana","Tripura","Uttar Pradesh",
            "Uttarakhand","West Bengal","Andaman and Nicobar Islands","Chandigarh","Lakshadweep","Delhi","Puducherry","Ladakh",
            "Jammu and Kashmir"
        ],
        "Australia": [
            "New South Wales","Victoria","Queensland","South Australia","Western Australia","Tasmania","Australian Capital Territory","Northern Territory"
        ],
        "Canada": [
            "Alberta","British Columbia","Manitoba","New Brunswick","Newfoundland and Labrador","Northwest Territories","Nova Scotia",
            "Nunavut","Ontario","Prince Edward Island","Quebec","Saskatchewan","Yukon"
        ],
        // Add more countries and their states/provinces as needed
        "United Kingdom": [
            "England", "Northern Ireland", "Scotland", "Wales"
        ],
        "Germany": [
            "Baden-Württemberg", "Bavaria", "Berlin", "Brandenburg", "Bremen", "Hamburg", "Hesse", "Lower Saxony", "Mecklenburg-Vorpommern", "North Rhine-Westphalia", "Rhineland-Palatinate", "Saarland", "Saxony", "Saxony-Anhalt", "Schleswig-Holstein", "Thuringia"
        ],
        "Brazil": [
            "Acre", "Alagoas", "Amapá", "Amazonas", "Bahia", "Ceará", "Distrito Federal", "Espírito Santo", "Goiás", "Maranhão", "Mato Grosso", "Mato Grosso do Sul", "Minas Gerais", "Pará", "Paraíba", "Paraná", "Pernambuco", "Piauí", "Rio de Janeiro", "Rio Grande do Norte", "Rio Grande do Sul", "Rondônia", "Roraima", "Santa Catarina", "São Paulo", "Sergipe", "Tocantins"
        ],
        "China": [
            "Anhui", "Beijing", "Chongqing", "Fujian", "Gansu", "Guangdong", "Guangxi", "Guizhou", "Hainan", "Hebei", "Heilongjiang", "Henan", "Hong Kong", "Hubei", "Hunan", "Inner Mongolia", "Jiangsu", "Jiangxi", "Jilin", "Liaoning", "Macau", "Ningxia", "Qinghai", "Shaanxi", "Shandong", "Shanghai", "Shanxi", "Sichuan", "Tianjin", "Tibet", "Xinjiang", "Yunnan", "Zhejiang"
        ],
        "South Africa": [
            "Eastern Cape", "Free State", "Gauteng", "KwaZulu-Natal", "Limpopo", "Mpumalanga", "North West", "Northern Cape", "Western Cape"
        ],
        "Mexico": [
            "Aguascalientes", "Baja California", "Baja California Sur", "Campeche", "Chiapas", "Chihuahua", "Coahuila", "Colima", "Durango", "Guanajuato", "Guerrero", "Hidalgo", "Jalisco", "Mexico City", "México State", "Michoacán", "Morelos", "Nayarit", "Nuevo León", "Oaxaca", "Puebla", "Querétaro", "Quintana Roo", "San Luis Potosí", "Sinaloa", "Sonora", "Tabasco", "Tamaulipas", "Tlaxcala", "Veracruz", "Yucatán", "Zacatecas"
        ],
        "Russia": [
            "Adygea", "Altai Krai", "Altai Republic", "Amur", "Arkhangelsk", "Astrakhan", "Bashkortostan", "Belgorod", "Bryansk", "Buryatia", "Chechnya", "Chelyabinsk", "Chukotka", "Chuvashia", "Dagestan", "Ingushetia", "Irkutsk", "Ivanovo", "Jewish Autonomous Oblast", "Kabardino-Balkaria", "Kaliningrad", "Kalmykia", "Kaluga", "Kamchatka", "Karachay-Cherkessia", "Karelia", "Kemerovo", "Khabarovsk", "Khakassia", "Khanty-Mansi", "Kirov", "Komi", "Kostroma", "Krasnodar", "Krasnoyarsk", "Kurgan", "Kursk", "Leningrad", "Lipetsk", "Magadan", "Mari El", "Mordovia", "Moscow", "Moscow Oblast", "Murmansk", "Nenets", "Nizhny Novgorod", "North Ossetia–Alania", "Novgorod", "Novosibirsk", "Omsk", "Orenburg", "Oryol", "Penza", "Perm", "Primorsky", "Pskov", "Rostov", "Ryazan", "Saint Petersburg", "Sakha", "Sakhalin", "Samara", "Saratov", "Smolensk", "Stavropol", "Sverdlovsk", "Tambov", "Tatarstan", "Tomsk", "Tula", "Tuva", "Tver", "Tyumen", "Udmurtia", "Ulyanovsk", "Vladimir", "Volgograd", "Vologda", "Voronezh", "Yamalo-Nenets", "Yaroslavl", "Zabaykalsky"
        ],
        "France": [
            "Auvergne-Rhône-Alpes", "Bourgogne-Franche-Comté", "Brittany", "Centre-Val de Loire", "Corsica", "Grand Est", "Hauts-de-France", "Île-de-France", "Normandy", "Nouvelle-Aquitaine", "Occitanie", "Pays de la Loire", "Provence-Alpes-Côte d'Azur"
        ],
        "Italy": [
            "Abruzzo", "Aosta Valley", "Apulia", "Basilicata", "Calabria", "Campania", "Emilia-Romagna", "Friuli Venezia Giulia", "Lazio", "Liguria", "Lombardy", "Marche", "Molise", "Piedmont", "Sardinia", "Sicily", "Trentino-Alto Adige", "Tuscany", "Umbria", "Veneto"
        ],
        "Spain": [
            "Andalusia", "Aragon", "Asturias", "Balearic Islands", "Basque Country", "Canary Islands", "Cantabria", "Castile and León", "Castilla-La Mancha", "Catalonia", "Extremadura", "Galicia", "La Rioja", "Madrid", "Murcia", "Navarre", "Valencian Community", "Ceuta", "Melilla"
        ],
        "Japan": [
            "Aichi", "Akita", "Aomori", "Chiba", "Ehime", "Fukui", "Fukuoka", "Fukushima", "Gifu", "Gunma", "Hiroshima", "Hokkaido", "Hyogo", "Ibaraki", "Ishikawa", "Iwate", "Kagawa", "Kagoshima", "Kanagawa", "Kochi", "Kumamoto", "Kyoto", "Mie", "Miyagi", "Miyazaki", "Nagano", "Nagasaki", "Nara", "Niigata", "Oita", "Okayama", "Okinawa", "Osaka", "Saga", "Saitama", "Shiga", "Shimane", "Shizuoka", "Tochigi", "Tokushima", "Tokyo", "Tottori", "Toyama", "Wakayama", "Yamagata", "Yamaguchi", "Yamanashi"
        ]
    };

    // For countries not in the map, show a generic "Other" option
    function getStatesForCountry(country) {
        if(countryStateMap[country]) {
            return countryStateMap[country];
        } else {
            return ["Other"];
        }
    }

    function populateStates(selectedCountry, selectedState) {
        var $state = $('#state');
        $state.empty();
        $state.append('<option value="" disabled>Select a State</option>');
        var states = getStatesForCountry(selectedCountry);
        for(var i=0; i<states.length; i++) {
            var state = states[i];
            var selected = (selectedState && selectedState === state) ? 'selected' : '';
            $state.append('<option value="'+state+'" '+selected+'>'+state+'</option>');
        }
        // If no state selected, select the first option
        if(!selectedState) {
            $state.prop('selectedIndex', 0);
        }
    }

    $(document).ready(function() {
        // On page load, populate states if country is already selected (for update)
        var selectedCountry = "<?php echo isset($getClient->client_country) ? addslashes($getClient->client_country) : ''; ?>";
        var selectedState = "<?php echo isset($getClient->client_state) ? addslashes($getClient->client_state) : ''; ?>";
        if(selectedCountry) {
            populateStates(selectedCountry, selectedState);
        }

        // On country change, update states
        $('#country').on('change', function() {
            var country = $(this).val();
            populateStates(country, '');
        });
    });
</script>
                    
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
							&nbsp;&nbsp;<a class="btn btn-default icon-btn" href="<?php echo base_url('clients');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
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
	$('#department,#country,#state').select2();	 // Autosuggest list
</script>
<!-- Organizatoin form validation -->
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->