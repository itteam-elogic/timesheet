<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kpi_Reports extends CI_Controller {


	public function __construct() {
		
		parent::__construct();
		// Load form helper library
		$this->load->helper('form');
		// Load form validation library
		$this->load->library('form_validation');
		// Load session library
		$this->load->library('session');
        
        $this->load->library('pagination');
		
		$this->load->helper('text');
		$this->load->helper('department');
		$this->load->helper('kpi_display');
        
        $this->load->library('excel'); // load excel library		
		// Load database
		
		// Load database		
		$this->load->model('timesheet_login');
		
		$this->load->model('client_model');
        
        $this->load->model('project_model');
		
		$this->load->model('task_model');
        
        $this->load->model('emptimelog_model');
        
        $this->load->model('kpi_reports_model');
        
        $this->load->model('resourcelog_model');
        
        $this->load->model('feedback_model');
		
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
		
    }
    
     
public function index() {
       
        $this->load->view('kpi-reports/index'); // This loads application/views/kpi_reports/index.php
    }

public function getMonthWiseEmpData_mep()
{
    $search = $this->input->get('search');

    // Year and month(s) from dropdowns; support month[] or month (comma-separated)
    $year = $this->input->get('year');
    $monthParam = $this->input->get('month');
    if (is_array($monthParam)) {
        $selectedMonths = array_map('intval', array_filter($monthParam));
    } elseif (!empty($monthParam)) {
        $selectedMonths = array_map('intval', array_filter(explode(',', $monthParam)));
    } else {
        $selectedMonths = [];
    }

    // Default: previous month
    $prevMonth = (int) date('n', strtotime('first day of previous month'));
    $prevYear = (int) date('Y', strtotime('first day of previous month'));

    if (empty($year) || empty($selectedMonths)) {
        $year = $prevYear;
        $selectedMonths = [$prevMonth];
    }

    $year = (int) $year;
    $selectedMonths = array_unique(array_filter($selectedMonths, function ($m) {
        return $m >= 1 && $m <= 12;
    }));
    sort($selectedMonths, SORT_NUMERIC);

    if (empty($selectedMonths)) {
        $selectedMonths = [$prevMonth];
    }

    // Build from_date and to_date from first and last selected month
    $firstMonth = $selectedMonths[0];
    $lastMonth = $selectedMonths[count($selectedMonths) - 1];
    $from_date = sprintf('%04d-%02d-01', $year, $firstMonth);
    $to_date = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $lastMonth)));

    $data['getkpiReports'] = $this->kpi_reports_model->getkpiInformation(999999, 0, '', $search, $from_date, $to_date, 'MEP');
    $data['search'] = $search;
    $data['from_date'] = $from_date;
    $data['to_date'] = $to_date;
    $data['year'] = $year;
    $data['selected_months'] = $selectedMonths;
    $data['getkpiReportsSummary'] = $this->kpi_reports_model->getkpiInformationSummary();
    $data['pagination_links'] = '';

    $data['monthName'] = $selectedMonths;

    $data['datesMatch'] = true;

    $this->load->view('kpi-reports/kpi-report-month-wise-result_mep', $data);
}


public function getMonthWiseEmpData()
{
    
    
    $search = $this->input->get('search');
    if (is_array($search)) {
        $search = array_values(array_filter(array_map('trim', $search)));
        $search = !empty($search) ? implode(', ', array_unique($search)) : '';
    } elseif (is_string($search)) {
        $search = kpi_normalize_search_display($search);
    } else {
        $search = '';
    }

    $from_year = $this->input->get('from_year');
    $from_month = $this->input->get('from_month');
    $to_year = $this->input->get('to_year');
    $to_month = $this->input->get('to_month');
    $from_date = $this->input->get('from_date');
    $to_date = $this->input->get('to_date');
    $department = $this->input->get('department');
    if (!empty($department) && (is_string($department) && trim($department) === '' || $department === '__all__')) {
        $department = '';
    }

    $hasYmParams = ($from_year !== null && $from_year !== '') && ($to_year !== null && $to_year !== '');
    $hasDateParams = !empty($from_date) && !empty($to_date)
        && trim((string) $from_date) !== '' && trim((string) $to_date) !== '';

    if (!$hasYmParams && !$hasDateParams) {
        $prevMonth = (int) date('n', strtotime('first day of previous month'));
        $prevYear = (int) date('Y', strtotime('first day of previous month'));
        $from_year = $prevYear;
        $from_month = $prevMonth;
        $to_year = $prevYear;
        $to_month = $prevMonth;
    }

    $resolvedDates = $this->_resolve_kpi_consolidated_dates(
        $hasDateParams ? $from_date : null,
        $hasDateParams ? $to_date : null,
        $hasYmParams || !$hasDateParams ? $from_year : null,
        $hasYmParams || !$hasDateParams ? $from_month : null,
        $hasYmParams || !$hasDateParams ? $to_year : null,
        $hasYmParams || !$hasDateParams ? $to_month : null
    );
    $from_date = $resolvedDates['from_date'];
    $to_date = $resolvedDates['to_date'];

    // Fetch all data without pagination - pass very large limit to get all records (department filter for Architecture sub-departments)
    $data['getkpiReports'] = $this->kpi_reports_model->getkpiInformation(999999, 0, '', $search, $from_date, $to_date, $department, 'getMonthWiseEmpData');
    $data['search'] = $search;
    $data['from_date'] = $from_date;
    $data['to_date'] = $to_date;
    $data['from_year'] = $resolvedDates['from_year'];
    $data['from_month'] = $resolvedDates['from_month'];
    $data['to_year'] = $resolvedDates['to_year'];
    $data['to_month'] = $resolvedDates['to_month'];
    $data['department'] = $department;
    $data['summary_heading'] = kpi_month_wise_summary_heading($department, $search, $from_date, $to_date);
    $data['search_included_emp_ids'] = !empty($search) ? $this->kpi_reports_model->getSearchMatchEmpIds($search) : array();
    // Summary always follows the same filtered employee list as the grid
    $data['getkpiReportsSummary'] = [];
    if (!empty($data['getkpiReports'])) {
        $seen = [];
        foreach ($data['getkpiReports'] as $r) {
            if (isset($seen[$r->empId])) continue;
            $seen[$r->empId] = true;
            $data['getkpiReportsSummary'][] = (object) [
                'empId' => $r->empId,
                'name' => $r->name,
                'reporting_manger' => $r->reporting_manger,
                'department' => $r->department,
                'emp_com_id' => $r->emp_com_id
            ];
        }
    }

    // Keep Employee Name column alphabetical (case-insensitive).
    if (!empty($data['getkpiReports']) && is_array($data['getkpiReports'])) {
        usort($data['getkpiReports'], function($a, $b) {
            $nameA = isset($a->name) ? (string)$a->name : '';
            $nameB = isset($b->name) ? (string)$b->name : '';
            return strcasecmp($nameA, $nameB);
        });
    }
    if (!empty($data['getkpiReportsSummary']) && is_array($data['getkpiReportsSummary'])) {
        usort($data['getkpiReportsSummary'], function($a, $b) {
            $nameA = isset($a->name) ? (string)$a->name : '';
            $nameB = isset($b->name) ? (string)$b->name : '';
            return strcasecmp($nameA, $nameB);
        });
    }

    $data['pagination_links'] = ''; // No pagination - show all data on single page
    
    // Convert date range to months array for the view (needed for month loop)
    $startDate = new DateTime($from_date);
    $endDate = new DateTime($to_date);
    $monthName = [];
    $currentDate = clone $startDate;
    $currentDate->modify('first day of this month');
    while ($currentDate <= $endDate) {
        $monthName[] = (int)$currentDate->format('n');
        $currentDate->modify('+1 month');
    }
    
    // If no months found, default to previous month
    if (empty($monthName)) {
        $prevMonth = (int) date('n', strtotime('first day of previous month'));
        $monthName = [$prevMonth];
    }
    
    $data['monthName'] = $monthName;
    
    // Disable quality error log date validation - always show data
    $data['datesMatch'] = true;

    // Batch-preload report data for speed (avoids N*M model calls in the view)
    $data['preload'] = [];
    $data['managerNamesById'] = [];
    if (!empty($data['getkpiReports'])) {
        $monthYearPairs = [];
        $startDate = new DateTime($from_date);
        $endDate = new DateTime($to_date);
        $currentDate = clone $startDate;
        $currentDate->modify('first day of this month');
        while ($currentDate <= $endDate) {
            $monthYearPairs[] = ['month' => (int)$currentDate->format('n'), 'year' => (int)$currentDate->format('Y')];
            $currentDate->modify('+1 month');
        }
        if (empty($monthYearPairs) && !empty($data['monthName'])) {
            $prevMonth = (int) date('n', strtotime('first day of previous month'));
            $prevYear = (int) date('Y', strtotime('first day of previous month'));
            $monthYearPairs = [['month' => $prevMonth, 'year' => $prevYear]];
        }
        $empIds = [];
        $empComIdByEmpId = [];
        $empDeptByEmpId = [];
        $managerIds = [];
        foreach ($data['getkpiReports'] as $r) {
            $empIds[] = $r->empId;
            $empComIdByEmpId[$r->empId] = isset($r->emp_com_id) ? $r->emp_com_id : '';
            $empDeptByEmpId[$r->empId] = isset($r->department) ? $r->department : '';
            if (!empty($r->reporting_manger)) {
                $managerIds[$r->reporting_manger] = true;
            }
        }
        $empIds = array_values(array_unique($empIds));
        $managerIds = array_keys($managerIds);
        if (!empty($empIds) && !empty($monthYearPairs)) {
            $data['preload'] = $this->kpi_reports_model->getMonthWiseReportDataBatch($empIds, $empComIdByEmpId, $empDeptByEmpId, $monthYearPairs);
        }
        if (!empty($managerIds)) {
            $managers = $this->db->select('empId, name')->from('employee_details')->where_in('empId', $managerIds)->get()->result();
            foreach ($managers as $m) {
                $data['managerNamesById'][$m->empId] = $m->name;
            }
        }
    }

    // Load view - data is always shown
    $this->load->view('kpi-reports/kpi-report-month-wise-result', $data);
}
    
public function consolidatedReport()
{

    
    $search = $this->input->get('search'); // From search input box (can be array from multi-select)
    if (is_array($search)) {
        // Normalize: trim, drop empty, de-duplicate and join as comma-separated string
        $search = array_values(array_filter(array_map('trim', $search)));
        $search = !empty($search) ? implode(', ', array_unique($search)) : '';
    }
    $department = $this->input->get('department'); // From department dropdown (can be array or comma-separated string)
    $resolvedDates = $this->_resolve_kpi_consolidated_dates(
        $this->input->get('from_date'),
        $this->input->get('to_date'),
        $this->input->get('from_year'),
        $this->input->get('from_month'),
        $this->input->get('to_year'),
        $this->input->get('to_month')
    );
    $data_from_date = $resolvedDates['from_date'];
    $data_to_date = $resolvedDates['to_date'];

    // Handle multiple departments; __all__ = All departments (no filter)
    if (!empty($department)) {
        if (is_array($department)) {
            $department = array_values(array_filter($department, function($d) { return $d !== '' && $d !== '__all__'; }));
            $department = !empty($department) ? $department : '';
        } elseif (is_string($department) && $department !== '__all__') {
            $department = array_filter(array_map('trim', explode(',', $department)));
            $department = array_values(array_filter($department, function($d) { return $d !== '__all__'; }));
            $department = !empty($department) ? $department : '';
        } else {
            $department = '';
        }
    } else {
        $department = '';
    }

    // Fetch all data without pagination - pass very large limit to get all records
    $data['getkpiReports'] = $this->kpi_reports_model->getkpiInformation(999999, 0, '', $search, $data_from_date, $data_to_date, $department);
    $data['search'] = $search;
    $data['department'] = $department;
    $data['from_date'] = $data_from_date;
    $data['to_date'] = $data_to_date;
    $data['from_year'] = $resolvedDates['from_year'];
    $data['from_month'] = $resolvedDates['from_month'];
    $data['to_year'] = $resolvedDates['to_year'];
    $data['to_month'] = $resolvedDates['to_month'];
    $data['getkpiReportsSummary'] = [];
    if (!empty($data['getkpiReports'])) {
        $seen = [];
        foreach ($data['getkpiReports'] as $r) {
            if (isset($seen[$r->empId])) continue;
            $seen[$r->empId] = true;
            $data['getkpiReportsSummary'][] = (object) [
                'empId' => $r->empId,
                'name' => $r->name,
                'reporting_manger' => $r->reporting_manger,
                'department' => $r->department,
                'emp_com_id' => $r->emp_com_id
            ];
        }
    }
    if (!empty($data['getkpiReportsSummary']) && is_array($data['getkpiReportsSummary'])) {
        usort($data['getkpiReportsSummary'], function($a, $b) {
            $nameA = isset($a->name) ? (string)$a->name : '';
            $nameB = isset($b->name) ? (string)$b->name : '';
            return strcasecmp($nameA, $nameB);
        });
    }
    $data['pagination_links'] = ''; // No pagination - show all data on single page
    
    // Validate quality error log dates (use data dates for validation)
    $data['datesMatch'] = $this->kpi_reports_model->validateQualityErrorLogDates('', $data_from_date, $data_to_date);
    
    // Store data dates for view calculations (but keep form dates empty)
    $data['data_from_date'] = $data_from_date;
    $data['data_to_date'] = $data_to_date;

    // Batch-preload report data for speed (avoids N*M model calls in the view)
    $data['preload'] = [];
    $data['managerNamesById'] = [];
    $data['managerDeptTotals'] = [];
    $calc_from = $data_from_date;
    $calc_to = $data_to_date;
    if (empty($calc_from) || empty($calc_to)) {
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');
        $calc_from = $currentYear . '-01-01';
        $calc_to = date('Y-m-t', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
    }
    $monthYearPairs = [];
    if (!empty($calc_from) && !empty($calc_to)) {
        $startDate = new DateTime($calc_from);
        $endDate = new DateTime($calc_to);
        $currentDate = clone $startDate;
        $currentDate->modify('first day of this month');
        while ($currentDate <= $endDate) {
            $monthYearPairs[] = array('month' => (int) $currentDate->format('n'), 'year' => (int) $currentDate->format('Y'));
            $currentDate->modify('+1 month');
        }
    }
    $allKpiDepartments = function_exists('ts_primary_delivery_departments')
        ? ts_primary_delivery_departments()
        : array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');
    if (!empty($department)) {
        if (is_array($department)) {
            $departmentsToShow = array_values(array_intersect($allKpiDepartments, array_filter($department)));
            if (empty($departmentsToShow)) {
                $departmentsToShow = array_values(array_filter($department));
            }
        } else {
            $departmentsToShow = in_array($department, $allKpiDepartments, true)
                ? array($department)
                : array($department);
        }
    } else {
        $departmentsToShow = $allKpiDepartments;
    }
    $departmentsToShow = kpi_consolidated_summary_departments_to_show($departmentsToShow, $search);
    $data['departmentsToShow'] = $departmentsToShow;
    $data['monthYearPairs'] = $monthYearPairs;

    if (!empty($data['getkpiReports'])) {
        $empIds = [];
        $empComIdByEmpId = [];
        $managerIds = [];
        foreach ($data['getkpiReports'] as $r) {
            $primaryDeliveryDepts = function_exists('ts_primary_delivery_departments')
                ? ts_primary_delivery_departments()
                : ['Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP'];
            if (!in_array($r->department, $primaryDeliveryDepts, true) || empty($r->reporting_manger)) {
                continue;
            }
            $empIds[] = $r->empId;
            $empComIdByEmpId[$r->empId] = isset($r->emp_com_id) ? $r->emp_com_id : '';
            if (!empty($r->reporting_manger)) {
                $managerIds[$r->reporting_manger] = true;
            }
        }
        if (!empty($search)) {
            foreach (kpi_consolidated_filter_manager_profiles($search) as $profile) {
                $mid = $profile['empId'];
                if ($mid === '' || in_array($mid, $empIds, true)) {
                    continue;
                }
                $empIds[] = $mid;
                if (!isset($empComIdByEmpId[$mid])) {
                    $row = $this->db->select('emp_com_id')->from('employee_details')->where('empId', $mid)->get()->row();
                    $empComIdByEmpId[$mid] = ($row && isset($row->emp_com_id)) ? $row->emp_com_id : '';
                }
            }
        }
        $empIds = array_values(array_unique($empIds));
        $managerIds = array_keys($managerIds);
        if (!empty($empIds) && !empty($monthYearPairs)) {
            $data['preload'] = $this->kpi_reports_model->getConsolidatedReportDataBatch($empIds, $empComIdByEmpId, $monthYearPairs);
        }
        if (!empty($managerIds)) {
            $managers = $this->db->select('empId, name')->from('employee_details')->where_in('empId', $managerIds)->get()->result();
            foreach ($managers as $m) {
                $data['managerNamesById'][$m->empId] = $m->name;
            }
        }
    }

    $data['managerDeptTotals'] = $this->accumulate_consolidated_dept_manager_totals(
        !empty($data['getkpiReports']) ? $data['getkpiReports'] : array(),
        !empty($data['preload']) ? $data['preload'] : array(),
        $monthYearPairs,
        $departmentsToShow
    );
    $data['managerSelfTotalsByDept'] = $this->build_manager_self_totals_by_dept(
        !empty($data['getkpiReports']) ? $data['getkpiReports'] : array(),
        !empty($data['preload']) ? $data['preload'] : array(),
        $monthYearPairs,
        $search,
        $departmentsToShow
    );

    // Load view
    $this->load->view('kpi-reports/kpi-consolidated-report', $data);
    
}

    /**
     * Client report grid: merge clients[], pms[], and project into the comma-separated search string used by getAllClientInformation.
     *
     * @return array{search_merged: string, clients: array, pms: array, project: string}
     */
    protected function client_report_grid_filters_from_request()
    {
        $terms = array();
        $clientsSel = array();
        $clients = $this->input->get('clients');
        if (!is_array($clients) && $clients !== null && trim((string)$clients) !== '') {
            $clients = array((string)$clients);
        }
        if (is_array($clients)) {
            foreach ($clients as $c) {
                $t = trim((string)$c);
                if ($t !== '' && strcasecmp($t, '__all__') !== 0) {
                    $clientsSel[] = $t;
                    $terms[] = $t;
                }
            }
        }
        $pmsSel = array();
        $pms = $this->input->get('pms');
        if (!is_array($pms) && $pms !== null && trim((string)$pms) !== '') {
            $pms = array((string)$pms);
        }
        if (is_array($pms)) {
            foreach ($pms as $p) {
                $t = trim((string)$p);
                if ($t !== '' && strcasecmp($t, '__all__') !== 0) {
                    $pmsSel[] = $t;
                    $terms[] = $t;
                }
            }
        }
        $projectRaw = $this->input->get('project');
        $project = '';
        if (is_array($projectRaw)) {
            foreach ($projectRaw as $p) {
                $t = trim((string) $p);
                if ($t !== '' && strcasecmp($t, '__all__') !== 0) {
                    $project = $t;
                    break;
                }
            }
        } else {
            $project = trim((string) $projectRaw);
        }
        if ($project !== '' && strcasecmp($project, '__all__') !== 0) {
            $terms[] = $project;
        }
        $terms = array_values(array_unique($terms));
        return array(
            'search_merged' => implode(', ', $terms),
            'clients' => $clientsSel,
            'pms' => $pmsSel,
            'project' => ($project !== '' && strcasecmp($project, '__all__') !== 0) ? $project : '',
        );
    }

    /**
     * Apply client / project / PM grid filters to grouped client report data (matches grid JS).
     *
     * @param array $grouped
     * @param array $grid Keys: clients, pms, project
     * @return array
     */
    protected function filter_client_report_grouped_data(array $grouped, array $grid, $search = '')
    {
        $this->load->helper('kpi_display');
        return client_report_filter_grouped_data($grouped, $grid, $search);
    }

    /**
     * Write department KPI summary table to Excel (matches on-screen dept KPI table).
     *
     * @param PHPExcel_Worksheet $sheet
     * @param array $summary Result from getClientReportDepartmentKpiSummary()
     * @param int $startRow 1-based row index
     * @return int Next row index (after table + blank spacer)
     */
    protected function write_client_report_dept_kpi_summary_excel($sheet, array $summary, $startRow)
    {
        $this->load->helper('kpi_display');
        if (empty($summary['has_data']) || empty($summary['rows']) || !is_array($summary['rows'])) {
            return (int) $startRow;
        }

        $formatPct = function ($v) {
            if ($v === null || $v === '') {
                return '-';
            }
            return round((float) $v) . '%';
        };
        $formatHours = function ($v) {
            if ($v === null || $v === '') {
                return '-';
            }
            $v = (float) $v;
            if (fmod($v, 1.0) == 0.0) {
                return (string) (int) $v;
            }
            return rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.');
        };
        $formatNumber = function ($v) {
            if ($v === null || $v === '') {
                return '-';
            }
            return number_format((float) $v, 2, '.', '');
        };
        $metricKeys = array(
            array('prod_hours', $formatHours),
            array('pg_hours', $formatHours),
            array('utilization_hours', $formatHours),
            array('productivity_pct', $formatPct),
            array('project_general_pct', $formatPct),
            array('utilization_pct', $formatPct),
            array('quality_pct', $formatPct),
            array('invoiced_hours', $formatNumber),
            array('difference', $formatNumber),
        );
        $writeMetricValues = function ($sheet, $deptRow, $rowNum, array $cols) use ($metricKeys) {
            foreach ($metricKeys as $i => $metric) {
                $val = isset($deptRow[$metric[0]]) ? $deptRow[$metric[0]] : null;
                $sheet->setCellValueExplicit($cols[$i] . $rowNum, $metric[1]($val), PHPExcel_Cell_DataType::TYPE_STRING);
            }
        };

        $headerStyle = array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size' => 10),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '014B88')),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $sectionStyle = array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size' => 11),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '2C5AA0')),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $borderStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 'B8C4D4'),
                ),
            ),
        );
        $centerStyle = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $deptHeaders = array(
            'Departments', 'Prod Hours', 'PG Hours', 'Utilization Hours',
            'Productivity%', 'Project General%', 'Utilization%', 'Quality %',
            'Invoiced Hours', 'Difference',
        );

        $writeDeptTable = function ($sheet, $title, $deptRows, &$row) use (
            $writeMetricValues, $headerStyle, $sectionStyle, $borderStyle, $centerStyle, $deptHeaders
        ) {
            $sheet->setCellValue('A' . $row, $title);
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($sectionStyle);
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
            $headerRow = $row;
            $sheet->fromArray($deptHeaders, null, 'A' . $headerRow);
            $sheet->getStyle('A' . $headerRow . ':J' . $headerRow)->applyFromArray($headerStyle);
            $sheet->getRowDimension($headerRow)->setRowHeight(22);
            $row++;
            $dataStart = $row;
            foreach ($deptRows as $deptRow) {
                $sheet->setCellValue('A' . $row, isset($deptRow['label']) ? $deptRow['label'] : '');
                $writeMetricValues($sheet, $deptRow, $row, array('B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'));
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;
            }
            if ($row > $dataStart) {
                $dataEnd = $row - 1;
                $sheet->getStyle('A' . $headerRow . ':J' . $dataEnd)->applyFromArray($borderStyle);
                $sheet->getStyle('A' . $dataStart . ':A' . $dataEnd)->getFont()->setBold(true);
                $sheet->getStyle('A' . $dataStart . ':A' . $dataEnd)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('EEF2F7');
                $sheet->getStyle('B' . $dataStart . ':J' . $dataEnd)->applyFromArray($centerStyle);
                $sheet->getStyle('E' . $dataStart . ':E' . $dataEnd)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('D4EDDA');
                $sheet->getStyle('F' . $dataStart . ':F' . $dataEnd)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFF3CD');
                $sheet->getStyle('G' . $dataStart . ':G' . $dataEnd)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E2D5F3');
            }
            $row++;
        };

        $monthsGrouped = array();
        $monthOrder = array();
        foreach ($summary['rows'] as $scanRow) {
            $monthKey = isset($scanRow['month_key']) ? (string) $scanRow['month_key'] : '';
            if ($monthKey === '') {
                $monthKey = '_none';
            }
            if (!isset($monthsGrouped[$monthKey])) {
                $monthOrder[] = $monthKey;
                $monthsGrouped[$monthKey] = array(
                    'label' => isset($scanRow['month']) ? (string) $scanRow['month'] : '',
                    'rows' => array(),
                );
            }
            $monthsGrouped[$monthKey]['rows'][] = $scanRow;
        }
        $hasMultipleMonths = count($monthOrder) > 1;

        $row = (int) $startRow;
        $sheet->setCellValue('A' . $row, 'Department & Project Manager Client Summary Report');
        $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('014B88');
        $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(24);
        $row += 2;

        if ($hasMultipleMonths) {
            $writeDeptTable($sheet, 'Consolidated', client_report_dept_kpi_consolidate_rows($summary['rows']), $row);
        }

        foreach ($monthOrder as $monthKey) {
            $monthBlock = $monthsGrouped[$monthKey];
            $monthTitle = $monthBlock['label'] !== '' ? $monthBlock['label'] : 'Month';
            $writeDeptTable($sheet, $monthTitle, $monthBlock['rows'], $row);
        }

        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(14);
        foreach (range('C', 'J') as $widthCol) {
            $sheet->getColumnDimension($widthCol)->setWidth(16);
        }
        $sheet->getSheetView()->setZoomScale(100);
        $sheet->setSelectedCell('A1');

        return $row;
    }

    /**
     * Aggregate consolidated KPI hours by department and reporting manager (team totals per manager).
     *
     * @param array $kpiReports
     * @param array $preload Optional batch from getConsolidatedReportDataBatch
     * @param array $monthYearPairs [['month'=>int,'year'=>int], ...]
     * @param array $departmentsToShow
     * @return array<string, array<string|int, array<string, mixed>>>
     */
    protected function accumulate_consolidated_dept_manager_totals(array $kpiReports, array $preload, array $monthYearPairs, array $departmentsToShow)
    {
        $monthHoursMap = array(
            1 => 178.5, 2 => 170.0, 3 => 161.5, 4 => 187.0,
            5 => 178.5, 6 => 178.5, 7 => 195.5, 8 => 170.0,
            9 => 187.0, 10 => 170.0, 11 => 170.0, 12 => 187.0,
        );
        $skipDepts = array('IT', 'HR', 'Software', 'Operations Manager', '');
        $managerTotals = array();
        $summarySeen = array();

        foreach ($kpiReports as $kpiResult) {
            if (!is_object($kpiResult)) {
                continue;
            }
            if (isset($kpiResult->empId) && isset($summarySeen[$kpiResult->empId])) {
                continue;
            }
            if (isset($kpiResult->empId)) {
                $summarySeen[$kpiResult->empId] = true;
            }

            $dept = isset($kpiResult->department) ? trim((string) $kpiResult->department) : '';
            $mgrId = isset($kpiResult->reporting_manger) ? $kpiResult->reporting_manger : '';
            if ($dept === '' || !in_array($dept, $departmentsToShow, true)) {
                continue;
            }
            if (in_array($dept, $skipDepts, true) || $mgrId === '' || $mgrId === null) {
                continue;
            }

            $monthlyTotals = array(
                'totalProd' => 0, 'totalGen' => 0, 'totalElog' => 0,
                'totalHours' => 0, 'totalWorkHrs' => 0, 'validMonths' => 0,
            );

            foreach ($monthYearPairs as $monthYear) {
                $month = (int) $monthYear['month'];
                $yearForMonth = (int) $monthYear['year'];
                if ($dept === 'MEP') {
                    $hoursStr = isset($preload['production'][$kpiResult->empId][$month][$yearForMonth])
                        ? $preload['production'][$kpiResult->empId][$month][$yearForMonth]
                        : $this->kpi_reports_model->empProductionHoursMonthWiseMEP($kpiResult->empId, $month, $yearForMonth);
                } else {
                    $hoursStr = isset($preload['production'][$kpiResult->empId][$month][$yearForMonth])
                        ? $preload['production'][$kpiResult->empId][$month][$yearForMonth]
                        : $this->kpi_reports_model->empProductionHoursMonthWiseCons($kpiResult->empId, $month, $yearForMonth);
                }
                $hours = explode('@#===', is_string($hoursStr) ? $hoursStr : '0@#===0@#===0');
                $prod = isset($hours[0]) ? (float) $hours[0] : 0;
                $gen = isset($hours[1]) ? (float) $hours[1] : 0;
                $elog = isset($hours[2]) ? (float) $hours[2] : 0;
                $total = $prod + $gen + $elog;
                $workHrs = isset($monthHoursMap[$month]) ? $monthHoursMap[$month] : 0;

                if ($workHrs > 0 && $total > 0) {
                    $monthlyTotals['totalProd'] += $prod;
                    $monthlyTotals['totalGen'] += $gen;
                    $monthlyTotals['totalElog'] += $elog;
                    $monthlyTotals['totalHours'] += $total;
                    $monthlyTotals['totalWorkHrs'] += $workHrs;
                    $monthlyTotals['validMonths']++;
                }
            }

            if ($monthlyTotals['validMonths'] == 0 || $monthlyTotals['totalHours'] <= 0) {
                continue;
            }

            if (!isset($managerTotals[$dept][$mgrId])) {
                $managerTotals[$dept][$mgrId] = array(
                    'count' => 0,
                    'totalProd' => 0, 'totalGen' => 0, 'totalElog' => 0,
                    'totalHours' => 0, 'totalWorkHrs' => 0, 'totalUtilHours' => 0,
                );
            }
            $managerTotals[$dept][$mgrId]['count']++;
            $managerTotals[$dept][$mgrId]['totalProd'] += $monthlyTotals['totalProd'];
            $managerTotals[$dept][$mgrId]['totalGen'] += $monthlyTotals['totalGen'];
            $managerTotals[$dept][$mgrId]['totalElog'] += $monthlyTotals['totalElog'];
            $managerTotals[$dept][$mgrId]['totalHours'] += $monthlyTotals['totalHours'];
            $managerTotals[$dept][$mgrId]['totalWorkHrs'] += $monthlyTotals['totalWorkHrs'];
            $managerTotals[$dept][$mgrId]['totalUtilHours'] += ($monthlyTotals['totalProd'] + $monthlyTotals['totalGen']);
        }

        return $managerTotals;
    }

    /**
     * Single employee consolidated hours (manager's own timesheet, not rolled under reporting manager).
     *
     * @param object $kpiResult
     * @param array $preload
     * @param array $monthYearPairs
     * @return array<string, mixed>|null
     */
    protected function accumulate_single_employee_consolidated_totals($kpiResult, array $preload, array $monthYearPairs)
    {
        if (!is_object($kpiResult) || empty($kpiResult->empId)) {
            return null;
        }
        $monthHoursMap = array(
            1 => 178.5, 2 => 170.0, 3 => 161.5, 4 => 187.0,
            5 => 178.5, 6 => 178.5, 7 => 195.5, 8 => 170.0,
            9 => 187.0, 10 => 170.0, 11 => 170.0, 12 => 187.0,
        );
        $dept = isset($kpiResult->department) ? trim((string) $kpiResult->department) : '';
        $monthlyTotals = array(
            'totalProd' => 0, 'totalGen' => 0, 'totalElog' => 0,
            'totalHours' => 0, 'totalWorkHrs' => 0, 'validMonths' => 0,
        );
        foreach ($monthYearPairs as $monthYear) {
            $month = (int) $monthYear['month'];
            $yearForMonth = (int) $monthYear['year'];
            if ($dept === 'MEP') {
                $hoursStr = isset($preload['production'][$kpiResult->empId][$month][$yearForMonth])
                    ? $preload['production'][$kpiResult->empId][$month][$yearForMonth]
                    : $this->kpi_reports_model->empProductionHoursMonthWiseMEP($kpiResult->empId, $month, $yearForMonth);
            } else {
                $hoursStr = isset($preload['production'][$kpiResult->empId][$month][$yearForMonth])
                    ? $preload['production'][$kpiResult->empId][$month][$yearForMonth]
                    : $this->kpi_reports_model->empProductionHoursMonthWiseCons($kpiResult->empId, $month, $yearForMonth);
            }
            $hours = explode('@#===', is_string($hoursStr) ? $hoursStr : '0@#===0@#===0');
            $prod = isset($hours[0]) ? (float) $hours[0] : 0;
            $gen = isset($hours[1]) ? (float) $hours[1] : 0;
            $elog = isset($hours[2]) ? (float) $hours[2] : 0;
            $total = $prod + $gen + $elog;
            $workHrs = isset($monthHoursMap[$month]) ? $monthHoursMap[$month] : 0;
            if ($workHrs > 0 && $total > 0) {
                $monthlyTotals['totalProd'] += $prod;
                $monthlyTotals['totalGen'] += $gen;
                $monthlyTotals['totalElog'] += $elog;
                $monthlyTotals['totalHours'] += $total;
                $monthlyTotals['totalWorkHrs'] += $workHrs;
                $monthlyTotals['validMonths']++;
            }
        }
        if ($monthlyTotals['validMonths'] == 0 || $monthlyTotals['totalHours'] <= 0) {
            return null;
        }
        return array(
            'count' => 1,
            'totalProd' => $monthlyTotals['totalProd'],
            'totalGen' => $monthlyTotals['totalGen'],
            'totalElog' => $monthlyTotals['totalElog'],
            'totalHours' => $monthlyTotals['totalHours'],
            'totalWorkHrs' => $monthlyTotals['totalWorkHrs'],
            'totalUtilHours' => $monthlyTotals['totalProd'] + $monthlyTotals['totalGen'],
        );
    }

    /**
     * Filtered manager's own hours keyed by delivery department.
     */
    protected function build_manager_self_totals_by_dept(array $kpiReports, array $preload, array $monthYearPairs, $search, array $departmentsToShow)
    {
        $profiles = kpi_consolidated_filter_manager_profiles($search);
        if (empty($profiles) || empty($monthYearPairs)) {
            return array();
        }
        $byEmpId = array();
        foreach ($kpiReports as $r) {
            if (is_object($r) && isset($r->empId)) {
                $byEmpId[$r->empId] = $r;
            }
        }
        $byDept = array();
        foreach ($profiles as $profile) {
            $empId = $profile['empId'];
            $dept = $profile['department'];
            if (!in_array($dept, $departmentsToShow, true)) {
                continue;
            }
            $kpiResult = isset($byEmpId[$empId]) ? $byEmpId[$empId] : null;
            if (!$kpiResult) {
                $kpiResult = $this->db->select('empId, department, emp_com_id, reporting_manger')
                    ->from('employee_details')
                    ->where('empId', $empId)
                    ->get()
                    ->row();
            }
            if (!$kpiResult) {
                continue;
            }
            if (empty($kpiResult->department)) {
                $kpiResult->department = $dept;
            }
            $totals = $this->accumulate_single_employee_consolidated_totals($kpiResult, $preload, $monthYearPairs);
            if ($totals !== null) {
                $byDept[$dept] = $totals;
            }
        }
        return $byDept;
    }

	/**
	 * Load client report rows using the same rules as the on-screen grid.
	 *
	 * @return array{monthWiseData: array, clientInfo: array, hasMonthWiseData: bool, monthsCovered: array, monthsDisplayText: string}
	 */
	protected function load_client_report_display_data($from_date, $to_date, $search, $department, array $grid = array())
	{
		$this->load->helper('kpi_display');
		$parsed = client_report_grid_filter_terms($grid);
		$hasStructuredGrid = !empty($parsed['client_terms']) || !empty($parsed['pm_terms']) || $parsed['project_term'] !== '';
		$dbSearch = $hasStructuredGrid ? '' : $search;
		$monthsCovered = array();
		$monthsDisplayText = '';
		if (!empty($from_date) && !empty($to_date)) {
			$startDate = new DateTime($from_date);
			$endDate = new DateTime($to_date);
			$currentDate = clone $startDate;

			while ($currentDate <= $endDate) {
				$monthKey = $currentDate->format('Y-m');
				$monthLabel = $currentDate->format('F Y');
				$monthShort = $currentDate->format('F');
				$monthYear = $currentDate->format('Y');

				if (!isset($monthsCovered[$monthKey])) {
					$monthsCovered[$monthKey] = array(
						'label' => $monthLabel,
						'short' => $monthShort,
						'year' => $monthYear,
					);
				}

				$currentDate->modify('first day of next month');
			}

			if (count($monthsCovered) > 0) {
				$monthLabels = array_values($monthsCovered);
				if (count($monthLabels) == 1) {
					$monthsDisplayText = $monthLabels[0]['short'] . ' - ' . $monthLabels[0]['year'];
				} else {
					$firstMonth = $monthLabels[0]['short'] . ' - ' . $monthLabels[0]['year'];
					$lastMonth = $monthLabels[count($monthLabels) - 1]['short'] . ' - ' . $monthLabels[count($monthLabels) - 1]['year'];

					if ($monthLabels[0]['year'] == $monthLabels[count($monthLabels) - 1]['year']) {
						$monthsDisplayText = $monthLabels[0]['short'] . ' to ' . $monthLabels[count($monthLabels) - 1]['short'] . ' - ' . $monthLabels[0]['year'];
					} else {
						$monthsDisplayText = $firstMonth . ' to ' . $lastMonth;
					}
				}
			}
		}

		$monthWiseData = array();
		$clientInfo = array();
		$hasMonthWiseData = false;

		if (!empty($from_date) && !empty($to_date)) {
			$startDate = new DateTime($from_date);
			$endDate = new DateTime($to_date);
			$startMonth = (int) $startDate->format('n');
			$startYear = (int) $startDate->format('Y');
			$endMonth = (int) $endDate->format('n');
			$endYear = (int) $endDate->format('Y');

			$spansMultipleMonths = ($startYear != $endYear) || ($startMonth != $endMonth);

			if ($spansMultipleMonths) {
				$allMonthRows = $this->kpi_reports_model->getAllClientInformation('', $dbSearch, $from_date, $to_date, $department, true);
				$monthGroupedByKey = array();
				foreach ($allMonthRows as $row) {
					$monthKey = isset($row->report_month) ? (string) $row->report_month : '';
					if ($monthKey === '') {
						continue;
					}
					if (!isset($monthGroupedByKey[$monthKey])) {
						$monthGroupedByKey[$monthKey] = array();
					}
					$cid = $row->client_Id;
					if (!isset($monthGroupedByKey[$monthKey][$cid])) {
						$monthGroupedByKey[$monthKey][$cid] = array(
							'client_name' => $row->client_name,
							'department' => $row->department,
							'clientpm' => $row->clientpm,
							'client_pm_name' => isset($row->client_pm_name) ? $row->client_pm_name : '',
							'client_start_date' => isset($row->client_start_date) ? $row->client_start_date : '',
							'client_end_date' => isset($row->client_end_date) ? $row->client_end_date : '',
							'projects' => array(),
						);
					}
					$monthGroupedByKey[$monthKey][$cid]['projects'][] = $row;
				}

				foreach ($monthsCovered as $monthKey => $monthInfo) {
					if (empty($monthGroupedByKey[$monthKey])) {
						continue;
					}
					$monthGrouped = $monthGroupedByKey[$monthKey];
					uasort($monthGrouped, function ($a, $b) {
						return strcasecmp(
							isset($a['client_name']) ? (string) $a['client_name'] : '',
							isset($b['client_name']) ? (string) $b['client_name'] : ''
						);
					});
					$parts = explode('-', $monthKey);
					$y = isset($parts[0]) ? (int) $parts[0] : (int) $monthInfo['year'];
					$m = isset($parts[1]) ? (int) $parts[1] : 1;
					$monthWiseData[$monthKey] = array(
						'label' => $monthInfo['label'],
						'from_date' => sprintf('%04d-%02d-01', $y, $m),
						'to_date' => date('Y-m-t', mktime(0, 0, 0, $m, 1, $y)),
						'data' => $monthGrouped,
					);
				}

				$hasMonthWiseData = !empty($monthWiseData);
			} else {
				$clientInfo = $this->kpi_reports_model->getAllClientInformation('', $dbSearch, $from_date, $to_date, $department);
			}
		} else {
			$clientInfo = $this->kpi_reports_model->getAllClientInformation('', $dbSearch, $from_date, $to_date, $department);
		}

		return array(
			'monthWiseData' => $monthWiseData,
			'clientInfo' => $clientInfo,
			'hasMonthWiseData' => $hasMonthWiseData,
			'monthsCovered' => $monthsCovered,
			'monthsDisplayText' => $monthsDisplayText,
		);
	}

	/**
	 * Group flat client report rows by client Id (matches kpi-client-report.php view).
	 *
	 * @param array $clientInfo
	 * @return array
	 */
	protected function group_client_report_rows(array $clientInfo)
	{
		$grouped = array();
		foreach ($clientInfo as $row) {
			$grouped[$row->client_Id]['client_name'] = $row->client_name;
			$grouped[$row->client_Id]['department'] = $row->department;
			$grouped[$row->client_Id]['clientpm'] = $row->clientpm;
			$grouped[$row->client_Id]['client_pm_name'] = isset($row->client_pm_name) ? $row->client_pm_name : '';
			if (!isset($grouped[$row->client_Id]['client_start_date'])) {
				$grouped[$row->client_Id]['client_start_date'] = isset($row->client_start_date) ? $row->client_start_date : '';
				$grouped[$row->client_Id]['client_end_date'] = isset($row->client_end_date) ? $row->client_end_date : '';
			} else {
				$rowStartTs = client_report_client_date_ts(isset($row->client_start_date) ? $row->client_start_date : '');
				$existingStartTs = client_report_client_date_ts($grouped[$row->client_Id]['client_start_date']);
				if ($rowStartTs !== false && ($existingStartTs === false || $rowStartTs < $existingStartTs)) {
					$grouped[$row->client_Id]['client_start_date'] = $row->client_start_date;
				}
				$rowEndTs = client_report_client_date_ts(isset($row->client_end_date) ? $row->client_end_date : '');
				$existingEndTs = client_report_client_date_ts($grouped[$row->client_Id]['client_end_date']);
				if ($rowEndTs !== false && ($existingEndTs === false || $rowEndTs > $existingEndTs)) {
					$grouped[$row->client_Id]['client_end_date'] = $row->client_end_date;
				}
			}
			$grouped[$row->client_Id]['projects'][] = $row;
		}
		if (!empty($grouped)) {
			uasort($grouped, function ($a, $b) {
				$nameA = isset($a['client_name']) ? (string) $a['client_name'] : '';
				$nameB = isset($b['client_name']) ? (string) $b['client_name'] : '';
				return strcasecmp($nameA, $nameB);
			});
		}
		return $grouped;
	}

	/**
	 * Load and filter client report grouped data for export (matches on-screen grid filters).
	 *
	 * @param array|null $reportData Optional pre-loaded result from load_client_report_display_data()
	 * @return array{monthWiseData: array, grouped: array, hasMonthWiseData: bool}
	 */
	protected function prepare_client_report_export_data($from_date, $to_date, $search, $department, array $grid, array $reportData = null)
	{
		if ($reportData === null) {
			$reportData = $this->load_client_report_display_data($from_date, $to_date, $search, $department, $grid);
		}
		$monthWiseData = array();
		$grouped = array();

		if ($reportData['hasMonthWiseData']) {
			foreach ($reportData['monthWiseData'] as $monthKey => $monthData) {
				$filtered = $this->filter_client_report_grouped_data(
					isset($monthData['data']) ? $monthData['data'] : array(),
					$grid,
					$search
				);
				if (empty($filtered)) {
					continue;
				}
				$monthData['data'] = $filtered;
				$monthWiseData[$monthKey] = $monthData;
			}
			return array(
				'monthWiseData' => $monthWiseData,
				'grouped' => array(),
				'hasMonthWiseData' => !empty($monthWiseData),
			);
		}

		$grouped = $this->group_client_report_rows($reportData['clientInfo']);
		$grouped = $this->filter_client_report_grouped_data($grouped, $grid, $search);

		return array(
			'monthWiseData' => array(),
			'grouped' => $grouped,
			'hasMonthWiseData' => false,
		);
	}

	/**
	 * Flatten filtered client-report grouped/month-wise data into project rows (matches on-screen grid).
	 *
	 * @param array $prepared Result from prepare_client_report_export_data()
	 * @return array
	 */
	protected function flatten_client_report_prepared_projects(array $prepared)
	{
		$rows = array();
		if (!empty($prepared['hasMonthWiseData']) && !empty($prepared['monthWiseData']) && is_array($prepared['monthWiseData'])) {
			foreach ($prepared['monthWiseData'] as $monthData) {
				if (empty($monthData['data']) || !is_array($monthData['data'])) {
					continue;
				}
				foreach ($monthData['data'] as $clientData) {
					if (empty($clientData['projects']) || !is_array($clientData['projects'])) {
						continue;
					}
					foreach ($clientData['projects'] as $proj) {
						$rows[] = $proj;
					}
				}
			}
			return $rows;
		}

		if (!empty($prepared['grouped']) && is_array($prepared['grouped'])) {
			foreach ($prepared['grouped'] as $clientData) {
				if (empty($clientData['projects']) || !is_array($clientData['projects'])) {
					continue;
				}
				foreach ($clientData['projects'] as $proj) {
					$rows[] = $proj;
				}
			}
		}

		return $rows;
	}

	/**
	 * Build department KPI summary from the same filtered rows as the client-report grid.
	 *
	 * @param string $from_date
	 * @param string $to_date
	 * @param mixed $department
	 * @param string $search
	 * @param array $grid
	 * @param array|null $reportData Optional pre-loaded load_client_report_display_data() result
	 * @return array{rows: array, has_data: bool}
	 */
	protected function build_client_report_dept_kpi_summary($from_date, $to_date, $department, $search, array $grid, array $reportData = null, array $prepared = null)
	{
		if ($prepared === null) {
			if ($reportData === null) {
				$reportData = $this->load_client_report_display_data($from_date, $to_date, $search, $department, $grid);
			}
			$prepared = $this->prepare_client_report_export_data($from_date, $to_date, $search, $department, $grid, $reportData);
		}
		$projectRows = $this->flatten_client_report_prepared_projects($prepared);

		return $this->kpi_reports_model->getClientReportDepartmentKpiSummary(
			$from_date,
			$to_date,
			$department,
			$search,
			$grid,
			$projectRows
		);
	}

	/**
	 * Shared GET parsing for client report and deferred department KPI JSON.
	 *
	 * @return array{department: mixed, from_date: mixed, to_date: mixed, search: string, grid: array}
	 */
	protected function client_report_core_filters_from_get() {
		$department = $this->input->get('department');
		if (!empty($department)) {
			if (is_array($department)) {
				$department = array_values(array_filter($department, function ($d) {
					return $d !== '' && $d !== '__all__';
				}));
				$department = !empty($department) ? $department : '';
			} elseif (is_string($department) && $department !== '__all__') {
				$department = array_values(array_filter(array_map('trim', explode(',', $department)), function ($d) {
					return $d !== '' && $d !== '__all__';
				}));
				$department = !empty($department) ? $department : '';
			} else {
				$department = '';
			}
		} else {
			$department = '';
		}
		$from_date = $this->input->get('from_date');
		$to_date = $this->input->get('to_date');
		$from_year = $this->input->get('from_year');
		$from_month = $this->input->get('from_month');
		$to_year = $this->input->get('to_year');
		$to_month = $this->input->get('to_month');
		if ($from_year !== null && strtoupper(trim((string) $from_year)) === 'ALL') {
			$from_month = '';
		}
		if ($to_year !== null && strtoupper(trim((string) $to_year)) === 'ALL') {
			$to_month = '';
		}

		$hasYearMonth = ($from_year !== null && $from_year !== '' && $from_month !== null && $from_month !== ''
			&& $to_year !== null && $to_year !== '' && $to_month !== null && $to_month !== '');

		$hasYearOnly = ($from_year !== null && $from_year !== ''
			&& $to_year !== null && $to_year !== ''
			&& !$hasYearMonth);

		if (!$hasYearMonth && !$hasYearOnly) {
			$monthIdRaw = $this->input->get('month_id');
			$yearParam = $this->input->get('year');
			
			// Only process month_id if it's explicitly different from current month
			// Otherwise, default to previous month
			$currentMonth = (int) date('n');
			$m = ($monthIdRaw !== null && $monthIdRaw !== '') ? (int) $monthIdRaw : $currentMonth;
			
			if ((empty($from_date) || empty($to_date)) && $m !== $currentMonth && $m >= 1 && $m <= 12) {
				// month_id specified and it's not current month - use it
				$y = ($yearParam !== null && $yearParam !== '') ? (int) $yearParam : (int) date('Y');
				if ($y < 2000 || $y > 2100) {
					$y = (int) date('Y');
				}
				$from_date = sprintf('%04d-%02d-01', $y, $m);
				$to_date = date('Y-m-t', strtotime($from_date));
			}

			if (empty($from_date) && empty($to_date)) {
				// Default to previous month instead of current month
				$from_date = date('Y-m-01', strtotime('first day of previous month'));
				$to_date = date('Y-m-t', strtotime('first day of previous month'));
			}
		}

		$resolved = ($hasYearMonth || $hasYearOnly)
			? $this->_resolve_kpi_consolidated_dates(null, null, $from_year, $from_month, $to_year, $to_month)
			: $this->_resolve_kpi_consolidated_dates($from_date, $to_date, null, null, null, null);

		$grid = $this->client_report_grid_filters_from_request();
		$search = $grid['search_merged'];
		if ($search === '') {
			$search = trim((string) $this->input->get('search'));
		}

		return array(
			'department' => $department,
			'from_date' => $resolved['from_date'],
			'to_date' => $resolved['to_date'],
			'from_year' => $resolved['from_year'],
			'from_month' => $resolved['from_month'],
			'to_year' => $resolved['to_year'],
			'to_month' => $resolved['to_month'],
			'search' => $search,
			'grid' => $grid,
		);
	}

