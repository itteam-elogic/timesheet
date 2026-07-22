<?php
/**
 * Employee timesheet inactivity report (no entries in last 6 months).
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Emp_record_inactivity_model extends CI_Model {

    private static $indexesEnsured = false;

    public function __construct() {
        parent::__construct();
        $this->ensurePerformanceIndexes();
    }

    private function buildValidReportDateSql($alias = 'erd') {
        return "{$alias}.emp_report_dates IS NOT NULL
            AND {$alias}.emp_report_dates > '1970-01-01'
            AND {$alias}.emp_report_dates != '0000-00-00'";
    }

    /**
     * One-time index creation to speed up MAX(emp_report_dates) lookups.
     */
    private function ensurePerformanceIndexes() {
        if (self::$indexesEnsured) {
            return;
        }
        self::$indexesEnsured = true;

        $indexes = array(
            'idx_erd_inactivity_lookup' => 'CREATE INDEX idx_erd_inactivity_lookup ON emp_record_details (client_Id, project_Id, empId, emp_report_dates)',
            'idx_erd_emp_report_dates' => 'CREATE INDEX idx_erd_emp_report_dates ON emp_record_details (empId, emp_report_dates)',
        );

        foreach ($indexes as $indexName => $createSql) {
            $exists = $this->db->query(
                "SELECT 1 FROM information_schema.statistics
                 WHERE table_schema = DATABASE()
                   AND table_name = 'emp_record_details'
                   AND index_name = ?
                 LIMIT 1",
                array($indexName)
            )->num_rows() > 0;

            if (!$exists) {
                @$this->db->query($createSql);
            }
        }
    }

    public function getDefaultFromDate() {
        return date('Y-m-01', strtotime('-6 months'));
    }

    public function getDefaultToDate() {
        return date('Y-m-t');
    }

    /**
     * Convert year/month filter values to from_date and to_date (inclusive).
     * "All" on year uses a rolling 6-month inactivity window (not 2010–today).
     */
    public function resolveYearMonthDateRange($fromYear, $fromMonth, $toYear, $toMonth) {
        $defaultFrom = $this->getDefaultFromDate();
        $defaultTo = $this->getDefaultToDate();
        $startYear = 2010;
        $endYear = (int)date('Y');
        $currentMonth = (int)date('n');

        $fromYear = trim((string)$fromYear);
        $fromMonth = trim((string)$fromMonth);
        $toYear = trim((string)$toYear);
        $toMonth = trim((string)$toMonth);

        $fromYearIsAll = (strtoupper($fromYear) === 'ALL');
        $toYearIsAll = (strtoupper($toYear) === 'ALL');
        $hasAny = ($fromYear !== '' || $fromMonth !== '' || $toYear !== '' || $toMonth !== '');

        if (!$hasAny) {
            return array(
                'from_date' => $defaultFrom,
                'to_date' => $defaultTo,
                'from_year' => 'ALL',
                'from_month' => '',
                'to_year' => 'ALL',
                'to_month' => '',
            );
        }

        if ($toYearIsAll) {
            $toDate = $defaultTo;
            $uiToYear = 'ALL';
            $uiToMonth = '';
        } elseif ($toYear !== '' && (int)$toYear > 0) {
            $ty = max($startYear, min($endYear, (int)$toYear));
            $tm = ($toMonth !== '' && (int)$toMonth >= 1 && (int)$toMonth <= 12)
                ? (int)$toMonth
                : (($ty === $endYear) ? $currentMonth : 12);
            $toDate = date('Y-m-t', mktime(0, 0, 0, $tm, 1, $ty));
            $uiToYear = (string)$ty;
            $uiToMonth = ($toMonth !== '') ? (string)$tm : '';
        } else {
            $toDate = $defaultTo;
            $uiToYear = '';
            $uiToMonth = '';
        }

        if ($fromYearIsAll) {
            $fromDate = date('Y-m-01', strtotime('-6 months', strtotime($toDate)));
            $uiFromYear = 'ALL';
            $uiFromMonth = '';
        } elseif ($fromYear !== '' && (int)$fromYear > 0) {
            $fy = max($startYear, min($endYear, (int)$fromYear));
            $fm = ($fromMonth !== '' && (int)$fromMonth >= 1 && (int)$fromMonth <= 12)
                ? (int)$fromMonth
                : 1;
            $fromDate = sprintf('%04d-%02d-01', $fy, $fm);
            $uiFromYear = (string)$fy;
            $uiFromMonth = ($fromMonth !== '') ? (string)$fm : '';
        } else {
            $fromDate = date('Y-m-01', strtotime('-6 months', strtotime($toDate)));
            $uiFromYear = $fromYear;
            $uiFromMonth = $fromMonth;
        }

        if (strtotime($fromDate) > strtotime($toDate)) {
            $tmp = $fromDate;
            $fromDate = $toDate;
            $toDate = $tmp;
            $tmpY = $uiFromYear;
            $tmpM = $uiFromMonth;
            $uiFromYear = $uiToYear;
            $uiFromMonth = $uiToMonth;
            $uiToYear = $tmpY;
            $uiToMonth = $tmpM;
        }

        return array(
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'from_year' => $uiFromYear,
            'from_month' => $uiFromMonth,
            'to_year' => $uiToYear,
            'to_month' => $uiToMonth,
        );
    }

    /**
     * Cached filter dropdowns (10 min) to avoid 4 queries on every page load.
     */
    public function getFilterDropdownData($clientId = '') {
        $clientId = trim((string)$clientId);
        $cacheKey = 'eri_filter_dropdowns_' . ($clientId !== '' ? $clientId : 'all');
        $cached = $this->session->userdata($cacheKey);

        if (is_array($cached) && isset($cached['expires']) && $cached['expires'] > time()) {
            return $cached['data'];
        }

        $data = array(
            'clients' => $this->getActiveClients(),
            'projects' => $this->getProjectsByClient($clientId),
            'departments' => $this->getDepartmentsList(),
            'reportingManagers' => $this->getReportingManagersList(),
            'projectStatuses' => $this->getProjectStatuses(),
        );

        $this->session->set_userdata($cacheKey, array(
            'expires' => time() + 600,
            'data' => $data,
        ));

        return $data;
    }

    /**
     * Projects with no timesheet log in last 6 months (by project_Id only, not per employee).
     * Uses emp_record_details.emp_report_dates joined to project_details on project_Id.
     */
    public function getInactiveTimesheetRecords($filters = array()) {
        $cutoffDate = !empty($filters['from_date']) ? $filters['from_date'] : $this->getDefaultFromDate();
        $toDate = !empty($filters['to_date']) ? $filters['to_date'] : $this->getDefaultToDate();
        $clientId = isset($filters['client_Id']) ? trim((string)$filters['client_Id']) : '';
        $projectId = isset($filters['project_Id']) ? trim((string)$filters['project_Id']) : '';
        $department = isset($filters['department']) ? trim((string)$filters['department']) : '';
        $projectStatus = isset($filters['project_status']) ? trim((string)$filters['project_status']) : '';

        $validDateSql = $this->buildValidReportDateSql('erd');
        $recentValidDateSql = $this->buildValidReportDateSql('erd_recent');
        $deptExpr = "COALESCE(NULLIF(TRIM(p.project_type), ''), c.department)";
        $where = array('1=1');
        $params = array();

        if ($department !== '') {
            $where[] = "{$deptExpr} = ?";
            $params[] = $department;
        }
        if ($clientId !== '') {
            $where[] = 'p.client_Id = ?';
            $params[] = (int)$clientId;
        }
        if ($projectId !== '') {
            $where[] = 'p.project_Id = ?';
            $params[] = (int)$projectId;
        }
        if ($projectStatus !== '') {
            $where[] = 'p.status = ?';
            $params[] = $projectStatus;
        }

        // Inactive = no emp_report_dates in [from_date, to_date] for this project_Id.
        // Also accept projects whose last log is before from_date (no recent activity).
        $sql = "
            SELECT
                p.project_Id,
                p.client_Id,
                c.client_name,
                {$deptExpr} AS department,
                p.project_name,
                p.status AS project_status,
                p.project_start_date,
                p.project_end_date,
                pl.emp_report_dates,
                DATEDIFF(CURDATE(), pl.emp_report_dates) AS days_since_last_entry
            FROM project_details p
            INNER JOIN client_details c ON c.client_Id = p.client_Id
            LEFT JOIN (
                SELECT
                    erd.project_Id,
                    MAX(DATE(erd.emp_report_dates)) AS emp_report_dates
                FROM emp_record_details erd
                WHERE {$validDateSql}
                GROUP BY erd.project_Id
            ) pl ON pl.project_Id = p.project_Id
            WHERE " . implode(' AND ', $where) . "
              AND pl.emp_report_dates IS NOT NULL
              AND DATE(pl.emp_report_dates) < DATE(?)
              AND NOT EXISTS (
                    SELECT 1
                    FROM emp_record_details erd_recent
                    WHERE erd_recent.project_Id = p.project_Id
                      AND {$recentValidDateSql}
                      AND DATE(erd_recent.emp_report_dates) >= DATE(?)
                      AND DATE(erd_recent.emp_report_dates) <= DATE(?)
              )
            ORDER BY c.client_name ASC, p.project_name ASC
        ";

        $params[] = $cutoffDate;
        $params[] = $cutoffDate;
        $params[] = $toDate;

        $records = $this->db->query($sql, $params)->result();
        return $this->excludeRecentlyActiveProjects($records, $cutoffDate, $toDate);
    }

    /**
     * Drop projects that still have emp_report_dates within the date range (project_Id only).
     */
    private function excludeRecentlyActiveProjects($records, $cutoffDate, $toDate = null) {
        if (empty($records)) {
            return $records;
        }

        if ($toDate === null) {
            $toDate = $this->getDefaultToDate();
        }

        $validDateSql = $this->buildValidReportDateSql('erd');
        $recentRows = $this->db->query("
            SELECT DISTINCT erd.project_Id
            FROM emp_record_details erd
            WHERE {$validDateSql}
              AND DATE(erd.emp_report_dates) >= DATE(" . $this->db->escape($cutoffDate) . ")
              AND DATE(erd.emp_report_dates) <= DATE(" . $this->db->escape($toDate) . ")
        ")->result();

        if (empty($recentRows)) {
            return $records;
        }

        $activeProjectIds = array();
        foreach ($recentRows as $row) {
            $activeProjectIds[(string)$row->project_Id] = true;
        }

        $filtered = array();
        foreach ($records as $record) {
            if (!empty($activeProjectIds[(string)$record->project_Id])) {
                continue;
            }
            if (!empty($record->emp_report_dates)) {
                $entryTs = strtotime($record->emp_report_dates);
                if ($entryTs !== false
                    && $entryTs >= strtotime($cutoffDate)
                    && $entryTs <= strtotime($toDate . ' 23:59:59')) {
                    continue;
                }
            }
            $filtered[] = $record;
        }

        return $filtered;
    }

    /**
     * @deprecated Use excludeRecentlyActiveProjects()
     */
    private function excludeRecentlyActiveRecords($records, $cutoffDate) {
        return $this->excludeRecentlyActiveProjects($records, $cutoffDate);
    }

    /**
     * Per client: employees with no timesheet on a project name (optional checkbox).
     */
    public function getClientProjectNeverEnteredRecords($filters = array()) {
        $clientId = isset($filters['client_Id']) ? trim((string)$filters['client_Id']) : '';
        if ($clientId === '') {
            return array();
        }

        $fromDate = !empty($filters['from_date']) ? $filters['from_date'] : $this->getDefaultFromDate();
        $projectId = isset($filters['project_Id']) ? trim((string)$filters['project_Id']) : '';
        $reportingManager = isset($filters['reporting_manager']) ? trim((string)$filters['reporting_manager']) : '';
        $employeeName = isset($filters['employee_name']) ? trim((string)$filters['employee_name']) : '';
        $projectStatus = isset($filters['project_status']) ? trim((string)$filters['project_status']) : '';

        $validDateSql = $this->buildValidReportDateSql('erd');
        $validDateLoggedSql = $this->buildValidReportDateSql('erd_logged');

        $params = array((int)$clientId);
        $projectFilterSql = '';
        if ($projectId !== '') {
            $projectFilterSql = ' AND cp.project_Id = ?';
            $params[] = (int)$projectId;
        }
        if ($projectStatus !== '') {
            $projectFilterSql .= ' AND cp.status = ?';
            $params[] = $projectStatus;
        }

        $params[] = $fromDate;

        $outerWhere = array("e.status = 'Active'");
        if ($reportingManager !== '') {
            $outerWhere[] = '(e.reporting_manger = ? OR e.empId = ?)';
            $params[] = $reportingManager;
            $params[] = $reportingManager;
        }
        if ($employeeName !== '') {
            $outerWhere[] = 'e.name LIKE ?';
            $params[] = '%' . $employeeName . '%';
        }

        $roleSql = $this->buildRoleVisibilitySql($params);
        if ($roleSql !== '') {
            $outerWhere[] = $roleSql;
        }

        $sql = "
            SELECT
                e.empId,
                e.name AS employee_name,
                e.emp_com_id,
                mgr.name AS reporting_manager,
                c.client_Id,
                c.client_name,
                COALESCE(NULLIF(p.project_type, ''), c.department) AS department,
                p.project_Id,
                p.project_name,
                p.status AS project_status,
                p.project_start_date,
                p.project_end_date,
                NULL AS emp_report_dates,
                NULL AS days_since_last_entry
            FROM (
                SELECT DISTINCT ce.empId, cp.client_Id, cp.project_Id
                FROM (
                    SELECT DISTINCT erd.empId, erd.client_Id
                    FROM emp_record_details erd
                    WHERE {$validDateSql}
                      AND erd.client_Id = ?
                ) ce
                INNER JOIN project_details cp ON cp.client_Id = ce.client_Id
                {$projectFilterSql}
                LEFT JOIN (
                    SELECT DISTINCT
                        erd_logged.empId,
                        erd_logged.client_Id,
                        TRIM(p_logged.project_name) AS project_name
                    FROM emp_record_details erd_logged
                    INNER JOIN project_details p_logged
                        ON p_logged.project_Id = erd_logged.project_Id
                        AND p_logged.client_Id = erd_logged.client_Id
                    WHERE {$validDateLoggedSql}
                ) logged ON logged.empId = ce.empId
                    AND logged.client_Id = cp.client_Id
                    AND logged.project_name = TRIM(cp.project_name)
                LEFT JOIN (
                    SELECT DISTINCT
                        erd_recent.empId,
                        erd_recent.client_Id,
                        TRIM(p_recent.project_name) AS project_name
                    FROM emp_record_details erd_recent
                    INNER JOIN project_details p_recent
                        ON p_recent.project_Id = erd_recent.project_Id
                        AND p_recent.client_Id = erd_recent.client_Id
                    WHERE {$this->buildValidReportDateSql('erd_recent')}
                      AND DATE(erd_recent.emp_report_dates) >= DATE(?)
                ) recent ON recent.empId = ce.empId
                    AND recent.client_Id = cp.client_Id
                    AND recent.project_name = TRIM(cp.project_name)
                WHERE logged.empId IS NULL
                  AND recent.empId IS NULL
            ) missing
            INNER JOIN employee_details e ON e.empId = missing.empId
            LEFT JOIN employee_details mgr ON mgr.empId = e.reporting_manger
            INNER JOIN client_details c ON c.client_Id = missing.client_Id
            INNER JOIN project_details p ON p.project_Id = missing.project_Id AND p.client_Id = missing.client_Id
            WHERE " . implode(' AND ', $outerWhere) . "
            ORDER BY c.client_name ASC, p.project_name ASC, e.name ASC
        ";

        return $this->db->query($sql, $params)->result();
    }

    public function getEmployeesNeverEntered($filters = array()) {
        $reportingManager = isset($filters['reporting_manager']) ? trim((string)$filters['reporting_manager']) : '';
        $employeeName = isset($filters['employee_name']) ? trim((string)$filters['employee_name']) : '';

        $where = array(
            "e.status = 'Active'",
            "NOT EXISTS (SELECT 1 FROM emp_record_details erd_ne WHERE erd_ne.empId = e.empId LIMIT 1)",
        );
        $params = array();

        if ($reportingManager !== '') {
            $where[] = '(e.reporting_manger = ? OR e.empId = ?)';
            $params[] = $reportingManager;
            $params[] = $reportingManager;
        }
        if ($employeeName !== '') {
            $where[] = 'e.name LIKE ?';
            $params[] = '%' . $employeeName . '%';
        }

        $roleSql = $this->buildRoleVisibilitySql($params);
        if ($roleSql !== '') {
            $where[] = $roleSql;
        }

        $sql = "
            SELECT
                e.empId,
                e.name AS employee_name,
                e.emp_com_id,
                mgr.name AS reporting_manager,
                NULL AS client_Id,
                '—' AS client_name,
                e.department,
                NULL AS project_Id,
                '—' AS project_name,
                '—' AS project_status,
                NULL AS project_start_date,
                NULL AS project_end_date,
                NULL AS emp_report_dates,
                NULL AS days_since_last_entry
            FROM employee_details e
            LEFT JOIN employee_details mgr ON mgr.empId = e.reporting_manger
            WHERE " . implode(' AND ', $where) . "
            ORDER BY e.name ASC
        ";

        return $this->db->query($sql, $params)->result();
    }

    public function getActiveClients() {
        return $this->db->select('client_Id, client_name')
            ->from('client_details')
            ->where('status', 'Active')
            ->order_by('client_name', 'asc')
            ->get()
            ->result();
    }

    public function getDepartmentsList() {
        return $this->db
            ->select('COALESCE(NULLIF(TRIM(p.project_type), ""), c.department) AS department', false)
            ->from('project_details p')
            ->join('client_details c', 'c.client_Id = p.client_Id', 'inner')
            ->where('c.status', 'Active')
            ->where("COALESCE(NULLIF(TRIM(p.project_type), ''), c.department) IS NOT NULL", null, false)
            ->where("COALESCE(NULLIF(TRIM(p.project_type), ''), c.department) != ''", null, false)
            ->group_by('COALESCE(NULLIF(TRIM(p.project_type), ""), c.department)', false)
            ->order_by('department', 'asc')
            ->get()
            ->result();
    }

    public function getProjectsByClient($clientId = '') {
        $this->db->select('project_Id, project_name')
            ->from('project_details')
            ->order_by('project_name', 'asc');

        if ($clientId !== '') {
            $this->db->where('client_Id', $clientId);
        }

        return $this->db->get()->result();
    }

    public function getReportingManagersList() {
        return $this->db->select('mgr.empId, mgr.name')
            ->from('employee_details emp')
            ->join('employee_details mgr', 'mgr.empId = emp.reporting_manger', 'inner')
            ->where('emp.status', 'Active')
            ->where('emp.reporting_manger !=', '')
            ->group_by('mgr.empId')
            ->order_by('mgr.name', 'asc')
            ->get()
            ->result();
    }

    public function getProjectStatuses() {
        return $this->db->select('DISTINCT TRIM(status) AS project_status', false)
            ->from('project_details')
            ->where("TRIM(status) IS NOT NULL AND TRIM(status) != ''", null, false)
            ->order_by('project_status', 'asc')
            ->get()
            ->result();
    }

    private function buildRoleVisibilitySql(&$params) {
        $userType = isset($this->session->userdata['logged_in_timesheet']['user_type'])
            ? $this->session->userdata['logged_in_timesheet']['user_type']
            : '';
        $loggedInEmpId = isset($this->session->userdata['logged_in_timesheet']['empId'])
            ? $this->session->userdata['logged_in_timesheet']['empId']
            : '';

        if (in_array($userType, array('admin', 'superadmin', 'business_head'), true)) {
            return '';
        }

        if ($userType === 'manager') {
            $params[] = $loggedInEmpId;
            $params[] = $loggedInEmpId;
            return '(e.reporting_manger = ? OR e.empId = ?)';
        }

        if (in_array($userType, array('developer', 'team_member'), true)) {
            $params[] = $loggedInEmpId;
            return 'e.empId = ?';
        }

        return '';
    }

    public function updateProjectStatus($projectId, $newStatus, $expectedCurrentStatus = '') {
        $projectId = (int)$projectId;
        if ($projectId <= 0 || !in_array($newStatus, array('Process', 'Closed'), true)) {
            return false;
        }

        $this->db->where('project_Id', $projectId);
        if ($expectedCurrentStatus !== '') {
            $this->db->where('status', $expectedCurrentStatus);
        } else {
            $this->db->where_in('status', array('Process', 'Closed'));
        }
        $this->db->update('project_details', array('status' => $newStatus));

        return $this->db->affected_rows() > 0;
    }

    public function updateProjectsStatusByIds($projectIds = array(), $fromStatus = 'Process', $toStatus = 'Closed') {
        if (empty($projectIds) || !is_array($projectIds)) {
            return 0;
        }
        if (!in_array($fromStatus, array('Process', 'Closed'), true) || !in_array($toStatus, array('Process', 'Closed'), true)) {
            return 0;
        }

        $validIds = array();
        foreach ($projectIds as $projectId) {
            $projectId = (int)$projectId;
            if ($projectId > 0) {
                $validIds[] = $projectId;
            }
        }
        $validIds = array_values(array_unique($validIds));
        if (empty($validIds)) {
            return 0;
        }

        $this->db->where_in('project_Id', $validIds);
        $this->db->where('status', $fromStatus);
        $this->db->update('project_details', array('status' => $toStatus));

        return (int)$this->db->affected_rows();
    }
}
