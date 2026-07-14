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
<div class="container" id="printableINArea" style="border: #000 1px solid; margin-bottom: 2%; padding: 3%;">
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
                    <p>This Agreement is made and entered as of <?=date_format($agreement_date,"d F  Y");?>, between eLogic Solutions India Pvt Ltd (hereafter referred to as "Vendor"), an Indian company with offices at Plot No. 72, The May Flower Building, P & T Colony, Karkhana, Secunderabad, Telangana, India and <?=$getServiceData->client_name;?> (hereafter referred to as "Client"), a corporation with its primary place of business at "<?=$getServiceData->client_adress;?>" (each a "party" and collectively the "parties").</p>
                    <p>In consideration of the foregoing and the agreements contained below, the parties agree as follows:</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="pull-left">
                    <h4><b>Scope of Services</b></h4>
                    <p>This agreement is being made in response to the Client&apos;s request for assistance with developing Dynamo scripts for the project "<b><?=$getServiceData->project_name;?></b>".</p>
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
                    <?php if($getServiceData->sow_discount == '0'):?>
					<li>Cost Estimate: <?=$getServiceData->total_est_hours;?> * $<?=$getServiceData->country_wise_rate;?>/hr = <?=number_format ($total_est_cost_amt,2);?> <?=$getServiceData->country_wise_code;?></li>
					<?php else: ?>
					<li>Discount Price : <?=$getServiceData->sow_discount;?> <?=$getServiceData->country_wise_code;?> </li>
					<li>After Discount Cost Estimation: <?=$getServiceData->total_est_hours;?> * $<?=$getServiceData->country_wise_rate;?>/hr = <?=number_format ($total_est_cost_amt-$getServiceData->sow_discount,2);?> <?=$getServiceData->country_wise_code;?></li> <!-- Discount -->					
	                <?php endif; ?>
                    <!-- <li>GST : <?=$gst_Amount?></li>-->
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
                    <li>All checks to be drawn in favor eLogicTech Solutions</li>
                    <li>All payment disputes if any should to be made within 7 working days of receiving the invoice.</li>
                    <li>Check shall be sent by mail should be addressed to the below address:</li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p style="text-decoration: underline; margin-left: 3.8%;"><b>Wire transfers to bank can be made to</b></p>
                <ul style="list-style: none;">
                    <li>Company Name: <b>eLogic Solutions India Pvt Ltd.</b></li>
                    <li>Accounts Number: <b>00428020000706</b></li>
                    <li>Swift Code: <b>HDFCINBB</b></li>
                </ul>
                <p style="text-decoration: underline; margin-left: 3.8%;"><b>HDFC BANK LTD</b></p>
                <ul style="list-style: none;">
                <li>Usha kiran Complex, GR Floor,</li>
                <li>Pardise Circle, Sarojini Devi Road,</li>  
                <li>Secunderabad - 500003.</li>
                 <li>India</li>
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
                        <li>Confidentiality: All CAD/Revit documents, project information, records and other correspondence information will be handled in a confidential manner.  Neither party will share any of this information with any other company or individual without written authorization by the other party.</li>
                        <li>Governing Law: This Agreement shall be governed by, inter­preted, and applied in accordance with the laws of the country that the Client is incorporated in.</li>
                        <li>All work generated as a part of this Agreement becomes proprietary rights belonging to the Client.</li>
                        <li>Quality issues any will need to be notified to the Project Manager / Management within 7 working days of Delivery of the Project, failing which the work done will be consider to be of acceptable Quality. </li>
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
                <p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;">eLogic Solutions India Pvt Ltd. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?=$getServiceData->client_name;?></p>
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
                    <br><span style="font-size: 12px;">eLogic Solutions India Pvt Ltd., CIN No U72200TG1999PTC032910</span></p>
                <p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;"><span style="font-size: 
      12px;"><strong>Development Center</strong>: Plot No 72, The May Flower, P &amp; T Colony, Secunderabad &#45; 500 009, Telangana State, India.</span></p>
                <p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;"><span style="font-size: 12px;">Tel: +91-40-27892176 E-Mail:
                        <a href="mailto:accounts@elogictech.com">accounts@elogictech.com</a> URL:
                        <a href="http://www.elogictech.com">www.elogictech.com</a>
                    </span></p>
            </div>
        </div>
    </footer>