public function clientReport() {

	if ($this->input->get('cleared') === '1') {
		redirect('kpi_reports/clientReport', 'location', 302);
		return;
	}

	$core = $this->client_report_core_filters_from_get();
	$department = $core['department'];
	$from_date = $core['from_date'];
	$to_date = $core['to_date'];
	$search = $core['search'];
	$grid = $core['grid'];

	$reportData = $this->load_client_report_display_data($from_date, $to_date, $search, $department, $grid);
	$monthsCovered = $reportData['monthsCovered'];
	$monthsDisplayText = $reportData['monthsDisplayText'];

	$prepared = $this->prepare_client_report_export_data($from_date, $to_date, $search, $department, $grid, $reportData);
	if ($prepared['hasMonthWiseData']) {
		$data['monthWiseData'] = $prepared['monthWiseData'];
		$data['clientInfo'] = array();
		$data['grouped'] = array();
	} else {
		$data['clientInfo'] = array();
		$data['monthWiseData'] = array();
		$data['grouped'] = $prepared['grouped'];
	}
    
    $data['search'] = $search;
    $data['department'] = $department;
    $data['clients_filter'] = $grid['clients'];
    $data['pms_filter'] = $grid['pms'];
    $data['project_filter'] = $grid['project'];
    if (empty($data['clients_filter']) && empty($data['pms_filter']) && $data['project_filter'] === '' && $search !== '') {
        $data['clients_filter'] = array_values(array_filter(array_map('trim', explode(',', $search)), function ($x) {
            return $x !== '';
        }));
    }
    $data['from_date'] = $from_date;
    $data['to_date'] = $to_date;
    $data['from_year'] = $core['from_year'];
    $data['from_month'] = $core['from_month'];
    $data['to_year'] = $core['to_year'];
    $data['to_month'] = $core['to_month'];
    $data['monthsCovered'] = $monthsCovered;
    $data['monthsDisplayText'] = $monthsDisplayText;
    $data['pagination_links'] = ''; // No pagination - show all data

    // kpi-client-report does not gate quality columns on datesMatch; skip COUNT to speed initial load.
    $data['datesMatch'] = true;

    $this->load->helper('kpi_display');
    $data['deptKpiSummary'] = $this->build_client_report_dept_kpi_summary(
        $from_date,
        $to_date,
        $department,
        $search,
        $grid,
        $reportData,
        $prepared
    );

    // Load view
    $this->load->view('kpi-reports/kpi-client-report', $data);
             
}

	/**
	 * Deferred department KPI summary for client report (same filters as main page).
	 * Loaded after the main grid so the first paint is faster.
	 */
	public function clientReportDepartmentKpiJson() {
		$core = $this->client_report_core_filters_from_get();
		$summary = $this->build_client_report_dept_kpi_summary(
			$core['from_date'],
			$core['to_date'],
			$core['department'],
			$core['search'],
			$core['grid']
		);
		$payload = array(
			'has_data' => !empty($summary['has_data']),
			'rows' => isset($summary['rows']) ? $summary['rows'] : array(),
		);
		$this->output
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($payload));
	}

public function clientconsReport() {
        $department = $this->input->get('department');
        if (!empty($department)) {
            if (is_array($department)) {
                $department = array_values(array_filter($department, function ($d) {
                    return $d !== '' && $d !== '__all__';
                }));
                $department = !empty($department) ? $department : '';
            } elseif (is_string($department) && $department !== '__all__') {
                $department = array_values(array_filter(array_map('trim', explode(',', $department)), function ($d) {
                    return $d !== '' && $d !== '__all__';
                }));
                $department = !empty($department) ? $department : '';
            } else {
                $department = '';
            }
        } else {
            $department = '';
        }

        $grid = $this->client_report_grid_filters_from_request();
        $search = $grid['search_merged'];
        if ($search === '') {
            $legacySearch = $this->input->get('search');
            if (!empty($legacySearch)) {
                $search = trim((string) $legacySearch);
            }
        }
        $from_date = $this->input->get('from_date'); // From date
        $to_date = $this->input->get('to_date'); // To date
    
        // For data retrieval: use January to current month if dates not provided
        $data_from_date = $from_date;
        $data_to_date = $to_date;
        if (empty($from_date) || empty($to_date)) {
            $currentMonth = date('n');
            $currentYear = date('Y');
            $data_from_date = date('Y-01-01'); // January 1st of current year
            $data_to_date = date('Y-m-t', mktime(0, 0, 0, $currentMonth, 1, $currentYear)); // Last day of current month
        }
    
        // Load all rows in one query (no pagination); avoids extra count/AJAX round-trip
        $data['clientCons'] = $this->kpi_reports_model->ClientInformationConsolidated(0, 0, $search, $data_from_date, $data_to_date, $department);
        $data['search'] = $search;
        $data['department'] = $department;
        $data['clients_filter'] = $grid['clients'];
        $data['pms_filter'] = $grid['pms'];
        $data['project_filter'] = $grid['project'];
        if (empty($data['clients_filter']) && empty($data['pms_filter']) && $data['project_filter'] === '' && $search !== '') {
            $data['clients_filter'] = array_values(array_filter(array_map('trim', explode(',', $search)), function ($x) {
                return $x !== '';
            }));
        }
        // Pass dates to view - if empty, use default (January to current month) for display
        $data['from_date'] = !empty($from_date) ? $from_date : $data_from_date;
        $data['to_date'] = !empty($to_date) ? $to_date : $data_to_date;
        // Always show grid and enable download - quality error columns will show empty/zero if no match
        $data['datesMatch'] = true;

        // Department KPI loads after first paint via AJAX (same endpoint as monthly client report)
        $data['departmentKpiSummary'] = array('rows' => array(), 'has_data' => false);

        // Load view
        $this->load->view('kpi-reports/kpi-client-cons-report', $data);
//    }
             
}

   public function autosuggest_employee_names()
{
    $term = $this->input->get('term'); // this is the user input from the search bar (AJAX)
    
    if (!empty($term)) {
        $suggestions = $this->kpi_reports_model->get_employee_suggestions($term);
        echo json_encode($suggestions); // return the suggestions in JSON format
    }
}
    
       public function autosuggest_employee_names_arch()
{
    $term = $this->input->get('term'); // this is the user input from the search bar (AJAX)
    
    if (!empty($term)) {
        $suggestions = $this->kpi_reports_model->get_employee_suggestions_arch($term);
        echo json_encode($suggestions); // return the suggestions in JSON format
    }
}
 
           public function autosuggest_employee_names_cons()
{
    $term = trim((string) $this->input->get('term'));
    $suggestions = $this->kpi_reports_model->get_employee_suggestions_cons($term);
    echo json_encode($suggestions);
}
 
    
    public function autosuggest_project_names()
{
    $term = $this->input->get('term');
    if ($term === null) {
        $term = '';
    }
    $term = trim((string)$term);
    $context = strtolower(trim((string)$this->input->get('context')));

    $department = $this->input->get('department');
    $deptArr = array();
    if (is_array($department)) {
        foreach ($department as $d) {
            $t = trim((string)$d);
            if ($t !== '' && strcasecmp($t, '__all__') !== 0) {
                $deptArr[] = $t;
            }
        }
    } elseif (is_string($department) && trim($department) !== '' && strcasecmp(trim($department), '__all__') !== 0) {
        foreach (array_map('trim', explode(',', $department)) as $t) {
            if ($t !== '' && strcasecmp($t, '__all__') !== 0) {
                $deptArr[] = $t;
            }
        }
    }

    $clientsRaw = $this->input->get('clients');
    $clientsArr = array();
    if (is_array($clientsRaw)) {
        foreach ($clientsRaw as $c) {
            $t = trim((string)$c);
            if ($t !== '') {
                $clientsArr[] = $t;
            }
        }
    } elseif (is_string($clientsRaw) && trim($clientsRaw) !== '') {
        foreach (array_map('trim', explode(',', $clientsRaw)) as $t) {
            if ($t !== '') {
                $clientsArr[] = $t;
            }
        }
    }

    $activeClients = null;
    if ($context === 'clients') {
        $suggestions = $this->kpi_reports_model->get_client_report_client_suggestions($term, $deptArr);
        if ($term === '') {
            $activeClients = $this->kpi_reports_model->count_client_report_clients_for_suggest($deptArr);
        }
    } elseif ($context === 'projects') {
        $suggestions = $this->kpi_reports_model->get_client_report_project_suggestions($term, $clientsArr);
    } elseif ($context === 'managers') {
        $suggestions = $this->kpi_reports_model->get_client_report_manager_suggestions_formatted($term);
    } else {
        $suggestions = $this->kpi_reports_model->get_project_suggestions($term);
        if ($term === '') {
            $activeClients = $this->kpi_reports_model->count_active_clients_for_suggest();
        }
    }

    $this->output->set_content_type('application/json', 'utf-8');
    echo json_encode(array(
        'suggestions' => array_values($suggestions),
        'active_clients_count' => $activeClients,
    ));
}
 
    

