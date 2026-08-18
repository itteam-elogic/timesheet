<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader');

  $getUpdateId = $this->uri->segment('3'); // Update Segment
  if (empty($getUpdateId) && !empty($this->input->post('project_id'))) {
      $getUpdateId = $this->input->post('project_id');
  }
  
  $getClientNames = $this->client_model->getClientName(); // List of Clients

  $loginManagerName =  $this->session->userdata['logged_in_timesheet']['empId'];  //session user name.

  $selectedProjectManagerEmpId = $loginManagerName;
  $storedProjectManagerName = '';
  if (!empty($getUpdateId) && !empty($updateProject)) {
      foreach ($updateProject as $projectRow) {
          $storedProjectManagerName = isset($projectRow->p_manager) ? trim($projectRow->p_manager) : '';
          if (!empty($projectRow->empId)) {
              $selectedProjectManagerEmpId = $projectRow->empId;
          } elseif (!empty($projectRow->who_allocated_project_empId)) {
              $selectedProjectManagerEmpId = $projectRow->who_allocated_project_empId;
          }
      }
  }
  	
?>

<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1>Project Master Report</h1>
		</div>
	</div>
	<?php if(empty($getUpdateId)): ?>
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div>
					<h4 class="line-head">Add Project</h4>
					<span style="float:right; position:relative; top:-50px;">
					<a class="btn btn-info btn-flat" data-toggle="tooltip" title="Back To Projects" href="<?php echo base_url('projects'); ?>">
							<i class="fa fa-arrow-left"></i> Back to Projects
						</a>
					</span>
				</div>
				<div style="clear:both;"></div>
				<form class="form-horizontal" method="post" name="add_project_info" id="add_project_info" action="<?php echo base_url('projects/addproject');?>">
					<div class="form-group">
						<label class="control-label col-md-2"> Client Name : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="client_Id" name="client_Id">
								<option value="">Please select client</option>
									<?php foreach($getClientNames as $key => $clientName):
										$hideeLogicGeneral = str_replace("eLogic Solutions", "",$clientName->client_name);
										//echo '<pre>'; print_r($hideeLogicGeneral);
										if('eLogic Solutions'.$hideeLogicGeneral != $clientName->client_name){
										?>
									<option value="<?php echo $clientName->client_Id;?>"><?php echo ucfirst($clientName->client_name);?></option>
									<?php } ?>
								<?php endforeach; ?>
							</select>
						</div>
						<label class="control-label col-md-2">Project Number : </label>
						<div class="col-md-3">
						<?php if($loginManagerName == '421'): ?>	
						<input class="form-control" type="text" name="project_number" id="project_number" value=""><?php echo form_error('project_number'); ?>
						<?php else: ?>	
					<input class="form-control" type="text" readonly name="project_number" id="project_number" value="<?php echo $this->project_model->addProjectNumber();?>"><?php echo form_error('project_number'); ?> 
						<?php endif; ?>	
						</div>
						<!--<div class="col-md-3"><span class="required-star"><b>( System Generating Project Number Dynamically )</b></span>
            			</div>-->
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Project Name : <span class="required-star">*</span></label>
						<div class="col-md-3">
						<input class="form-control" type="text" name="project_name" id="project_name" placeholder="Enter Project Name" value="<?php echo set_value('project_name'); ?>"><?php echo form_error('project_name'); ?>		
						</div>

						<label class="control-label col-md-2">City : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="city" id="city" placeholder="Enter City" value="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">State : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="state" name="state">
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

						<label class="control-label col-md-2">Country : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="country" name="country">
								<option value="" selected disabled>Select a country</option>
								<option value="Afghanistan">Afghanistan</option>
								<option value="Albania">Albania</option>
								<option value="Algeria">Algeria</option>
								<option value="Andorra">Andorra</option>
								<option value="Angola">Angola</option>
								<option value="Antigua and Barbuda">Antigua and Barbuda</option>
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
								<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
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
								<option value="Eswatini (fmr. " Swaziland")">Eswatini (fmr. "Swaziland")</option>
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
						<label class="control-label col-md-2">Project Client Code : </label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="pc_code" id="pc_code" placeholder="Enter Project Client Code" value="">
						</div>
						<label class="control-label col-md-2">Project Manager : <span class="required-star">*</span></label>
						<div class="col-md-3">
							
							<select class="form-control" id="p_manager" name="p_manager">
								<option value="" selected disabled>Please Choose Project Manager</option>
								<?php foreach($this->project_model->getManagers() as $managers): ?>
									<option value="<?php echo $managers->name; ?>" <?php if($managers->empId == $loginManagerName): echo 'selected'; endif;?>><?php echo $managers->name; ?></option>
								<?php endforeach; ?>
							</select>

						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Start Date : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="project_start_date" id="project_start_date" readonly="" placeholder="Enter Project Start Date" value="<?php echo set_value('project_start_date'); ?>">
						</div>
						<label class="control-label col-md-2">End Date : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="project_end_date" id="project_end_date" readonly="" placeholder="Enter Project End Date" value="<?php echo set_value('project_end_date'); ?>">
							<?php echo form_error('project_end_date'); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Man Days : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="man_days" name="man_days">
								<option value="" selected disabled>Please choose Man Days</option>
								<option value="hourly">Hourly</option>
								<option value="monthly">Monthly</option>
								<option value="annually">Annually</option>
							</select>
						</div>
						<label class="control-label col-md-2">Estimated Hours : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="estimated_hours" id="estimated_hours" placeholder="Enter Estimated Hours" value="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Notification on Completion of hours : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="notif_hours_choice" name="notif_hours_choice">
								<option value="" selected disabled>Please choose Notification on Completion of hours</option>
								<option value="30">30</option>
								<option value="50">50</option>
								<option value="80">80</option>
								<option value="100">100</option>
								<option value="other">Other</option>
							</select>
							<input class="form-control" type="number" name="notif_hours_custom" id="notif_hours_custom" min="1" max="1000" placeholder="Enter hours (1-1000)" value="" style="display:none; margin-top:8px;">
							<input type="hidden" name="notif_hours" id="notif_hours" value="">
						</div>
						<label class="control-label col-md-2">Team Members : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="team_members" name="team_members[]" multiple>
								<option value="" disabled>Please Choose Team Members</option>
								<?php foreach($this->project_model->teamMembers() as $Mteam): ?>
									<option value="<?php echo $Mteam->name;?>"><?php echo $Mteam->name;?></option>
								<?php endforeach; ?>	
							</select>
							<label id="team_members-error" class="error" for="team_members" style="display: none;"></label>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Project Type : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="project_type" name="project_type">
								<!-- <option value="" selected disabled>Choose Services</option>
								<option value="BIM">BIM</option>
								<option value="Architectural">Architectural</option>
								<option value="Structural">Structural</option>
								<option value="MEP">MEP</option>
								<option value="3D Visualization">3D Visualization</option>
								<option value="Standardization">Standardization</option>
								<option value="Steel Detailing">Steel Detailing</option>
								<option value="Product Modeling">Product Modeling</option>
								<option value="Downtime">Down Time</option>
								<option value="Other Services">Other Services</option> -->
								<option value="Architectural">Architectural</option>
								<option value="Structural">Structural</option>
								<option value="MEP">MEP</option>
								<option value="3D Visualization">3D Visualization</option>
								<option value="2D Auto CAD">2D Auto CAD</option>								
							</select>
						</div>

						
					<div class="form-group">
						<label class="control-label col-md-2">Project Status : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="status" name="status">
								<option value="" selected disabled>Please select status</option>
								<option value="Process">In Process</option>
                                <option value="Process">On Hold</option>
                                <option value="Process">Billing Complete</option>
								<!-- <option value="Pending">Pending</option> -->
								<option value="Closed">Closed</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Resource Billability : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="resource_billability" name="resource_billability">
								<option value="" selected disabled>Please select Billability</option>
								<option value="Billable">Billable</option>
								<option value="Non_billable">Non-Billable</option>
							</select>
						</div>
						<label class="control-label col-md-2">Link to the Project on the Server : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input type="text" class="form-control" placeholder="Enter link to the Project" id="link_to_project" name="link_to_project" value=""/>
						</div>
						
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Construction Technology : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="construction_technology" name="construction_technology">
								<option value="" selected disabled>Please choose Construction Technology</option>
								<option value="WD">Wood</option>
								<option value="STL">Steel</option>
								<option value="CON">Concrete</option>
								<option value="CMP">Composite</option>
								<option value="MSN">Masonry</option>
								<option value="NA">Not Applicable</option>
								<!--<option value="AHS">Wood</option>--->
							</select>
						</div>
						<label class="control-label col-md-2">Building Type : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="building_typology" name="building_typology">
								<option value="" selected disabled>Please choose Building Typology</option>
								<option value="COM">Commercial</option>
								<option value="RES">Residential</option>
								<option value="HSC">Historic Conservation</option>
								<option value="HOS">Hospitality</option>
								<option value="EDU">Educational</option>
								<option value="REL">Religious</option>
								<option value="HGR">High Rise</option>
								<option value="PUB">Public Buildings</option>
								<option value="IDR">Industrial</option>
								<option value="HCF">Health Care Facility</option>
								<option value="INF">Infrastructure</option>
								<option value="MIL">Military</option>
								<option value="TEL">Telecommunication</option>
								<option value="Senior Living">Senior Living</option>
								<option value="Restaurants">Restaurants</option>
								<option value="Dormitories">Dormitories</option>
							</select>
						</div>
					</div>


					<div class="form-group">
						<label class="control-label col-md-2">Scope Category : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="scope_category" name="scope_category">
								<option value="" selected disabled>Please choose scope category</option>
								<option value="BLC">Pre Design / Blue Line conversion / As Builts</option>
								<option value="SC">Schematics / Illustrations</option>
								<option value="DD">Design Development</option>
								<option value="CDP">Construction Doc Partial</option>
								<option value="CDC">Construction Doc Complete</option>
								<option value="BP">Building Permit</option>
								<option value="RD">Record Drawings</option>
								<option value="3D">3D Renderings</option>
								<option value="AD">Architectural Design and development (Dom Projects)</option>
								<option value="DDS">DD+SC</option>
								<option value="CDPS">DD+CDP</option>
								<option value="CDCS">DD+CDC</option>
								<option value="BPS">DD+CDC+BP</option>
								<option value="RDS">RD+BP+CDC+DD</option>
								<option value="3D+CD">CD set partial + 3D visualization</option>
								<option value="CM">AEC Component Modeling</option>
								<option value="CS">CAD Standards</option>
								<option value="DL">Detail Library</option>
								<option value="FM">Facility Management</option>
								<option value="MEP">MEP drawings</option>
								<option value="MW">Mill Work</option>
								<option value="SD">Structural drawings</option>
								<option value="SHD">Shop Drawings</option>
								<option value="STC">Standards Conversion</option>
								<option value="TDR">Technical Design Review</option>
								<option value="URD">Urban Design</option>
								<option value="DOC">Process Documentation</option>
								<option value="ZD">Zoning Drawings</option>
								<option value="LE">Lease Exhibits</option>
								<option value="CIV">Civil Drawings</option>
								<option value="LE+ZD">Lease + Zoning</option>
								<option value="RM">Revit Modeling</option>
								<option value="MD">Measure Drawing</option>
								<option value="EXE">Site Execution</option>
								<option value="4DA">Construction Animation</option>
								<option value="ARM">ArchiCAD Modelling</option>
								<option value="CORD">Coordination</option>
								<option value="Existing condition">Existing condition</option>
								
							</select>
						</div>
						<label class="control-label col-md-2">Technology Category : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="technology_category" name="technology_category">
								<option value="" selected disabled>Please choose Technology Category</option>
								<option value="3DM">3ds Viz, 3ds Max</option>
								<option value="AC">AutoCAD</option>
								<option value="ACA">AutoCAD Architecture (ADT)</option>
								<option value="ACB">AutoCAD Building Systems</option>
								<option value="ACE">AutoCAD Electrical</option>
								<option value="ACM">AutoCAD Mechanical</option>
								<option value="AR">Archicad</option>
								<option value="IN">Inventor</option>
								<option value="MS">Microstation</option>
								<option value="PS">Photoshop</option>
								<option value="RA">Revit Architecture</option>
								<option value="RM">Revit MEP</option>
								<option value="RS">Revit Structural</option>
								<option value="SKP">Sketch-up</option>
								<option value="AC-3DM">CAD and 3DS Max modeling</option>
								<option value="RA-3DM">Revit Modeling, Max Renders</option>
								<option value="PS-3DM">Photoshop editing and 3DS Max</option>
								<option value="R-ALL">Revit All Disciplines (Arch, Str and MEP)</option>
								<option value="AIL">Adobe Illustrator</option>
                                <option value="RISA software">RISA software</option>
							</select>
						</div>
					</div>


					<div class="form-group">
					<label class="control-label col-md-2">Total building Area (Sft.) : </label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="total_building_area" id="total_building_area" placeholder="Enter Total building Area" value="">
						</div>	
										
						<label class="control-label col-md-2">Total Site Area (Sft.) : </label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="total_site_area" id="total_site_area" placeholder="Enter Total Site Area (Sft.)" value="">
						</div>				
						
					</div>
					<div class="form-group">
					<label class="control-label col-md-2">Project Description : </label>
						<div class="col-md-3">
							<textarea class="form-control" name="project_desc" id="project_desc" placeholder="Enter Project Description" rows="2"><?php echo set_value('project_desc'); ?></textarea>
						</div>
					</div>


					<div class="form-group">
						<div class="row col-md-10 mb-10">
							<div class="col-md-3">
								<h4 class="control-label">Primary Project Contact Info : </h4>
							</div>
						</div>
						<div class="row mb-20 col-md-11">
							<div class="col-md-2"></div>
							<div class="col-md-3">
								<label>Contact Name : <span class="required-star">*</span></label>
								<input class="form-control col-md-8" type="text" name="project_contact_name" id="project_contact_name" placeholder="Enter project contact name" value="" onblur="getContactDetails(this.value)">
							</div>
							<div class="col-md-3">
								<label>Email Id : <span class="required-star">*</span></label>
								<input class="form-control col-md-8" type="text" name="project_email_id" id="project_email_id" placeholder="Enter Project contact email id" value="">
							</div>
							<div class="col-md-3">
								<label>Contact Number : <span class="required-star">*</span></label>
								<input class="form-control col-md-8" type="text" name="project_contact_number" id="project_contact_number" placeholder="Enter Project contact number" value="">
							</div>
						</div>
					</div>			
					<div class="card-footer">
						<div class="row">
							<div class="col-md-12 col-md-offset-5">
								<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Create</button>
								<a class="btn btn-default icon-btn" href="<?php echo base_url('projects');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
							</div>
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
				 	<h4 class="line-head">Update Project</h4>
				 	<span style="float:right; position:relative; top:-50px;">
					<a class="btn btn-info btn-flat" data-toggle="tooltip" title="Back To Projects" href="<?php echo base_url('projects'); ?>">
							<i class="fa fa-arrow-left"></i> Back to Projects
						</a>
				 </div>
				 <div style="clear:both;"></div>
				<?php foreach($updateProject as $key => $getProjectData) { 	 }   ?>
				<form class="form-horizontal" method="post" name="add_project_info" id="add_project_info" action="<?php echo base_url('projects/updateproject');?>">
					<input type="hidden" id="project_id" name="project_id" value="<?php echo $getProjectData->project_Id; ?>" /> 
					<div class="form-group">
						<label class="control-label col-md-2"> Client Name : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="client_Id" name="client_Id">
								<option value="">Please select client</option>
									<?php foreach($getClientNames as $key => $clientName): 
										$hideeLogicGeneral = str_replace("eLogic Solutions", "",$clientName->client_name);
										//echo '<pre>'; print_r($hideeLogicGeneral);
										if('eLogic Solutions'.$hideeLogicGeneral != $clientName->client_name){
										?>
									<option value="<?php echo $clientName->client_Id;?>" <?php if($getProjectData->client_Id == $clientName->client_Id) echo 'selected="selected"'; ?>><?php echo ucfirst($clientName->client_name);?></option>
									<?php } ?>
								<?php endforeach; ?>
							</select>
						</div>
						<label class="control-label col-md-2">Project Number : </label>
						<div class="col-md-3">
						<input class="form-control" type="text" readonly name="project_number" id="project_number" value="<?php echo $getProjectData->project_number;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Project Name : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="project_name" id="project_name" placeholder="Enter Project Name" value="<?php echo $getProjectData->project_name;?>">
						</div>

						<label class="control-label col-md-2">City : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="city" id="city" placeholder="Enter City" value="<?php echo $getProjectData->city;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">State : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="state" name="state">
								<option value="" selected disabled>Select a State</option>
								<option value="Alabama" <?php if($getProjectData->state == 'Alabama') echo 'selected="selected"'; ?>>Alabama</option>
								<option value="Alaska" <?php if($getProjectData->state == 'Alaska') echo 'selected="selected"'; ?>>Alaska</option>
								<option value="Arizona" <?php if($getProjectData->state == 'Arizona') echo 'selected="selected"'; ?>>Arizona</option>
								<option value="Arkansas" <?php if($getProjectData->state == 'Arkansas') echo 'selected="selected"'; ?>>Arkansas</option>
								<option value="California" <?php if($getProjectData->state == 'California') echo 'selected="selected"'; ?>>California</option>
								<option value="Colorado" <?php if($getProjectData->state == 'Colorado') echo 'selected="selected"'; ?>>Colorado</option>
								<option value="Connecticut" <?php if($getProjectData->state == 'Connecticut') echo 'selected="selected"'; ?>>Connecticut</option>
								<option value="Delaware" <?php if($getProjectData->state == 'Delaware') echo 'selected="selected"'; ?>>Delaware</option>
								<option value="District Of Columbia" <?php if($getProjectData->state == 'District Of Columbia') echo 'selected="selected"';?>>District Of Columbia</option>
								<option value="Florida" <?php if($getProjectData->state == 'Florida') echo 'selected="selected"';?>>Florida</option>
								<option value="Georgia" <?php if($getProjectData->state == 'Georgia') echo 'selected="selected"';?>>Georgia</option>
								<option value="Hawaii" <?php if($getProjectData->state == 'Hawaii') echo 'selected="selected"';?>>Hawaii</option>
								<option value="Idaho" <?php if($getProjectData->state == 'Idaho') echo 'selected="selected"';?>>Idaho</option>
								<option value="Illinois" <?php if($getProjectData->state == 'Illinois') echo 'selected="selected"';?>>Illinois</option>
								<option value="Indiana" <?php if($getProjectData->state == 'Indiana') echo 'selected="selected"';?>>Indiana</option>
								<option value="Iowa" <?php if($getProjectData->state == 'Iowa') echo 'selected="selected"';?>>Iowa</option>
								<option value="Kansas" <?php if($getProjectData->state == 'Kansas') echo 'selected="selected"';?>>Kansas</option>
								<option value="Kentucky" <?php if($getProjectData->state == 'Kentucky') echo 'selected="selected"';?>>Kentucky</option>
								<option value="Louisiana" <?php if($getProjectData->state == 'Maine') echo 'selected="selected"';?>>Louisiana</option>
								<option value="Maine" <?php if($getProjectData->state == 'District Of Columbia') echo 'selected="selected"';?>>Maine</option>
								<option value="Maryland" <?php if($getProjectData->state == 'Maryland') echo 'selected="selected"';?>>Maryland</option>
								<option value="Massachusetts" <?php if($getProjectData->state == 'Massachusetts') echo 'selected="selected"';?>>Massachusetts</option>
								<option value="Michigan" <?php if($getProjectData->state == 'Michigan') echo 'selected="selected"';?>>Michigan</option>
								<option value="Minnesota" <?php if($getProjectData->state == 'Minnesota') echo 'selected="selected"';?>>Minnesota</option>
								<option value="Mississippi" <?php if($getProjectData->state == 'Mississippi') echo 'selected="selected"';?>>Mississippi</option>
								<option value="Missouri" <?php if($getProjectData->state == 'Missouri') echo 'selected="selected"';?>>Missouri</option>
								<option value="Montana" <?php if($getProjectData->state == 'Montana') echo 'selected="selected"';?>>Montana</option>
								<option value="Nebraska" <?php if($getProjectData->state == 'Nebraska') echo 'selected="selected"';?>>Nebraska</option>
								<option value="Nevada" <?php if($getProjectData->state == 'Nevada') echo 'selected="selected"';?>>Nevada</option>
								<option value="New Hampshire" <?php if($getProjectData->state == 'New Hampshire') echo 'selected="selected"';?>>New Hampshire</option>
								<option value="New Jersey" <?php if($getProjectData->state == 'New Jersey') echo 'selected="selected"';?>>New Jersey</option>
								<option value="New Mexico" <?php if($getProjectData->state == 'New Mexico') echo 'selected="selected"';?>>New Mexico</option>
								<option value="New York" <?php if($getProjectData->state == 'New York') echo 'selected="selected"';?>>New York</option>
								<option value="North Carolina" <?php if($getProjectData->state == 'North Carolina') echo 'selected="selected"';?>>North Carolina</option>
								<option value="North Dakota" <?php if($getProjectData->state == 'North Dakota') echo 'selected="selected"';?>>North Dakota</option>
								<option value="Ohio" <?php if($getProjectData->state == 'Ohio') echo 'selected="selected"';?>>Ohio</option>
								<option value="Oklahoma" <?php if($getProjectData->state == 'Oklahoma') echo 'selected="selected"';?>>Oklahoma</option>
								<option value="Oregon" <?php if($getProjectData->state == 'Oregon') echo 'selected="selected"';?>>Oregon</option>
								<option value="Pennsylvania" <?php if($getProjectData->state == 'Pennsylvania') echo 'selected="selected"';?>>Pennsylvania</option>
								<option value="Rhode Island" <?php if($getProjectData->state == 'Rhode Island') echo 'selected="selected"';?>>Rhode Island</option>
								<option value="South Carolina" <?php if($getProjectData->state == 'South Carolina') echo 'selected="selected"';?>>South Carolina</option>
								<option value="South Dakota" <?php if($getProjectData->state == 'South Dakota') echo 'selected="selected"';?>>South Dakota</option>
								<option value="Tennessee" <?php if($getProjectData->state == 'Tennessee') echo 'selected="selected"';?>>Tennessee</option>
								<option value="Texas" <?php if($getProjectData->state == 'Texas') echo 'selected="selected"';?>>Texas</option>
								<option value="Utah" <?php if($getProjectData->state == 'Utah') echo 'selected="selected"';?>>Utah</option>
								<option value="Vermont" <?php if($getProjectData->state == 'Vermont') echo 'selected="selected"';?>>Vermont</option>
								<option value="Virginia" <?php if($getProjectData->state == 'Virginia') echo 'selected="selected"';?>>Virginia</option>
								<option value="Washington" <?php if($getProjectData->state == 'Washington') echo 'selected="selected"';?>>Washington</option>
								<option value="West Virginia" <?php if($getProjectData->state == 'West Virginia') echo 'selected="selected"';?>>West Virginia</option>
								<option value="Wisconsin" <?php if($getProjectData->state == 'Wisconsin') echo 'selected="selected"';?>>Wisconsin</option>
								<option value="Wyoming" <?php if($getProjectData->state == 'Wyoming') echo 'selected="selected"';?>>Wyoming</option>
								<option value="Badakhshan" <?php if($getProjectData->state == 'Badakhshan') echo 'selected="selected"';?>>Badakhshan</option>
								<option value="Badghis" <?php if($getProjectData->state == 'Badghis') echo 'selected="selected"';?>>Badghis</option>
								<option value="Baghlan" <?php if($getProjectData->state == 'Baghlan') echo 'selected="selected"';?>>Baghlan</option>
								<option value="Balkh" <?php if($getProjectData->state == 'Balkh') echo 'selected="selected"';?>>Balkh</option>
								<option value="Bamyan" <?php if($getProjectData->state == 'Bamyan') echo 'selected="selected"';?>>Bamyan</option>
								<option value="Daykundi" <?php if($getProjectData->state == 'Daykundi') echo 'selected="selected"';?>>Daykundi</option>
								<option value="Farah" <?php if($getProjectData->state == 'Farah') echo 'selected="selected"';?>>Farah</option>
								<option value="Faryab" <?php if($getProjectData->state == 'Faryab') echo 'selected="selected"';?>>Faryab</option>
								<option value="Ghaziabad" <?php if($getProjectData->state == 'Ghaziabad') echo 'selected="selected"';?>>Ghaziabad</option>
								<option value="Ghazni" <?php if($getProjectData->state == 'Ghazni') echo 'selected="selected"';?>>Ghazni</option>
								<option value="Helmand" <?php if($getProjectData->state == 'Helmand') echo 'selected="selected"';?>>Helmand</option>
								<option value="Herat" <?php if($getProjectData->state == 'Herat') echo 'selected="selected"';?>>Herat</option>
								<option value="Jowzjan" <?php if($getProjectData->state == 'Jowzjan') echo 'selected="selected"';?>>Jowzjan</option>
								<option value="Kabul" <?php if($getProjectData->state == 'Kabul') echo 'selected="selected"';?>>Kabul</option>
								<option value="Kandahar" <?php if($getProjectData->state == 'Herat') echo 'selected="selected"';?>>Kandahar</option>
								<option value="Kapisa" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Kapisa</option>
								<option value="Khost" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Khost</option>
								<option value="Kunar" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Kunar</option>
								<option value="Kunduz" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Kunduz</option>
								<option value="Laghman" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Laghman</option>
								<option value="Logar" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Logar</option>
								<option value="Nangarhar" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Nangarhar</option>
								<option value="Nimroz" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Nimroz</option>
								<option value="Nuristan" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Nuristan</option>
								<option value="Paktia" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Paktia</option>
								<option value="Paktika" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Paktika</option>
								<option value="Panjshir" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Panjshir</option>
								<option value="Parwan" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Parwan</option>
								<option value="Samangan" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Samangan</option>
								<option value="SarePol" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Sar-e Pol</option>
								<option value="Takhar" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Takhar</option>
								<option value="Urozgan" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Urozgan</option>
								<option value="Wardak" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Wardak</option>
								<option value="Zabul" <?php if($getProjectData->state == 'Kapisa') echo 'selected="selected"';?>>Zabul</option>
								<option value="New South Wales" <?php if($getProjectData->state == 'New South Wales') echo 'selected="selected"';?>>New South Wales</option>
								<option value="Victoria" <?php if($getProjectData->state == 'Victoria') echo 'selected="selected"';?>>Victoria</option>
								<option value="Queensland" <?php if($getProjectData->state == 'Queensland') echo 'selected="selected"';?>>Queensland</option>
								<option value="South Australia" <?php if($getProjectData->state == 'South Australia') echo 'selected="selected"';?>>South Australia</option>
								<option value="Western Australia" <?php if($getProjectData->state == 'Western Australia') echo 'selected="selected"';?>>Western Australia</option>
								<option value="Tasmania" <?php if($getProjectData->state == 'Tasmania') echo 'selected="selected"';?>>Tasmania</option>
								<option value="Australian Capital Territory" <?php if($getProjectData->state == 'Australian Capital Territory') echo 'selected="selected"';?>>Australian Capital Territory</option>
								<option value="Northern Territory" <?php if($getProjectData->state == 'Northern Territory') echo 'selected="selected"';?>>Northern Territory</option>
								<option value="Capital Governorate" <?php if($getProjectData->state == 'Capital Governorate') echo 'selected="selected"';?>>Capital Governorate</option>
								<option value="Muharraq Governorate" <?php if($getProjectData->state == 'Muharraq Governorate') echo 'selected="selected"';?>>Muharraq Governorate</option>
								<option value="Northern Governorate" <?php if($getProjectData->state == 'Northern Governorate') echo 'selected="selected"';?>>Northern Governorate</option>
								<option value="Southern Governorate" <?php if($getProjectData->state == 'Southern Governorate') echo 'selected="selected"';?>>Southern Governorate</option>
								<option value="Central Governorate" <?php if($getProjectData->state == 'Central Governorate') echo 'selected="selected"';?>>Central Governorate</option>
								<option value="Alberta" <?php if($getProjectData->state == 'Alberta') echo 'selected="selected"';?>>Alberta</option>
								<option value="British Columbia" <?php if($getProjectData->state == 'British Columbia') echo 'selected="selected"';?>>British Columbia</option>
								<option value="Manitoba" <?php if($getProjectData->state == 'Manitoba') echo 'selected="selected"';?>>Manitoba</option>
								<option value="New Brunswick" <?php if($getProjectData->state == 'New Brunswick') echo 'selected="selected"';?>>New Brunswick</option>
								<option value="Newfoundland and Labrador" <?php if($getProjectData->state == 'Newfoundland and Labrador') echo 'selected="selected"';?>>Newfoundland and Labrador</option>
								<option value="Northwest Territories" <?php if($getProjectData->state == 'Northwest Territories') echo 'selected="selected"';?>>Northwest Territories</option>
								<option value="Nova Scotia" <?php if($getProjectData->state == 'Nova Scotia') echo 'selected="selected"';?>>Nova Scotia</option>
								<option value="Nunavut" <?php if($getProjectData->state == 'Nunavut') echo 'selected="selected"';?>>Nunavut</option>
								<option value="Ontario" <?php if($getProjectData->state == 'Ontario') echo 'selected="selected"';?>>Ontario</option>
								<option value="Prince Edward Island" <?php if($getProjectData->state == 'Prince Edward Island') echo 'selected="selected"';?>>Prince Edward Island</option>
								<option value="Quebec" <?php if($getProjectData->state == 'Quebec') echo 'selected="selected"';?>>Quebec</option>
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
								<option value="Dubai">Dubai</option>
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

						<label class="control-label col-md-2">Country : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="country" name="country">
								<option value="" selected disabled>Select a country</option>
								<option value="Afghanistan">Afghanistan</option>
								<option value="Albania">Albania</option>
								<option value="Algeria">Algeria</option>
								<option value="Andorra">Andorra</option>
								<option value="Angola">Angola</option>
								<option value="Antigua and Barbuda">Antigua and Barbuda</option>
								<option value="Argentina">Argentina</option>
								<option value="Armenia">Armenia</option>
								<option value="Australia" <?php if($getProjectData->country == 'Australia') echo 'selected="selected"';?>>Australia</option>
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
								<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
								<option value="Botswana">Botswana</option>
								<option value="Brazil" <?php if($getProjectData->country == 'Brazil') echo 'selected="selected"';?>>Brazil</option>
								<option value="Brunei">Brunei</option>
								<option value="Bulgaria">Bulgaria</option>
								<option value="Burkina Faso">Burkina Faso</option>
								<option value="Burundi">Burundi</option>
								<option value="Cabo Verde">Cabo Verde</option>
								<option value="Cambodia">Cambodia</option>
								<option value="Cameroon">Cameroon</option>
								<option value="Canada" <?php if($getProjectData->country == 'Canada') echo 'selected="selected"';?>>Canada</option>
								<option value="Central African Republic">Central African Republic</option>
								<option value="Chad">Chad</option>
								<option value="Chile">Chile</option>
								<option value="China" <?php if($getProjectData->country == 'China') echo 'selected="selected"';?>>China</option>
								<option value="Colombia">Colombia</option>
								<option value="Comoros">Comoros</option>
								<option value="Costa Rica">Costa Rica</option>
								<option value="Croatia">Croatia</option>
								<option value="Cuba">Cuba</option>
								<option value="Cyprus">Cyprus</option>
								<option value="Denmark" <?php if($getProjectData->country == 'Denmark') echo 'selected="selected"';?>>Denmark</option>
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
								<option value="Eswatini (fmr. " Swaziland")">Eswatini (fmr. "Swaziland")</option>
								<option value="Ethiopia">Ethiopia</option>
								<option value="Fiji">Fiji</option>
								<option value="Finland" <?php if($getProjectData->country == 'Finland') echo 'selected="selected"';?>>Finland</option>
								<option value="France">France</option>
								<option value="Gabon">Gabon</option>
								<option value="Gambia">Gambia</option>
								<option value="Georgia">Georgia</option>
								<option value="Germany" <?php if($getProjectData->country == 'Germany') echo 'selected="selected"';?>>Germany</option>
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
								<option value="India" <?php if($getProjectData->country == 'India') echo 'selected="selected"';?>>India</option>
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
								<option value="Qatar" <?php if($getProjectData->country == 'Qatar') echo 'selected="selected"';?>>Qatar</option>
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
								<option value="South Africa" <?php if($getProjectData->country == 'South Africa') echo 'selected="selected"';?>>South Africa</option>
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
								<option value="Turkey" <?php if($getProjectData->country == 'Turkey') echo 'selected="selected"';?>>Turkey</option>
								<option value="Turkmenistan">Turkmenistan</option>
								<option value="Tuvalu">Tuvalu</option>
								<option value="Uganda">Uganda</option>
								<option value="Ukraine">Ukraine</option>
								<option value="United Arab Emirates" <?php if($getProjectData->country == 'United Arab Emirates') echo 'selected="selected"';?>>United Arab Emirates</option>
								<option value="United Kingdom" <?php if($getProjectData->country == 'United Kingdom') echo 'selected="selected"';?>>United Kingdom</option>
								<option value="United States of America" <?php if($getProjectData->country == 'United States of America') echo 'selected="selected"';?>>United States of America</option>
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
						<label class="control-label col-md-2">Project Client Code : </label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="pc_code" id="pc_code" placeholder="Enter Project Client Code" value="<?php echo $getProjectData->pc_code;?>">
						</div>
						<label class="control-label col-md-2">Project Manager : <span class="required-star">*</span></label>
						<div class="col-md-3">
							
							<select class="form-control" id="p_manager" name="p_manager">
								<option value="" selected disabled>Please Choose Project Manager</option>
								<?php foreach($this->project_model->getManagers() as $managers): ?>
									<option value="<?php echo $managers->name; ?>" <?php
                                        if (!empty($storedProjectManagerName) && $managers->name == $storedProjectManagerName) {
                                            echo 'selected';
                                        } elseif ($managers->empId == $selectedProjectManagerEmpId) {
                                            echo 'selected';
                                        }
                                    ?>><?php echo $managers->name; ?></option>
								<?php endforeach; ?>
							</select>

						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Start Date : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="project_start_date" id="project_start_date" readonly="" placeholder="Enter Project Start Date" value="<?php echo set_value('project_start_date', $getProjectData->project_start_date); ?>">
						</div>
						<label class="control-label col-md-2">End Date : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="project_end_date" id="project_end_date" readonly="" placeholder="Enter Project End Date" value="<?php echo set_value('project_end_date', $getProjectData->project_end_date); ?>">
							<?php echo form_error('project_end_date'); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Man Days : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="man_days" name="man_days">
								<option value="" selected disabled>Please choose Man Days</option>
								<option value="hourly" <?php if($getProjectData->man_days == 'hourly') echo 'selected="selected"'; ?>>Hourly</option>
								<option value="monthly" <?php if($getProjectData->man_days == 'monthly') echo 'selected="selected"'; ?>>Monthly</option>
								<option value="annually" <?php if($getProjectData->man_days == 'annually') echo 'selected="selected"'; ?>>Annually</option>
							</select>
						</div>
						<label class="control-label col-md-2">Estimated Hours : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="estimated_hours" id="estimated_hours" placeholder="Enter Estimated Hours" value="<?php echo $getProjectData->estimated_hours;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Notification on Completion of hours : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<?php
								$notifHoursVal = isset($getProjectData->notif_hours) ? (string)$getProjectData->notif_hours : '';
								$notifPresets = array('30', '50', '80', '100');
								$notifIsPreset = in_array($notifHoursVal, $notifPresets, true);
								$notifIsOther = ($notifHoursVal !== '' && !$notifIsPreset);
							?>
							<select class="form-control" id="notif_hours_choice" name="notif_hours_choice">
								<option value="" disabled <?php if($notifHoursVal === '') echo 'selected="selected"'; ?>>Please choose Notification on Completion of hours</option>
								<option value="30" <?php if($notifHoursVal === '30') echo 'selected="selected"'; ?>>30</option>
								<option value="50" <?php if($notifHoursVal === '50') echo 'selected="selected"'; ?>>50</option>
								<option value="80" <?php if($notifHoursVal === '80') echo 'selected="selected"'; ?>>80</option>
								<option value="100" <?php if($notifHoursVal === '100') echo 'selected="selected"'; ?>>100</option>
								<option value="other" <?php if($notifIsOther) echo 'selected="selected"'; ?>>Other</option>
							</select>
							<input class="form-control" type="number" name="notif_hours_custom" id="notif_hours_custom" min="1" max="1000" placeholder="Enter hours (1-1000)" value="<?php echo $notifIsOther ? $notifHoursVal : ''; ?>" style="<?php echo $notifIsOther ? 'margin-top:8px;' : 'display:none; margin-top:8px;'; ?>">
							<input type="hidden" name="notif_hours" id="notif_hours" value="<?php echo $notifHoursVal; ?>">
						</div>
						<label class="control-label col-md-2">Team Members : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="team_members_update" name="team_members[]" multiple>
								<option value="" disabled>Please Choose Team Members</option>
								<?php foreach($this->project_model->teamMembers() as $Mteam): ?>
									<option value="<?php echo $Mteam->name;?>" <?php if(strpos($getProjectData->team_members, $Mteam->name) !== false) echo 'selected="selected"'; ?>><?php echo $Mteam->name;?></option>
								<?php endforeach; ?>	
							</select>
							<label id="team_members_update-error" class="error" for="team_members_update" style="display: none;"></label>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Project Type : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="project_type" name="project_type">
								<option value="" selected disabled>Choose Services</option>
								<option value="Architectural" <?php if($getProjectData->project_type == 'Architectural') echo 'selected="selected"'; ?>>Architectural</option>
								<option value="Structural" <?php if($getProjectData->project_type == 'Structural') echo 'selected="selected"'; ?>>Structural</option>
								<option value="MEP" <?php if($getProjectData->project_type == 'MEP') echo 'selected="selected"'; ?>>MEP</option>
								<option value="3D Visualization" <?php if($getProjectData->project_type == '3D Visualization') echo 'selected="selected"'; ?>>3D Visualization</option>
								<option value="2D Auto CAD" <?php if($getProjectData->project_type == '2D Auto CAD') echo 'selected="selected"'; ?>>2D Auto CAD</option>
								
							</select>
						</div>

						
					<div class="form-group">
						<label class="control-label col-md-2">Project Status : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="status" name="status">
								<option value="" selected disabled>Please select status</option>
								<option value="Process" <?php if($getProjectData->status == 'Process') echo 'selected="selected"'; ?>>In Process</option>
                                <option value="Process" <?php if($getProjectData->status == 'On Hold') echo 'selected="selected"'; ?>>On Hold</option>
                                <option value="Process" <?php if($getProjectData->status == 'Billing Complete') echo 'selected="selected"'; ?>>Billing Complete</option>
								<!-- <option value="Pending" <?php if($getProjectData->status == 'Pending') echo 'selected="selected"'; ?>>Pending</option> -->
								<option value="Closed" <?php if($getProjectData->status == 'Closed') echo 'selected="selected"'; ?>>Closed</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Resource Billability : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="resource_billability" name="resource_billability">
								<option value="" selected disabled>Please select Billability</option>
								<option value="Billable" <?php if($getProjectData->resource_billability == 'Billable') echo 'selected="selected"'; ?>>Billable</option>
								<option value="Non_billable" <?php if($getProjectData->resource_billability == 'Non_billable') echo 'selected="selected"'; ?>>Non-Billable</option>
							</select>
						</div>
						<label class="control-label col-md-2">Total Site Area (Sft.) : </label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="total_site_area" id="total_site_area" placeholder="Enter Total Site Area (Sft.)" value="<?php echo $getProjectData->total_site_area;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Construction Technology : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="construction_technology" name="construction_technology">
								<option value="" selected disabled>Please choose Construction Technology</option>
								<option value="WD" <?php if($getProjectData->construction_technology == 'WD') echo 'selected="selected"'; ?>>Wood</option>
								<option value="STL" <?php if($getProjectData->construction_technology == 'STL') echo 'selected="selected"'; ?>>Steel</option>
								<option value="CON" <?php if($getProjectData->construction_technology == 'CON') echo 'selected="selected"'; ?>>Concrete</option>
								<option value="CMP" <?php if($getProjectData->construction_technology == 'CMP') echo 'selected="selected"'; ?>>Composite</option>
								<option value="MSN" <?php if($getProjectData->construction_technology == 'MSN') echo 'selected="selected"'; ?>>Masonry</option>
								<option value="NA" <?php if($getProjectData->construction_technology == 'NA') echo 'selected="selected"'; ?>>Not Applicable</option>
								<!--<option value="AHS">Wood</option>--->
							</select>
						</div>
						<label class="control-label col-md-2">Building Type : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="building_typology" name="building_typology">
								<option value="" selected disabled>Please choose Building Typology</option>
								<option value="COM" <?php if($getProjectData->building_typology == 'COM') echo 'selected="selected"'; ?>>Commercial</option>
								<option value="RES" <?php if($getProjectData->building_typology == 'RES') echo 'selected="selected"'; ?>>Residential</option>
								<option value="HSC" <?php if($getProjectData->building_typology == 'HSC') echo 'selected="selected"'; ?>>Historic Conservation</option>
								<option value="HOS" <?php if($getProjectData->building_typology == 'HOS') echo 'selected="selected"'; ?>>Hospitality</option>
								<option value="EDU" <?php if($getProjectData->building_typology == 'EDU') echo 'selected="selected"'; ?>>Educational</option>
								<option value="REL" <?php if($getProjectData->building_typology == 'REL') echo 'selected="selected"'; ?>>Religious</option>
								<option value="HGR" <?php if($getProjectData->building_typology == 'HGR') echo 'selected="selected"'; ?>>High Rise</option>
								<option value="PUB" <?php if($getProjectData->building_typology == 'PUB') echo 'selected="selected"'; ?>>Public Buildings</option>
								<option value="IDR" <?php if($getProjectData->building_typology == 'IDR') echo 'selected="selected"'; ?>>Industrial</option>
								<option value="HCF" <?php if($getProjectData->building_typology == 'HCF') echo 'selected="selected"'; ?>>Health Care Facility</option>
								<option value="INF" <?php if($getProjectData->building_typology == 'INF') echo 'selected="selected"'; ?>>Infrastructure</option>
								<option value="MIL" <?php if($getProjectData->building_typology == 'MIL') echo 'selected="selected"'; ?>>Military</option>
								<option value="TEL" <?php if($getProjectData->building_typology == 'TEL') echo 'selected="selected"'; ?>>Telecommunication</option>								
								<option value="Senior Living" <?php if($getProjectData->building_typology == 'Senior Living') echo 'selected="selected"'; ?>>Senior Living</option>
								<option value="Restaurants" <?php if($getProjectData->building_typology == 'Restaurants') echo 'selected="selected"'; ?>>Restaurants</option>
								<option value="Dormitories" <?php if($getProjectData->building_typology == 'Dormitories') echo 'selected="selected"'; ?>>Dormitories</option>
							</select>
						</div>
					</div>


					<div class="form-group">
						<label class="control-label col-md-2">Scope Category : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="scope_category" name="scope_category">
								<option value="" selected disabled>Please choose scope category</option>
								<option value="BLC" <?php if($getProjectData->scope_category == 'BLC') echo 'selected="selected"'; ?>>Pre Design / Blue Line conversion / As Builts</option>
								<option value="BLC" <?php if($getProjectData->scope_category == 'BLC') echo 'selected="selected"'; ?>>Pre Design / Blue Line conversion / As Builts</option>
								<option value="SC" <?php if($getProjectData->scope_category == 'SC') echo 'selected="selected"'; ?>>Schematics / Illustrations</option>
								<option value="DD" <?php if($getProjectData->scope_category == 'DD') echo 'selected="selected"'; ?>>Design Development</option>
								<option value="CDP" <?php if($getProjectData->scope_category == 'CDP') echo 'selected="selected"'; ?>>Construction Doc Partial</option>
								<option value="CDC" <?php if($getProjectData->scope_category == 'CDC') echo 'selected="selected"'; ?>>Construction Doc Complete</option>
								<option value="BP" <?php if($getProjectData->scope_category == 'BP') echo 'selected="selected"'; ?>>Building Permit</option>
								<option value="RD" <?php if($getProjectData->scope_category == 'RD') echo 'selected="selected"'; ?>>Record Drawings</option>
								<option value="3D" <?php if($getProjectData->scope_category == '3D') echo 'selected="selected"'; ?>>3D Renderings</option>
								<option value="AD" <?php if($getProjectData->scope_category == 'AD') echo 'selected="selected"'; ?>>Architectural Design and development (Dom Projects)</option>
								<option value="DDS" <?php if($getProjectData->scope_category == 'DDS') echo 'selected="selected"'; ?>>DD+SC</option>
								<option value="CDPS" <?php if($getProjectData->scope_category == 'CDPS') echo 'selected="selected"'; ?>>DD+CDP</option>
								<option value="CDCS" <?php if($getProjectData->scope_category == 'CDCS') echo 'selected="selected"'; ?>>DD+CDC</option>
								<option value="BPS" <?php if($getProjectData->scope_category == 'BPS') echo 'selected="selected"'; ?>>DD+CDC+BP</option>
								<option value="RDS" <?php if($getProjectData->scope_category == 'RDS') echo 'selected="selected"'; ?>>RD+BP+CDC+DD</option>
								<option value="3D+CD" <?php if($getProjectData->scope_category == '3D+CD') echo 'selected="selected"'; ?>>CD set partial + 3D visualization</option>
								<option value="CM" <?php if($getProjectData->scope_category == 'CM') echo 'selected="selected"'; ?>>AEC Component Modeling</option>
								<option value="CS" <?php if($getProjectData->scope_category == 'CS') echo 'selected="selected"'; ?>>CAD Standards</option>
								<option value="DL" <?php if($getProjectData->scope_category == 'DL') echo 'selected="selected"'; ?>>Detail Library</option>
								<option value="FM" <?php if($getProjectData->scope_category == 'FM') echo 'selected="selected"'; ?>>Facility Management</option>
								<option value="MEP" <?php if($getProjectData->scope_category == 'MEP') echo 'selected="selected"'; ?>>MEP drawings</option>
								<option value="MW" <?php if($getProjectData->scope_category == 'MW') echo 'selected="selected"'; ?>>Mill Work</option>
								<option value="SD" <?php if($getProjectData->scope_category == 'SD') echo 'selected="selected"'; ?>>Structural drawings</option>
								<option value="SHD" <?php if($getProjectData->scope_category == 'SHD') echo 'selected="selected"'; ?>>Shop Drawings</option>
								<option value="STC" <?php if($getProjectData->scope_category == 'STC') echo 'selected="selected"'; ?>>Standards Conversion</option>
								<option value="TDR" <?php if($getProjectData->scope_category == 'TDR') echo 'selected="selected"'; ?>>Technical Design Review</option>
								<option value="URD" <?php if($getProjectData->scope_category == 'URD') echo 'selected="selected"'; ?>>Urban Design</option>
								<option value="DOC" <?php if($getProjectData->scope_category == 'DOC') echo 'selected="selected"'; ?>>Process Documentation</option>
								<option value="ZD" <?php if($getProjectData->scope_category == 'ZD') echo 'selected="selected"'; ?>>Zoning Drawings</option>
								<option value="LE" <?php if($getProjectData->scope_category == 'LE') echo 'selected="selected"'; ?>>Lease Exhibits</option>
								<option value="CIV" <?php if($getProjectData->scope_category == 'CIV') echo 'selected="selected"'; ?>>Civil Drawings</option>
								<option value="LE+ZD" <?php if($getProjectData->scope_category == 'LE+ZD') echo 'selected="selected"'; ?>>Lease + Zoning</option>
								<option value="RM" <?php if($getProjectData->scope_category == 'RM') echo 'selected="selected"'; ?>>Revit Modeling</option>
								<option value="MD" <?php if($getProjectData->scope_category == 'MD') echo 'selected="selected"'; ?>>Measure Drawing</option>
								<option value="EXE" <?php if($getProjectData->scope_category == 'EXE') echo 'selected="selected"'; ?>>Site Execution</option>
								<option value="4DA" <?php if($getProjectData->scope_category == '4DA') echo 'selected="selected"'; ?>>Construction Animation</option>
								<option value="ARM" <?php if($getProjectData->scope_category == 'ARM') echo 'selected="selected"'; ?>>ArchiCAD Modelling</option>
								<option value="CORD" <?php if($getProjectData->scope_category == 'CORD') echo 'selected="selected"'; ?>>Coordination</option>
								<option value="Existing condition" <?php if($getProjectData->scope_category == 'Existing condition') echo 'selected="selected"'; ?>>Coordination</option>
							</select>
						</div>
						<label class="control-label col-md-2">Technology Category : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<select class="form-control" id="technology_category" name="technology_category">
								<option value="" selected disabled>Please choose Technology Category</option>
								<option value="3DM" <?php if($getProjectData->technology_category == '3DM') echo 'selected="selected"'; ?>>3ds Viz, 3ds Max</option>
								<option value="AC" <?php if($getProjectData->technology_category == 'AC') echo 'selected="selected"'; ?>>AutoCAD</option>
								<option value="ACA" <?php if($getProjectData->technology_category == 'ACA') echo 'selected="selected"'; ?>>AutoCAD Architecture (ADT)</option>
								<option value="ACB" <?php if($getProjectData->technology_category == 'ACB') echo 'selected="selected"'; ?>>AutoCAD Building Systems</option>
								<option value="ACE" <?php if($getProjectData->technology_category == 'ACE') echo 'selected="selected"'; ?>>AutoCAD Electrical</option>
								<option value="ACM" <?php if($getProjectData->technology_category == 'ACM') echo 'selected="selected"'; ?>>AutoCAD Mechanical</option>
								<option value="AR" <?php if($getProjectData->technology_category == 'AR') echo 'selected="selected"'; ?>>Archicad</option>
								<option value="IN" <?php if($getProjectData->technology_category == 'IN') echo 'selected="selected"'; ?>>Inventor</option>
								<option value="MS" <?php if($getProjectData->technology_category == 'MS') echo 'selected="selected"'; ?>>Microstation</option>
								<option value="PS" <?php if($getProjectData->technology_category == 'PS') echo 'selected="selected"'; ?>>Photoshop</option>
								<option value="RA" <?php if($getProjectData->technology_category == 'RA') echo 'selected="selected"'; ?>>Revit Architecture</option>
								<option value="RM" <?php if($getProjectData->technology_category == 'RM') echo 'selected="selected"'; ?>>Revit MEP</option>
								<option value="RS" <?php if($getProjectData->technology_category == 'RS') echo 'selected="selected"'; ?>>Revit Structural</option>
								<option value="SKP" <?php if($getProjectData->technology_category == 'SKP') echo 'selected="selected"'; ?>>Sketch-up</option>
								<option value="AC-3DM" <?php if($getProjectData->technology_category == 'AC-3DM') echo 'selected="selected"'; ?>>CAD and 3DS Max modeling</option>
								<option value="RA-3DM" <?php if($getProjectData->technology_category == 'RA-3DM') echo 'selected="selected"'; ?>>Revit Modeling, Max Renders</option>
								<option value="PS-3DM" <?php if($getProjectData->technology_category == 'PS-3DM') echo 'selected="selected"'; ?>>Photoshop editing and 3DS Max</option>
								<option value="R-ALL" <?php if($getProjectData->technology_category == 'R-ALL') echo 'selected="selected"'; ?>>Revit All Disciplines (Arch, Str and MEP)</option>
								<option value="AIL" <?php if($getProjectData->technology_category == 'AIL') echo 'selected="selected"'; ?>>Adobe Illustrator</option>
                                <option value="AIL" <?php if($getProjectData->technology_category == 'RISA software') echo 'selected="selected"'; ?>>RISA software</option>
							</select>
						</div>
					</div>


					<div class="form-group">
						<label class="control-label col-md-2">Project Description : </label>
						<div class="col-md-3">
							<textarea class="form-control" name="project_desc" id="project_desc" placeholder="Enter Project Description" rows="2"><?php echo $getProjectData->project_desc; ?></textarea>
						</div>

						<label class="control-label col-md-2">Link to the Project on the Server : <span class="required-star">*</span></label>
						<div class="col-md-3">
							<input type="text" class="form-control" placeholder="Enter link to the Project" id="link_to_project" name="link_to_project" value="<?php echo $getProjectData->link_to_project; ?>"/>
						</div>
					</div>


					<div class="form-group">
						<div class="row col-md-10 mb-10">
							<div class="col-md-3">
								<h4 class="control-label">Primary Project Contact Info : </h4>
							</div>
						</div>
						<div class="row mb-20 col-md-11">
							<div class="col-md-2"></div>
							<div class="col-md-3">
								<label>Contact Name : <span class="required-star">*</span></label>
								<input class="form-control col-md-8" type="text" name="project_contact_name" id="project_contact_name" placeholder="Enter project contact name" value="<?php echo $getProjectData->project_contact_name; ?>">
							</div>
							<div class="col-md-3">
								<label>Email Id : <span class="required-star">*</span></label>
								<input class="form-control col-md-8" type="text" name="project_email_id" id="project_email_id" placeholder="Enter Project contact email id" value="<?php echo $getProjectData->project_email_id; ?>">
							</div>
							<div class="col-md-3">
								<label>Contact Number : <span class="required-star">*</span></label>
								<input class="form-control col-md-8" type="text" name="project_contact_number" id="project_contact_number" placeholder="Enter Project contact number" value="<?php echo $getProjectData->project_contact_number; ?>">
							</div>
						</div>
					</div>			
					<div class="card-footer">
						<div class="row">
							<div class="col-md-12 col-md-offset-5">
								<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update</button>
								<a class="btn btn-default icon-btn" href="<?php echo base_url('projects');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
							</div>
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
<style type="text/css">
	form[name='add_project_info'] label.error {
		color: #a94442 !important;
		font-weight: 600;
		display: block;
		margin-top: 4px;
	}
	form[name='add_project_info'] label.error[for="project_end_date"] {
		color: #a94442 !important;
	}
