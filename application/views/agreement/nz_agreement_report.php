<?php 

    foreach( $getServieAgreement as $key => $getServiceData){ 

        $agreement_date=date_create($getServiceData->agreement_date);
        $total_est_cost_amt = ($getServiceData->total_est_hours * $getServiceData->country_wise_rate);
        //$est_deliverable_dates = date_create($getServiceData->est_deliverable_dates);
        $gst_Amount = ( $total_est_cost_amt * 10 ) / 100 ;   
		$serviceType = $getServiceData->service_type; 
        
        if($getServiceData->est_deliverable_dates !=''):
                  
                $deliverable_date_with_text = date_format(date_create($getServiceData->est_deliverable_dates),"Y M d");
                  
                  else:
                  
                        $deliverable_date_with_text = $getServiceData->est_deliverable_text;
                  
                  endif;
        
        
        //Monthly service country wise code 

            $monthlyCountryWiseCode = $getServiceData->country_wise_code;

            $monthlyCWCode = explode(',', $monthlyCountryWiseCode);

            if(!empty($monthlyCWCode[0])){
                
                $countryC = $monthlyCWCode[0];
                
            }else{
                
                $countryC = 'NZD';
            }
        
        //Calculating the multiple price with saparate comma and calculation
        
        $monthlyCountryWisePrice = $getServiceData->est_cost;
        
        $monthlyCWPrice = explode(',', $monthlyCountryWisePrice);
        
        $monthlyCWtotalPrice =  array_sum($monthlyCWPrice); // 150
        
       
            //End of the country wise code 
        
        $monthlydesignatedName = $getServiceData->designated_consultants_name; // designation name
        
        $monthlydesignatedConsultName = explode(',', $monthlydesignatedName);
        
        $designated_start_date_service  = explode(',', $getServiceData->designated_start_date_service);
        
        $designated_end_date_service  = explode(',', $getServiceData->designated_end_date_service);
        
        $designated_desc_offered_services  = explode(',', $getServiceData->designated_desc_offered_services);
        
        //echo '<pre>'; print_r($monthlydesignatedConsult);
        
        
   } 