public function generateMonthWiseEmpDataExcel_mep()
{
    // Get from_date and to_date instead of month_id
    $from_date = $this->input->get('from_date');
    $to_date = $this->input->get('to_date');
    $search = $this->input->get('search');
    $empWiseKpi = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];

    // Set default dates if none selected (current month)
    if (empty($from_date) || empty($to_date)) {
        $currentMonth = date('n');
        $currentYear = date('Y');
        $from_date = date('Y-m-01', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
        $to_date = date('Y-m-t', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
    }

    // Convert date range to months array
    $monthName = [];
    if (!empty($from_date) && !empty($to_date)) {
        $startDate = new DateTime($from_date);
        $endDate = new DateTime($to_date);
        $currentDate = clone $startDate;
        $currentDate->modify('first day of this month');
        
        while ($currentDate <= $endDate) {
            $monthName[] = (int)$currentDate->format('n');
            $currentDate->modify('+1 month');
        }
        
        if (empty($monthName)) {
            $monthName = [date('n')];
        }
    } else {
        $monthName = [date('n')];
    }

    if (!empty($monthName)) {
        // Skip quality validation when called from generateMonthWiseEmpDataExcel (same as grid)
        $skipQualityValidation = ($this->input->get('skip_quality_validation') === '1');
        if (!$skipQualityValidation && !$this->kpi_reports_model->validateQualityErrorLogDates($monthName)) {
            echo "Quality error log data does not match the selected dates. Excel download is not available.";
            exit();
        }
        
        // Fetch all data for Excel export - use same method as view to ensure all data is exported
        // Use getkpiInformation with large limit to get all records matching the filters (same as view)
        $allKpiReports = $this->kpi_reports_model->getkpiInformation(999999, 0, '', $search, $from_date, $to_date);
        if (!empty($allKpiReports) && is_array($allKpiReports)) {
            usort($allKpiReports, function($a, $b) {
                $nameA = isset($a->name) ? (string)$a->name : '';
                $nameB = isset($b->name) ? (string)$b->name : '';
                return strcasecmp($nameA, $nameB);
            });
        }
        $sessionData = $this->session->userdata('logged_in_timesheet');
        $userType = isset($sessionData['user_type']) ? strtolower($sessionData['user_type']) : '';
        $months = [
            '1' => 'Jan', '2' => 'Feb', '3' => 'Mar', '4' => 'Apr', '5' => 'May', '6' => 'Jun',
            '7' => 'Jul', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
        ];
        
        // Full month names for display
        $fullMonthNames = [
            '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
            '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
            '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];
        
        // Determine month loop range for grouping (matching view logic exactly)
        if ($userType == 'developer') {
            $monthLoopRange = range(1, date('n') - 1);
        } else {
            if (is_array($monthName)) {
                // Ensure all values are integers and filter out non-numeric values
                $monthLoopRange = array_map('intval', array_filter($monthName, 'is_numeric'));
            } else {
                $monthLoopRange = [(int)$monthName];
            }
            
            // Ensure monthLoopRange is not empty
            if (empty($monthLoopRange)) {
                $monthLoopRange = [date('n')];
            }
        }

        // Load the excel library
        $this->load->library('excel');
        $objPHPExcel = $this->excel;

        // Generate filename and title based on selected months
        if (is_array($monthName) && count($monthName) > 1) {
            $monthNames = array_map(function($m) use ($months) { 
                return isset($months[$m]) ? $months[$m] : ''; 
            }, $monthName);
            $titleSuffix = implode('_', array_map('strtolower', $monthNames));
            $titleDisplay = implode(', ', array_map('strtolower', $monthNames));
        } else {
            $singleMonth = is_array($monthName) ? $monthName[0] : $monthName;
            $titleSuffix = isset($months[$singleMonth]) ? strtolower($months[$singleMonth]) : $singleMonth;
            $titleDisplay = isset($months[$singleMonth]) ? $months[$singleMonth] : $singleMonth;
        }

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("eLogic")
                                    ->setLastModifiedBy("eLogic")
                                    ->setTitle("Employee KPI Report - " . $titleDisplay)
                                    ->setSubject("Employee KPI Report")
                                    ->setDescription("MEP Employee KPI Report for the selected month(s)")
                                    ->setKeywords("kpi report employee")
                                    ->setCategory("Report");

        // **Calculate Averages for the Summary Table**
        $totalProductivity = 0;
        $totalProjectGeneral = 0;
        $totalElogicGeneral = 0;
        $totalAvailability = 0;
        $totalUtilization = 0;
        $validEmployeeCount = 0;

        foreach ($allKpiReports as $kpiResult) {
            // Handle multiple months - use selected months for non-developers
            if ($userType == 'developer') {
                $monthLoopRange = range(1, date('n') - 1);
            } else {
                $monthLoopRange = is_array($monthName) ? $monthName : [$monthName];
            }
            foreach ($monthLoopRange as $currentMonth) {
                // Calculate year for the month based on date range
                $monthYearForSummary = date('Y');
                if (!empty($from_date) && !empty($to_date)) {
                    $startDate = new DateTime($from_date);
                    $endDate = new DateTime($to_date);
                    $currentDate = clone $startDate;
                    $currentDate->modify('first day of this month');
                    
                    while ($currentDate <= $endDate) {
                        $currentMonthNum = (int)$currentDate->format('n');
                        if ($currentMonthNum == $currentMonth) {
                            $monthYearForSummary = (int)$currentDate->format('Y');
                            break;
                        }
                        $currentDate->modify('+1 month');
                    }
                }
                
                // Use MEP-specific function with year parameter to match view
                $getTotalProductionH = $this->kpi_reports_model->empProductionHoursMonthWiseMEP($kpiResult->empId, $currentMonth, $monthYearForSummary);
                $productionHoursArray = explode('@#===', $getTotalProductionH);
                $totalProductionHours = !empty($productionHoursArray[0]) ? $productionHoursArray[0] : 0;
                $totalEmpProductionGeneralHours = isset($productionHoursArray[1]) ? $productionHoursArray[1] : 0;
                $totalEmpGeneralHours = isset($productionHoursArray[2]) ? $productionHoursArray[2] : 0;
                $totalHours = array_sum([$totalProductionHours, $totalEmpGeneralHours, $totalEmpProductionGeneralHours]);

                if ($totalHours > 0) {
                    $totalProductivity += ($totalProductionHours / $totalHours) * 100;
                    $totalProjectGeneral += ($totalEmpProductionGeneralHours / $totalHours) * 100;
                    $totalElogicGeneral += ($totalEmpGeneralHours / $totalHours) * 100;

                    // Set monthWorkingHours (same logic as before)
                    switch ($currentMonth):
                        case '1': $monthWorkingHours = 178.5; break;
                        case '2': $monthWorkingHours = 170.0; break;
                        case '3': $monthWorkingHours = 161.5; break;
                        case '4': $monthWorkingHours = 187.0; break;
                        case '5': $monthWorkingHours = 178.5; break;
                        case '6': $monthWorkingHours = 178.5; break;
                        case '7': $monthWorkingHours = 195.5; break;
                        case '8': $monthWorkingHours = 170.0; break;
                        case '9': $monthWorkingHours = 187.0; break;
                        case '10': $monthWorkingHours = 170.0; break;
                        case '11': $monthWorkingHours = 170.0; break;
                        case '12': $monthWorkingHours = 187.0; break;
                        default: $monthWorkingHours = 0; break;
                    endswitch;
                    $totalAvailability += ($totalHours / $monthWorkingHours) * 100;
                    $totalUtilization += (($totalProductionHours + $totalEmpProductionGeneralHours) / $totalHours) * 100;
                    $validEmployeeCount++;
                }
            }
        }

        $avgProductivity = ($validEmployeeCount > 0) ? $totalProductivity / $validEmployeeCount : 0;
        $avgProjectGeneral = ($validEmployeeCount > 0) ? $totalProjectGeneral / $validEmployeeCount : 0;
        $avgElogicGeneral = ($validEmployeeCount > 0) ? $totalElogicGeneral / $validEmployeeCount : 0;
        $avgAvailability = ($validEmployeeCount > 0) ? $totalAvailability / $validEmployeeCount : 0;
        $avgUtilization = ($validEmployeeCount > 0) ? $totalUtilization / $validEmployeeCount : 0;

        // **Create Title Row with Date Range and Search Name**
        $titleRow = 0;
        $fullMonthNames = [
            '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
            '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
            '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];
        
        // Format date range for title
        if (!empty($from_date) && !empty($to_date)) {
            $fromDateObj = new DateTime($from_date);
            $toDateObj = new DateTime($to_date);
            $fromMonth = $fullMonthNames[$fromDateObj->format('n')];
            $fromYear = $fromDateObj->format('Y');
            $toMonth = $fullMonthNames[$toDateObj->format('n')];
            $toYear = $toDateObj->format('Y');
            
            if ($fromMonth === $toMonth && $fromYear === $toYear) {
                $dateText = $fromMonth . " " . $fromYear;
            } else {
                $dateText = $fromMonth . " " . $fromYear . " to " . $toMonth . " " . $toYear;
            }
        } else {
            $currentMonth = date('n');
            $currentYear = date('Y');
            $dateText = $fullMonthNames[$currentMonth] . " " . $currentYear;
        }
        
        // Build title with search name if available
        $titleText = $dateText . " MEP KPI Report";
        if (!empty($search)) {
            $titleText .= " ( " . $search . " )";
        }
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $titleRow, $titleText);
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $titleRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $titleRow)->getFont()->setSize(14);

        // **Create Summary Table**
        $summaryHeaders = ['Departments', 'Productivity%', 'Project General%', 'eLogic General%', 'Availability%', 'Utilization%'];
        $summaryData = ['MEP', round($avgProductivity) . '%', round($avgProjectGeneral) . '%', round($avgElogicGeneral) . '%', round($avgAvailability) . '%', round($avgUtilization) . '%'];

        $summaryStartRow = 1;
        $summaryStartColumn = 0;
        $summaryEndColumn = count($summaryHeaders) - 1;
        $summaryEndRow = $summaryStartRow + 1; // Header row + 1 data row

        // Write Summary Headers
        for ($i = 0; $i < count($summaryHeaders); $i++) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($summaryStartColumn + $i, $summaryStartRow, $summaryHeaders[$i]);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($summaryStartColumn + $i, $summaryStartRow)->getFont()->setBold(true);
        }
        // Summary header row background color
        $summaryHeaderRange = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($summaryStartColumn, $summaryStartRow)->getCoordinate() . ':' .
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($summaryEndColumn, $summaryStartRow)->getCoordinate();
        $objPHPExcel->getActiveSheet()->getStyle($summaryHeaderRange)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
        $objPHPExcel->getActiveSheet()->getStyle($summaryHeaderRange)->getFont()->getColor()->setARGB('FFFFFFFF');

        // Write Summary Data
        for ($i = 0; $i < count($summaryData); $i++) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($summaryStartColumn + $i, $summaryStartRow + 1, $summaryData[$i]);
        }

        // **Apply Borders to Summary Table**
        $styleSummaryBorders = array(
            'borders' => array(
                'outline' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
                'inside' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
                'allborders' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
            ),
        );
        
        $startCell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($summaryStartColumn, $summaryStartRow)->getCoordinate();
        $endCell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($summaryEndColumn, $summaryEndRow)->getCoordinate();
        $objPHPExcel->getActiveSheet()->getStyle($startCell . ':' . $endCell)->applyFromArray($styleSummaryBorders);

        // Add headers for the main report (start from row 4 + 2 = row 6 now, after the summary table and 2 empty rows)
        $headers = [];
        $columnIndex = 0;
        $reportStartRow = $summaryEndRow + 5; // +5 to leave room for legend + two merged rows above header
        $reportStartColumn = 0;

        // Add Legend Row above the table headers
        $legendRow = $summaryEndRow + 1;
        
        // Build legend text with all abbreviations
        $legendText = '';
        $legendItems = [
            ['RM', 'Reporting Manager'],
            ['ID', 'Employee ID'],
            ['EE', 'Employee Name'],
            ['P', 'Productive Hours'],
            ['PG', 'Project General Hours'],
            ['E', 'General Hours'],
            ['AV', 'Total Available Hours'],
            ['BH', 'Business Hours'],
            ['P%', 'Productive Hours%'],
            ['E%', 'General Hours%'],
            ['U%', 'Utilization%'],
            ['AV%', 'Availability%'],
            ['PG%', 'Project General%'],
            ['QA', 'Quality Accuracy'],
            ['PA', 'Process Adherence'],
            ['A', 'UPL and Attend not updated'],
            ['L/E', 'No of Late and Early Login'],
            ['A&B', 'Above and Beyond'],
        ];
        
        $legendParts = [];
        foreach ($legendItems as $item) {
            $legendParts[] = $item[0] . ' - ' . $item[1];
        }
        $legendText = implode(' | ', $legendParts);
        
        // Calculate how many columns the report will span
        $reportColumnCount = ($userType != 'developer') ? 26 : 23; // Approximate column count
        
        // Set legend text in first column and merge across all report columns
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $legendRow, $legendText);
        $objPHPExcel->getActiveSheet()->mergeCells(
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $legendRow)->getCoordinate() . ':' .
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($reportColumnCount - 1, $legendRow)->getCoordinate()
        );
        
        // Style the legend row
        $legendCell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $legendRow)->getCoordinate();
        $objPHPExcel->getActiveSheet()->getStyle($legendCell)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle($legendCell)->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle($legendCell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($legendCell)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF8F9FA'); // Light gray background
        
        // Apply text wrapping
        $objPHPExcel->getActiveSheet()->getStyle($legendCell)->getAlignment()->setWrapText(true);

        // Two merged rows above the report header (merge all columns)
        $mergedRow1 = $legendRow + 1;
        $mergedRow2 = $legendRow + 2;
        $objPHPExcel->getActiveSheet()->mergeCells(
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $mergedRow1)->getCoordinate() . ':' .
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($reportColumnCount - 1, $mergedRow1)->getCoordinate()
        );
        $objPHPExcel->getActiveSheet()->mergeCells(
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $mergedRow2)->getCoordinate() . ':' .
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($reportColumnCount - 1, $mergedRow2)->getCoordinate()
        );

        if ($userType != 'developer') {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'RM');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'ID');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'EE');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Month');
        } else {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Month');
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'P');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'PG');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'E');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'AV');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'BH');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'P%');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'E%');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'U%');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'AV%');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Score'); // Availability Score
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'PG%');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Score'); // Project General Score
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'QA');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Score'); // Quality Score
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'PA');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Score'); // Process Adherence Score
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'A');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Score'); // Attendance Score
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'L/E');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Score'); // No of Late and Early Login Score
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'A&B');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Score'); // Above and Beyond Score
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Total');

        // Apply bold style and header background color to the report headers
        $headerEndColumn = $columnIndex - 1;
        for ($col = $reportStartColumn; $col <= $headerEndColumn; $col++) {
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $reportStartRow - 1)->getFont()->setBold(true);
        }
        $reportHeaderRange = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($reportStartColumn, $reportStartRow - 1)->getCoordinate() . ':' .
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($headerEndColumn, $reportStartRow - 1)->getCoordinate();
        $objPHPExcel->getActiveSheet()->getStyle($reportHeaderRange)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
        $objPHPExcel->getActiveSheet()->getStyle($reportHeaderRange)->getFont()->getColor()->setARGB('FFFFFFFF');

        $row = $reportStartRow; // Start data from the calculated report start row
        $reportLastRow = $reportStartRow - 1; // Initialize to the header row

        // Loop through months first (month-wise grouping)
        foreach ($monthLoopRange as $currentMonth) {
            // Calculate year for the month based on date range
            $monthYear = date('Y');
            if (!empty($from_date) && !empty($to_date)) {
                $startDate = new DateTime($from_date);
                $endDate = new DateTime($to_date);
                $currentDate = clone $startDate;
                $currentDate->modify('first day of this month');
                
                // Find which year this month belongs to
                while ($currentDate <= $endDate) {
                    $currentMonthNum = (int)$currentDate->format('n');
                    if ($currentMonthNum == $currentMonth) {
                        $monthYear = (int)$currentDate->format('Y');
                        break;
                    }
                    $currentDate->modify('+1 month');
                }
            }
            
            // Calculate first and last day of the month
            $firstDay = '01';
            $lastDay = date('d', mktime(0, 0, 0, $currentMonth + 1, 0, $monthYear));
            // Month range clipped to report date range for Process Adherence
            $excelMonthStart = $monthYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-01';
            $excelMonthEnd = date('Y-m-t', strtotime($excelMonthStart));
            if (!empty($from_date) && $excelMonthStart < $from_date) {
                $excelMonthStart = $from_date;
            }
            if (!empty($to_date) && $excelMonthEnd > $to_date) {
                $excelMonthEnd = $to_date;
            }
            // Now loop through all employees for this month
            foreach ($allKpiReports as $kpiResult) {
                // Use MEP-specific function with year parameter to match view
                $getTotalProductionH = $this->kpi_reports_model->empProductionHoursMonthWiseMEP($kpiResult->empId, $currentMonth, $monthYear);
                $productionHoursArray = explode('@#===', $getTotalProductionH);
                $totalProductionHours = !empty($productionHoursArray[0]) ? $productionHoursArray[0] : 0;
                $totalEmpProductionGeneralHours = isset($productionHoursArray[1]) ? $productionHoursArray[1] : 0;
                $totalEmpGeneralHours = isset($productionHoursArray[2]) ? $productionHoursArray[2] : 0;
                $totalHours = array_sum([$totalProductionHours, $totalEmpGeneralHours, $totalEmpProductionGeneralHours]);

                // Calculate percentages
                $productivityPercentage = $totalHours > 0 ? ($totalProductionHours / $totalHours) * 100 : 0;
                $projectgeneralPercentage = $totalHours > 0 ? ($totalEmpProductionGeneralHours / $totalHours) * 100 : 0;
                $elogicgeneralPercentage = $totalHours > 0 ? ($totalEmpGeneralHours / $totalHours) * 100 : 0;
                $utilizationPercentage = $totalHours > 0 ? (($totalProductionHours + $totalEmpProductionGeneralHours) / $totalHours) * 100 : 0;

                // Process Adherence: use report date range to match view
                $instanceData = $this->kpi_reports_model->timesheetDefaulter($kpiResult->empId, $currentMonth, $monthYear, $excelMonthStart, $excelMonthEnd);
                $timespent = $this->kpi_reports_model->LMShours($kpiResult->empId, $currentMonth, $monthYear) ?: 0;
                $quality = $this->kpi_reports_model->qualityLog($kpiResult->empId, $currentMonth, $monthYear);

                $absentdata = $this->kpi_reports_model->perkabsent($kpiResult->emp_com_id, $currentMonth, $monthYear);
                $perkArray = explode('Perk@#', $absentdata);
                $perkEmpAbsentDays = !empty($perkArray[0]) ? $perkArray[0] : 0;
                $lateLogin = !empty($perkArray[1]) ? $perkArray[1] : 0;
                $earlyOut = !empty($perkArray[2]) ? $perkArray[2] : 0;
                $LateloginandEarlyout = max($lateLogin + $earlyOut - 3, 0);

                // Set monthWorkingHours - ensure $currentMonth is integer for proper comparison
                $currentMonthInt = (int)$currentMonth;
                switch ($currentMonthInt):
                    case 1: $monthWorkingHours = 178.5; break;
                    case 2: $monthWorkingHours = 170.0; break;
                    case 3: $monthWorkingHours = 161.5; break;
                    case 4: $monthWorkingHours = 187.0; break;
                    case 5: $monthWorkingHours = 178.5; break;
                    case 6: $monthWorkingHours = 178.5; break;
                    case 7: $monthWorkingHours = 195.5; break;
                    case 8: $monthWorkingHours = 170.0; break;
                    case 9: $monthWorkingHours = 187.0; break;
                    case 10: $monthWorkingHours = 170.0; break;
                    case 11: $monthWorkingHours = 170.0; break;
                    case 12: $monthWorkingHours = 187.0; break;
                    default: $monthWorkingHours = 0; break;
                endswitch;
                // Calculate availability percentage - prevent division by zero
                $availabilityPercentage = ($monthWorkingHours > 0) ? (($totalHours / $monthWorkingHours) * 100) : 0;

                if (!in_array($kpiResult->department, ['IT', 'HR', 'Software', 'Operations Manager', 'Structural', 'Architectural', '3D Visualization', '']) && !empty($kpiResult->reporting_manger)) {
                    $getTeamwiseMangerName = $this->resourcelog_model->getManagerName($kpiResult->reporting_manger);
                    $firstName = ($getTeamwiseMangerName && is_string($getTeamwiseMangerName)) ? strtok($getTeamwiseMangerName, ' ') : '';
                    if ($firstName === false || $firstName === null) $firstName = '';
                    $firstName = (string) $firstName;

                    if ($totalHours != 0) {
                        $columnIndex = 0;
                        // Short month names for display (matching grid) - use integer keys to match $currentMonthInt
                        $shortMonthNames = [
                            1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
                            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AUG',
                            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DEC'
                        ];
                        
                        if ($userType != 'developer') {
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $firstName);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $kpiResult->emp_com_id);
                            $empFirstName = (isset($kpiResult->name) && $kpiResult->name !== '') ? strtok($kpiResult->name, ' ') : '';
                            if ($empFirstName === false) $empFirstName = '';
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, (string)$empFirstName);
                            // Add short month name (matching grid display)
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $shortMonthNames[$currentMonthInt]);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $shortMonthNames[$currentMonthInt]);
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $totalProductionHours);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $totalEmpProductionGeneralHours);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $totalEmpGeneralHours);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $totalHours);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $monthWorkingHours);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($productivityPercentage) . '%');
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($elogicgeneralPercentage) . '%');
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($utilizationPercentage) . '%');
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($availabilityPercentage) . '%');

                        // Availability Score (matching view logic exactly)
                        $availabilityScore = 0;
                        if ($availabilityPercentage == 0) {
                            $availabilityScore = 0;
                        } elseif ($availabilityPercentage >= 90) {
                            $availabilityScore = 15;
                        } elseif ($availabilityPercentage >= 80 && $availabilityPercentage <= 89) {
                            $availabilityScore = 10;
                        } else {
                            $availabilityScore = 5;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $availabilityScore);

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($projectgeneralPercentage) . '%');

                        // Project General Score (matching view logic exactly)
                        $projectGeneralScore = 0;
                        if ($projectgeneralPercentage == 0) {
                            $projectGeneralScore = 0;
                        } elseif ($projectgeneralPercentage <= 20) {
                            $projectGeneralScore = 15;
                        } elseif ($projectgeneralPercentage > 20 && $projectgeneralPercentage <= 30) {
                            $projectGeneralScore = 10;
                        } else {
                            $projectGeneralScore = 5;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $projectGeneralScore);

                        // QA column - match view display logic exactly
                        $qaDisplay = '';
                        if ($quality === null || $quality === '') {
                            $qaDisplay = '--';
                        } elseif ($quality == 0) {
                            $qaDisplay = '100%';
                        } else {
                            $qaDisplay = round($quality) . '%';
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $qaDisplay);

                        // Quality Score (matching view logic exactly)
                        $qualityScore = 0;
                        if ($quality === null || $quality === '') {
                            $qualityScore = 0;
                        } elseif ($quality == 0) {
                            // Zero errors means 100% quality, so give highest score
                            $qualityScore = 20;
                        } elseif ($quality > 94) {
                            $qualityScore = 20;
                        } elseif ($quality >= 90 && $quality <= 94) {
                            $qualityScore = 10;
                        } else {
                            $qualityScore = 5;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $qualityScore);

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, !empty($instanceData) ? $instanceData : 0);

                        // Process Adherence Score (matching view logic exactly)
                        $processAdherenceScore = 15;
                        if ($instanceData == 0) {
                            $processAdherenceScore = 15;
                        } elseif ($instanceData <= 5) {
                            $processAdherenceScore = 10;
                        } elseif ($instanceData >= 6) {
                            $processAdherenceScore = 5;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $processAdherenceScore);

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, !empty($perkEmpAbsentDays) ? $perkEmpAbsentDays : 0);

                        // Attendance Score (matching view logic exactly)
                        $attendanceScore = 10;
                        if ($perkEmpAbsentDays == 0) {
                            $attendanceScore = 10;
                        } elseif ($perkEmpAbsentDays <= 5) {
                            $attendanceScore = 5;
                        } elseif ($perkEmpAbsentDays >= 6) {
                            $attendanceScore = 2;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $attendanceScore);

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $LateloginandEarlyout);

                        // Late Login Score (matching view logic exactly)
                        $lateLoginScore = 10;
                        if ($LateloginandEarlyout == 0) {
                            $lateLoginScore = 10;
                        } elseif ($LateloginandEarlyout <= 5) {
                            $lateLoginScore = 5;
                        } elseif ($LateloginandEarlyout >= 6) {
                            $lateLoginScore = 2;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $lateLoginScore);

                        // Above and Beyond Hours
                        $aboveBeyondHours = '';
                        if (!empty($timespent)) {
                            $hours = floor($timespent / 3600);
                            $minutes = floor(($timespent % 3600) / 60);
                            $aboveBeyondHours = $hours . '.' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                        } else {
                            $aboveBeyondHours = '0';
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $aboveBeyondHours);

                        // Above and Beyond Score (matching view logic exactly)
                        $ABScore = 5;
                        if (!empty($timespent)) {
                            $hours = floor($timespent / 3600);
                            if ($hours >= 10) {
                                $ABScore = 15;
                            } elseif ($hours >= 8) {
                                $ABScore = 10;
                            } else {
                                $ABScore = 5;
                            }
                        } else {
                            $ABScore = 5;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $ABScore);

                        // Total Score
                        $totalScore = $availabilityScore + $projectGeneralScore + $qualityScore + $processAdherenceScore + $attendanceScore + $lateLoginScore + $ABScore;
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $totalScore);

                        $reportLastRow = $row; // Update the last row
                        $row++;
                    }
                }
            }
        }

        // **Apply Borders to Main Report Table (AFTER populating data)**
        $styleReportBorders = array(
            'borders' => array(
                'outline' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
                'inside' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
                'allborders' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
            ),
        );
        
        $reportEndColumn = $headerEndColumn; // Use the previously calculated end column
        if ($reportLastRow >= $reportStartRow) { // Apply border only if there is data
            $reportRange = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($reportStartColumn, $reportStartRow - 1)->getCoordinate() . ':' .
                          $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($reportEndColumn, $reportLastRow)->getCoordinate();
            
            $objPHPExcel->getActiveSheet()->getStyle($reportRange)->applyFromArray($styleReportBorders);
            
            // Auto-size columns for better visibility
            for ($col = 0; $col <= $reportEndColumn; $col++) {
                $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }

        // Set title of the sheet
        $objPHPExcel->getActiveSheet()->setTitle('KPI Report');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client's web browser (Excel5 or Excel2007)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="mep_kpi_report_' . $titleSuffix . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed (depending on your server configuration)
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit();
    } else {
        // Handle the case where month_id is not provided
        echo "Please select a month to generate the report.";
    }
}

    /**
     * Excel download for getMonthWiseEmpData (month-wise KPI report).
     * Same data as grid; no quality date validation so download is always available.
     */
    public function generateMonthWiseEmpDataExcel()
    {
        $_GET['skip_quality_validation'] = '1';
        $this->generateMonthWiseEmpDataExcel_arch();
    }
    
  public function generateMonthWiseEmpDataExcel_arch()
{
    $from_year = $this->input->get('from_year');
    $from_month = $this->input->get('from_month');
    $to_year = $this->input->get('to_year');
    $to_month = $this->input->get('to_month');
    $from_date = $this->input->get('from_date');
    $to_date = $this->input->get('to_date');
    $search = $this->input->get('search');
    if (is_array($search)) {
        $search = array_values(array_filter(array_map('trim', $search)));
        $search = !empty($search) ? implode(', ', array_unique($search)) : '';
    } elseif (is_string($search)) {
        $search = kpi_normalize_search_display($search);
    } else {
        $search = '';
    }
    $department = $this->input->get('department');
    if (!empty($department) && (is_string($department) && (trim($department) === '' || $department === '__all__'))) {
        $department = '';
    }
    $empWiseKpi = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];

    $hasYmParams = ($from_year !== null && $from_year !== '') && ($to_year !== null && $to_year !== '');
    $hasDateParams = !empty($from_date) && !empty($to_date)
        && trim((string) $from_date) !== '' && trim((string) $to_date) !== '';

    if (!$hasYmParams && !$hasDateParams) {
        $prevMonth = (int) date('n', strtotime('first day of previous month'));
        $prevYear = (int) date('Y', strtotime('first day of previous month'));
        $from_year = $prevYear;
        $from_month = $prevMonth;
        $to_year = $prevYear;
        $to_month = $prevMonth;
    }

    $resolvedDates = $this->_resolve_kpi_consolidated_dates(
        $hasDateParams ? $from_date : null,
        $hasDateParams ? $to_date : null,
        $hasYmParams || !$hasDateParams ? $from_year : null,
        $hasYmParams || !$hasDateParams ? $from_month : null,
        $hasYmParams || !$hasDateParams ? $to_year : null,
        $hasYmParams || !$hasDateParams ? $to_month : null
    );
    $from_date = $resolvedDates['from_date'];
    $to_date = $resolvedDates['to_date'];

    $monthYearPairs = [];
    if (!empty($from_date) && !empty($to_date)) {
        $startDate = new DateTime($from_date);
        $endDate = new DateTime($to_date);
        $currentDate = clone $startDate;
        $currentDate->modify('first day of this month');
        while ($currentDate <= $endDate) {
            $monthYearPairs[] = [
                'month' => (int) $currentDate->format('n'),
                'year' => (int) $currentDate->format('Y'),
            ];
            $currentDate->modify('+1 month');
        }
    }
    if (empty($monthYearPairs)) {
        $prevMonth = (int) date('n', strtotime('first day of previous month'));
        $prevYear = (int) date('Y', strtotime('first day of previous month'));
        $monthYearPairs = [['month' => $prevMonth, 'year' => $prevYear]];
    }

    // Convert date range to months array (legacy title suffix)
    $monthName = array_map(function ($pair) {
        return (int) $pair['month'];
    }, $monthYearPairs);

    if (!empty($monthName)) {
        // Skip quality validation when called from generateMonthWiseEmpDataExcel (same as grid)
        $skipQualityValidation = ($this->input->get('skip_quality_validation') === '1');
        if (!$skipQualityValidation && !$this->kpi_reports_model->validateQualityErrorLogDates($monthName)) {
            echo "Quality error log data does not match the selected dates. Excel download is not available.";
            exit();
        }
        
        // Fetch all data for Excel export - same filters as grid (search, department)
        $allKpiReports = $this->kpi_reports_model->getkpiInformation(999999, 0, '', $search, $from_date, $to_date, $department, 'getMonthWiseEmpData');
        if (!empty($allKpiReports) && is_array($allKpiReports)) {
            usort($allKpiReports, function($a, $b) {
                $nameA = isset($a->name) ? (string)$a->name : '';
                $nameB = isset($b->name) ? (string)$b->name : '';
                return strcasecmp($nameA, $nameB);
            });
        }

        $getkpiReportsSummary = [];
        if (!empty($allKpiReports)) {
            $summarySeen = [];
            foreach ($allKpiReports as $r) {
                if (isset($summarySeen[$r->empId])) {
                    continue;
                }
                $summarySeen[$r->empId] = true;
                $getkpiReportsSummary[] = $r;
            }
        }

        $searchIncludedEmpIds = !empty($search) ? $this->kpi_reports_model->getSearchMatchEmpIds($search) : array();
        $preload = [];
        $managerNamesById = [];
        if (!empty($allKpiReports)) {
            $empIds = [];
            $empComIdByEmpId = [];
            $empDeptByEmpId = [];
            $managerIds = [];
            foreach ($allKpiReports as $r) {
                $empIds[] = $r->empId;
                $empComIdByEmpId[$r->empId] = isset($r->emp_com_id) ? $r->emp_com_id : '';
                $empDeptByEmpId[$r->empId] = isset($r->department) ? $r->department : '';
                if (!empty($r->reporting_manger)) {
                    $managerIds[$r->reporting_manger] = true;
                }
            }
            $empIds = array_values(array_unique($empIds));
            $managerIds = array_keys($managerIds);
            if (!empty($empIds) && !empty($monthYearPairs)) {
                $preload = $this->kpi_reports_model->getMonthWiseReportDataBatch($empIds, $empComIdByEmpId, $empDeptByEmpId, $monthYearPairs);
            }
            if (!empty($managerIds)) {
                $managers = $this->db->select('empId, name')->from('employee_details')->where_in('empId', $managerIds)->get()->result();
                foreach ($managers as $m) {
                    $managerNamesById[$m->empId] = $m->name;
                }
            }
        }
        $sessionData = $this->session->userdata('logged_in_timesheet');
        $userType = isset($sessionData['user_type']) ? strtolower($sessionData['user_type']) : '';
        $months = [
            '1' => 'Jan', '2' => 'Feb', '3' => 'Mar', '4' => 'Apr', '5' => 'May', '6' => 'Jun',
            '7' => 'Jul', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
        ];

        // Load the excel library
        $this->load->library('excel');
        $objPHPExcel = $this->excel;

        // Generate filename and title based on selected months
        if (is_array($monthName) && count($monthName) > 1) {
            $monthNames = array_map(function($m) use ($months) { 
                return isset($months[$m]) ? $months[$m] : ''; 
            }, $monthName);
            $titleSuffix = implode('_', array_map('strtolower', $monthNames));
            $titleDisplay = implode(', ', array_map('strtolower', $monthNames));
        } else {
            $singleMonth = is_array($monthName) ? $monthName[0] : $monthName;
            $titleSuffix = isset($months[$singleMonth]) ? strtolower($months[$singleMonth]) : $singleMonth;
            $titleDisplay = isset($months[$singleMonth]) ? $months[$singleMonth] : $singleMonth;
        }

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("eLogic")
                                     ->setLastModifiedBy("eLogic")
                                     ->setTitle("KPI Report - " . $titleDisplay)
                                     ->setSubject("Employee KPI Report")
                                     ->setDescription("Employee KPI Report for the selected month(s)")
                                     ->setKeywords("kpi report employee")
                                     ->setCategory("Report");

        $primaryDeliveryDepts = function_exists('ts_primary_delivery_departments')
            ? ts_primary_delivery_departments()
            : array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');
        $departmentsToShow = (!empty($department))
            ? array($department)
            : $primaryDeliveryDepts;

        $summaryIsMemberMode = kpi_month_wise_is_member_summary_mode($search);
        if ($summaryIsMemberMode) {
            $summaryRows = kpi_month_wise_build_member_summary_rows(
                $getkpiReportsSummary,
                $monthYearPairs,
                $preload,
                $this->kpi_reports_model
            );
        } else {
            $summaryRows = kpi_month_wise_build_department_summary_rows(
                $getkpiReportsSummary,
                $monthYearPairs,
                $preload,
                $this->kpi_reports_model,
                $departmentsToShow,
                $department
            );
        }

        // **Create Title Row with Date Range**
        $titleRow = 0;
        $fullMonthNames = [
            '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
            '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
            '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];
        
        if (!empty($from_date) && !empty($to_date)) {
            $fromDateObj = new DateTime($from_date);
            $toDateObj = new DateTime($to_date);
            $fromMonth = $fullMonthNames[$fromDateObj->format('n')];
            $fromYear = $fromDateObj->format('Y');
            $toMonth = $fullMonthNames[$toDateObj->format('n')];
            $toYear = $toDateObj->format('Y');
            
            if ($fromMonth === $toMonth && $fromYear === $toYear) {
                $dateText = $fromMonth . " " . $fromYear;
            } else {
                $dateText = $fromMonth . " " . $fromYear . " to " . $toMonth . " " . $toYear;
            }
        } else {
            $currentMonth = date('n');
            $currentYear = date('Y');
            $dateText = $fullMonthNames[$currentMonth] . " " . $currentYear;
        }
        
        $titleText = kpi_month_wise_summary_heading($department, $search, $from_date, $to_date);
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $titleRow, $titleText);
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $titleRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $titleRow)->getFont()->setSize(14);
        
        // **Create Department / Member Summary Table**
        $summaryHeaders = [
            $summaryIsMemberMode ? 'Members' : 'Departments',
            'Productivity Hours', 'Project General Hours', 'eLogic General Hours', 'Availability Hours', 'Utilization Hours',
            'Total Hours',
            'Productivity%', 'Project General%', 'eLogic General%', 'Availability%', 'Utilization%',
        ];
        $summaryStartRow = 2;
        $summaryStartColumn = 0;
        $summaryEndColumn = count($summaryHeaders) - 1;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'KPI Report');
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getFont()->setBold(true);

        for ($i = 0; $i < count($summaryHeaders); $i++) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($summaryStartColumn + $i, $summaryStartRow, $summaryHeaders[$i]);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($summaryStartColumn + $i, $summaryStartRow)->getFont()->setBold(true);
        }
        $summaryHeaderRange = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($summaryStartColumn, $summaryStartRow)->getCoordinate() . ':' .
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($summaryEndColumn, $summaryStartRow)->getCoordinate();
        $objPHPExcel->getActiveSheet()->getStyle($summaryHeaderRange)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
        $objPHPExcel->getActiveSheet()->getStyle($summaryHeaderRange)->getFont()->getColor()->setARGB('FFFFFFFF');

        $summaryDataRow = $summaryStartRow + 1;
        foreach ($summaryRows as $summaryRow) {
            $m = $summaryRow['metrics'];
            $summaryRowData = [
                $summaryRow['label'],
                $this->_kpi_format_excel_hours($m['totalProd']),
                $this->_kpi_format_excel_hours($m['totalGen']),
                $this->_kpi_format_excel_hours($m['totalElog']),
                $this->_kpi_format_excel_hours($m['totalHours']),
                $this->_kpi_format_excel_hours($m['totalUtilHours']),
                $this->_kpi_format_excel_hours($m['totalHours']),
                (int) $m['productivity'] . '%',
                (int) $m['projectGen'] . '%',
                (int) $m['elogicGen'] . '%',
                (int) $m['availability'] . '%',
                (int) $m['utilization'] . '%',
            ];
            for ($i = 0; $i < count($summaryRowData); $i++) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($summaryStartColumn + $i, $summaryDataRow, $summaryRowData[$i]);
            }
            $summaryDataRow++;
        }

        $summaryEndRow = ($summaryDataRow > $summaryStartRow + 1) ? $summaryDataRow - 1 : $summaryStartRow;
                // **Apply Borders to Summary Table**
        $styleSummaryBorders = array(
            'borders' => array(
                'outline' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
                'inside' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
                'allborders' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
            ),
        );
        
        $startCell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($summaryStartColumn, $summaryStartRow)->getCoordinate();
        $endCell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($summaryEndColumn, $summaryEndRow)->getCoordinate();
        $objPHPExcel->getActiveSheet()->getStyle($startCell . ':' . $endCell)->applyFromArray($styleSummaryBorders);

        // Add headers for the main report (after the summary table)
        $headers = [];
        $columnIndex = 0;
        $reportStartRow = $summaryEndRow + 3;
        $reportStartColumn = 0;

        // Report headers with full names (matching grid view)
        if ($userType != 'developer') {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Reporting Manager');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Employee ID');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Employee Name');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Client');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Month');
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Productive Hours');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Project General Hours');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'General Hours');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Avail. Hours');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Availability %');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Utilization %');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'General Hours %');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Productive %');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'P Score');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Proj. General %');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'PG Score');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Quality Acc.');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'QA Score');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Process Adh.');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'PA Score');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Attend Not Upd.');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Attend Score');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Late/Early Login');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'L/E Score');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Above & Beyond');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'A&B Score');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow - 1, 'Total');

