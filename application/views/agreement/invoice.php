<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php
foreach($getInvoice as $key => $getInvoiceResult){ // fetching the data from service agreement details table 
	
	    $agreement_date=date_create($getInvoiceResult->agreement_date);
        $total_est_cost_amt = ($getInvoiceResult->total_est_hours * $getInvoiceResult->country_wise_rate);
        $est_deliverable_dates = date_create($getInvoiceResult->est_deliverable_dates);
        $gst_Amount = ( $total_est_cost_amt * 10 ) / 100 ; 
		$serviceType = $getInvoiceResult->service_type; 
	
	$companyAddress = $this->db->select('country_company_name,company_address')->from('service_country_code')->where('country_code_name' ,$getInvoiceResult->country_code)->get()->result();
	//amount received and discount amount conditon. 
	
	if($serviceType == 'monthly'):
		
		$receivedAmount = $getInvoiceResult->est_cost;
		
	 else:	
		
		if($getInvoiceResult->sow_discount == '0'){

			$receivedAmount = $total_est_cost_amt;

		}else{

			$receivedAmount = $total_est_cost_amt-$getInvoiceResult->sow_discount;

		}
	
		
	endif;
		
		$amountWordFormat = $this->numbertowords->convert_number($receivedAmount);
	
	}
?>
<style type="text/css">
        .receipt-content .logo a:hover {
            text-decoration: none;
            color: #7793C4;
        }

        .receipt-content .invoice-wrapper {
            /*background: #FFF;
            border: 1px solid #CDD3E2;
            box-shadow: 0px 0px 1px #CCC;
            padding: 40px 40px 60px;
            margin-top: 40px;
            border-radius: 4px;*/ 
			padding: 40px 40px 60px;
			
        }

        .receipt-content .invoice-wrapper .payment-details span {
            color: #A9B0BB;
            display: block;
        }

        .receipt-content .invoice-wrapper .payment-details a {
            display: inline-block;
            margin-top: 5px;
        }

        .receipt-content .invoice-wrapper .line-items .print a {
            display: inline-block;
            border: 1px solid #9CB5D6;
            padding: 13px 13px;
            border-radius: 5px;
            color: #708DC0;
            font-size: 13px;
            -webkit-transition: all 0.2s linear;
            -moz-transition: all 0.2s linear;
            -ms-transition: all 0.2s linear;
            -o-transition: all 0.2s linear;
            transition: all 0.2s linear;
        }

        .receipt-content .invoice-wrapper .line-items .print a:hover {
            text-decoration: none;
            border-color: #333;
            color: #333;
        }

        .receipt-content {
            background: #FFF;
        }

        @media (min-width: 1200px) {
            .receipt-content .container {
                width: 100%;
            }
        }

        .receipt-content .logo {
            text-align: center;
            margin-top: 50px;
        }

        .receipt-content .logo a {
            font-family: Myriad Pro, Lato, Helvetica Neue, Arial;
            font-size: 36px;
            letter-spacing: .1px;
            color: #555;
            font-weight: 300;
            -webkit-transition: all 0.2s linear;
            -moz-transition: all 0.2s linear;
            -ms-transition: all 0.2s linear;
            -o-transition: all 0.2s linear;
            transition: all 0.2s linear;
        }

        .receipt-content .invoice-wrapper .intro {
            line-height: 25px;
            color: #444;
        }

        .receipt-content .invoice-wrapper .payment-info {
            padding: 1%;
            border: 1px #000 solid;
        }

        .receipt-content .invoice-wrapper .payment-info span {
            color: #A9B0BB;
        }

        .receipt-content .invoice-wrapper .payment-info strong {
            display: block;
            color: #444;
            margin-top: 3px;
        }

        @media (max-width: 767px) {
            .receipt-content .invoice-wrapper .payment-info .text-right {
                text-align: left;
                margin-top: 20px;
            }
        }

        .receipt-content .invoice-wrapper .payment-details {
            border-top: 2px solid #EBECEE;
            margin-top: 30px;
            padding-top: 20px;
            line-height: 22px;
        }


        @media (max-width: 767px) {
            .receipt-content .invoice-wrapper .payment-details .text-right {
                text-align: left;
                margin-top: 20px;
            }
        }

        .receipt-content .invoice-wrapper .line-items {
            margin-top: 40px;
        }

        .receipt-content .invoice-wrapper .line-items .headers {
            color: #A9B0BB;
            font-size: 13px;
            letter-spacing: .3px;
            border-bottom: 2px solid #EBECEE;
            padding-bottom: 4px;
        }

        .receipt-content .invoice-wrapper .line-items .items {
            margin-top: 8px;
            border-bottom: 2px solid #EBECEE;
            padding-bottom: 8px;
        }

        .receipt-content .invoice-wrapper .line-items .items .item {
            padding: 10px 0;
            color: #696969;
            font-size: 15px;
        }

        @media (max-width: 767px) {
            .receipt-content .invoice-wrapper .line-items .items .item {
                font-size: 13px;
            }
        }

        .receipt-content .invoice-wrapper .line-items .items .item .amount {
            letter-spacing: 0.1px;
            color: #84868A;
            font-size: 16px;
        }

        @media (max-width: 767px) {
            .receipt-content .invoice-wrapper .line-items .items .item .amount {
                font-size: 13px;
            }
        }

        .receipt-content .invoice-wrapper .line-items .total {
            margin-top: 30px;
        }

        .receipt-content .invoice-wrapper .line-items .total .extra-notes {
            float: left;
            width: 40%;
            text-align: left;
            font-size: 13px;
            color: #7A7A7A;
            line-height: 20px;
        }

        @media (max-width: 767px) {
            .receipt-content .invoice-wrapper .line-items .total .extra-notes {
                width: 100%;
                margin-bottom: 30px;
                float: none;
            }
        }

        .receipt-content .invoice-wrapper .line-items .total .extra-notes strong {
            display: block;
            margin-bottom: 5px;
            color: #454545;
        }

        .receipt-content .invoice-wrapper .line-items .total .field {
            margin-bottom: 7px;
            font-size: 14px;
            color: #555;
        }

        .receipt-content .invoice-wrapper .line-items .total .field.grand-total {
            margin-top: 10px;
            font-size: 16px;
            font-weight: 500;
        }

        .receipt-content .invoice-wrapper .line-items .total .field.grand-total span {
            color: #20A720;
            font-size: 16px;
        }

        .receipt-content .invoice-wrapper .line-items .total .field span {
            display: inline-block;
            margin-left: 20px;
            min-width: 85px;
            color: #84868A;
            font-size: 15px;
        }

        .receipt-content .invoice-wrapper .line-items .print {
            margin-top: 50px;
            text-align: center;
        }



        .receipt-content .invoice-wrapper .line-items .print a i {
            margin-right: 3px;
            font-size: 14px;
        }

        .receipt-content .footer {
            margin-top: 40px;
            margin-bottom: 110px;
            text-align: center;
            font-size: 12px;
            color: #969CAD;
        }
    </style>
