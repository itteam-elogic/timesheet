<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Kpi_Reports_Model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
			$this->load->helper('department');
	
	}  

    /**
     * Month-wise Individual KPI: primary delivery departments only.
     */
    private function _monthWiseKpiReportDepartments()
    {
        if (function_exists('ts_primary_delivery_departments')) {
            return ts_primary_delivery_departments();
        }
        return array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');
    }

    private function _isMonthWiseKpiReportRoute($currentFunction)
    {
        if ($currentFunction === 'getMonthWiseEmpData') {
            return true;
        }
        if (strpos((string)$currentFunction, 'generateMonthWiseEmpDataExcel') === 0) {
            return true;
        }
        return false;
    }

    private function _kpiFullAccessUser($empWiseKpi, $empId)
    {
        return $empId == '140'
            || $empWiseKpi === 'admin'
            || $empWiseKpi === 'business_head';
    }

    private function _resolveKpiDepartmentScope($currentFunction)
    {
        if (strpos($currentFunction, 'mep') !== false) {
            $departmentsToInclude = array('MEP');
            $excludeEmpIds = array('146', '230', '149', '455');
        } elseif ($currentFunction === 'consolidatedReport') {
            $departmentsToInclude = function_exists('ts_primary_delivery_departments')
                ? ts_primary_delivery_departments()
                : array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');
            $excludeEmpIds = array('146', '230', '149', '455', '41', '394', '270', '47', '182', '71', '53', '155');
        } elseif ($this->_isMonthWiseKpiReportRoute($currentFunction)) {
            $departmentsToInclude = $this->_monthWiseKpiReportDepartments();
            $excludeEmpIds = array('41', '394', '270', '47', '182', '71', '53', '155');
        } else {
            $departmentsToInclude = array('Architectural', 'Structural', '3D Visualization');
            $excludeEmpIds = array('41', '394', '270', '47', '182', '71', '53', '155');
        }

        return array($departmentsToInclude, $excludeEmpIds);
    }

    
//func for kpi report table ( monthly & consolidated )
public function getkpiInformation($limit, $offset, $monthName = '', $search = '', $from_date = '', $to_date = '', $department = '', $uriSegment2Override = null){
    
    $empWiseKpi = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];
    $currentFunction = ($uriSegment2Override !== null && $uriSegment2Override !== '') ? $uriSegment2Override : $this->uri->segment(2);
    $departmentSelectExpr = 'employee_details.department';
    if ($currentFunction === 'consolidatedReport') {
        $departmentSelectExpr = 'COALESCE(NULLIF(manager.department, ""), employee_details.department)';
    }

    // When searching by name: get empIds of people whose name matches, so we can include their own rows even if excluded by dept (e.g. manager's own record)
    $searchMatchEmpIds = array();
    if (!empty($search)) {
        $searchTerms = array_map('trim', explode(',', $search));
        $this->db->select('empId')->from('employee_details')->where('status', 'Active');
        $this->db->group_start();
        foreach ($searchTerms as $term) {
            if (!empty($term)) $this->db->or_like('name', $term, 'both');
        }
        $this->db->group_end();
        $res = $this->db->get()->result();
        foreach ($res as $r) $searchMatchEmpIds[] = $r->empId;
        $searchMatchEmpIds = array_values(array_unique($searchMatchEmpIds));
    }

    $this->db->select('employee_details.empId, employee_details.name, employee_details.reporting_manger, ' . $departmentSelectExpr . ' as department, employee_details.emp_com_id, manager.name as manager_name', false)
             ->from('employee_details')
             ->join('employee_details as manager', 'manager.empId = employee_details.reporting_manger', 'left')
             ->where('employee_details.status', 'Active');

if (!empty($search)) {
    $searchTerms = array_map('trim', explode(',', $search));
    $this->db->group_start();
    foreach ($searchTerms as $term) {
        if (!empty($term)) {
            $this->db->or_group_start()
                   ->like('employee_details.name', $term, 'both')
                   ->or_like('manager.name', $term, 'both');
            
            // Enhanced handling for Architecture department group
            if (stripos($term, 'arch') !== false) {
                $this->db->or_where_in('employee_details.department', 
                    ['Architectural', 'Structural', '3D Visualization']);
            }
            $this->db->or_like('employee_details.department', $term, 'both');
            
            $this->db->group_end();
        }
    }
    // Include employees by resolved IDs (ensures employee-wise search returns data when department filter is also applied)
    if (!empty($searchMatchEmpIds)) {
        $this->db->or_where_in('employee_details.empId', $searchMatchEmpIds);
    }
    $this->db->group_end();
}

    // Apply department filter if provided (takes priority over default logic)
    if (!empty($department)) {
        // Allow multiple departments (array) or single department (string)
        if (is_array($department)) {
            $this->db->where_in('employee_details.department', $department);
        } else {
            $this->db->where('employee_details.department', $department);
        }
    } elseif ($empWiseKpi == 'developer') {
        $this->db->where('employee_details.empId', $empId);
    } elseif ($this->_kpiFullAccessUser($empWiseKpi, $empId)) {
        

        list($departmentsToInclude, $excludeEmpIds) = $this->_resolveKpiDepartmentScope($currentFunction);

        $this->db->group_start();
        $this->db->where_in('employee_details.department', $departmentsToInclude);
        
        if (strpos($currentFunction, 'mep') === false && $currentFunction !== 'consolidatedReport' && !$this->_isMonthWiseKpiReportRoute($currentFunction)) {
            $this->db->where('employee_details.department !=', 'MEP');
        }
        
        if (!empty($excludeEmpIds)) {
            $this->db->where_not_in('employee_details.empId', $excludeEmpIds);
        }
        $this->db->group_end();
        if (!empty($searchMatchEmpIds)) {
            $this->db->or_where_in('employee_details.empId', $searchMatchEmpIds);
        }
        
    }elseif ($empWiseKpi == 'manager' ) {
        // Include manager's own data and team; show manager's row first
        $this->db->group_start();
        $this->db->where('employee_details.reporting_manger', $empId);
        $this->db->or_where('employee_details.empId', $empId);
        $this->db->group_end();
        $this->db->order_by('(employee_details.empId = ' . $this->db->escape($empId) . ')', 'DESC');
    } elseif ($empId == '149') {
        $this->db->where('employee_details.department', 'MEP')
                 ->where('employee_details.empId !=', '149');
    } elseif ($empId == '47') {
        $departments = ['Architectural', 'Structural', '3D Visualization'];
        $this->db->where_in('employee_details.department', $departments)
                 ->where_not_in('employee_details.empId', ['47', '270']);
    }
    
    
    else {
        
      
        list($departmentsToInclude, $excludeEmpIds) = $this->_resolveKpiDepartmentScope($currentFunction);

        $this->db->group_start();
        $this->db->where_in('employee_details.department', $departmentsToInclude);
        
        if (strpos($currentFunction, 'mep') === false && $currentFunction !== 'consolidatedReport' && !$this->_isMonthWiseKpiReportRoute($currentFunction)) {
            $this->db->where('employee_details.department !=', 'MEP');
        }
        
        if (!empty($excludeEmpIds)) {
            $this->db->where_not_in('employee_details.empId', $excludeEmpIds);
        }
        $this->db->group_end();
        if (!empty($searchMatchEmpIds)) {
            $this->db->or_where_in('employee_details.empId', $searchMatchEmpIds);
        }
    }

    
    $this->db->order_by('employee_details.emp_com_id', 'asc');
    
    $this->db->limit($limit, $offset);
    
    return $this->db->get()->result();
}

    /**
     * Return empIds of employees whose name matches the search (for including their rows in view when department would hide them).
     */
    public function getSearchMatchEmpIds($search) {
        if (empty($search)) return array();
        $terms = array_map('trim', explode(',', $search));
        $terms = array_filter($terms);
        if (empty($terms)) return array();
        $this->db->select('empId')->from('employee_details')->where('status', 'Active');
        $this->db->group_start();
        foreach ($terms as $t) {
            $this->db->or_like('name', $t, 'both');
        }
        $this->db->group_end();
        $res = $this->db->get()->result();
        $ids = array();
        foreach ($res as $r) $ids[] = $r->empId;
        return array_values(array_unique($ids));
    }

    /**
     * Department-wise KPI % (Productivity, Project General, eLogic General, Availability, Utilization)
     * Averages employees by their employee_details.department into each delivery bucket.
     * Rows follow ts_primary_delivery_departments(): Architectural, Structural, 3D Visualization, 2D Auto CAD, MEP.
     * Departments with no qualifying employees show "-" in % columns (table still appears if any dept has data).
     * When $department filter is set, only those primary delivery departments (in canonical order) appear in rows.
     * When $search is non-empty (e.g. manager / name filter), only departments with at least one qualifying employee appear; empty buckets are omitted.
     * When client / PM / project scope filters are active, summary is derived from the same filtered client-project rows as the grid (not company-wide employee KPIs).
     *
     * @param string $from_date Y-m-d
     * @param string $to_date Y-m-d
     * @param string|array $department Department filter from report (optional)
     * @param string $search Merged client / PM / project search (optional)
     * @param array $grid Optional keys: clients, pms, project (from client_report_grid_filters_from_request)
     * @return array{rows: array<int, array<string, mixed>>, has_data: bool}
     */
    public function getClientReportDepartmentKpiSummary($from_date, $to_date, $department = '', $search = '', $grid = array(), $projectRows = null)
    {
        return $this->getClientReportDepartmentKpiSummaryFromProjects($from_date, $to_date, $department, $search, $grid, $projectRows);
    }

    /**
     * Quality accuracy % for one client-report project row (matches grid logic).
     *
     * @param object $proj
     * @return float|null 0–100 or null when no valid quality row
     */
    private function client_report_project_quality_pct($proj, $from_date, $to_date)
    {
        $hasValidQualityError = false;
        if (!empty($from_date) && !empty($to_date)) {
            if (!empty($proj->analyzer_report_date)) {
                $errorDate = strtotime($proj->analyzer_report_date);
                $fromDate = strtotime($from_date);
                $toDate = strtotime($to_date);
                if ($errorDate !== false && $fromDate !== false && $toDate !== false
                    && $errorDate >= $fromDate && $errorDate <= $toDate) {
                    $hasValidQualityError = true;
                }
            }
        } else {
            $hasValidQualityError = !empty($proj->analyzer_num_of_errors) || !empty($proj->reviewer_num_of_errors);
        }

        if (!$hasValidQualityError) {
            return null;
        }

        $analyzerErrors = isset($proj->analyzer_num_of_errors) ? (float) $proj->analyzer_num_of_errors : 0;
        $reviewerErrors = isset($proj->reviewer_num_of_errors) ? (float) $proj->reviewer_num_of_errors : 0;
        $projectTotalErrors = $analyzerErrors + $reviewerErrors;

        return 100 - $projectTotalErrors;
    }

    /**
     * Empty department summary row (dashes in UI).
     */
    private function client_report_dept_kpi_empty_row($deptLabel, $monthLabel = '', $monthKey = '')
    {
        return array(
            'month' => $monthLabel,
            'month_key' => $monthKey,
            'label' => $deptLabel,
            'prod_hours' => null,
            'pg_hours' => null,
            'utilization_hours' => null,
            'productivity_pct' => null,
            'project_general_pct' => null,
            'utilization_pct' => null,
            'quality_pct' => null,
            'invoiced_hours' => null,
            'difference' => null,
        );
    }

    /**
     * Calendar months in a client-report date range (Y-m keys, April-style labels).
     *
     * @param string $from_date
     * @param string $to_date
     * @return array<string, array{key:string,label:string,label_with_year:string,year:string,from_date:string,to_date:string}>
     */
    private function client_report_months_in_range($from_date, $to_date)
    {
        $months = array();
        if (empty($from_date) || empty($to_date)) {
            return $months;
        }
        try {
            $start = new DateTime($from_date);
            $end = new DateTime($to_date);
        } catch (Exception $e) {
            return $months;
        }
        $current = clone $start;
        $current->modify('first day of this month');
        $endMonth = clone $end;
        $endMonth->modify('first day of this month');
        while ($current <= $endMonth) {
            $key = $current->format('Y-m');
            $months[$key] = array(
                'key' => $key,
                'label' => $current->format('F'),
                'label_with_year' => $current->format('F Y'),
                'year' => $current->format('Y'),
                'from_date' => $current->format('Y-m-01'),
                'to_date' => $current->format('Y-m-t'),
            );
            $current->modify('first day of next month');
        }
        return $months;
    }

    /**
     * Resolve a project row to a Y-m key inside the selected range.
     *
     * @param object $proj
     * @param array $monthsCovered
     * @return string
     */
    private function client_report_project_month_key($proj, array $monthsCovered)
    {
        if (!empty($proj->report_month)) {
            $key = (string) $proj->report_month;
            if (isset($monthsCovered[$key])) {
                return $key;
            }
        }
        if (count($monthsCovered) === 1) {
            $keys = array_keys($monthsCovered);
            return $keys[0];
        }
        return '';
    }

    /**
     * True when KPI summary should match filtered client-report grid scope (not all employees).
     */
    private function client_report_summary_uses_project_scope($grid, $search)
    {
        if (!is_array($grid)) {
            $grid = array();
        }
        if (!empty($grid['clients']) || !empty($grid['pms']) || !empty($grid['project'])) {
            return true;
        }
        return trim((string) $search) !== '';
    }

    /**
     * Filter client-report project rows (same rules as grid / Excel client filter).
     *
     * @param array $projects
     * @param array $grid
     * @param string $search
     * @return array
     */
    private function apply_client_report_row_filters(array $projects, array $grid, $search = '')
    {
        $this->load->helper('kpi_display');
        return client_report_filter_project_rows($projects, $grid, $search);
    }

    /**
     * Map a project row department label to a primary delivery department bucket.
     *
     * @param string $rawDept
     * @param array $deptOrder
     * @return string
     */
    private function client_report_resolve_department_bucket($rawDept, array $deptOrder)
    {
        $dept = function_exists('ts_normalize_primary_delivery_department')
            ? ts_normalize_primary_delivery_department($rawDept)
            : trim((string) $rawDept);
        if ($dept !== '' && in_array($dept, $deptOrder, true)) {
            return $dept;
        }

        $compact = strtolower(preg_replace('/[\s\-_]+/', '', (string) $rawDept));
        if ($compact !== '' && strpos($compact, '2d') !== false && strpos($compact, 'cad') !== false) {
            return '2D Auto CAD';
        }
        if ($compact !== '' && strpos($compact, '3d') !== false && strpos($compact, 'visual') !== false) {
            return '3D Visualization';
        }

        return '';
    }

    /**
     * Department KPI summary from filtered client-project hours (matches visible grid totals).
     * One row per month in the selected range, then department.
     *
     * @param string $from_date
     * @param string $to_date
     * @param string|array $department
     * @param string $search
     * @param array $grid
     * @return array{rows: array, has_data: bool}
     */
    private function getClientReportDepartmentKpiSummaryFromProjects($from_date, $to_date, $department, $search, array $grid, $projectRows = null)
    {
        $rows = array();
        if (empty($from_date) || empty($to_date)) {
            return array('rows' => $rows, 'has_data' => false);
        }

        $monthsCovered = $this->client_report_months_in_range($from_date, $to_date);
        if (empty($monthsCovered)) {
            return array('rows' => $rows, 'has_data' => false);
        }
        $yearsInRange = array();
        foreach ($monthsCovered as $monthInfo) {
            $yearsInRange[$monthInfo['year']] = true;
        }
        $useYearInMonthLabel = count($yearsInRange) > 1;

        $deptOrder = function_exists('ts_primary_delivery_departments')
            ? ts_primary_delivery_departments()
            : array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');

        $filterDepts = array();
        if (!empty($department)) {
            if (is_array($department)) {
                $filterDepts = array_values(array_filter(array_map('trim', $department), function ($v) {
                    return (string) $v !== '';
                }));
            } elseif (is_string($department)) {
                $filterDepts = array_values(array_filter(array_map('trim', explode(',', $department)), function ($v) {
                    return (string) $v !== '';
                }));
            }
        }
        $displayDeptOrder = $deptOrder;
        if (!empty($filterDepts)) {
            $displayDeptOrder = array();
            foreach ($deptOrder as $dKey) {
                if (in_array($dKey, $filterDepts, true)) {
                    $displayDeptOrder[] = $dKey;
                }
            }
        }
        if (empty($displayDeptOrder)) {
            return array('rows' => array(), 'has_data' => false);
        }

        if (is_array($projectRows)) {
            $projects = $projectRows;
        } else {
            $projects = $this->getAllClientInformation('', $search, $from_date, $to_date, $department, count($monthsCovered) > 1);
            $projects = $this->apply_client_report_row_filters($projects, $grid, $search);
        }

        $hasReportMonth = false;
        foreach ($projects as $proj) {
            if (!empty($proj->report_month)) {
                $hasReportMonth = true;
                break;
            }
        }
        if (count($monthsCovered) > 1 && !$hasReportMonth && !empty($projects)) {
            $projects = $this->getAllClientInformation('', $search, $from_date, $to_date, $department, true);
            $projects = $this->apply_client_report_row_filters($projects, $grid, $search);
        }

        $searchHasFilter = trim((string) $search) !== ''
            || (is_array($grid) && (!empty($grid['clients']) || !empty($grid['pms']) || !empty($grid['project'])));

        $monthDeptHours = array();
        foreach ($monthsCovered as $monthKey => $monthInfo) {
            $monthDeptHours[$monthKey] = array();
            foreach ($deptOrder as $dKey) {
                $monthDeptHours[$monthKey][$dKey] = array(
                    'production' => 0.0,
                    'general' => 0.0,
                    'invoiced' => 0.0,
                    'quality_sum' => 0.0,
                    'quality_count' => 0,
                );
            }
        }

        if (empty($projects)) {
            if ($searchHasFilter) {
                return array('rows' => array(), 'has_data' => false);
            }
            foreach ($monthsCovered as $monthKey => $monthInfo) {
                $monthLabel = $useYearInMonthLabel ? $monthInfo['label_with_year'] : $monthInfo['label'];
                foreach ($displayDeptOrder as $deptLabel) {
                    $rows[] = $this->client_report_dept_kpi_empty_row($deptLabel, $monthLabel, $monthKey);
                }
            }
            return array('rows' => $rows, 'has_data' => !empty($rows));
        }

        foreach ($projects as $proj) {
            $monthKey = $this->client_report_project_month_key($proj, $monthsCovered);
            if ($monthKey === '' || !isset($monthDeptHours[$monthKey])) {
                continue;
            }
            $rawDept = isset($proj->department) ? (string) $proj->department : '';
            $dept = $this->client_report_resolve_department_bucket($rawDept, $deptOrder);
            if ($dept === '') {
                continue;
            }
            $production = isset($proj->total_hours) ? (float) $proj->total_hours : 0.0;
            $general = isset($proj->general_hours) ? (float) $proj->general_hours : 0.0;
            $monthDeptHours[$monthKey][$dept]['production'] += $production;
            $monthDeptHours[$monthKey][$dept]['general'] += $general;
            if (isset($proj->project_invoice_amt) && $proj->project_invoice_amt !== '' && $proj->project_invoice_amt !== null) {
                $monthDeptHours[$monthKey][$dept]['invoiced'] += (float) $proj->project_invoice_amt;
            }
            $monthFrom = $monthsCovered[$monthKey]['from_date'];
            $monthTo = $monthsCovered[$monthKey]['to_date'];
            $qualityPct = $this->client_report_project_quality_pct($proj, $monthFrom, $monthTo);
            if ($qualityPct !== null) {
                $monthDeptHours[$monthKey][$dept]['quality_sum'] += $qualityPct;
                $monthDeptHours[$monthKey][$dept]['quality_count']++;
            }
        }

        foreach ($monthsCovered as $monthKey => $monthInfo) {
            $monthLabel = $useYearInMonthLabel ? $monthInfo['label_with_year'] : $monthInfo['label'];
            $monthRows = array();
            foreach ($displayDeptOrder as $deptLabel) {
                $bucket = $monthDeptHours[$monthKey][$deptLabel];
                $production = $bucket['production'];
                $general = $bucket['general'];
                $invoiced = $bucket['invoiced'];
                $total = $production + $general;
                $utilHours = $total;

                if ($total <= 0 && $invoiced <= 0 && $bucket['quality_count'] === 0) {
                    if ($searchHasFilter) {
                        continue;
                    }
                    $monthRows[] = $this->client_report_dept_kpi_empty_row($deptLabel, $monthLabel, $monthKey);
                    continue;
                }

                $qualityPct = null;
                if ($bucket['quality_count'] > 0) {
                    $qualityPct = round($bucket['quality_sum'] / $bucket['quality_count']);
                }

                $monthRows[] = array(
                    'month' => $monthLabel,
                    'month_key' => $monthKey,
                    'label' => $deptLabel,
                    'prod_hours' => $production > 0 ? round($production, 2) : ($total > 0 ? 0 : null),
                    'pg_hours' => $general > 0 ? round($general, 2) : ($total > 0 ? 0 : null),
                    'utilization_hours' => $utilHours > 0 ? round($utilHours, 2) : null,
                    'productivity_pct' => $total > 0 ? round(($production / $total) * 100) : null,
                    'project_general_pct' => $total > 0 ? round(($general / $total) * 100) : null,
                    'utilization_pct' => $total > 0 ? round(($utilHours / $total) * 100) : null,
                    'quality_pct' => $qualityPct,
                    'invoiced_hours' => ($total > 0 || $invoiced != 0) ? round($invoiced, 2) : null,
                    'difference' => ($total > 0 || $invoiced != 0) ? round($invoiced - $total, 2) : null,
                );
            }
            if (empty($monthRows)) {
                foreach ($displayDeptOrder as $deptLabel) {
                    $monthRows[] = $this->client_report_dept_kpi_empty_row($deptLabel, $monthLabel, $monthKey);
                }
            }
            foreach ($monthRows as $monthRow) {
                $rows[] = $monthRow;
            }
        }

        if (empty($rows)) {
            return array('rows' => array(), 'has_data' => false);
        }

        return array('rows' => $rows, 'has_data' => true);
    }
    
 
//func for monthly summary table 
public function getkpiInformationSummary(){
        
  
        $empWiseKpi = $this->session->userdata['logged_in_timesheet']['user_type'];
        
        $empId = $this->session->userdata['logged_in_timesheet']['empId'];
        
        if($empWiseKpi == 'developer'):
        
            $employeeQ = $this->db->select('empId, name, reporting_manger, department, emp_com_id')
            ->from('employee_details')
            ->where('status', 'Active')
            ->where('empId', $empId)
            ->get()->result();
    
    elseif ($empId == '140' ):
        $departmentsToInclude = ['Architectural', 'Structural','3D Visualization', 'MEP'];
        $employeeQ = $this->db->select('empId, name, reporting_manger, department, emp_com_id')
            ->from('employee_details')
            ->where('status', 'Active')
            ->where_in('department', $departmentsToInclude) 
            ->where_not_in('empId', ['146', '230', '149', '455','41', '394', '270', '47', '182', '71', '53', '155']) 
            ->order_by('emp_com_id', 'asc')
           
            ->get()->result();
        
        elseif($empWiseKpi == 'manager'):

            $employeeQ = $this->db->select('empId, name, reporting_manger, department, emp_com_id')
            ->from('employee_details')
            ->where('status', 'Active')
            ->group_start()
            ->where('reporting_manger', $empId)
            ->or_where('empId', $empId)
            ->group_end()
            ->order_by('(empId = ' . $this->db->escape($empId) . ')', 'DESC')
            ->order_by('emp_com_id', 'asc')
            ->get()->result();
        
        elseif($empId == '149'):
        
        $employeeQ = $this->db->select('empId, name, reporting_manger, department, emp_com_id')
        ->from('employee_details')
        ->where('status', 'Active')
        ->where('department', 'MEP')
        ->where('empId !=', '149') // Exclude empId 149 from the results
        ->order_by('emp_com_id', 'asc')
        ->get()->result();
        
        elseif($empId == '47'):
        
        $departments = array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD');
        $employeeQ = $this->db->select('empId, name, reporting_manger, department, emp_com_id')
            ->from('employee_details')
            ->where('status', 'Active')
            ->where_in('department', $departments)
            ->where_not_in('empId', ['47', '270']) // Exclude empIds 47 and 270
            ->order_by('emp_com_id', 'asc')
            ->get()->result();
        
        else: 
        $departmentsToInclude = ['Architectural', 'Structural','3D Visualization', 'MEP'];
        $employeeQ = $this->db->select('empId, name, reporting_manger, department, emp_com_id')
            ->from('employee_details')
            ->where('status', 'Active')
            ->where_in('department', $departmentsToInclude) 
            ->where_not_in('empId', ['146', '230', '149', '455','41', '394', '270', '47', '182', '71', '53', '155']) 
            ->order_by('emp_com_id', 'asc')
           
            ->get()->result();
         
        
        endif;
//        echo $this->db->last_query();

        return $employeeQ;     
    }
    
         
//count for monthly kpi report table
public function get_total_kpis($monthName = '', $search = '', $from_date = '', $to_date = ''){

    $empWiseKpi = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];

    $this->db->from('employee_details')
             ->join('employee_details as manager', 'manager.empId = employee_details.reporting_manger', 'left')
             ->where('employee_details.status', 'Active');

if (!empty($search)) {
    $searchTerms = array_map('trim', explode(',', $search));
    $this->db->group_start();
    foreach ($searchTerms as $term) {
        if (!empty($term)) {
            $this->db->or_group_start()
                   ->like('employee_details.name', $term, 'both')
                   ->or_like('manager.name', $term, 'both');
            
  
            if (stripos($term, 'arch') !== false) {
                $this->db->or_where_in('employee_details.department', 
                    ['Architectural', 'Structural', '3D Visualization']);
            }
            $this->db->or_like('employee_details.department', $term, 'both');
            
            $this->db->group_end();
        }
    }
    $this->db->group_end();
}

   
    if ($empWiseKpi == 'developer') {
        $this->db->where('employee_details.empId', $empId);
    } elseif ($this->_kpiFullAccessUser($empWiseKpi, $empId)) {
        $currentFunction = $this->uri->segment(2);
        list($departmentsToInclude, $excludeEmpIds) = $this->_resolveKpiDepartmentScope($currentFunction);
        $this->db->where_in('employee_details.department', $departmentsToInclude);
        if (!empty($excludeEmpIds)) {
            $this->db->where_not_in('employee_details.empId', $excludeEmpIds);
        }
    } 
    elseif ($empWiseKpi == 'manager') {
        $this->db->group_start();
        $this->db->where('employee_details.reporting_manger', $empId);
        $this->db->or_where('employee_details.empId', $empId);
        $this->db->group_end();
    } elseif ($empId == '149') {
        $this->db->where('employee_details.department', 'MEP')
                 ->where('employee_details.empId !=', '149');
    } elseif ($empId == '47') {
        $departments = ['Architectural', 'Structural', '3D Visualization'];
        $this->db->where_in('employee_details.department', $departments)
                 ->where_not_in('employee_details.empId', ['47', '270']);
    } else {
        $currentFunction = $this->uri->segment(2);
        list($departmentsToInclude, $excludeEmpIds) = $this->_resolveKpiDepartmentScope($currentFunction);
        $this->db->where_in('employee_details.department', $departmentsToInclude);
        if (!empty($excludeEmpIds)) {
            $this->db->where_not_in('employee_details.empId', $excludeEmpIds);
        }
    }

    
    return $this->db->count_all_results();
}

    
//count for consolidated kpi report table
public function get_total_kpis_cons($search = '', $from_date = '', $to_date = '', $department = ''){
    $empWiseKpi = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];

    $this->db->from('employee_details')
             ->join('employee_details as manager', 'manager.empId = employee_details.reporting_manger', 'left')
             ->where('employee_details.status', 'Active');