// Apply bold style and header background color to the report headers
        $headerEndColumn = $columnIndex - 1;
        for ($col = $reportStartColumn; $col <= $headerEndColumn; $col++) {
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $reportStartRow - 1)->getFont()->setBold(true);
        }
        $reportHeaderRange = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($reportStartColumn, $reportStartRow - 1)->getCoordinate() . ':' .
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($headerEndColumn, $reportStartRow - 1)->getCoordinate();
        $objPHPExcel->getActiveSheet()->getStyle($reportHeaderRange)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
        $objPHPExcel->getActiveSheet()->getStyle($reportHeaderRange)->getFont()->getColor()->setARGB('FFFFFFFF');

        // Add Month column header for Architecture export (matching grid)
        $columnIndex = 0;
        if ($userType != 'developer') {
            // Headers already set above, but we need to add Month column
            // The headers are: RM, ID, EE, then data columns
            // We need to add Month after EE
            // Since headers are already set, we'll add Month in the data rows
        }
        
        $row = $reportStartRow; // Start data from the calculated report start row
        $reportLastRow = $reportStartRow - 1; // Initialize to the header row

        // Get full month names for headers
        $fullMonthNames = [
            '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
            '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
            '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];

        // Determine month range (month + year pairs, matching grid preload)
        if ($userType == 'developer') {
            $monthLoopPairs = [];
            for ($m = 1; $m < (int) date('n'); $m++) {
                $monthLoopPairs[] = ['month' => $m, 'year' => (int) date('Y')];
            }
        } else {
            $monthLoopPairs = $monthYearPairs;
        }

        // Loop through months first (month-wise grouping)
        foreach ($monthLoopPairs as $monthPair) {
            $currentMonth = (int) $monthPair['month'];
            $monthYear = (int) $monthPair['year'];
            $currentMonthInt = $currentMonth;

            // Now loop through all employees for this month
            foreach ($allKpiReports as $kpiResult) {
                if ($kpiResult->department === 'MEP') {
                    $getTotalProductionH = isset($preload['production'][$kpiResult->empId][$currentMonth][$monthYear])
                        ? $preload['production'][$kpiResult->empId][$currentMonth][$monthYear]
                        : $this->kpi_reports_model->empProductionHoursMonthWiseMEP($kpiResult->empId, $currentMonth, $monthYear);
                } else {
                    $getTotalProductionH = isset($preload['production'][$kpiResult->empId][$currentMonth][$monthYear])
                        ? $preload['production'][$kpiResult->empId][$currentMonth][$monthYear]
                        : $this->kpi_reports_model->empProductionHoursMonthWiseAllStatus($kpiResult->empId, $currentMonth, $monthYear);
                }
                $productionHoursArray = explode('@#===', $getTotalProductionH);
                $totalProductionHours = !empty($productionHoursArray[0]) ? $productionHoursArray[0] : 0;
                $totalEmpProductionGeneralHours = isset($productionHoursArray[1]) ? $productionHoursArray[1] : 0;
                $totalEmpGeneralHours = isset($productionHoursArray[2]) ? $productionHoursArray[2] : 0;
                $totalHours = array_sum([$totalProductionHours, $totalEmpGeneralHours, $totalEmpProductionGeneralHours]);

                // Calculate percentages
                $productivityPercentage = $totalHours > 0 ? ($totalProductionHours / $totalHours) * 100 : 0;
                $projectgeneralPercentage = $totalHours > 0 ? ($totalEmpProductionGeneralHours / $totalHours) * 100 : 0;
                $elogicgeneralPercentage = $totalHours > 0 ? ($totalEmpGeneralHours / $totalHours) * 100 : 0;
                $utilizationPercentage = $totalHours > 0 ? (($totalProductionHours + $totalEmpProductionGeneralHours) / $totalHours) * 100 : 0;

                $instanceData = isset($preload['defaulter'][$kpiResult->empId][$currentMonth][$monthYear])
                    ? $preload['defaulter'][$kpiResult->empId][$currentMonth][$monthYear]
                    : $this->kpi_reports_model->timesheetDefaulter($kpiResult->empId, $currentMonth, $monthYear);
                $timespent = isset($preload['lms'][$kpiResult->empId][$currentMonth][$monthYear])
                    ? $preload['lms'][$kpiResult->empId][$currentMonth][$monthYear]
                    : ($this->kpi_reports_model->LMShours($kpiResult->empId, $currentMonth, $monthYear) ?: 0);
                $qualityPreloaded = isset($preload['quality'][$kpiResult->empId][$currentMonth]) && array_key_exists($monthYear, $preload['quality'][$kpiResult->empId][$currentMonth]);
                $quality = $qualityPreloaded
                    ? $preload['quality'][$kpiResult->empId][$currentMonth][$monthYear]
                    : $this->kpi_reports_model->qualityLog($kpiResult->empId, $currentMonth, $monthYear);

                $monthStart = $monthYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-01';
                $monthEnd = date('Y-m-t', strtotime($monthStart));
                $absentdata = isset($preload['perk'][$kpiResult->emp_com_id][$currentMonth][$monthYear])
                    ? $preload['perk'][$kpiResult->emp_com_id][$currentMonth][$monthYear]
                    : $this->kpi_reports_model->perkabsentByDateRange($kpiResult->emp_com_id, $monthStart, $monthEnd);
                $perkArray = explode('Perk@#', $absentdata);
                $perkEmpAbsentDays = !empty($perkArray[0]) ? $perkArray[0] : 0;
                $lateLogin = !empty($perkArray[1]) ? $perkArray[1] : 0;
                $earlyOut = !empty($perkArray[2]) ? $perkArray[2] : 0;
                $LateloginandEarlyout = max($lateLogin + $earlyOut - 3, 0);

                // Set monthWorkingHours
                switch ($currentMonthInt):
                    case 1: $monthWorkingHours = 178.5; break;
                    case 2: $monthWorkingHours = 170.0; break;
                    case 3: $monthWorkingHours = 161.5; break;
                    case 4: $monthWorkingHours = 187.0; break;
                    case 5: $monthWorkingHours = 178.5; break;
                    case 6: $monthWorkingHours = 178.5; break;
                    case 7: $monthWorkingHours = 195.5; break;
                    case 8: $monthWorkingHours = 170.0; break;
                    case 9: $monthWorkingHours = 187.0; break;
                    case 10: $monthWorkingHours = 170.0; break;
                    case 11: $monthWorkingHours = 170.0; break;
                    case 12: $monthWorkingHours = 187.0; break;
                    default: $monthWorkingHours = 0; break;
                endswitch;
                $availabilityPercentage = ($monthWorkingHours > 0) ? (($totalHours / $monthWorkingHours) * 100) : 0;

                $isSearchIncludedRow = !empty($searchIncludedEmpIds) && in_array($kpiResult->empId, $searchIncludedEmpIds, true);
                $empDept = isset($kpiResult->department) ? trim((string) $kpiResult->department) : '';
                $showEmployeeRow = in_array($empDept, $primaryDeliveryDepts, true) || $isSearchIncludedRow;

                if ($showEmployeeRow) {
                    $getTeamwiseMangerName = isset($managerNamesById[$kpiResult->reporting_manger])
                        ? $managerNamesById[$kpiResult->reporting_manger]
                        : $this->resourcelog_model->getManagerName($kpiResult->reporting_manger);
                    $firstName = ($getTeamwiseMangerName && is_string($getTeamwiseMangerName)) ? strtok($getTeamwiseMangerName, ' ') : '';
                    if ($firstName === false || $firstName === null) $firstName = '';
                    $firstName = (string) $firstName;

                    if ($totalHours != 0) {
                        // Short month names for display (matching grid) - use integer keys to match $currentMonthInt
                        $shortMonthNames = [
                            1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
                            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AUG',
                            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DEC'
                        ];
                        
                        $columnIndex = 0;
                        if ($userType != 'developer') {
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $firstName);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $kpiResult->emp_com_id);
                            $empFirstName = (isset($kpiResult->name) && $kpiResult->name !== '') ? strtok($kpiResult->name, ' ') : '';
                            if ($empFirstName === false) $empFirstName = '';
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, (string)$empFirstName);
                            $workedClients = isset($preload['clients'][$kpiResult->empId][$currentMonthInt][$monthYear])
                                ? $preload['clients'][$kpiResult->empId][$currentMonthInt][$monthYear]
                                : $this->kpi_reports_model->getClientsWorkedOnMonthWise($kpiResult->empId, $currentMonthInt, $monthYear);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $workedClients !== '' ? $workedClients : '—');
                            // Add short month name with year (matching grid display)
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $shortMonthNames[$currentMonthInt] . ' - ' . $monthYear);
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $totalProductionHours);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $totalEmpProductionGeneralHours);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $totalEmpGeneralHours);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $totalHours);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($availabilityPercentage) . '%');
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($utilizationPercentage) . '%');
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($elogicgeneralPercentage) . '%');
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($productivityPercentage) . '%');

                        // Productivity Score
                        $productivityScore = 0;
                        if ($productivityPercentage == 0) $productivityScore = 0;
                        elseif ($productivityPercentage >= 85) $productivityScore = 20;
                        elseif ($productivityPercentage >= 80) $productivityScore = 15;
                        else $productivityScore = 10;
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $productivityScore);

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($projectgeneralPercentage) . '%');

                        // Project General Score (matching view logic exactly)
                        $projectGeneralScore = 0;
                        if ($projectgeneralPercentage == 0) {
                            $projectGeneralScore = 0;
                        } elseif ($projectgeneralPercentage <= 20) {
                            $projectGeneralScore = 15;
                        } elseif ($projectgeneralPercentage > 20 && $projectgeneralPercentage <= 30) {
                            $projectGeneralScore = 10;
                        } else {
                            $projectGeneralScore = 5;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $projectGeneralScore);

                        // QA column - match view display logic
                        $qaDisplay = '';
                        if ($quality === null || $quality === '') {
                            $qaDisplay = '--';
                        } elseif ($quality == 0) {
                            $qaDisplay = '100%';
                        } else {
                            $qaDisplay = round($quality) . '%';
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $qaDisplay);

                        // Quality Score (matching view logic exactly)
                        $qualityScore = 0;
                        if ($quality === null || $quality === '') {
                            $qualityScore = 0;
                        } elseif ($quality == 0) {
                            // Zero errors means 100% quality, so give highest score
                            $qualityScore = 20;
                        } elseif ($quality > 94) {
                            $qualityScore = 20;
                        } elseif ($quality >= 90 && $quality <= 94) {
                            $qualityScore = 10;
                        } else {
                            $qualityScore = 5;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $qualityScore);

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, !empty($instanceData) ? $instanceData : 0);

                        // Process Adherence Score (matching view logic exactly)
                        $processAdherenceScore = 15;
                        if ($instanceData == 0) {
                            $processAdherenceScore = 15;
                        } elseif ($instanceData <= 5) {
                            $processAdherenceScore = 10;
                        } elseif ($instanceData >= 6) {
                            $processAdherenceScore = 5;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $processAdherenceScore);

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, !empty($perkEmpAbsentDays) ? $perkEmpAbsentDays : 0);

                        // Attendance Score (matching view logic exactly)
                        $attendanceScore = 10;
                        if ($perkEmpAbsentDays == 0) {
                            $attendanceScore = 10;
                        } elseif ($perkEmpAbsentDays <= 5) {
                            $attendanceScore = 5;
                        } elseif ($perkEmpAbsentDays >= 6) {
                            $attendanceScore = 2;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $attendanceScore);

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $LateloginandEarlyout);

                        // Late Login Score (matching view logic exactly)
                        $lateLoginScore = 10;
                        if ($LateloginandEarlyout == 0) {
                            $lateLoginScore = 10;
                        } elseif ($LateloginandEarlyout <= 5) {
                            $lateLoginScore = 5;
                        } elseif ($LateloginandEarlyout >= 6) {
                            $lateLoginScore = 2;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $lateLoginScore);

                        // Above and Beyond Hours
                        $aboveBeyondHours = '0';
                        if (!empty($timespent)) {
                            $hours = floor($timespent / 3600);
                            $minutes = floor(($timespent % 3600) / 60);
                            $aboveBeyondHours = $hours . '.' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $aboveBeyondHours);

                        // Above and Beyond Score (matching view logic exactly)
                        $ABScore = 6;
                        if (!empty($timespent)) {
                            $hours = floor($timespent / 3600);
                            if ($hours >= 10) {
                                $ABScore = 10;
                            } elseif ($hours >= 8) {
                                $ABScore = 8;
                            } else {
                                $ABScore = 6;
                            }
                        } else {
                            $ABScore = 6;
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $ABScore);

                        // Total Score
                        $totalScore = $productivityScore + $projectGeneralScore + $qualityScore + $processAdherenceScore + $attendanceScore + $lateLoginScore + $ABScore;
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $totalScore);
                        $reportLastRow = $row; // Update the last row
                        $row++;
                    }
                }
            }
            // Add blank row after each month section
            $row++;
        }

        // **Apply Borders to Main Report Table (AFTER populating data)**
        $styleReportBorders = array(
            'borders' => array(
                'outline' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
                'inside' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
                'allborders' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
                ),
            ),
        );
        

   $reportEndColumn = $headerEndColumn; // Use the previously calculated end column
        if ($reportLastRow >= $reportStartRow) { // Apply border only if there is data
            $reportRange = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($reportStartColumn, $reportStartRow - 1)->getCoordinate() . ':' .
                          $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($reportEndColumn, $reportLastRow)->getCoordinate();
            
            $objPHPExcel->getActiveSheet()->getStyle($reportRange)->applyFromArray($styleReportBorders);
            
            // Auto-size columns for better visibility
            for ($col = 0; $col <= $reportEndColumn; $col++) {
                $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }

        // Set title of the sheet
        $objPHPExcel->getActiveSheet()->setTitle('KPI Report');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client's web browser (Excel5 or Excel2007)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="kpi_report_' . $titleSuffix . '.xls"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit();
    } else {
        // Handle the case where month_id is not provided
        echo "Please select a month to generate the report.";
    }
}
    
