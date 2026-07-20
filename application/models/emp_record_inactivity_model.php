<?php
/**
 * Employee timesheet inactivity report (no entries in last 6 months).
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Emp_record_inactivity_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Default period: last 6 months from today.
     */
    public function getDefaultFromDate() {
        return date('Y-m-d', strtotime('-6 months'));
    }

    public function getDefaultToDate() {
        return date('Y-m-d');
    }

    /**
     * Employees with client/project history but no emp_record_details in the given date range.
     */
    public function getInactiveTimesheetRecords($filters = array()) {
        $fromDate = !empty($filters['from_date']) ? $filters['from_date'] : $this->getDefaultFromDate();
        $toDate = !empty($filters['to_date']) ? $filters['to_date'] : $this->getDefaultToDate();
        $clientId = isset($filters['client_Id']) ? trim((string)$filters['client_Id']) : '';
        $projectId = isset($filters['project_Id']) ? trim((string)$filters['project_Id']) : '';
        $reportingManager = isset($filters['reporting_manager']) ? trim((string)$filters['reporting_manager']) : '';
        $employeeName = isset($filters['employee_name']) ? trim((string)$filters['employee_name']) : '';
        $projectStatus = isset($filters['project_status']) ? trim((string)$filters['project_status']) : '';

        $where = array('recent.empId IS NULL');
        $params = array($fromDate, $toDate);

        if ($clientId !== '') {
            $where[] = 'c.client_Id = ?';
            $params[] = $clientId;
        }
        if ($projectId !== '') {
            $where[] = 'p.project_Id = ?';
            $params[] = $projectId;
        }
        if ($reportingManager !== '') {
            $where[] = '(e.reporting_manger = ? OR e.empId = ?)';
            $params[] = $reportingManager;
            $params[] = $reportingManager;
        }
        if ($employeeName !== '') {
            $where[] = 'e.name LIKE ?';
            $params[] = '%' . $employeeName . '%';
        }
        if ($projectStatus !== '') {
            $where[] = 'p.status = ?';
            $params[] = $projectStatus;
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
                c.client_Id,
                c.client_name,
                COALESCE(NULLIF(p.project_type, ''), c.department) AS department,
                p.project_Id,
                p.project_name,
                p.status AS project_status,
                p.project_start_date,
                p.project_end_date,
                last_all.last_entry_date,
                DATEDIFF(CURDATE(), last_all.last_entry_date) AS days_since_last_entry
            FROM (
                SELECT DISTINCT empId, client_Id, project_Id
                FROM emp_record_details
            ) hist
            INNER JOIN employee_details e ON e.empId = hist.empId AND e.status = 'Active'
            LEFT JOIN employee_details mgr ON mgr.empId = e.reporting_manger
            INNER JOIN client_details c ON c.client_Id = hist.client_Id
            INNER JOIN project_details p ON p.project_Id = hist.project_Id AND p.client_Id = hist.client_Id
            INNER JOIN (
                SELECT empId, client_Id, project_Id, MAX(emp_report_dates) AS last_entry_date
                FROM emp_record_details
                GROUP BY empId, client_Id, project_Id
            ) last_all ON last_all.empId = hist.empId
                AND last_all.client_Id = hist.client_Id
                AND last_all.project_Id = hist.project_Id
            LEFT JOIN (
                SELECT empId, client_Id, project_Id
                FROM emp_record_details
                WHERE emp_report_dates >= ?
                  AND emp_report_dates <= ?
                GROUP BY empId, client_Id, project_Id
            ) recent ON recent.empId = hist.empId
                AND recent.client_Id = hist.client_Id
                AND recent.project_Id = hist.project_Id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY c.client_name ASC, p.project_name ASC, e.name ASC
        ";

        return $this->db->query($sql, $params)->result();
    }

    /**
     * Active employees who never entered any timesheet record.
     */
    public function getEmployeesNeverEntered($filters = array()) {
        $reportingManager = isset($filters['reporting_manager']) ? trim((string)$filters['reporting_manager']) : '';
        $employeeName = isset($filters['employee_name']) ? trim((string)$filters['employee_name']) : '';

        $where = array("e.status = 'Active'", 'e.empId NOT IN (SELECT DISTINCT empId FROM emp_record_details)');
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
                NULL AS last_entry_date,
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

    /**
     * Update only project status for a single project.
     */
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

    /**
     * Bulk update project status; only status field is changed.
     */
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