if (!empty($search)) {
    $searchTerms = array_map('trim', explode(',', $search));
    $this->db->group_start();
    foreach ($searchTerms as $term) {
        if (!empty($term)) {
            $this->db->or_group_start()
                   ->like('employee_details.name', $term, 'both')
                   ->or_like('manager.name', $term, 'both');
            
            // Enhanced handling for Architecture department group
            if (stripos($term, 'arch') !== false) {
                $this->db->or_where_in('employee_details.department', 
                    ['Architectural', 'Structural', '3D Visualization']);
            }
            $this->db->or_like('employee_details.department', $term, 'both');
            
            $this->db->group_end();
        }
    }
    $this->db->group_end();
}

    // Apply department filter if provided (takes priority over default logic)
    if (!empty($department)) {
        // Allow multiple departments (array) or single department (string)
        if (is_array($department)) {
            $this->db->where_in('employee_details.department', $department);
        } else {
            $this->db->where('employee_details.department', $department);
        }
    } elseif ($empWiseKpi == 'developer') {
        $this->db->where('employee_details.empId', $empId);
    } elseif ($empId == '140' ) {
        
        $currentFunction = $this->uri->segment(2);
        
        if (strpos($currentFunction, 'mep') !== false) {
            $departmentsToInclude = ['MEP'];
            $excludeEmpIds = ['146', '230', '149', '455'];
        } elseif ($currentFunction === 'consolidatedReport') {
            $departmentsToInclude = function_exists('ts_primary_delivery_departments') ? ts_primary_delivery_departments() : ['Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP'];
            $excludeEmpIds = ['146', '230', '149', '455','41', '394', '270', '47', '182', '71', '53', '155'];
        } else {
            $departmentsToInclude = ['Architectural', 'Structural', '3D Visualization'];
            $excludeEmpIds = ['41', '394', '270', '47', '182', '71', '53', '155'];
        }

        $this->db->where_in('employee_details.department', $departmentsToInclude);
        
        if (!empty($excludeEmpIds)) {
            $this->db->where_not_in('employee_details.empId', $excludeEmpIds);
        }
        
    } 
    elseif ($empWiseKpi == 'manager') {
        $this->db->group_start();
        $this->db->where('employee_details.reporting_manger', $empId);
        $this->db->or_where('employee_details.empId', $empId);
        $this->db->group_end();
    } elseif ($empId == '149') {
        $this->db->where('employee_details.department', 'MEP')
                 ->where('employee_details.empId !=', '149');
    } elseif ($empId == '47') {
        $departments = ['Architectural', 'Structural', '3D Visualization'];
        $this->db->where_in('employee_details.department', $departments)
                 ->where_not_in('employee_details.empId', ['47', '270']);
    } else {
        $currentFunction = $this->uri->segment(2);
        
         if (strpos($currentFunction, 'mep') !== false) {
            $departmentsToInclude = ['MEP'];
            $excludeEmpIds = ['146', '230', '149', '455'];
        } elseif ($currentFunction === 'consolidatedReport') {
            $departmentsToInclude = function_exists('ts_primary_delivery_departments') ? ts_primary_delivery_departments() : ['Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP'];
            $excludeEmpIds = ['146', '230', '149', '455','41', '394', '270', '47', '182', '71', '53', '155'];
        } else {
            $departmentsToInclude = ['Architectural', 'Structural', '3D Visualization'];
            $excludeEmpIds = ['41', '394', '270', '47', '182', '71', '53', '155'];
        }

        $this->db->where_in('employee_details.department', $departmentsToInclude);
        
        // Exclude MEP only when not in MEP view and not in consolidated report
        if (strpos($currentFunction, 'mep') === false && $currentFunction !== 'consolidatedReport') {
            $this->db->where('employee_details.department !=', 'MEP');
        }
        
        if (!empty($excludeEmpIds)) {
            $this->db->where_not_in('employee_details.empId', $excludeEmpIds);
        }
    }
    
    return $this->db->count_all_results();
}
 
 
//func for monthly kpi report table ( without limit - for excel )   
public function getAllKpiInformation($monthName = '', $search = '', $from_date = '', $to_date = ''){
       
        $empWiseKpi = $this->session->userdata['logged_in_timesheet']['user_type'];
        $empId = $this->session->userdata['logged_in_timesheet']['empId'];

        $this->db->select('employee_details.empId, employee_details.name, employee_details.reporting_manger, employee_details.department, employee_details.emp_com_id, manager.name as manager_name')
                 ->from('employee_details')
                 ->join('employee_details as manager', 'manager.empId = employee_details.reporting_manger', 'left')
                 ->where('employee_details.status', 'Active');

       
        if (!empty($department)) {
            $this->db->where('employee_details.department', $department);
        }

      
        if (!empty($excludedEmpIds)) {
            $this->db->where_not_in('employee_details.empId', $excludedEmpIds);
        }

       
        if (!empty($search)) {
            // Split search terms by commas and trim whitespace
            $searchTerms = array_map('trim', explode(',', $search));

            $this->db->group_start();
            foreach ($searchTerms as $term) {
                if (!empty($term)) {
                    $this->db->or_group_start()  // Start a new group for each term
                             ->like('employee_details.name', $term)
                             ->or_like('manager.name', $term)
                             ->group_end();
                }
            }
            $this->db->group_end();
        }

        if ($empWiseKpi == 'developer') {
            $this->db->where('employee_details.empId', $empId);
        } elseif ($empId == '140' ) {
        
                
        $currentFunction = $this->uri->segment(2);
        
        if (strpos($currentFunction, 'mep') !== false) {
            $departmentsToInclude = ['MEP'];
            $excludeEmpIds = ['146', '230', '149', '455'];
        } elseif ($currentFunction === 'consolidatedReport') {
            $departmentsToInclude = function_exists('ts_primary_delivery_departments') ? ts_primary_delivery_departments() : ['Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP'];
            $excludeEmpIds = ['146', '230', '149', '455','41', '394', '270', '47', '182', '71', '53', '155'];
        } else {

            $departmentsToInclude = ['Architectural', 'Structural', '3D Visualization'];
            $excludeEmpIds = ['41', '394', '270', '47', '182', '71', '53', '155'];
        }

        $this->db->where_in('employee_details.department', $departmentsToInclude);
        
        // Exclude MEP only when not in MEP view and not in consolidated report
        if (strpos($currentFunction, 'mep') === false && $currentFunction !== 'consolidatedReport') {
            $this->db->where('employee_details.department !=', 'MEP');
        }
        
        if (!empty($excludeEmpIds)) {
            $this->db->where_not_in('employee_details.empId', $excludeEmpIds);
        }
        
    } 
       elseif ($empWiseKpi == 'manager') {
            $this->db->group_start();
            $this->db->where('employee_details.reporting_manger', $empId);
            $this->db->or_where('employee_details.empId', $empId);
            $this->db->group_end();
            $this->db->order_by('(employee_details.empId = ' . $this->db->escape($empId) . ')', 'DESC');
            $this->db->order_by('employee_details.emp_com_id', 'asc');
        } elseif ($empId == '149') {
            $this->db->where('employee_details.department', 'MEP')
                     ->where('employee_details.empId !=', '149');
            $this->db->order_by('employee_details.emp_com_id', 'asc');
        } elseif ($empId == '47') {
            $departments = ['Architectural', 'Structural', '3D Visualization'];
            $this->db->where_in('employee_details.department', $departments)
                     ->where_not_in('employee_details.empId', ['47', '270']);
            $this->db->order_by('employee_details.emp_com_id', 'asc');
} else {
        $currentFunction = $this->uri->segment(2);
        if (strpos($currentFunction, 'mep') !== false) {
            $departmentsToInclude = ['MEP'];
            $excludeEmpIds = ['146', '230', '149', '455'];
        } elseif ($currentFunction === 'consolidatedReport') {
            $departmentsToInclude = ['Architectural', 'Structural', '3D Visualization', 'MEP'];
            $excludeEmpIds = ['146', '230', '149', '455','41', '394', '270', '47', '182', '71', '53', '155'];
        } else {
           
            $departmentsToInclude = ['Architectural', 'Structural', '3D Visualization'];
            $excludeEmpIds = ['41', '394', '270', '47', '182', '71', '53', '155'];
        }

        $this->db->where_in('employee_details.department', $departmentsToInclude);

        // Exclude MEP only when not in MEP view and not in consolidated report
        if (strpos($currentFunction, 'mep') === false && $currentFunction !== 'consolidatedReport') {
            $this->db->where('employee_details.department !=', 'MEP');
        }

        if (!empty($excludeEmpIds)) {
            $this->db->where_not_in('employee_details.empId', $excludeEmpIds);
        }
    }

        $employeeQ = $this->db->get()->result();
        return $employeeQ;
    } 
    
    
//func for consolidated kpi report table ( without limit - for excel )         
public function getAllConsolidatedKpiInformation($search = null, $empWiseKpi = null, $empId = null, $from_date = '', $to_date = '', $department = '') {
        $searchMatchEmpIds = array();
        if (!empty($search)) {
            $searchTerms = array_map('trim', explode(',', $search));
            $this->db->select('empId')->from('employee_details')->where('status', 'Active');
            $this->db->group_start();
            foreach ($searchTerms as $term) {
                if (!empty($term)) $this->db->or_like('name', $term, 'both');
            }
            $this->db->group_end();
            $res = $this->db->get()->result();
            foreach ($res as $r) $searchMatchEmpIds[] = $r->empId;
            $searchMatchEmpIds = array_values(array_unique($searchMatchEmpIds));
        }

        $this->db->select('ed.empId, ed.emp_com_id, ed.name, COALESCE(NULLIF(manager.department, ""), ed.department) as department, ed.reporting_manger, manager.name as manager_name', false);
        $this->db->from('employee_details ed');
        $this->db->join('employee_details as manager', 'manager.empId = ed.reporting_manger', 'left');
        $this->db->where('ed.status', 'Active');

        // Apply department filter if provided (takes priority over default logic)
        if (!empty($department)) {
            if (is_array($department)) {
                $this->db->where_in('ed.department', $department);
            } else {
                $this->db->where('ed.department', $department);
            }
        } elseif ($empWiseKpi == 'developer') {
            $this->db->where('ed.empId', $empId);
        } elseif ($empWiseKpi == 'manager') {
            $this->db->group_start();
            $this->db->where('ed.reporting_manger', $empId);
            $this->db->or_where('ed.empId', $empId);
            $this->db->group_end();
            $this->db->order_by('(ed.empId = ' . $this->db->escape($empId) . ')', 'DESC');
            $this->db->order_by('ed.emp_com_id', 'asc');
        } elseif ($empId == '149') {
            $this->db->where('ed.department', 'MEP')
                     ->where('ed.empId !=', '149');
            $this->db->order_by('ed.emp_com_id', 'asc');
        } elseif ($empId == '47') {
            $departments = ['Architectural', 'Structural', '3D Visualization'];
            $this->db->where_in('ed.department', $departments)
                     ->where_not_in('ed.empId', ['47', '270']);
            $this->db->order_by('ed.emp_com_id', 'asc');
} else {
            $departmentsToInclude = function_exists('ts_primary_delivery_departments') ? ts_primary_delivery_departments() : ['Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP'];
            $excludeEmpIds = ['47', '270', '149'];
            $this->db->where_in('ed.department', $departmentsToInclude)
                     ->where_not_in('ed.empId', $excludeEmpIds);
        }

        if (!empty($search)) {
            // Handle comma-separated search terms (same as getkpiInformation)
            $searchTerms = array_map('trim', explode(',', $search));
            $this->db->group_start();
            foreach ($searchTerms as $term) {
                if (!empty($term)) {
                    $this->db->or_group_start()
                           ->like('ed.name', $term, 'both')
                           ->or_like('manager.name', $term, 'both')
                           ->or_like('ed.emp_com_id', $term, 'both');
                    
                    // Enhanced handling for Architecture department group
                    if (stripos($term, 'arch') !== false) {
                        $this->db->or_where_in('ed.department', 
                            ['Architectural', 'Structural', '3D Visualization']);
                    }
                    $this->db->or_like('ed.department', $term, 'both');
                    $this->db->group_end();
                }
            }
            if (!empty($searchMatchEmpIds)) {
                $this->db->or_where_in('ed.empId', $searchMatchEmpIds);
            }
            $this->db->group_end();
        }

        $employees = $this->db->get()->result_array();
        $consolidatedData = [];
        
        // Calculate which months to include based on date range or default behavior
        if (!empty($from_date) && !empty($to_date)) {
            // Use date range - calculate months within the range
            $startDate = new DateTime($from_date);
            $endDate = new DateTime($to_date);
            $startMonth = (int)$startDate->format('n');
            $startYear = (int)$startDate->format('Y');
            $endMonth = (int)$endDate->format('n');
            $endYear = (int)$endDate->format('Y');
            
            $monthNames = [];
            $currentDate = clone $startDate;
            $currentDate->modify('first day of this month');
            
            while ($currentDate <= $endDate) {
                $monthNames[] = (int)$currentDate->format('n');
                $currentDate->modify('+1 month');
            }
        } else {
            // Default behavior - January to previous month
            $currentMonth = date('n');
            $monthNames = range(1, $currentMonth - 1);
        }

        foreach ($employees as $employee) {
            $totalMonthlyProductivity = 0;
            $totalMonthlyProjectGeneral = 0;
            $totalMonthlyElogicGeneral = 0;
            $totalMonthlyAvailability = 0;
            $totalMonthlyUtilization = 0;
            $totalMonthlyTimeSpent = 0;
            $totalMonthlyinstanceData = 0;
            $totalMonthlyperkEmpAbsentDays = 0;
            $totalMonthlyLateloginandEarlyout = 0;
            $totalMonthlyQuality = 0;
            $validMonths = 0;

            foreach ($monthNames as $month) {
                $productionData = $this->empProductionHoursMonthWise($employee['empId'], $month);
                $productionHoursArray = explode('@#===', $productionData);

                $perkData = $this->perkabsent($employee['emp_com_id'], $month);
                $perkArray = explode('Perk@#', $perkData);

                $monthlyProduction = isset($productionHoursArray[0]) ? $productionHoursArray[0] : 0;
                $monthlyProjectGeneral = isset($productionHoursArray[1]) ? $productionHoursArray[1] : 0;
                $monthlyElogicGeneral = isset($productionHoursArray[2]) ? $productionHoursArray[2] : 0;
                $monthlyperkEmpAbsentDays = !empty($perkArray[0]) ? $perkArray[0] : 0;
                $monthlylateLogin = !empty($perkArray[1]) ? $perkArray[1] : 0;
                $monthlyearlyOut = !empty($perkArray[2]) ? $perkArray[2] : 0;

                $monthlyTotalHours = $monthlyProduction + $monthlyProjectGeneral + $monthlyElogicGeneral;
                $monthlyUtilization = $monthlyTotalHours > 0 ? (($monthlyProduction + $monthlyProjectGeneral) / $monthlyTotalHours) * 100 : 0;
                $monthlyTimeSpent = $this->LMShours($employee['empId'], $month);
                $monthlyinstanceData = $this->timesheetDefaulter($employee['empId'], $month);
                $monthlyQuality = $this->qualityyearlylog($employee['empId'], $month);

                // Set month-wise working hours (same logic as in your view)
                if ($month == '1') $monthWorkingHours = 178.5;
                elseif ($month == '2') $monthWorkingHours = 170.0;
                elseif ($month == '3') $monthWorkingHours = 161.5;
                elseif ($month == '4') $monthWorkingHours = 187.0;
                elseif ($month == '5') $monthWorkingHours = 178.5;
                elseif ($month == '6') $monthWorkingHours = 178.5;
                elseif ($month == '7') $monthWorkingHours = 195.5;
                elseif ($month == '8') $monthWorkingHours = 170.0;
                elseif ($month == '9') $monthWorkingHours = 187.0;
                elseif ($month == '10') $monthWorkingHours = 170.0;
                elseif ($month == '11') $monthWorkingHours = 170.0;
                elseif ($month == '12') $monthWorkingHours = 187.0;
                else $monthWorkingHours = 0;

                $monthlyAvailability = ($monthWorkingHours > 0) ? ($monthlyTotalHours / $monthWorkingHours) * 100 : 0;
                $monthlyLateloginandEarlyout = $monthlylateLogin + $monthlyearlyOut;

                if ($monthlyTotalHours > 0) {
                    $totalMonthlyProductivity += ($monthlyProduction / $monthlyTotalHours) * 100;
                    $totalMonthlyProjectGeneral += ($monthlyProjectGeneral / $monthlyTotalHours) * 100;
                    $totalMonthlyElogicGeneral += ($monthlyElogicGeneral / $monthlyTotalHours) * 100;
                    $totalMonthlyAvailability += $monthlyAvailability;
                    $totalMonthlyUtilization += $monthlyUtilization;
                    $totalMonthlyTimeSpent += $monthlyTimeSpent;
                    $totalMonthlyinstanceData += $monthlyinstanceData;
                    $totalMonthlyperkEmpAbsentDays += $monthlyperkEmpAbsentDays;
                    $totalMonthlyLateloginandEarlyout += $monthlyLateloginandEarlyout;
                    $totalMonthlyQuality += $monthlyQuality;
                    $validMonths++;
                }
            }

            $avgProductivityPercentage = ($validMonths > 0) ? ($totalMonthlyProductivity / $validMonths) : 0;
            $avgProjectGeneralPercentage = ($validMonths > 0) ? ($totalMonthlyProjectGeneral / $validMonths) : 0;
            $avgElogicGeneralPercentage = ($validMonths > 0) ? ($totalMonthlyElogicGeneral / $validMonths) : 0;
            $avgAvailabilityPercentage = ($validMonths > 0) ? ($totalMonthlyAvailability / $validMonths) : 0;
            $avgUtilizationPercentage = ($validMonths > 0) ? ($totalMonthlyUtilization / $validMonths) : 0;
            $avgQualityPercentage = ($validMonths > 0) ? ($totalMonthlyQuality / $validMonths) : 0;

            $getTeamwiseMangerName = $this->resourcelog_model->getManagerName($employee['reporting_manger']);

            $consolidatedData[] = [
                'empId' => $employee['empId'],
                'emp_com_id' => $employee['emp_com_id'],
                'name' => $employee['name'],
                'department' => $employee['department'],
                'reporting_manger' => $getTeamwiseMangerName,
                'avgProductivityPercentage' => $avgProductivityPercentage,
                'avgProjectGeneralPercentage' => $avgProjectGeneralPercentage,
                'avgElogicGeneralPercentage' => $avgElogicGeneralPercentage,
                'avgAvailabilityPercentage' => $avgAvailabilityPercentage,
                'avgUtilizationPercentage' => $avgUtilizationPercentage,
                'avgQualityPercentage' => $avgQualityPercentage,
                'totalinstanceData' => $totalMonthlyinstanceData,
                'totalperkEmpAbsentDays' => $totalMonthlyperkEmpAbsentDays,
                'totalLateloginandEarlyout' => $totalMonthlyLateloginandEarlyout,
                'aboveBeyondHours' => $totalMonthlyTimeSpent, // Assuming LMS hours is "Above & Beyond"
            ];
        }

        return $consolidatedData;
    }
  
 
//func for hours - monthly-  kpi (Approved only - legacy behaviour used by existing reports)
public function empProductionHoursMonthWise($empId , $monthId, $year = null){        
        $currentYear = date('Y');

        // If year is provided, use it; otherwise use default logic
        if ($year !== null && is_numeric($year)) {
            $previousMonthYear = (int)$year;
        } elseif ($monthId == 12) {
            $previousMonthYear = $currentYear - 1;
        } else {
            $previousMonthYear = $currentYear;
        }

        if (!empty($empId)):
            $queryProductionHours = $this->db->select('emp_record_details.emp_time_hours, emp_record_details.project_Id, emp_record_details.empId, project_details.project_name')
                ->from('emp_record_details')
                ->join('project_details', 'emp_record_details.project_Id = project_details.project_Id', 'left')
                ->where('emp_record_details.empId', $empId)
                //->where('project_details.status', 'Process')
                ->where('emp_record_details.status', 'Approved')
                ->where('YEAR(emp_record_details.emp_report_dates)', $previousMonthYear)
                ->where('MONTH(emp_record_details.emp_report_dates)', $monthId)
                ->get()
                ->result();

            if (!empty($queryProductionHours)) {
                $totalproductionHours = 0;
                $totalempGeneralHours = 0;
                $totalempProductionGeneralHours = 0;

                foreach ($queryProductionHours as $productionH) {
                    $hideGeneral = str_replace(" - (General)", "", $productionH->project_name);

                    if ($productionH->project_name == 'General') {
                        $totalempGeneralHours += $productionH->emp_time_hours;
                    } elseif ($hideGeneral.' - (General)' == $productionH->project_name) {
                        $totalempProductionGeneralHours += $productionH->emp_time_hours;
                    } else {
                        $totalproductionHours += $productionH->emp_time_hours;
                    }
                }

                return $totalproductionHours .'@#==='. $totalempProductionGeneralHours.'@#==='.$totalempGeneralHours; 
            }

        endif;
        
    }

//func for hours - monthly-  kpi  (Approved + Unapproved – used for MEP report P/PG/E totals)
public function empProductionHoursMonthWiseAllStatus($empId , $monthId, $year = null){        
        $currentYear = date('Y');

        // If year is provided, use it; otherwise use default logic
        if ($year !== null && is_numeric($year)) {
            $previousMonthYear = (int)$year;
        } elseif ($monthId == 12) {
            $previousMonthYear = $currentYear - 1;
        } else {
            $previousMonthYear = $currentYear;
        }

        if (!empty($empId)):
            $queryProductionHours = $this->db->select('emp_record_details.emp_time_hours, emp_record_details.project_Id, emp_record_details.empId, project_details.project_name')
                ->from('emp_record_details')
                ->join('project_details', 'emp_record_details.project_Id = project_details.project_Id', 'left')
                ->where('emp_record_details.empId', $empId)
                //->where('project_details.status', 'Process')
                // include Approved, Unapproved, and Rejected timesheet records
                ->where_in('emp_record_details.status', ['Approved', 'Unapproved', 'Rejected'])
                ->where('YEAR(emp_record_details.emp_report_dates)', $previousMonthYear)
                ->where('MONTH(emp_record_details.emp_report_dates)', $monthId)
                ->get()
                ->result();

            if (!empty($queryProductionHours)) {
                $totalproductionHours = 0;
                $totalempGeneralHours = 0;
                $totalempProductionGeneralHours = 0;

                foreach ($queryProductionHours as $productionH) {
                    $hideGeneral = str_replace(" - (General)", "", $productionH->project_name);

                    if ($productionH->project_name == 'General') {
                        $totalempGeneralHours += $productionH->emp_time_hours;
                    } elseif ($hideGeneral.' - (General)' == $productionH->project_name) {
                        $totalempProductionGeneralHours += $productionH->emp_time_hours;
                    } else {
                        $totalproductionHours += $productionH->emp_time_hours;
                    }
                }

                return $totalproductionHours .'@#==='. $totalempProductionGeneralHours.'@#==='.$totalempGeneralHours; 
            }

        endif;
        
        // Return zeros if no records found
        return '0@#===0@#===0';
    }

//func for hours - MEP report (includes Approved, Unapproved, and Rejected)
public function empProductionHoursMonthWiseMEP($empId , $monthId, $year = null){        
        $currentYear = date('Y');

        // If year is provided, use it; otherwise use default logic
        if ($year !== null && is_numeric($year)) {
            $previousMonthYear = (int)$year;
        } elseif ($monthId == 12) {
            $previousMonthYear = $currentYear - 1;
        } else {
            $previousMonthYear = $currentYear;
        }

        if (!empty($empId)):
            $queryProductionHours = $this->db->select('emp_record_details.emp_time_hours, emp_record_details.project_Id, emp_record_details.empId, project_details.project_name')
                ->from('emp_record_details')
                ->join('project_details', 'emp_record_details.project_Id = project_details.project_Id', 'left')
                ->where('emp_record_details.empId', $empId)
                //->where('project_details.status', 'Process')
                // include Approved, Unapproved, and Rejected timesheet records for MEP
                ->where_in('emp_record_details.status', ['Approved', 'Unapproved', 'Rejected'])
                ->where('YEAR(emp_record_details.emp_report_dates)', $previousMonthYear)
                ->where('MONTH(emp_record_details.emp_report_dates)', $monthId)
                ->get()
                ->result();

            if (!empty($queryProductionHours)) {
                $totalproductionHours = 0;
                $totalempGeneralHours = 0;
                $totalempProductionGeneralHours = 0;

                foreach ($queryProductionHours as $productionH) {
                    $hideGeneral = str_replace(" - (General)", "", $productionH->project_name);

                    if ($productionH->project_name == 'General') {
                        $totalempGeneralHours += $productionH->emp_time_hours;
                    } elseif ($hideGeneral.' - (General)' == $productionH->project_name) {
                        $totalempProductionGeneralHours += $productionH->emp_time_hours;
                    } else {
                        $totalproductionHours += $productionH->emp_time_hours;
                    }
                }

                return $totalproductionHours .'@#==='. $totalempProductionGeneralHours.'@#==='.$totalempGeneralHours; 
            }

        endif;
        
        // Return zeros if no records found
        return '0@#===0@#===0';
    }
 
//func for hours - consolidated - kpi (optional $year: when provided, use it; otherwise use current year logic)
public function empProductionHoursMonthWiseCons($empId, $monthId, $year = null){
    $currentMonth = (int) date('n');
    $currentYear = (int) date('Y');
    if ($year !== null && is_numeric($year)) {
        $yearForQuery = (int) $year;
    } else {
        $yearForQuery = ($currentMonth == 1) ? ($currentYear - 1) : $currentYear;
    }

    if (empty($empId)) {
        return '0@#===0@#===0';
    }

    $queryProductionHours = $this->db->select('emp_record_details.emp_time_hours, emp_record_details.project_Id, emp_record_details.empId, project_details.project_name')
        ->from('emp_record_details')
        ->join('project_details', 'emp_record_details.project_Id = project_details.project_Id', 'left')
        ->where('emp_record_details.empId', $empId)
        ->where_in('emp_record_details.status', ['Approved', 'Unapproved', 'Rejected'])
        ->where('YEAR(emp_record_details.emp_report_dates)', $yearForQuery)
        ->where('MONTH(emp_record_details.emp_report_dates)', $monthId)
        ->get()
        ->result();

    if (empty($queryProductionHours)) {
        return '0@#===0@#===0';
    }

    $totalproductionHours = 0;
    $totalempGeneralHours = 0;
    $totalempProductionGeneralHours = 0;

    foreach ($queryProductionHours as $productionH) {
        $hideGeneral = str_replace(" - (General)", "", $productionH->project_name);
        if ($productionH->project_name == 'General') {
            $totalempGeneralHours += $productionH->emp_time_hours;
        } elseif ($hideGeneral . ' - (General)' == $productionH->project_name) {
            $totalempProductionGeneralHours += $productionH->emp_time_hours;
        } else {
            $totalproductionHours += $productionH->emp_time_hours;
        }
    }

    return $totalproductionHours . '@#===' . $totalempProductionGeneralHours . '@#===' . $totalempGeneralHours;
}

/**
 * Batch-load all data needed for consolidated report (one query per data type) to avoid N*M per-row calls.
 * Returns arrays keyed by empId (and emp_com_id for perk) and month/year for use in the view.
 * @param array $empIds list of employee IDs
 * @param array $empComIdByEmpId [empId => emp_com_id]
 * @param array $monthYearPairs [['month'=>m, 'year'=>y], ...]
 * @return array ['production' => [empId][month][year]=string, 'perk' => [emp_com_id][month][year]=string, 'lms' => ..., 'defaulter' => ..., 'quality' => ...]
 */