public function generateConsolidatedReportExcel() {
    $search = $this->input->get('search');
    if (is_array($search)) {
        $search = array_values(array_filter(array_map('trim', $search)));
        $search = !empty($search) ? implode(', ', array_unique($search)) : '';
    }
    $department = $this->input->get('department');
    if (!empty($department)) {
        if (is_array($department)) {
            $department = array_values(array_filter($department, function($d) { return $d !== '' && $d !== '__all__'; }));
            $department = !empty($department) ? $department : '';
        } elseif (is_string($department) && $department !== '__all__') {
            $department = array_values(array_filter(array_map('trim', explode(',', $department)), function($d) { return $d !== '__all__'; }));
            $department = !empty($department) ? $department : '';
        } else {
            $department = '';
        }
    } else {
        $department = '';
    }
    $resolvedDates = $this->_resolve_kpi_consolidated_dates(
        $this->input->get('from_date'),
        $this->input->get('to_date'),
        $this->input->get('from_year'),
        $this->input->get('from_month'),
        $this->input->get('to_year'),
        $this->input->get('to_month')
    );
    $data_from_date = $resolvedDates['from_date'];
    $data_to_date = $resolvedDates['to_date'];
    $empWiseKpi = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];

    // Validate quality error log dates
    if (!$this->kpi_reports_model->validateQualityErrorLogDates('', $data_from_date, $data_to_date)) {
        echo "Quality error log data does not match the selected dates. Excel download is not available.";
        exit();
    }

    // Get raw data (same source as table) - use data dates for query, search is already passed
    $consolidatedKpiData = $this->kpi_reports_model->getAllConsolidatedKpiInformation($search, $empWiseKpi, $empId, $data_from_date, $data_to_date, $department);
    // Ensure object rows for consistent access.
    if (is_array($consolidatedKpiData)) {
        foreach ($consolidatedKpiData as $i => $r) {
            if (is_array($r)) {
                $consolidatedKpiData[$i] = (object)$r;
            }
        }
    }
    // Build manager cache once to avoid repeated lookups and prevent empty Reporting Manager in Excel.
    $managerNameById = [];
    if (!empty($consolidatedKpiData) && is_array($consolidatedKpiData)) {
        $managerIds = [];
        foreach ($consolidatedKpiData as $r) {
            if (!empty($r->reporting_manger) && ctype_digit((string)$r->reporting_manger)) {
                $managerIds[] = (int)$r->reporting_manger;
            }
        }
        $managerIds = array_values(array_unique($managerIds));
        if (!empty($managerIds)) {
            $managerRows = $this->db->select('empId, name')
                ->from('employee_details')
                ->where_in('empId', $managerIds)
                ->get()
                ->result();
            foreach ($managerRows as $m) {
                $managerNameById[(int)$m->empId] = trim((string)$m->name);
            }
        }
    }
    $resolveManagerFullName = function($row) use ($managerNameById) {
        // 1) Preferred: manager_name from consolidated query
        if (!empty($row->manager_name) && is_string($row->manager_name)) {
            $name = trim($row->manager_name);
            if ($name !== '') return $name;
        }
        // 2) Fallback: lookup reporting manager empId from cache
        if (!empty($row->reporting_manger) && ctype_digit((string)$row->reporting_manger)) {
            $mid = (int)$row->reporting_manger;
            if (!empty($managerNameById[$mid])) {
                return $managerNameById[$mid];
            }
        }
        // 3) Last fallback: reporting_manger value itself (handles cases where it is already a name)
        if (isset($row->reporting_manger)) {
            $rm = trim((string)$row->reporting_manger);
            if ($rm !== '') return $rm;
        }
        return '';
    };
    // Sort rows by Reporting Manager (A-Z), then employee name.
    if (!empty($consolidatedKpiData) && is_array($consolidatedKpiData)) {
        usort($consolidatedKpiData, function($a, $b) use ($resolveManagerFullName) {
            $ma = $resolveManagerFullName($a);
            $mb = $resolveManagerFullName($b);
            $cmp = strcasecmp($ma, $mb);
            if ($cmp !== 0) return $cmp;
            $ea = isset($a->name) ? (string)$a->name : '';
            $eb = isset($b->name) ? (string)$b->name : '';
            return strcasecmp($ea, $eb);
        });
    }

    $this->load->library('excel');
    $objPHPExcel = $this->excel;
    
    // Set document properties
    $objPHPExcel->getProperties()
        ->setCreator("eLogic")
        ->setLastModifiedBy("eLogic")
        ->setTitle("Consolidated Employee KPI Report")
        ->setDescription("Consolidated KPI Report for all employees");

    // ================== DEPARTMENT SUMMARY (match grid: 5 depts, % + hours) ==================
    $monthYearPairs = [];
    if (!empty($data_from_date) && !empty($data_to_date)) {
        $startDate = new DateTime($data_from_date);
        $endDate = new DateTime($data_to_date);
        $currentDate = clone $startDate;
        $currentDate->modify('first day of this month');
        while ($currentDate <= $endDate) {
            $monthYearPairs[] = ['month' => (int)$currentDate->format('n'), 'year' => (int)$currentDate->format('Y')];
            $currentDate->modify('+1 month');
        }
    } else {
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');
        for ($m = 1; $m <= $currentMonth; $m++) {
            $monthYearPairs[] = ['month' => $m, 'year' => $currentYear];
        }
    }
    $monthNames = array_map(function($p) { return $p['month']; }, $monthYearPairs);
    $monthNames = array_map('intval', array_filter($monthNames, 'is_numeric'));

    $allKpiDepartments = function_exists('ts_primary_delivery_departments')
        ? ts_primary_delivery_departments()
        : ['Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP'];

    if (!empty($department)) {
        if (is_array($department)) {
            $departmentsToShow = array_values(array_intersect($allKpiDepartments, array_filter($department)));
            if (empty($departmentsToShow)) {
                $departmentsToShow = array_values(array_filter($department));
            }
        } else {
            $deptList = array_values(array_filter(array_map('trim', explode(',', $department))));
            $departmentsToShow = array_values(array_intersect($allKpiDepartments, $deptList));
            if (empty($departmentsToShow)) {
                $departmentsToShow = $deptList;
            }
        }
    } else {
        $departmentsToShow = $allKpiDepartments;
    }
    $departmentsToShow = kpi_consolidated_summary_departments_to_show($departmentsToShow, $search);

    $managerDeptTotals = $this->accumulate_consolidated_dept_manager_totals(
        is_array($consolidatedKpiData) ? $consolidatedKpiData : array(),
        array(),
        $monthYearPairs,
        $departmentsToShow
    );
    $managerSelfTotalsByDept = $this->build_manager_self_totals_by_dept(
        is_array($consolidatedKpiData) ? $consolidatedKpiData : array(),
        array(),
        $monthYearPairs,
        $search,
        $departmentsToShow
    );

    $showAllDeptRows = empty($department) && empty($search);

    // ================== TITLE ROW WITH DATE RANGE AND SEARCH NAME ==================
    $titleRow = 1; // PHPExcel uses 1-based row indexing
    $fullMonthNames = [
        '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
        '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
        '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
    ];
    
    // Format date range for title (using dynamic dates)
    if (!empty($data_from_date) && !empty($data_to_date)) {
        $fromDateObj = new DateTime($data_from_date);
        $toDateObj = new DateTime($data_to_date);
        $fromMonth = $fullMonthNames[$fromDateObj->format('n')];
        $fromYear = $fromDateObj->format('Y');
        $toMonth = $fullMonthNames[$toDateObj->format('n')];
        $toYear = $toDateObj->format('Y');
        
        $dateText = $fromMonth . " " . $fromYear . " - " . $toMonth . " " . $toYear;
    } else {
        // Default: January to current month
        $currentMonth = date('n');
        $currentYear = date('Y');
        $currentMonthName = $fullMonthNames[$currentMonth];
        $dateText = "January " . $currentYear . " - " . $currentMonthName . " " . $currentYear;
    }
    
    // Build title with search name if available
    $titleText = 'KPI Report ' . $dateText;
    if (!empty($search)) {
        $titleText .= ' ( ' . $search . ' )';
    }

    $summaryEndColumn = 12; // 13 columns (A-M)
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $titleRow, $titleText);
    $objPHPExcel->getActiveSheet()->mergeCells('A' . $titleRow . ':M' . $titleRow);
    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $titleRow)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $titleRow)->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $titleRow)->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'KPI Report');
    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 2)->getFont()->setBold(true);

    $summaryMgrColHeading = !empty(kpi_consolidated_search_terms($search)) ? 'Team' : 'Reporting Manager';
    $summaryHeaders = [
        'Department', $summaryMgrColHeading,
        'Productivity Hours', 'Project General Hours', 'eLogic General Hours', 'Availability Hours', 'Utilization Hours',
        'Total Hours',
        'Productivity%', 'Project General%', 'eLogic General%', 'Availability%', 'Utilization%',
    ];
    $summaryStartRow = 3;

    foreach ($summaryHeaders as $col => $header) {
        $objPHPExcel->getActiveSheet()
            ->setCellValueByColumnAndRow($col, $summaryStartRow, $header)
            ->getStyleByColumnAndRow($col, $summaryStartRow)->getFont()->setBold(true);
    }
    $summaryHeaderRange = 'A' . $summaryStartRow . ':M' . $summaryStartRow;
    $objPHPExcel->getActiveSheet()->getStyle($summaryHeaderRange)->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF014B88');
    $objPHPExcel->getActiveSheet()->getStyle($summaryHeaderRange)->getFont()->getColor()->setARGB('FFFFFFFF');

    $row = $summaryStartRow + 1;
    foreach ($departmentsToShow as $deptName) {
        $mgrBlocks = isset($managerDeptTotals[$deptName]) && is_array($managerDeptTotals[$deptName])
            ? $managerDeptTotals[$deptName] : array();
        $displayRows = kpi_consolidated_dept_summary_display_rows(
            $deptName,
            $mgrBlocks,
            $managerSelfTotalsByDept,
            $search,
            $showAllDeptRows
        );
        if (empty($displayRows)) {
            if (!$showAllDeptRows) {
                continue;
            }
            $displayRows = array(array('mgrId' => '', 'stats' => array('count' => 0, 'totalProd' => 0, 'totalGen' => 0, 'totalElog' => 0, 'totalHours' => 0, 'totalWorkHrs' => 0, 'totalUtilHours' => 0)));
        }
        $firstMgrRow = true;
        foreach ($displayRows as $displayRow) {
            $mgrId = $displayRow['mgrId'];
            $d = $displayRow['stats'];
            $deptTotalProd = isset($d['totalProd']) ? (float) $d['totalProd'] : 0;
            $deptTotalGen = isset($d['totalGen']) ? (float) $d['totalGen'] : 0;
            $deptTotalElog = isset($d['totalElog']) ? (float) $d['totalElog'] : 0;
            $deptTotalHours = isset($d['totalHours']) ? (float) $d['totalHours'] : 0;
            $deptTotalWorkHrs = isset($d['totalWorkHrs']) ? (float) $d['totalWorkHrs'] : 0;
            $deptTotalUtilHours = isset($d['totalUtilHours']) ? (float) $d['totalUtilHours'] : 0;

            $productivity = $deptTotalHours > 0 ? round(($deptTotalProd / $deptTotalHours) * 100) : 0;
            $projectGen = $deptTotalHours > 0 ? round(($deptTotalGen / $deptTotalHours) * 100) : 0;
            $elogicGen = $deptTotalHours > 0 ? round(($deptTotalElog / $deptTotalHours) * 100) : 0;
            $availability = $deptTotalWorkHrs > 0 ? round(($deptTotalHours / $deptTotalWorkHrs) * 100) : 0;
            $utilization = $deptTotalHours > 0 ? round(($deptTotalUtilHours / $deptTotalHours) * 100) : 0;

            $actualMgrName = '--';
            if ($mgrId !== '' && isset($managerNameById[(int) $mgrId])) {
                $actualMgrName = $managerNameById[(int) $mgrId];
            } elseif ($mgrId !== '') {
                $actualMgrName = (string) $mgrId;
            }
            $mgrLabel = kpi_consolidated_team_display_label($mgrId, $actualMgrName, $managerNameById, $search);

            $summaryRowData = array(
                $firstMgrRow ? $deptName : '',
                $mgrLabel,
                $this->_kpi_format_excel_hours($deptTotalProd),
                $this->_kpi_format_excel_hours($deptTotalGen),
                $this->_kpi_format_excel_hours($deptTotalElog),
                $this->_kpi_format_excel_hours($deptTotalHours),
                $this->_kpi_format_excel_hours($deptTotalUtilHours),
                $this->_kpi_format_excel_hours($deptTotalHours),
                $productivity . '%',
                $projectGen . '%',
                $elogicGen . '%',
                $availability . '%',
                $utilization . '%',
            );
            for ($i = 0; $i < count($summaryRowData); $i++) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $summaryRowData[$i]);
            }
            $row++;
            $firstMgrRow = false;
        }
    }

    $summaryEndRow = ($row > $summaryStartRow + 1) ? $row - 1 : $summaryStartRow;
    $summaryRange = 'A' . $summaryStartRow . ':M' . $summaryEndRow;
    $objPHPExcel->getActiveSheet()->getStyle($summaryRange)->applyFromArray([
        'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
    ]);

     // ================== ORIGINAL REPORT TABLE ==================
    // Add headers (starting below summary table with spacing)
    $headers = ['Dept', 'Reporting Manager', 'ID', 'EE', 'Avg Productivity %', 'Avg Project General %','Avg eLogic General %','Avg Availability %' ,'Avg Utilization %','Avg Quality Accuracy %','Total Process Adherence','Total UPL and Attend not updated','Total No of Late and Early Login','Above & Beyond'];
    
    $columnIndex = 0;
    $reportStartRow = $summaryEndRow + 3; // Start report 2 rows after summary table
    $headerEndColumn = count($headers) - 1;
    
    foreach ($headers as $header) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $reportStartRow, $header);
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($columnIndex-1, $reportStartRow)->getFont()->setBold(true);
    }
    // Header row background and font color (match grid: dark blue #014b88, white text)
    $reportHeaderRange = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $reportStartRow)->getCoordinate() . ':' .
        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($headerEndColumn, $reportStartRow)->getCoordinate();
    $objPHPExcel->getActiveSheet()->getStyle($reportHeaderRange)->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF014B88');
    $objPHPExcel->getActiveSheet()->getStyle($reportHeaderRange)->getFont()->getColor()->setARGB('FFFFFFFF');
    
    // Full month names for display
    $fullMonthNames = [
        '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
        '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
        '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
    ];
    
    // Ensure monthNames are integers
    $monthNames = array_map('intval', array_filter($monthNames, 'is_numeric'));
    
    // Sort months in descending order (newest first) - matching view
    rsort($monthNames);
    
    // Add data rows - Group by employee (one row per employee with aggregated data across all months)
    $row = $reportStartRow + 1;
    $primaryDeliveryDepts = function_exists('ts_primary_delivery_departments')
        ? ts_primary_delivery_departments()
        : ['Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP'];

    foreach ($consolidatedKpiData as $kpiResult) {
        if (!in_array($kpiResult->department, $primaryDeliveryDepts, true) || empty($kpiResult->reporting_manger)) {
            continue;
        }

        // Initialize totals for this employee across all months
        $totals = [
            'productivity' => 0,
            'projectGeneral' => 0,
            'elogicGeneral' => 0,
            'availability' => 0,
            'utilization' => 0,
            'quality' => 0,
            'instanceData' => 0,
            'perkEmpAbsentDays' => 0,
            'lateloginandEarlyout' => 0,
            'timeSpent' => 0,
            'validMonths' => 0
        ];
        
        // Loop through all months and aggregate data (pass year so MEP data shows)
        foreach ($monthYearPairs as $monthYear) {
            $month = (int) $monthYear['month'];
            $yearForMonth = (int) $monthYear['year'];
            
            if ($kpiResult->department === 'MEP') {
                $monthlyData = $this->kpi_reports_model->empProductionHoursMonthWiseMEP($kpiResult->empId, $month, $yearForMonth);
            } else {
                $monthlyData = $this->kpi_reports_model->empProductionHoursMonthWiseCons($kpiResult->empId, $month, $yearForMonth);
            }
            $productionHoursArray = is_string($monthlyData) ? explode('@#===', $monthlyData) : ['0', '0', '0'];

            $monthlyProduction = isset($productionHoursArray[0]) ? (float)$productionHoursArray[0] : 0;
            $monthlyProjectGeneral = isset($productionHoursArray[1]) ? (float)$productionHoursArray[1] : 0;
            $monthlyElogicGeneral = isset($productionHoursArray[2]) ? (float)$productionHoursArray[2] : 0;
            $monthlyTotalHours = $monthlyProduction + $monthlyProjectGeneral + $monthlyElogicGeneral;

            if ($monthlyTotalHours <= 0) {
                continue;
            }

            $monthWorkingHours = [
                1 => 178.5, 2 => 170.0, 3 => 161.5, 4 => 187.0, 5 => 178.5,
                6 => 178.5, 7 => 195.5, 8 => 170.0, 9 => 187.0, 10 => 170.0,
                11 => 170.0, 12 => 187.0
            ];
            $workHrs = isset($monthWorkingHours[$month]) ? $monthWorkingHours[$month] : 0;

            if ($workHrs <= 0) {
                continue;
            }

            $monthlyProductivityPercentage = ($monthlyProduction / $monthlyTotalHours) * 100;
            $monthlyProjectGeneralPercentage = ($monthlyProjectGeneral / $monthlyTotalHours) * 100;
            $monthlyElogicGeneralPercentage = ($monthlyElogicGeneral / $monthlyTotalHours) * 100;
            $monthlyAvailability = ($monthlyTotalHours / $workHrs) * 100;
            $monthlyUtilization = (($monthlyProduction + $monthlyProjectGeneral) / $monthlyTotalHours) * 100;
            
            // Get other monthly data
            $monthlyTimeSpent = $this->kpi_reports_model->LMShours($kpiResult->empId, $month);
            $monthlyinstanceData = $this->kpi_reports_model->timesheetDefaulter($kpiResult->empId, $month);
            $monthlyQuality = $this->kpi_reports_model->qualityyearlylog($kpiResult->empId, $month);
            
            $monthlyabsentdata = $this->kpi_reports_model->perkabsent($kpiResult->emp_com_id, $month);
            $perkArray = explode('Perk@#', $monthlyabsentdata);
            $monthlyperkEmpAbsentDays = !empty($perkArray[0]) ? (int)$perkArray[0] : 0;
            $monthlylateLogin = !empty($perkArray[1]) ? (int)$perkArray[1] : 0;
            $monthlyearlyOut = !empty($perkArray[2]) ? (int)$perkArray[2] : 0;
            // Same as monthly report: 3 allowance per month
            $monthlyLateloginandEarlyout = max($monthlylateLogin + $monthlyearlyOut - 3, 0);
            
            // Accumulate totals
            $totals['productivity'] += $monthlyProductivityPercentage;
            $totals['projectGeneral'] += $monthlyProjectGeneralPercentage;
            $totals['elogicGeneral'] += $monthlyElogicGeneralPercentage;
            $totals['availability'] += $monthlyAvailability;
            $totals['utilization'] += $monthlyUtilization;
            $totals['quality'] += $monthlyQuality;
            $totals['instanceData'] += $monthlyinstanceData;
            $totals['perkEmpAbsentDays'] += $monthlyperkEmpAbsentDays;
            $totals['lateloginandEarlyout'] += $monthlyLateloginandEarlyout;
            $totals['timeSpent'] += $monthlyTimeSpent;
            $totals['validMonths']++;
        }
        
        // Only write row if employee has valid data
        if ($totals['validMonths'] > 0) {
            // Calculate averages
            $avgProductivity = $totals['productivity'] / $totals['validMonths'];
            $avgProjectGeneral = $totals['projectGeneral'] / $totals['validMonths'];
            $avgElogicGeneral = $totals['elogicGeneral'] / $totals['validMonths'];
            $avgAvailability = $totals['availability'] / $totals['validMonths'];
            $avgUtilization = $totals['utilization'] / $totals['validMonths'];
            $avgQuality = $totals['quality'] / $totals['validMonths'];
            
            // Totals (sum across all months)
            $totalInstanceData = $totals['instanceData'];
            $totalPerkEmpAbsentDays = $totals['perkEmpAbsentDays'];
            $totalLateloginandEarlyout = $totals['lateloginandEarlyout'];
            
            // Format aboveBeyondHours (total time spent)
            $totalTimeSpent = $totals['timeSpent'];
            if (!empty($totalTimeSpent)) {
                $hours = floor($totalTimeSpent / 3600);
                $minutes = floor(($totalTimeSpent % 3600) / 60);
                $aboveBeyondHours = $hours . '.' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
            } else {
                $aboveBeyondHours = '0';
            }
            
            // Reporting Manager: robust resolution with cache and fallback to raw value.
            $managerFullName = $resolveManagerFullName($kpiResult);
            $firstName = ($managerFullName && is_string($managerFullName)) ? strtok($managerFullName, ' ') : '';
            if ($firstName === false || $firstName === null) {
                $firstName = '';
            }
            $reportingManagerDisplay = (string) $firstName;

            // Write data row
            $columnIndex = 0;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $kpiResult->department);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $reportingManagerDisplay);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $kpiResult->emp_com_id);
            // Get first name from employee name (matching view)
            $nameParts = isset($kpiResult->name) ? explode(" ", $kpiResult->name) : [];
            $employeeFirstName = (!empty($nameParts) && isset($nameParts[0])) ? $nameParts[0] : '';
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $employeeFirstName);
            // Month column removed - matching view
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($avgProductivity) . '%');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($avgProjectGeneral) . '%');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($avgElogicGeneral) . '%');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($avgAvailability) . '%');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($avgUtilization) . '%');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($avgQuality) . '%');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($totalInstanceData));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($totalPerkEmpAbsentDays));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, round($totalLateloginandEarlyout));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($columnIndex++, $row, $aboveBeyondHours);
            $row++;
        }
    }

    // Auto-size columns
    for ($col = 0; $col < count($headers); $col++) {
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
    }

    // Set title of the sheet
    $objPHPExcel->getActiveSheet()->setTitle('Consolidated KPI Report');

    // Set active sheet index to the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Generate filename based on date range or default
    if (!empty($from_date) && !empty($to_date)) {
        $fromDateFormatted = date('d-m-Y', strtotime($from_date));
        $toDateFormatted = date('d-m-Y', strtotime($to_date));
        $filename = 'consolidated_kpi_report_' . $fromDateFormatted . '_to_' . $toDateFormatted . '.xls';
    } else {
        $previousMonthName = date('F', strtotime('last month'));
        $filename = 'consolidated_kpi_report_jan_' . strtolower($previousMonthName) . '.xls';
    }

    // Output the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit();
}