?>
<?php if($serviceType == 'hourly'): // Hourly Based Service Details ?>
<div class="container" style="border: #000 1px solid; margin-bottom: 2%; padding: 3%;">
	<div class="googoose-wrapper">
		<div class="row" align="center">
			<div class="googoose header">
				<div class="float-start googoose header" style="height:46px; margin:0 0; padding:0 0; position: relative; top: 0; bottom: 0; clear:both;">
					<img src="<?php echo HTTP_IMAGES_PATH; ?>sow-logo.jpg" width="20%" height="46px;" title="eLogicTech" class="res-media" />
				</div>
			</div>
			<div style="margin:0 0; padding:0 0; position: relative; top: 0; bottom: 0; clear:both;">
				<h3 style="text-align: center; margin:0 0; padding:0 0; clear:both;">SERVICES AGREEMENT</h3>
				<h4 style="text-align: right;">eL-SOW-<?=date_format($agreement_date,"y");?>-<?=$getServiceData->country_code;?><?=$getServiceData->project_code;?></h4>
				<h4 style="text-align: right;">Date: <?=date_format($agreement_date,"Y, M d");?></h4>
			</div>
		</div>

		<div class="row">
			<section title="Agreement">
				<div class="text-left">
					<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This Agreement is made and entered as of <?=date_format($agreement_date,"d F  Y");?>, between eLogicTech Solutions Inc(hereafter referred to as "Vendor"), a US company with Registered offices at "1312 17th Street Unit 2229, Denver, Colorodo, CA 80202" and <?=$getServiceData->client_name;?> (hereafter referred to as "Client"), a corporation with its primary place of business at "<?=$getServiceData->client_adress;?>" (each a "party" and collectively the "parties").</p>
					<p>In consideration of the foregoing and the agreements contained below, the parties agree as follows:</p>
				</div>
			</section>
			<section title="Scope of Services">
				<div>
					<p><b>SCOPE OF SERVICES</b></p>
					<p>This agreement is being made in response to the Client's request for further assistance with developing DD & CD set for <b>"<?=$getServiceData->project_name;?>"</b> Project.</p>
					<p><b>THE OVERALL SCOPE OF WORK COMPRISES.</b></p>
					<?=$getServiceData->scope_of_the_work;?>
				</div>
			</section>
			<section title="Deliverables">
				<div>
					<p><b>DELIVERABLES</b></p>
					<?=$getServiceData->deliverables;?>
				</div>
				<div>
					<p><b>PROVIDED BY CLIENT</b></p>
					<?=$getServiceData->provided_by_client_info;?>
				</div>
			</section>
			<section title="Contact Details">
				<p><b>Contact Details:</b></p>
				<div class="col-md-12">
					<div class="three-columns-grid">
						<div>&#x2022; &nbsp; &nbsp;Project Contact Details:</div>
						<p style="margin-left: 25%;">Contact Name: <?=$getServiceData->project_contact_name;?></p>
						<p style="margin-left: 25%;">Email id: <a href="#"><?=$getServiceData->project_email_id;?></a></p>
						<p style="margin-left: 25%;">Contact No: <?=$getServiceData->project_contact_number;?></p>
					</div>

					<div class="three-columns-grid">
						<div>&#x2022; &nbsp; &nbsp;Billing Contact Details:</div>
						<p style="margin-left: 25%;">Contact Name: <?=$getServiceData->billing_contact_name;?></p>
						<p style="margin-left: 25%;">Email id: <a href="#"><?=$getServiceData->billing_email_id;?></a></p>
						<p style="margin-left: 25%;">Contact No: <?=$getServiceData->billing_contact_number;?></p>
					</div>
				</div>
			</section>
		</div>

		<div class="row">
			<section title="Time and Cost">
				<p><b>TIME AND COST ESTIMATE FOR DEVELOPING FOR PROJECTS:</b></p>
				<ul>
					<li>Total Time Estimate: <?=$getServiceData->total_est_hours;?> hours</li>
					<!-- <li>GST : <?=$gst_Amount?></li>-->
					<?php if($getServiceData->sow_discount == '' || $getServiceData->sow_discount == '0'):?>
					<?php if($total_est_cost_amt == '0'):?>
					<li>Cost Estimate: <?=$getServiceData->total_est_hours;?> * $<?=$getServiceData->country_wise_rate;?>/hr </li>
					<?php else: ?>
					<li>Cost Estimate: <?=$getServiceData->total_est_hours;?> * $<?=$getServiceData->country_wise_rate;?>/hr = <?=number_format ($total_est_cost_amt,2);?> <?=$getServiceData->country_wise_code;?></li>
					<?php endif;?>


					<?php else: ?>
					<li>Discount Price : <?=$getServiceData->sow_discount;?> <?=$getServiceData->country_wise_code;?> </li>
					<li>After Discount Cost Estimation: <?=$getServiceData->total_est_hours;?> * $<?=$getServiceData->country_wise_rate;?>/hr = <?=number_format ($total_est_cost_amt-$getServiceData->sow_discount,2);?> <?=$getServiceData->country_wise_code;?></li> <!-- Discount -->
					<?php endif; ?>
					<li>Date and Time deadline: <?=$deliverable_date_with_text;?></li>
					<li>The deliverable date / time will change as per the change in scope.</li>
					<li>Any change in the above scope should be mutually discussed and agreed on email.</li>
					<li>Additional Services: Additional scope will be billed on a time-basis @ $<?=$getServiceData->country_wise_rate;?>/hr</li>
					<li>Any deliverable disputes need to be made within 7 working days of receiving the deliverable.</li>
					<?php if(!empty($getServiceData->est_remarks)): 
						$additional_text = str_replace(['<p>', '</p>'], '', $getServiceData->est_remarks);
					?>
					<li>Additional Information: <?=$additional_text;?></li>
					<!-- Additional Information -->
					<?php endif; ?>

				</ul>
			</section>
			<section title="Escalation Level">
				<p><b>ESCALATION LEVEL:</b></p>
				<p>The client can escalate their concern to eLogic Management Team</p>
				<p class="text-center" style="margin-left:-3.5%;">Contact Name: Rupali Modi</p>
				<p class="text-center">Email id: <a href="#">rupali@elogictech.com</a></p>
			</section>
			<section title="Payment Terms">
				<p><b>PAYMENT TERMS</b></p>
				<ul>
					<li>Invoice will be raised after completion of every 100 hours.</li>
					<li>All Invoices should be paid within 10 days of receiving the invoice.</li>
					<li>eLogic payment cannot be linked to the payment of the end client.</li>
					<li>Client shall pay the invoiced amounts by check or Wire Transfer.</li>
					<li>All checks are to be drawn in favor eLogicTech Solutions, Inc.</li>
					<li>All payment disputes if any should be made within 7 working days of receiving the invoice.</li>
					<li>If the payment on the invoice is not received 30 days after the invoice is due, a late payment fee of 1.5% will be accrued to the balance per month.</li>
				</ul>
			</section>
		</div>

		<div class="row">
			<section title="Bank Details">
				<p><b>For Wire Transfer:</b></p>
				<table class="table tableb sow_table">
					<tbody class="table-group-divider tableb">
						<tr>
							<td style="width:40%;">Company Name</td>
							<td>: ELOGIC SOLUTIONS INDIA PVT LTD</td>
						</tr>
						<tr>
							<td style="width:40%;">Accounts Number</td>
							<td>: 00428020000706</td>
						</tr>
						<tr>
							<td style="width:40%;">SWIFT Address</td>
							<td>: HDFCINBB</td>
						</tr>
						<tr>
							<td style="width:40%;">Type of Account</td>
							<td>: Current Account</td>
						</tr>
						<tr>
							<td style="width:40%;">Bank Name</td>
							<td>: HDFC Bank Ltd</td>
						</tr>
						<tr>
							<td style="width:40%;">Address</td>
							<td>: Ushakiran Complex, Paradise, Secunderabad, Telangana India</td>
						</tr>
					</tbody>
				</table>
			</section>
		</div>

		<div class="row">
			<section title="Terms and Conditions">
				<p><b>TERMS AND CONDITIONS</b></p>
				<ol>
					<li>Confidentiality: All CAD/Revit documents, project information, records and other correspondence information will be handled in a confidential manner. Neither party will share any of this information with any other company or individual without written authorization by the other party.</li>
					<li>Governing Law: This Agreement shall be governed by, interpreted, and applied in accordance with the laws of the country that the Client is incorporated in.</li>
					<li>All work generated as a part of this Agreement becomes proprietary rights belonging to the Client.</li>
					<li>Quality issues any will need to be notified to the Project Manager / Management within 7 working days of Delivery of the Project, failing which the work done will be consider to be of acceptable Quality.</li>
					<li>Term: This Agreement shall be effective for the entire duration of the project.</li>
				</ol>
				<p>The parties have caused their duly authorized representatives to execute this Agreement as of the date first set forth above.</p>
			</section>
		</div>
		<div class="row">
			<section title="Signature">
				<div class="col-md-12">
					<table class="table tableb sow_table" style="width:100%;">

						<tbody class="table-group-divider tableb">
							<tr style="width:100%;">
								<td style="width:50%;">___________________</td>
								<td style="width:50%;">___________________</td>
							</tr>
							<tr style="width:100%;">
								<?php if($getServiceData->sow_signature == 'rupali'): ?>
								<td style="width:50%;">Rupali Modi</td>
								<?php elseif($getServiceData->sow_signature == 'farhan'): ?>
								<td style="width:50%;">Syed Farhan</td>
								<?php elseif($getServiceData->sow_signature == 'chauhan'): ?>
								<td style="width:50%;">Pradip Chauhan</td>
								<?php elseif($getServiceData->sow_signature == 'rahul'): ?>
								<td style="width:50%;">Rahul Kumar</td>
								<?php elseif($getServiceData->sow_signature == 'uppala'): ?>
								<td style="width:50%;">Naresh Uppala</td>
								<?php else: ?>
								<td style="width:50%;">Rupali Modi</td>
								<?php endif; ?>
								<td><?=$getServiceData->billing_contact_name;?></td>
							</tr>
							<tr style="width:100%;">
								<?php if($getServiceData->sow_signature == 'rupali'): ?>
								<td style="width:50%;">CEO</td>
								<?php elseif($getServiceData->sow_signature == 'farhan'): ?>
								<td style="width:50%;">Business Head</td>
								<?php elseif($getServiceData->sow_signature == 'chauhan'): ?>
								<td style="width:50%;">Business Head</td>
								<?php elseif($getServiceData->sow_signature == 'rahul'): ?>
								<td style="width:50%;">Business Head</td>
								<?php elseif($getServiceData->sow_signature == 'uppala'): ?>
								<td style="width:50%;">Business Head</td>
								<?php else: ?>
								<td style="width:50%;">CEO</td>
								<?php endif; ?>
								<td><?=$getServiceData->billing_contact_designation;?></td>
							</tr>
							<tr style="width:100%;">
								<td style="width:50%;">eLogicTech Solutions Inc</td>
								<td style="width:50%;"><?=$getServiceData->client_name;?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</section>
		</div>

		<div class="row googoose footer">
			<hr />
			<section title="Address">
				<div class="text-center footer" style="font-size:12px;">
					<p>eLogicTech Solutions INC. VOIP: (415) 738 0642</p>
					<p>Development Center: Plot No 72, The May Flower, P & T Colony, Secunderabad &#8209; 500 009, Telangana State, India.</p>
					<p>Tel: +91-40-65212232 E-Mail: <a href="mailto:accounts@elogictech.com">accounts@elogictech.com</a> URL: <a href="https://www.elogictech.com/">www.elogictech.com</a></p>
					<!--<span class="googoose currentpage"></span>
					<span class="googoose totalpage"></span> -->
				</div>
			</section>
		</div>

	</div>
