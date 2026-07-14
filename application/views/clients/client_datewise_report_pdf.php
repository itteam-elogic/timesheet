<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Resource Billability</title>
	<style>
		@font-face {
			font-family: Junge;
			src: url(Junge-Regular.ttf);
		}

		.clearfix:after {
			content: "";
			display: table;
			clear: both;
		}

		a {
			color: #001028;
			text-decoration: none;
		}

		body {
			font-family: Junge;
			position: relative;
			width: 21cm;
			height: 29.7cm;
			margin: 0 auto;
			color: #001028;
			background: #FFFFFF;
			font-size: 14px;
		}

		.arrow {
			margin-bottom: 4px;
		}

		.arrow.back {
			text-align: right;
		}

		.inner-arrow {
			padding-right: 10px;
			height: 30px;
			display: inline-block;
			background-color: rgb(233, 125, 49);
			text-align: center;

			line-height: 30px;
			vertical-align: middle;
		}

		.arrow.back .inner-arrow {
			background-color: rgb(233, 217, 49);
			padding-right: 0;
			padding-left: 10px;
		}

		.arrow:before,
		.arrow:after {
			content: '';
			display: inline-block;
			width: 0;
			height: 0;
			border: 15px solid transparent;
			vertical-align: middle;
		}

		.arrow:before {
			border-top-color: rgb(233, 125, 49);
			border-bottom-color: rgb(233, 125, 49);
			border-right-color: rgb(233, 125, 49);
		}

		.arrow.back:before {
			border-top-color: transparent;
			border-bottom-color: transparent;
			border-right-color: rgb(233, 217, 49);
			border-left-color: transparent;
		}

		.arrow:after {
			border-left-color: rgb(233, 125, 49);
		}

		.arrow.back:after {
			border-left-color: rgb(233, 217, 49);
			border-top-color: rgb(233, 217, 49);
			border-bottom-color: rgb(233, 217, 49);
			border-right-color: transparent;
		}

		.arrow span {
			display: inline-block;
			width: 80px;
			margin-right: 20px;
			text-align: right;
		}

		.arrow.back span {
			margin-right: 0;
			margin-left: 20px;
			text-align: left;
		}

		h1 {
			color: #000000;
			font-family: Junge;
			font-size: 2.4em;
			font-weight: normal;
			text-align: center;
			margin: 0 0 1em 0;
		}

		h1 small {
			font-size: 0.45em;
			line-height: 1.5em;
			float: left;
		}

		h1 small:last-child {
			float: right;
		}

		#project {
			float: left;
		}

		#company {
			float: right;
		}

		table {
			width: 100%;
			border-spacing: 0px;
			margin-bottom: 30px;
		}

		table th,
		table td {
			text-align: center !important;
			border: black solid 1px;
			;
		}

		table th {
			padding: 5px 10px;
			color: #000000;
			border-bottom: 1px solid #C1CED9;
			white-space: nowrap;
			font-weight: bold;
		}

		table .service,
		table .desc {
			text-align: left;
		}

		table td {
			padding: 5px;
			text-align: right;
		}

		table td.service,
		table td.desc {
			vertical-align: top;
		}

		table td.unit,
		table td.qty,
		table td.total {
			font-size: 1.2em;
		}

		table td.unit {
			text-align: left !important;
		}

		table td.sub {
			border-top: 1px solid #C1CED9;
		}

		table td.grand {
			border-top: 1px solid #5D6975;
		}

		table tr:nth-child(2n-1) td {
			background: #EEEEEE;
		}

		table tr:last-child td {
			background: #DDDDDD;
		}

		#details {
			margin-bottom: 30px;
		}

		footer {
			color: #5D6975;
			width: 100%;
			height: 30px;
			position: absolute;
			bottom: 0;
			border-top: 1px solid #C1CED9;
			padding: 8px 0;
			text-align: center;
		}

		table tr td:nth-child(1) {
			width: 5% !important;
		}

		table tr td:nth-child(2) {
			width: 25% !important;
		}

		table tr td:nth-child(3) {
			width: 20% !important;
		}

		table tr td:nth-child(4) {
			width: 5% !important;
		}

		table tr td:nth-child(5) {
			width: 5% !important;
		}

		table tr td:nth-child(6) {
			width: 15% !important;
		}

		table tr td:nth-child(7) {
			width: 5% !important;
		}

		table tr td:nth-child(8) {
			width: 5% !important;
		}

		table tr td:nth-child(9) {
			width: 15% !important;
		}
	</style>

</head>

<body style="position:relative;left:-35px;">
	<?php if(!empty($resouceBillabilityPdfResult)):?>
	<div>
		<div>
			<div style="text-align:center;">
				<h1><i class="fa fa-bell"></i> Resource Billability</h1>
			</div>
			<div style="margin:0 0 3% 0; text-align:center; font-size:18px;">
				<p style="padding: 0% 0% 0% 1%;"><span style="padding-left:5px">This is prepared based on  <?php echo date('d-M-Y',strtotime($_REQUEST['form_date']));?> To <?php echo date('d-M-Y',strtotime($_REQUEST['to_date']));?> </span></p>
			</div>
		</div>

		<table>
			<thead>
				<tr>
					<th>Sno</th>
					<th>Name</th>
					<th>Project Name</th>
					<th>Type</th>
					<th>Billable Hours</th>
					<th>Non-Billable Hours</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				  $i=1;
				  $totalDeveloperBHoursCNT = 0;
				  $totalDeveloperNon_BHoursCNT = 0;
				  foreach ($resouceBillabilityPdfResult as $key => $reportResult) :

					if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;

					$getListOfProjects   	= $this->emptimelog_model->getAddedReportTaskNames($reportResult->task_Id); // List of tasks

			/***************************** Billable And Non-billable hours conditions **************/

				if($reportResult->resource_billability == 'Billable'){

						$totalDeveloperBHours   	=  $reportResult->vickty;

						$totalDeveloperBHoursCNT   +=  $reportResult->vickty;

				}else{

						$totalDeveloperBHours 		= 	'0';

				} 

				if($reportResult->resource_billability == 'Non_billable'){

						$totalDeveloperNon_BHours   	=  $reportResult->vickty;

						$totalDeveloperNon_BHoursCNT    += $reportResult->vickty;

				}else{

						$totalDeveloperNon_BHours 		= 	'0';

				}  

		  /***************************** Billable And Non-billable hours conditions **************/
			?>
				<tr>
					<td>
						<?php echo $i ?>
					</td>
					<td><span class="label label-info"><?php echo ucfirst($reportResult->name);?></span></td>
					<td>
						<?php echo ucfirst($reportResult->project_name);?> </td>
					<td>
						<?php echo ucfirst($reportResult->resource_billability);?> </td>
					<td>
						<?php echo $totalDeveloperBHours; ?> </td>
					<td>
						<?php echo $totalDeveloperNon_BHours; ?> </td>
				</tr>
				<?php $i++; endforeach; ?>

				<tr>
					<td colspan="4" class="sub" style="text-align: right !important;">Billable Hours :</td>
					<td class="sub total" colspan="2" style="text-align: left !important; padding-left: 10px;">
						<?php echo $totalDeveloperBHoursCNT; ?>
					</td>
				</tr>
				<tr>
					<td colspan="5" style="text-align: right !important;">Non-Billable Hours :</td>
					<td class="total" colspan="1" style="text-align: left !important; padding-left: 10px;">
						<?php echo $totalDeveloperNon_BHoursCNT; ?> </td>
				</tr>

			</tbody>
		</table>
	</div>
	<?php endif; ?>
</body>

</html>