public function generateClientReportExcel() {
    $this->load->helper('kpi_display');
    $core = $this->client_report_core_filters_from_get();
    $from_date = $core['from_date'];
    $to_date = $core['to_date'];
    $search = $core['search'];
    $department = $core['department'];
    $grid = $core['grid'];
    
    // Same load → prepare → summary pipeline as clientReport() so Excel matches the grid.
    $reportData = $this->load_client_report_display_data($from_date, $to_date, $search, $department, $grid);
    $exportData = $this->prepare_client_report_export_data($from_date, $to_date, $search, $department, $grid, $reportData);
    $monthWiseData = $exportData['monthWiseData'];
    $grouped = $exportData['grouped'];
    $hasMonthWiseData = !empty($exportData['hasMonthWiseData']);

    // Load PHPExcel library
    $this->load->library('excel');
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $title = "Client KPI Report";
    if (!empty($from_date) && !empty($to_date)) {
        $title .= " - " . $from_date . " to " . $to_date;
    }
    
    $objPHPExcel->getProperties()->setCreator("Your System")
                                ->setLastModifiedBy("Your System")
                                ->setTitle($title)
                                ->setSubject("Client Performance Report")
                                ->setDescription("Client performance report for date range");

    // Create worksheets: summary (scrollable months) then client grid
    $objPHPExcel->setActiveSheetIndex(0);
    $summarySheet = $objPHPExcel->getActiveSheet();
    $summarySheet->setTitle('Dept Summary');

    $deptKpiSummary = $this->build_client_report_dept_kpi_summary(
        $from_date,
        $to_date,
        $department,
        $search,
        $grid,
        $reportData,
        $exportData
    );
    $this->write_client_report_dept_kpi_summary_excel($summarySheet, $deptKpiSummary, 1);

    $sheet = $objPHPExcel->createSheet();
    $sheet->setTitle('Client Report');
    $clientTableStartRow = 1;

    $clientReportMonthIncludeYear = false;
    if (!empty($from_date) && !empty($to_date)) {
        $fromYearKey = date('Y', strtotime($from_date));
        $toYearKey = date('Y', strtotime($to_date));
        $clientReportMonthIncludeYear = ($fromYearKey !== false && $toYearKey !== false && $fromYearKey !== $toYearKey);
    }

    // Headers matching grid view exactly (same columns and order)
    $headers = [
        'Client Name', 'Project Manager', 'Month', 'Department', 'Start Date', 'End Date', 'Billing',
        'Production Hours', 'Project General Hours', 'Total Hours', 'Invoiced', 'Quality Errors', 'Productivity %', 'Project General %', 'Difference'
    ];
    $sheet->fromArray($headers, NULL, 'A' . $clientTableStartRow);

    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
        'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '5B9BD5']],
        'alignment' => [
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ]
    ];
    $sheet->getStyle('A' . $clientTableStartRow . ':O' . $clientTableStartRow)->applyFromArray($headerStyle);
    $sheet->getRowDimension($clientTableStartRow)->setRowHeight(32);

    // Data starts after client table header row
    $rowNum = $clientTableStartRow + 1;
    $clientExcelRows = array();
    $posDiffRows = array();
    $negDiffRows = array();

    // Helper function to write client and project rows
    $writeClientProjects = function($data, $clientId, $monthFromDate, $monthToDate, $monthLabel, &$rowNum, $sheet) use ($from_date, $to_date, $clientReportMonthIncludeYear, &$clientExcelRows, &$posDiffRows, &$negDiffRows) {
        // Get client PM name once (same source as grid view)
        $clientPmName = '--';
        if (!empty($data['client_pm_name'])) {
            $clientPmName = $data['client_pm_name'];
        } elseif (!empty($data['clientpm'])) {
            $pm = $this->db->select('name')->from('employee_details')->where('empId', $data['clientpm'])->get()->row();
            $clientPmName = $pm ? $pm->name : '--';
        }
        $clientPmDisplay = $clientPmName !== '--' ? explode(' ', trim($clientPmName))[0] : '--';
        
        // Calculate totals for the date range (same as grid view)
        $totalProductionHours = 0;
        $totalGeneralHours = 0;
        $totalProjects = count($data['projects']);
        $projectQualityErrors = [];
        
        foreach ($data['projects'] as $proj) {
            // Get production hours from project data (already filtered by date range)
            $projectProductionHours = isset($proj->total_hours) ? $proj->total_hours : 0;
            $totalProductionHours += $projectProductionHours;
            
            // Get general hours from project data
            $generalHours = isset($proj->general_hours) ? $proj->general_hours : 0;
            $totalGeneralHours += $generalHours;
            
            // Check if quality error date matches date range (if date range is provided)
            $hasValidQualityError = false;
            if (!empty($monthFromDate) && !empty($monthToDate)) {
                if (!empty($proj->analyzer_report_date)) {
                    $errorDate = strtotime($proj->analyzer_report_date);
                    $fromDate = strtotime($monthFromDate);
                    $toDate = strtotime($monthToDate);
                    if ($errorDate >= $fromDate && $errorDate <= $toDate) {
                        $hasValidQualityError = true;
                    }
                }
            } else {
                $hasValidQualityError = !empty($proj->analyzer_num_of_errors) || !empty($proj->reviewer_num_of_errors);
            }
            
            if ($hasValidQualityError) {
                // Calculate quality errors (analyzer + reviewer)
                $analyzerErrors = isset($proj->analyzer_num_of_errors) ? $proj->analyzer_num_of_errors : 0;
                $reviewerErrors = isset($proj->reviewer_num_of_errors) ? $proj->reviewer_num_of_errors : 0;
                $projectTotalErrors = $analyzerErrors + $reviewerErrors;
                $projectQualityErrors[$proj->project_Id] = $projectTotalErrors;
            } else {
                $projectQualityErrors[$proj->project_Id] = null;
            }
        }
        
        // Calculate QA% = (100 - errors)% - only count projects with valid quality errors
        $k_QualityErrorPercentage = '--';
        $projectsWithValidErrors = 0;
        $sumPercentages = 0;
        foreach ($projectQualityErrors as $projId => $projErrors) {
            if ($projErrors !== null) {
                $sumPercentages += (100 - $projErrors);
                $projectsWithValidErrors++;
            }
        }
        if ($projectsWithValidErrors > 0) {
            $avgPercentage = $sumPercentages / $projectsWithValidErrors;
            $k_QualityErrorPercentage = round($avgPercentage) . '%';
        }

        $totalCombined = $totalProductionHours + $totalGeneralHours;
        $totalproductivityPercentage = $totalCombined > 0 ? ($totalProductionHours / $totalCombined) * 100 : 0;
        $totalprojectgeneralPercentage = $totalCombined > 0 ? ($totalGeneralHours / $totalCombined) * 100 : 0;

        // Calculate total invoice amount for all projects
        $totalInvoiceAmount = 0;
        foreach ($data['projects'] as $proj) {
            if (isset($proj->project_invoice_amt) && !empty($proj->project_invoice_amt)) {
                $totalInvoiceAmount += (float)$proj->project_invoice_amt;
            }
        }
        
        // Calculate difference for client row (match grid: totalInvoiceAmount - totalCombined)
        $clientDifference = $totalInvoiceAmount - $totalCombined;

        // Get billing (man_days) from first project - same as grid
        $billable = '';
        foreach ($data['projects'] as $proj) {
            if (!empty($proj->man_days)) {
                $billable = $proj->man_days;
                break;
            }
        }
        $billableFormatted = $billable !== '' && $billable !== null ? ucfirst((string) $billable) : '';

        // Client row dates: same SQL MIN/MAX logic as on-screen grid (Execution Plan pattern).
        $clientDates = client_report_resolve_client_dates($data);
        $clientStartDateTs = $clientDates['start_ts'];
        $clientEndDateTs = $clientDates['end_ts'];
        $startDateDisplay = client_report_format_client_date_display($clientStartDateTs);
        $endDateDisplay = client_report_format_client_date_display($clientEndDateTs);
        $monthNameDisplay = client_report_month_display_name($monthFromDate, $monthLabel, $clientReportMonthIncludeYear);

        // Add client row (same columns as grid: Client Name, PM, Month, Dept, Start, End, Billing, ...)
        $clientRow = [
            $data['client_name'],
            $clientPmDisplay,
            $monthNameDisplay,
            $data['department'],
            $startDateDisplay,
            $endDateDisplay,
            $billableFormatted,
            $totalProductionHours,
            $totalGeneralHours,
            $totalCombined,
            !empty($totalInvoiceAmount) ? number_format($totalInvoiceAmount, 2) : '',
            $k_QualityErrorPercentage,
            round($totalproductivityPercentage) . '%',
            round($totalprojectgeneralPercentage) . '%',
            number_format($clientDifference, 2)
        ];
        
        $sheet->fromArray($clientRow, '__xlsx_null__', 'A' . $rowNum);
        $clientExcelRows[] = $rowNum;
        if ($clientDifference >= 0) {
            $posDiffRows[] = $rowNum;
        } else {
            $negDiffRows[] = $rowNum;
        }
        $rowNum++;

        // Add project rows (same calculation as grid view)
        foreach ($data['projects'] as $proj) {
            // Get production hours from project data (already filtered by date range)
            $productionHours = isset($proj->total_hours) ? $proj->total_hours : 0;
            
            // Get general hours from project data
            $generalHours = isset($proj->general_hours) ? $proj->general_hours : 0;
            
            $combinedHours = $productionHours + $generalHours;
            
            $productivityPercentage = $combinedHours > 0 ? ($productionHours / $combinedHours) * 100 : 0;
            $projectgeneralPercentage = $combinedHours > 0 ? ($generalHours / $combinedHours) * 100 : 0;
            
            // Calculate difference in hours for project row
            $projectInvoiceAmount = isset($proj->project_invoice_amt) && !empty($proj->project_invoice_amt) ? (float)$proj->project_invoice_amt : 0;
            $projectDifference = $combinedHours - $projectInvoiceAmount;
            
            $pmName = isset($proj->pm_name) ? explode(' ', trim($proj->pm_name))[0] : '--';
            
            // Calculate individual project QA% = (100 - errors)% - only if date matches date range (if date range is provided)
            $projectQualityErrorPercentage = '--';
            if (!empty($monthFromDate) && !empty($monthToDate)) {
                if (!empty($proj->analyzer_report_date)) {
                    $errorDate = strtotime($proj->analyzer_report_date);
                    $fromDate = strtotime($monthFromDate);
                    $toDate = strtotime($monthToDate);
                    if ($errorDate >= $fromDate && $errorDate <= $toDate) {
                        $analyzerErrors = isset($proj->analyzer_num_of_errors) ? $proj->analyzer_num_of_errors : 0;
                        $reviewerErrors = isset($proj->reviewer_num_of_errors) ? $proj->reviewer_num_of_errors : 0;
                        $projectTotalErrors = $analyzerErrors + $reviewerErrors;
                        $projectQualityErrorPercentage = (100 - $projectTotalErrors) . '%';
                    }
                }
            } else {
                $analyzerErrors = isset($proj->analyzer_num_of_errors) ? $proj->analyzer_num_of_errors : 0;
                $reviewerErrors = isset($proj->reviewer_num_of_errors) ? $proj->reviewer_num_of_errors : 0;
                $projectTotalErrors = $analyzerErrors + $reviewerErrors;
                $projectQualityErrorPercentage = (100 - $projectTotalErrors) . '%';
            }
            
            // Helper function to validate date (reuse same logic)
            $isValidDateProj = function($dateStr) {
                if (empty($dateStr) || $dateStr == '0000-00-00' || $dateStr == '0000-00-00 00:00:00') {
                    return false;
                }
                $timestamp = strtotime($dateStr);
                if ($timestamp === false || $timestamp < 0) {
                    return false;
                }
                $formatted = date('Y-m-d', $timestamp);
                if ($formatted == '1970-01-01') {
                    return false;
                }
                return $timestamp;
            };
            
            $monthNameDisplay = client_report_month_display_name($monthFromDate, $monthLabel, $clientReportMonthIncludeYear);
            
            // Format individual project dates
            $projStartDateDisplay = '';
            $projEndDateDisplay = '';
            if (!empty($proj->project_start_date)) {
                $startTimestamp = $isValidDateProj($proj->project_start_date);
                if ($startTimestamp !== false) {
                    $formattedDate = date('d-M-Y', $startTimestamp);
                    if ($formattedDate != '01-Jan-1970') {
                        $projStartDateDisplay = $formattedDate;
                    }
                }
            }
            if (!empty($proj->project_end_date)) {
                $endTimestamp = $isValidDateProj($proj->project_end_date);
                if ($endTimestamp !== false) {
                    $formattedDate = date('d-M-Y', $endTimestamp);
                    if ($formattedDate != '01-Jan-1970') {
                        $projEndDateDisplay = $formattedDate;
                    }
                }
            }
            
            // Get invoice amount for this project
            $projectInvoiceAmount = '';
            if (isset($proj->project_invoice_amt) && !empty($proj->project_invoice_amt)) {
                $projectInvoiceAmount = number_format($proj->project_invoice_amt, 2);
            }
            
            // Billing: same as grid (raw man_days value)
            $projBilling = isset($proj->man_days) ? $proj->man_days : '';
            
            // Project row: first column = Project Name (same as grid), then PM, Month, Dept, Start, End, Billing, ...
            $projectRow = [
                $proj->project_name ?: '',
                $pmName ?: '--',
                $monthNameDisplay,
                $proj->department ?: '',
                $projStartDateDisplay,
                $projEndDateDisplay,
                $projBilling,
                $productionHours ?: 0,
                $generalHours ?: 0,
                $combinedHours ?: 0,
                $projectInvoiceAmount,
                $projectQualityErrorPercentage,
                round($productivityPercentage) . '%',
                round($projectgeneralPercentage) . '%',
                number_format($projectDifference, 2)
            ];
            
            $sheet->fromArray($projectRow, '__xlsx_null__', 'A' . $rowNum);
            if ($projectDifference >= 0) {
                $posDiffRows[] = $rowNum;
            } else {
                $negDiffRows[] = $rowNum;
            }
            $rowNum++;
        }
    };

    // Write month-wise data stacked top-to-bottom (April, then May, ...)
    $monthBannerStyle = array(
        'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size' => 11),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '2C5AA0')),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    if ($hasMonthWiseData && !empty($monthWiseData)) {
        foreach ($monthWiseData as $monthKey => $monthData) {
            $monthLabel = $monthData['label'];
            $monthFromDate = $monthData['from_date'];
            $monthToDate = $monthData['to_date'];
            $sortedMonthData = isset($monthData['data']) && is_array($monthData['data']) ? $monthData['data'] : array();
            uasort($sortedMonthData, function ($a, $b) {
                $nameA = isset($a['client_name']) ? (string) $a['client_name'] : '';
                $nameB = isset($b['client_name']) ? (string) $b['client_name'] : '';
                return strcasecmp($nameA, $nameB);
            });

            $sheet->setCellValue('A' . $rowNum, $monthLabel);
            $sheet->getStyle('A' . $rowNum . ':O' . $rowNum)->applyFromArray($monthBannerStyle);
            $sheet->getRowDimension($rowNum)->setRowHeight(22);
            $rowNum++;

            foreach ($sortedMonthData as $clientId => $data) {
                $writeClientProjects($data, $clientId, $monthFromDate, $monthToDate, $monthLabel, $rowNum, $sheet);
            }
        }
    } else {
        foreach ($grouped as $clientId => $data) {
            $writeClientProjects($data, $clientId, $from_date, $to_date, '', $rowNum, $sheet);
        }
    }

    $gridColWidths = array(
        'A' => 32, 'B' => 16, 'C' => 16, 'D' => 20, 'E' => 14, 'F' => 14, 'G' => 12,
        'H' => 14, 'I' => 18, 'J' => 14, 'K' => 14, 'L' => 14, 'M' => 14, 'N' => 16, 'O' => 14,
    );
    foreach ($gridColWidths as $columnID => $width) {
        $sheet->getColumnDimension($columnID)->setWidth($width);
    }

    $borderStyle = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => 'B8C4D4'),
            ),
        ),
    );
    $clientRowStyle = array(
        'font' => array('bold' => true),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E7E6E6')),
        'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
    );
    if ($rowNum > $clientTableStartRow + 1) {
        $gridDataStart = $clientTableStartRow + 1;
        $gridDataEnd = $rowNum - 1;
        $sheet->getStyle('A' . $clientTableStartRow . ':O' . $gridDataEnd)->applyFromArray($borderStyle);
        $sheet->getStyle('A' . $gridDataStart . ':O' . $gridDataEnd)->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $gridDataStart . ':A' . $gridDataEnd)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B' . $gridDataStart . ':O' . $gridDataEnd)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        foreach ($clientExcelRows as $clientRowNum) {
            $sheet->getStyle('A' . $clientRowNum . ':O' . $clientRowNum)->applyFromArray($clientRowStyle);
            $sheet->getRowDimension($clientRowNum)->setRowHeight(20);
        }
        $sheet->getStyle('C' . $gridDataStart . ':C' . $gridDataEnd)->getFont()->setBold(true);
        $sheet->getStyle('C' . $gridDataStart . ':C' . $gridDataEnd)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E8F0F8');
        foreach ($posDiffRows as $diffRowNum) {
            $sheet->getStyle('O' . $diffRowNum)->getFont()->setBold(true)->getColor()->setRGB('28a745');
        }
        foreach ($negDiffRows as $diffRowNum) {
            $sheet->getStyle('O' . $diffRowNum)->getFont()->setBold(true)->getColor()->setRGB('dc3545');
        }
    }
    $sheet->getStyle('A' . $clientTableStartRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $sheet->getRowDimension($clientTableStartRow)->setRowHeight(22);
    $sheet->freezePane('A2');
    $sheet->getSheetView()->setZoomScale(100);
    $sheet->setSelectedCell('A1');
    $objPHPExcel->setActiveSheetIndex(0);

    if (!empty($from_date) && !empty($to_date)) {
        $fromDateFormatted = date('d-m-Y', strtotime($from_date));
        $toDateFormatted = date('d-m-Y', strtotime($to_date));
        $fileName = 'client_report_' . $fromDateFormatted . '_to_' . $toDateFormatted . '.xlsx';
    } else {
        $fileName = 'client_report_' . date('Y-m-d') . '.xlsx';
    }

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $useXlsx = class_exists('ZipArchive');
    if ($useXlsx) {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    } else {
        $fileName = preg_replace('/\.xlsx$/i', '.xls', $fileName);
        header('Content-Type: application/vnd.ms-excel');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    }
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');
    $objWriter->setPreCalculateFormulas(false);
    $objWriter->save('php://output');
    exit();
}

    public function generateConsolidatedClientReportExcel() {
    $department = $this->input->get('department');
    if (!empty($department)) {
        if (is_array($department)) {
            $department = array_values(array_filter($department, function ($d) {
                return $d !== '' && $d !== '__all__';
            }));
            $department = !empty($department) ? $department : '';
        } elseif (is_string($department) && $department !== '__all__') {
            $department = array_values(array_filter(array_map('trim', explode(',', $department)), function ($d) {
                return $d !== '' && $d !== '__all__';
            }));
            $department = !empty($department) ? $department : '';
        } else {
            $department = '';
        }
    } else {
        $department = '';
    }

    $grid = $this->client_report_grid_filters_from_request();
    $search = $grid['search_merged'];
    if ($search === '') {
        $legacySearch = $this->input->get('search');
        if (!empty($legacySearch)) {
            $search = trim((string) $legacySearch);
        }
    }
    $from_date = $this->input->get('from_date');
    $to_date = $this->input->get('to_date');
    
    // Use January to current month if dates not provided
    if (empty($from_date) || empty($to_date)) {
        $currentMonth = date('n');
        $currentYear = date('Y');
        $from_date = date('Y-01-01'); // January 1st of current year
        $to_date = date('Y-m-t', mktime(0, 0, 0, $currentMonth, 1, $currentYear)); // Last day of current month
    }
    
    // Same query path as on-screen grid (ClientInformationConsolidated), then apply grid filters
    $clientInfo = $this->kpi_reports_model->ClientInformationConsolidated(0, 0, $search, $from_date, $to_date, $department);

    $grouped = array();
    foreach ($clientInfo as $row) {
        $grouped[$row->client_Id]['client_name'] = $row->client_name;
        $grouped[$row->client_Id]['department'] = $row->department;
        $grouped[$row->client_Id]['clientpm'] = $row->clientpm;
        $grouped[$row->client_Id]['client_pm_name'] = isset($row->client_pm_name) ? $row->client_pm_name : '';
        $grouped[$row->client_Id]['projects'][] = $row;
    }
    $grouped = $this->filter_client_report_grouped_data($grouped, $grid, $search);
    if (!empty($grouped) && is_array($grouped)) {
        uasort($grouped, function ($a, $b) {
            $nameA = isset($a['client_name']) ? (string) $a['client_name'] : '';
            $nameB = isset($b['client_name']) ? (string) $b['client_name'] : '';
            return strcasecmp($nameA, $nameB);
        });
    }

    // Department KPI summary (matches on-screen table; respects same filters)
    $deptKpiSummary = $this->kpi_reports_model->getClientReportDepartmentKpiSummary(
        $from_date,
        $to_date,
        $department,
        $search,
        $grid
    );

    // Load PHPExcel library
    $this->load->library('excel');
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Your System")
                                ->setLastModifiedBy("Your System")
                                ->setTitle("Consolidated Client Report - " . date('Y'))
                                ->setSubject("Year-to-Date Client Performance")
                                ->setDescription("Consolidated client performance report");

    // Create worksheet
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Consolidated Report');

    $clientTableStartRow = $this->write_client_report_dept_kpi_summary_excel($sheet, $deptKpiSummary, 1);

    // Headers: no Project Name column; project names show under client in Client Name column
    $headers = [
        'Client Name', 'Project Manager', 'Department', 'Billing',
        'Start Date', 'End Date', 'Production Hours', 'Project General Hours', 'Total Hours', 'Invoiced', 'Quality Errors', 'Productivity%', 'Project General%', 'Difference', 'Actual Vs Billable'
    ];
    $sheet->fromArray($headers, NULL, 'A' . $clientTableStartRow);

    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '5B9BD5']],
        'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER]
    ];
    $sheet->getStyle('A' . $clientTableStartRow . ':O' . $clientTableStartRow)->applyFromArray($headerStyle);

    $rowNum = $clientTableStartRow + 1;

    foreach ($grouped as $clientId => $data) {
        // Calculate consolidated totals
        // Use project ID to deduplicate - ensure each project is only counted once to prevent PG hours from repeating
        $totalconsProductionHours = 0;
        $totalconsGeneralHours = 0;
        $totalQualityErrors  = 0; 
        $processedProjects = []; // Track processed projects by project ID to avoid duplicates
        $projectQualityErrors = [];
        $totalProjectQualityErrors = 0;
        
        foreach ($data['projects'] as $proj) {
            // Skip if this project ID has already been processed (deduplication)
            $projectKey = $proj->client_Id . '_' . $proj->project_Id;
            if (isset($processedProjects[$projectKey])) {
                continue; // Skip duplicate project
            }
            $processedProjects[$projectKey] = true;
            
            // Use production hours from query result (same as grid view) - avoids N+1 queries
            $productiveHours = isset($proj->total_hours) ? (float)$proj->total_hours : 0;
            $totalconsProductionHours += $productiveHours;
            
            // Get PG (Project General) hours for this specific project - calculated per project based on project ID
            // This uses the same logic as the view, matching projects like "Domino's Kingshighway (Mech)" 
            // with general projects like "Domino's Kingshighway - Mech (General)"
            // Only count once per unique project to prevent PG hours from repeating
            $generalHours = isset($proj->general_hours) ? $proj->general_hours : 0;
            $totalconsGeneralHours += $generalHours;
            
            // Check if quality error date matches date range (if date range is provided)
            $hasValidQualityError = false;
            if (!empty($from_date) && !empty($to_date)) {
                if (!empty($proj->analyzer_report_date)) {
                    $errorDate = strtotime($proj->analyzer_report_date);
                    $fromDate = strtotime($from_date);
                    $toDate = strtotime($to_date);
                    if ($errorDate >= $fromDate && $errorDate <= $toDate) {
                        $hasValidQualityError = true;
                    }
                }
            } else {
                $hasValidQualityError = !empty($proj->analyzer_num_of_errors) || !empty($proj->reviewer_num_of_errors);
            }
            
            if ($hasValidQualityError) {
                $analyzerErrors = isset($proj->analyzer_num_of_errors) ? $proj->analyzer_num_of_errors : 0;
                $reviewerErrors = isset($proj->reviewer_num_of_errors) ? $proj->reviewer_num_of_errors : 0;
                $projectTotalErrors = $analyzerErrors + $reviewerErrors;
                $projectQualityErrors[$proj->project_Id] = $projectTotalErrors;
                $totalProjectQualityErrors += $projectTotalErrors;
            } else {
                $projectQualityErrors[$proj->project_Id] = null;
            }
                
        }

        $k_QualityErrorPercentage = '--';
        $projectsWithValidErrors = 0;
        $sumPercentages = 0;
        foreach ($projectQualityErrors as $projId => $projErrors) {
            if ($projErrors !== null) {
                $sumPercentages += (100 - $projErrors);
                $projectsWithValidErrors++;
            }
        }
        if ($projectsWithValidErrors > 0) {
            $avgPercentage = $sumPercentages / $projectsWithValidErrors;
            $k_QualityErrorPercentage = round($avgPercentage) . '%';
        }

        $totalconsCombined = $totalconsProductionHours + $totalconsGeneralHours;
        $consproductivityPercentage = $totalconsCombined > 0 ? ($totalconsProductionHours / $totalconsCombined) * 100 : 0;
        $consprojectgeneralPercentage = $totalconsCombined > 0 ? ($totalconsGeneralHours / $totalconsCombined) * 100 : 0;

        // Calculate total invoice amount for all projects
        $totalconsInvoiceAmount = 0;
        foreach ($data['projects'] as $proj) {
            if (isset($proj->project_invoice_amt) && !empty($proj->project_invoice_amt)) {
                $totalconsInvoiceAmount += (float)$proj->project_invoice_amt;
            }
        }
        
        // Calculate difference in hours for client row
        $consClientDifference = $totalconsCombined - $totalconsInvoiceAmount;

        // Get client PM name
        $clientPmName = '--';
        if (!empty($data['clientpm'])) {
            $pm = $this->db->select('name')->from('employee_details')->where('empId', $data['clientpm'])->get()->row();
            $clientPmName = $pm ? explode(' ', trim($pm->name))[0] : '--';
        }

        // Get billing from first project with man_days; format Hourly/Monthly with first letter capital
        $billable = '';
        foreach ($data['projects'] as $proj) {
            if (!empty($proj->man_days)) {
                $billable = $proj->man_days;
                break;
            }
        }
        $billableFormatted = $billable;
        if ($billable !== '' && $billable !== null && !is_numeric(trim((string)$billable))) {
            $billableFormatted = ucfirst(strtolower(trim((string)$billable)));
        }

        // Helper function to validate and format date
        $isValidDate = function($dateStr) {
            if (empty($dateStr) || $dateStr == '0000-00-00' || $dateStr == '0000-00-00 00:00:00') {
                return false;
            }
            $timestamp = strtotime($dateStr);
            if ($timestamp === false || $timestamp < 0) {
                return false;
            }
            // Check if date is not 1970-01-01 (invalid date fallback)
            $formatted = date('Y-m-d', $timestamp);
            if ($formatted == '1970-01-01') {
                return false;
            }
            return $timestamp;
        };
        
        // Calculate earliest start date and latest end date from all projects
        // Use last_work_date from emp_record_details for end date instead of project_end_date (same as view)
        $earliestStartDate = null;
        $latestEndDate = null;
        foreach ($data['projects'] as $proj) {
            if (!empty($proj->project_start_date)) {
                $projStartDate = $isValidDate($proj->project_start_date);
                if ($projStartDate !== false && ($earliestStartDate === null || $projStartDate < $earliestStartDate)) {
                    $earliestStartDate = $projStartDate;
                }
            }
            // Use last_work_date from emp_record_details if available, otherwise fall back to project_end_date
            $endDateToUse = !empty($proj->last_work_date) ? $proj->last_work_date : (isset($proj->project_end_date) ? $proj->project_end_date : null);
            if (!empty($endDateToUse)) {
                $projEndDate = $isValidDate($endDateToUse);
                if ($projEndDate !== false && ($latestEndDate === null || $projEndDate > $latestEndDate)) {
                    $latestEndDate = $projEndDate;
                }
            }
        }

        // Format dates for client row based on projects
        $startDateDisplay = '--';
        $endDateDisplay = '--';
        if ($earliestStartDate !== null) {
            $formattedDate = date('d-M-Y', $earliestStartDate);
            // Check if formatted date is not 01-Jan-1970
            if ($formattedDate != '01-Jan-1970') {
                $startDateDisplay = $formattedDate;
            }
        }
        if ($latestEndDate !== null) {
            $formattedDate = date('d-M-Y', $latestEndDate);
            // Check if formatted date is not 01-Jan-1970
            if ($formattedDate != '01-Jan-1970') {
                $endDateDisplay = $formattedDate;
            }
        }
        
        // Add client row (bold with light fill); first column = client name
        $clientRow = [
            $data['client_name'],
            $clientPmName,
            $data['department'],
            $billableFormatted,
            $startDateDisplay,
            $endDateDisplay,
            $totalconsProductionHours,
            $totalconsGeneralHours,
            $totalconsCombined,
            !empty($totalconsInvoiceAmount) ? number_format($totalconsInvoiceAmount, 2) : '',
            $k_QualityErrorPercentage,
            round($consproductivityPercentage),
            round($consprojectgeneralPercentage),
            number_format($consClientDifference, 2),
            ''  // Actual vs Billable
        ];
        
        $sheet->fromArray($clientRow, NULL, 'A' . $rowNum);
        
        // Style client row
        $clientStyle = [
            'font' => ['bold' => true],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'E7E6E6']]
        ];
        $sheet->getStyle('A' . $rowNum . ':O' . $rowNum)->applyFromArray($clientStyle);
        
        // Apply color to Difference column for client row (column N)
        $diffCell = 'N' . $rowNum;
        $diffColor = $consClientDifference >= 0 ? '28a745' : 'dc3545'; // Green for positive, red for negative
        $diffStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => $diffColor]]
        ];
        $sheet->getStyle($diffCell)->applyFromArray($diffStyle);
        $rowNum++;

        // Add project rows
        // Use project ID to deduplicate - ensure each project appears only once to prevent PG hours from repeating
        $displayedProjects = []; // Track displayed projects by project ID to avoid duplicates
        foreach ($data['projects'] as $proj) {
            // Skip if this project ID has already been displayed (deduplication)
            $projectKey = $proj->client_Id . '_' . $proj->project_Id;
            if (isset($displayedProjects[$projectKey])) {
                continue; // Skip duplicate project
            }
            $displayedProjects[$projectKey] = true;
            
            // Use production hours from query result (same as grid view) - avoids N+1 queries
            $productiveHours = isset($proj->total_hours) ? (float)$proj->total_hours : 0;
            
            // Get PG (Project General) hours for this specific project - calculated per project based on project ID
            // This uses the same logic as the view, matching projects like "Domino's Kingshighway (Mech)" 
            // with general projects like "Domino's Kingshighway - Mech (General)"
            // Only display once per unique project to prevent PG hours from repeating
            $generalHours = isset($proj->general_hours) ? $proj->general_hours : 0;
            $combinedHours = $productiveHours + $generalHours;
            
            $productivityPercentage = $combinedHours > 0 ? ($productiveHours / $combinedHours) * 100 : 0;
            $projectgeneralPercentage = $combinedHours > 0 ? ($generalHours / $combinedHours) * 100 : 0;
            
            // Calculate difference in hours for project row
            $consProjectInvoiceAmount = isset($proj->project_invoice_amt) && !empty($proj->project_invoice_amt) ? (float)$proj->project_invoice_amt : 0;
            $consProjectDifference = $combinedHours - $consProjectInvoiceAmount;
            
            $pmName = explode(' ', trim($proj->pm_name))[0];
            
            // Calculate individual project quality error percentage - only if date matches date range (if date range is provided)
            $projectQualityErrorPercentage = '--';
            if (!empty($from_date) && !empty($to_date)) {
                if (!empty($proj->analyzer_report_date)) {
                    $errorDate = strtotime($proj->analyzer_report_date);
                    $fromDate = strtotime($from_date);
                    $toDate = strtotime($to_date);
                    if ($errorDate >= $fromDate && $errorDate <= $toDate) {
                        $analyzerErrors = isset($proj->analyzer_num_of_errors) ? $proj->analyzer_num_of_errors : 0;
                        $reviewerErrors = isset($proj->reviewer_num_of_errors) ? $proj->reviewer_num_of_errors : 0;
                        $projectTotalErrors = $analyzerErrors + $reviewerErrors;
                        $projectQualityErrorPercentage = (100 - $projectTotalErrors) . '%';
                    }
                }
            } else {
                $analyzerErrors = isset($proj->analyzer_num_of_errors) ? $proj->analyzer_num_of_errors : 0;
                $reviewerErrors = isset($proj->reviewer_num_of_errors) ? $proj->reviewer_num_of_errors : 0;
                $projectTotalErrors = $analyzerErrors + $reviewerErrors;
                $projectQualityErrorPercentage = (100 - $projectTotalErrors) . '%';
            }

            // Helper function to validate date (reuse same logic)
            $isValidDateProj = function($dateStr) {
                if (empty($dateStr) || $dateStr == '0000-00-00' || $dateStr == '0000-00-00 00:00:00') {
                    return false;
                }
                $timestamp = strtotime($dateStr);
                if ($timestamp === false || $timestamp < 0) {
                    return false;
                }
                $formatted = date('Y-m-d', $timestamp);
                if ($formatted == '1970-01-01') {
                    return false;
                }
                return $timestamp;
            };
            
            // Format start and end dates separately
            // Use last_work_date from emp_record_details for end date instead of project_end_date (same as view)
            $startDateDisplay = '--';
            $endDateDisplay = '--';
            if (!empty($proj->project_start_date)) {
                $startTimestamp = $isValidDateProj($proj->project_start_date);
                if ($startTimestamp !== false) {
                    $formattedDate = date('d-M-Y', $startTimestamp);
                    if ($formattedDate != '01-Jan-1970') {
                        $startDateDisplay = $formattedDate;
                    }
                }
            }
            // Use last_work_date from emp_record_details if available, otherwise fall back to project_end_date
            $endDateToUse = !empty($proj->last_work_date) ? $proj->last_work_date : (isset($proj->project_end_date) ? $proj->project_end_date : null);
            if (!empty($endDateToUse)) {
                $endTimestamp = $isValidDateProj($endDateToUse);
                if ($endTimestamp !== false) {
                    $formattedDate = date('d-M-Y', $endTimestamp);
                    if ($formattedDate != '01-Jan-1970') {
                        $endDateDisplay = $formattedDate;
                    }
                }
            }
            
            // Get invoice amount for this project
            $projectInvoiceAmount = '';
            if (isset($proj->project_invoice_amt) && !empty($proj->project_invoice_amt)) {
                $projectInvoiceAmount = number_format($proj->project_invoice_amt, 2);
            }
            
            // Billing: Hourly/Monthly with first letter capital
            $projBilling = $proj->man_days ?: '';
            if ($projBilling !== '' && !is_numeric(trim((string)$projBilling))) {
                $projBilling = ucfirst(strtolower(trim((string)$projBilling)));
            }
            
            // Project row: project name in first column (below client name, same Client Name column)
            $projectRow = [
                $proj->project_name ?: '',
                $pmName,
                $proj->department,
                $projBilling,
                $startDateDisplay,
                $endDateDisplay,
                $productiveHours,
                $generalHours,
                $combinedHours,
                $projectInvoiceAmount,
                $projectQualityErrorPercentage,
                round($productivityPercentage),
                round($projectgeneralPercentage),
                number_format($consProjectDifference, 2),
                ''  // Actual vs Billable
            ];
            
            $sheet->fromArray($projectRow, NULL, 'A' . $rowNum);
            
            // Apply color to Difference column for project row (column N)
            $diffCell = 'N' . $rowNum;
            $diffColor = $consProjectDifference >= 0 ? '28a745' : 'dc3545'; // Green for positive, red for negative
            $diffStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => $diffColor]]
            ];
            $sheet->getStyle($diffCell)->applyFromArray($diffStyle);
            
            // Alternate row coloring
            if ($rowNum % 2 == 0) {
                $sheet->getStyle('A' . $rowNum . ':O' . $rowNum)
                      ->getFill()
                      ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                      ->getStartColor()
                      ->setRGB('F2F2F2');
            }
            
            $rowNum++;
        }
    }

    // Formatting
    foreach (range('A', 'O') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Add borders
    $borderStyle = [
        'borders' => [
            'allborders' => [
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => ['rgb' => 'DDDDDD']
            ]
        ]
    ];
    if ($rowNum > $clientTableStartRow + 1) {
        $sheet->getStyle('A' . $clientTableStartRow . ':O' . ($rowNum - 1))->applyFromArray($borderStyle);
    }

    // Client Name column (A) left-aligned; rest of columns (B–O) centered
    $leftStyle = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT]];
    $centerStyle = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER]];
    if ($rowNum > $clientTableStartRow + 1) {
        $sheet->getStyle('A' . ($clientTableStartRow + 1) . ':A' . ($rowNum - 1))->applyFromArray($leftStyle);
        $sheet->getStyle('B' . ($clientTableStartRow + 1) . ':O' . ($rowNum - 1))->applyFromArray($centerStyle);
    }
    $sheet->getStyle('A' . $clientTableStartRow)->applyFromArray($leftStyle);

    $sheet->freezePane('A' . ($clientTableStartRow + 1));

    // Output - Generate filename based on date range
    if (!empty($from_date) && !empty($to_date)) {
        $fromDateFormatted = date('d-M-Y', strtotime($from_date));
        $toDateFormatted = date('d-M-Y', strtotime($to_date));
        $filename = 'consolidated_client_report_' . $fromDateFormatted . '_to_' . $toDateFormatted . '.xls';
    } else {
        $previousMonthName = date('F', strtotime('last month'));
        $filename = 'consolidated_client_report_jan_' . strtolower($previousMonthName) . '.xls';
    }

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');

    header('Cache-Control: max-age=0');
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit();
}

/**
 * Feedback Form - Display form for employees to submit feedback
 */
public function feedbackForm() {
    $data = array();
    
    // Get current user information
    if (!empty($this->session->userdata['logged_in_timesheet']['username'])) {
        $userDetails = $this->timesheet_login->user_information($this->session->userdata['logged_in_timesheet']['username']);
        foreach ($userDetails as $key) {
            $data['current_emp'] = $key;
        }
    }
    
    $this->load->view('kpi-reports/feedback-form', $data);
}

/**
 * Edit Feedback - Load feedback data for editing
 */
public function editFeedback($feedback_id = NULL) {
    if (empty($feedback_id)) {
        $this->session->set_flashdata('error', 'Invalid feedback ID.');
        redirect('kpi_reports/feedbackReports');
    }
    
    // Get feedback details
    $feedback = $this->feedback_model->get_feedback($feedback_id);
    if (empty($feedback)) {
        $this->session->set_flashdata('error', 'Feedback not found.');
        redirect('kpi_reports/feedbackReports');
    }
    
    // Check permissions - user must be the creator, reporting manager, or team member
    $logged_in_empId = intval($this->session->userdata['logged_in_timesheet']['empId']);
    $feedback_reporting_manager = !empty($feedback->reporting_manager) ? intval($feedback->reporting_manager) : 0;
    $feedback_empId = !empty($feedback->empId) ? intval($feedback->empId) : 0;
    $feedback_team_member = !empty($feedback->team_members) ? intval($feedback->team_members) : 0;
    $user_type = $this->session->userdata['logged_in_timesheet']['user_type'];
    $is_admin = in_array($user_type, ['admin', 'superadmin']);
    
    // Can edit if user is admin, reporting manager, creator, or team member (HR is view-only)
    $can_edit = $is_admin ||
                ($feedback_reporting_manager > 0 && $feedback_reporting_manager == $logged_in_empId) || 
                ($feedback_empId > 0 && $feedback_empId == $logged_in_empId) || 
                ($feedback_team_member > 0 && $feedback_team_member == $logged_in_empId);
    
    if (!$can_edit) {
        $this->session->set_flashdata('error', 'You do not have permission to edit this feedback.');
        redirect('kpi_reports/feedbackReports');
    }
    
    $data = array();
    $data['feedback'] = $feedback;
    $data['is_edit_mode'] = TRUE;
    
    // Get current user information
    if (!empty($this->session->userdata['logged_in_timesheet']['username'])) {
        $userDetails = $this->timesheet_login->user_information($this->session->userdata['logged_in_timesheet']['username']);
        foreach ($userDetails as $key) {
            $data['current_emp'] = $key;
        }
    }
    
    // Parse feedback_month to extract from/to dates
    if (!empty($feedback->feedback_month)) {
        // Format: "2024-JAN to 2024-FEB" or similar
        $month_parts = explode(' to ', $feedback->feedback_month);
        if (count($month_parts) == 2) {
            $from_part = trim($month_parts[0]);
            $to_part = trim($month_parts[1]);
            
            // Convert "2024-JAN" to "2024-01"
            $month_map = array(
                'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
                'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08',
                'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'
            );
            
            $from_parts = explode('-', $from_part);
            $to_parts = explode('-', $to_part);
            
            if (count($from_parts) == 2 && count($to_parts) == 2) {
                $from_year = $from_parts[0];
                $from_month_abbr = strtoupper($from_parts[1]);
                $to_year = $to_parts[0];
                $to_month_abbr = strtoupper($to_parts[1]);
                
                $data['feedback_month_from'] = isset($month_map[$from_month_abbr]) ? $from_year . '-' . $month_map[$from_month_abbr] : '';
                $data['feedback_month_to'] = isset($month_map[$to_month_abbr]) ? $to_year . '-' . $month_map[$to_month_abbr] : '';
            }
        }
    }
    
    // Parse feedback_type JSON to array
    if (!empty($feedback->feedback_type)) {
        $decoded = json_decode($feedback->feedback_type, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $data['feedback_types'] = $decoded;
        } else {
            $data['feedback_types'] = array($feedback->feedback_type);
        }
    } else {
        $data['feedback_types'] = array();
    }
    
    // Parse sub_categories JSON to array
    if (!empty($feedback->sub_categories)) {
        $decoded = json_decode($feedback->sub_categories, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $data['sub_categories'] = $decoded;
        } else {
            $data['sub_categories'] = array($feedback->sub_categories);
        }
    } else {
        $data['sub_categories'] = array();
    }
    
    $this->load->view('kpi-reports/feedback-form', $data);
}

/**
 * Update Feedback Form - Handle edited feedback submission
 */