public function getConsolidatedReportDataBatch($empIds, $empComIdByEmpId, $monthYearPairs) {
    $production = [];
    $perk = [];
    $lms = [];
    $defaulter = [];
    $quality = [];
    if (empty($empIds) || empty($monthYearPairs)) {
        return ['production' => $production, 'perk' => $perk, 'lms' => $lms, 'defaulter' => $defaulter, 'quality' => $quality];
    }
    $empIds = array_values(array_unique(array_filter($empIds)));
    $empComIds = array_values(array_unique(array_filter($empComIdByEmpId)));

    // 1. Production hours: one query for all emp_record_details in range (only requested months), then aggregate in PHP
    $monthConds = [];
    foreach ($monthYearPairs as $p) {
        $monthConds[] = '(YEAR(emp_record_details.emp_report_dates) = ' . (int)$p['year'] . ' AND MONTH(emp_record_details.emp_report_dates) = ' . (int)$p['month'] . ')';
    }
    $this->db->select('emp_record_details.empId, YEAR(emp_record_details.emp_report_dates) AS y, MONTH(emp_record_details.emp_report_dates) AS m, emp_record_details.emp_time_hours, project_details.project_name');
    $this->db->from('emp_record_details');
    $this->db->join('project_details', 'emp_record_details.project_Id = project_details.project_Id', 'left');
    $this->db->where_in('emp_record_details.empId', $empIds);
    $this->db->where_in('emp_record_details.status', ['Approved', 'Unapproved', 'Rejected']);
    if (!empty($monthConds)) {
        $this->db->where('(' . implode(' OR ', $monthConds) . ')', null, false);
    }
    $rows = $this->db->get()->result();
    foreach ($empIds as $eid) {
        foreach ($monthYearPairs as $p) {
            $production[$eid][(int)$p['month']][(int)$p['year']] = '0@#===0@#===0';
        }
    }
    foreach ($rows as $r) {
        $y = (int)$r->y;
        $m = (int)$r->m;
        if (!isset($production[$r->empId][$m][$y])) continue;
        $parts = explode('@#===', $production[$r->empId][$m][$y]);
        $prod = (float)$parts[0];
        $pg = (float)$parts[1];
        $gen = (float)$parts[2];
        $hideGeneral = str_replace(" - (General)", "", $r->project_name);
        if ($r->project_name == 'General') {
            $gen += $r->emp_time_hours;
        } elseif ($hideGeneral . ' - (General)' == $r->project_name) {
            $pg += $r->emp_time_hours;
        } else {
            $prod += $r->emp_time_hours;
        }
        $production[$r->empId][$m][$y] = $prod . '@#===' . $pg . '@#===' . $gen;
    }

    // 2. Perk absent: one query per (month,year) with 26-25 period (skip if no emp_com_ids)
    if (!empty($empComIds)) {
        foreach ($monthYearPairs as $p) {
            $monthId = (int)$p['month'];
            $reportYear = (int)$p['year'];
            if ($monthId == 1) {
                $prevM = 12;
                $prevY = $reportYear - 1;
            } else {
                $prevM = $monthId - 1;
                $prevY = $reportYear;
            }
            $startDatefromD = $prevY . '-' . str_pad($prevM, 2, '0', STR_PAD_LEFT) . '-26';
            $endDatefromD = $reportYear . '-' . str_pad($monthId, 2, '0', STR_PAD_LEFT) . '-25';
            $q = $this->db->select('perk_emp_code, perk_emp_absent_days, perk_emp_late_in, perk_emp_early_out')
                ->from('perk_monthly_data')
                ->where('from_date', $startDatefromD)
                ->where('to_date', $endDatefromD)
                ->where_in('perk_emp_code', $empComIds)
                ->get()->result();
            foreach ($empComIds as $ec) {
                $perk[$ec][$monthId][$reportYear] = '0Perk@#0Perk@#0';
            }
            foreach ($q as $row) {
                $perk[$row->perk_emp_code][$monthId][$reportYear] = $row->perk_emp_absent_days . 'Perk@#' . $row->perk_emp_late_in . 'Perk@#' . $row->perk_emp_early_out;
            }
        }
    }

    // 3. LMS hours: one query (match LMShours: same year/month for started_date and completed_date)
    $this->db->select('empId, YEAR(started_date) AS y, MONTH(started_date) AS m, SUM(watch_time) AS total_watch_time');
    $this->db->from('lms_video_progress');
    $this->db->where_in('empId', $empIds);
    $this->db->where('MONTH(completed_date) = MONTH(started_date)', null, false);
    $this->db->group_by(['empId', 'YEAR(started_date)', 'MONTH(started_date)']);
    $lmsRows = $this->db->get()->result();
    foreach ($empIds as $eid) {
        foreach ($monthYearPairs as $p) {
            $lms[$eid][(int)$p['month']][(int)$p['year']] = 0;
        }
    }
    foreach ($lmsRows as $r) {
        if (isset($lms[$r->empId][(int)$r->m][(int)$r->y])) {
            $lms[$r->empId][(int)$r->m][(int)$r->y] = (int)$r->total_watch_time;
        }
    }

    // 4. Timesheet defaulter: one query for all records in date range, then PHP loop per (empId, month, year)
    $minDate = null;
    $maxDate = null;
    foreach ($monthYearPairs as $p) {
        $d1 = $p['year'] . '-' . str_pad($p['month'], 2, '0', STR_PAD_LEFT) . '-01';
        $d2 = date('Y-m-t', strtotime($d1));
        if ($minDate === null || $d1 < $minDate) $minDate = $d1;
        if ($maxDate === null || $d2 > $maxDate) $maxDate = $d2;
    }
    foreach ($empIds as $eid) {
        foreach ($monthYearPairs as $p) {
            $defaulter[$eid][(int)$p['month']][(int)$p['year']] = 0;
        }
    }
    if ($minDate && $maxDate) {
        $erd = $this->db->select('empId, emp_report_dates, SUM(emp_time_hours) AS total_hrs')
            ->from('emp_record_details')
            ->where('emp_report_dates >=', $minDate)
            ->where('emp_report_dates <=', $maxDate)
            ->where_in('empId', $empIds)
            ->group_by(['empId', 'emp_report_dates'])
            ->get()->result();
        $byEmpDate = [];
        foreach ($erd as $e) {
            $byEmpDate[$e->empId][$e->emp_report_dates] = (float)$e->total_hrs;
        }
        $currentDate = new DateTime();
        $currentY = (int)$currentDate->format('Y');
        $currentM = (int)$currentDate->format('n');
        foreach ($monthYearPairs as $p) {
            $monthId = (int)$p['month'];
            $reportYear = (int)$p['year'];
            $startDatefromD = $reportYear . '-' . str_pad($monthId, 2, '0', STR_PAD_LEFT) . '-01';
            // Correct mktime argument order: hour, minute, second, month, day, year
            // This ensures we get the true last day of the report month instead of an invalid far-future date
            $lastDay = date('Y-m-t', mktime(0, 0, 0, $monthId, 1, $reportYear));
            $today = $currentDate->format('Y-m-d');
            $endDatefromD = ($reportYear == $currentY && $monthId == $currentM && $lastDay > $today) ? $today : $lastDay;
            $d = new DateTime($startDatefromD);
            $end = new DateTime($endDatefromD);
            while ($d <= $end) {
                $dw = (int)$d->format('w');
                if ($dw >= 1 && $dw <= 5) {
                    $dateStr = $d->format('Y-m-d');
                    foreach ($empIds as $eid) {
                        $hrs = isset($byEmpDate[$eid][$dateStr]) ? $byEmpDate[$eid][$dateStr] : 0;
                        // Only treat days with no entry as defaulters for consolidated report
                        if ($hrs == 0) $defaulter[$eid][$monthId][$reportYear]++;
                    }
                }
                $d->modify('+1 day');
            }
        }
    }

    // 5. Quality: one query per (month,year) to match qualityyearlylog logic (YEAR/MONTH of report)
    foreach ($monthYearPairs as $p) {
        $m = (int)$p['month'];
        $y = (int)$p['year'];
        $this->db->select('qty_empId, 100 - (SUM(analyzer_num_of_errors + reviewer_num_of_errors) / COUNT(qty_empId)) AS total_errors');
        $this->db->from('quality_error_log');
        $this->db->where('YEAR(analyzer_report_date)', $y);
        $this->db->where('MONTH(analyzer_report_date)', $m);
        $this->db->where_in('qty_empId', $empIds);
        $this->db->group_by('qty_empId');
        $qRows = $this->db->get()->result();
        foreach ($empIds as $eid) {
            // Keep missing monthly quality as null so UI can show '--' instead of '100%'.
            $quality[$eid][$m][$y] = null;
        }
        foreach ($qRows as $row) {
            $quality[$row->qty_empId][$m][$y] = $row->total_errors;
        }
    }

    return ['production' => $production, 'perk' => $perk, 'lms' => $lms, 'defaulter' => $defaulter, 'quality' => $quality];
}

/**
 * Batch-load data for month-wise KPI report (same idea as consolidated: avoid N*M per-row calls).
 * Uses empProductionHoursMonthWiseAllStatus logic (Approved/Unapproved/Rejected), perkabsentByDateRange, LMShours, timesheetDefaulter, qualitylog.
 * @param array $empIds
 * @param array $empComIdByEmpId [empId => emp_com_id]
 * @param array $empDeptByEmpId [empId => department] for qualitylog formula
 * @param array $monthYearPairs [['month'=>m, 'year'=>y], ...]
 * @return array ['production'=>..., 'perk'=>..., 'lms'=>..., 'defaulter'=>..., 'quality'=>...]
 */
public function getMonthWiseReportDataBatch($empIds, $empComIdByEmpId, $empDeptByEmpId, $monthYearPairs) {
    $production = [];
    $perk = [];
    $lms = [];
    $defaulter = [];
    $quality = [];
    if (empty($empIds) || empty($monthYearPairs)) {
        return ['production' => $production, 'perk' => $perk, 'lms' => $lms, 'defaulter' => $defaulter, 'quality' => $quality];
    }
    $empIds = array_values(array_unique(array_filter($empIds)));
    $empComIds = array_values(array_unique(array_filter($empComIdByEmpId)));

    // 1. Production hours (same as consolidated: Approved, Unapproved, Rejected - matches empProductionHoursMonthWiseAllStatus)
    $monthConds = [];
    foreach ($monthYearPairs as $p) {
        $monthConds[] = '(YEAR(emp_record_details.emp_report_dates) = ' . (int)$p['year'] . ' AND MONTH(emp_record_details.emp_report_dates) = ' . (int)$p['month'] . ')';
    }
    $this->db->select('emp_record_details.empId, YEAR(emp_record_details.emp_report_dates) AS y, MONTH(emp_record_details.emp_report_dates) AS m, emp_record_details.emp_time_hours, project_details.project_name');
    $this->db->from('emp_record_details');
    $this->db->join('project_details', 'emp_record_details.project_Id = project_details.project_Id', 'left');
    $this->db->where_in('emp_record_details.empId', $empIds);
    $this->db->where_in('emp_record_details.status', ['Approved', 'Unapproved', 'Rejected']);
    if (!empty($monthConds)) {
        $this->db->where('(' . implode(' OR ', $monthConds) . ')', null, false);
    }
    $rows = $this->db->get()->result();
    foreach ($empIds as $eid) {
        foreach ($monthYearPairs as $p) {
            $production[$eid][(int)$p['month']][(int)$p['year']] = '0@#===0@#===0';
        }
    }
    foreach ($rows as $r) {
        $y = (int)$r->y;
        $m = (int)$r->m;
        if (!isset($production[$r->empId][$m][$y])) continue;
        $parts = explode('@#===', $production[$r->empId][$m][$y]);
        $prod = (float)$parts[0];
        $pg = (float)$parts[1];
        $gen = (float)$parts[2];
        $hideGeneral = str_replace(" - (General)", "", $r->project_name);
        if ($r->project_name == 'General') {
            $gen += $r->emp_time_hours;
        } elseif ($hideGeneral . ' - (General)' == $r->project_name) {
            $pg += $r->emp_time_hours;
        } else {
            $prod += $r->emp_time_hours;
        }
        $production[$r->empId][$m][$y] = $prod . '@#===' . $pg . '@#===' . $gen;
    }

    // 2. Perk by date range (one query per month: to_date between monthStart and monthEnd, then pick latest to_date per emp)
    if (!empty($empComIds)) {
        foreach ($monthYearPairs as $p) {
            $m = (int)$p['month'];
            $y = (int)$p['year'];
            $monthStart = $y . '-' . str_pad($m, 2, '0', STR_PAD_LEFT) . '-01';
            $monthEnd = date('Y-m-t', strtotime($monthStart));
            $q = $this->db->select('perk_emp_code, perk_emp_absent_days, perk_emp_late_in, perk_emp_early_out, to_date')
                ->from('perk_monthly_data')
                ->where('to_date >=', $monthStart)
                ->where('to_date <=', $monthEnd)
                ->where_in('perk_emp_code', $empComIds)
                ->order_by('to_date', 'DESC')
                ->get()->result();
            foreach ($empComIds as $ec) {
                $perk[$ec][$m][$y] = '0Perk@#0Perk@#0';
            }
            foreach ($q as $row) {
                if (!isset($perk[$row->perk_emp_code][$m][$y]) || $perk[$row->perk_emp_code][$m][$y] === '0Perk@#0Perk@#0') {
                    $perk[$row->perk_emp_code][$m][$y] = $row->perk_emp_absent_days . 'Perk@#' . $row->perk_emp_late_in . 'Perk@#' . $row->perk_emp_early_out;
                }
            }
        }
    }

    // 3. LMS hours (same as consolidated)
    $this->db->select('empId, YEAR(started_date) AS y, MONTH(started_date) AS m, SUM(watch_time) AS total_watch_time');
    $this->db->from('lms_video_progress');
    $this->db->where_in('empId', $empIds);
    $this->db->where('MONTH(completed_date) = MONTH(started_date)', null, false);
    $this->db->group_by(['empId', 'YEAR(started_date)', 'MONTH(started_date)']);
    $lmsRows = $this->db->get()->result();
    foreach ($empIds as $eid) {
        foreach ($monthYearPairs as $p) {
            $lms[$eid][(int)$p['month']][(int)$p['year']] = 0;
        }
    }
    foreach ($lmsRows as $r) {
        if (isset($lms[$r->empId][(int)$r->m][(int)$r->y])) {
            $lms[$r->empId][(int)$r->m][(int)$r->y] = (int)$r->total_watch_time;
        }
    }

    // 4. Timesheet defaulter (same as consolidated)
    $minDate = null;
    $maxDate = null;
    foreach ($monthYearPairs as $p) {
        $d1 = $p['year'] . '-' . str_pad($p['month'], 2, '0', STR_PAD_LEFT) . '-01';
        $d2 = date('Y-m-t', strtotime($d1));
        if ($minDate === null || $d1 < $minDate) $minDate = $d1;
        if ($maxDate === null || $d2 > $maxDate) $maxDate = $d2;
    }
    foreach ($empIds as $eid) {
        foreach ($monthYearPairs as $p) {
            $defaulter[$eid][(int)$p['month']][(int)$p['year']] = 0;
        }
    }
    if ($minDate && $maxDate) {
        $erd = $this->db->select('empId, emp_report_dates, SUM(emp_time_hours) AS total_hrs')
            ->from('emp_record_details')
            ->where('emp_report_dates >=', $minDate)
            ->where('emp_report_dates <=', $maxDate)
            ->where_in('empId', $empIds)
            ->group_by(['empId', 'emp_report_dates'])
            ->get()->result();
        $byEmpDate = [];
        foreach ($erd as $e) {
            $byEmpDate[$e->empId][$e->emp_report_dates] = (float)$e->total_hrs;
        }
        $currentDate = new DateTime();
        $currentY = (int)$currentDate->format('Y');
        $currentM = (int)$currentDate->format('n');
        foreach ($monthYearPairs as $p) {
            $monthId = (int)$p['month'];
            $reportYear = (int)$p['year'];
            $startDatefromD = $reportYear . '-' . str_pad($monthId, 2, '0', STR_PAD_LEFT) . '-01';
            $lastDay = date('Y-m-t', mktime(0, 0, 0, $monthId, 1, $reportYear));
            $today = $currentDate->format('Y-m-d');
            $endDatefromD = ($reportYear == $currentY && $monthId == $currentM && $lastDay > $today) ? $today : $lastDay;
            $d = new DateTime($startDatefromD);
            $end = new DateTime($endDatefromD);
            while ($d <= $end) {
                $dw = (int)$d->format('w');
                if ($dw >= 1 && $dw <= 5) {
                    $dateStr = $d->format('Y-m-d');
                    foreach ($empIds as $eid) {
                        $hrs = isset($byEmpDate[$eid][$dateStr]) ? $byEmpDate[$eid][$dateStr] : 0;
                        // Only treat days with no entry as defaulters for monthly client batch logic
                        if ($hrs == 0) $defaulter[$eid][$monthId][$reportYear]++;
                    }
                }
                $d->modify('+1 day');
            }
        }
    }

    // 5. Quality (qualitylog: self_checker_name, department-based formula)
    foreach ($monthYearPairs as $p) {
        $m = (int)$p['month'];
        $y = (int)$p['year'];
        $this->db->select('self_checker_name AS empId, SUM(analyzer_num_of_errors + reviewer_num_of_errors) AS err_sum, COUNT(qty_empId) AS cnt');
        $this->db->from('quality_error_log');
        $this->db->where('YEAR(analyzer_report_date)', $y);
        $this->db->where('MONTH(analyzer_report_date)', $m);
        $this->db->where_in('self_checker_name', $empIds);
        $this->db->group_by('self_checker_name');
        $qRows = $this->db->get()->result();
        foreach ($empIds as $eid) {
            $quality[$eid][$m][$y] = 0;
        }
        foreach ($qRows as $row) {
            $eid = $row->empId;
            $cnt = (int)$row->cnt;
            $errSum = (float)$row->err_sum;
            $dept = isset($empDeptByEmpId[$eid]) ? $empDeptByEmpId[$eid] : '';
            if (in_array($dept, ['3D Visualization', 'Structural', 'Architectural', 'MEP'])) {
                $quality[$eid][$m][$y] = $cnt > 0 ? (100 - ($errSum / $cnt)) : 0;
            } else {
                $quality[$eid][$m][$y] = $errSum;
            }
        }
    }

    return ['production' => $production, 'perk' => $perk, 'lms' => $lms, 'defaulter' => $defaulter, 'quality' => $quality];
}

//func to get projects worked on by employee in a month
public function getProjectsWorkedOnMonthWise($empId, $monthId) {
    $currentYear = date('Y');
    
    if ($monthId == 12) {
        $previousMonthYear = $currentYear - 1;
    } else {
        $previousMonthYear = $currentYear;
    }
    
    if (!empty($empId)) {
        $query = $this->db->select('DISTINCT project_details.project_name')
            ->from('emp_record_details')
            ->join('project_details', 'emp_record_details.project_Id = project_details.project_Id', 'left')
            ->where('emp_record_details.empId', $empId)
            //->where('project_details.status', 'Process')
            ->where('emp_record_details.status', 'Approved')
            ->where('YEAR(emp_record_details.emp_report_dates)', $previousMonthYear)
            ->where('MONTH(emp_record_details.emp_report_dates)', $monthId)
            ->where("project_details.project_name NOT LIKE '%(General)%'")
            ->where("project_details.project_name != 'General'")
            ->order_by('project_details.project_name', 'ASC')
            ->get()
            ->result();
        
        if (!empty($query)) {
            $projectNames = array();
            foreach ($query as $row) {
                if (!empty($row->project_name)) {
                    $projectNames[] = $row->project_name;
                }
            }
            return implode(', ', $projectNames);
        }
    }
    
    return '--';
}

//func for quality accuracy - monthly - kpi  
/* public function qualitylog($empId, $monthId) {
    
        $currentYear = date('Y');
   
    if ($monthId == 12) {
        $previousMonthYear = $currentYear - 1;
    } else {
        $previousMonthYear = $currentYear;
    }   
    
    $deptQuery = $this->db->select('department')
        ->from('employee_details') 
        ->where('empId', $empId)
        ->get()
        ->row();

    if (!$deptQuery) {
        return 0;
    }

    $department = $deptQuery->department; //get dept name

    if(!empty($empId)):    
    
    $this->db->select('qty_empId, COUNT(qty_empId) as total_emp_count, , SUM(analyzer_num_of_errors + reviewer_num_of_errors) as total_sum_employee')
        ->from('quality_error_log')
        ->where('YEAR(quality_error_log.analyzer_report_date)', $previousMonthYear) 
        ->where('MONTH(quality_error_log.analyzer_report_date)', $monthId) 
        ->where('self_checker_name', $empId)
       // ->where('qty_empId', $empId)
        ->group_by('qty_empId');
   
    
    $query = $this->db->get();
   //echo $this->db->last_query().'<br>';
    // Fetch result
    $result = $query->row();
    if ($result && $result->total_emp_count > 0) {
        $accuracy = 100 - ($result->total_sum_employee / $result->total_emp_count);
        return $accuracy;
    } else {
        return "100";
    }
    
    endif;
}*/
    
    
    //func for quality accuracy - monthly - kpi  
public function qualitylog($empId, $monthId, $year = null) {
    
        $currentYear = date('Y');
   
    // If year is provided, use it; otherwise use default logic
    if ($year !== null && is_numeric($year)) {
        $previousMonthYear = (int)$year;
    } elseif ($monthId == 12) {
        $previousMonthYear = $currentYear - 1;
    } else {
        $previousMonthYear = $currentYear;
    }   
    
    $deptQuery = $this->db->select('department')
        ->from('employee_details') 
        ->where('empId', $empId)
        ->get()
        ->row();

    if (!$deptQuery) {
        return 0;
    }

    $department = $deptQuery->department; //get dept name

    if(!empty($empId)):    
    
    $this->db->select('qty_empId')
        ->from('quality_error_log')
        ->where('YEAR(quality_error_log.analyzer_report_date)', $previousMonthYear) 
        ->where('MONTH(quality_error_log.analyzer_report_date)', $monthId) 
        ->where('self_checker_name', $empId);

if (in_array($department, ['3D Visualization', 'Structural', 'Architectural', 'MEP'])) {
        $this->db->select('100 - (SUM(analyzer_num_of_errors + reviewer_num_of_errors) / COUNT(qty_empId)) AS total_errors');
    } else {
       
        $this->db->select('SUM(analyzer_num_of_errors + reviewer_num_of_errors) AS total_errors');
    }

    $this->db->group_by('qty_empId');
    $query = $this->db->get();
    
    // Fetch result
    $result = $query->result();
    // Return null if no data exists, otherwise return the calculated value
    return !empty($result) ? $result[0]->total_errors : null;
    
    endif;
}
    

//func for quality accuracy - consolidated - kpi      
public function qualityyearlylog($empId, $monthId) {
    
            $currentYear = date('Y');
    
   
    if ($monthId == 12) {
        $previousMonthYear = $currentYear - 1;
    } else {
        $previousMonthYear = $currentYear;
    } 

    $deptQuery = $this->db->select('department')
        ->from('employee_details')
        ->where('empId', $empId)
        ->get()
        ->row();

    if (!$deptQuery) {
        return 0;
    }

    if(!empty($empId)):    

        $this->db->select('qty_empId')
            ->from('quality_error_log')
            ->where('YEAR(quality_error_log.analyzer_report_date)', $previousMonthYear) 
            ->where('MONTH(quality_error_log.analyzer_report_date)', $monthId)
            //->where('self_checker_name', $empId);
            ->where('qty_empId', $empId);

        $this->db->select('100 - (SUM(analyzer_num_of_errors + reviewer_num_of_errors) / COUNT(qty_empId)) AS total_errors');

        $this->db->group_by('qty_empId');
        $query = $this->db->get();
        
        $result = $query->result();
        return !empty($result) ? $result[0]->total_errors : 0;
    
    endif;
}
    
    
//func for timesheet defaulter - kpi      
public function timesheetDefaulter($empId, $monthId, $year = null) {
    
$currentDate = new DateTime(); // today
$currentYear = (int) $currentDate->format('Y');
$currentMonth = (int) $currentDate->format('n');

// If year is provided, use it; otherwise use default logic
if ($year !== null && is_numeric($year)) {
    $reportYear = (int)$year;
} elseif ($monthId > $currentMonth) {
    // Month is from previous year
    $reportYear = $currentYear - 1;
} else {
    $reportYear = $currentYear;
}

// Calendar month: 1st to last day
$startDatefromD = $reportYear . '-' . str_pad($monthId, 2, '0', STR_PAD_LEFT) . '-01';
$lastDayOfMonth = date('Y-m-t', mktime(0, 0, 0, $monthId, 1, $reportYear));
// If reporting current month (not yet completed), only consider dates up to today
$today = $currentDate->format('Y-m-d');
$endDatefromD = ($reportYear == $currentYear && $monthId == $currentMonth && $lastDayOfMonth > $today)
    ? $today
    : $lastDayOfMonth;

    // Get all dates for this employee in the month with total hours per day
    $querydefaulter = $this->db->select('emp_report_dates, SUM(emp_time_hours) as total_time_hrs')
        ->from('emp_record_details')
        ->where('emp_report_dates >=', $startDatefromD)
        ->where('emp_report_dates <=', $endDatefromD)
        ->where('empId', $empId)
        ->group_by('emp_report_dates')
        ->get()
        ->result();

    $hoursByDate = [];
    foreach ($querydefaulter as $entry) {
        $hoursByDate[$entry->emp_report_dates] = (float) $entry->total_time_hrs;
    }

    // Working days in range (Mon–Fri only), only up to endDatefromD
    $defaulterCount = 0;
    $d = new DateTime($startDatefromD);
    $end = new DateTime($endDatefromD);
    while ($d <= $end) {
        $dw = (int) $d->format('w'); // 0=Sun, 6=Sat
        if ($dw >= 1 && $dw <= 5) {
            $dateStr = $d->format('Y-m-d');
            $hours = isset($hoursByDate[$dateStr]) ? $hoursByDate[$dateStr] : 0;
            // Defaulter = only days with no entry (0 hours)
            if ($hours == 0) {
                $defaulterCount++;
            }
        }
        $d->modify('+1 day');
    }

    return $defaulterCount;
}
    
    
//func for absent and le & eo - kpi    
public function perkabsent($emp_com_id, $monthId, $year = null) {
   
    $currentDate = new DateTime(); // today
$currentYear = (int) $currentDate->format('Y');
$currentMonth = (int) $currentDate->format('n');

// If year is provided, use it; otherwise use default logic
if ($year !== null && is_numeric($year)) {
    $reportYear = (int)$year;
} elseif ($monthId > $currentMonth) {
    // Month is from previous year
    $reportYear = $currentYear - 1;
} else {
    $reportYear = $currentYear;
}

// Calculate previous month of the selected month
if ($monthId == 1) {
    $previousMonth = 12;
    $previousMonthYear = $reportYear - 1;
} else {
    $previousMonth = $monthId - 1;
    $previousMonthYear = $reportYear;
}

// Build dates
$startDatefromD = $previousMonthYear . '-' . str_pad($previousMonth, 2, '0', STR_PAD_LEFT) . '-26';
$endDatefromD = $reportYear . '-' . str_pad($monthId, 2, '0', STR_PAD_LEFT) . '-25';

    
    $queryabsent = $this->db->select('perk_emp_absent_days, perk_emp_late_in, perk_emp_early_out,perk_emp_code')
        ->from('perk_monthly_data')
        ->where('from_date =', $startDatefromD)
        ->where('to_date =', $endDatefromD)        
        ->where('perk_emp_code', $emp_com_id)        
        ->get()
        ->result();
    
   // echo '<pre>'; print_r($queryabsent); exit;


    if (!empty($queryabsent)) {
        
        foreach ($queryabsent as $row) {
            
            
            $perkEmpAbsentDays = $row->perk_emp_absent_days;  
            $lateLogin = $row->perk_emp_late_in; 
            $earlyOut = $row->perk_emp_early_out; 
           
        }
        
        return $perkEmpAbsentDays . 'Perk@#' . $lateLogin . 'Perk@#' . $earlyOut;
        
    }

    // Fallback: try calendar month (1st to last day) in case perk_monthly_data uses calendar month range
    $monthStart = $reportYear . '-' . str_pad($monthId, 2, '0', STR_PAD_LEFT) . '-01';
    $monthEnd = date('Y-m-t', strtotime($monthStart));
    return $this->perkabsentByDateRange($emp_com_id, $monthStart, $monthEnd);
}   

