<?php 

    foreach( $getServieAgreement as $key => $getServiceData){ 

        $agreement_date=date_create($getServiceData->agreement_date);
        $total_est_cost_amt = ($getServiceData->total_est_hours * $getServiceData->country_wise_rate);
        $est_deliverable_dates = date_create($getServiceData->est_deliverable_dates);
        $gst_Amount = ( $total_est_cost_amt * 10 ) / 100 ;   
		$serviceType = $getServiceData->service_type; 
   } 

?>
<?php if($serviceType == 'hourly'): // Hourly Based Service Details ?>
<div class="container" id="printableUSArea" style="border: #000 1px solid; margin-bottom: 2%; padding: 3%;">
	<header>
		<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;"><strong><u>SERVICES AGREEMENT</u></strong></p>
		<p style="margin-top:0cm;margin-right:0cm;margin-bottom:.0001pt;margin-left:21.6pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:right;"><b>eL-SOW-<?=date_format($agreement_date,"y");?>-<?=$getServiceData->country_code;?><?=$getServiceData->project_code;?></b></p>
		<p style="margin-top:0cm;margin-right:0cm;margin-bottom:.0001pt;margin-left:21.6pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:right;"><b>Date: <?=date_format($agreement_date,"Y, M d");?>&nbsp; &nbsp;</b></p>
		<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;margin-left:36.0pt;text-align:justify;">&nbsp;</p>
	</header>
	<section>
		<div class="row">
			<div class="col-md-12">
				<div class="pull-right" style="margin-top:2% ">
					<p>This Agreement is made and entered as of <?=date_format($agreement_date,"d F  Y");?>, between eLogicTech Solutions (hereafter referred to as "Vendor"), a US company with Registered offices at 1312 17th Street Unit 2229, Denver, Colorodo, CA 80202 and <?=$getServiceData->client_name;?> (hereafter referred to as "Client"), a corporation with its primary place of business at "<?=$getServiceData->client_adress;?>" (each a "party" and collectively the "parties").</p>
					<p>In consideration of the foregoing and the agreements contained below, the parties agree as follows:</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="pull-left">
					<h4><b>Scope of Services</b></h4>
					<p>This agreement is being made in response to the Client&apos;s request for further assistance with updating Revit Model and CD set as per the received red lines for "<b><?=$getServiceData->project_name;?></b>" Project.</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<p><b>The overall scope of work comprises</b></p>
				<?=$getServiceData->scope_of_the_work;?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<p style="text-decoration:underline; text-transform:uppercase;"><b>DELIVERABLES</b></p>
				<?=$getServiceData->deliverables;?>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<p style="text-transform:uppercase;"><b>PROVIDED BY CLIENT</b></p>
				<?=$getServiceData->provided_by_client_info;?>
			</div>
		</div>
	</section>
	<section>
		<div class="row">
			<div class="col-md-12">
				<div style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">
					<p><span style="font-family:&quot;Times New Roman&quot;,serif;font-size:16px;"><strong>Project Contact details:</strong><strong>&nbsp;</strong>&nbsp; &nbsp; &nbsp; &nbsp;Contact name: &nbsp; &nbsp; &nbsp; &nbsp; <?=$getServiceData->project_contact_name;?></span>
						<br><span style="font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Email Id: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?=$getServiceData->project_email_id;?></span>
						<br><span style="font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Contact No: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span style="color:black;"><?=$getServiceData->project_contact_number;?></span></span>
						<br>
						<br><span style="font-family:&quot;Times New Roman&quot;,serif;font-size:16px;"><strong>Billing Contact Details:&nbsp;</strong>&nbsp; &nbsp; &nbsp; &nbsp;Contact name: &nbsp; &nbsp; &nbsp; &nbsp;<?=$getServiceData->billing_contact_name;?></span>
						<br><span style="font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Email Id: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?=$getServiceData->billing_email_id;?></span>
						<br><span style="font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Contact No: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?=$getServiceData->billing_contact_number;?></span></p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<p><b>TIME AND COST ESTIMATE FOR DEVELOPING AUTOCAD DRAWINGS:</b></p>
				<ul>
					<li>Total Time Estimate: <?=$getServiceData->total_est_hours;?> hours</li>

					<!-- <li>GST : <?=$gst_Amount?></li>-->
					<?php if($getServiceData->sow_discount == '0'):?>
					<li>Cost Estimate: <?=$getServiceData->total_est_hours;?> * $<?=$getServiceData->country_wise_rate;?>/hr = <?=number_format ($total_est_cost_amt,2);?> <?=$getServiceData->country_wise_code;?></li>
					<?php else: ?>
					<li>Discount Price : <?=$getServiceData->sow_discount;?> <?=$getServiceData->country_wise_code;?> </li>
					<li>After Discount Cost Estimation: <?=$getServiceData->total_est_hours;?> * $<?=$getServiceData->country_wise_rate;?>/hr = <?=number_format ($total_est_cost_amt-$getServiceData->sow_discount,2);?> <?=$getServiceData->country_wise_code;?></li> <!-- Discount -->
					<?php endif; ?>
					<li>Date and Time deadline: <?=date_format($est_deliverable_dates,"Y, F d");?></li>
					<li>The deliverable date / time will change as per the change in scope.</li>
					<li>Any change in the above scope should be mutually discussed and agreed on email.</li>
					<li>Additional Services: Additional scope will be billed on a time-basis @ $<?=$getServiceData->country_wise_rate;?>/hr</li>
					<li>Any deliverable disputes need to be made within 7 working days of receiving the deliverable.</li>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">
					<h2 style="margin:0cm;margin-bottom:.0001pt;font-size:13px;font-family:&quot;Arial&quot;,sans-serif;font-style:italic;"><span style="font-size:16px;font-family:&quot;Times New Roman&quot;,serif;font-style:normal;"><strong>ESCALATION LEVEL:</strong></span></h2>
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;text-indent:28.8pt;">The client can escalate their concern to eLogic Management Team &nbsp;</p>
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;margin-left:144.0pt;text-align:justify;text-indent:36.0pt;">Contact name: Rupali Modi &nbsp;</p>
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;margin-left:144.0pt;text-align:justify;text-indent:36.0pt;">Email id: &nbsp;
						<a href="mailto:rupali@elogictech.com">rupali@elogictech.com</a>
					</p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<p style="text-transform:uppercase;"><b>PAYMENT TERMS</b></p>
				<?=$getServiceData->est_remarks;?>
				<ul style="margin-top:-10px;">
					<li>Client shall pay invoiced amounts by check or Wire Transfer.</li>
					<li>All checks to be drawn in favor eLogicTech Solutions, Inc.</li>
					<li>All payment disputes if any should to be made within 7 working days of receiving the invoice.</li>
					<li>Checks shall be sent by mail to the address mentioned below:</li>

					<ul style="list-style: none; padding-top: 1%;">
						<li>eLogicTech Solutions Inc</li>
						<li>1312 17th Street Unit 2229,</li>
						<li>Denver, CO 80202</li>
					</ul>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<p style="text-decoration: underline; margin-left: 3.8%;"><b>If you need to wire transfer the amount directly, please note our bank details as follows:</b></p>
				<ul style="list-style: none;">
					<li>Company Name: <b>eLogicTech Solutions Inc</b></li>
					<li>Accounts Number: <b>139105298756</b></li>
					<li>Routing Transit / ABA Number: <b>California 026009593(wires) 123103716(Checks)</b></li>
					<li>CHIPS Address: <b>0959</b></li>
					<li>SWIFT Address: <b>BOFAUS3N</b></li>
				</ul>
			</div>
		</div>

	</section>
	<section>
		<div class="row">
			<div class="col-md-12">
				<div style="margin:2% 0 12% 0;">
					<p style="text-transform:uppercase;"><b>Terms and Conditions</b></p>
					<ul>
						<li>Confidentiality: All CAD/Revit documents, project information, records and other correspondence information will be handled in a confidential manner. Neither party will share any of this information with any other company or individual without written authorization by the other party.</li>
						<li>Governing Law: This Agreement shall be governed by, inter­preted, and applied in accordance with the laws of the country that the Client is incorporated in.</li>
						<li>All work generated as a part of this Agreement becomes proprietary rights belonging to the Client.</li>
						<li>Quality issues any will need to be notified to the Project Manager / Management within 7 working days of Delivery of the Project, failing which the work done will be consider to be of acceptable Quality.</li>
						<li>Term: This Agreement shall be effective for the entire duration of the project.</li>
					</ul>
					<p>The parties have caused their duly authorized representatives to execute this Agreement as of the date first set forth above.</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<p style="margin-top:0cm;margin-right:0cm;margin-bottom:12.0pt;margin-left:0cm;text-indent:0cm;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;">
					<br>
				</p>
				<p style="margin-top:0cm;margin-right:0cm;margin-bottom:12.0pt;margin-left:0cm;text-indent:0cm;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;">
					<br>
				</p>
				<p style="margin-top:0cm;margin-right:0cm;margin-bottom:12.0pt;margin-left:0cm;text-indent:0cm;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;">_______________________ &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;____________________</p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;">Rupali Modi &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?=$getServiceData->billing_contact_name;?></p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;">CEO &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?=$getServiceData->billing_contact_designation;?>&nbsp;</p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;">eLogicTech Solutions Inc &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?=$getServiceData->client_name;?></p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;">
					<br>
				</p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;">
					<br>
					<br>
					<br>
				</p>
			</div>
		</div>
	</section>
	<footer>
		<div class="row">
			<div class="col-md-12">
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:right;">&nbsp;</p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp;</p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp;</p>
				<div style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;border:none;border-bottom:solid black 1.0pt;padding:0cm 0cm 1.0pt 0cm;">
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;border:none;padding:0cm;"><span style="font-size:13px;">&nbsp;</span></p>
				</div>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;">
					<br><span style="font-size: 12px;">eLogicTech Solutions Inc, USA. VOIP: (415) 634 6120</span></p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;"><span style="font-size: 
      12px;"><strong>Development Center</strong>: Plot No 72, The May Flower, P &amp; T Colony, Secunderabad &#45; 500 009, Telangana State, India.</span></p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;"><span style="font-size: 12px;">Tel: +91-40-65212232 E-Mail:
						<a href="mailto:accounts@elogictech.com">accounts@elogictech.com</a> URL:
						<a href="http://www.elogictech.com">www.elogictech.com</a>
					</span></p>
			</div>
		</div>
	</footer>