</style>
<script type="text/javascript" language="javascript">
	// Wait for the DOM to be ready
	$(function() {
		// Custom validator: at least one team member must be selected (for multi-select)
		$.validator.addMethod("teamMembersRequired", function(value, element) {
			var val = $(element).val();
			return val && (Array.isArray(val) ? val.length > 0 : (val + "").length > 0);
		}, "Please choose team member.");

		$.validator.addMethod("endDateAfterStart", function(value, element) {
			var start = $.trim($("#project_start_date").val() || "");
			var end = $.trim(value || "");
			if (!start || !end) {
				return true;
			}
			var startParts = start.split("-");
			var endParts = end.split("-");
			if (startParts.length !== 3 || endParts.length !== 3) {
				return false;
			}
			var startDate = new Date(parseInt(startParts[0], 10), parseInt(startParts[1], 10) - 1, parseInt(startParts[2], 10));
			var endDate = new Date(parseInt(endParts[0], 10), parseInt(endParts[1], 10) - 1, parseInt(endParts[2], 10));
			return endDate.getTime() > startDate.getTime();
		}, "End Date must be greater than Start Date. Record not entered.");

		function syncNotifHoursField() {
			var $choice = $("#notif_hours_choice");
			var $custom = $("#notif_hours_custom");
			var $hours = $("#notif_hours");
			var choice = $choice.val();
			if (choice === "other") {
				$custom.show();
				$hours.val($custom.val());
			} else if (choice) {
				$custom.hide().val("");
				$hours.val(choice);
			} else {
				$custom.hide().val("");
				$hours.val("");
			}
		}

		$("#notif_hours_choice").on("change", syncNotifHoursField);
		$("#notif_hours_custom").on("input", function() {
			$("#notif_hours").val($(this).val());
		});

		$("form[name='add_project_info']").validate({
			rules: {
				client_Id: {
					required: true
				},
				client_name: {
					required: true
				},
				project_name: {
					required: true
				},
				project_type: {
					required: true
				},
				status: {
					required: true
				},
				resource_billability: {
					required: true
				},
				man_days: {
					required: true
				},
				city: {
					required: true
				},
				state: {
					required: true
				},
				country: {
					required: true
				},
				contact_info: {
					required: true
				},
				p_manager: {
					required: true
				},
				estimated_hours: {
					required: true
				},
				notif_hours_choice: {
					required: true
				},
				notif_hours_custom: {
					required: function() {
						return $("#notif_hours_choice").val() === "other";
					},
					number: true,
					min: 1,
					max: 1000
				},
				construction_technology: {
					required: true
				},
				building_typology: {
					required: true
				},
				scope_category: {
					required: true
				},
				technology_category: {
					required: true
				},
				link_to_project: {
					required: true
				},
				project_end_date: {
					required: true,
					endDateAfterStart: true
				},
				project_start_date: {
					required: true
				},
				project_contact_name: {
					required: true
				},
				project_email_id: {
					required: true
				},
				project_contact_number: {
					required: true
				},
				"team_members[]": {
					teamMembersRequired: true
				}
			},
			messages: {
				client_Id: "Please choose client name",
				project_name: "Please enter project name",
				project_type: "Please choose project type",
				status: "Please choose project status",
				resource_billability: "Please choose project resource billability",
				man_days: "Please choose man days value",
				city: "Please enter city",
				state: "Please choose state",
				country: "Please choose country",
				contact_info: "Please enter primary project contact info",
				p_manager: "Please choose project manager",
				estimated_hours: "Please enter estimated hours",
				notif_hours_choice: "Please choose notification on completion of hours",
				notif_hours_custom: {
					required: "Please enter notification hours",
					number: "Please enter a valid number",
					min: "Please enter a value between 1 and 1000",
					max: "Please enter a value between 1 and 1000"
				},
				construction_technology: "Please choose construction technology",
				building_typology: "Please choose building typology",
				scope_category: "Please choose scope category",
				technology_category: "Please choose technology category",
				link_to_project: "Please enter link to the project on the server",
				project_end_date: "Please enter proejct end date",
				project_start_date: "Please enter project start date",
				project_contact_name: "Please enter project contact name",
				project_email_id: "Please enter project email id",
				project_contact_number: "Please enter project contact number",
				"team_members[]": "Please choose team member."
			},
			errorPlacement: function(error, element) {
				// Show team members error below the field (Select2 hides the original select, so place after parent)
				if (element.attr("name") === "team_members[]") {
					error.insertAfter(element.parent()).css({ "display": "block", "color": "#a94442" });
					return;
				}
				error.insertAfter(element);
				error.css({ "display": "block", "color": "#a94442" });
			},
			submitHandler: function(form) {
				syncNotifHoursField();
				form.submit();
			}
		});
	});

	$(document).ready(function() {
		function eriEndDateMinFromStart() {
			var startVal = $("#project_start_date").val();
			if (!startVal) {
				return null;
			}
			var parts = startVal.split("-");
			if (parts.length !== 3) {
				return null;
			}
			return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10) + 1);
		}

		var today = $("#project_start_date").val();
		$("#project_start_date, #project_end_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				if (this.id == 'project_start_date') {
					var rMin = eriEndDateMinFromStart();
					var dateMin = $('#project_start_date').datepicker("getDate");
					var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate() + 365);
					if (rMin) {
						$('#project_end_date').datepicker("option", "minDate", rMin);
					}
					$('#project_end_date').datepicker("option", "maxDate", rMax);
					$('#project_end_date').valid();
				}
				if (this.id == 'project_end_date') {
					$('#project_end_date').valid();
				}
			}
		});
		var initialMin = eriEndDateMinFromStart();
		if (initialMin) {
			$('#project_end_date').datepicker("option", "minDate", initialMin);
		}

		$('#project_start_date').on('change', function() {
			var rMin = eriEndDateMinFromStart();
			if (rMin) {
				$('#project_end_date').datepicker("option", "minDate", rMin);
			}
			$('#project_end_date').valid();
		});
	})
	$('#client_Id,#resource_billability,#status,#project_type,#team_members,#team_members_update,#project_type,#state,#country,#p_manager,#construction_technology,#building_typology,#man_days,#scope_category,#technology_category').select2(); // Autosuggest list on clients


	$(document).ready(function() {
		$('#resource_billability').on('change', function() {

			var demovalue = $(this).val();

			if (demovalue == 'Billable') {

				$("#showMandays").show();

			} else {

				$("div.myDiv").hide();
			}
		});
	});

	/**** Auto Suggest Organization *********/
	$(function() {
		$("#project_contact_name").autocomplete({
			source: "getClientContactInformaiton", // path to the get_birds method
		});

		$("#project_name").autocomplete({
			source: "getListOfProjects", // path to the get_birds method
		});
	});
	/**** Auto Suggest Organization *********/

	function getContactDetails(contactName){

		$.ajax({
			type: "POST",
			url: "<?php echo site_url('projects/getcontactSetionDetails');?>",
			data: 'project_contact_name=' + contactName,
			success: function(data) {

				var address = data;
				var arr = address.split('__');
				$("#project_email_id").val(arr[0]);
				$("#project_contact_number").val(arr[1]);
				
				
				
			}
		});
	}

	
	
</script>
<!-- Organizatoin form validation -->
<style>
	.mandaysradio {
		position: absolute;
		margin-top: 13px;
	}

	.ui-autocomplete {
		max-height: 250px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
	}

	/* IE 6 doesn't support max-height
	 * we use height instead, but this forces the menu to always be this tall
	 */

	* html .ui-autocomplete {
		height: 250px;
	}

	
</style>

<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->