/**
 * Fetch perk data (absent days, late in, early out) by date range.
 * For each report month we use exactly ONE period: the period whose to_date falls within [from_date, to_date].
 * Pick the single row with the latest to_date in that range to avoid double-counting when multiple
 * sections/months are shown (e.g. 26-25 vs calendar periods or duplicate rows).
 */
public function perkabsentByDateRange($emp_com_id, $from_date, $to_date) {
    if (empty($from_date) || empty($to_date)) {
        return '0Perk@#0Perk@#0';
    }
    $this->db->select('perk_emp_absent_days, perk_emp_late_in, perk_emp_early_out');
    $this->db->from('perk_monthly_data');
    $this->db->where('perk_emp_code', $emp_com_id);
    $this->db->where('to_date >=', $from_date);
    $this->db->where('to_date <=', $to_date);
    $this->db->order_by('to_date', 'DESC');
    $this->db->limit(1);
    $row = $this->db->get()->row();
    if (!empty($row)) {
        $perkEmpAbsentDays = (int) $row->perk_emp_absent_days;
        $lateLogin = (int) $row->perk_emp_late_in;
        $earlyOut = (int) $row->perk_emp_early_out;
        return $perkEmpAbsentDays . 'Perk@#' . $lateLogin . 'Perk@#' . $earlyOut;
    }
    return '0Perk@#0Perk@#0';
}
    
    
//func for above and beyond - kpi       
public function LMShours($empId, $monthId, $year = null) {

 $currentYear = date('Y');
    
   
    // If year is provided, use it; otherwise use default logic
    if ($year !== null && is_numeric($year)) {
        $previousMonthYear = (int)$year;
    } elseif ($monthId == 12) {
        $previousMonthYear = $currentYear - 1;
    } else {
        $previousMonthYear = $currentYear;
    } 
$querytime = $this->db->select('empId, SUM(watch_time) AS total_watch_time')
    ->from('lms_video_progress')                         
    ->where('YEAR(lms_video_progress.started_date)', $previousMonthYear) 
    ->where('MONTH(lms_video_progress.started_date)', $monthId) 
    ->where('MONTH(lms_video_progress.completed_date)', $monthId) 
    ->where('empId', $empId)
    ->group_by('empId')                                  
    ->get()->result();  
        
        
   
    if (!empty($querytime)) {
        
        foreach ($querytime as $timewatched) {
            return $timewatched->total_watch_time;
        }
    }
//echo $this->db->last_query(); 
    return 0;
}
    
   
//func for client report table    
public function ClientInformation($limit, $offset, $monthName = '', $search = '', $from_date = '', $to_date = '', $department = '') {
 
    $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];     
$mepManagers = ['146', '230', '149','455'];
$arcManagers = ['41', '394' , '270','47', '182', '71', '53', '155'];
$isMEPManager = in_array($empId, $mepManagers);
        $isARCManager = in_array($empId, $arcManagers);
    


    // Calculate production hours in subquery WITHOUT quality_error_log join to avoid multiplication
    // Build production hours subquery first
    $hoursSubquery = $this->db->select('
        emp_record_details.client_Id,
        emp_record_details.project_Id,
        SUM(emp_record_details.emp_time_hours) as total_hours
    ', false)
    ->from('emp_record_details')
    ->join('client_details', 'client_details.client_Id = emp_record_details.client_id', 'inner')
    ->join('project_details', 'project_details.project_id = emp_record_details.project_id', 'inner')
    ->where('client_details.status', 'Active')
    //->where('project_details.status', 'process')
    ->where("project_details.project_name NOT LIKE '%(General)%'");
    
    // Apply user-specific department filters to subquery
    // Use project_type from project_details if not empty, otherwise use department from client_details
    if (!empty($department)) {
        // If department filter is selected, use it (handle both single and multiple departments)
        if (is_array($department)) {
            $hoursSubquery->where_in("COALESCE(NULLIF(project_details.project_type, ''), client_details.department)", $department);
        } else {
            $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", $department);
        }
    } elseif ($userType == 'admin' || $empId == '140') {
        $primaryDepartments = function_exists('ts_primary_delivery_departments') ? ts_primary_delivery_departments() : array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');
        $hoursSubquery->where_in("COALESCE(NULLIF(project_details.project_type, ''), client_details.department)", $primaryDepartments);
    } elseif ($empId == '149'|| $isMEPManager ) {
        $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", 'MEP');
    } elseif ($empId == '47' || $isARCManager) {
        $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
    }
    
    // Apply date range filter to subquery
    if (!empty($from_date) && !empty($to_date)) {
        $hoursSubquery->where('emp_record_details.emp_report_dates >=', $from_date);
        $hoursSubquery->where('emp_record_details.emp_report_dates <=', $to_date);
    }
    // If no date range provided, show all data (no date filter applied)
    
    $hoursSubquery->group_by('emp_record_details.project_Id, emp_record_details.client_Id');
    $hoursSubquerySQL = $hoursSubquery->get_compiled_select();
    $this->db->reset_query();
    
    // Determine if we should pull invoice from project_invoice_monthly (month/year-wise) instead of static project_invoice_amt
    $useMonthlyInvoice = !empty($from_date) && !empty($to_date);
    if ($useMonthlyInvoice) {
        $report_year = (int) date('Y', strtotime($from_date));
        $report_month = (int) date('n', strtotime($from_date));
    }

    // Main query using the subquery - join quality_error_log separately to avoid multiplication
    // Use project_type from project_details if not empty, otherwise use department from client_details
    $selectInvoice = $useMonthlyInvoice
        ? 'COALESCE(MAX(pim.invoice_hours), 0) as project_invoice_amt,'
        : 'project_details.project_invoice_amt,';
    // Pre-aggregate invoice hours per project in a separate subquery to avoid double-counting
    // when joining with quality_error_log (which can have multiple rows per project).
    $invoiceSubSQL = null;
    if ($useMonthlyInvoiceCons) {
        $invoiceQuery = $this->db->select('project_Id, SUM(invoice_hours) AS total_invoice_hours', false)
            ->from('project_invoice_monthly');
        // Restrict invoice rows to months in our [fromKeyCons, toKeyCons] window
        $invoiceQuery->where("(invoice_year * 100 + invoice_month) >= {$fromKeyCons}", null, false);
        $invoiceQuery->where("(invoice_year * 100 + invoice_month) <= {$toKeyCons}", null, false);
        $invoiceQuery->group_by('project_Id');
        $invoiceSubSQL = $invoiceQuery->get_compiled_select();
        $this->db->reset_query();
    }

    $this->db->select('
        project_hours.client_Id,
        project_hours.project_Id,
        client_details.empId as clientpm,
        project_details.empId as projectpm,
        client_details.client_name,
        COALESCE(NULLIF(project_details.project_type, ""), client_details.department) as department,
        project_details.project_name,
        project_details.status,
        project_details.man_days,
        project_details.project_start_date,
        project_details.project_end_date,
        ' . $selectInvoice . '
        employee_details.name AS pm_name,
        MAX(quality_error_log.qty_project_Id) as qty_project_Id,
        SUM(COALESCE(quality_error_log.analyzer_num_of_errors, 0)) as analyzer_num_of_errors,
        SUM(COALESCE(quality_error_log.reviewer_num_of_errors, 0)) as reviewer_num_of_errors,
        MAX(quality_error_log.analyzer_report_date) as analyzer_report_date,
        project_hours.total_hours
    ');
    $this->db->from('(' . $hoursSubquerySQL . ') as project_hours');
    $this->db->join('client_details', 'client_details.client_Id = project_hours.client_Id', 'inner');
    $this->db->join('project_details', 'project_details.project_id = project_hours.project_Id', 'inner');
    if ($useMonthlyInvoice) {
        // Month-wise invoice: fetch only the invoice row for the selected report month/year
        $this->db->join(
            'project_invoice_monthly pim',
            'pim.project_Id = project_hours.project_Id AND pim.invoice_year = ' . $report_year . ' AND pim.invoice_month = ' . $report_month,
            'left'
        );
    }
    $this->db->join(
        'quality_error_log',
        $this->_client_report_quality_join_on('project_hours.project_Id', 'project_hours.client_Id', $from_date, $to_date),
        'left'
    );
    $this->db->join('employee_details', 'employee_details.empId = project_details.empId', 'left');

    // Apply filters to main query
    $this->db->where('client_details.status', 'Active');
    //$this->db->where('project_details.status', 'process');
    $this->db->where("client_details.client_name != ''");
    // Use project_type from project_details if not empty, otherwise use department from client_details
    $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IS NOT NULL");
    $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) !=", '');
    $this->db->where("client_details.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
    $this->db->where("project_details.project_name NOT LIKE '%(General)%'");
    
    // Apply user-specific department filters
    // Use project_type from project_details if not empty, otherwise use department from client_details
    if (!empty($department)) {
        // If department filter is selected, use it (handle both single and multiple departments)
        if (is_array($department)) {
            $this->db->where_in("COALESCE(NULLIF(project_details.project_type, ''), client_details.department)", $department);
        } else {
            $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", $department);
        }
    } elseif ($userType == 'admin' || $empId == '140') {
        $primaryDepartments = function_exists('ts_primary_delivery_departments') ? ts_primary_delivery_departments() : array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');
        $this->db->where_in("COALESCE(NULLIF(project_details.project_type, ''), client_details.department)", $primaryDepartments);
    } elseif ($empId == '149'|| $isMEPManager ) {
        $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", 'MEP');
    } elseif ($empId == '47' || $isARCManager) {
        $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
    }

    if (!empty($search)) {
        $searchTerms = array_filter(array_map('trim', explode(',', $search)));
        $this->db->group_start();
        foreach ($searchTerms as $term) {
            $this->db->or_where('client_details.client_name', $term)
                     ->or_where('project_details.project_name', $term)
                     ->or_where('pm_details.name', $term)
                     ->or_where('client_pm_details.name', $term);
        }
        $this->db->group_end();
    }

    // Group by client and project to ensure unique rows - quality error log fields are aggregated
    // Use project_type from project_details if not empty, otherwise use department from client_details
    $this->db->group_by('project_hours.project_Id, project_hours.client_Id, client_details.empId, project_details.empId, client_details.client_name, COALESCE(NULLIF(project_details.project_type, ""), client_details.department), project_details.project_name, project_details.status, project_details.man_days, project_details.project_start_date, project_details.project_end_date, employee_details.name');
    $this->db->order_by('project_hours.client_Id', 'DESC');
    $this->db->limit($limit, $offset);

    $query = $this->db->get();
//    echo $this->db->last_query();
    $projects = $query->result();

    // Calculate general hours for all projects based on "General" pattern (anywhere in name)
    if (!empty($projects)) {
        // Get all unique project names
        $projectNames = array_unique(array_map(function($p) {
            return $p->project_name;
        }, $projects));
        
        // Batch query: Find all general project IDs
        // Search for any project containing "General" anywhere in the name (case-insensitive)
        $this->db->select('project_id, project_name');
        $this->db->from('project_details');
        $this->db->like('project_name', 'General', 'both');
        $allGeneralProjects = $this->db->get()->result();
        
        // Create a mapping: base project name -> general project ID
        $generalProjectMap = [];
        foreach ($allGeneralProjects as $gp) {
            // Extract base project name (remove General and all its variations)
            // Handle patterns like: "Project - (General)", "Project (General)", "Project-General", "Project General", etc.
            $baseName = preg_replace('/\s*[-]?\s*\(?General\)?\s*/i', '', $gp->project_name);
            $baseName = preg_replace('/\s*General\s*/i', '', $baseName);
            $baseName = trim($baseName);
            
            // Check if this base name matches any of our project names
            foreach ($projectNames as $projectName) {
                $normalizedProjectName = trim($projectName);
                $normalizedBaseName = trim($baseName);
                
                // Case-insensitive exact match - the project name is the base name
                if (strcasecmp($normalizedProjectName, $normalizedBaseName) === 0) {
                    // Map base name to general project ID (use first match if multiple)
                    if (!isset($generalProjectMap[$projectName])) {
                        $generalProjectMap[$projectName] = $gp->project_id;
                    }
                    break; // Found a match, move to next general project
                }
                
                // Flexible match: Check if project name matches base name (handles variations like "Chris's" vs "Chris" or suffixes like "_HVAC")
                // Normalize by handling possessive forms, removing special characters, and converting to lowercase
                // Handle possessive: "Chris's" -> "chris", "Chris'" -> "chris"
                $normalizedProjectForMatch = strtolower($normalizedProjectName);
                $normalizedProjectForMatch = preg_replace('/[\'`]s?/', '', $normalizedProjectForMatch); // Remove apostrophes and possessive 's'
                $normalizedProjectForMatch = preg_replace('/[_\-\s]+/', '', $normalizedProjectForMatch); // Remove underscores, hyphens, spaces
                
                $normalizedBaseForMatch = strtolower($normalizedBaseName);
                $normalizedBaseForMatch = preg_replace('/[\'`]s?/', '', $normalizedBaseForMatch); // Remove apostrophes and possessive 's'
                $normalizedBaseForMatch = preg_replace('/[_\-\s]+/', '', $normalizedBaseForMatch); // Remove underscores, hyphens, spaces
                
                // Check if project name starts with base name (after normalization)
                // This handles cases like "Chris's Pancake and Dining_HVAC" matching "Chris Pancake and Dining"
                if (!empty($normalizedBaseForMatch) && strpos($normalizedProjectForMatch, $normalizedBaseForMatch) === 0) {
                    // Map base name to general project ID (use first match if multiple)
                    if (!isset($generalProjectMap[$projectName])) {
                        $generalProjectMap[$projectName] = $gp->project_id;
                    }
                    break; // Found a match, move to next general project
                }
                
                // Also check if base name is contained in project name (for cases with additional suffixes)
                // This ensures we catch variations like "Chris Pancake and Dining" matching "Chris's Pancake and Dining_HVAC"
                if (!empty($normalizedBaseForMatch) && !empty($normalizedProjectForMatch)) {
                    // Check if base is contained in project or vice versa
                    $baseContainedInProject = strpos($normalizedProjectForMatch, $normalizedBaseForMatch) !== false;
                    $projectContainedInBase = strpos($normalizedBaseForMatch, $normalizedProjectForMatch) !== false;
                    
                    if ($baseContainedInProject || $projectContainedInBase) {
                        // Ensure the match is significant (base name represents a substantial portion)
                        $baseLen = strlen($normalizedBaseForMatch);
                        $projectLen = strlen($normalizedProjectForMatch);
                        if ($baseLen >= 5 && $projectLen >= 5) { // Both should be at least 5 characters
                            // Check if there's significant overlap (at least 60% of shorter string)
                            $shorterLen = min($baseLen, $projectLen);
                            $longerLen = max($baseLen, $projectLen);
                            $matchRatio = $shorterLen / max($longerLen, 1);
                            if ($matchRatio >= 0.6) {
                                // Map base name to general project ID (use first match if multiple)
                                if (!isset($generalProjectMap[$projectName])) {
                                    $generalProjectMap[$projectName] = $gp->project_id;
                                }
                                break; // Found a match, move to next general project
                            }
                        }
                    }
                }
            }
        }
        
        // Calculate general hours per client + normalized base name combination
        // This ensures all projects with the same base name share the same general hours value
        $generalHoursMapByBaseName = []; // Key: clientId_normalizedBaseName, Value: general hours
        $projectToBaseNameMap = []; // Key: clientId_projectName, Value: normalizedBaseName
        
        // First, normalize all project names and group by client + normalized base name
        foreach ($projects as $p) {
            $baseProjectName = trim($p->project_name);
            $normalizedBaseName = strtolower($baseProjectName);
            $normalizedBaseName = preg_replace('/[\'`]s?/', '', $normalizedBaseName);
            $normalizedBaseName = preg_replace('/[_\-\s]+/', '', $normalizedBaseName);
            
            $key = $p->client_Id . '_' . $p->project_name;
            $baseKey = $p->client_Id . '_' . $normalizedBaseName;
            
            $projectToBaseNameMap[$key] = $baseKey;
            
            // If we haven't calculated general hours for this client + base name yet, do it now
            if (!isset($generalHoursMapByBaseName[$baseKey])) {
                $clientId = $p->client_Id;
                $baseName = trim($p->project_name);
                $generalHours = 0;
                
                // Check if we have a matching general project from the initial map
                $generalProjectId = null;
                if (isset($generalProjectMap[$baseName])) {
                    $generalProjectId = $generalProjectMap[$baseName];
                } else {
                    // Fallback: Try to find general project directly for this client-project combination
                    $normalizedProjectName = strtolower($baseName);
                    $normalizedProjectName = preg_replace('/[\'`]s?/', '', $normalizedProjectName);
                    $normalizedProjectName = preg_replace('/[_\-\s]+/', '', $normalizedProjectName);
                    
                    // Search for general projects that have hours for this client and match this project name
                    $this->db->distinct();
                    $this->db->select('pd.project_id, pd.project_name');
                    $this->db->from('project_details pd');
                    $this->db->join('emp_record_details erd', 'erd.project_id = pd.project_id', 'inner');
                    $this->db->where('erd.client_id', $clientId);
                    $this->db->like('pd.project_name', 'General', 'both');
                    
                    if (!empty($from_date) && !empty($to_date)) {
                        $this->db->where('erd.emp_report_dates >=', $from_date);
                        $this->db->where('erd.emp_report_dates <=', $to_date);
                    }
                    
                    $fallbackGeneralProjects = $this->db->get()->result();
                    
                    // Try to match with normalized names
                    foreach ($fallbackGeneralProjects as $fgp) {
                        $generalBaseName = preg_replace('/\s*[-]?\s*\(?General\)?\s*/i', '', $fgp->project_name);
                        $generalBaseName = preg_replace('/\s*General\s*/i', '', $generalBaseName);
                        $generalBaseName = trim($generalBaseName);
                        $normalizedGeneralBase = strtolower($generalBaseName);
                        $normalizedGeneralBase = preg_replace('/[\'`]s?/', '', $normalizedGeneralBase);
                        $normalizedGeneralBase = preg_replace('/[_\-\s]+/', '', $normalizedGeneralBase);
                        
                        if (!empty($normalizedGeneralBase) && !empty($normalizedProjectName)) {
                            $matches = false;
                            
                            if (strpos($normalizedProjectName, $normalizedGeneralBase) === 0 || 
                                strpos($normalizedGeneralBase, $normalizedProjectName) === 0) {
                                $matches = true;
                            } elseif (strlen($normalizedGeneralBase) >= 5 && strlen($normalizedProjectName) >= 5) {
                                if (strpos($normalizedProjectName, $normalizedGeneralBase) !== false || 
                                    strpos($normalizedGeneralBase, $normalizedProjectName) !== false) {
                                    $shorterLen = min(strlen($normalizedGeneralBase), strlen($normalizedProjectName));
                                    $longerLen = max(strlen($normalizedGeneralBase), strlen($normalizedProjectName));
                                    if ($shorterLen > 0 && ($shorterLen / $longerLen) >= 0.6) {
                                        $matches = true;
                                    }
                                }
                            }
                            
                            if ($matches) {
                                $generalProjectId = $fgp->project_id;
                                break;
                            }
                        }
                    }
                }
                
                // Calculate general hours for this client + base name combination
                if ($generalProjectId) {
                    $this->db->select('SUM(emp_time_hours) as total_general_hours');
                    $this->db->from('emp_record_details');
                    $this->db->where('project_id', $generalProjectId);
                    $this->db->where('client_id', $clientId);
                    
                    if (!empty($from_date) && !empty($to_date)) {
                        $this->db->where('emp_report_dates >=', $from_date);
                        $this->db->where('emp_report_dates <=', $to_date);
                    }
                    
                    $generalHoursResult = $this->db->get()->row();
                    $generalHours = $generalHoursResult ? (float)$generalHoursResult->total_general_hours : 0;
                }
                
                $generalHoursMapByBaseName[$baseKey] = $generalHours;
            }
        }
        
        // Append general hours to each project - all projects with same base name get same value
        // But only show on the first project with each client + base name combination
        $shownGeneralHoursKeys = []; // Track client + base project name combinations that have shown general hours
        foreach ($projects as $i => &$project) {
            $key = $project->client_Id . '_' . $project->project_name;
            $baseKey = isset($projectToBaseNameMap[$key]) ? $projectToBaseNameMap[$key] : null;
            $gen_hours = ($baseKey && isset($generalHoursMapByBaseName[$baseKey])) ? $generalHoursMapByBaseName[$baseKey] : 0;
            
            // Only show general hours on the first project with this client + base name combination
            if ($gen_hours > 0 && $baseKey && !isset($shownGeneralHoursKeys[$baseKey])) {
                $project->general_hours = $gen_hours;
                $shownGeneralHoursKeys[$baseKey] = true;
            } else {
                $project->general_hours = 0;
            }

            // Fetch last date from emp_record_details for this project-client combination
            $this->db->select('MAX(emp_report_dates) as last_work_date');
            $this->db->from('emp_record_details');
            $this->db->where('project_id', $project->project_Id);
            $this->db->where('client_id', $project->client_Id);

            // Apply date range filter if provided
            if (!empty($from_date) && !empty($to_date)) {
                $this->db->where('emp_report_dates >=', $from_date);
                $this->db->where('emp_report_dates <=', $to_date);
            }

            $lastDateResult = $this->db->get()->row();
            $project->last_work_date = $lastDateResult && !empty($lastDateResult->last_work_date) ? $lastDateResult->last_work_date : null;
        }
        
        // Find general projects that have hours but no matching regular project
        // This ensures we show general hours even if the project itself doesn't have regular hours
        $existingClientProjectKeys = [];
        foreach ($projects as $p) {
            $key = $p->client_Id . '_' . $p->project_name;
            $existingClientProjectKeys[$key] = true;
        }
        
        // Find all general projects with hours for clients that match our filters
        $this->db->distinct();
        $this->db->select('
            erd.client_id,
            erd.project_id,
            pd.project_name,
            cd.client_name,
            cd.empId as clientpm,
            COALESCE(NULLIF(pd.project_type, ""), cd.department) as department,
            pd.empId as projectpm,
            pd.status,
            pd.man_days,
            pd.project_start_date,
            pd.project_end_date
        ');
        $this->db->from('emp_record_details erd');
        $this->db->join('project_details pd', 'pd.project_id = erd.project_id', 'inner');
        $this->db->join('client_details cd', 'cd.client_Id = erd.client_id', 'inner');
        $this->db->where('cd.status', 'Active');
        $this->db->where("cd.client_name != ''");
        $this->db->where("cd.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
        $this->db->like('pd.project_name', 'General', 'both');
        
        // Apply date range filter
        if (!empty($from_date) && !empty($to_date)) {
            $this->db->where('erd.emp_report_dates >=', $from_date);
            $this->db->where('erd.emp_report_dates <=', $to_date);
        }
        
        // Apply user-specific department filters
        if (!empty($department)) {
            if (is_array($department)) {
                $this->db->where_in("COALESCE(NULLIF(pd.project_type, ''), cd.department)", $department);
            } else {
                $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) =", $department);
            }
        } elseif ($userType == 'admin' || $empId == '140') {
            $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) IN", "('MEP', '3D Visualization', 'Architectural', 'Structural', '2D Auto CAD')", false);
        } elseif ($empId == '149'|| $isMEPManager ) {
            $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) =", 'MEP');
        } elseif ($empId == '47' || $isARCManager) {
            $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) IN", "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
        }
        
        $generalProjectsWithHours = $this->db->get()->result();
        
        // For each general project, extract base name and check if regular project exists
        foreach ($generalProjectsWithHours as $gp) {
            // Extract base project name from general project name
            $generalProjectName = $gp->project_name;
            $baseName = preg_replace('/\s*[-]?\s*\(?General\)?\s*/i', '', $generalProjectName);
            $baseName = preg_replace('/\s*General\s*/i', '', $baseName);
            $baseName = trim($baseName);
            
            if (empty($baseName)) {
                continue; // Skip if no base name extracted
            }
            
            // Check if a regular project with this name already exists for this client
            $key = $gp->client_id . '_' . $baseName;
            if (isset($existingClientProjectKeys[$key])) {
                continue; // Regular project already exists, skip
            }
            
            // Check if we can match this general project to any existing project using flexible matching
            $matched = false;
            foreach ($existingClientProjectKeys as $existingKey => $val) {
                list($existingClientId, $existingProjectName) = explode('_', $existingKey, 2);
                
                // Only check if same client
                if ($existingClientId != $gp->client_id) {
                    continue;
                }
                
                // Normalize names for comparison
                $normalizedBaseName = strtolower($baseName);
                $normalizedBaseName = preg_replace('/[\'`]s?/', '', $normalizedBaseName);
                $normalizedBaseName = preg_replace('/[_\-\s]+/', '', $normalizedBaseName);
                
                $normalizedExistingName = strtolower($existingProjectName);
                $normalizedExistingName = preg_replace('/[\'`]s?/', '', $normalizedExistingName);
                $normalizedExistingName = preg_replace('/[_\-\s]+/', '', $normalizedExistingName);
                
                // Check if they match
                if (!empty($normalizedBaseName) && !empty($normalizedExistingName)) {
                    if (strpos($normalizedExistingName, $normalizedBaseName) === 0 || 
                        strpos($normalizedBaseName, $normalizedExistingName) === 0) {
                        $matched = true;
                        break;
                    } elseif (strlen($normalizedBaseName) >= 5 && strlen($normalizedExistingName) >= 5) {
                        if (strpos($normalizedExistingName, $normalizedBaseName) !== false || 
                            strpos($normalizedBaseName, $normalizedExistingName) !== false) {
                            $shorterLen = min(strlen($normalizedBaseName), strlen($normalizedExistingName));
                            $longerLen = max(strlen($normalizedBaseName), strlen($normalizedExistingName));
                            if ($shorterLen > 0 && ($shorterLen / $longerLen) >= 0.6) {
                                $matched = true;
                                break;
                            }
                        }
                    }
                }
            }
            
            if ($matched) {
                continue; // Matched to existing project, skip
            }
            
            // Calculate general hours for this general project
            $this->db->select('SUM(emp_time_hours) as total_general_hours');
            $this->db->from('emp_record_details');
            $this->db->where('project_id', $gp->project_id);
            $this->db->where('client_id', $gp->client_id);
            
            // Apply date range filter
            if (!empty($from_date) && !empty($to_date)) {
                $this->db->where('emp_report_dates >=', $from_date);
                $this->db->where('emp_report_dates <=', $to_date);
            }
            
            $generalHoursResult = $this->db->get()->row();
            $generalHours = $generalHoursResult ? (float)$generalHoursResult->total_general_hours : 0;
            
            // Only add if there are actual general hours
            if ($generalHours > 0) {
                // Get PM name
                $this->db->select('name');
                $this->db->from('employee_details');
                $this->db->where('empId', $gp->projectpm);
                $pmResult = $this->db->get()->row();
                $pmName = $pmResult ? $pmResult->name : '';
                
                // Create a new project object with 0 production hours but general hours
                $newProject = new stdClass();
                $newProject->client_Id = $gp->client_id;
                $newProject->project_Id = $gp->project_id;
                $newProject->client_name = $gp->client_name;
                $newProject->project_name = $baseName; // Use base name, not general project name
                $newProject->department = $gp->department;
                $newProject->clientpm = $gp->clientpm;
                $newProject->projectpm = $gp->projectpm;
                $newProject->pm_name = $pmName;
                $newProject->status = $gp->status;
                $newProject->man_days = $gp->man_days;
                $newProject->project_start_date = $gp->project_start_date;
                $newProject->project_end_date = $gp->project_end_date;
                $newProject->total_hours = 0; // No production hours
                
                // Check if a project with the same client and base name already has general hours
                $normalizedBaseNameForNew = strtolower($baseName);
                $normalizedBaseNameForNew = preg_replace('/[\'`]s?/', '', $normalizedBaseNameForNew);
                $normalizedBaseNameForNew = preg_replace('/[_\-\s]+/', '', $normalizedBaseNameForNew);
                $generalHoursKeyForNew = $gp->client_id . '_' . $normalizedBaseNameForNew;
                
                // Only show general hours if this is the first project with this client + base name combination
                $shouldShowGeneralHours = !isset($shownGeneralHoursKeys[$generalHoursKeyForNew]);
                $newProject->general_hours = $shouldShowGeneralHours ? $generalHours : 0; // Only show if first with this client+base name
                
                // Track that we've shown general hours for this client + base name combination
                if ($shouldShowGeneralHours) {
                    $shownGeneralHoursKeys[$generalHoursKeyForNew] = true;
                }
                
                $newProject->qty_project_Id = null;
                $newProject->analyzer_num_of_errors = 0;
                $newProject->reviewer_num_of_errors = 0;
                $newProject->analyzer_report_date = null;
                
                // Get last work date from general project
                $this->db->select('MAX(emp_report_dates) as last_work_date');
                $this->db->from('emp_record_details');
                $this->db->where('project_id', $gp->project_id);
                $this->db->where('client_id', $gp->client_id);
                
                if (!empty($from_date) && !empty($to_date)) {
                    $this->db->where('emp_report_dates >=', $from_date);
                    $this->db->where('emp_report_dates <=', $to_date);
                }
                
                $lastDateResult = $this->db->get()->row();
                $newProject->last_work_date = $lastDateResult && !empty($lastDateResult->last_work_date) ? $lastDateResult->last_work_date : null;
                
                // Add to projects array
                $projects[] = $newProject;
            }
        }
    }

    return $projects;
}
    
    
/**
 * Consolidated client report: resolve effective date range (index-friendly bounds).
 */