</div>
<?php else: // Monthly Based Service Details ?>

<div class="container" id="printableUSArea_M" style="border: #000 1px solid; margin-bottom: 2%; padding: 3%;">
	<header>
		<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;"><strong><u>MONTHLY SERVICE AGREEMENT</u></strong></p>
		<p style="margin-top:0cm;margin-right:0cm;margin-bottom:.0001pt;margin-left:21.6pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:right;"><b>eL-SOW-<?=date_format($agreement_date,"y");?>-<?=$getServiceData->country_code;?><?=$getServiceData->project_code;?></b></p>
		<p style="margin-top:0cm;margin-right:0cm;margin-bottom:.0001pt;margin-left:21.6pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:right;"><b>Date: <?=date_format($agreement_date,"Y, M d");?>&nbsp; &nbsp;</b></p>
		<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;margin-left:36.0pt;text-align:justify;">&nbsp;</p>
	</header>
	<section>
		<div class="row">
			<div class="col-md-12">
				<div class="pull-right" style="margin-top:2% ">
					<p>This Agreement is made and entered as of <?=date_format($agreement_date,"M d,  Y");?>, between eLogicTech Solutions Inc (hereafter referred to as "Vendor"), a US company with Registered offices at Jackson, Wyoming and <?=$getServiceData->client_name;?> (hereafter referred to as "Client"), a corporation with its primary place of business at "<?=$getServiceData->client_adress;?>" (each a "party" and collectively the "parties").</p>
					<p>In consideration of the foregoing and the agreements contained below, the parties agree as follows:</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="pull-left">
					<p><b>1. Services:</b> <?=$getServiceData->scope_of_the_work;?></p>
					<p><b>2. Payment:</b> Client shall pay Vendor an amount of <?=$getServiceData->country_wise_code;?> <?=number_format($getServiceData->est_cost,2);?> per month per each designated Consultant. This rate shall apply for a period of three months of the Agreement, from Project Start date onwards however, that Vendor may initiate one or more rate changes by giving Client a minimum one-month advance email notice of changes effective after the initial three months of this Agreement. 3D rendering services will be available as an addon service at $25/hour within the monthly model contract.</p>
                    <p><b>A.</b> Rate changes must be agreed to by all parties, in writing. The rate increase maybe initiated once in a year and can increase upto 5%. A rate change will not incur in the first 12 months of working together. </p>
					<p><b>3.</b> A list of the designated Consultants and the Start Date for work by each is attached as Appendix</p>
					<p><b>A: List of Consultants.</b></p>	
				<div style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">
					<p><span style="font-family:&quot;Times New Roman&quot;,serif;font-size:16px;"><strong>Project Contact details:</strong><strong>&nbsp;</strong>&nbsp; &nbsp; &nbsp; &nbsp;Contact name: &nbsp; &nbsp; &nbsp; &nbsp; <?=$getServiceData->project_contact_name;?></span>
						<br><span style="font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Email Id: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?=$getServiceData->project_email_id;?></span>
						<br><span style="font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Contact No: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span style="color:black;"><?=$getServiceData->project_contact_number;?></span></span>
						<br>
						<br><span style="font-family:&quot;Times New Roman&quot;,serif;font-size:16px;"><strong>Billing Contact Details:&nbsp;</strong>&nbsp; &nbsp; &nbsp; &nbsp;Contact name: &nbsp; &nbsp; &nbsp; &nbsp;<?=$getServiceData->billing_contact_name;?></span>
						<br><span style="font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Email Id: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?=$getServiceData->billing_email_id;?></span>
						<br><span style="font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Contact No: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?=$getServiceData->billing_contact_number;?></span></p>
				</div>
			</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="pull-left">
					<p><b>4. PAYMENT TERMS :</b></p>
					<p><b>a.</b> Payment Terms - Invoice will be raised monthly basis at the end of each month.</p>
					<p><b>b.</b> All Invoices should be paid within 10 days of receiving the invoice.</p>
					<p><b>c.</b> eLogic payment cannot be linked to payment of end client.</p>
					<p><b>d.</b> Monthly payment need to be made whether the 180 hours are utilized or note.</p>
					<p><b>e.</b> Unutilized hours cannot be carried forward to next month.</p>
					<p><b>f.</b> All payment disputes if any should to be made within 7 working days of receiving the Invoice.</p>
					<p><b>g.</b> If payment of invoice is not received 15 days after the invoice is due, Services will be suspended until full payment is received. Consultant will give 5 day prior notice to Client account being overdue</p>
					<p><b>h.</b> If payment on invoice is not received 30 days after the invoice is due, a late payment fee of 1.5% will be accrued to balance per month.</p>
					<p><b>i.</b> Client shall pay invoiced amount by check payable / wire transfers to eLogicTech Solutions Within 10 days of receiving the invoice.</p>
				</div>
			</div>
		</div>
	</section>
	<section>
		<div class="row">
			<div class="col-md-12">
				<p><b>Checks shall be sent by mail to the address mentioned below:</b></p>
				<p><b>Regular Mail:</b></p>
				<p><b>eLogicTech Solutions Inc</b></p>
				<p>1312 17th Street Unit 2229<br/>
				Denver, CO 80202
				</p>
				
				<p><b>Wire transfers to bank can be made to:</b></p>
				<ul style="list-style: none;">
					<li>Company Name: <b>eLogicTech Solutions Inc</b></li>
					<li>Accounts Number: <b>139105298756</b></li>
					<li>Routing Transit / ABA Number: <b>California 026009593(wires) 123103716(Checks)</b></li>
					<li>CHIPS Address: <b>0959</b></li>
					<li>SWIFT Address: <b>BOFAUS3N</b></li>
				</ul>
				<p>5. Changes to List of Consultants: Both parties agree to provide a minimum written notice of one month for deleting or adding Consultants. However, in the case where Vendor gives a notice to Client to replace a Consultant, Vendor will make all reasonable efforts to provide the Client with a suitable replacement.</p>
				<p>6. Confidentiality: All exchanges of information between both parties shall be confidential. Each party agrees that for a period of one year following termination of this agreement, it will keep confidential all Confidential Information of the other disclosed to it in connection with this Agreement and will not disclose or otherwise use this Confidential Information.</p>
				<p>7. Term: This Agreement shall be effective for a minimum period of three months for each consultant and shall be automatically extended on a month-to-month basis so long as one or more consultants are performing work for the client and have not been deleted by the client pursuant to Clause#3 of this Agreement.</p>
				<p>8. Quality issues any will need to be notified to the Management within 7 working days of delivery of the Project, failing which the work done will be consider to be of acceptable Quality.</p>
				<div style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;">9. The client can escalate their concern to eLogicTech Management Team &nbsp;</p>
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;margin-left:80.0pt;text-align:justify;">Contact name: Rupali Modi &nbsp;</p>
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;margin-left:80.0pt;text-align:justify;">Email id: &nbsp;
						<a href="mailto:rupali@elogictech.com">rupali@elogictech.com</a>
					</p>
				</div>
				<p>10. Termination: This agreement may be terminated giving a minimum written notice of one Month.</p>
				<p>11. The breaking fee does not apply if two more quality issues were reported by the client in accordance with Section 8 above.</p>
				<p>12. Governing Law: This Agreement shall be governed by, interpreted, and applied in accordance with the laws of the country that the Client is incorporated in.</p>
				<p>13. If a consultant/employee leaves the vendor&apos;s employment, client agrees not to employ that person or to utilize directly or indirectly the services of that person&apos;s subsequent employers for a period of five years from the time the person leaves vendor&apos;s employment.</p>
				<p>14. All work generated as a part of this agreement becomes proprietary rights belonging to the client.</p>
			</div>
		</div>
	</section>
    <section>
    <div class="row text-center" style="border-top:none; padding-top:10px;">
			<div>
				<p><b>The parties have caused their duly authorized representatives to execute this agreement as of the date first set forth above.</b></p>
			</div>
		</div>
	</section>
    
	<section>
		<section>
			<div style="width:50%; float:left;">
				<div>
					<h5><b>Client<br/>TATE ASIA-PACIFIC PTY LTD</b></h5>
				</div>
				<div>
					<p>By:</p><br>
					<p>Name: <?=$getServiceData->project_contact_name;?></p>
					<p>Title: <?=$getServiceData->project_contact_designation;?></p>
					<p>Date: <?=$getServiceData->agreement_date;?></p>
				</div>
			</div>
			<div style="width:50%; float:left; margin-bottom:5%;">
				<div>
					<h5><b>Vendor<br/>ELOGIC SOLUTIONS PTY LTD</b></h5>
				</div>
				<div>
					<p>By:</p><br>
					<p>Name: Rupali Modi</p>
					<p>Title: Director</p>
					<p>Date: <?=$getServiceData->agreement_date;?></p>
				</div>
			</div>
		</section>

		<div class="row" style="border-bottom: none;">
			<div>
				<h4 class="text-center" style="text-decoration:underline;">APPENDIX A: LIST OF DESIGNATED CONSULTANTS</h4>
			</div>
			<div style="margin-left:2%">LIST OF DESIGNATED CONSULTANTS:</div>
		</div>

		<div class="row" style="border-bottom: none;">
			<div class="table-responsive">
				<table class="table table-condensed tbordertd">
					<thead style="border: 1px solid #000;">
						<tr>
							<td class="text-center" style="white-space: nowrap;"><strong>S No</strong></td>
							<td class="text-center"><strong>Name</strong></td>
							<td class="text-center"><strong>Start Date for Services</strong></td>
							<td class="text-center"><strong>End Date for Services</strong></td>
							<td class="text-right"><strong>Description of the offered service(s)</strong></td>
						</tr>
					</thead>
					<tbody>
						<!-- foreach ($order->lineItems as $line) or some such thing here -->
						<tr>
							<td class="text-center">1</td>
							<td class="text-center" style="white-space: nowrap;"><strong><?=$getServiceData->designated_consultants_name;?></strong></td>
							<td class="text-center"><strong><?=$getServiceData->designated_start_date_service;?></strong></td>
							<td class="text-center"><strong><?=$getServiceData->designated_end_date_service;?></strong></td>
							<td class="text-left"><?=$getServiceData->designated_desc_offered_services;?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row text-center" style="border-top:none; padding-top:10px;">
			<div class="col-sm-6">
				<p>Total Number of Designated Consultants:</p>
			</div>
			<div class="col-sm-6">
				<p>01</p>
			</div>
		</div>
	</section>

		<footer>
		<div class="row">
			<div class="col-md-12">
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:right;">&nbsp;</p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp;</p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">&nbsp;</p>
				<div style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;border:none;border-bottom:solid black 1.0pt;padding:0cm 0cm 1.0pt 0cm;">
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;border:none;padding:0cm;"><span style="font-size:13px;">&nbsp;</span></p>
				</div>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;">
					<br><span style="font-size: 12px;">eLogicTech Solutions Inc, USA. VOIP: (415) 634 6120</span></p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;"><span style="font-size: 
      12px;"><strong>Development Center</strong>: Plot No 72, The May Flower, P &amp; T Colony, Secunderabad &#45; 500 009, Telangana State, India.</span></p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;"><span style="font-size: 12px;">Tel: +91-40-65212232 E-Mail:
						<a href="mailto:accounts@elogictech.com">accounts@elogictech.com</a> URL:
						<a href="http://www.elogictech.com">www.elogictech.com</a>
					</span></p>
			</div>
		</div>
	</footer>

</div>

<?php endif;?>
<script>
	$(document).ready(function() {
		$("#printAgreement").click(function() {
			var mode = 'iframe'; //popup
			document.title = "";
			var close = mode == "popup";
			var options = {
				mode: mode,
				popClose: close
			};
			$("div.printableUSArea,#printableUSArea_M").printArea(options);
		});
	});

	function exportHTML() {
		var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
			"xmlns:w='urn:schemas-microsoft-com:office:word' " +
			"xmlns='http://www.w3.org/TR/REC-html40'>" +
			"<head><meta charset='utf-8'><title>eL-SOW-<?=date_format($agreement_date,"y");?>-<?=$getServiceData->country_code;?><?=$getServiceData->project_code;?></title></head><body>";
		var footer = "</body></html>";
		<?php if($serviceType == 'monthly'){ ?>
		var sourceHTML = header + document.getElementById("printableUSArea_M").innerHTML +  footer;
		<?php }else { ?>
		var sourceHTML = header + document.getElementById("printableUSArea").innerHTML +  footer;
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