public function updateFeedbackForm() {
    $feedback_id = $this->input->post('feedback_id');
    
    if (empty($feedback_id)) {
        $this->session->set_flashdata('error', 'Invalid feedback ID.');
        redirect('kpi_reports/feedbackReports');
    }
    
    // Get feedback details
    $feedback = $this->feedback_model->get_feedback($feedback_id);
    if (empty($feedback)) {
        $this->session->set_flashdata('error', 'Feedback not found.');
        redirect('kpi_reports/feedbackReports');
    }
    
    // Check permissions
    $logged_in_empId = intval($this->session->userdata['logged_in_timesheet']['empId']);
    $feedback_reporting_manager = !empty($feedback->reporting_manager) ? intval($feedback->reporting_manager) : 0;
    $feedback_empId = !empty($feedback->empId) ? intval($feedback->empId) : 0;
    $feedback_team_member = !empty($feedback->team_members) ? intval($feedback->team_members) : 0;
    $user_type = $this->session->userdata['logged_in_timesheet']['user_type'];
    $is_admin = in_array($user_type, ['admin', 'superadmin']);
    
    // HR is view-only; can edit if admin, reporting manager, creator, or team member
    $can_edit = $is_admin ||
                ($feedback_reporting_manager > 0 && $feedback_reporting_manager == $logged_in_empId) || 
                ($feedback_empId > 0 && $feedback_empId == $logged_in_empId) || 
                ($feedback_team_member > 0 && $feedback_team_member == $logged_in_empId);
    
    if (!$can_edit) {
        $this->session->set_flashdata('error', 'You do not have permission to edit this feedback.');
        redirect('kpi_reports/feedbackReports');
    }
    
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
    
    $this->form_validation->set_rules('feedback_message', 'Feedback Message', 'required|trim');
    $this->form_validation->set_rules('feedback_for', 'Feedback for', 'required|trim');
    $this->form_validation->set_rules('feedback_month_from', 'Feedback From Month', 'required|trim');
    $this->form_validation->set_rules('feedback_month_to', 'Feedback To Month', 'required|trim');
    
    // Custom validation for feedback_type array
    $feedback_types_post = $this->input->post('feedback_type');
    if (empty($feedback_types_post) && isset($_POST['feedback_type'])) {
        $feedback_types_post = $_POST['feedback_type'];
    }
    
    $has_feedback_type = false;
    if (!empty($feedback_types_post)) {
        if (is_array($feedback_types_post)) {
            $has_feedback_type = count(array_filter($feedback_types_post)) > 0;
        } else {
            $has_feedback_type = !empty(trim($feedback_types_post));
        }
    }
    
    if (!$has_feedback_type) {
        $this->form_validation->set_rules('feedback_type', 'Improvement area', 'required', array('required' => 'Please select at least one Improvement area.'));
    }
    
    if ($this->form_validation->run() == FALSE) {
        $this->editFeedback($feedback_id);
    } else {
        // Handle file upload
        $attached_file = $feedback->attached_file; // Keep existing file if no new upload
        if (!empty($_FILES['attached_file']['name'])) {
            $config['upload_path'] = './uploads/feedback/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|txt';
            $config['max_size'] = 10240; // 10MB
            $config['encrypt_name'] = TRUE;
            
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('attached_file')) {
                // Delete old file if exists
                if (!empty($feedback->attached_file) && file_exists('./' . $feedback->attached_file)) {
                    @unlink('./' . $feedback->attached_file);
                }
                $upload_data = $this->upload->data();
                $attached_file = 'uploads/feedback/' . $upload_data['file_name'];
            }
        }
        
        // Get department from form
        $form_department = $this->input->post('department') ? $this->input->post('department') : $feedback->department;
        
        // Handle team member
        $team_members = $this->input->post('team_members') ? $this->input->post('team_members') : NULL;
        
        // Handle sub-categories
        $sub_categories = $this->input->post('sub_categories');
        $sub_categories_array = array();
        if (!empty($sub_categories) && is_array($sub_categories)) {
            $sub_categories_array = array_filter(array_map('trim', $sub_categories));
        }
        $sub_categories_json = !empty($sub_categories_array) ? json_encode($sub_categories_array) : NULL;
        
        // Handle multiple improvement areas
        $feedback_types = $this->input->post('feedback_type');
        if (empty($feedback_types) && isset($_POST['feedback_type'])) {
            $feedback_types = $_POST['feedback_type'];
        }
        
        $feedback_types_array = array();
        if (!empty($feedback_types)) {
            if (is_array($feedback_types)) {
                $feedback_types_array = array_filter(array_map('trim', $feedback_types));
            } else {
                $feedback_types_array = array(trim($feedback_types));
            }
        }
        
        if (empty($feedback_types_array)) {
            $this->session->set_flashdata('error', 'Please select at least one Improvement area.');
            redirect('kpi_reports/editFeedback/' . $feedback_id);
            return;
        }
        
        $feedback_types_json = json_encode($feedback_types_array);
        
        // Process feedback month
        $feedback_month_from = $this->input->post('feedback_month_from');
        $feedback_month_to = $this->input->post('feedback_month_to');
        $feedback_month = NULL;
        
        if (!empty($feedback_month_from) && !empty($feedback_month_to)) {
            $month_abbr = array('01' => 'JAN', '02' => 'FEB', '03' => 'MAR', '04' => 'APR', 
                               '05' => 'MAY', '06' => 'JUN', '07' => 'JUL', '08' => 'AUG', 
                               '09' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC');
            
            $from_parts = explode('-', $feedback_month_from);
            $to_parts = explode('-', $feedback_month_to);
            
            $from_year = isset($from_parts[0]) ? $from_parts[0] : '';
            $from_month = isset($from_parts[1]) && isset($month_abbr[$from_parts[1]]) ? $month_abbr[$from_parts[1]] : '';
            
            $to_year = isset($to_parts[0]) ? $to_parts[0] : '';
            $to_month = isset($to_parts[1]) && isset($month_abbr[$to_parts[1]]) ? ucfirst(strtolower($month_abbr[$to_parts[1]])) : '';
            
            if ($from_year && $from_month && $to_year && $to_month) {
                $feedback_month = $from_year . '-' . $from_month . ' to ' . $to_year . '-' . $to_month;
            }
        }
        
        $data = array(
            'department' => $form_department,
            'feedback_type' => $feedback_types_json,
            'feedback_message' => $this->input->post('feedback_message'),
            'reporting_manager' => $this->input->post('reporting_manager') ? $this->input->post('reporting_manager') : NULL,
            'project_coordinator' => $this->input->post('project_coordinator') ? $this->input->post('project_coordinator') : NULL,
            'team_members' => $team_members,
            'feedback_month' => $feedback_month,
            'attached_file' => $attached_file,
            'feedback_for' => $this->input->post('feedback_for') ? $this->input->post('feedback_for') : NULL,
            'strengths_achievements' => $this->input->post('strengths_achievements') ? $this->input->post('strengths_achievements') : NULL,
            'sub_categories' => $sub_categories_json,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $result = $this->feedback_model->update_feedback($data, $feedback_id);
        
        if ($result) {
            $this->session->set_flashdata('success', 'Feedback updated successfully!');
            redirect('kpi_reports/feedbackReports');
        } else {
            $this->session->set_flashdata('error', 'Failed to update feedback. Please try again.');
            redirect('kpi_reports/editFeedback/' . $feedback_id);
        }
    }
}

/**
 * Submit Feedback - Handle form submission
 */
public function submitFeedback() {
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
    
    $this->form_validation->set_rules('feedback_message', 'Feedback Message', 'required|trim');
    $this->form_validation->set_rules('feedback_for', 'Feedback for', 'required|trim');
    $this->form_validation->set_rules('feedback_month_from', 'Feedback From Month', 'required|trim');
    $this->form_validation->set_rules('feedback_month_to', 'Feedback To Month', 'required|trim');
    
    // Custom validation for feedback_type array - check before validation runs
    $feedback_types_post = $this->input->post('feedback_type');
    // Also try getting from raw POST if CodeIgniter doesn't capture it
    if (empty($feedback_types_post) && isset($_POST['feedback_type'])) {
        $feedback_types_post = $_POST['feedback_type'];
    }
    
    $has_feedback_type = false;
    if (!empty($feedback_types_post)) {
        if (is_array($feedback_types_post)) {
            $has_feedback_type = count(array_filter($feedback_types_post)) > 0;
        } else {
            $has_feedback_type = !empty(trim($feedback_types_post));
        }
    }
    
    if (!$has_feedback_type) {
        $this->form_validation->set_rules('feedback_type', 'Improvement area', 'required', array('required' => 'Please select at least one Improvement area.'));
    }
    
    if ($this->form_validation->run() == FALSE) {
        $this->feedbackForm();
    } else {
        // Get current user information
        $empId = $this->session->userdata['logged_in_timesheet']['empId'];
        $userDetails = $this->timesheet_login->user_information($this->session->userdata['logged_in_timesheet']['username']);
        $employee_name = '';
        $department = '';
        
        foreach ($userDetails as $key) {
            $employee_name = $key->name;
            $department = $key->department;
        }
        
        // Handle file upload
        $attached_file = '';
        if (!empty($_FILES['attached_file']['name'])) {
            $config['upload_path'] = './uploads/feedback/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|txt';
            $config['max_size'] = 10240; // 10MB
            $config['encrypt_name'] = TRUE;
            
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('attached_file')) {
                $upload_data = $this->upload->data();
                $attached_file = 'uploads/feedback/' . $upload_data['file_name'];
            }
        }
        
        // Get department from form if provided, otherwise use user's department
        $form_department = $this->input->post('department') ? $this->input->post('department') : $department;
        
        // Handle team member (single selection)
        $team_members = $this->input->post('team_members') ? $this->input->post('team_members') : NULL;
        
        // Handle sub-categories - filter out empty values and store as JSON
        $sub_categories = $this->input->post('sub_categories');
        $sub_categories_array = array();
        if (!empty($sub_categories) && is_array($sub_categories)) {
            $sub_categories_array = array_filter(array_map('trim', $sub_categories)); // Remove empty values
        }
        $sub_categories_json = !empty($sub_categories_array) ? json_encode($sub_categories_array) : NULL;
        
        // Handle multiple improvement areas - store as JSON
        // CodeIgniter handles array inputs differently - try both methods
        $feedback_types = $this->input->post('feedback_type');
        
        // If empty, try getting from raw POST data (for array inputs)
        if (empty($feedback_types) && isset($_POST['feedback_type'])) {
            $feedback_types = $_POST['feedback_type'];
        }
        
        // Debug: Log what we received
       log_message('debug', 'Raw POST feedback_type: ' . print_r(isset($_POST['feedback_type']) ? $_POST['feedback_type'] : 'NOT SET', true));
       log_message('debug', 'CodeIgniter input feedback_type: ' . print_r($feedback_types, true));        
        $feedback_types_array = array();
        
        if (!empty($feedback_types)) {
            if (is_array($feedback_types)) {
                $feedback_types_array = array_filter(array_map('trim', $feedback_types)); // Remove empty values
            } else {
                // If single value (backward compatibility), convert to array
                $feedback_types_array = array(trim($feedback_types));
            }
        }
        
        // Ensure we have at least one improvement area
        if (empty($feedback_types_array)) {
            $this->session->set_flashdata('error', 'Please select at least one Improvement area.');
            redirect('kpi_reports/feedbackForm');
            return;
        }
        
        $feedback_types_json = json_encode($feedback_types_array);
        
        // Debug: Log the JSON being saved
        log_message('debug', 'Feedback types JSON to save: ' . $feedback_types_json);
        
        // Process feedback month from and to fields
        $feedback_month_from = $this->input->post('feedback_month_from');
        $feedback_month_to = $this->input->post('feedback_month_to');
        $feedback_month = NULL;
        
        if (!empty($feedback_month_from) && !empty($feedback_month_to)) {
            // Convert YYYY-MM to month abbreviations with year
            $month_abbr = array('01' => 'JAN', '02' => 'FEB', '03' => 'MAR', '04' => 'APR', 
                               '05' => 'MAY', '06' => 'JUN', '07' => 'JUL', '08' => 'AUG', 
                               '09' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC');
            
            $from_parts = explode('-', $feedback_month_from);
            $to_parts = explode('-', $feedback_month_to);
            
            $from_year = isset($from_parts[0]) ? $from_parts[0] : '';
            $from_month = isset($from_parts[1]) && isset($month_abbr[$from_parts[1]]) ? $month_abbr[$from_parts[1]] : '';
            
            $to_year = isset($to_parts[0]) ? $to_parts[0] : '';
            $to_month = isset($to_parts[1]) && isset($month_abbr[$to_parts[1]]) ? ucfirst(strtolower($month_abbr[$to_parts[1]])) : '';
            
            if ($from_year && $from_month && $to_year && $to_month) {
                $feedback_month = $from_year . '-' . $from_month . ' to ' . $to_year . '-' . $to_month;
            }
        }
        
        $data = array(
            'empId' => $empId,
            'employee_name' => $employee_name,
            'department' => $form_department,
            'feedback_type' => $feedback_types_json, // Store as JSON for multiple selections
            'subject' => '',
            'feedback_message' => $this->input->post('feedback_message'),
            'status' => 'Sent',
            'reporting_manager' => $this->input->post('reporting_manager') ? $this->input->post('reporting_manager') : NULL,
            'project_coordinator' => $this->input->post('project_coordinator') ? $this->input->post('project_coordinator') : NULL,
            'team_members' => $team_members,
            'feedback_month' => $feedback_month,
            'attached_file' => $attached_file,
            'feedback_for' => $this->input->post('feedback_for') ? $this->input->post('feedback_for') : NULL,
            'strengths_achievements' => $this->input->post('strengths_achievements') ? $this->input->post('strengths_achievements') : NULL,
            'sub_categories' => $sub_categories_json,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        // Debug: Log the data being saved (especially feedback_type)
        log_message('debug', 'Controller - Feedback data - feedback_type: ' . $feedback_types_json);
        log_message('debug', 'Controller - Feedback data array: ' . print_r($data, true));
        
        $feedback_id = $this->feedback_model->add_feedback($data);
        
        // Debug: Log the result
        log_message('debug', 'Controller - Feedback insert result ID: ' . ($feedback_id ? $feedback_id : 'FALSE'));
        
        if ($feedback_id) {
            // Send email notifications to team member and reporting manager
            $this->sendFeedbackSubmissionEmails($feedback_id, $data);
            
            $this->session->set_flashdata('success', 'Feedback submitted successfully!');
            // Redirect to feedback reports view
            redirect('kpi_reports/feedbackReports');
        } else {
            $this->session->set_flashdata('error', 'Failed to submit feedback. Please try again.');
            redirect('kpi_reports/feedbackForm');
        }
    }
}

/**
 * My Feedback - Display feedback for logged-in employee
 */
public function myFeedback() {
    $data = array();
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];
    
    // Get feedback for this employee
    $filters = array('empId' => $empId);
    $data['feedback_list'] = $this->feedback_model->get_feedback(NULL, $filters);
    
    $this->load->view('kpi-reports/my-feedback', $data);
}

/**
 * Manager Feedback Form - For managers to give feedback to employees
 */
public function managerFeedbackForm() {
    $data = array();
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];
    $user_type = $this->session->userdata['logged_in_timesheet']['user_type'];
    
    // Check if user is manager
    if (!in_array($user_type, ['admin', 'manager', 'business_head', 'superadmin'])) {
        $this->session->set_flashdata('error', 'You do not have permission to access this page.');
        redirect('kpi_reports/myFeedback');
    }
    
    // Get team members for this manager
    $data['team_members'] = $this->feedback_model->get_project_coordinators_by_manager($empId);
    
    $this->load->view('kpi-reports/manager-feedback-form', $data);
}

/**
 * Submit Manager Feedback - Handle manager feedback submission
 */
public function submitManagerFeedback() {
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
    
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];
    $user_type = $this->session->userdata['logged_in_timesheet']['user_type'];
    
    // Check if user is manager
    if (!in_array($user_type, ['admin', 'manager', 'business_head', 'superadmin'])) {
        $this->session->set_flashdata('error', 'You do not have permission to submit feedback.');
        redirect('kpi_reports/myFeedback');
    }
    
    $this->form_validation->set_rules('employee_id', 'Employee', 'required|trim');
    $this->form_validation->set_rules('feedback_type[]', 'Improvement area', 'required');
    $this->form_validation->set_rules('feedback_message', 'Feedback for improvement', 'required|trim');
    $this->form_validation->set_rules('feedback_for', 'Feedback for', 'required|trim');
    $this->form_validation->set_rules('feedback_month_from', 'Feedback From Month', 'required|trim');
    $this->form_validation->set_rules('feedback_month_to', 'Feedback To Month', 'required|trim');
    
    if ($this->form_validation->run() == FALSE) {
        $this->managerFeedbackForm();
    } else {
        // Get employee details
        $employee_id = $this->input->post('employee_id');
        $employee_details = $this->timesheet_login->getEmployees($employee_id);
        $employee_name = '';
        $employee_dept = '';
        
        if ($employee_details && !empty($employee_details)) {
            $employee_name = $employee_details[0]->name;
            $employee_dept = $employee_details[0]->department;
        }
        
        // Handle file upload
        $attached_file = '';
        if (!empty($_FILES['attached_file']['name'])) {
            $config['upload_path'] = './uploads/feedback/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|txt';
            $config['max_size'] = 10240; // 10MB
            $config['encrypt_name'] = TRUE;
            
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('attached_file')) {
                $upload_data = $this->upload->data();
                $attached_file = 'uploads/feedback/' . $upload_data['file_name'];
            }
        }
        
        // Handle multiple improvement areas - store as JSON
        // CodeIgniter handles array inputs differently - try both methods
        $feedback_types = $this->input->post('feedback_type');
        
        // If empty, try getting from raw POST data (for array inputs)
        if (empty($feedback_types) && isset($_POST['feedback_type'])) {
            $feedback_types = $_POST['feedback_type'];
        }
        
        $feedback_types_array = array();
        
        if (!empty($feedback_types)) {
            if (is_array($feedback_types)) {
                $feedback_types_array = array_filter(array_map('trim', $feedback_types)); // Remove empty values
            } else {
                // If single value (backward compatibility), convert to array
                $feedback_types_array = array(trim($feedback_types));
            }
        }
        
        // Ensure we have at least one improvement area
        if (empty($feedback_types_array)) {
            $this->session->set_flashdata('error', 'Please select at least one Improvement area.');
            redirect('kpi_reports/managerFeedbackForm');
            return;
        }
        
        $feedback_types_json = json_encode($feedback_types_array);
        
        // Process feedback month from and to fields
        $feedback_month_from = $this->input->post('feedback_month_from');
        $feedback_month_to = $this->input->post('feedback_month_to');
        $feedback_month = NULL;
        
        if (!empty($feedback_month_from) && !empty($feedback_month_to)) {
            // Convert YYYY-MM to month abbreviations with year
            $month_abbr = array('01' => 'JAN', '02' => 'FEB', '03' => 'MAR', '04' => 'APR', 
                               '05' => 'MAY', '06' => 'JUN', '07' => 'JUL', '08' => 'AUG', 
                               '09' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC');
            
            $from_parts = explode('-', $feedback_month_from);
            $to_parts = explode('-', $feedback_month_to);
            
            $from_year = isset($from_parts[0]) ? $from_parts[0] : '';
            $from_month = isset($from_parts[1]) && isset($month_abbr[$from_parts[1]]) ? $month_abbr[$from_parts[1]] : '';
            
            $to_year = isset($to_parts[0]) ? $to_parts[0] : '';
            $to_month = isset($to_parts[1]) && isset($month_abbr[$to_parts[1]]) ? ucfirst(strtolower($month_abbr[$to_parts[1]])) : '';
            
            if ($from_year && $from_month && $to_year && $to_month) {
                $feedback_month = $from_year . '-' . $from_month . ' to ' . $to_year . '-' . $to_month;
            }
        }
        
        $data = array(
            'empId' => $employee_id,
            'employee_name' => $employee_name,
            'department' => $employee_dept,
            'feedback_type' => $feedback_types_json, // Store as JSON for multiple selections
            'subject' => '',
            'feedback_message' => $this->input->post('feedback_message'),
            'status' => 'Sent',
            'reporting_manager' => $empId,
            'project_coordinator' => NULL,
            'team_members' => NULL,
            'feedback_month' => $feedback_month,
            'attached_file' => $attached_file,
            'feedback_for' => $this->input->post('feedback_for'),
            'strengths_achievements' => $this->input->post('strengths_achievements'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $feedback_id = $this->feedback_model->add_feedback($data);
        
        if ($feedback_id) {
            $this->session->set_flashdata('success', 'Feedback submitted successfully!');
            redirect('kpi_reports/feedbackReports');
        } else {
            $this->session->set_flashdata('error', 'Failed to submit feedback. Please try again.');
            redirect('kpi_reports/managerFeedbackForm');
        }
    }
}

/**
 * Feedback Reports - Display all feedback with filters
 */
public function feedbackReports() {
    $data = array();
    
    // Load pagination library
    $this->load->library('pagination');
    
    // Get filter parameters
    $filters = array();
    
    if ($this->input->get('status')) {
        $filters['status'] = $this->input->get('status');
    }
    
    if ($this->input->get('department')) {
        $filters['department'] = $this->input->get('department');
    }
    
    if ($this->input->get('feedback_type')) {
        $filters['feedback_type'] = $this->input->get('feedback_type');
    }
    
    if ($this->input->get('priority')) {
        $filters['priority'] = $this->input->get('priority');
    }
    
    if ($this->input->get('from_date')) {
        $filters['from_date'] = $this->input->get('from_date');
    }
    
    if ($this->input->get('to_date')) {
        $filters['to_date'] = $this->input->get('to_date');
    }
    
    if ($this->input->get('empId')) {
        $filters['empId'] = $this->input->get('empId');
    }
    
    if ($this->input->get('assigned_to')) {
        $filters['assigned_to'] = $this->input->get('assigned_to');
    }
    
    // Get total records count for pagination
    $total_records = $this->feedback_model->get_feedback_count($filters);
    
    // Pagination configuration
    $config = array();
    $config['base_url'] = base_url('kpi_reports/feedbackReports');
    $config['total_rows'] = $total_records;
    $config['per_page'] = 20;
    $config['num_links'] = 3;
    $config['use_page_numbers'] = TRUE;
    $config['reuse_query_string'] = TRUE;
    $config['page_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';
    
    // Pagination styling
    $config['full_tag_open'] = '<ul class="pagination" style="margin: 20px 0;">';
    $config['full_tag_close'] = '</ul>';
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link" style="background-color: #4361ee; border-color: #4361ee; color: white;">';
    $config['cur_tag_close'] = '</span></li>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['first_link'] = 'First';
    $config['last_link'] = 'Last';
    $config['next_link'] = 'Next &raquo;';
    $config['prev_link'] = '&laquo; Prev';
    
    // Add page link styling
    $config['attributes'] = array('class' => 'page-link');
    
    $this->pagination->initialize($config);
    
    // Get current page number from query string
    $page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $config['per_page'];
    
    // Add pagination to filters
    $filters['limit'] = $config['per_page'];
    $filters['offset'] = $offset;
    
    // Get feedback data with pagination
    $data['feedback_list'] = $this->feedback_model->get_feedback(NULL, $filters);
    
    // Get statistics
    $data['stats'] = $this->feedback_model->get_feedback_stats($filters);
    $data['dept_stats'] = $this->feedback_model->get_feedback_by_department($filters);
    $data['type_stats'] = $this->feedback_model->get_feedback_by_type($filters);
    
    // Get employees and managers for filters
    $data['employees'] = $this->feedback_model->get_all_employees();
    $data['managers'] = $this->feedback_model->get_managers();
    
    // Get current filters for view
    $data['filters'] = $filters;

    // HR department and admin/superadmin can view all members' feedback (and related UI controls)
    $user_type = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];
    $data['can_view_all_feedback'] = $this->feedback_model->can_view_all_feedback($user_type, $empId);
    $data['is_hr_user'] = $this->feedback_model->is_hr_department_user($empId);
    
    // Pagination links
    $data['pagination_links'] = $this->pagination->create_links();
    
    $this->load->view('kpi-reports/feedback-reports', $data);
}

/**
 * Update Feedback Status/Response - For managers/admins and team members
 */
public function updateFeedback() {
    // Log all POST data for debugging
    log_message('info', '=== updateFeedback called ===');
    log_message('info', 'Raw POST data: ' . json_encode($_POST));
    log_message('info', 'CodeIgniter POST data: ' . json_encode($this->input->post()));
    
    // Get feedback_id - try multiple methods
    $feedback_id = $this->input->post('feedback_id');
    if (empty($feedback_id) && isset($_POST['feedback_id'])) {
        $feedback_id = $_POST['feedback_id'];
        log_message('info', 'Feedback ID retrieved from $_POST: ' . $feedback_id);
    }
    
    $status = $this->input->post('status');
    $response = $this->input->post('response');
    $assigned_to = $this->input->post('assigned_to');
    
    // Log received values
    log_message('info', 'POST values - status: "' . $status . '", response: "' . ($response ? 'provided' : 'empty') . '", assigned_to: "' . ($assigned_to ? $assigned_to : 'empty') . '"');
    log_message('info', 'Received values - feedback_id: "' . $feedback_id . '" (type: ' . gettype($feedback_id) . '), status: "' . $status . '", response: "' . $response . '"');
    
    // Validate feedback_id
    $feedback_id = intval($feedback_id);
    if (empty($feedback_id) || $feedback_id <= 0) {
        log_message('error', 'CRITICAL: Invalid or missing feedback_id! Received: "' . $this->input->post('feedback_id') . '"');
        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Invalid feedback ID.'));
            exit;
        }
        $this->session->set_flashdata('error', 'Invalid feedback ID.');
        redirect('kpi_reports/feedbackReports');
    }
    
    log_message('info', 'Validated feedback_id: ' . $feedback_id);
    
    // Clean the status value - handle all possible cases
    if ($status === false || $status === null) {
        $status = '';
    } else {
        $status = trim($status);
    }
    
    // Also check for status in POST array directly (in case of form issues)
    if (empty($status) && isset($_POST['status'])) {
        $status = trim($_POST['status']);
        log_message('info', 'Status retrieved from $_POST directly: "' . $status . '"');
    }
    
    // Check all POST data for status (in case it's duplicated)
    if (empty($status)) {
        $all_post = $this->input->post();
        if (isset($all_post['status']) && !empty($all_post['status'])) {
            $status = trim($all_post['status']);
            log_message('info', 'Status found in all POST data: "' . $status . '"');
        }
    }
    
    // Log cleaned status
    log_message('info', 'Cleaned status value: "' . $status . '" (length: ' . strlen($status) . ')');
    
    // Clean the response value
    if ($response === false || $response === null) {
        $response = '';
    } else {
        $response = trim($response);
    }
    
    $user_type = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = intval($this->session->userdata['logged_in_timesheet']['empId']);
    $is_manager = in_array($user_type, ['admin', 'business_head', 'manager', 'superadmin']);
    
    // Get the feedback before update to check old status and get reporting manager info
    $feedback = $this->feedback_model->get_feedback($feedback_id);
    if (empty($feedback)) {
        $this->session->set_flashdata('error', 'Feedback not found.');
        redirect('kpi_reports/feedbackReports');
    }
    
    $old_status = $feedback->status;
    $reporting_manager_id = !empty($feedback->reporting_manager) ? intval($feedback->reporting_manager) : 0;
    $feedback_empId = !empty($feedback->empId) ? intval($feedback->empId) : 0;
    $feedback_team_member = !empty($feedback->team_members) ? intval($feedback->team_members) : 0;
    
    // Permission check: User can update if they are reporting manager OR team member OR manager/admin
    $is_reporting_manager = ($reporting_manager_id > 0 && $reporting_manager_id == $empId);
    $is_team_member = ($feedback_empId > 0 && $feedback_empId == $empId) || 
                      ($feedback_team_member > 0 && $feedback_team_member == $empId);
    
    // Reporting manager, team member, or manager/admin can update
    $can_update = $is_manager || $is_reporting_manager || $is_team_member;
    
    if (!$can_update) {
        $this->session->set_flashdata('error', 'You do not have permission to update this feedback.');
        redirect('kpi_reports/feedbackReports');
    }
    
    // Simple update - process status and response
    $data = array('updated_at' => date('Y-m-d H:i:s'));
    
    // Process status if provided - WORKS FOR ALL USERS (team members, managers, admins)
    if (!empty($status) && $status !== '') {
        $status = trim($status);
        $status_upper = strtoupper($status);
        
        log_message('info', 'Processing status - Original: "' . $status . '", Upper: "' . $status_upper . '", User: Team Member=' . ($is_team_member ? 'YES' : 'NO') . ', Reporting Manager=' . ($is_reporting_manager ? 'YES' : 'NO'));
        
        if ($status_upper === 'SENT') {
            $data['status'] = 'Sent';
            log_message('info', 'Status set to: "Sent"');
        } elseif ($status_upper === 'ACKNOWLEDGE') {
            $data['status'] = 'Acknowledge';
            log_message('info', 'Status set to: "Acknowledge"');
        } else {
            log_message('error', 'Invalid status value: "' . $status . '" (upper: "' . $status_upper . '")');
            $this->session->set_flashdata('error', 'Invalid status value. Please select either "Sent" or "Acknowledge".');
            redirect('kpi_reports/feedbackReports');
        }
        
        // CRITICAL: Ensure status is in data array (same for team members and reporting managers)
        if (!isset($data['status'])) {
            log_message('error', 'CRITICAL: Status processing failed! Status not set in data array.');
            log_message('error', 'Status value was: "' . $status . '", Upper: "' . $status_upper . '"');
            $this->session->set_flashdata('error', 'Status processing failed. Please try again.');
            redirect('kpi_reports/feedbackReports');
        }
        
        log_message('info', 'Status successfully added to data array: "' . $data['status'] . '"');
    }
    
    // Update response if provided
    if ($response !== false && $response !== null) {
        $data['response'] = trim($response);
        if (!empty($data['response'])) {
            $data['response_date'] = date('Y-m-d H:i:s');
        }
    }
    
    // Only managers/admins can update assigned_to (if provided)
    if ($is_manager && !empty($assigned_to)) {
        $data['assigned_to'] = intval($assigned_to);
    }
    
    // Ensure we have at least one field to update (status, response, or assigned_to)
    // Allow update even if only updated_at is set (for response-only updates)
    if (count($data) <= 1 && empty($data['status']) && empty($data['response']) && empty($data['assigned_to'])) {
        $this->session->set_flashdata('error', 'No valid data to update.');
        redirect('kpi_reports/feedbackReports');
    }
    
    // Log the data being updated for debugging
    log_message('info', '=== UPDATE DATA PREPARATION ===');
    log_message('info', 'Feedback ID: ' . $feedback_id);
    log_message('info', 'User: ' . $empId);
    log_message('info', 'Is Team Member: ' . ($is_team_member ? 'YES' : 'NO'));
    log_message('info', 'Is Reporting Manager: ' . ($is_reporting_manager ? 'YES' : 'NO'));
    log_message('info', 'Is Manager: ' . ($is_manager ? 'YES' : 'NO'));
    log_message('info', 'Status received: "' . $status . '"');
    log_message('info', 'Response received: "' . ($response ? substr($response, 0, 50) . '...' : '(empty)') . '"');
    log_message('info', 'Data array before update: ' . json_encode($data));
    
    // Verify status is in data array if status was provided (for all users)
    if (!isset($data['status']) && !empty($status)) {
        log_message('error', 'CRITICAL ERROR: Status is NOT in data array! Status value was: "' . $status . '", Data: ' . json_encode($data));
        $this->session->set_flashdata('error', 'Status was not processed correctly.');
        redirect('kpi_reports/feedbackReports');
    }
    
    log_message('info', 'Status is in data array: "' . (isset($data['status']) ? $data['status'] : 'NOT SET') . '"');
    log_message('info', 'Final update data: ' . json_encode($data));
    
    // CRITICAL: Verify feedback_id exists and get current status
    $check_feedback = $this->feedback_model->get_feedback($feedback_id);
    if (empty($check_feedback)) {
        log_message('error', 'CRITICAL: Feedback ID ' . $feedback_id . ' does not exist in database!');
        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Feedback not found.'));
            exit;
        }
        $this->session->set_flashdata('error', 'Feedback not found.');
        redirect('kpi_reports/feedbackReports');
    }
    
    $current_db_status = $check_feedback->status;
    $expected_status = isset($data['status']) ? $data['status'] : 'NO STATUS IN DATA';
    log_message('info', '=== BEFORE UPDATE ===');
    log_message('info', 'Current DB status: "' . $current_db_status . '"');
    log_message('info', 'Expected new status: "' . $expected_status . '"');
    log_message('info', 'User Type: Team Member=' . ($is_team_member ? 'YES' : 'NO') . ', Reporting Manager=' . ($is_reporting_manager ? 'YES' : 'NO'));
    log_message('info', 'Data to update: ' . json_encode($data));
    
    // Perform the database update - SAME FUNCTION FOR TEAM MEMBERS AND REPORTING MANAGERS
    log_message('info', '=== CALLING update_feedback MODEL ===');
    log_message('info', 'Feedback ID: ' . $feedback_id);
    log_message('info', 'Data being passed: ' . json_encode($data));
    log_message('info', 'Is Team Member: ' . ($is_team_member ? 'YES' : 'NO'));
    
    $result = $this->feedback_model->update_feedback($data, $feedback_id);
    
    // Log the result and verify
    log_message('info', 'Model update_feedback returned: ' . ($result ? 'TRUE' : 'FALSE'));
    
    if ($result) {
        log_message('info', 'Update SUCCESS for feedback ID ' . $feedback_id . ', Status updated to: ' . (isset($data['status']) ? $data['status'] : 'NOT SET'));
        
        // CRITICAL: Verify the update by reading back from database with a fresh query
        $this->db->flush_cache();
        $this->db->reconnect(); // Force fresh connection
        usleep(200000); // 0.2 second delay for database commit
        
        $verify_query = $this->db->query("SELECT status FROM employee_feedback WHERE feedback_id = " . intval($feedback_id) . " LIMIT 1");
        if ($verify_query->num_rows() > 0) {
            $actual_status = $verify_query->row()->status;
            log_message('info', 'AFTER UPDATE VERIFICATION: Database status is now "' . $actual_status . '", Expected: "' . (isset($data['status']) ? $data['status'] : 'NOT SET') . '"');
            if (isset($data['status']) && $actual_status !== $data['status']) {
                log_message('error', 'CRITICAL STATUS MISMATCH! Expected: "' . $data['status'] . '", Got: "' . $actual_status . '", Previous: "' . $current_db_status . '"');
                // Force update one more time with direct SQL
                $this->db->reconnect();
                $force_sql = "UPDATE employee_feedback SET status = '" . $this->db->escape_str($data['status']) . "' WHERE feedback_id = " . intval($feedback_id);
                log_message('info', 'Force update SQL: ' . $force_sql);
                $this->db->query($force_sql);
                $force_affected = $this->db->affected_rows();
                log_message('info', 'Force update affected_rows: ' . $force_affected);
                
                // Verify again after force update
                $this->db->flush_cache();
                usleep(200000);
                $final_check = $this->db->query("SELECT status FROM employee_feedback WHERE feedback_id = " . intval($feedback_id) . " LIMIT 1");
                if ($final_check->num_rows() > 0) {
                    $final_status = $final_check->row()->status;
                    log_message('info', 'After force update, status is: "' . $final_status . '"');
                }
            } else {
                log_message('info', '✓ Status successfully verified in database');
            }
        } else {
            log_message('error', 'CRITICAL: Feedback record not found during verification!');
        }
    } else {
        log_message('error', 'Update FAILED for feedback ID ' . $feedback_id . ', Data attempted: ' . json_encode($data));
        log_message('error', 'This is a CRITICAL FAILURE - update_feedback returned FALSE');
    }
    
    log_message('info', 'Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
    
    if ($result) {
        // Check if status was updated and if team member updated it
        $status_updated = false;
        $new_status = isset($data['status']) ? $data['status'] : $old_status;
        if (isset($data['status']) && $old_status != $data['status']) {
            $status_updated = true;
        }
        
        log_message('info', 'Status updated: ' . ($status_updated ? 'YES' : 'NO') . ', Old: "' . $old_status . '", New: "' . $new_status . '"');
        
        // Send email notification to reporting manager if team member updated status to "Acknowledge"
        if ($status_updated && !$is_manager && $is_team_member && $reporting_manager_id > 0 && $new_status == 'Acknowledge') {
            $this->sendStatusUpdateEmail($feedback, $old_status, $new_status, $empId);
        }
        
        $this->session->set_flashdata('success', 'Feedback updated successfully!');
    } else {
        // More detailed error message for debugging
        $error_msg = 'Failed to update feedback.';
        if (isset($data['status'])) {
            $error_msg .= ' Attempted to set status to: ' . $data['status'];
        }
        
        log_message('error', 'Update failed. Error: ' . $error_msg);
        $this->session->set_flashdata('error', $error_msg);
    }
    
    // Always redirect after form submission
    redirect('kpi_reports/feedbackReports');
}