private function _client_cons_date_range($from_date, $to_date)
{
    if (!empty($from_date) && !empty($to_date)) {
        return array(
            'from' => $from_date,
            'to' => $to_date,
            'from_key' => (int)date('Ym', strtotime($from_date)),
            'to_key' => (int)date('Ym', strtotime($to_date)),
        );
    }
    $currentDate = new DateTime();
    $currentYear = (int)$currentDate->format('Y');
    $currentMonth = (int)$currentDate->format('n');
    if ($currentMonth == 1) {
        $queryYear = $currentYear - 1;
        $endMonth = 12;
    } else {
        $queryYear = $currentYear;
        $endMonth = $currentMonth - 1;
    }
    return array(
        'from' => sprintf('%04d-01-01', $queryYear),
        'to' => date('Y-m-t', mktime(0, 0, 0, $endMonth, 1, $queryYear)),
        'from_key' => (int)($queryYear . '01'),
        'to_key' => (int)($queryYear . str_pad($endMonth, 2, '0', STR_PAD_LEFT)),
    );
}

private function _client_cons_apply_department_scope($builder, $department, $userType, $empId, $isMEPManager, $isARCManager)
{
    $deptExpr = "COALESCE(NULLIF(project_details.project_type, ''), client_details.department)";
    if (!empty($department)) {
        if (is_array($department)) {
            $builder->where_in($deptExpr, $department);
        } else {
            $builder->where($deptExpr . ' =', $department);
        }
    } elseif ($userType == 'admin' || $empId == '140') {
        $builder->where($deptExpr . ' IN', "('MEP', '3D Visualization', 'Architectural', 'Structural', '2D Auto CAD')", false);
    } elseif ($empId == '149' || $isMEPManager) {
        $builder->where($deptExpr . ' =', 'MEP');
    } elseif ($empId == '47' || $isARCManager) {
        $builder->where($deptExpr . ' IN', "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
    }
}

private function _client_cons_hours_subquery_sql($department, $range)
{
    $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];
    $mepManagers = array('146', '230', '149', '455');
    $arcManagers = array('41', '394', '270', '47', '182', '71', '53', '155');
    $isMEPManager = in_array($empId, $mepManagers);
    $isARCManager = in_array($empId, $arcManagers);

    $hoursSubquery = $this->db->select('
        emp_record_details.client_Id,
        emp_record_details.project_Id,
        SUM(emp_record_details.emp_time_hours) AS total_hours
    ', false)
        ->from('emp_record_details')
        ->join('client_details', 'client_details.client_Id = emp_record_details.client_id', 'inner')
        ->join('project_details', 'project_details.project_id = emp_record_details.project_id', 'inner')
        ->where('client_details.status', 'Active')
        ->where("project_details.project_name NOT LIKE '%(General)%'")
        ->where('emp_record_details.emp_report_dates >=', $range['from'])
        ->where('emp_record_details.emp_report_dates <=', $range['to']);

    $this->_client_cons_apply_department_scope($hoursSubquery, $department, $userType, $empId, $isMEPManager, $isARCManager);
    $hoursSubquery->group_by('emp_record_details.project_Id, emp_record_details.client_Id');
    $sql = $hoursSubquery->get_compiled_select();
    $this->db->reset_query();
    return $sql;
}

/**
 * LEFT JOIN ON for quality_error_log, scoped to selected month/date range.
 * When $reportMonthExpr is set (month-wise rows), match that YYYY-MM; otherwise use from/to dates.
 */
private function _client_report_quality_join_on($projectIdExpr, $clientIdExpr, $from_date = '', $to_date = '', $reportMonthExpr = null)
{
    $on = 'quality_error_log.qty_project_Id = ' . $projectIdExpr
        . ' AND quality_error_log.qty_client_Id = ' . $clientIdExpr;
    if (!empty($reportMonthExpr)) {
        $on .= ' AND DATE_FORMAT(quality_error_log.analyzer_report_date, \'%Y-%m\') = ' . $reportMonthExpr;
    } elseif (!empty($from_date) && !empty($to_date)) {
        $on .= ' AND quality_error_log.analyzer_report_date >= ' . $this->db->escape($from_date)
            . ' AND quality_error_log.analyzer_report_date <= ' . $this->db->escape($to_date);
    }
    return $on;
}

private function _client_cons_quality_subquery_sql($from_date = '', $to_date = '')
{
    $q = $this->db->select('
        qty_client_Id,
        qty_project_Id,
        MAX(analyzer_report_date) AS analyzer_report_date,
        SUM(COALESCE(analyzer_num_of_errors, 0)) AS analyzer_num_of_errors,
        SUM(COALESCE(reviewer_num_of_errors, 0)) AS reviewer_num_of_errors
    ', false)
        ->from('quality_error_log');
    if (!empty($from_date) && !empty($to_date)) {
        $q->where('analyzer_report_date >=', $from_date)
            ->where('analyzer_report_date <=', $to_date);
    }
    $q = $q->group_by('qty_project_Id, qty_client_Id')
        ->get_compiled_select();
    $this->db->reset_query();
    return $q;
}

private function _client_cons_invoice_subquery_sql($fromKey, $toKey)
{
    if (empty($fromKey) || empty($toKey)) {
        return null;
    }
    return 'SELECT project_Id, COALESCE(SUM(invoice_hours), 0) AS project_invoice_amt
        FROM project_invoice_monthly
        WHERE (invoice_year * 100 + invoice_month) >= ' . (int)$fromKey . '
          AND (invoice_year * 100 + invoice_month) <= ' . (int)$toKey . '
        GROUP BY project_Id';
}

private function _client_cons_apply_outer_filters($department, $search)
{
    $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];
    $mepManagers = array('146', '230', '149', '455');
    $arcManagers = array('41', '394', '270', '47', '182', '71', '53', '155');
    $isMEPManager = in_array($empId, $mepManagers);
    $isARCManager = in_array($empId, $arcManagers);

    $deptExpr = "COALESCE(NULLIF(project_details.project_type, ''), client_details.department)";
    $this->db->where('client_details.status', 'Active');
    $this->db->where($deptExpr . ' IS NOT NULL', null, false);
    $this->db->where($deptExpr . ' !=', '');
    $this->db->where("client_details.client_name != ''");
    $this->db->where("client_details.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
    $this->db->where("project_details.project_name NOT LIKE '%(General)%'");
    $this->_client_cons_apply_department_scope($this->db, $department, $userType, $empId, $isMEPManager, $isARCManager);

    if (!empty($search)) {
        $searchTerms = array_filter(array_map('trim', explode(',', $search)));
        $this->db->group_start();
        foreach ($searchTerms as $term) {
            $this->db->or_like('client_details.client_name', $term, 'both')
                ->or_like('project_details.project_name', $term, 'both')
                ->or_like('pm_details.name', $term, 'both')
                ->or_like('client_pm_details.name', $term, 'both');
        }
        $this->db->group_end();
    }
}

private function _client_cons_normalize_name_key($name)
{
    $name = strtolower(trim((string)$name));
    $name = preg_replace('/[\'`]s?/', '', $name);
    return preg_replace('/[_\-\s]+/', '', $name);
}

private function _client_cons_general_base_name($projectName)
{
    $base = preg_replace('/\s*[-]?\s*\(?General\)?\s*/i', '', (string)$projectName);
    $base = preg_replace('/\s*General\s*/i', '', $base);
    return trim($base);
}

private function _client_cons_names_match($projectName, $generalBaseName)
{
    if (strcasecmp(trim($projectName), trim($generalBaseName)) === 0) {
        return true;
    }
    $pn = $this->_client_cons_normalize_name_key($projectName);
    $gb = $this->_client_cons_normalize_name_key($generalBaseName);
    if ($pn === '' || $gb === '') {
        return false;
    }
    if (strpos($pn, $gb) === 0 || strpos($gb, $pn) === 0) {
        return true;
    }
    if (strlen($pn) >= 5 && strlen($gb) >= 5) {
        if (strpos($pn, $gb) !== false || strpos($gb, $pn) !== false) {
            $shorter = min(strlen($pn), strlen($gb));
            $longer = max(strlen($pn), strlen($gb));
            return ($shorter / max($longer, 1)) >= 0.6;
        }
    }
    return false;
}

/**
 * Map production project names to general project IDs (scoped to page clients).
 */
private function _client_cons_build_general_project_map(array $clientIds, array $projectNames, $from_date, $to_date)
{
    $map = array();
    $projectNames = array_values(array_unique(array_filter(array_map('trim', $projectNames))));
    $clientIds = array_values(array_unique(array_filter(array_map('intval', $clientIds))));
    if (empty($projectNames) || empty($clientIds)) {
        return $map;
    }

    $range = $this->_client_cons_date_range($from_date, $to_date);
    $this->db->distinct();
    $this->db->select('pd.project_id, pd.project_name', false);
    $this->db->from('project_details pd');
    $this->db->join('emp_record_details erd', 'erd.project_id = pd.project_id', 'inner');
    $this->db->where_in('erd.client_id', $clientIds);
    $this->db->like('pd.project_name', 'General', 'both');
    $this->db->where('erd.emp_report_dates >=', $range['from']);
    $this->db->where('erd.emp_report_dates <=', $range['to']);
    $generalRows = $this->db->get()->result();
    $this->db->reset_query();

    $unmatched = array_flip($projectNames);
    foreach ($generalRows as $gp) {
        $baseName = $this->_client_cons_general_base_name($gp->project_name);
        if ($baseName === '') {
            continue;
        }
        foreach (array_keys($unmatched) as $projectName) {
            if ($this->_client_cons_names_match($projectName, $baseName)) {
                if (!isset($map[$projectName])) {
                    $map[$projectName] = (int)$gp->project_id;
                }
                unset($unmatched[$projectName]);
            }
        }
        if (empty($unmatched)) {
            break;
        }
    }
    return $map;
}

//func for consolidated client report table     
public function ClientInformationConsolidated($limit, $offset, $search = '', $from_date = '', $to_date = '', $department = '') {
    $range = $this->_client_cons_date_range($from_date, $to_date);
    $hoursSubquerySQL = $this->_client_cons_hours_subquery_sql($department, $range);
    $qualitySubquerySQL = $this->_client_cons_quality_subquery_sql($range['from'], $range['to']);
    $invoiceSubquerySQL = $this->_client_cons_invoice_subquery_sql($range['from_key'], $range['to_key']);

    $invoiceSelect = $invoiceSubquerySQL
        ? 'COALESCE(inv_sum.project_invoice_amt, 0) AS project_invoice_amt,'
        : 'project_details.project_invoice_amt,';

    $this->db->select('
        project_hours.client_Id,
        project_hours.project_Id,
        client_details.empId AS clientpm,
        project_details.empId AS projectpm,
        client_details.client_name,
        COALESCE(NULLIF(project_details.project_type, ""), client_details.department) AS department,
        project_details.project_name,
        project_details.status,
        project_details.man_days,
        project_details.project_start_date,
        project_details.project_end_date,
        ' . $invoiceSelect . '
        pm_details.name AS pm_name,
        client_pm_details.name AS client_pm_name,
        qe_agg.qty_project_Id,
        COALESCE(qe_agg.analyzer_num_of_errors, 0) AS analyzer_num_of_errors,
        COALESCE(qe_agg.reviewer_num_of_errors, 0) AS reviewer_num_of_errors,
        qe_agg.analyzer_report_date,
        project_hours.total_hours
    ', false);
    $this->db->from('(' . $hoursSubquerySQL . ') AS project_hours');
    $this->db->join('client_details', 'client_details.client_Id = project_hours.client_Id', 'inner');
    $this->db->join('project_details', 'project_details.project_id = project_hours.project_Id', 'inner');
    $this->db->join('(' . $qualitySubquerySQL . ') AS qe_agg', 'qe_agg.qty_project_Id = project_hours.project_Id AND qe_agg.qty_client_Id = project_hours.client_Id', 'left', false);
    if ($invoiceSubquerySQL) {
        $this->db->join('(' . $invoiceSubquerySQL . ') AS inv_sum', 'inv_sum.project_Id = project_hours.project_Id', 'left', false);
    }
    $this->db->join('employee_details pm_details', 'pm_details.empId = project_details.empId', 'left');
    $this->db->join('employee_details client_pm_details', 'client_pm_details.empId = client_details.empId', 'left');

    $this->_client_cons_apply_outer_filters($department, $search);
    $this->db->order_by('client_details.client_name', 'ASC');
    $this->db->order_by('project_details.project_name', 'ASC');
    if ($limit > 0) {
        $this->db->limit((int) $limit, max(0, (int) $offset));
    }
    $projects = $this->db->get()->result();

    if (!empty($projects)) {
        $projectNames = array();
        $clientIds = array();
        foreach ($projects as $p) {
            $projectNames[] = $p->project_name;
            $clientIds[] = $p->client_Id;
        }
        $lastWorkDateMap = $this->_client_report_batch_last_work_dates(
            $clientIds,
            $range['from'],
            $range['to'],
            false
        );
        $generalProjectMap = $this->_client_cons_build_general_project_map($clientIds, $projectNames, $from_date, $to_date);

        $generalHoursMap = array();
        $clientProjectMap = array();
        foreach ($projects as $p) {
            $key = $p->client_Id . '_' . $p->project_name;
            if (!isset($clientProjectMap[$key])) {
                $clientProjectMap[$key] = $p;
            }
        }

        $generalHourPairs = array();
        foreach ($clientProjectMap as $project) {
            $baseName = $project->project_name;
            if (isset($generalProjectMap[$baseName])) {
                $generalHourPairs[] = array(
                    'client_id' => (int)$project->client_Id,
                    'project_id' => (int)$generalProjectMap[$baseName],
                );
            }
        }

        if (!empty($generalHourPairs)) {
            $generalHoursByPair = $this->_client_report_batch_general_hours(
                $generalHourPairs,
                $range['from'],
                $range['to']
            );

            foreach ($clientProjectMap as $key => $project) {
                $baseName = $project->project_name;
                if (!isset($generalProjectMap[$baseName])) {
                    $generalHoursMap[$key] = 0;
                    continue;
                }
                $pairKey = (int)$project->client_Id . '_' . (int)$generalProjectMap[$baseName];
                $generalHoursMap[$key] = isset($generalHoursByPair[$pairKey]) ? $generalHoursByPair[$pairKey] : 0;
            }
        }

        foreach ($projects as &$project) {
            $key = $project->client_Id . '_' . $project->project_name;
            $project->general_hours = isset($generalHoursMap[$key]) ? $generalHoursMap[$key] : 0;
            $lastKey = (int)$project->client_Id . '_' . (int)$project->project_Id;
            $project->last_work_date = isset($lastWorkDateMap[$lastKey]) ? $lastWorkDateMap[$lastKey] : null;
        }
        unset($project);
    }

    return $projects;
}
     

//count for monthly client report table    
public function get_total_clients($monthName = '', $search = '', $from_date = '', $to_date = '', $department = '') {
        $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];     
$mepManagers = ['146', '230', '149','455'];
$arcManagers = ['41', '394' , '270','47', '182', '71', '53', '155'];
$isMEPManager = in_array($empId, $mepManagers);
        $isARCManager = in_array($empId, $arcManagers);
    
    
    $this->db->select('COUNT(DISTINCT project_details.project_Id) as total_projects');
    $this->db->from('emp_record_details');
    $this->db->join('client_details', 'client_details.client_Id = emp_record_details.client_id', 'inner');
    $this->db->join('project_details', 'project_details.project_id = emp_record_details.project_id', 'inner');
    $this->db->join('quality_error_log', 'quality_error_log.qty_project_Id = project_details.project_id AND quality_error_log.qty_client_Id = emp_record_details.client_Id', 'left');
    $this->db->join('employee_details', 'employee_details.empId = project_details.empId', 'left');

    $this->db->where('client_details.status', 'Active');
    //$this->db->where('project_details.status', 'process');
    $this->db->where("client_details.client_name != ''");
    // Use project_type from project_details if not empty, otherwise use department from client_details
    $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IS NOT NULL");
    $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) !=", '');
    $this->db->where("client_details.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
    $this->db->where("project_details.project_name NOT LIKE '%(General)%'");


    // Apply user-specific department filters
    // Use project_type from project_details if not empty, otherwise use department from client_details
    if (!empty($department)) {
        // If department filter is selected, use it (handle both single and multiple departments)
        if (is_array($department)) {
            $this->db->where_in("COALESCE(NULLIF(project_details.project_type, ''), client_details.department)", $department);
        } else {
            $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", $department);
        }
    } elseif ($userType == 'admin' || $empId == '140') {
        // Default non-admin access
        $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('MEP', '3D Visualization', 'Architectural', 'Structural', '2D Auto CAD')", false);
    } elseif ($empId == '149'|| $isMEPManager ) {
        
        $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", 'MEP');
    } elseif ($empId == '47' || $isARCManager) {
        
        $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
    }  

// Handle date range - if not provided, show all data
// Hours are filtered here; quality_error_log is date-scoped in the main client-report joins.
if (!empty($from_date) && !empty($to_date)) {
    // Date range filter for employee record details
    $this->db->where('emp_record_details.emp_report_dates >=', $from_date);
    $this->db->where('emp_record_details.emp_report_dates <=', $to_date);
}
// If no date range provided, show all data (no date filter applied)

    if (!empty($search)) {
        $searchTerms = array_filter(array_map('trim', explode(',', $search)));
        $this->db->group_start();
        foreach ($searchTerms as $term) {
            $this->db->or_where('client_details.client_name', $term)
                     ->or_where('project_details.project_name', $term)
                     ->or_where('employee_details.name', $term);
        }
        $this->db->group_end();
    }

    $query = $this->db->get();
     
    return $query->row()->total_projects;
}
         

//count for consolidated client report table        
public function get_total_clients_cons($search = '', $from_date = '', $to_date = '', $department = '') {
    $range = $this->_client_cons_date_range($from_date, $to_date);
    $hoursSubquerySQL = $this->_client_cons_hours_subquery_sql($department, $range);

    $this->db->select('COUNT(*) AS total_projects', false);
    $this->db->from('(' . $hoursSubquerySQL . ') AS project_hours');
    $this->db->join('client_details', 'client_details.client_Id = project_hours.client_Id', 'inner');
    $this->db->join('project_details', 'project_details.project_id = project_hours.project_Id', 'inner');
    $this->db->join('employee_details pm_details', 'pm_details.empId = project_details.empId', 'left');
    $this->db->join('employee_details client_pm_details', 'client_pm_details.empId = client_details.empId', 'left');

    $this->_client_cons_apply_outer_filters($department, $search);

    $row = $this->db->get()->row();
    return $row ? (int)$row->total_projects : 0;
}    
    
     
/**
 * Batch MAX(emp_report_dates) per client/project (optional per month) — avoids N+1 in client report.
 *
 * @return array<string,string> keys clientId_projectId or clientId_projectId_Y-m
 */
private function _client_report_batch_last_work_dates(array $clientIds, $from_date, $to_date, $by_month = false)
{
    $clientIds = array_values(array_unique(array_filter(array_map('intval', $clientIds))));
    if (empty($clientIds)) {
        return array();
    }
    $this->db->select('client_id, project_id', false);
    if ($by_month) {
        $this->db->select("DATE_FORMAT(emp_report_dates, '%Y-%m') as report_month", false);
    }
    $this->db->select('MAX(emp_report_dates) as last_work_date', false);
    $this->db->from('emp_record_details');
    $this->db->where_in('client_id', $clientIds);
    if (!empty($from_date) && !empty($to_date)) {
        $this->db->where('emp_report_dates >=', $from_date);
        $this->db->where('emp_report_dates <=', $to_date);
    }
    if ($by_month) {
        $this->db->group_by('client_id, project_id, report_month');
    } else {
        $this->db->group_by('client_id, project_id');
    }
    $map = array();
    foreach ($this->db->get()->result() as $row) {
        $key = (int)$row->client_id . '_' . (int)$row->project_id;
        if ($by_month && !empty($row->report_month)) {
            $key .= '_' . $row->report_month;
        }
        $map[$key] = $row->last_work_date;
    }
    $this->db->reset_query();
    return $map;
}

/**
 * Batch SUM general-project hours per client/project — avoids N+1 in client report.
 *
 * @return array<string,float> keys clientId_projectId
 */
private function _client_report_batch_general_hours(array $pairs, $from_date, $to_date)
{
    if (empty($pairs)) {
        return array();
    }
    $clientIds = array();
    $projectIds = array();
    foreach ($pairs as $pair) {
        $clientIds[(int)$pair['client_id']] = true;
        $projectIds[(int)$pair['project_id']] = true;
    }
    $clientIds = array_keys($clientIds);
    $projectIds = array_keys($projectIds);
    if (empty($clientIds) || empty($projectIds)) {
        return array();
    }
    $this->db->select('client_id, project_id, SUM(emp_time_hours) as total_general_hours', false);
    $this->db->from('emp_record_details');
    $this->db->where_in('client_id', $clientIds);
    $this->db->where_in('project_id', $projectIds);
    if (!empty($from_date) && !empty($to_date)) {
        $this->db->where('emp_report_dates >=', $from_date);
        $this->db->where('emp_report_dates <=', $to_date);
    }
    $this->db->group_by('client_id, project_id');
    $wanted = array();
    foreach ($pairs as $pair) {
        $wanted[(int)$pair['client_id'] . '_' . (int)$pair['project_id']] = true;
    }
    $map = array();
    foreach ($this->db->get()->result() as $row) {
        $key = (int)$row->client_id . '_' . (int)$row->project_id;
        if (isset($wanted[$key])) {
            $map[$key] = (float)$row->total_general_hours;
        }
    }
    $this->db->reset_query();
    return $map;
}

//func for monthly client report table ( without limit - for excel )         
public function getAllClientInformation($monthName = '', $search = '', $from_date = '', $to_date = '', $department = '', $aggregate_by_month = false) {
   
        $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];     
$mepManagers = ['146', '230', '149','455'];
$arcManagers = ['41', '394' , '270','47', '182', '71', '53', '155'];
$isMEPManager = in_array($empId, $mepManagers);
        $isARCManager = in_array($empId, $arcManagers);
    
    // Calculate production hours in subquery WITHOUT quality_error_log join to avoid multiplication
    // Build production hours subquery first
    $hoursSelect = '
        emp_record_details.client_Id,
        emp_record_details.project_Id,
        SUM(emp_record_details.emp_time_hours) as total_hours';
    if ($aggregate_by_month) {
        $hoursSelect .= ',
        DATE_FORMAT(emp_record_details.emp_report_dates, \'%Y-%m\') as report_month';
    }
    $hoursSubquery = $this->db->select($hoursSelect, false)
    ->from('emp_record_details')
    ->join('client_details', 'client_details.client_Id = emp_record_details.client_id', 'inner')
    ->join('project_details', 'project_details.project_id = emp_record_details.project_id', 'inner')
    ->where('client_details.status', 'Active')
    ->where("project_details.project_name NOT LIKE '%(General)%'");
    
    // Apply user-specific department filters to subquery
    // Use project_type from project_details if not empty, otherwise use department from client_details
    if (!empty($department)) {
        // If department filter is selected, use it (handle both single and multiple departments)
        if (is_array($department)) {
            $hoursSubquery->where_in("COALESCE(NULLIF(project_details.project_type, ''), client_details.department)", $department);
        } else {
            $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", $department);
        }
    } elseif ($userType == 'admin' || $empId == '140') {
        $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('MEP', '3D Visualization', 'Architectural', 'Structural', '2D Auto CAD')", false);
    } elseif ($empId == '149'|| $isMEPManager ) {
        $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", 'MEP');
    } elseif ($empId == '47' || $isARCManager) {
        $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
    }
    
    // Apply date range filter to subquery
    if (!empty($from_date) && !empty($to_date)) {
        $hoursSubquery->where('emp_record_details.emp_report_dates >=', $from_date);
        $hoursSubquery->where('emp_record_details.emp_report_dates <=', $to_date);
    }
    // If no date range provided, show all data (no date filter applied)
    
    if ($aggregate_by_month) {
        $hoursSubquery->group_by('emp_record_details.project_Id, emp_record_details.client_Id, report_month');
    } else {
        $hoursSubquery->group_by('emp_record_details.project_Id, emp_record_details.client_Id');
    }
    $hoursSubquerySQL = $hoursSubquery->get_compiled_select();
    $this->db->reset_query();
    
    // Determine if we should pull invoice from project_invoice_monthly (month/year-wise) instead of static project_invoice_amt
    $useMonthlyInvoice = !empty($from_date) && !empty($to_date) && !$aggregate_by_month;
    $fromKeyInvoice = null;
    $toKeyInvoice = null;
    if ($useMonthlyInvoice) {
        $fromKeyInvoice = (int) date('Ym', strtotime($from_date));
        $toKeyInvoice = (int) date('Ym', strtotime($to_date));
    }

    // Main query using the subquery - join quality_error_log separately to avoid multiplication
    $selectInvoice = 'project_details.project_invoice_amt,';
    if ($useMonthlyInvoice && $fromKeyInvoice && $toKeyInvoice) {
        $selectInvoice = '(
            SELECT COALESCE(SUM(pim.invoice_hours), 0)
            FROM project_invoice_monthly pim
            WHERE pim.project_Id = project_hours.project_Id
              AND (pim.invoice_year * 100 + pim.invoice_month) >= ' . $fromKeyInvoice . '
              AND (pim.invoice_year * 100 + pim.invoice_month) <= ' . $toKeyInvoice . '
        ) as project_invoice_amt,';
    }
    $reportMonthSelect = $aggregate_by_month ? 'project_hours.report_month,' : '';
    $clientDateSubqueryBase = " FROM project_details p2
        WHERE p2.client_Id = client_details.client_Id
        AND LOWER(COALESCE(p2.project_name, '')) NOT LIKE '%general%'";
    $this->db->select('
        project_hours.client_Id,
        project_hours.project_Id,
        ' . $reportMonthSelect . '
        client_details.empId as clientpm,
        project_details.empId as projectpm,
        client_details.client_name,
        COALESCE(NULLIF(project_details.project_type, ""), client_details.department) as department,
        project_details.project_name,
        project_details.status,
        project_details.man_days,
        project_details.project_start_date,
        project_details.project_end_date,
        (SELECT MIN(p2.project_start_date)' . $clientDateSubqueryBase . '
            AND p2.project_start_date IS NOT NULL
            AND p2.project_start_date != \'0000-00-00\') as client_start_date,
        (SELECT MAX(p2.project_end_date)' . $clientDateSubqueryBase . '
            AND p2.project_end_date IS NOT NULL
            AND p2.project_end_date != \'0000-00-00\') as client_end_date,
        ' . $selectInvoice . '
        employee_details.name AS pm_name,
        client_pm_ed.name AS client_pm_name,
        MAX(quality_error_log.qty_project_Id) as qty_project_Id,
        SUM(COALESCE(quality_error_log.analyzer_num_of_errors, 0)) as analyzer_num_of_errors,
        SUM(COALESCE(quality_error_log.reviewer_num_of_errors, 0)) as reviewer_num_of_errors,
        MAX(quality_error_log.analyzer_report_date) as analyzer_report_date,
        project_hours.total_hours
    ');
    $this->db->from('(' . $hoursSubquerySQL . ') as project_hours');
    $this->db->join('client_details', 'client_details.client_Id = project_hours.client_Id', 'inner');
    $this->db->join('project_details', 'project_details.project_id = project_hours.project_Id', 'inner');
    $qualityReportMonthExpr = $aggregate_by_month ? 'project_hours.report_month' : null;
    $this->db->join(
        'quality_error_log',
        $this->_client_report_quality_join_on(
            'project_hours.project_Id',
            'project_hours.client_Id',
            $from_date,
            $to_date,
            $qualityReportMonthExpr
        ),
        'left'
    );
    $this->db->join('employee_details', 'employee_details.empId = project_details.empId', 'left');
    $this->db->join('employee_details client_pm_ed', 'client_pm_ed.empId = client_details.empId', 'left');

    // Apply filters to main query
    $this->db->where('client_details.status', 'Active');
    //$this->db->where('project_details.status', 'process');
    $this->db->where("client_details.client_name != ''");
    // Use project_type from project_details if not empty, otherwise use department from client_details
    $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IS NOT NULL");
    $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) !=", '');
    $this->db->where("client_details.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
    $this->db->where("project_details.project_name NOT LIKE '%(General)%'");
    
    // Apply user-specific department filters
    // Use project_type from project_details if not empty, otherwise use department from client_details
    if (!empty($department)) {
        // If department filter is selected, use it (handle both single and multiple departments)
        if (is_array($department)) {
            $this->db->where_in("COALESCE(NULLIF(project_details.project_type, ''), client_details.department)", $department);
        } else {
            $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", $department);
        }
    } elseif ($userType == 'admin' || $empId == '140') {
        $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('MEP', '3D Visualization', 'Architectural', 'Structural', '2D Auto CAD')", false);
    } elseif ($empId == '149'|| $isMEPManager ) {
        $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", 'MEP');
    } elseif ($empId == '47' || $isARCManager) {
        $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
    }

    if (!empty($search)) {
        $searchTerms = array_filter(array_map('trim', explode(',', $search)));
        $this->db->group_start();
        $first = true;
        foreach ($searchTerms as $term) {
            if ($first) {
                $this->db->group_start();
                $first = false;
            } else {
                $this->db->or_group_start();
            }
            $this->db->like('client_details.client_name', $term, 'both');
            $this->db->or_like('project_details.project_name', $term, 'both');
            $this->db->or_like('employee_details.name', $term, 'both');
            $this->db->or_like('client_pm_ed.name', $term, 'both');
            $this->db->group_end();
        }
        $this->db->group_end();
    }

    // Group by client and project to ensure unique rows - quality error log fields are aggregated
  $groupBy = 'project_hours.project_Id, project_hours.client_Id, client_details.empId, project_details.empId, client_details.client_name, COALESCE(NULLIF(project_details.project_type, ""), client_details.department), project_details.project_name, project_details.status, project_details.man_days, project_details.project_start_date, project_details.project_end_date, project_details.project_invoice_amt, employee_details.name, client_pm_ed.name';
    if ($aggregate_by_month) {
        $groupBy .= ', project_hours.report_month';
    }
    $this->db->group_by($groupBy);
    $this->db->order_by('project_hours.client_Id', 'DESC');

    $query = $this->db->get();
    
    $projects = $query->result();

    // Calculate general hours for all projects based on " - (General)" pattern
    if (!empty($projects)) {
        // Get all unique project names
        $projectNames = array_unique(array_map(function($p) {
            return $p->project_name;
        }, $projects));
        
        // Batch query: Find all general project IDs
        // First, get all general projects that might match
        $this->db->select('project_id, project_name');
        $this->db->from('project_details');
        $this->db->group_start()
            ->like('project_name', '(General)')
            ->or_like('project_name', '-(General)')
            ->or_like('project_name', '- (General)')
            ->or_like('project_name', ' - (General)')
            ->or_like('project_name', ' (General)')
        ->group_end();
        $allGeneralProjects = $this->db->get()->result();
        
        // Create a mapping: base project name -> general project ID
        $generalProjectMap = [];
        foreach ($allGeneralProjects as $gp) {
            // Extract base project name (remove General and all its variations)
            // Handle patterns like: "Project - (General)", "Project (General)", "Project-General", "Project General", etc.
            $baseName = preg_replace('/\s*[-]?\s*\(?General\)?\s*/i', '', $gp->project_name);
            $baseName = preg_replace('/\s*General\s*/i', '', $baseName);
            $baseName = trim($baseName);
            
            // Check if this base name matches any of our project names
            foreach ($projectNames as $projectName) {
                $normalizedProjectName = trim($projectName);
                $normalizedBaseName = trim($baseName);
                
                // Case-insensitive exact match - the project name is the base name
                if (strcasecmp($normalizedProjectName, $normalizedBaseName) === 0) {
                    // Map base name to general project ID (use first match if multiple)
                    if (!isset($generalProjectMap[$projectName])) {
                        $generalProjectMap[$projectName] = $gp->project_id;
                    }
                    break; // Found a match, move to next general project
                }
                
                // Flexible match: Check if project name matches base name (handles variations like "Chris's" vs "Chris" or suffixes like "_HVAC")
                // Normalize by handling possessive forms, removing special characters, and converting to lowercase
                // Handle possessive: "Chris's" -> "chris", "Chris'" -> "chris"
                $normalizedProjectForMatch = strtolower($normalizedProjectName);
                $normalizedProjectForMatch = preg_replace('/[\'`]s?/', '', $normalizedProjectForMatch); // Remove apostrophes and possessive 's'
                $normalizedProjectForMatch = preg_replace('/[_\-\s]+/', '', $normalizedProjectForMatch); // Remove underscores, hyphens, spaces
                
                $normalizedBaseForMatch = strtolower($normalizedBaseName);
                $normalizedBaseForMatch = preg_replace('/[\'`]s?/', '', $normalizedBaseForMatch); // Remove apostrophes and possessive 's'
                $normalizedBaseForMatch = preg_replace('/[_\-\s]+/', '', $normalizedBaseForMatch); // Remove underscores, hyphens, spaces
                
                // Check if project name starts with base name (after normalization)
                // This handles cases like "Chris's Pancake and Dining_HVAC" matching "Chris Pancake and Dining"
                if (!empty($normalizedBaseForMatch) && strpos($normalizedProjectForMatch, $normalizedBaseForMatch) === 0) {
                    // Map base name to general project ID (use first match if multiple)
                    if (!isset($generalProjectMap[$projectName])) {
                        $generalProjectMap[$projectName] = $gp->project_id;
                    }
                    break; // Found a match, move to next general project
                }
                
                // Also check if base name is contained in project name (for cases with additional suffixes)
                // This ensures we catch variations like "Chris Pancake and Dining" matching "Chris's Pancake and Dining_HVAC"
                if (!empty($normalizedBaseForMatch) && !empty($normalizedProjectForMatch)) {
                    // Check if base is contained in project or vice versa
                    $baseContainedInProject = strpos($normalizedProjectForMatch, $normalizedBaseForMatch) !== false;
                    $projectContainedInBase = strpos($normalizedBaseForMatch, $normalizedProjectForMatch) !== false;
                    
                    if ($baseContainedInProject || $projectContainedInBase) {
                        // Ensure the match is significant (base name represents a substantial portion)
                        $baseLen = strlen($normalizedBaseForMatch);
                        $projectLen = strlen($normalizedProjectForMatch);
                        if ($baseLen >= 5 && $projectLen >= 5) { // Both should be at least 5 characters
                            // Check if there's significant overlap (at least 60% of shorter string)
                            $shorterLen = min($baseLen, $projectLen);
                            $longerLen = max($baseLen, $projectLen);
                            $matchRatio = $shorterLen / max($longerLen, 1);
                            if ($matchRatio >= 0.6) {
                                // Map base name to general project ID (use first match if multiple)
                                if (!isset($generalProjectMap[$projectName])) {
                                    $generalProjectMap[$projectName] = $gp->project_id;
                                }
                                break; // Found a match, move to next general project
                            }
                        }
                    }
                }
            }
        }
        
        // Calculate general hours per client + general project ID combination
        // Track by general project ID to ensure each general project's hours are only shown once per client
        $generalHoursMapByGeneralProjectId = []; // Key: clientId_generalProjectId, Value: general hours
        $projectToGeneralProjectIdMap = []; // Key: clientId_projectId, Value: generalProjectId
        
        $needsFallback = array();
        foreach ($projects as $p) {
            $baseName = trim($p->project_name);
            if (isset($generalProjectMap[$baseName])) {
                $projectKey = $p->client_Id . '_' . $p->project_Id;
                $projectToGeneralProjectIdMap[$projectKey] = $generalProjectMap[$baseName];
            } else {
                $needsFallback[] = $p;
            }
        }

        if (!empty($needsFallback)) {
            $fallbackClientIds = array();
            foreach ($needsFallback as $p) {
                $fallbackClientIds[(int)$p->client_Id] = true;
            }
            $fallbackClientIds = array_keys($fallbackClientIds);
            $this->db->distinct();
            $this->db->select('erd.client_id, pd.project_id, pd.project_name');
            $this->db->from('project_details pd');
            $this->db->join('emp_record_details erd', 'erd.project_id = pd.project_id', 'inner');
            $this->db->where_in('erd.client_id', $fallbackClientIds);
            $this->db->like('pd.project_name', 'General', 'both');
            if (!empty($from_date) && !empty($to_date)) {
                $this->db->where('erd.emp_report_dates >=', $from_date);
                $this->db->where('erd.emp_report_dates <=', $to_date);
            }
            $fallbackRows = $this->db->get()->result();
            $this->db->reset_query();
            $fallbackByClient = array();
            foreach ($fallbackRows as $fgp) {
                $fallbackByClient[(int)$fgp->client_id][] = $fgp;
            }

            foreach ($needsFallback as $p) {
                $clientId = $p->client_Id;
                $baseName = trim($p->project_name);
                $generalProjectId = null;
                $normalizedProjectName = strtolower($baseName);
                $normalizedProjectName = preg_replace('/[\'`]s?/', '', $normalizedProjectName);
                $normalizedProjectName = preg_replace('/[_\-\s]+/', '', $normalizedProjectName);
                $fallbackGeneralProjects = isset($fallbackByClient[(int)$clientId]) ? $fallbackByClient[(int)$clientId] : array();

                foreach ($fallbackGeneralProjects as $fgp) {
                    $generalBaseName = preg_replace('/\s*[-]?\s*\(?General\)?\s*/i', '', $fgp->project_name);
                    $generalBaseName = preg_replace('/\s*General\s*/i', '', $generalBaseName);
                    $generalBaseName = trim($generalBaseName);
                    $normalizedGeneralBase = strtolower($generalBaseName);
                    $normalizedGeneralBase = preg_replace('/[\'`]s?/', '', $normalizedGeneralBase);
                    $normalizedGeneralBase = preg_replace('/[_\-\s]+/', '', $normalizedGeneralBase);

                    if (!empty($normalizedGeneralBase) && !empty($normalizedProjectName)) {
                        $matches = false;
                        if (strpos($normalizedProjectName, $normalizedGeneralBase) === 0 ||
                            strpos($normalizedGeneralBase, $normalizedProjectName) === 0) {
                            $matches = true;
                        } elseif (strlen($normalizedGeneralBase) >= 5 && strlen($normalizedProjectName) >= 5) {
                            if (strpos($normalizedProjectName, $normalizedGeneralBase) !== false ||
                                strpos($normalizedGeneralBase, $normalizedProjectName) !== false) {
                                $shorterLen = min(strlen($normalizedGeneralBase), strlen($normalizedProjectName));
                                $longerLen = max(strlen($normalizedGeneralBase), strlen($normalizedProjectName));
                                if ($shorterLen > 0 && ($shorterLen / $longerLen) >= 0.6) {
                                    $matches = true;
                                }
                            }
                        }
                        if ($matches) {
                            $generalProjectId = $fgp->project_id;
                            break;
                        }
                    }
                }
                if ($generalProjectId) {
                    $projectKey = $p->client_Id . '_' . $p->project_Id;
                    $projectToGeneralProjectIdMap[$projectKey] = $generalProjectId;
                }
            }
        }

        $generalHourPairs = array();
        foreach ($projectToGeneralProjectIdMap as $projectKey => $generalProjectId) {
            list($clientId, ) = explode('_', $projectKey, 2);
            $generalHourPairs[$clientId . '_' . $generalProjectId] = array(
                'client_id' => (int)$clientId,
                'project_id' => (int)$generalProjectId,
            );
        }
        $generalHoursMapByGeneralProjectId = $this->_client_report_batch_general_hours(
            array_values($generalHourPairs),
            $from_date,
            $to_date
        );
        
        $clientIds = array();
        foreach ($projects as $p) {
            $clientIds[] = $p->client_Id;
        }
        $lastWorkDateMap = $this->_client_report_batch_last_work_dates(
            $clientIds,
            $from_date,
            $to_date,
            (bool)$aggregate_by_month
        );

        $shownGeneralProjectIds = array();
        foreach ($projects as &$project) {
            $projectKey = $project->client_Id . '_' . $project->project_Id;
            $generalProjectId = isset($projectToGeneralProjectIdMap[$projectKey]) ? $projectToGeneralProjectIdMap[$projectKey] : null;

            if ($generalProjectId) {
                $generalProjectKey = $project->client_Id . '_' . $generalProjectId;
                $gen_hours = isset($generalHoursMapByGeneralProjectId[$generalProjectKey]) ? $generalHoursMapByGeneralProjectId[$generalProjectKey] : 0;

                if ($gen_hours > 0 && !isset($shownGeneralProjectIds[$generalProjectKey])) {
                    if (stripos($project->project_name, 'General') === false) {
                        $project->general_hours = $gen_hours;
                        $shownGeneralProjectIds[$generalProjectKey] = true;
                    } else {
                        $project->general_hours = 0;
                    }
                } else {
                    $project->general_hours = 0;
                }
            } else {
                $project->general_hours = 0;
            }

            $lastKey = $project->client_Id . '_' . $project->project_Id;
            if ($aggregate_by_month && !empty($project->report_month)) {
                $lastKey .= '_' . $project->report_month;
            }
            $project->last_work_date = isset($lastWorkDateMap[$lastKey]) ? $lastWorkDateMap[$lastKey] : null;
        }
        unset($project);
        
        // Find general projects that have hours but no matching regular project
        $existingClientProjectKeys = array();
        foreach ($projects as $p) {
            $key = $p->client_Id . '_' . $p->project_name;
            $existingClientProjectKeys[$key] = true;
        }

        $this->db->distinct();
        $this->db->select('
            erd.client_id,
            erd.project_id,
            pd.project_name,
            cd.client_name,
            cd.empId as clientpm,
            COALESCE(NULLIF(pd.project_type, ""), cd.department) as department,
            pd.empId as projectpm,
            pd.status,
            pd.man_days,
            pd.project_start_date,
            pd.project_end_date
        ');
        $this->db->from('emp_record_details erd');
        $this->db->join('project_details pd', 'pd.project_id = erd.project_id', 'inner');
        $this->db->join('client_details cd', 'cd.client_Id = erd.client_id', 'inner');
        $this->db->where('cd.status', 'Active');
        $this->db->where("cd.client_name != ''");
        $this->db->where("cd.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
        $this->db->like('pd.project_name', 'General', 'both');

        if (!empty($from_date) && !empty($to_date)) {
            $this->db->where('erd.emp_report_dates >=', $from_date);
            $this->db->where('erd.emp_report_dates <=', $to_date);
        }

        if (!empty($department)) {
            if (is_array($department)) {
                $this->db->where_in("COALESCE(NULLIF(pd.project_type, ''), cd.department)", $department);
            } else {
                $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) =", $department);
            }
        } elseif ($userType == 'admin' || $empId == '140') {
            $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) IN", "('MEP', '3D Visualization', 'Architectural', 'Structural', '2D Auto CAD')", false);
        } elseif ($empId == '149'|| $isMEPManager ) {
            $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) =", 'MEP');
        } elseif ($empId == '47' || $isARCManager) {
            $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) IN", "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
        }

        $generalProjectsWithHours = $this->db->get()->result();
        $this->db->reset_query();

        $shownGeneralHoursKeys = array();
        $orphanCandidates = array();
        $orphanHourPairs = array();

        foreach ($generalProjectsWithHours as $gp) {
            $generalProjectName = $gp->project_name;
            $baseName = preg_replace('/\s*[-]?\s*\(?General\)?\s*/i', '', $generalProjectName);
            $baseName = preg_replace('/\s*General\s*/i', '', $baseName);
            $baseName = trim($baseName);

            if (empty($baseName)) {
                continue;
            }

            $key = $gp->client_id . '_' . $baseName;
            if (isset($existingClientProjectKeys[$key])) {
                continue;
            }

            $matched = false;
            foreach ($existingClientProjectKeys as $existingKey => $val) {
                list($existingClientId, $existingProjectName) = explode('_', $existingKey, 2);
                if ($existingClientId != $gp->client_id) {
                    continue;
                }
                $normalizedBaseName = strtolower($baseName);
                $normalizedBaseName = preg_replace('/[\'`]s?/', '', $normalizedBaseName);
                $normalizedBaseName = preg_replace('/[_\-\s]+/', '', $normalizedBaseName);
                $normalizedExistingName = strtolower($existingProjectName);
                $normalizedExistingName = preg_replace('/[\'`]s?/', '', $normalizedExistingName);
                $normalizedExistingName = preg_replace('/[_\-\s]+/', '', $normalizedExistingName);
                if (!empty($normalizedBaseName) && !empty($normalizedExistingName)) {
                    if (strpos($normalizedExistingName, $normalizedBaseName) === 0 ||
                        strpos($normalizedBaseName, $normalizedExistingName) === 0) {
                        $matched = true;
                        break;
                    } elseif (strlen($normalizedBaseName) >= 5 && strlen($normalizedExistingName) >= 5) {
                        if (strpos($normalizedExistingName, $normalizedBaseName) !== false ||
                            strpos($normalizedBaseName, $normalizedExistingName) !== false) {
                            $shorterLen = min(strlen($normalizedBaseName), strlen($normalizedExistingName));
                            $longerLen = max(strlen($normalizedBaseName), strlen($normalizedExistingName));
                            if ($shorterLen > 0 && ($shorterLen / $longerLen) >= 0.6) {
                                $matched = true;
                                break;
                            }
                        }
                    }
                }
            }

            if ($matched) {
                continue;
            }

            $orphanCandidates[] = array('gp' => $gp, 'baseName' => $baseName);
            $orphanHourPairs[] = array(
                'client_id' => (int)$gp->client_id,
                'project_id' => (int)$gp->project_id,
            );
        }

        if (!empty($orphanCandidates)) {
            $orphanHoursMap = $this->_client_report_batch_general_hours($orphanHourPairs, $from_date, $to_date);
            $orphanClientIds = array();
            $pmIds = array();
            foreach ($orphanCandidates as $item) {
                $orphanClientIds[] = $item['gp']->client_id;
                if (!empty($item['gp']->projectpm)) {
                    $pmIds[] = $item['gp']->projectpm;
                }
                if (!empty($item['gp']->clientpm)) {
                    $pmIds[] = $item['gp']->clientpm;
                }
            }
            $orphanLastMap = $this->_client_report_batch_last_work_dates($orphanClientIds, $from_date, $to_date, false);
            $pmNamesById = array();
            $pmIds = array_values(array_unique(array_filter($pmIds)));
            if (!empty($pmIds)) {
                $pmRows = $this->db->select('empId, name')->from('employee_details')->where_in('empId', $pmIds)->get()->result();
                foreach ($pmRows as $pmRow) {
                    $pmNamesById[$pmRow->empId] = $pmRow->name;
                }
                $this->db->reset_query();
            }

            foreach ($orphanCandidates as $item) {
                $gp = $item['gp'];
                $baseName = $item['baseName'];
                $hoursKey = (int)$gp->client_id . '_' . (int)$gp->project_id;
                $generalHours = isset($orphanHoursMap[$hoursKey]) ? $orphanHoursMap[$hoursKey] : 0;
                if ($generalHours <= 0) {
                    continue;
                }

                $pmName = !empty($gp->projectpm) && isset($pmNamesById[$gp->projectpm]) ? $pmNamesById[$gp->projectpm] : '';
                $clientPmName = !empty($gp->clientpm) && isset($pmNamesById[$gp->clientpm]) ? $pmNamesById[$gp->clientpm] : '';

                $newProject = new stdClass();
                $newProject->client_Id = $gp->client_id;
                $newProject->project_Id = $gp->project_id;
                $newProject->client_name = $gp->client_name;
                $newProject->project_name = $baseName;
                $newProject->department = $gp->department;
                $newProject->clientpm = $gp->clientpm;
                $newProject->projectpm = $gp->projectpm;
                $newProject->pm_name = $pmName;
                $newProject->client_pm_name = $clientPmName;
                $newProject->status = $gp->status;
                $newProject->man_days = $gp->man_days;
                $newProject->project_start_date = $gp->project_start_date;
                $newProject->project_end_date = $gp->project_end_date;
                $newProject->total_hours = 0;

                $normalizedBaseNameForNew = strtolower($baseName);
                $normalizedBaseNameForNew = preg_replace('/[\'`]s?/', '', $normalizedBaseNameForNew);
                $normalizedBaseNameForNew = preg_replace('/[_\-\s]+/', '', $normalizedBaseNameForNew);
                $generalHoursKeyForNew = $gp->client_id . '_' . $normalizedBaseNameForNew;

                $shouldShowGeneralHours = !isset($shownGeneralHoursKeys[$generalHoursKeyForNew]);
                $newProject->general_hours = $shouldShowGeneralHours ? $generalHours : 0;
                if ($shouldShowGeneralHours) {
                    $shownGeneralHoursKeys[$generalHoursKeyForNew] = true;
                }

                $newProject->qty_project_Id = null;
                $newProject->analyzer_num_of_errors = 0;
                $newProject->reviewer_num_of_errors = 0;
                $newProject->analyzer_report_date = null;
                $newProject->last_work_date = isset($orphanLastMap[$hoursKey]) ? $orphanLastMap[$hoursKey] : null;

                $projects[] = $newProject;
            }
        }
    }

    return $projects;
}
    
    
//func for consolidated client report table ( without limit - for excel )           
public function getAllClientInformationConsolidated($search = null, $empWiseKpi = null, $empId = null, $from_date = '', $to_date = '', $department = '') {
    
        $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
    $empId = $this->session->userdata['logged_in_timesheet']['empId'];     
$mepManagers = ['146', '230', '149','455'];
$arcManagers = ['41', '394' , '270','47', '182', '71', '53', '155'];
$isMEPManager = in_array($empId, $mepManagers);
        $isARCManager = in_array($empId, $arcManagers);
    
     
     $currentDate = new DateTime(); // or DateTime('now')
$currentYear = (int) $currentDate->format('Y');
$currentMonth = (int) $currentDate->format('n');

// If current month is January, use previous year for full year data
if ($currentMonth == 1) {
    $queryYear = $currentYear - 1;
    $endMonth = 12; // Up to December of last year
} else {
    $queryYear = $currentYear;
    $endMonth = $currentMonth; // Up to current month
}
    

    // Calculate production hours in subquery WITHOUT quality_error_log join to avoid multiplication
    // Build production hours subquery first
    $hoursSubquery = $this->db->select('
        emp_record_details.client_Id,
        emp_record_details.project_Id,
        SUM(emp_record_details.emp_time_hours) as total_hours
    ', false)
    ->from('emp_record_details')
    ->join('client_details', 'client_details.client_Id = emp_record_details.client_id', 'inner')
    ->join('project_details', 'project_details.project_id = emp_record_details.project_id', 'inner')
    ->where('client_details.status', 'Active')
    //->where('project_details.status', 'process')
    ->where("project_details.project_name NOT LIKE '%(General)%'");
    
    // Apply user-specific department filters to subquery
    // Use project_type from project_details if not empty, otherwise use department from client_details
    if (!empty($department)) {
        // If department filter is selected, use it (handle both single and multiple departments)
        if (is_array($department)) {
            $hoursSubquery->where_in("COALESCE(NULLIF(project_details.project_type, ''), client_details.department)", $department);
        } else {
            $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", $department);
        }
    } elseif ($userType == 'admin' || $empId == '140') {
        $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('MEP', '3D Visualization', 'Architectural', 'Structural', '2D Auto CAD')", false);
    } elseif ($empId == '149'|| $isMEPManager ) {
        $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) =", 'MEP');
    } elseif ($empId == '47' || $isARCManager) {
        $hoursSubquery->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
    }
    
    // Apply date range filter to subquery
    if (!empty($from_date) && !empty($to_date)) {
        $hoursSubquery->where('emp_record_details.emp_report_dates >=', $from_date);
        $hoursSubquery->where('emp_record_details.emp_report_dates <=', $to_date);
    } else {
        $hoursSubquery->where('YEAR(emp_record_details.emp_report_dates)', $queryYear);
        $hoursSubquery->where('MONTH(emp_record_details.emp_report_dates) >=', 1);
        $hoursSubquery->where('MONTH(emp_record_details.emp_report_dates) <=', $endMonth);
    }
    
    $hoursSubquery->group_by('emp_record_details.project_Id, emp_record_details.client_Id');
    $hoursSubquerySQL = $hoursSubquery->get_compiled_select();
    $this->db->reset_query();
    
    // Main query using the subquery - join quality_error_log separately to avoid multiplication
    // Use project_type from project_details if not empty, otherwise use department from client_details
    // For invoiced hours, when a reporting window is defined, SUM invoice_hours from
    // project_invoice_monthly for that project across all months in the window using a scalar subquery.
    $fromKeyExcel = null;
    $toKeyExcel = null;
    if (!empty($from_date) && !empty($to_date)) {
        $fromKeyExcel = (int) date('Ym', strtotime($from_date));
        $toKeyExcel = (int) date('Ym', strtotime($to_date));
    } else {
        $fromKeyExcel = (int) ($queryYear . '01');
        $toKeyExcel = (int) ($queryYear . str_pad($endMonth, 2, '0', STR_PAD_LEFT));
    }
    $selectInvoiceExcel = 'project_details.project_invoice_amt,';
    if ($fromKeyExcel && $toKeyExcel) {
        $selectInvoiceExcel = '(
            SELECT COALESCE(SUM(pim.invoice_hours), 0)
            FROM project_invoice_monthly pim
            WHERE pim.project_Id = project_hours.project_Id
              AND (pim.invoice_year * 100 + pim.invoice_month) >= ' . $fromKeyExcel . '
              AND (pim.invoice_year * 100 + pim.invoice_month) <= ' . $toKeyExcel . '
        ) as project_invoice_amt,';
    }

    $this->db->select('
        project_hours.client_Id,
        project_hours.project_Id,
        client_details.empId as clientpm,
        project_details.empId as projectpm,
        client_details.client_name,
        COALESCE(NULLIF(project_details.project_type, ""), client_details.department) as department,
        project_details.project_name,
        project_details.status,
        project_details.man_days,
        project_details.project_start_date,
        project_details.project_end_date,
        ' . $selectInvoiceExcel . '
        employee_details.name AS pm_name,
        MAX(quality_error_log.qty_project_Id) as qty_project_Id,
        SUM(COALESCE(quality_error_log.analyzer_num_of_errors, 0)) as analyzer_num_of_errors,
        SUM(COALESCE(quality_error_log.reviewer_num_of_errors, 0)) as reviewer_num_of_errors,
        MAX(quality_error_log.analyzer_report_date) as analyzer_report_date,
        project_hours.total_hours
    ');
    $qualityFromDate = $from_date;
    $qualityToDate = $to_date;
    if (empty($qualityFromDate) || empty($qualityToDate)) {
        $qualityFromDate = sprintf('%04d-01-01', $queryYear);
        $qualityToDate = date('Y-m-t', mktime(0, 0, 0, $endMonth, 1, $queryYear));
    }

    $this->db->from('(' . $hoursSubquerySQL . ') as project_hours');
    $this->db->join('client_details', 'client_details.client_Id = project_hours.client_Id', 'inner');
    $this->db->join('project_details', 'project_details.project_id = project_hours.project_Id', 'inner');
    $this->db->join(
        'quality_error_log',
        $this->_client_report_quality_join_on('project_hours.project_Id', 'project_hours.client_Id', $qualityFromDate, $qualityToDate),
        'left'
    );
    $this->db->join('employee_details', 'employee_details.empId = project_details.empId', 'left');

    $this->db->where('client_details.status', 'Active');
    // Use project_type from project_details if not empty, otherwise use department from client_details
    $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IN", "('MEP', '3D Visualization', 'Architectural', 'Structural', '2D Auto CAD')", false);
    $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) IS NOT NULL");
    $this->db->where("COALESCE(NULLIF(project_details.project_type, ''), client_details.department) !=", '');
    $this->db->where("client_details.client_name != ''");
    $this->db->where("client_details.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
   // $this->db->where('project_details.status', 'process');
    $this->db->where("project_details.project_name NOT LIKE '%(General)%'");


    if (!empty($department)) {
        // If department filter is selected, use it (handle both single and multiple departments)
        if (is_array($department)) {
            $this->db->where_in('client_details.department', $department);
        } else {
            $this->db->where('client_details.department', $department);
        }
    } elseif ($userType == 'admin' || $empId == '140') {
        // Default non-admin access
        $this->db->where_in('client_details.department', ['MEP', '3D Visualization', 'Architectural', 'Structural']);
    } elseif ($empId == '149'|| $isMEPManager ) {
        
        $this->db->where('client_details.department', 'MEP');
    } elseif ($empId == '47' || $isARCManager) {
        
        $this->db->where_in('client_details.department', ['Architectural', 'Structural', '3D Visualization']);
    } 
    
    if (!empty($search)) {
        $searchTerms = array_filter(array_map('trim', explode(',', $search)));
        $this->db->group_start();
        $first = true;
        foreach ($searchTerms as $term) {
            if ($first) {
                $this->db->group_start();
                $first = false;
            } else {
                $this->db->or_group_start();
            }
            $this->db->like('client_details.client_name', $term, 'both');
            $this->db->or_like('project_details.project_name', $term, 'both');
            $this->db->or_like('employee_details.name', $term, 'both');
            $this->db->group_end();
        }
        $this->db->group_end();
    }

    // Apply grouping and ordering
    // Group by client and project only to ensure unique rows - quality error log fields are aggregated
    $this->db->group_by('project_hours.project_Id, project_hours.client_Id, client_details.empId, project_details.empId, client_details.client_name, client_details.department, project_details.project_name, project_details.status, project_details.man_days, project_details.project_start_date, project_details.project_end_date, project_details.project_invoice_amt, employee_details.name');
    $this->db->order_by('project_hours.client_Id', 'DESC');

    $query = $this->db->get();
//    echo $this->db->last_query();

    $projects = $query->result();
    
    // Calculate general hours for all projects based on "- (General)" or " (General)" patterns
    // This ensures PG hours are calculated per project based on matching general projects
    if (!empty($projects)) {
        // Get all unique project names
        $projectNames = array_unique(array_map(function($p) {
            return $p->project_name;
        }, $projects));
        
        // Batch query: Find all general project IDs
        // Specifically search for projects with "- (General)" or " (General)" patterns
        $this->db->select('project_id, project_name');
        $this->db->from('project_details');
        // Look for patterns: "- (General)" or " (General)" - these are the standard patterns for general projects
        $this->db->group_start();
        $this->db->like('project_name', '- (General)', 'both'); // Pattern: "Project - (General)"
        $this->db->or_like('project_name', ' (General)', 'both'); // Pattern: "Project (General)"
        $this->db->group_end();
        $allGeneralProjects = $this->db->get()->result();
        
        // Create a mapping: base project name -> general project ID
        $generalProjectMap = [];
        foreach ($allGeneralProjects as $gp) {
            // Extract base project name by removing "- (General)" or " (General)" patterns
            // Handle patterns: "Project - (General)", "Project (General)", or "Domino's Kingshighway - Mech (General)"
            $baseName = preg_replace('/\s*-\s*\(General\)\s*$/i', '', $gp->project_name); // Remove "- (General)" at end
            $baseName = preg_replace('/\s*\(General\)\s*$/i', '', $baseName); // Remove " (General)" at end
            $baseName = trim($baseName);
            
            // Check if this base name matches any of our project names
            foreach ($projectNames as $projectName) {
                $normalizedProjectName = trim($projectName);
                $normalizedBaseName = trim($baseName);
                
                // First check: Direct pattern match - if general project is exactly "[Project Name] (General)"
                // This handles cases like "Domino's Kingshighway - Mech (General)" matching "Domino's Kingshighway - Mech"
                $expectedGeneralPattern = $normalizedProjectName . ' (General)';
                if (strcasecmp($gp->project_name, $expectedGeneralPattern) === 0) {
                    if (!isset($generalProjectMap[$projectName])) {
                        $generalProjectMap[$projectName] = $gp->project_id;
                    }
                    break; // Found a match, move to next general project
                }
                
                // Second check: Case-insensitive exact match - the project name is the base name
                // This handles cases where base name extraction worked correctly
                if (strcasecmp($normalizedProjectName, $normalizedBaseName) === 0) {
                    // Map base name to general project ID (use first match if multiple)
                    if (!isset($generalProjectMap[$projectName])) {
                        $generalProjectMap[$projectName] = $gp->project_id;
                    }
                    break; // Found a match, move to next general project
                }
                
                // Flexible match: Check if project name matches base name (handles variations)
                // This handles cases like:
                // - General: "Domino's Kingshighway - Mech (General)" -> base: "Domino's Kingshighway - Mech"
                // - Production: "Domino's Kingshighway (Mech)"
                // Normalize by handling possessive forms, removing special characters, and converting to lowercase
                // Handle possessive: "Chris's" -> "chris", "Chris'" -> "chris"
                $normalizedProjectForMatch = strtolower($normalizedProjectName);
                $normalizedProjectForMatch = preg_replace('/[\'`]s?/', '', $normalizedProjectForMatch); // Remove apostrophes and possessive 's'
                $normalizedProjectForMatch = preg_replace('/\([^)]*\)/', '', $normalizedProjectForMatch); // Remove parentheses and their contents like "(Mech)"
                $normalizedProjectForMatch = preg_replace('/[_\-\s]+/', '', $normalizedProjectForMatch); // Remove underscores, hyphens, spaces
                
                $normalizedBaseForMatch = strtolower($normalizedBaseName);
                $normalizedBaseForMatch = preg_replace('/[\'`]s?/', '', $normalizedBaseForMatch); // Remove apostrophes and possessive 's'
                $normalizedBaseForMatch = preg_replace('/\([^)]*\)/', '', $normalizedBaseForMatch); // Remove parentheses and their contents
                $normalizedBaseForMatch = preg_replace('/[_\-\s]+/', '', $normalizedBaseForMatch); // Remove underscores, hyphens, spaces
                
                // Check if normalized versions match exactly (handles "Domino's Kingshighway - Mech" vs "Domino's Kingshighway (Mech)")
                if ($normalizedProjectForMatch === $normalizedBaseForMatch && !empty($normalizedBaseForMatch)) {
                    if (!isset($generalProjectMap[$projectName])) {
                        $generalProjectMap[$projectName] = $gp->project_id;
                    }
                    break; // Found a match, move to next general project
                }
                
                // Check if project name starts with base name (after normalization)
                // This handles cases like "Chris's Pancake and Dining_HVAC" matching "Chris Pancake and Dining"
                if (!empty($normalizedBaseForMatch) && strpos($normalizedProjectForMatch, $normalizedBaseForMatch) === 0) {
                    // Map base name to general project ID (use first match if multiple)
                    if (!isset($generalProjectMap[$projectName])) {
                        $generalProjectMap[$projectName] = $gp->project_id;
                    }
                    break; // Found a match, move to next general project
                }
                
                // Also check if base name is contained in project name (for cases with additional suffixes)
                // This ensures we catch variations like "Chris Pancake and Dining" matching "Chris's Pancake and Dining_HVAC"
                if (!empty($normalizedBaseForMatch) && !empty($normalizedProjectForMatch)) {
                    // Check if base is contained in project or vice versa
                    $baseContainedInProject = strpos($normalizedProjectForMatch, $normalizedBaseForMatch) !== false;
                    $projectContainedInBase = strpos($normalizedBaseForMatch, $normalizedProjectForMatch) !== false;
                    
                    if ($baseContainedInProject || $projectContainedInBase) {
                        // Ensure the match is significant (base name represents a substantial portion)
                        $baseLen = strlen($normalizedBaseForMatch);
                        $projectLen = strlen($normalizedProjectForMatch);
                        if ($baseLen >= 5 && $projectLen >= 5) { // Both should be at least 5 characters
                            // Check if there's significant overlap (at least 60% of shorter string)
                            $shorterLen = min($baseLen, $projectLen);
                            $longerLen = max($baseLen, $projectLen);
                            $matchRatio = $shorterLen / max($longerLen, 1);
                            if ($matchRatio >= 0.6) {
                                // Map base name to general project ID (use first match if multiple)
                                if (!isset($generalProjectMap[$projectName])) {
                                    $generalProjectMap[$projectName] = $gp->project_id;
                                }
                                break; // Found a match, move to next general project
                            }
                        }
                    }
                }
            }
        }
        
        // Calculate general hours per client + general project ID combination
        // Track by general project ID to ensure each general project's hours are only shown once per client
        // This matches the logic used in the regular client report (getAllClientInformation)
        $generalHoursMapByGeneralProjectId = []; // Key: clientId_generalProjectId, Value: general hours
        $projectToGeneralProjectIdMap = []; // Key: clientId_projectId, Value: generalProjectId
        
        // First, find matching general project for each project
        foreach ($projects as $p) {
            $clientId = $p->client_Id;
            $baseName = trim($p->project_name);
            $generalProjectId = null;
            
            // Check if we have a matching general project from the initial map
            if (isset($generalProjectMap[$baseName])) {
                $generalProjectId = $generalProjectMap[$baseName];
            } else {
                // Fallback: Try to find general project directly for this client-project combination
                $normalizedProjectName = strtolower($baseName);
                $normalizedProjectName = preg_replace('/[\'`]s?/', '', $normalizedProjectName);
                $normalizedProjectName = preg_replace('/[_\-\s]+/', '', $normalizedProjectName);
                
                // Search for general projects that have hours for this client and match this project name
                $this->db->distinct();
                $this->db->select('pd.project_id, pd.project_name');
                $this->db->from('project_details pd');
                $this->db->join('emp_record_details erd', 'erd.project_id = pd.project_id', 'inner');
                $this->db->where('erd.client_id', $clientId);
                $this->db->like('pd.project_name', 'General', 'both');
                
                if (!empty($from_date) && !empty($to_date)) {
                    $this->db->where('erd.emp_report_dates >=', $from_date);
                    $this->db->where('erd.emp_report_dates <=', $to_date);
                } else {
                    $this->db->where('YEAR(erd.emp_report_dates)', $queryYear);
                    $this->db->where('MONTH(erd.emp_report_dates) >=', 1);
                    $this->db->where('MONTH(erd.emp_report_dates) <=', $endMonth);
                }
                
                $fallbackGeneralProjects = $this->db->get()->result();
                
                // Try to match with normalized names
                foreach ($fallbackGeneralProjects as $fgp) {
                    $generalBaseName = preg_replace('/\s*[-]?\s*\(?General\)?\s*/i', '', $fgp->project_name);
                    $generalBaseName = preg_replace('/\s*General\s*/i', '', $generalBaseName);
                    $generalBaseName = trim($generalBaseName);
                    $normalizedGeneralBase = strtolower($generalBaseName);
                    $normalizedGeneralBase = preg_replace('/[\'`]s?/', '', $normalizedGeneralBase);
                    $normalizedGeneralBase = preg_replace('/[_\-\s]+/', '', $normalizedGeneralBase);
                    
                    if (!empty($normalizedGeneralBase) && !empty($normalizedProjectName)) {
                        $matches = false;
                        
                        if (strpos($normalizedProjectName, $normalizedGeneralBase) === 0 || 
                            strpos($normalizedGeneralBase, $normalizedProjectName) === 0) {
                            $matches = true;
                        } elseif (strlen($normalizedGeneralBase) >= 5 && strlen($normalizedProjectName) >= 5) {
                            if (strpos($normalizedProjectName, $normalizedGeneralBase) !== false || 
                                strpos($normalizedGeneralBase, $normalizedProjectName) !== false) {
                                $shorterLen = min(strlen($normalizedGeneralBase), strlen($normalizedProjectName));
                                $longerLen = max(strlen($normalizedGeneralBase), strlen($normalizedProjectName));
                                if ($shorterLen > 0 && ($shorterLen / $longerLen) >= 0.6) {
                                    $matches = true;
                                }
                            }
                        }
                        
                        if ($matches) {
                            $generalProjectId = $fgp->project_id;
                            break;
                        }
                    }
                }
            }
            
            // Store the mapping
            if ($generalProjectId) {
                $projectKey = $p->client_Id . '_' . $p->project_Id;
                $projectToGeneralProjectIdMap[$projectKey] = $generalProjectId;
                
                // Calculate general hours for this client + general project ID combination (only once per general project)
                $generalProjectKey = $clientId . '_' . $generalProjectId;
                if (!isset($generalHoursMapByGeneralProjectId[$generalProjectKey])) {
                    $this->db->select('SUM(emp_time_hours) as total_general_hours');
                    $this->db->from('emp_record_details');
                    $this->db->where('project_id', $generalProjectId);
                    $this->db->where('client_id', $clientId);
                    
                    if (!empty($from_date) && !empty($to_date)) {
                        $this->db->where('emp_report_dates >=', $from_date);
                        $this->db->where('emp_report_dates <=', $to_date);
                    } else {
                        $this->db->where('YEAR(emp_report_dates)', $queryYear);
                        $this->db->where('MONTH(emp_report_dates) >=', 1);
                        $this->db->where('MONTH(emp_report_dates) <=', $endMonth);
                    }
                    
                    $generalHoursResult = $this->db->get()->row();
                    $generalHours = $generalHoursResult ? (float)$generalHoursResult->total_general_hours : 0;
                    $generalHoursMapByGeneralProjectId[$generalProjectKey] = $generalHours;
                }
            }
        }
        
        // Append general hours to each project - but only show once per general project ID per client
        // Track which general project IDs have already been shown to prevent duplication
        // This matches the logic used in the regular client report
        $shownGeneralProjectIds = []; // Track client + general project ID combinations that have shown general hours
        foreach ($projects as &$project) {
            $projectKey = $project->client_Id . '_' . $project->project_Id;
            $generalProjectId = isset($projectToGeneralProjectIdMap[$projectKey]) ? $projectToGeneralProjectIdMap[$projectKey] : null;
            
            if ($generalProjectId) {
                $generalProjectKey = $project->client_Id . '_' . $generalProjectId;
                $gen_hours = isset($generalHoursMapByGeneralProjectId[$generalProjectKey]) ? $generalHoursMapByGeneralProjectId[$generalProjectKey] : 0;
                
                // Only show general hours on the first project that matches this general project ID
                // This ensures each general project's hours are shown only once per client
                if ($gen_hours > 0 && !isset($shownGeneralProjectIds[$generalProjectKey])) {
                    // Determine if the project name does not contain 'General' (case-insensitive)
                    if (stripos($project->project_name, 'General') === false) {
                        $project->general_hours = $gen_hours;
                        $shownGeneralProjectIds[$generalProjectKey] = true;
                    } else {
                        $project->general_hours = 0;
                    }
                } else {
                    $project->general_hours = 0;
                }
            } else {
                $project->general_hours = 0;
            }
            
            // Fetch last date from emp_record_details for this project-client combination
            $this->db->select('MAX(emp_report_dates) as last_work_date');
            $this->db->from('emp_record_details');
            $this->db->where('project_id', $project->project_Id);
            $this->db->where('client_id', $project->client_Id);
            
            // Apply date range filter if provided
            if (!empty($from_date) && !empty($to_date)) {
                $this->db->where('emp_report_dates >=', $from_date);
                $this->db->where('emp_report_dates <=', $to_date);
            } else {
                $this->db->where('YEAR(emp_report_dates)', $queryYear);
                $this->db->where('MONTH(emp_report_dates) >=', 1);
                $this->db->where('MONTH(emp_report_dates) <=', $endMonth);
            }
            
            $lastDateResult = $this->db->get()->row();
            $project->last_work_date = $lastDateResult && !empty($lastDateResult->last_work_date) ? $lastDateResult->last_work_date : null;
        }
        
        // Find general projects that have hours but no matching regular project
        // This ensures we show general hours even if the project itself doesn't have regular hours
        $existingClientProjectKeys = [];
        foreach ($projects as $p) {
            $key = $p->client_Id . '_' . $p->project_name;
            $existingClientProjectKeys[$key] = true;
        }
        
        // Track which general project IDs have already been shown to prevent duplication
        // Use the same $shownGeneralProjectIds that was populated when assigning general hours to existing projects
        // This ensures we don't duplicate general hours that are already shown
        
        // Find all general projects with hours for clients that match our filters
        $this->db->distinct();
        $this->db->select('
            erd.client_id,
            erd.project_id,
            pd.project_name,
            cd.client_name,
            cd.empId as clientpm,
            COALESCE(NULLIF(pd.project_type, ""), cd.department) as department,
            pd.empId as projectpm,
            pd.status,
            pd.man_days,
            pd.project_start_date,
            pd.project_end_date,
            pd.project_invoice_amt
        ');
        $this->db->from('emp_record_details erd');
        $this->db->join('project_details pd', 'pd.project_id = erd.project_id', 'inner');
        $this->db->join('client_details cd', 'cd.client_Id = erd.client_id', 'inner');
        $this->db->where('cd.status', 'Active');
        $this->db->where("cd.client_name != ''");
        $this->db->where("cd.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
        // Look for patterns: "- (General)" or " (General)"
        $this->db->group_start();
        $this->db->like('pd.project_name', '- (General)', 'both'); // Pattern: "Project - (General)"
        $this->db->or_like('pd.project_name', ' (General)', 'both'); // Pattern: "Project (General)"
        $this->db->group_end();
        
        // Apply date range filter
        if (!empty($from_date) && !empty($to_date)) {
            $this->db->where('erd.emp_report_dates >=', $from_date);
            $this->db->where('erd.emp_report_dates <=', $to_date);
        } else {
            $this->db->where('YEAR(erd.emp_report_dates)', $queryYear);
            $this->db->where('MONTH(erd.emp_report_dates) >=', 1);
            $this->db->where('MONTH(erd.emp_report_dates) <=', $endMonth);
        }
        
        // Apply user-specific department filters
        if (!empty($department)) {
            if (is_array($department)) {
                $this->db->where_in("COALESCE(NULLIF(pd.project_type, ''), cd.department)", $department);
            } else {
                $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) =", $department);
            }
        } elseif ($userType == 'admin' || $empId == '140') {
            $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) IN", "('MEP', '3D Visualization', 'Architectural', 'Structural', '2D Auto CAD')", false);
        } elseif ($empId == '149'|| $isMEPManager ) {
            $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) =", 'MEP');
        } elseif ($empId == '47' || $isARCManager) {
            $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) IN", "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
        }
        
        $generalProjectsWithHours = $this->db->get()->result();
        
        // For each general project, extract base name and check if regular project exists
        // Goal: Show general hours if:
        // 1. Project doesn't exist at all
        // 2. Project exists but has no hours entered
        // Skip only if: Project exists in results and already has general hours assigned
        foreach ($generalProjectsWithHours as $gp) {
            // Extract base project name from general project name
            $generalProjectName = $gp->project_name;
            // Remove "- (General)" or " (General)" patterns at the end
            $baseName = preg_replace('/\s*-\s*\(General\)\s*$/i', '', $generalProjectName); // Remove "- (General)" at end
            $baseName = preg_replace('/\s*\(General\)\s*$/i', '', $baseName); // Remove " (General)" at end
            $baseName = trim($baseName);
            
            if (empty($baseName)) {
                continue; // Skip if no base name extracted
            }
            
            // Check if a regular project with this name already exists for this client
            // First check projects that have hours (from query results)
            $key = $gp->client_id . '_' . $baseName;
            if (isset($existingClientProjectKeys[$key])) {
                // Project exists in results - check if it already has general hours
                // If it has general hours, skip. If not, we'll show general hours below
                $projectHasGeneralHoursInResults = false;
                foreach ($projects as $existingProj) {
                    if ($existingProj->client_Id == $gp->client_id && 
                        trim($existingProj->project_name) === $baseName) {
                        if (isset($existingProj->general_hours) && $existingProj->general_hours > 0) {
                            $projectHasGeneralHoursInResults = true;
                            break;
                        }
                    }
                }
                if ($projectHasGeneralHoursInResults) {
                    continue; // Regular project already exists with hours and has general hours, skip
                }
                // Otherwise, continue to show general hours even though project exists in results
            }
            
            // Check if we can match this general project to any existing project (with hours) using flexible matching
            $matched = false;
            foreach ($existingClientProjectKeys as $existingKey => $val) {
                list($existingClientId, $existingProjectName) = explode('_', $existingKey, 2);
                
                // Only check if same client
                if ($existingClientId != $gp->client_id) {
                    continue;
                }
                
                // Normalize names for comparison
                $normalizedBaseName = strtolower($baseName);
                $normalizedBaseName = preg_replace('/[\'`]s?/', '', $normalizedBaseName);
                $normalizedBaseName = preg_replace('/\([^)]*\)/', '', $normalizedBaseName); // Remove parentheses and their contents
                $normalizedBaseName = preg_replace('/[_\-\s]+/', '', $normalizedBaseName);
                
                $normalizedExistingName = strtolower($existingProjectName);
                $normalizedExistingName = preg_replace('/[\'`]s?/', '', $normalizedExistingName);
                $normalizedExistingName = preg_replace('/\([^)]*\)/', '', $normalizedExistingName); // Remove parentheses and their contents
                $normalizedExistingName = preg_replace('/[_\-\s]+/', '', $normalizedExistingName);
                
                // Check if they match
                if (!empty($normalizedBaseName) && !empty($normalizedExistingName)) {
                    if (strpos($normalizedExistingName, $normalizedBaseName) === 0 || 
                        strpos($normalizedBaseName, $normalizedExistingName) === 0) {
                        $matched = true;
                        break;
                    } elseif (strlen($normalizedBaseName) >= 5 && strlen($normalizedExistingName) >= 5) {
                        if (strpos($normalizedExistingName, $normalizedBaseName) !== false || 
                            strpos($normalizedBaseName, $normalizedExistingName) !== false) {
                            $shorterLen = min(strlen($normalizedBaseName), strlen($normalizedExistingName));
                            $longerLen = max(strlen($normalizedBaseName), strlen($normalizedExistingName));
                            if ($shorterLen > 0 && ($shorterLen / $longerLen) >= 0.6) {
                                $matched = true;
                                break;
                            }
                        }
                    }
                }
            }
            
            // Also check if a project exists in project_details table (even if it has no hours)
            // This handles the case where project exists but no hours are entered
            if (!$matched) {
                $this->db->select('pd.project_id, pd.project_name');
                $this->db->from('project_details pd');
                $this->db->join('client_details cd', 'cd.client_Id = pd.client_id', 'inner');
                $this->db->where('pd.client_id', $gp->client_id);
                $this->db->where('cd.status', 'Active');
                $this->db->where("pd.project_name NOT LIKE '%(General)%'");
                
                // Apply department filters
                if (!empty($department)) {
                    if (is_array($department)) {
                        $this->db->where_in("COALESCE(NULLIF(pd.project_type, ''), cd.department)", $department);
                    } else {
                        $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) =", $department);
                    }
                } elseif ($userType == 'admin' || $empId == '140') {
                    $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) IN", "('MEP', '3D Visualization', 'Architectural', 'Structural', '2D Auto CAD')", false);
                } elseif ($empId == '149'|| $isMEPManager ) {
                    $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) =", 'MEP');
                } elseif ($empId == '47' || $isARCManager) {
                    $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) IN", "('Architectural', 'Structural', '3D Visualization', '2D Auto CAD')", false);
                }
                
                $allProjectsForClient = $this->db->get()->result();
                
                // Check if any project matches the base name
                foreach ($allProjectsForClient as $ap) {
                    $projectName = trim($ap->project_name);
                    $normalizedProjectName = strtolower($projectName);
                    $normalizedProjectName = preg_replace('/[\'`]s?/', '', $normalizedProjectName);
                    $normalizedProjectName = preg_replace('/\([^)]*\)/', '', $normalizedProjectName);
                    $normalizedProjectName = preg_replace('/[_\-\s]+/', '', $normalizedProjectName);
                    
                    $normalizedBaseNameForCheck = strtolower($baseName);
                    $normalizedBaseNameForCheck = preg_replace('/[\'`]s?/', '', $normalizedBaseNameForCheck);
                    $normalizedBaseNameForCheck = preg_replace('/\([^)]*\)/', '', $normalizedBaseNameForCheck);
                    $normalizedBaseNameForCheck = preg_replace('/[_\-\s]+/', '', $normalizedBaseNameForCheck);
                    
                    // Check if they match
                    if (!empty($normalizedBaseNameForCheck) && !empty($normalizedProjectName)) {
                        // Exact match
                        if ($normalizedProjectName === $normalizedBaseNameForCheck) {
                            $matched = true;
                            break;
                        }
                        // Flexible match
                        if (strpos($normalizedProjectName, $normalizedBaseNameForCheck) === 0 || 
                            strpos($normalizedBaseNameForCheck, $normalizedProjectName) === 0) {
                            $matched = true;
                            break;
                        } elseif (strlen($normalizedBaseNameForCheck) >= 5 && strlen($normalizedProjectName) >= 5) {
                            if (strpos($normalizedProjectName, $normalizedBaseNameForCheck) !== false || 
                                strpos($normalizedBaseNameForCheck, $normalizedProjectName) !== false) {
                                $shorterLen = min(strlen($normalizedBaseNameForCheck), strlen($normalizedProjectName));
                                $longerLen = max(strlen($normalizedBaseNameForCheck), strlen($normalizedProjectName));
                                if ($shorterLen > 0 && ($shorterLen / $longerLen) >= 0.6) {
                                    $matched = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            
            // If matched to an existing project, check if that project is in our results (has hours)
            // If project exists in project_details but NOT in results (no hours), we should show general hours
            // If project exists in results and already has general hours, skip
            if ($matched) {
                // Check if the matched project appears in our results (meaning it has hours)
                $projectInResults = false;
                $projectHasGeneralHours = false;
                foreach ($projects as $existingProj) {
                    if ($existingProj->client_Id == $gp->client_id) {
                        $normalizedExistingProjName = strtolower(trim($existingProj->project_name));
                        $normalizedExistingProjName = preg_replace('/[\'`]s?/', '', $normalizedExistingProjName);
                        $normalizedExistingProjName = preg_replace('/\([^)]*\)/', '', $normalizedExistingProjName);
                        $normalizedExistingProjName = preg_replace('/[_\-\s]+/', '', $normalizedExistingProjName);
                        
                        $normalizedBaseForCheck = strtolower($baseName);
                        $normalizedBaseForCheck = preg_replace('/[\'`]s?/', '', $normalizedBaseForCheck);
                        $normalizedBaseForCheck = preg_replace('/\([^)]*\)/', '', $normalizedBaseForCheck);
                        $normalizedBaseForCheck = preg_replace('/[_\-\s]+/', '', $normalizedBaseForCheck);
                        
                        if ($normalizedExistingProjName === $normalizedBaseForCheck) {
                            $projectInResults = true;
                            // Check if it already has general hours
                            if (isset($existingProj->general_hours) && $existingProj->general_hours > 0) {
                                $projectHasGeneralHours = true;
                            }
                            break;
                        }
                    }
                }
                
                // If project is in results and already has general hours, skip
                // If project exists in project_details but NOT in results (no hours), continue to show general hours
                if ($projectInResults && $projectHasGeneralHours) {
                    continue; // Matched to existing project in results that already has general hours, skip
                }
                // Otherwise, continue to show general hours:
                // - Project exists in project_details but not in results (no hours) - show general hours
                // - Project exists in results but has no general hours - show general hours (shouldn't happen, but handle it)
            }
            // If $matched is false (project doesn't exist at all), we continue here to show general hours
            
            // Calculate general hours for this general project
            // This will execute if:
            // 1. Project doesn't exist at all ($matched = false)
            // 2. Project exists in project_details but not in results (no hours entered)
            // 3. Project exists in results but has no general hours assigned
            $this->db->select('SUM(emp_time_hours) as total_general_hours');
            $this->db->from('emp_record_details');
            $this->db->where('project_id', $gp->project_id);
            $this->db->where('client_id', $gp->client_id);
            
            // Apply date range filter
            if (!empty($from_date) && !empty($to_date)) {
                $this->db->where('emp_report_dates >=', $from_date);
                $this->db->where('emp_report_dates <=', $to_date);
            } else {
                $this->db->where('YEAR(emp_report_dates)', $queryYear);
                $this->db->where('MONTH(emp_report_dates) >=', 1);
                $this->db->where('MONTH(emp_report_dates) <=', $endMonth);
            }
            
            $generalHoursResult = $this->db->get()->row();
            $generalHours = $generalHoursResult ? (float)$generalHoursResult->total_general_hours : 0;
            
            // Only add if there are actual general hours
            if ($generalHours > 0) {
                // Get PM name
                $this->db->select('name');
                $this->db->from('employee_details');
                $this->db->where('empId', $gp->projectpm);
                $pmResult = $this->db->get()->row();
                $pmName = $pmResult ? $pmResult->name : '';
                
                // Check if this general project's hours have already been shown
                $generalProjectKey = $gp->client_id . '_' . $gp->project_id;
                if (isset($shownGeneralProjectIds[$generalProjectKey])) {
                    continue; // Already shown, skip to prevent duplication
                }
                
                // Create a new project object with 0 production hours but general hours
                $newProject = new stdClass();
                $newProject->client_Id = $gp->client_id;
                $newProject->project_Id = $gp->project_id;
                $newProject->client_name = $gp->client_name;
                $newProject->project_name = $baseName; // Use base name, not general project name
                $newProject->department = $gp->department;
                $newProject->clientpm = $gp->clientpm;
                $newProject->projectpm = $gp->projectpm;
                $newProject->pm_name = $pmName;
                $newProject->status = $gp->status;
                $newProject->man_days = $gp->man_days;
                $newProject->project_start_date = $gp->project_start_date;
                $newProject->project_end_date = $gp->project_end_date;
                $newProject->project_invoice_amt = isset($gp->project_invoice_amt) ? $gp->project_invoice_amt : null;
                $newProject->total_hours = 0; // No production hours
                $newProject->general_hours = $generalHours; // Show general hours
                
                $newProject->qty_project_Id = null;
                $newProject->analyzer_num_of_errors = 0;
                $newProject->reviewer_num_of_errors = 0;
                $newProject->analyzer_report_date = null;
                
                // Get last work date from general project
                $this->db->select('MAX(emp_report_dates) as last_work_date');
                $this->db->from('emp_record_details');
                $this->db->where('project_id', $gp->project_id);
                $this->db->where('client_id', $gp->client_id);
                
                if (!empty($from_date) && !empty($to_date)) {
                    $this->db->where('emp_report_dates >=', $from_date);
                    $this->db->where('emp_report_dates <=', $to_date);
                } else {
                    $this->db->where('YEAR(emp_report_dates)', $queryYear);
                    $this->db->where('MONTH(emp_report_dates) >=', 1);
                    $this->db->where('MONTH(emp_report_dates) <=', $endMonth);
                }
                
                $lastDateResult = $this->db->get()->row();
                $newProject->last_work_date = $lastDateResult && !empty($lastDateResult->last_work_date) ? $lastDateResult->last_work_date : null;
                
                // Track that we've shown this general project
                $shownGeneralProjectIds[$generalProjectKey] = true;
                
                // Add to projects array
                $projects[] = $newProject;
            }
        }
    }

    
    if (!empty($monthName) && is_numeric($monthName) && $monthName >= 1 && $monthName <= 12) {
    $year = date('Y');
    $endMonth = (int)$monthName;

    if ($endMonth == 12) {
        $year -= 1;
    }

    foreach ($projects as &$project) {
        $project->general_hours = $this->getGeneralHoursForProjectConsolidated(
            $project->project_name,
            $year,
            $endMonth
        );
    }
}


    return $projects;
}  


//func for dropdown auto suggest - consolidated KPI (managers only)
public function get_employee_suggestions_cons($term) {
    $term = trim((string) $term);
    $results = array();
    foreach ($this->get_project_manager_suggestion_name_rows($term) as $row) {
        $name = isset($row['name']) ? trim((string) $row['name']) : '';
        if ($name !== '') {
            $results[] = $name;
        }
    }
    $results = array_values(array_unique($results));
    $limit = ($term === '') ? 500 : 20;
    return array_slice($results, 0, $limit);
}
  

//func for dropdown auto suggest - monthly - arch - kpi           
public function get_employee_suggestions_arch($term){
    $departmentsToInclude = ['Architectural', 'Structural', '3D Visualization'];
    
    $this->db->select('name');
    $this->db->from('employee_details');
    $this->db->like('name', $term, 'both');
    $this->db->where('status', 'Active');
    
    // Filter by the specified departments
    $this->db->group_start();
    foreach ($departmentsToInclude as $index => $dept) {
        if ($index === 0) {
            $this->db->where('department', $dept);
        } else {
            $this->db->or_where('department', $dept);
        }
    }
    $this->db->group_end();
    
    $this->db->limit(5); 
    
    $query = $this->db->get();
    return array_column($query->result_array(), 'name');
}
     
  
//func for dropdown auto suggest - monthly - mep - kpi        
public function get_employee_suggestions($term){
    $departmentsToInclude = ['MEP'];
    
    $this->db->select('name');
    $this->db->from('employee_details');
    $this->db->like('name', $term, 'both');
    $this->db->where('status', 'Active');
    
    
    $this->db->group_start();
    foreach ($departmentsToInclude as $index => $dept) {
        if ($index === 0) {
            $this->db->where('department', $dept);
        } else {
            $this->db->or_where('department', $dept);
        }
    }
    $this->db->group_end();
    
    $this->db->limit(5); 
    
    $query = $this->db->get();
    return array_column($query->result_array(), 'name');
}    
    
    
/** Total active clients matching clientReport suggest filters (same as client dropdown list). */
public function count_active_clients_for_suggest() {
    $this->db->reset_query();
    $this->db->from('client_details');
    $this->db->where('status', 'Active');
    $this->db->where("client_name != ''", null, false);
    $this->db->where("client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
    return (int) $this->db->count_all_results();
}

/**
 * Names for the "Managers" autosuggest group: project PMs (project_details.empId),
 * client/account managers (client_details.empId), and anyone listed as another employee's reporting_manger.
 *
 * @param string $term optional LIKE filter on name
 * @return array
 */
private function get_project_manager_suggestion_name_rows($term = '')
{
        $term = trim((string)$term);
        $managerExcludeNames = array('eLogic Timesheet', 'Jaishri', 'Laxmikanth', 'Rahul Kumar', 'Rupali', 'Shirley', 'Shirely', 'Suman', 'Farhan');
        $names = array();

        $applyNameFilters = function () use ($term, $managerExcludeNames) {
            if ($term !== '') {
                $this->db->like('ed.name', $term, 'both');
            }
            foreach ($managerExcludeNames as $ex) {
                $this->db->not_like('ed.name', $ex, 'both');
            }
        };

        $this->db->distinct();
        $this->db->select('ed.name');
        $this->db->from('employee_details ed');
        $this->db->join('project_details pd', 'pd.empId = ed.empId', 'inner');
        $this->db->where('ed.status', 'Active');
        $this->db->where("ed.name != ''", null, false);
        $this->db->where("pd.project_name NOT LIKE '%(General)%' ESCAPE '!'", null, false);
        $applyNameFilters();
        $this->db->order_by('ed.name', 'asc');
        foreach ($this->db->get()->result_array() as $r) {
            $n = trim((string)(isset($r['name']) ? $r['name'] : ''));
            if ($n !== '') {
                $names[$n] = true;
            }
        }
        $this->db->reset_query();

        $this->db->distinct();
        $this->db->select('ed.name');
        $this->db->from('employee_details ed');
        $this->db->join('client_details cd', 'cd.empId = ed.empId', 'inner');
        $this->db->where('cd.status', 'Active');
        $this->db->where('ed.status', 'Active');
        $this->db->where("ed.name != ''", null, false);
        $this->db->where("cd.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'", null, false);
        $applyNameFilters();
        $this->db->order_by('ed.name', 'asc');
        foreach ($this->db->get()->result_array() as $r) {
            $n = trim((string)(isset($r['name']) ? $r['name'] : ''));
            if ($n !== '') {
                $names[$n] = true;
            }
        }
        $this->db->reset_query();

        $this->db->distinct();
        $this->db->select('ed.name');
        $this->db->from('employee_details ed');
        $this->db->where('ed.status', 'Active');
        $this->db->where("ed.name != ''", null, false);
        $this->db->where('ed.empId IN (SELECT DISTINCT rm.reporting_manger FROM employee_details rm WHERE rm.reporting_manger IS NOT NULL AND rm.reporting_manger != \'\' AND rm.reporting_manger != \'0\')', null, false);
        $applyNameFilters();
        $this->db->order_by('ed.name', 'asc');
        foreach ($this->db->get()->result_array() as $r) {
            $n = trim((string)(isset($r['name']) ? $r['name'] : ''));
            if ($n !== '') {
                $names[$n] = true;
            }
        }
        $this->db->reset_query();

        $excludeManagerDropdownExact = array('hemanth kmv');
        $excludeManagerDropdownContains = array('shirely', 'shirley', 'suman', 'farhan');
        $normalizeManagerDropdownName = function ($name) {
            return strtolower(trim(preg_replace('/\s+/u', ' ', (string)$name)));
        };
        $isExcludedManagerDropdownName = function ($name) use ($excludeManagerDropdownExact, $excludeManagerDropdownContains, $normalizeManagerDropdownName) {
            $norm = $normalizeManagerDropdownName($name);
            if ($norm === '') {
                return true;
            }
            if (in_array($norm, $excludeManagerDropdownExact, true)) {
                return true;
            }
            foreach ($excludeManagerDropdownContains as $fragment) {
                if ($fragment !== '' && strpos($norm, $fragment) !== false) {
                    return true;
                }
            }
            return false;
        };

        $keys = array_keys($names);
        $keys = array_values(array_filter($keys, function ($n) use ($isExcludedManagerDropdownName) {
            return !$isExcludedManagerDropdownName($n);
        }));
        usort($keys, 'strcasecmp');

        $out = array();
        foreach ($keys as $n) {
            $out[] = array('name' => $n);
        }
        return $out;
    }

/**
 * Client report: clients that have at least one non-general project, optionally scoped by delivery department.
 *
 * @param string $term
 * @param array $departments
 * @return array<int, array{label: string, value: string, category: string}>
 */
public function get_client_report_client_suggestions($term, $departments = array())
{
    $term = trim((string)$term);
    $deps = array();
    if (is_array($departments)) {
        foreach ($departments as $d) {
            $d = trim((string)$d);
            if ($d !== '' && strcasecmp($d, '__all__') !== 0) {
                $deps[] = $d;
            }
        }
    }

    $this->db->distinct();
    $this->db->select('cd.client_name as name');
    $this->db->from('client_details cd');
    $this->db->join('project_details pd', 'pd.client_Id = cd.client_Id', 'inner');
    $this->db->where('cd.status', 'Active');
    $this->db->where("cd.client_name != ''", null, false);
    $this->db->where("cd.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'", null, false);
    $this->db->where("pd.project_name NOT LIKE '%(General)%' ESCAPE '!'", null, false);
    $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) IS NOT NULL", null, false);
    $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) !=", '');
    if (!empty($deps)) {
        $this->db->where_in("COALESCE(NULLIF(pd.project_type, ''), cd.department)", $deps);
    }
    if ($term !== '') {
        $this->db->like('cd.client_name', $term, 'both');
    }
    $this->db->order_by('cd.client_name', 'asc');
    $this->db->limit(($term === '') ? 500 : 80);
    $rows = $this->db->get()->result_array();
    $this->db->reset_query();

    $out = array();
    foreach ($rows as $r) {
        $n = trim((string)(isset($r['name']) ? $r['name'] : ''));
        if ($n === '') {
            continue;
        }
        $out[] = array('label' => $n, 'value' => $n, 'category' => 'Clients');
    }
    return $out;
}

/**
 * Distinct client count for client-report client autosuggest (same scope as get_client_report_client_suggestions).
 *
 * @param array $departments
 * @return int
 */
public function count_client_report_clients_for_suggest($departments = array())
{
    $deps = array();
    if (is_array($departments)) {
        foreach ($departments as $d) {
            $d = trim((string)$d);
            if ($d !== '' && strcasecmp($d, '__all__') !== 0) {
                $deps[] = $d;
            }
        }
    }

    $this->db->select('COUNT(DISTINCT cd.client_Id) AS cnt', false);
    $this->db->from('client_details cd');
    $this->db->join('project_details pd', 'pd.client_Id = cd.client_Id', 'inner');
    $this->db->where('cd.status', 'Active');
    $this->db->where("cd.client_name != ''", null, false);
    $this->db->where("cd.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'", null, false);
    $this->db->where("pd.project_name NOT LIKE '%(General)%' ESCAPE '!'", null, false);
    $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) IS NOT NULL", null, false);
    $this->db->where("COALESCE(NULLIF(pd.project_type, ''), cd.department) !=", '');
    if (!empty($deps)) {
        $this->db->where_in("COALESCE(NULLIF(pd.project_type, ''), cd.department)", $deps);
    }
    $row = $this->db->get()->row();
    $this->db->reset_query();
    return $row && isset($row->cnt) ? (int)$row->cnt : 0;
}

