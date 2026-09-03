<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Management_plan extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('session');
		$this->load->model('timesheet_login');
		$this->load->model('management_plan_model');

		if (empty($this->session->userdata['logged_in_timesheet'])) {
			redirect('home/login');
		}
	}

	public function index() {
		$filterState = $this->build_filter_state();
		$data = $this->build_view_data($filterState);
		$this->load->view('management_plan/management_plan', $data);
	}

	public function export_report() {
		$filterState = $this->build_filter_state();
		$viewData = $this->build_view_data($filterState);
		$rows = isset($viewData['records']) ? $viewData['records'] : array();

		$this->load->library('excel');
		$objPHPExcel = $this->excel;
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->setTitle('Management Plan');

		$headers = array('S.No', 'Client Name', 'Start Date', 'End Date', 'Timesheet Date', 'Invoice Hours');
		$sheet->fromArray($headers, null, 'A1');

		$headerStyle = array(
			'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '2C5AA0')),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
		$sheet->getRowDimension(1)->setRowHeight(28);

		$clientFill = array(
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'F8F9FA')),
			'font' => array('bold' => true)
		);

		$line = 2;
		$sno = 1;
		foreach ($rows as $row) {
			$sheet->setCellValue('A' . $line, $sno);
			$sheet->setCellValue('B' . $line, $this->format_client_name(isset($row->client_name) ? $row->client_name : ''));
			$sheet->setCellValue('C' . $line, $this->format_date(isset($row->start_date) ? $row->start_date : ''));
			$sheet->setCellValue('D' . $line, $this->format_date(isset($row->end_date) ? $row->end_date : '', true));
			$sheet->setCellValue('E' . $line, $this->format_date(isset($row->timesheet_date) ? $row->timesheet_date : '', true));
			$sheet->setCellValue('F' . $line, $this->format_hours(isset($row->invoice_hours) ? $row->invoice_hours : 0));
			$sheet->getStyle('A' . $line . ':F' . $line)->applyFromArray($clientFill);
			$line++;

			$monthRows = isset($row->month_rows) ? $row->month_rows : array();
			foreach ($monthRows as $monthRow) {
				$yearVal = isset($monthRow->year_val) ? (int)$monthRow->year_val : 0;
				$monthVal = isset($monthRow->month_val) ? (int)$monthRow->month_val : 0;
				$monthLabel = ($yearVal > 0 && $monthVal >= 1 && $monthVal <= 12)
					? date('M-Y', strtotime(sprintf('%04d-%02d-01', $yearVal, $monthVal)))
					: '';
				$monthStart = ($yearVal > 0 && $monthVal >= 1 && $monthVal <= 12)
					? sprintf('%04d-%02d-01', $yearVal, $monthVal)
					: '';
				$monthEnd = ($monthStart !== '') ? date('Y-m-t', strtotime($monthStart)) : '';

				$sheet->setCellValue('A' . $line, '');
				$sheet->setCellValue('B' . $line, '  > ' . $monthLabel);
				$sheet->setCellValue('C' . $line, $this->format_date($monthStart));
				$sheet->setCellValue('D' . $line, $this->format_date($monthEnd, true));
				$sheet->setCellValue('E' . $line, $this->format_date(isset($monthRow->timesheet_date) ? $monthRow->timesheet_date : '', true));
				$sheet->setCellValue('F' . $line, $this->format_hours(isset($monthRow->invoice_hours) ? $monthRow->invoice_hours : 0));
				$line++;
			}
			$sno++;
		}

		$lastRow = ($line > 2) ? ($line - 1) : 1;
		$sheet->getStyle('A1:F' . $lastRow)->applyFromArray(array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => 'D0D5DD')
				)
			),
			'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		));
		$sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('C2:F' . $lastRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$columnWidths = array('A' => 10, 'B' => 38, 'C' => 16, 'D' => 16, 'E' => 18, 'F' => 16);
		foreach ($columnWidths as $col => $width) {
			$sheet->getColumnDimension($col)->setWidth($width);
		}
		$sheet->freezePane('A2');

		$filename = 'Management_Plan_' . date('Ymd_His') . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

	private function build_view_data($filterState) {
		$records = $this->management_plan_model->get_management_plan_report($filterState['params']);
		$monthRows = $this->management_plan_model->get_month_wise_by_client($filterState['params']);
		$monthsByClient = array();
		foreach ($monthRows as $monthRow) {
			$clientId = isset($monthRow->client_Id) ? (int)$monthRow->client_Id : 0;
			if ($clientId <= 0) {
				continue;
			}
			if (!isset($monthsByClient[$clientId])) {
				$monthsByClient[$clientId] = array();
			}
			$monthsByClient[$clientId][] = $monthRow;
		}
		foreach ($records as $row) {
			$clientId = isset($row->client_Id) ? (int)$row->client_Id : 0;
			$row->month_rows = isset($monthsByClient[$clientId]) ? $monthsByClient[$clientId] : array();
		}

		return array(
			'records' => $records,
			'clients' => $this->management_plan_model->get_filter_clients(),
			'years' => $this->management_plan_model->get_filter_years(),
			'months' => $this->management_plan_model->get_filter_months(),
			'client_Id' => $filterState['client_Id'],
			'from_year' => $filterState['from_year'],
			'from_month' => $filterState['from_month'],
			'to_year' => $filterState['to_year'],
			'to_month' => $filterState['to_month']
		);
	}

	private function build_filter_state() {
		$clientId = $this->normalize_filter_values($this->input->get_post('client_Id'));
		$fromYear = $this->normalize_filter_values($this->input->get_post('from_year'));
		$fromMonth = $this->normalize_filter_values($this->input->get_post('from_month'));
		$toYear = $this->normalize_filter_values($this->input->get_post('to_year'));
		$toMonth = $this->normalize_filter_values($this->input->get_post('to_month'));

		$rawFromYear = $this->input->get_post('from_year');
		$rawToYear = $this->input->get_post('to_year');
		$isInitialYearLoad = ($rawFromYear === null && $rawToYear === null);

		if (empty($fromYear) && empty($toYear) && $isInitialYearLoad) {
			$currentYear = (string)date('Y');
			$fromYear = array($currentYear);
			$toYear = array($currentYear);
		}

		$params = array(
			'client_Id' => $clientId,
			'from_year' => $fromYear,
			'from_month' => $fromMonth,
			'to_year' => $toYear,
			'to_month' => $toMonth
		);

		return array(
			'params' => $params,
			'client_Id' => $clientId,
			'from_year' => $fromYear,
			'from_month' => $fromMonth,
			'to_year' => $toYear,
			'to_month' => $toMonth
		);
	}

	private function normalize_filter_values($value) {
		if (is_array($value)) {
			$clean = array();
			foreach ($value as $item) {
				$item = trim((string)$item);
				if ($item !== '' && $item !== 'all') {
					$clean[] = $item;
				}
			}
			return array_values(array_unique($clean));
		}

		$value = trim((string)$value);
		if ($value === '' || $value === 'all') {
			return array();
		}
		return array($value);
	}

	private function format_date($value, $withTime = false) {
		if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
			return '';
		}
		$ts = strtotime($value);
		if ($ts === false) {
			return '';
		}
		if ($withTime && date('H:i:s', $ts) !== '00:00:00') {
			return date('d-M-Y H:i', $ts);
		}
		return date('d-M-Y', $ts);
	}

	private function format_hours($value) {
		$value = (float)$value;
		if (fmod($value, 1.0) == 0.0) {
			return (string)(int)$value;
		}
		return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
	}

	private function format_client_name($value) {
		return ucfirst(str_replace("'", " ", trim((string)$value)));
	}
}
