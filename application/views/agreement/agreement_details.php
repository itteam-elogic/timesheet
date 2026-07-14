<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php foreach( $getServieAgreement as $key => $getServiceData){ 

        $agreement_date=date_create($getServiceData->agreement_date);
        $total_est_cost_amt = ($getServiceData->total_est_hours * $getServiceData->country_wise_rate);
        $est_deliverable_dates = date_create($getServiceData->est_deliverable_dates);
        $gst_Amount = ( $total_est_cost_amt * 10 ) / 100 ;

} 
?>
<style type="text/css">
    body {
        font-family: times-new-roman;
        font-size: 18px;
        font-weight: 500;
        border: none;        
    }
	.tbordertd td { border:1px #000 solid;}

    @font-face {
        font-family: times-new-roman;
        src: url(times new roman.ttf);
    }

    

    a {
        text-decoration: none;
    }

    section {
        clear: both;
    }

    hr {
        margin: 20px -20px;
        border: 0;
        border-top: 1px solid #000;
    }

    .invoice-container .invoice-status {
        margin: 20px 0 0 0;
        text-transform: uppercase;
        font-size: 24px;
        font-weight: bold;
    }

    .invoice-container {
        margin: 15px auto;
        padding: 70px;
        max-width: 850px;
        background-color: #fff;
        -moz-border-radius: 6px;
        -webkit-border-radius: 6px;
        -o-border-radius: 6px;
        border-radius: 6px;
        box-shadow: 1px 2px 5px black;
    }

    .invoice-container .payment-btn-container {
        margin-top: 5px;
        text-align: center;
    }

    .invoice-container .payment-btn-container table {
        margin: 0 auto;
    }

    .invoice-container .small-text {
        font-size: 0.9em;
    }

    .invoice-container td.total-row {
        background-color: #f8f8f8;
    }

    .invoice-container td.no-line {
        border: 0;
    }

    .headsign p {
        margin: 0;
        text-align: justify;
    }

    .headsign-mt {
        margin-top: 4% !important;
    }

    .heads-mt {
        margin-top: 8.5% !important;
    }

    .mrtop {
        margin-top: -40px;
    }

    .footer p {
            
        line-height: 12px;
    }

    .italic {
        font-style: italic
    }

    .zerop {
        padding: 0px
    }

    .bank_details {
        list-style: none;
        padding: 0;
        font-weight: 600;
    }

    .list_style {
        list-style-type: none;
    }

    .signature {
        margin-top: 9%;
        padding-bottom: 20%;
    }

    .tableb thead tr th {
        border: 1px #000 solid;
    }

    .tableb td {
        border: 1px #000 solid;
    }

    .tableb tr {
        border: 1px #000 solid;
    }

    .listup {
        margin-top: 1.5%;
        list-style-type: upper-alpha;
    }

    .consultants_list {
        list-style: none;
        margin-left: -6.5%;
        margin-top: 2%;
    }

    .signaturemtb {
        margin: 35% 0;
    }

    .listlower {
        list-style-type: lower-alpha;
    }

    .martp {
        margin: 2% 0;
    }

    .patop {
        padding-top: 2%;
    }

    .listmar {
        list-style: none;
        margin-left: -6.5%;
        margin-top: 2%;
    }

    @media print {

        html,
        body {
            width: 100%;
        }
    }

    @media (max-width: 895px) {
        .invoice-container {
            margin: 15px;
        }
    }

    @media (max-width: 767px) {
        .invoice-container {
            padding: 45px 45px 70px 45px;
        }
    }

    @media screen {
        .application-logo {
            width: 100%;
        }
    }

    @media print {

        .application-logo,
        ._imgcont,
        img {
            width: 30%;
            max-height: none !important;
            height: 50px !important;
        }
    }

    .signaturemt_list li {
        display: inherit !important;
    }

    .sow_table table,
    tr,
    td,
    th {
        border: none !important;
        line-height: 15px !important;
    }

    .sow_table_stamp {
        position: relative;
        top: 4em;
    }

    .sow_table_grid {
        display: grid;
        float: left;
    }

    .sow_table_stamp tr,
    td {
        padding-right: 50px;
    }
    @page { size: auto;  margin: 0mm; }
    @print { 
    @page :footer { 
        display: none
    } 
  
    @page :header { 
        display: none
    } 
} 
</style>
<!-- Inlude Header here END-->
<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Service Agreement</h1>
        </div>
       

        <div><button class="btn btn-info exportButton" id="btn-export" onclick="exportDoc();">Export to word doc</button> 
        </div>
		
       
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <?php if($getServiceData->country_code == 'US'): // US Company Agreement Information ?>

                <?php $this->load->view('agreement/us_agreement_report'); // US Agreement Details ?>

                <?php elseif($getServiceData->country_code == 'AU') :  //Australia service agreement information ?>

                <?php $this->load->view('agreement/au_agreement_report'); // AU Agreement Details ?>
                
                <?php elseif($getServiceData->country_code == 'TRY'): // Turkey Agreement Details ; ?>
                
                <?php $this->load->view('agreement/try_agreement_report'); // Turkey Agreement Details ?>
                
                 <?php elseif($getServiceData->country_code == 'NZ'): // New Zealand Agreement Details ; ?>
                
                <?php $this->load->view('agreement/nz_agreement_report'); // New Zealand Agreement Details ?>                
                
                 <?php elseif($getServiceData->country_code == 'CA'): // Canada Agreement Details ; ?>
                
                <?php $this->load->view('agreement/ca_agreement_report'); // Canada Agreement Details ?>
                
				<?php elseif(in_array($getServiceData->country_code, array('IN','DEU','ITA') )): // Canada Agreement Details ; ?>
                
                <?php $this->load->view('agreement/in_agreement_report'); // Canada Agreement Details ?>
                
                
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>jquery.PrintArea.js"></script>
<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>jquery.googoose.js"></script>



<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->