/**
 * Client report: projects for selected client names (optional), excluding general projects.
 *
 * @param string $term
 * @param array $clientNames client_details.client_name
 * @return array<int, array{label: string, value: string, category: string}>
 */
public function get_client_report_project_suggestions($term, $clientNames = array())
{
    $term = trim((string)$term);
    $clients = array();
    if (is_array($clientNames)) {
        foreach ($clientNames as $c) {
            $c = trim((string)$c);
            if ($c !== '') {
                $clients[] = $c;
            }
        }
    }

    $this->db->distinct();
    $this->db->select('pd.project_name as name');
    $this->db->from('project_details pd');
    $this->db->join('client_details cd', 'cd.client_Id = pd.client_Id', 'inner');
    $this->db->where('cd.status', 'Active');
    $this->db->where("cd.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'", null, false);
    $this->db->where("pd.project_name NOT LIKE '%(General)%' ESCAPE '!'", null, false);
    if (!empty($clients)) {
        $this->db->where_in('cd.client_name', $clients);
    }
    if ($term !== '') {
        $this->db->like('pd.project_name', $term, 'both');
    }
    $this->db->order_by('pd.project_name', 'asc');
    $this->db->limit(($term === '') ? 500 : 80);
    $rows = $this->db->get()->result_array();
    $this->db->reset_query();

    $out = array();
    foreach ($rows as $r) {
        $n = trim((string)(isset($r['name']) ? $r['name'] : ''));
        if ($n === '') {
            continue;
        }
        $out[] = array('label' => $n, 'value' => $n, 'category' => 'Projects');
    }
    return $out;
}