<!-- Inlude Header here END-->
<div class="content-wrapper">
	<div class="page-title">
        <div>
            <h1>Invoice Information</h1>
        </div>
        <?php if($this->session->userdata['logged_in_timesheet']['user_type'] != 'manager') : ?>
        <div><button class="btn btn-info" id="btn-export" onclick="downloadInvoice();">Download Invoice</button> | 
            <a class="btn btn-primary icon-btn" href="javascript:void(0);" id="printInvoice" data-toggle="tooltip" title="print"><i class="fa fa-print"></i> Print</a>
        </div>
       <?php endif; ?> 
    </div>
    <div class="receipt-content" id="downloadInvoice_sow">
        <div class="container bootstrap snippet" style="padding-bottom: 2%;">
            <div class="row">
                <div class="col-md-12">
                    <div class="invoice-wrapper">
                        <div class="row" style="border: 1px #000 solid; margin-bottom: 2%; padding-bottom: 1%;">
                            <?php echo $companyAddress[0]->company_address; ?>
                            <div class="col-md-4 intro text-right">
                                <h2><b>INVOICE</b></h2>
                                <p style="margin: 0">INVOICE NO: <?=date_format($agreement_date,"y");?>-<?=$getInvoiceResult->country_code;?><?=$getInvoiceResult->project_code;?></p>
                                <p>DATE: <?=date_format($agreement_date,"M d, Y");?></p>
                            </div>
                        </div>
						<div class="row" style="border: 1px #000 solid; margin-bottom: 2%;">
                            <div class="col-sm-6">
                                <p>Consignee: <strong><?php echo $getInvoiceResult->client_name;?></strong></p>
								<p><strong><?=$getInvoiceResult->billing_contact_name;?> (<?=$getInvoiceResult->billing_contact_designation;?>)</strong></p>
								<p><?php echo $getInvoiceResult->client_adress;?><br>
								Tel <?=trim($getInvoiceResult->billing_contact_number);?>, <?=$getInvoiceResult->project_contact_number;?><br>
								Email: <?=$getInvoiceResult->billing_email_id;?>,  <?=$getInvoiceResult->project_email_id;?>	
								</p>
							</div>
                            <div class="col-sm-6 text-right">
                                <p>Buyer (if other then Consignee)<br><strong>SAME AS CONSIGNEE</strong></p>
                            </div>
                        </div>
                        <div class="row" style="border: 1px #000 solid; border-bottom: none;">
                            <div class="col-sm-5" style="border-right: 1px #000 solid; ">
                                <strong>
                                    BUYER'S ORDER NO. AND DATE
                                </strong>
                            </div>
                            <div class="col-sm-3 text-center" style="border-right: 1px #000 solid; ">
                                <strong>
                                    TERMES
                                </strong>
                            </div>
                            <div class="col-sm-4 text-right" style="">
                                <strong>
                                    DUE DATE
                                </strong>
                            </div>
                        </div>
                        <div class="row text-right" style="border: 1px #000 solid;">
						<div style="padding-left: 2%; line-height: 35px; float:left; clear:both;">
						<p><?=$getInvoiceResult->country_code;?><?=$getInvoiceResult->project_code;?> Date <?=date_format($agreement_date,"M d, Y");?></p>
						</div>
                            <div style="padding-right: 2%; line-height: 35px;">
                                <p><?=date_format($est_deliverable_dates,"M d, Y");?></p>
                            </div>
                        </div>
                        <div class="row text-center" style="border: 1px #000 solid; border-top: none; margin-bottom: 2%;">
                            <div class="col-sm-12">
                                <strong>TERMS OF PAYMENT</strong>
                                <p>BY CHEQUE PAYABLE TO <b><?php echo $companyAddress[0]->	country_company_name; ?>.</b></p>
                            </div>
                        </div>
                        <div class="row" style="margin-top:2%; display: none;">
                            <div class="headers clearfix" style="border: 1px solid #000; display: none">
                                <div class="row text-center">
                                    <div class="col-md-2" style="border-right: 1px #000 solid; ">CONSULTANT</div>
                                    <div class="col-md-3" style="border-right: 1px #000 solid; ">PERIOD</div>
                                    <div class="col-md-3" style="border-right: 1px #000 solid; ">RATE</div>
                                    <div class="col-md-2" style="border-right: 1px #000 solid; ">DESCRIPTION</div>
                                    <div class="col-md-2">AMOUNT</div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="border: 1px solid #000; border-bottom: none;">
                            <div class="table-responsive">
    					<table class="table table-condensed">
    						<thead>
                                <tr>
        							<td class="text-center"><strong>CONSULTANT</strong></td>
        							<td class="text-center"><strong>PERIOD</strong></td>
        							<td class="text-center"><strong>RATE</strong></td>
        							<td class="text-center"><strong>DESCRIPTION</strong></td>
                                    <td class="text-right"><strong>AMOUNT</strong></td>
                                </tr>
    						</thead>
    						<tbody>
    							<!-- foreach ($order->lineItems as $line) or some such thing here -->
    							<tr>
    								<td>1 Consultants</td>
    								<td class="text-center"><?=date_format($agreement_date,"M d, Y");?> to <?=date_format($est_deliverable_dates,"M d, Y");?></td>
    								<td class="text-center">
									$<?=number_format($receivedAmount,2);?>
									</td>
    								<td class="text-center"><?=$getInvoiceResult->department;?></td>
                                    <td class="text-right">$<?=number_format($receivedAmount,2);?></td>
    							</tr>
    							<tr>
    								<td class="no-line"></td>
    								<td class="no-line"></td>
    								<td class="no-line text-center"><strong>Amount In words: <?php  echo strtoupper($amountWordFormat);?> ONLY</strong></td>
    								<td class="no-line text-center">TOTAL</td>
                                    <td class="text-right">$<?=number_format($receivedAmount,2);?></td>
    							</tr>
    						</tbody>
    					</table>
    				</div>
                        </div>
                        <div class="row text-center" style="border: 1px solid #000;">
                            <div class="col-sm-6">
                                <strong>Remarks</strong>
                                <p>THIS IS COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE ANY SIGNATURE</p>
                            </div>
                            <div class="col-sm-6">
                                <strong>Name & Date</strong>
                                <p>RUPALI MODI<br><b><?=date("F d, Y");?></b></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	
	$(document).ready(function() {
		$("#printInvoice").click(function() {
			var mode = 'iframe'; //popup
			var close = mode == "popup";
			var options = {
				mode: mode,
				popClose: close
			};
			$("div#downloadInvoice_sow").printArea(options);
		});
	});
	
function downloadInvoice() {
		var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
			"xmlns:w='urn:schemas-microsoft-com:office:word' " +
			"xmlns='http://www.w3.org/TR/REC-html40'>" +
			"<head><meta charset='utf-8'><title>eL-SOW-Invoice-<?=date_format($agreement_date,"y");?>-<?=$getInvoiceResult->country_code;?><?=$getInvoiceResult->project_code;?></title></head><body>";
		var footer = "</body></html>";
		var sourceHTML = header + document.getElementById("downloadInvoice_sow").innerHTML +  footer;
		var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
		var fileDownload = document.createElement("a");
		document.body.appendChild(fileDownload);
		fileDownload.href = source;
		fileDownload.download = 'eL-SOW-Invoice-<?=date_format($agreement_date,"y");?>-<?=$getInvoiceResult->country_code;?><?=$getInvoiceResult->project_code;?>.doc';
		fileDownload.click();
		document.body.removeChild(fileDownload);
	}
</script>
<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>jquery.PrintArea.js"></script>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->