/**
 * Send email notifications to team member when feedback is submitted
 */
private function sendFeedbackSubmissionEmails($feedback_id, $feedback_data) {
    // Get feedback details
    $feedback = $this->feedback_model->get_feedback($feedback_id);
    if (empty($feedback)) {
        return false;
    }
    
    // Send email to team member only if team_members is set and has email
    if (!empty($feedback_data['team_members'])) {
        $this->db->select('name, email');
        $this->db->from('employee_details');
        $this->db->where('empId', $feedback_data['team_members']);
        $this->db->where('status', 'Active');
        $team_member = $this->db->get()->row();
        
        if (!empty($team_member) && !empty($team_member->email)) {
            return $this->sendFeedbackEmailToTeamMember($feedback, $team_member);
        }
    }
    
    return false;
}

/**
 * Send email notification to team member
 */
private function sendFeedbackEmailToTeamMember($feedback, $team_member) {
    // Email configuration
    $config['mailtype'] = 'html';
    $config['charset'] = 'iso-8859-1';
    $config['wordwrap'] = TRUE;
    $config['newline'] = "\r\n";
    $this->email->initialize($config);
    
    // Get submitted by name
    $submitted_by = !empty($feedback->employee_name) ? $feedback->employee_name : 'N/A';
    
    // Get feedback month
    $feedback_month = !empty($feedback->feedback_month) ? date('F Y', strtotime($feedback->feedback_month . '-01')) : date('F Y', strtotime($feedback->created_at));
    
    // Get feedback type (Feedback Type field) - handle multiple improvement areas
    $improvement_areas = array();
    if (!empty($feedback->feedback_type)) {
        $decoded = json_decode($feedback->feedback_type, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $improvement_areas = array_filter($decoded);
        } else {
            $improvement_areas = array($feedback->feedback_type);
        }
    }
    $improvement_areas_text = !empty($improvement_areas) ? implode(', ', $improvement_areas) : 'N/A';
    $feedback_type = !empty($feedback->feedback_for) ? $feedback->feedback_for : $improvement_areas_text;
    
    // Get status
    $status = !empty($feedback->status) ? $feedback->status : 'Sent';
    
    // Get feedback message
    $feedback_message = !empty($feedback->feedback_message) ? $feedback->feedback_message : 'N/A';
    
    // Email content
    $subject = 'Notification: Feedback Submitted';
    
    $body = 'Dear ' . ucwords($team_member->name) . ',<br><br>
    
This email is to notify you that the following feedback has been submitted for your review:<br><br>

<strong>Feedback Month:</strong> ' . htmlspecialchars($feedback_month) . '<br>
<strong>Submitted By:</strong> ' . htmlspecialchars($submitted_by) . '<br>
<strong>Feedback Type:</strong> ' . htmlspecialchars($feedback_type) . '<br>
<strong>Status:</strong> ' . htmlspecialchars($status) . '<br><br>

<strong>Feedback Notes:</strong><br>
' . nl2br(htmlspecialchars($feedback_message)) . '<br><br>

Kindly review the information and acknowledge it in the system.<br><br>

Sincerely,<br>
eLogic Timesheet System';
    
    // Send email
    $this->email->from('info@elogictech.com', 'eLogic Timesheet');
    $this->email->to($team_member->email);
    $this->email->subject($subject);
    $this->email->message($body);
    
    return $this->email->send();
}

/*** Send email notification to reporting manager */
private function sendFeedbackEmailToReportingManager($feedback, $reporting_manager) {
    // Email configuration
    $config['mailtype'] = 'html';
    $config['charset'] = 'iso-8859-1';
    $config['wordwrap'] = TRUE;
    $config['newline'] = "\r\n";
    $this->email->initialize($config);
    
    // Email content
    // Handle multiple improvement areas for email subject
    $improvement_areas = array();
    if (!empty($feedback->feedback_type)) {
        $decoded = json_decode($feedback->feedback_type, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $improvement_areas = array_filter($decoded);
        } else {
            $improvement_areas = array($feedback->feedback_type);
        }
    }
    $improvement_areas_text = !empty($improvement_areas) ? implode(', ', $improvement_areas) : 'N/A';
    $subject = 'New Feedback Submitted - ' . $improvement_areas_text;
    
    $body = '<!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Feedback Submission</title>
    </head>
    <body style="width: 95%; margin: 0 auto; background: #f1f1f1; border:1px solid #888; padding: 0 1% 2% 1%;">
        <div align="left" style="margin: 3% auto 2% 6%;">
            <img src="http://www.elogictechsolutions.com/assets/images/logo.png" style="width: 180px;">
        </div>
        <div style="background: #004b88; padding: 2%; border-radius: 15px; margin-top: 3%;">
            <section style="background: #004b88; border-radius: 6px; padding-top: 2%; font-size: 17px;">
                <div style="color: #fff; margin:2% auto 0px auto; padding-left: 6%;">
                    Dear ' . ucwords($reporting_manager->name) . ',
                </div>
                <div align="left" style="margin: 1% auto; padding-left: 6%; line-height: 24px; color: #fff;">
                    <p>A new feedback has been submitted and you are assigned as the reporting manager.</p>
                </div>
                <div align="left" style="margin: 1% auto; padding-left: 6%; line-height: 24px; color: #fff;">
                    <table style="color: #fff;">
                        <tbody>
                            <tr>
                                <td width="30%" style="padding: 5px 0;">Feedback Type:</td>
                                <td style="padding: 5px 0;">' . htmlspecialchars($improvement_areas_text) . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 0;">Submitted By:</td>
                                <td style="padding: 5px 0;">' . htmlspecialchars($feedback->employee_name) . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 0;">Status:</td>
                                <td style="padding: 5px 0;">' . htmlspecialchars($feedback->status) . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 0;">Feedback Date:</td>
                                <td style="padding: 5px 0;">' . date('d M Y', strtotime($feedback->created_at)) . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 0;">Department:</td>
                                <td style="padding: 5px 0;">' . htmlspecialchars($feedback->department ? $feedback->department : 'N/A') . '</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div align="left" style="margin: 2% auto; padding-left: 6%; line-height: 24px; color: #fff;">
                    <p><strong>Feedback Message:</strong></p>
                    <p style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 4px;">' . nl2br(htmlspecialchars($feedback->feedback_message)) . '</p>
                </div>
                <div align="left" style="margin: 3% auto 0 6%; line-height: 24px; color: #fff;">
                    <p>Please review the feedback details.</p>
                    <p>Thanks & Regards,<br>
                    <div style="color: #fff; padding-bottom: 4%;">eLogic Timesheet System</div>
                </p>
            </div>
        </section>
    </div>
    </body>
    </html>';
    
    // Send email
    $this->email->from('info@elogictech.com', 'eLogic Timesheet');
    $this->email->to($reporting_manager->email);
    $this->email->subject($subject);
    $this->email->message($body);
    
    return $this->email->send();
}

/**
 * Send email notification to reporting manager when team member updates status
 */
private function sendStatusUpdateEmail($feedback, $old_status, $new_status, $team_member_empId, $response = '') {
    // Only send email if status is "Acknowledge"
    if ($new_status != 'Acknowledge') {
        return false;
    }
    
    // Get reporting manager details
    if (empty($feedback->reporting_manager)) {
        return false;
    }
    
    $this->db->select('name, email');
    $this->db->from('employee_details');
    $this->db->where('empId', $feedback->reporting_manager);
    $this->db->where('status', 'Active');
    $manager = $this->db->get()->row();
    
    if (empty($manager) || empty($manager->email)) {
        return false;
    }
    
    // Get team member details
    $this->db->select('name');
    $this->db->from('employee_details');
    $this->db->where('empId', $team_member_empId);
    $team_member = $this->db->get()->row();
    $team_member_name = $team_member ? $team_member->name : 'Team Member';
    
    // Get feedback month (format as "Month Year")
    $feedback_month = !empty($feedback->feedback_month) ? date('F Y', strtotime($feedback->feedback_month . '-01')) : date('F Y', strtotime($feedback->created_at));
    
    // Get feedback type (use feedback_for field, fallback to feedback_type) - handle multiple improvement areas
    $improvement_areas = array();
    if (!empty($feedback->feedback_type)) {
        $decoded = json_decode($feedback->feedback_type, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $improvement_areas = array_filter($decoded);
        } else {
            $improvement_areas = array($feedback->feedback_type);
        }
    }
    $improvement_areas_text = !empty($improvement_areas) ? implode(', ', $improvement_areas) : 'N/A';
    $feedback_type = !empty($feedback->feedback_for) ? $feedback->feedback_for : $improvement_areas_text;
    
    // Email configuration
    $config['mailtype'] = 'html';
    $config['charset'] = 'iso-8859-1';
    $config['wordwrap'] = TRUE;
    $config['newline'] = "\r\n";
    $this->email->initialize($config);
    
    // Email content
    $subject = 'Team Member Feedback Acknowledgement';
    
    $body = 'Dear ' . ucwords($manager->name) . ',<br><br>
    
The feedback submitted for ' . ucwords($team_member_name) . ' has been reviewed and acknowledged.<br><br>

<strong>Month:</strong> ' . htmlspecialchars($feedback_month) . '<br>
<strong>Type:</strong> ' . htmlspecialchars($feedback_type) . '<br>
<strong>Status:</strong> Acknowledged<br><br>

This entry is now closed.<br><br>

Regards,<br>
eLogic Timesheet System';
    
    // Send email
    $this->email->from('info@elogictech.com', 'eLogic Timesheet');
    $this->email->to($manager->email);
    $this->email->subject($subject);
    $this->email->message($body);
    
    return $this->email->send();
}

/**
 * View Single Feedback - Display detailed view of a feedback
 */
public function viewFeedback($feedback_id = NULL) {
    if (empty($feedback_id)) {
        redirect('kpi_reports/feedbackReports');
    }
    
    $data['feedback'] = $this->feedback_model->get_feedback($feedback_id);
    
    if (empty($data['feedback'])) {
        $this->session->set_flashdata('error', 'Feedback not found.');
        redirect('kpi_reports/feedbackReports');
    }
    
    $data['managers'] = $this->feedback_model->get_managers();
    
    $this->load->view('kpi-reports/feedback-view', $data);
}

/**
 * Download Feedback Reports as Excel
 */
public function downloadFeedbackReportsExcel() {
    // Get filter parameters (same as feedbackReports)
    $filters = array();
    
    if ($this->input->get('status')) {
        $filters['status'] = $this->input->get('status');
    }
    
    if ($this->input->get('department')) {
        $filters['department'] = $this->input->get('department');
    }
    
    if ($this->input->get('feedback_type')) {
        $filters['feedback_type'] = $this->input->get('feedback_type');
    }
    
    if ($this->input->get('from_date')) {
        $filters['from_date'] = $this->input->get('from_date');
    }
    
    if ($this->input->get('to_date')) {
        $filters['to_date'] = $this->input->get('to_date');
    }
    
    if ($this->input->get('empId')) {
        $filters['empId'] = $this->input->get('empId');
    }
    
    if ($this->input->get('assigned_to')) {
        $filters['assigned_to'] = $this->input->get('assigned_to');
    }
    
    // Get feedback data
    $feedback_list = $this->feedback_model->get_feedback(NULL, $filters);
    
    // Load Excel library
    $this->load->library('excel');
    $objPHPExcel = $this->excel;
    
    // Set document properties
    $objPHPExcel->getProperties()
        ->setCreator("eLogic")
        ->setLastModifiedBy("eLogic")
        ->setTitle("Employee Feedback Reports")
        ->setDescription("Employee Feedback Reports Export");
    
    // Set active sheet
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Feedback Reports');
    
    // Set header row
    $sheet->setCellValue('A1', 'SNO');
    $sheet->setCellValue('B1', 'From & To Dates');
    $sheet->setCellValue('C1', 'Department');
    $sheet->setCellValue('D1', 'Reporting Manager');
    $sheet->setCellValue('E1', 'Project Coordinator');
    $sheet->setCellValue('F1', 'Team Member');
    $sheet->setCellValue('G1', 'Type');
    $sheet->setCellValue('H1', 'Status');
    $sheet->setCellValue('I1', 'Feedback Date');
    $sheet->setCellValue('J1', 'Feedback Message');
    
    // Style header row
    $headerStyle = array(
        'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'FFFFFF')),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '4361ee')
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),
        'borders' => array(
            'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
        )
    );
    $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
    
    // Set column widths
    $sheet->getColumnDimension('A')->setWidth(10);
    $sheet->getColumnDimension('B')->setWidth(35);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(25);
    $sheet->getColumnDimension('E')->setWidth(25);
    $sheet->getColumnDimension('F')->setWidth(25);
    $sheet->getColumnDimension('G')->setWidth(35);
    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->getColumnDimension('I')->setWidth(18);
    $sheet->getColumnDimension('J')->setWidth(50);
    
    // Add data rows
    $row = 2;
    $sno = 1;
    foreach ($feedback_list as $feedback) {
        // Parse feedback_month to extract From Date and To Date
        $date_range_display = 'N/A';
        
        if (!empty($feedback->feedback_month)) {
            // Format: "2026-JAN to 2026-Mar"
            if (preg_match('/(\d{4})-([A-Za-z]{3})\s+to\s+(\d{4})-([A-Za-z]{3})/i', $feedback->feedback_month, $matches)) {
                $from_year = $matches[1];
                $from_month_abbr = strtoupper($matches[2]);
                $to_year = $matches[3];
                $to_month_abbr = strtoupper($matches[4]);
                
                // Convert month abbreviation to number
                $month_map = array(
                    'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
                    'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08',
                    'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'
                );
                
                if (isset($month_map[$from_month_abbr]) && isset($month_map[$to_month_abbr])) {
                    $from_date = date('d M Y', strtotime($from_year . '-' . $month_map[$from_month_abbr] . '-01'));
                    // Get last day of the month
                    $last_day = date('t', strtotime($to_year . '-' . $month_map[$to_month_abbr] . '-01'));
                    $to_date = date('d M Y', strtotime($to_year . '-' . $month_map[$to_month_abbr] . '-' . $last_day));
                    $date_range_display = $from_date . ' - ' . $to_date;
                }
            }
        }
        
        $sheet->setCellValue('A' . $row, $sno);
        $sno++;
        $sheet->setCellValue('B' . $row, $date_range_display);
        $sheet->setCellValue('C' . $row, $feedback->emp_department ? $feedback->emp_department : $feedback->department);
        $sheet->setCellValue('D' . $row, $feedback->reporting_manager_name ? $feedback->reporting_manager_name : 'N/A');
        $sheet->setCellValue('E' . $row, $feedback->project_coordinator_name ? $feedback->project_coordinator_name : 'N/A');
        $sheet->setCellValue('F' . $row, $feedback->team_member_name ? $feedback->team_member_name : 'N/A');
        // Handle multiple improvement areas
        $improvement_areas = array();
        if (!empty($feedback->feedback_type)) {
            $decoded = json_decode($feedback->feedback_type, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // Filter out empty values but keep all non-empty values, including trimming whitespace
                $improvement_areas = array_filter($decoded, function($value) {
                    return !empty(trim($value));
                });
                // Re-index array to ensure sequential keys
                $improvement_areas = array_values($improvement_areas);
            } else {
                // If not JSON, treat as single value (backward compatibility)
                $improvement_areas = array(trim($feedback->feedback_type));
            }
        }
        $improvement_areas_text = !empty($improvement_areas) ? implode(', ', $improvement_areas) : 'N/A';
        $sheet->setCellValue('G' . $row, $improvement_areas_text);
        $sheet->setCellValue('H' . $row, $feedback->status);
        $sheet->setCellValue('I' . $row, date('d M Y', strtotime($feedback->created_at)));
        $sheet->setCellValue('J' . $row, $feedback->feedback_message);
        
        // Style data rows
        $dataStyle = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                'wrap' => true
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            )
        );
        // Center align SNO, Status columns
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($dataStyle);
        
        $row++;
    }
    
    // Set row height for better readability
    for ($i = 2; $i < $row; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(20);
    }
    
    // Generate filename with timestamp
    $filename = 'Feedback_Reports_' . date('Y-m-d_His') . '.xls';
    
    // Set headers for download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Write file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}

/**
 * AJAX: Get reporting managers by department
 */
public function getReportingManagersByDept() {
    $department = $this->input->post('department');
    $managers = $this->feedback_model->get_reporting_managers_by_department($department);
    echo json_encode($managers);
}

/**
 * AJAX: Get project coordinators by reporting manager
 */
public function getProjectCoordinatorsByManager() {
    $manager_id = $this->input->post('manager_id');
    $coordinators = $this->feedback_model->get_project_coordinators_by_manager($manager_id);
    echo json_encode($coordinators);
}

/**
 * AJAX: Get team members by department
 */
public function getTeamMembersByDept() {
    $department = $this->input->post('department');
    $team_members = $this->feedback_model->get_team_members_by_department($department);
    echo json_encode($team_members);
}

/**
 * AJAX: Get employee details by ID
 */
public function getEmployeeDetails() {
    $empId = $this->input->post('empId');
    $employee = $this->timesheet_login->getEmployees($empId);
    
    if (!empty($employee)) {
        $emp = $employee[0];
        echo json_encode(array(
            'empId' => $emp->empId,
            'name' => $emp->name,
            'department' => $emp->department
        ));
    } else {
        echo json_encode(array('error' => 'Employee not found'));
    }
}

/**
 * Feedback Grid View - Display feedback data in grid format grouped by month
 */
public function feedbackGridView() {
    $data = array();
    
    // Get filter parameters
    $filters = array();
    
    // Date range filters
    if ($this->input->get('from_date')) {
        $filters['from_date'] = $this->input->get('from_date');
    }
    
    if ($this->input->get('to_date')) {
        $filters['to_date'] = $this->input->get('to_date');
    }
    
    // Reporting Manager filter (comma-separated names)
    if ($this->input->get('reporting_manager')) {
        $filters['reporting_manager'] = $this->input->get('reporting_manager');
    }
    
    // If no date filters, default to current month
    if (empty($filters['from_date']) && empty($filters['to_date'])) {
        $filters['from_date'] = date('Y-m-01');
        $filters['to_date'] = date('Y-m-t');
    }
    
    // Get feedback data grouped by month
    $all_feedback = $this->feedback_model->get_feedback_by_month($filters);
    
    // Group feedback by month
    $grouped_by_month = array();
    foreach ($all_feedback as $feedback) {
        $month_key = $feedback->feedback_month ? $feedback->feedback_month : date('Y-m', strtotime($feedback->created_at));
        
        if (!isset($grouped_by_month[$month_key])) {
            $grouped_by_month[$month_key] = array();
        }
        
        $grouped_by_month[$month_key][] = $feedback;
    }
    
    // Sort months in descending order
    krsort($grouped_by_month);
    
    $data['grouped_feedback'] = $grouped_by_month;
    $data['filters'] = $filters;
    $data['employees'] = $this->feedback_model->get_all_employees();
    
    $this->load->view('kpi-reports/feedback-grid-view', $data);
}

/**
 * Download Feedback Grid View as Excel
 */
public function downloadFeedbackGridViewExcel() {
    // Get filter parameters (same as feedbackGridView)
    $filters = array();
    
    // Date range filters
    if ($this->input->get('from_date')) {
        $filters['from_date'] = $this->input->get('from_date');
    }
    
    if ($this->input->get('to_date')) {
        $filters['to_date'] = $this->input->get('to_date');
    }
    
    // Reporting Manager filter (comma-separated names)
    if ($this->input->get('reporting_manager')) {
        $filters['reporting_manager'] = $this->input->get('reporting_manager');
    }
    
    // If no date filters, default to current month
    if (empty($filters['from_date']) && empty($filters['to_date'])) {
        $filters['from_date'] = date('Y-m-01');
        $filters['to_date'] = date('Y-m-t');
    }
    
    // Get feedback data grouped by month
    $all_feedback = $this->feedback_model->get_feedback_by_month($filters);
    
    // Load Excel library
    $this->load->library('excel');
    $objPHPExcel = $this->excel;
    
    // Set document properties
    $objPHPExcel->getProperties()
        ->setCreator("eLogic")
        ->setLastModifiedBy("eLogic")
        ->setTitle("Feedback Grid View Report")
        ->setDescription("Feedback Grid View Export");
    
    // Set active sheet
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Feedback Grid View');
    
    // Set header row
    $sheet->setCellValue('A1', 'SNO');
    $sheet->setCellValue('B1', 'Feedback Date');
    $sheet->setCellValue('C1', 'Department');
    $sheet->setCellValue('D1', 'Reporting Manager');
    $sheet->setCellValue('E1', 'Project Coordinator');
    $sheet->setCellValue('F1', 'Team Member');
    $sheet->setCellValue('G1', 'Type');
    $sheet->setCellValue('H1', 'Status');
    $sheet->setCellValue('I1', 'Feedback Month');
    
    // Style header row
    $headerStyle = array(
        'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'FFFFFF')),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '4361ee')
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),
        'borders' => array(
            'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
        )
    );
    $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
    
    // Set column widths
    $sheet->getColumnDimension('A')->setWidth(10);
    $sheet->getColumnDimension('B')->setWidth(18);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(25);
    $sheet->getColumnDimension('E')->setWidth(25);
    $sheet->getColumnDimension('F')->setWidth(25);
    $sheet->getColumnDimension('G')->setWidth(35);
    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->getColumnDimension('I')->setWidth(18);
    
    // Add data rows
    $row = 2;
    $sno = 1;
    foreach ($all_feedback as $feedback) {
        $sheet->setCellValue('A' . $row, $sno);
        $sno++;
        $sheet->setCellValue('B' . $row, date('d M Y', strtotime($feedback->created_at)));
        $sheet->setCellValue('C' . $row, $feedback->emp_department ? $feedback->emp_department : $feedback->department);
        $sheet->setCellValue('D' . $row, $feedback->reporting_manager_name ? $feedback->reporting_manager_name : 'N/A');
        $sheet->setCellValue('E' . $row, $feedback->project_coordinator_name ? $feedback->project_coordinator_name : 'N/A');
        $sheet->setCellValue('F' . $row, $feedback->team_member_name ? $feedback->team_member_name : 'N/A');
        // Handle multiple improvement areas
        $improvement_areas = array();
        if (!empty($feedback->feedback_type)) {
            $decoded = json_decode($feedback->feedback_type, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $improvement_areas = array_filter($decoded);
            } else {
                $improvement_areas = array($feedback->feedback_type);
            }
        }
        $improvement_areas_text = !empty($improvement_areas) ? implode(', ', $improvement_areas) : 'N/A';
        $sheet->setCellValue('G' . $row, $improvement_areas_text);
        $sheet->setCellValue('H' . $row, $feedback->status);
        
        // Format feedback month
        $feedback_month = $feedback->feedback_month ? $feedback->feedback_month : date('Y-m', strtotime($feedback->created_at));
        $month_date = DateTime::createFromFormat('Y-m', $feedback_month);
        $month_display = $month_date ? $month_date->format('F Y') : $feedback_month;
        $sheet->setCellValue('I' . $row, $month_display);
        
        // Style data rows
        $dataStyle = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            )
        );
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($dataStyle);
        
        // Center align ID, Date, Status, and Month columns
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $row++;
    }
    
    // Set row height for better readability
    for ($i = 2; $i < $row; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(20);
    }
    
    // Generate filename with timestamp
    $filename = 'Feedback_Grid_View_' . date('Y-m-d_His') . '.xls';
    
    // Set headers for download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Write file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}

/**
 * Resolve consolidated KPI date range from year/month dropdowns or legacy from_date/to_date.
 *
 * @return array{from_date:string,to_date:string,from_year:int,from_month:int,to_year:int,to_month:int}
 */
private function _resolve_kpi_consolidated_dates($from_date, $to_date, $from_year, $from_month, $to_year, $to_month)
{
    $startYear = 2010;
    $endYear = (int) date('Y');
    $currentMonth = (int) date('n');
    $nowMonthStart = mktime(0, 0, 0, $currentMonth, 1, $endYear);

    $hasFromYear = ($from_year !== null && $from_year !== '');
    $hasToYear = ($to_year !== null && $to_year !== '');
    $hasFromMonth = ($from_month !== null && $from_month !== '');
    $hasToMonth = ($to_month !== null && $to_month !== '');

    if ($hasFromYear && $hasToYear) {
        $fromYearIsAll = (strtoupper(trim((string) $from_year)) === 'ALL');
        $toYearIsAll = (strtoupper(trim((string) $to_year)) === 'ALL');
        if ($fromYearIsAll) {
            $hasFromMonth = false;
            $from_month = '';
        }
        if ($toYearIsAll) {
            $hasToMonth = false;
            $to_month = '';
        }
        $fy = $fromYearIsAll ? $startYear : max($startYear, min($endYear, (int) $from_year));
        $ty = $toYearIsAll ? $endYear : max($startYear, min($endYear, (int) $to_year));
        if ($hasFromMonth) {
            $fm = max(1, min(12, (int) $from_month));
        } else {
            $fm = 1;
        }
        if ($hasToMonth) {
            $tm = max(1, min(12, (int) $to_month));
        } else {
            $tm = ($ty === $endYear) ? $currentMonth : 12;
        }

        if (mktime(0, 0, 0, $fm, 1, $fy) > mktime(0, 0, 0, $tm, 1, $ty)) {
            list($fy, $fm, $ty, $tm) = array($ty, $tm, $fy, $fm);
        }
        if (mktime(0, 0, 0, $tm, 1, $ty) > $nowMonthStart) {
            $ty = $endYear;
            $tm = $currentMonth;
            if (mktime(0, 0, 0, $fm, 1, $fy) > $nowMonthStart) {
                $fy = $endYear;
                $fm = $currentMonth;
            }
        }

        return array(
            'from_date' => sprintf('%04d-%02d-01', $fy, $fm),
            'to_date' => date('Y-m-t', mktime(0, 0, 0, $tm, 1, $ty)),
            'from_year' => $fromYearIsAll ? 'ALL' : $fy,
            'from_month' => $hasFromMonth ? $fm : 0,
            'to_year' => $toYearIsAll ? 'ALL' : $ty,
            'to_month' => $hasToMonth ? $tm : 0,
        );
    }

    if (!empty($from_date) && !empty($to_date)) {
        $fy = (int) date('Y', strtotime($from_date));
        $fm = (int) date('n', strtotime($from_date));
        $ty = (int) date('Y', strtotime($to_date));
        $tm = (int) date('n', strtotime($to_date));
        $fy = max($startYear, min($endYear, $fy));
        $ty = max($startYear, min($endYear, $ty));
        $fm = max(1, min(12, $fm));
        $tm = max(1, min(12, $tm));

        return array(
            'from_date' => sprintf('%04d-%02d-01', $fy, $fm),
            'to_date' => date('Y-m-t', mktime(0, 0, 0, $tm, 1, $ty)),
            'from_year' => $fy,
            'from_month' => $fm,
            'to_year' => $ty,
            'to_month' => $tm,
        );
    }

    return array(
        'from_date' => $endYear . '-01-01',
        'to_date' => date('Y-m-t', $nowMonthStart),
        'from_year' => $endYear,
        'from_month' => 1,
        'to_year' => $endYear,
        'to_month' => $currentMonth,
    );
}

/**
 * Format hours for KPI Excel export cells.
 */
private function _kpi_format_excel_hours($value)
{
    $value = (float)$value;
    if (fmod($value, 1.0) == 0.0) {
        return (string)(int)$value;
    }
    return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
}

/**
 * Autosuggest Reporting Manager Names
 */
public function autosuggest_reporting_managers() {
    $term = $this->input->get('term');
    
    // Set content type to JSON
    header('Content-Type: application/json');
    
    if (!empty($term) && strlen(trim($term)) >= 2) {
        $suggestions = $this->feedback_model->get_reporting_manager_suggestions(trim($term));
        echo json_encode($suggestions);
    } else {
        echo json_encode(array());
    }
    exit;
}

}