/**
 * @param string $term
 * @return array<int, array{label: string, value: string, category: string}>
 */
public function get_client_report_manager_suggestions_formatted($term = '')
{
    $out = array();
    foreach ($this->get_project_manager_suggestion_name_rows($term) as $r) {
        $n = trim((string)(isset($r['name']) ? $r['name'] : ''));
        if ($n === '') {
            continue;
        }
        $out[] = array('label' => $n, 'value' => $n, 'category' => 'Managers');
    }
    return $out;
}

//func for dropdown auto suggest - client (returns rows: label, value, category)
public function get_project_suggestions($term){
    $term = trim((string) $term);
    if ($term === '') {
        return $this->get_project_suggestions_browse();
    }

    $seen = array();
    $rows = array();
    $push = function ($name, $category) use (&$seen, &$rows) {
        $name = trim((string) $name);
        if ($name === '' || isset($seen[$name])) {
            return;
        }
        $seen[$name] = true;
        $rows[] = array('label' => $name, 'value' => $name, 'category' => $category);
    };

    $this->db->select('client_name as name');
    $this->db->from('client_details');
    $this->db->like('client_name', $term, 'both');
    $this->db->where('status', 'Active');
    $this->db->where("client_details.client_name != ''");
    $this->db->where("client_details.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
    $this->db->order_by('client_name', 'asc');
    foreach ($this->db->get()->result_array() as $r) {
        $push($r['name'], 'Clients');
    }

    $this->db->select('project_name as name');
    $this->db->from('project_details');
    $this->db->like('project_name', $term, 'both');
    $this->db->where("project_name NOT LIKE '% (General)' ESCAPE '!'");
    $this->db->order_by('project_name', 'asc');
    foreach ($this->db->get()->result_array() as $r) {
        $push($r['name'], 'Projects');
    }

    foreach ($this->get_project_manager_suggestion_name_rows($term) as $r) {
        $push($r['name'], 'Managers');
    }

    return array_slice($rows, 0, 14);
}

/** Browse list grouped by Clients / Projects / Employees */
private function get_project_suggestions_browse() {
    $seen = array();
    $rows = array();
    $push = function ($name, $category) use (&$seen, &$rows) {
        $name = trim((string) $name);
        if ($name === '' || isset($seen[$name])) {
            return;
        }
        $seen[$name] = true;
        $rows[] = array('label' => $name, 'value' => $name, 'category' => $category);
    };

    $this->db->select('client_name as name');
    $this->db->from('client_details');
    $this->db->where('status', 'Active');
    $this->db->where("client_name != ''");
    $this->db->where("client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'");
    $this->db->order_by('client_name', 'asc');
    foreach ($this->db->get()->result_array() as $r) {
        $push($r['name'], 'Clients');
    }

    $this->db->select('project_name as name');
    $this->db->from('project_details');
    $this->db->where("project_name NOT LIKE '% (General)' ESCAPE '!'");
    $this->db->order_by('project_name', 'asc');
    foreach ($this->db->get()->result_array() as $r) {
        $push($r['name'], 'Projects');
    }

    foreach ($this->get_project_manager_suggestion_name_rows('') as $r) {
        $push($r['name'], 'Managers');
    }

    return $rows;
}
    
      
//func for production hours - monthly - client  
public function projectProductionHoursMonthWise($projectId , $monthId){        
    
    $currentYear = date('Y');

    if ($monthId == 12) {
        $previousMonthYear = $currentYear - 1;
    } else {
        $previousMonthYear = $currentYear;
    }
    
    if (!empty($projectId)) {
        $queryProjectHours = $this->db
            ->select('SUM(emp_record_details.emp_time_hours) as total_hours, emp_record_details.project_Id')
            ->from('emp_record_details')
            ->join('project_details', 'project_details.project_Id = emp_record_details.project_Id')
            ->where('emp_record_details.project_Id', $projectId)
            ->where('MONTH(emp_record_details.emp_report_dates)', $monthId)
            ->where('YEAR(emp_record_details.emp_report_dates)', $previousMonthYear) 
            ->get()
            ->result(); 
        return $queryProjectHours;
    }

    return []; 
}

     
//func for general hours - monthly - client   
public function getGeneralHoursForProject($projectName, $monthId) {
   
    $currentYear = date('Y');

    if ($monthId == 12) {
        $previousMonthYear = $currentYear - 1;
    } else {
        $previousMonthYear = $currentYear;
    }
    
    $this->db->select('project_id');
    $this->db->from('project_details');
    $this->db->like('project_name', $projectName); 
    $this->db->like('project_name', 'General', 'both');
    $this->db->limit(1);

    $result = $this->db->get()->row();

    if ($result && isset($result->project_id)) {
        $generalProjectId = $result->project_id;

        $this->db->select('SUM(emp_time_hours) as total_general_hours');
        $this->db->from('emp_record_details');
        $this->db->where('project_id', $generalProjectId);
        $this->db->where('MONTH(emp_report_dates)', $monthId);
        $this->db->where('YEAR(emp_report_dates)', $previousMonthYear);

        $hoursResult = $this->db->get()->row();

        if ($hoursResult && isset($hoursResult->total_general_hours)) {
            return $hoursResult->total_general_hours;
        }
    }

    return 0;
}
    

//func for production hours - consolidated - client     
public function getconsprodHoursForProject($projectId, $year, $endMonth) {
    

//    echo "Year: " . $year . "<br>";
//    echo "End Month: " . $endMonth . "<br>";    
    
    $this->db->select('SUM(emp_time_hours) as total_productive_hours');
    $this->db->from('emp_record_details');
    $this->db->where('project_Id', $projectId);
    $this->db->where('YEAR(emp_report_dates)', $year);
    $this->db->where('MONTH(emp_report_dates) BETWEEN 1 AND '.$endMonth);
    $query = $this->db->get();
   $result = $query->row();
    return $result ? (float)$result->total_productive_hours : 0;
}
    
  
//func for general hours - consolidated - client   
private function getGeneralHoursForProjectConsolidated($projectName, $year, $endMonth) {
    
    $this->db->select('project_id');
    $this->db->from('project_details');
    $this->db->like('project_name', $projectName); 
    $this->db->like('project_name', 'General', 'both');
    $this->db->limit(1);

    $result = $this->db->get()->row();

    if (!$result || !isset($result->project_id)) {
        return 0;
    }

    $generalProjectId = $result->project_id;

    
    $this->db->select('SUM(emp_time_hours) as total_general_hours');
    $this->db->from('emp_record_details');
    $this->db->where('project_id', $generalProjectId);
    $this->db->where('YEAR(emp_report_dates)', $year);
    $this->db->where('MONTH(emp_report_dates) BETWEEN 1 AND '.$endMonth);
    
    $hoursResult = $this->db->get()->row();
    
    if ($hoursResult && isset($hoursResult->total_general_hours)) {
            return $hoursResult->total_general_hours;
        }
    }
    
    // Validation function to check if quality error logs exist for selected months/dates
    // Returns true if quality error logs exist (even with zero errors), false if none exist
    public function validateQualityErrorLogDates($monthName = '', $from_date = '', $to_date = '') {
        $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
        $empId = $this->session->userdata['logged_in_timesheet']['empId'];
        
        $mepManagers = ['146', '230', '149','455'];
        $arcManagers = ['41', '394' , '270','47', '182', '71', '53', '155'];
        $isMEPManager = in_array($empId, $mepManagers);
        $isARCManager = in_array($empId, $arcManagers);
        
        // Build query to check if quality error logs exist for the selected dates
        // Join directly with client_details (quality_error_log has qty_client_Id) and project_details for filters
        $this->db->select('COUNT(quality_error_log.qty_error_id) as log_count')
            ->from('quality_error_log')
            ->join('client_details', 'client_details.client_Id = quality_error_log.qty_client_Id', 'inner')
            ->join('project_details', 'project_details.project_Id = quality_error_log.qty_project_Id', 'inner')
            ->where('client_details.status', 'Active')
           // ->where('project_details.status', 'process')
            ->where("client_details.client_name != ''")
            ->where('client_details.department IS NOT NULL')
            ->where('client_details.department !=', '')
            ->where("client_details.client_name NOT LIKE '%eLogic Solutions%' ESCAPE '!'")
            ->where("project_details.project_name NOT LIKE '%(General)%'")
            ->where('quality_error_log.analyzer_report_date IS NOT NULL')
            ->where("quality_error_log.analyzer_report_date != ''");
        
        // Apply user-specific filters
        if ($userType == 'admin' || $empId == '140') {
            $this->db->where_in('client_details.department', ['MEP', '3D Visualization', 'Architectural', 'Structural']);
        } elseif ($empId == '149' || $isMEPManager) {
            $this->db->where('client_details.department', 'MEP');
        } elseif ($empId == '47' || $isARCManager) {
            $this->db->where_in('client_details.department', ['Architectural', 'Structural', '3D Visualization']);
        }
        
        // Apply date filters based on monthName or date range - filter by analyzer_report_date
        if (!empty($monthName)) {
            // Handle multiple months or single month
            if (!is_array($monthName)) {
                $monthName = [$monthName];
            }
            
            $currentYear = date('Y');
            $validMonths = array_filter($monthName, function($month) {
                return is_numeric($month) && $month >= 1 && $month <= 12;
            });
            
            if (!empty($validMonths)) {
                $this->db->group_start();
                foreach ($validMonths as $month) {
                    $previousMonthYear = ($month == 12) ? ($currentYear - 1) : $currentYear;
                    $this->db->or_group_start();
                    $this->db->where('MONTH(quality_error_log.analyzer_report_date)', $month);
                    $this->db->where('YEAR(quality_error_log.analyzer_report_date)', $previousMonthYear);
                    $this->db->group_end();
                }
                $this->db->group_end();
            }
        } elseif (!empty($from_date) && !empty($to_date)) {
            // Date range filter - filter by analyzer_report_date
            $this->db->where('quality_error_log.analyzer_report_date >=', $from_date);
            $this->db->where('quality_error_log.analyzer_report_date <=', $to_date);
        }
        // If no date filters provided, show all data (no date filter applied)
        
        $query = $this->db->get();
        $result = $query->row();
        
        // Return true if quality error logs exist OR if no date filters (show all data)
        // Return false only if date filters are provided and no quality error logs exist for those dates
        if (empty($monthName) && empty($from_date) && empty($to_date)) {
            // No filters - allow showing all data
            return true;
        }
        
        return ($result && $result->log_count > 0);
    }
    
    
}
    
     