</div>
<?php else: // Monthly Based Service Details ?>

<div class="container" style="border: #000 1px solid; margin-bottom: 2%; padding: 3%;">
	<div class="googoose-wrapper">
		<div class="row" align="center">
			<section title="Header">
				<div class="googoose header">
					<div class="float-start googoose header" style="height:46px; margin:0 0; padding:0 0; position: relative; top: 0; bottom: 0; clear:both;">
						<img src="<?php echo HTTP_IMAGES_PATH; ?>sow-logo.jpg" width="20%" height="46px;" title="eLogicTech" class="res-media" />
					</div>
				</div>
			</section>
			<div style="margin:0 0; padding:0 0; position: relative; top: 0; bottom: 0; clear:both;">
				<h3 style="text-align: center; margin:0 0; padding:0 0; clear:both;">MONTHLY MODEL AGREEMENT</h3>
				<h4 style="text-align: right;">eL-SOW-<?=date_format($agreement_date,"y");?>-<?=$getServiceData->country_code;?><?=$getServiceData->project_code;?></h4>
				<h4 style="text-align: right;">Date: <?=date_format($agreement_date,"Y, M d");?></h4>
			</div>
		</div>

		<div class="row">
			<section title="Agreement">
				<div class="text-left">
					<p>This Agreement is made and entered as of <?=date_format($agreement_date,"M d,  Y");?> between eLogicTech Solutions (hereafter referred to as "Vendor"), a US company with Registered offices at 1312, 17th Street Unit 2229, Denver, Colorado, 80202, and <?=$getServiceData->client_name;?> (hereafter referred to as "Client"), a corporation with its primary place of business at <?=$getServiceData->client_adress;?> (each a "party" and collectively the "parties").</p>
					<p>In consideration of the foregoing and the agreements contained below, the parties agree as follows:</p>
				</div>
			</section>
		</div>
		<div class="row">
			<section title="Services">
				<div class="text-left">
					<ol>
						<li class="mb-3">
							<b>Services:</b> <?=$getServiceData->scope_of_the_work;?>
						</li>
						<li class="mb-3">
							<b>Payment:</b> The Client shall pay Vendor an amount of <b><?=$countryC;?> <?=number_format($monthlyCWtotalPrice,2);?></b> USD per month per each designated Consultant. This rate shall apply for a period of <b>three months</b> of the Agreement, from the Project Start date onwards however, Vendor may initiate one or more rate changes by giving Client a minimum one-month advance email notice of changes effective after the initial three months of this Agreement. <b>3D rendering services will be available as an add-on service at $30/hour within the monthly model contract.</b>
							<ul class="listup">
								<li>Rate changes must be agreed to by all parties, in writing. The rate increase may be initiated once a year and can increase upto 5%. <b>A rate change will not incur in the first 12 months of working together.</b></li>
							</ul>

						</li>

						<div class="row">
							A list of the designated Consultants and the Start Date for work by each is attached as Appendix
							<section title="Contact Details:">
								<div class="col-md-12">
									<section title="Contact Details">
										<p><b>List of Consulants:</b></p>
										<div class="col-md-12">
											<div class="three-columns-grid">
												<div>&#x2022; &nbsp; &nbsp;Project Contact Details:</div>
												<p style="margin-left: 25%;">Contact Name: <?=$getServiceData->project_contact_name;?></p>
												<p style="margin-left: 25%;">Email id: <a href="#"><?=$getServiceData->project_email_id;?></a></p>
												<p style="margin-left: 25%;">Contact No: <?=$getServiceData->project_contact_number;?></p>
											</div>

											<div class="three-columns-grid">
												<div>&#x2022; &nbsp; &nbsp;Billing Contact Details:</div>
												<p style="margin-left: 25%;">Contact Name: <?=$getServiceData->billing_contact_name;?></p>
												<p style="margin-left: 25%;">Email id: <a href="#"><?=$getServiceData->billing_email_id;?></a></p>
												<p style="margin-left: 25%;">Contact No: <?=$getServiceData->billing_contact_number;?></p>
											</div>
										</div>
									</section>
								</div>
							</section>
						</div>
						<li class="mb-3"><b class="text-uppercase">Payment Terms:</b>
							<ul class="float-start listlower">
								<li>Payment Terms &#8209; Invoice will be raised monthly basis at the end of each month.</li>
								<li>All Invoices should be paid within 10 days of receiving the invoice.</li>
								<li>eLogic payment cannot be linked to payment of end client.</li>
								<li>Monthly payment needs to be made whether the 180 hours are utilized or not.</li>
								<li>Unutilized hours cannot be carried forward to next month.</li>
								<li>All payment disputes if any should be made within 7 working days of receiving the Invoice.</li>
								<li>If payment of the invoice is not received 15 days after the invoice is due, Services will be suspended until full payment is received. The consultant will give 5 days prior notice to the Client account being overdue</li>
								<li>If the payment on the invoice is not received 30 days after the invoice is due, a late payment fee of 1.5% will be accrued to the balance per month</li>
								<li>Client shall pay the invoiced amount by check payable / wire transfers to eLogicTech Solutions Within 10 days of receiving the invoice</li>


								<div class="row">
									<section title="Bank Details">
										<p><b>For Wire Transfer:</b></p>
										<table class="table tableb sow_table">
											<tbody class="table-group-divider tableb">
												<tr>
													<td style="width:40%;">Company Name</td>
													<td>: ELOGIC SOLUTIONS INDIA PVT LTD</td>
												</tr>
												<tr>
													<td style="width:40%;">Accounts Number</td>
													<td>: 00428020000706</td>
												</tr>
												<tr>
													<td style="width:40%;">SWIFT Address</td>
													<td>: HDFCINBB</td>
												</tr>
												<tr>
													<td style="width:40%;">Type of Account</td>
													<td>: Current Account</td>
												</tr>
												<tr>
													<td style="width:40%;">Bank Name</td>
													<td>: HDFC Bank Ltd</td>
												</tr>
												<tr>
													<td style="width:40%;">Address</td>
													<td>: Ushakiran Complex, Paradise, Secunderabad, Telangana India</td>
												</tr>
											</tbody>
										</table>
									</section>
								</div>



							</ul>
						</li>
						<li class="mb-3">Changes to List of Consultants: Both parties agree to provide a minimum written notice of one month for deleting or adding Consultants. However, in the case where Vendor gives a notice to Client to replace a Consultant, Vendor will make all reasonable efforts to provide the Client with a suitable replacement.</li>
						<li class="mb-3">Confidentiality: All exchanges of information between both parties shall be confidential. Each party agrees that for a period of one year following the termination of this agreement, it will keep confidential all Confidential Information of the other disclosed to it in connection with this Agreement and will not disclose or otherwise use this Confidential Information.</li>
						<li class="mb-3">Term: This Agreement shall be effective for a minimum period of three months for each consultant and shall be automatically extended on a month-to-month basis so long as one or more consultants are performing work for the client and have not been deleted by the client pursuant to Clause#3 of this Agreement.</li>
						<li class="mb-3">Quality issues if any will need to be notified to the Management within 7 working days of delivery of the Project, failing which the work done will be considered to be of acceptable Quality.</li>

						<li class="mb-3">The client can escalate their concern to eLogicTech Management Team
							<p class="text-center" style="margin-left:-3.5% !important;">Contact Name: Rupali Modi</p>
							<p class="text-center">Email id: <a href="#">rupali@elogictech.com</a></p>
						</li>
						<li class="mb-3">Termination: This agreement may be terminated by giving a minimum written notice of one Month.</li>
						<li class="mb-3">The breaking fee does not apply if two more quality issues were reported by the client in accordance with Section 8 above.</li>
						<li class="mb-3">Governing Law: This Agreement shall be governed by, interpreted, and applied in accordance with the laws of the country that the Client is incorporated.</li>
						<li class="mb-3">If a consultant/employee leaves the vendor&#8217;s employment, the client agrees not to employ that person or to utilize directly or indirectly the services of that person&#8217;s subsequent employers for a period of five years from the time the person leaves the vendor&#8217;s employment.</li>
						<li class="mb-3">All work generated as a part of this agreement becomes proprietary rights belonging to the client.</li>
					</ol>
				</div>
			</section>
			<section>
				<div>
					<p>The parties have caused their duly authorized representatives to execute this agreement as of the date first set forth above.</p>
				</div>
			</section>
		</div>
		<div class="row mb-lg-3">
			<section title="Signature">
				<div class="col-md-12">
					<table class="table tableb sow_table" style="width:100%;">
						<thead class="text-left;">
							<tr style="width:100%;" class="tableb">
								<th style="width:55%; text-align: left;" scope="col">Client</th>
								<th style="width:45%; text-align: left;" scope="col">Vendor</th>
							</tr>
							<tr style="width:100%;" class="tableb">
								<th style="width:55%; text-align: left;" scope="col"><?=$getServiceData->client_name;?></th>
								<th style="width:45%; text-align: left;" scope="col">eLogicTech Solutions</th>
							</tr>

						</thead>


						<tbody class="table-group-divider tableb sow_table_stamp">
							<tr style="width:100%;">
								<td style="width:55%;">By</td>
								<td style="width:45%;">By</td>
							</tr>
							<tr style="width:100%;">
								<td style="width:50%;">Name: Amanda OMalley</td>
								<?php if($getServiceData->sow_signature == 'rupali'): ?>
								<td style="width:50%;">Name: Rupali Modi</td>
								<?php elseif($getServiceData->sow_signature == 'farhan'): ?>
								<td style="width:50%;">Name: Syed Farhan</td>
								<?php elseif($getServiceData->sow_signature == 'chauhan'): ?>
								<td style="width:50%;">Name: Pradip Chauhan</td>
								<?php elseif($getServiceData->sow_signature == 'rahul'): ?>
								<td style="width:50%;">Name: Rahul Kumar</td>
								<?php elseif($getServiceData->sow_signature == 'uppala'): ?>
								<td style="width:50%;">Name: Naresh Uppala</td>
								<?php else: ?>
								<td style="width:50%;">Name: Rupali Modi</td>
								<?php endif; ?>
							</tr>
							<tr style="width:100%;">
								<td style="width:50%;">Title: <?=$getServiceData->project_contact_designation;?></td>
								<?php if($getServiceData->sow_signature == 'rupali'): ?>
								<td style="width:50%;">Title: CEO</td>
								<?php elseif($getServiceData->sow_signature == 'farhan'): ?>
								<td style="width:50%;">Title: Business Head</td>
								<?php elseif($getServiceData->sow_signature == 'chauhan'): ?>
								<td style="width:50%;">Title: Business Head</td>
								<td style="width:50%;">Title: Business Head</td>
								<?php elseif($getServiceData->sow_signature == 'rahul'): ?>
								<td style="width:50%;">Title: Business Head</td>
								<?php elseif($getServiceData->sow_signature == 'uppala'): ?>
								<td style="width:50%;">Title: Business Head</td>
								<?php else: ?>
								<td style="width:50%;">Title: CEO</td>
								<?php endif; ?>
							</tr>
							<tr style="width:100%;">
								<td>Date: <?=$getServiceData->agreement_date;?></td>
								<td>Date: <?=$getServiceData->agreement_date;?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</section>
		</div>
		<div class="row">
			<section title="Consultants">
				<div class="col-md-12" style="margin-top:5em;">
					<p class="text-center text-uppercase"><b>Appendix A: List of Designated Consultants</b></p>
					<p class="text-uppercase">List of Designated Consultants:</p>
				</div>
				<div class="col-md-12">
					<table class="table tableb">
						<thead>
							<tr class="tableb">
								<th style="border:#000 solid 1px !important;" scope="col">#</th>
								<th style="border:#000 solid 1px !important;" scope="col">Name</th>
								<th style="border:#000 solid 1px !important;" scope="col">Start Date for Services</th>
								<th style="border:#000 solid 1px !important;" scope="col">End Date for Services</th>
								<th style="border:#000 solid 1px !important;" scope="col">Description of the offered Services</th>
							</tr>
						</thead>
						<tbody class="table-group-divider tableb">
							<?php for($dc=0; $dc < count($monthlydesignatedConsultName); $dc++): ?>
							<tr>
								<td style="border:#000 solid 1px !important;" scope="row"><?php echo $dc+1; ?></td>
								<td style="border:#000 solid 1px !important;"><?=$monthlydesignatedConsultName[$dc];?></td>
								<td style="border:#000 solid 1px !important;"><?=$designated_start_date_service[$dc];?></td>
								<td style="border:#000 solid 1px !important;"><?=$designated_end_date_service[$dc];?></td>
								<td style="border:#000 solid 1px !important;"><?=$designated_desc_offered_services[$dc];?></td>
							</tr>
							<?php endfor; ?>
						</tbody>
					</table>
				</div>
			</section>
			<p>Total Number of Designated Consultants: [<?php echo count($monthlydesignatedConsultName)?>]</p>
		</div>

		<div class="row googoose footer">
			<section title="Address">
				<hr>
				<div class="text-center footer" style="font-size:12px;">
					<p>eLogicTech Solutions Inc, 1312, 17th Street Unit 2229, Denver, Colorado, 80202, United States.</p>
					<p>Development Center: Plot No 72, The May Flower, P & T Colony, Secunderabad &#8209; 500 009, Telangana State, India.</p>
					<p>Tel: +91-40-65212232 E-Mail: <a href="mailto:accounts@elogictech.com">accounts@elogictech.com</a> URL: <a href="https://www.elogictech.com/">www.elogictech.com</a></p>
				</div>
			</section>
		</div>
	</div>