</div>
<?php else: // Monthly Based Service Details ?>

<div class="container" id="printableINArea_M" style="border: #000 1px solid; margin-bottom: 2%; padding: 3%;">
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
					<p>This Agreement is made and entered into as of <?=date_format($agreement_date,"d F  Y");?> by and between ELOGIC SOLUTIONS PTY LTD. (hereafter referred to as "Vendor"), a 16 Kiah Drive, Point Cook, VIC, 3030, and <?=$getServiceData->client_name;?> (hereafter referred to as "Client"), a corporation with its primary place of business at "<?=$getServiceData->client_adress;?>" (each a "party" and collectively the "parties").</p>
					<p>In consideration of the foregoing and the agreements contained below, the parties agree as follows:</p>
				</div>

			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="pull-left">
					<p>1. Services: Vendor shall make available the designated consultants to provide BIM Architectural services to "Client" on a monthly basis, for up to 180 hours per employee per month. Hours in addition to the 180 hours per month will be billed at AUD 25 per hour+GST. The Services are limited to BIM Architectural Services and do not include MEP, Structural or 3D/animation rendering services. Designated consultants of the vendor (hereafter referred to as "Consultants") shall work for the client remotely from vendor's offices in India. The consultants will be available to work on client's requirements all working days, every month. Vendor will ensure that the designated consultants are fully devoted to client for the entire month for 180 hours. Vendor will invoice client for services provided by vendor on a monthly basis at the end of each month as per the monthly rate provided in clause#2 of this agreement. Client shall pay for these services promptly and shall be responsible for all payment collections, if any, from its customers. If vendor's invoices are not paid promptly, Vendor may suspend work on client’s projects, and may retain any completed work, until payment in full is received.</p>
					<p>2. Payment: Client shall pay Vendor an amount of AUD3,000/month+GST per each designated Consultant. This rate shall apply for a period of three months of the Agreement, from Project Start date onwards however, that Vendor may initiate one or more rate changes by giving Client a minimum one-month advance email notice of changes effective after the initial six months of this Agreement.</p>
					<p>3. A list of the designated Consultants and the Start Date for work by each is attached as Appendix A: List of Consultants. Vendor will invoice Client on a monthly basis at the end of each month. Client shall pay invoiced amounts by check payable to ELOGIC SOLUTIONS PTY LTD. within 10 days of receiving the invoice. Checks shall be sent by mail to the address mentioned below:</p>
					<p>ELOGIC SOLUTIONS PTY LTD</p>
					<p>16 KIAH DRIVE,</p>
					<p>POINT COOK, VIC, 3030</p>

				</div>
			</div>
		</div>
	</section>
	<section>
		<div class="row">
			<div class="col-md-12">
				<p style="text-decoration: underline; margin-left: 3.8%;"><b>Wire transfers to bank can be made to</b></p>
				<ul style="list-style: none;">
					<li> <b>ELOGIC SOLUTIONS PTY LTD</b></li>
					<li><b>Westpac Corporation</b></li>
					<li>BSB : <b>033161</b></li>
					<li>Account : <b>260437</b></li>
					<li>ABN : <b>16 624 567 380</b></li>

				</ul>
				<p>4. Changes to List of Consultants: Both parties agree to provide a minimum written notice of one month for deleting or adding Consultants. However, in the case where Vendor gives a notice to Client to replace a Consultant, Vendor will make all reasonable efforts to provide the Client with a suitable replacement. If a Consultant leaves Vendor's employment, Client agrees not to employ that person or to utilize directly or indirectly the services of that person’s subsequent employers for a period of twelve months from the time the person leaves Vendor's employment.</p>
				<p>5. Confidentiality: All exchanges of information between both parties shall be confidential. Each party agrees that for a period of one year following termination of this agreement, it will keep confidential all Confidential Information of the other disclosed to it in connection with this Agreement and will not disclose or otherwise use this Confidential Information.</p>
				<p>6. Term: This Agreement shall be effective for a minimum period of three months for each consultant and shall be automatically extended on a month-to-month basis so long as one or more consultants are performing work for the client and have not been deleted by the client pursuant to Clause#3 of this Agreement.</p>
				<p>7. Quality issues any will need to be notified to the Project Manager / Management within 7 working days of Delivery of the Project, failing which the work done will be consider to be of acceptable Quality. </p>
				<div style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;">
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:justify;text-indent:28.8pt;">The client can escalate their concern to eLogicTech Management Team &nbsp;</p>
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;margin-left:144.0pt;text-align:justify;text-indent:36.0pt;">Contact name: Rupali Modi &nbsp;</p>
					<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;margin-left:144.0pt;text-align:justify;text-indent:36.0pt;">Email id: &nbsp;
						<a href="mailto:rupali@elogictech.com">rupali@elogictech.com</a>
					</p>
				</div>
				<p>8. Termination: This Agreement may be terminated by either party, with or without cause, by giving a minimum written notice of one month after the initial 3-month period. </p>
				<p>9. Cooling off period or breaking fee: 1 month charges of the Contract per employee.</p>
				<p>10. Governing Law: This Agreement shall be governed by, inter-preted, and applied in accordance with the laws of the country that the Client is incorporated in.</p>
				<p>11. If a consultant/employee leaves the vendor's employment, client agrees not to employ that person or to utilize directly or indirectly the services of that person's subsequent employers for a period of five years from the time the person leaves vendor's employment.</p>
				<p>12. All work generated as a part of this Agreement becomes proprietary rights belonging to the Client.</p>
				<p>The parties have caused their duly authorized representatives to execute this Agreement as of the date first set forth above.</p>
			</div>
		</div>
	</section>
	<section>
		<section>
			<div style="width:50%; float:left;">
				<div>
					<h5><b>TATE ASIA-PACIFIC PTY LTD</b></h5>
				</div>
				<div>
					<p>By:</p><br>
					<p>Name:</p>
					<p>Title:</p>
					<p>Date:</p>
				</div>
			</div>
			<div style="width:50%; float:left; margin-bottom:5%;">
				<div>
					<h5><b>ELOGIC SOLUTIONS PTY LTD</b></h5>
				</div>
				<div>
					<p>By:</p><br>
					<p>Name: Rupali Modi</p>
					<p>Title: Director</p>
					<p>Date: 05-01-2021</p>
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
							<td class="text-center"><strong>S No</strong></td>
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
							<td class="text-center">J Vishal</td>
							<td class="text-center">Nov 11, 2020</td>
							<td class="text-center">Min 3 months</td>
							<td class="text-right">BIM Services</td>
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
					<br><span style="font-size: 12px;">eLogic Solutions Pty Ltd 16 Kiah Drive, Point Cook, VIC, 3030</span></p>
				<p style="margin:0cm;margin-bottom:.0001pt;font-size:16px;font-family:&quot;Times New Roman&quot;,serif;text-align:center;"><span style="font-size: 
      12px;"><strong>Development Center:</strong> Plot No 72, The May Flower, P &amp; T Colony, Secunderabad &#45; 500 009, Telangana State, India.</span></p>
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
            $("div#printableUSArea").printArea(options);
        });
    });

    function exportHTML() {
        var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
            "xmlns:w='urn:schemas-microsoft-com:office:word' " +
            "xmlns='http://www.w3.org/TR/REC-html40'>" +
            "<head><meta charset='utf-8'><title>eL-SOW-<?=date_format($agreement_date,"y");?>-<?=$getServiceData->country_code;?><?=$getServiceData->project_code;?></title></head><body>";
        var footer = "</body></html>";
        <?php if($serviceType == 'monthly'){ ?>
		var sourceHTML = header + document.getElementById("printableINArea_M").innerHTML +  footer;
		<?php }else { ?>
		var sourceHTML = header + document.getElementById("printableINArea").innerHTML +  footer;
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