</div>
<?php endif;?>

<script>
	$(document).ready(function() {
		$("#printAgreement").click(function() {
			var mode = 'iframe'; //popup
			document.title = '';
			var close = mode == "popup";
			var options = {
				mode: mode,
				popClose: close
			};
			$("div#printableUSArea,#printableUSArea_M").printArea(options);
		});
	});


	function exportDoc() {

		var o = {

			filename: 'eL-SOW_<?=date_format($agreement_date,"y");?>_<?=$getServiceData->country_code;?><?=$getServiceData->project_code;?>.doc'

		};

		$(document).googoose(o);
	};



	function exportHTML() {
		var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
			"xmlns:w='urn:schemas-microsoft-com:office:word' " +
			"xmlns='http://www.w3.org/TR/REC-html40'>" +
			"<head><meta charset='utf-8'><title>eL-SOW-<?=date_format($agreement_date,"y");?>-<?=$getServiceData->country_code;?><?=$getServiceData->project_code;?></title></head><body>";
		var footer = "</body></html>";
		<?php if($serviceType == 'monthly'){ ?>
		var sourceHTML = header + document.getElementById("printableUSArea_M").innerHTML + footer;
		<?php }else { ?>
		var sourceHTML = header + document.getElementById("printableUSArea").innerHTML + footer;
		<?php }	?>
		var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
		var fileDownload = document.createElement("a");
		document.body.appendChild(fileDownload);
		fileDownload.href = source;
		fileDownload.download = 'eL-SOW-<?=date_format($agreement_date,"y");?>-<?=$getServiceData->country_code;?><?=$getServiceData->project_code;?>.doc';
		fileDownload.click();
		document.body.removeChild(fileDownload);
	}
</script>