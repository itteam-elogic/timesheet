<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Execution_plan_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	private function get_elogic_client_ids() {
		return array('363','374','370','369','368','367','364','361','355','270','262','253','236','210','85','78','74','49','34','32','428');
	}

	private function apply_general_project_exclusion() {
		$this->db->where("LOWER(COALESCE(p.project_name, '')) NOT LIKE '%general%'", null, false);
	}

	private function get_excluded_department_names() {
		return array('business development', 'downtime');
	}

	private function apply_excluded_department_filter($departmentColumnSql = 'COALESCE(NULLIF(TRIM(p.project_type), ""), c.department)') {
		$escapedExcluded = array();
		foreach ($this->get_excluded_department_names() as $dept) {
			$escapedExcluded[] = $this->db->escape($dept);
		}
		if (!empty($escapedExcluded)) {
			$this->db->where('LOWER(TRIM(' . $departmentColumnSql . ')) NOT IN (' . implode(',', $escapedExcluded) . ')', null, false);
		}
	}

	private function build_report_date_range($from_year, $from_month, $to_year, $to_month) {
		$fromYear = !empty($from_year) ? (int)reset($from_year) : 0;
		$fromMonth = !empty($from_month) ? (int)reset($from_month) : 0;
		$toYear = !empty($to_year) ? (int)reset($to_year) : 0;
		$toMonth = !empty($to_month) ? (int)reset($to_month) : 0;

		$fromDate = '';
		$toDate = '';
		if ($fromYear > 0 && $fromMonth >= 1 && $fromMonth <= 12) {
			$fromDate = sprintf('%04d-%02d-01', $fromYear, $fromMonth);
		} elseif ($fromYear > 0) {
			$fromDate = sprintf('%04d-01-01', $fromYear);
		}
		if ($toYear > 0 && $toMonth >= 1 && $toMonth <= 12) {
			$toMonthEndDay = cal_days_in_month(CAL_GREGORIAN, $toMonth, $toYear);
			$toDate = sprintf('%04d-%02d-%02d', $toYear, $toMonth, $toMonthEndDay);
		} elseif ($toYear > 0) {
			$toDate = sprintf('%04d-12-31', $toYear);
		}

		if ($fromDate !== '' && $toDate !== '' && strtotime($fromDate) > strtotime($toDate)) {
			$swapDate = $fromDate;
			$fromDate = $toDate;
			$toDate = $swapDate;
		}

		$fromKey = null;
		$toKey = null;
		if ($fromDate !== '') {
			$fromKey = (int)date('Ym', strtotime($fromDate));
		}
		if ($toDate !== '') {
			$toKey = (int)date('Ym', strtotime($toDate));
		}

		return array(
			'fromDate' => $fromDate,
			'toDate' => $toDate,
			'fromKey' => $fromKey,
			'toKey' => $toKey
		);
	}

	private function build_timesheet_date_filter_sql($fromDate, $toDate, $dateColumn = 'emp_report_dates') {
		$conditions = array();
		if ($fromDate !== '') {
			$conditions[] = 'DATE(' . $dateColumn . ') >= ' . $this->db->escape($fromDate);
		}
		if ($toDate !== '') {
			$conditions[] = 'DATE(' . $dateColumn . ') <= ' . $this->db->escape($toDate);
		}
		if (empty($conditions)) {
			return '';
		}
		return ' AND ' . implode(' AND ', $conditions);
	}

	private function build_invoice_date_filter_sql($fromKey, $toKey) {
		$conditions = array();
		if ($fromKey !== null) {
			$conditions[] = '(invoice_year * 100 + invoice_month) >= ' . (int)$fromKey;
		}
		if ($toKey !== null) {
			$conditions[] = '(invoice_year * 100 + invoice_month) <= ' . (int)$toKey;
		}
		if (empty($conditions)) {
			return '';
		}
		return ' WHERE ' . implode(' AND ', $conditions);
	}

	private function build_project_period_overlap_sql($fromDate, $toDate) {
		if ($fromDate !== '' && $toDate !== '') {
			$fromEsc = $this->db->escape($fromDate);
			$toEsc = $this->db->escape($toDate);
			return '(
				(p.project_start_date IS NOT NULL AND p.project_start_date != \'0000-00-00\'
					AND p.project_end_date IS NOT NULL AND p.project_end_date != \'0000-00-00\'
					AND DATE(p.project_start_date) <= ' . $toEsc . ' AND DATE(p.project_end_date) >= ' . $fromEsc . ')
				OR
				(p.project_start_date IS NOT NULL AND p.project_start_date != \'0000-00-00\'
					AND (p.project_end_date IS NULL OR p.project_end_date = \'0000-00-00\')
					AND DATE(p.project_start_date) <= ' . $toEsc . ')
				OR
				((p.project_start_date IS NULL OR p.project_start_date = \'0000-00-00\')
					AND p.project_end_date IS NOT NULL AND p.project_end_date != \'0000-00-00\'
					AND DATE(p.project_end_date) >= ' . $fromEsc . ')
			)';
		}
		if ($fromDate !== '') {
			$fromEsc = $this->db->escape($fromDate);
			return '(
				(p.project_end_date IS NOT NULL AND p.project_end_date != \'0000-00-00\' AND DATE(p.project_end_date) >= ' . $fromEsc . ')
				OR
				((p.project_end_date IS NULL OR p.project_end_date = \'0000-00-00\')
					AND p.project_start_date IS NOT NULL AND p.project_start_date != \'0000-00-00\'
					AND DATE(p.project_start_date) <= ' . $fromEsc . ')
			)';
		}
		if ($toDate !== '') {
			$toEsc = $this->db->escape($toDate);
			return '(
				(p.project_start_date IS NOT NULL AND p.project_start_date != \'0000-00-00\' AND DATE(p.project_start_date) <= ' . $toEsc . ')
				OR
				((p.project_start_date IS NULL OR p.project_start_date = \'0000-00-00\')
					AND p.project_end_date IS NOT NULL AND p.project_end_date != \'0000-00-00\'
					AND DATE(p.project_end_date) >= ' . $toEsc . ')
			)';
		}
		return '';
	}

	private function apply_report_date_range_filter($fromDate, $toDate, $fromKey, $toKey) {
		if ($fromDate === '' && $toDate === '') {
			return;
		}

		$conditions = array();
		$overlapSql = $this->build_project_period_overlap_sql($fromDate, $toDate);
		if ($overlapSql !== '') {
			$conditions[] = $overlapSql;
		}

		$tsDateFilter = $this->build_timesheet_date_filter_sql($fromDate, $toDate, 'erd.emp_report_dates');
		if ($tsDateFilter !== '') {
			$conditions[] = 'EXISTS (SELECT 1 FROM emp_record_details erd WHERE erd.project_Id = p.project_Id AND erd.client_Id = p.client_Id' . $tsDateFilter . ')';
		}

		$invoiceConditions = array();
		if ($fromKey !== null) {
			$invoiceConditions[] = '(pim.invoice_year * 100 + pim.invoice_month) >= ' . (int)$fromKey;
		}
		if ($toKey !== null) {
			$invoiceConditions[] = '(pim.invoice_year * 100 + pim.invoice_month) <= ' . (int)$toKey;
		}
		if (!empty($invoiceConditions)) {
			$conditions[] = 'EXISTS (SELECT 1 FROM project_invoice_monthly pim WHERE pim.project_Id = p.project_Id AND ' . implode(' AND ', $invoiceConditions) . ')';
		}

		if (!empty($conditions)) {
			$this->db->where('(' . implode(' OR ', $conditions) . ')', null, false);
		}
	}

	private function has_selected_year_range($from_year, $to_year) {
		$fromYear = !empty($from_year) ? (int)reset($from_year) : 0;
		$toYear = !empty($to_year) ? (int)reset($to_year) : 0;
		return ($fromYear > 0 || $toYear > 0);
	}

	/**
	 * When a year is selected (2026, 2025, etc.), hide zero-timesheet projects
	 * unless they have period activity or started within the selected range.
	 * "All" year selection skips this filter and shows zero-timesheet rows.
	 */
	private function apply_period_activity_filter($fromDate, $toDate) {
		if ($fromDate === '' && $toDate === '') {
			return;
		}

		$conditions = array(
			'(COALESCE(ts_totals.total_timesheet_hours, 0) + COALESCE(ts_general_project_totals.general_timesheet_hours, 0) > 0)',
			'(COALESCE(invoice_totals.total_invoice_hours, 0) > 0)'
		);

		if ($fromDate !== '' && $toDate !== '') {
			$fromEsc = $this->db->escape($fromDate);
			$toEsc = $this->db->escape($toDate);
			$conditions[] = '(p.project_start_date IS NOT NULL AND p.project_start_date != \'0000-00-00\' '
				. 'AND DATE(p.project_start_date) >= ' . $fromEsc . ' AND DATE(p.project_start_date) <= ' . $toEsc . ')';
		} elseif ($fromDate !== '') {
			$fromEsc = $this->db->escape($fromDate);
			$conditions[] = '(p.project_start_date IS NOT NULL AND p.project_start_date != \'0000-00-00\' '
				. 'AND DATE(p.project_start_date) >= ' . $fromEsc . ')';
		} elseif ($toDate !== '') {
			$toEsc = $this->db->escape($toDate);
			$conditions[] = '(p.project_start_date IS NOT NULL AND p.project_start_date != \'0000-00-00\' '
				. 'AND DATE(p.project_start_date) <= ' . $toEsc . ')';
		}

		$this->db->where('(' . implode(' OR ', $conditions) . ')', null, false);
	}

	/**
	 * Returns execution plan report rows filtered by dropdown values.
	 */
	public function get_execution_plan_report($params) {
		$department      = isset($params['department']) ? (array)$params['department'] : array();
		$client_Id       = isset($params['client_Id']) ? (array)$params['client_Id'] : array();
		$project_Id      = isset($params['project_Id']) ? (array)$params['project_Id'] : array();
		$project_manager = isset($params['project_manager']) ? (array)$params['project_manager'] : array();
		$man_days        = isset($params['man_days']) ? (array)$params['man_days'] : array();
		$project_status  = isset($params['project_status']) ? (array)$params['project_status'] : array();
		$from_year       = isset($params['from_year']) ? (array)$params['from_year'] : array();
		$from_month      = isset($params['from_month']) ? (array)$params['from_month'] : array();
		$to_year         = isset($params['to_year']) ? (array)$params['to_year'] : array();
		$to_month        = isset($params['to_month']) ? (array)$params['to_month'] : array();

		$dateRange = $this->build_report_date_range($from_year, $from_month, $to_year, $to_month);
		$fromDate = $dateRange['fromDate'];
		$toDate = $dateRange['toDate'];
		$fromKey = $dateRange['fromKey'];
		$toKey = $dateRange['toKey'];
		$tsDateFilter = $this->build_timesheet_date_filter_sql($fromDate, $toDate, 'emp_report_dates');
		$tsGeneralDateFilter = $this->build_timesheet_date_filter_sql($fromDate, $toDate, 'erd.emp_report_dates');
		$invoiceDateFilter = $this->build_invoice_date_filter_sql($fromKey, $toKey);

		$ts_totals = "(SELECT project_Id, client_Id,
			SUM(emp_time_hours) as total_timesheet_hours,
			MAX(DATE(emp_report_dates)) as latest_timesheet_entry_date
			FROM emp_record_details
			WHERE 1=1{$tsDateFilter}
			GROUP BY project_Id, client_Id) ts_totals";
		$invoice_totals = "(SELECT project_Id, SUM(invoice_hours) as total_invoice_hours
			FROM project_invoice_monthly{$invoiceDateFilter}
			GROUP BY project_Id) invoice_totals";
		$ts_general_project_totals = "(SELECT gp.client_Id,
				LOWER(TRIM(REPLACE(REPLACE(gp.project_name, ' - (General)', ''), '(General)', ''))) as base_project_name,
				SUM(erd.emp_time_hours) as general_timesheet_hours,
				MAX(DATE(erd.emp_report_dates)) as latest_general_timesheet_entry_date
			FROM emp_record_details erd
			INNER JOIN project_details gp ON gp.project_Id = erd.project_Id AND gp.client_Id = erd.client_Id
			WHERE LOWER(TRIM(gp.project_name)) LIKE '%(general)%'{$tsGeneralDateFilter}
			GROUP BY gp.client_Id, LOWER(TRIM(REPLACE(REPLACE(gp.project_name, ' - (General)', ''), '(General)', '')))) ts_general_project_totals";

		$clientDateSubqueryBase = " FROM project_details p2
			WHERE p2.client_Id = c.client_Id
			AND LOWER(COALESCE(p2.project_name, '')) NOT LIKE '%general%'";

		$this->db->select('
				p.project_Id,
				p.project_name,
				p.project_start_date,
				p.project_end_date,
				p.man_days,
				COALESCE(p.team_members, "") as team_members,
				COALESCE(NULLIF(TRIM(p.status), ""), "") as project_status,
				COALESCE(p.estimated_hours, 0) as schedule_hours,
				(COALESCE(ts_totals.total_timesheet_hours, 0) + COALESCE(ts_general_project_totals.general_timesheet_hours, 0)) as timesheet_hours,
				CASE
					WHEN ts_totals.latest_timesheet_entry_date IS NULL THEN ts_general_project_totals.latest_general_timesheet_entry_date
					WHEN ts_general_project_totals.latest_general_timesheet_entry_date IS NULL THEN ts_totals.latest_timesheet_entry_date
					WHEN ts_totals.latest_timesheet_entry_date >= ts_general_project_totals.latest_general_timesheet_entry_date THEN ts_totals.latest_timesheet_entry_date
					ELSE ts_general_project_totals.latest_general_timesheet_entry_date
				END as timesheet_entry_date,
				COALESCE(invoice_totals.total_invoice_hours, 0) as invoice_hours,
				c.client_name,
				c.client_Id,
				COALESCE(NULLIF(TRIM(c.status), ""), "Inactive") as client_status,
				(SELECT MIN(p2.project_start_date)' . $clientDateSubqueryBase . '
					AND p2.project_start_date IS NOT NULL
					AND p2.project_start_date != \'0000-00-00\') as client_start_date,
				(SELECT MAX(p2.project_end_date)' . $clientDateSubqueryBase . '
					AND p2.project_end_date IS NOT NULL
					AND p2.project_end_date != \'0000-00-00\') as client_end_date,
				emp.name as project_manager_name,
				p.empId as project_manager_id,
				COALESCE(NULLIF(TRIM(p.project_type), ""), c.department) as department
			', false);
		$this->db->from('project_details as p');
		$this->db->join('client_details as c', 'c.client_Id = p.client_Id', 'left');
		$this->db->join('employee_details as emp', 'emp.empId = p.empId', 'left');
		$this->db->join($ts_totals, 'ts_totals.project_Id = p.project_Id AND ts_totals.client_Id = p.client_Id', 'left');
		$this->db->join($invoice_totals, 'invoice_totals.project_Id = p.project_Id', 'left');
		$this->db->join($ts_general_project_totals, "ts_general_project_totals.client_Id = p.client_Id AND ts_general_project_totals.base_project_name = LOWER(TRIM(p.project_name))", 'left');
		//$this->db->where('c.status', 'Active');
		$this->db->where_not_in('c.client_Id', $this->get_elogic_client_ids());
		$this->apply_general_project_exclusion();

		if (!empty($department)) {
			$escapedDepartments = array();
			foreach ($department as $dept) {
				$escapedDepartments[] = $this->db->escape($dept);
			}
			$this->db->where('COALESCE(NULLIF(TRIM(p.project_type), ""), c.department) IN (' . implode(',', $escapedDepartments) . ')', null, false);
		}
		if (!empty($client_Id)) {
			$this->db->where_in('c.client_Id', $client_Id);
		}
		if (!empty($project_Id)) {
			$this->db->where_in('p.project_Id', $project_Id);
		}
		if (!empty($project_manager)) {
			$this->db->where_in('p.empId', $project_manager);
		}
		if (!empty($man_days)) {
			$escapedManDays = array();
			foreach ($man_days as $dayType) {
				$escapedManDays[] = $this->db->escape(strtolower(trim((string)$dayType)));
			}
			$this->db->where('LOWER(TRIM(p.man_days)) IN (' . implode(',', $escapedManDays) . ')', null, false);
		}
		if (!empty($project_status)) {
			$escapedStatuses = array();
			foreach ($project_status as $status) {
				$statusKey = strtolower(trim((string)$status));
				if ($statusKey === 'process' || $statusKey === 'in process' || $statusKey === 'in_process') {
					$escapedStatuses[] = $this->db->escape('process');
					$escapedStatuses[] = $this->db->escape('in process');
					$escapedStatuses[] = $this->db->escape('in_process');
					continue;
				}
				if ($statusKey === 'on hold' || $statusKey === 'on_hold') {
					$escapedStatuses[] = $this->db->escape('on hold');
					$escapedStatuses[] = $this->db->escape('on_hold');
					continue;
				}
				$escapedStatuses[] = $this->db->escape($statusKey);
			}
			$escapedStatuses = array_values(array_unique($escapedStatuses));
			$this->db->where('LOWER(TRIM(p.status)) IN (' . implode(',', $escapedStatuses) . ')', null, false);
		}

		$this->apply_report_date_range_filter($fromDate, $toDate, $fromKey, $toKey);

		if ($this->has_selected_year_range($from_year, $to_year)) {
			$this->apply_period_activity_filter($fromDate, $toDate);
		}

		$this->db->order_by('p.project_end_date', 'desc');
		$this->db->order_by('p.project_start_date', 'desc');
		$this->db->order_by('p.project_name', 'desc');

		return $this->db->get()->result();
	}

	public function get_filter_departments() {
		$this->db->select('COALESCE(NULLIF(TRIM(p.project_type), ""), c.department) as department', false);
		$this->db->from('project_details as p');
		$this->db->join('client_details as c', 'c.client_Id = p.client_Id', 'inner');
		//$this->db->where('c.status', 'Active');
		$this->db->where_not_in('c.client_Id', $this->get_elogic_client_ids());
		$this->apply_general_project_exclusion();
		$this->db->where("COALESCE(NULLIF(TRIM(p.project_type), ''), c.department) IS NOT NULL AND COALESCE(NULLIF(TRIM(p.project_type), ''), c.department) != ''", null, false);
		$this->apply_excluded_department_filter();
		$this->db->group_by('COALESCE(NULLIF(TRIM(p.project_type), ""), c.department)', false);
		$this->db->order_by('department', 'asc');
		return $this->db->get()->result();
	}

	public function get_filter_clients() {
		$this->db->select('c.client_Id, c.client_name');
		$this->db->from('client_details as c');
		//$this->db->where('c.status', 'Active');
		$this->db->where_not_in('c.client_Id', $this->get_elogic_client_ids());
		// Match all_clients_reports: newest clients first so Select2 autosuggest order is the same
		$this->db->order_by('c.client_Id', 'desc');
		return $this->format_filter_clients($this->db->get()->result());
	}

	public function get_filter_clients_by_departments($departments = array()) {
		$departments = array_values(array_filter(array_map('trim', (array)$departments), function($item) {
			return $item !== '';
		}));
		if (empty($departments)) {
			return $this->get_filter_clients();
		}

		$this->db->distinct();
		$this->db->select('c.client_Id, c.client_name');
		$this->db->from('client_details as c');
		$this->db->join('project_details as p', 'p.client_Id = c.client_Id', 'inner');
		//$this->db->where('c.status', 'Active');
		$this->db->where_not_in('c.client_Id', $this->get_elogic_client_ids());
		$this->apply_general_project_exclusion();
		$escapedDepartments = array();
		foreach ($departments as $dept) {
			$escapedDepartments[] = $this->db->escape($dept);
		}
		$this->db->where('COALESCE(NULLIF(TRIM(p.project_type), ""), c.department) IN (' . implode(',', $escapedDepartments) . ')', null, false);
		$this->db->order_by('c.client_Id', 'desc');
		return $this->format_filter_clients($this->db->get()->result());
	}

	private function format_filter_clients($clients) {
		foreach ($clients as $client) {
			if (isset($client->client_name)) {
				$client->client_name = ucfirst(str_replace("'", " ", (string)$client->client_name));
			}
		}
		return $clients;
	}

	public function get_filter_projects() {
		$this->db->select('p.project_Id, p.project_name');
		$this->db->from('project_details as p');
		$this->db->join('client_details as c', 'c.client_Id = p.client_Id', 'inner');
		//$this->db->where('c.status', 'Active');
		$this->db->where_not_in('c.client_Id', $this->get_elogic_client_ids());
		$this->apply_general_project_exclusion();
		$this->db->order_by('p.project_name', 'asc');
		return $this->db->get()->result();
	}

	public function get_filter_projects_by_clients($clientIds = array(), $departments = array()) {
		$clientIds = array_values(array_filter(array_map('trim', (array)$clientIds), function($item) {
			return $item !== '';
		}));
		$departments = array_values(array_filter(array_map('trim', (array)$departments), function($item) {
			return $item !== '';
		}));

		$this->db->select('p.project_Id, p.project_name');
		$this->db->from('project_details as p');
		$this->db->join('client_details as c', 'c.client_Id = p.client_Id', 'inner');
		//$this->db->where('c.status', 'Active');
		$this->db->where_not_in('c.client_Id', $this->get_elogic_client_ids());
		$this->apply_general_project_exclusion();
		if (!empty($clientIds)) {
			$this->db->where_in('p.client_Id', $clientIds);
		}
		if (!empty($departments)) {
			$escapedDepartments = array();
			foreach ($departments as $dept) {
				$escapedDepartments[] = $this->db->escape($dept);
			}
			$this->db->where('COALESCE(NULLIF(TRIM(p.project_type), ""), c.department) IN (' . implode(',', $escapedDepartments) . ')', null, false);
		}
		$this->db->order_by('p.project_name', 'asc');
		return $this->db->get()->result();
	}

	public function get_filter_managers() {
		$this->db->distinct();
		$this->db->select('e.empId, e.name');
		$this->db->from('employee_details as e');
		$this->db->join('project_details as p', 'p.empId = e.empId', 'inner');
		$this->db->join('client_details as c', 'c.client_Id = p.client_Id', 'inner');
		$this->db->where('e.status', 'Active');
		$this->db->where_in('e.user_type', array('manager', 'business_head'));
		//$this->db->where('c.status', 'Active');
		$this->db->where_not_in('c.client_Id', $this->get_elogic_client_ids());
		$this->apply_general_project_exclusion();
		$this->db->order_by('e.name', 'asc');
		return $this->db->get()->result();
	}

	public function get_filter_years() {
		$escapedClientIds = array();
		foreach ($this->get_elogic_client_ids() as $clientId) {
			$escapedClientIds[] = $this->db->escape($clientId);
		}
		$excludedClientsSql = implode(',', $escapedClientIds);
		$currentYear = (int)date('Y');

		$sql = "SELECT DISTINCT year_val AS year FROM (
			SELECT YEAR(p.project_start_date) AS year_val
			FROM project_details p
			INNER JOIN client_details c ON c.client_Id = p.client_Id
			WHERE c.client_Id NOT IN ({$excludedClientsSql})
			AND p.project_start_date IS NOT NULL
			AND p.project_start_date != '0000-00-00'
			AND LOWER(COALESCE(p.project_name, '')) NOT LIKE '%general%'
			UNION
			SELECT YEAR(erd.emp_report_dates) AS year_val
			FROM emp_record_details erd
			INNER JOIN project_details p ON p.project_Id = erd.project_Id AND p.client_Id = erd.client_Id
			INNER JOIN client_details c ON c.client_Id = erd.client_Id
			WHERE c.client_Id NOT IN ({$excludedClientsSql})
			AND erd.emp_report_dates IS NOT NULL
			AND erd.emp_report_dates != '0000-00-00'
			AND LOWER(COALESCE(p.project_name, '')) NOT LIKE '%general%'
			UNION
			SELECT pim.invoice_year AS year_val
			FROM project_invoice_monthly pim
			INNER JOIN project_details p ON p.project_Id = pim.project_Id
			INNER JOIN client_details c ON c.client_Id = p.client_Id
			WHERE c.client_Id NOT IN ({$excludedClientsSql})
			AND LOWER(COALESCE(p.project_name, '')) NOT LIKE '%general%'
			UNION
			SELECT {$currentYear} AS year_val
		) combined_years
		WHERE year_val IS NOT NULL AND year_val > 0
		ORDER BY year DESC";

		return $this->db->query($sql)->result();
	}

	public function get_filter_months() {
		return array(
			(object)array('month_number' => 1, 'month_name' => 'January'),
			(object)array('month_number' => 2, 'month_name' => 'February'),
			(object)array('month_number' => 3, 'month_name' => 'March'),
			(object)array('month_number' => 4, 'month_name' => 'April'),
			(object)array('month_number' => 5, 'month_name' => 'May'),
			(object)array('month_number' => 6, 'month_name' => 'June'),
			(object)array('month_number' => 7, 'month_name' => 'July'),
			(object)array('month_number' => 8, 'month_name' => 'August'),
			(object)array('month_number' => 9, 'month_name' => 'September'),
			(object)array('month_number' => 10, 'month_name' => 'October'),
			(object)array('month_number' => 11, 'month_name' => 'November'),
			(object)array('month_number' => 12, 'month_name' => 'December')
		);
	}

	public function get_filter_project_statuses() {
		$this->db->distinct();
		$this->db->select('TRIM(p.status) as project_status', false);
		$this->db->from('project_details as p');
		$this->db->join('client_details as c', 'c.client_Id = p.client_Id', 'inner');
		//$this->db->where('c.status', 'Active');
		$this->db->where_not_in('c.client_Id', $this->get_elogic_client_ids());
		$this->apply_general_project_exclusion();
		$this->db->where("TRIM(p.status) IS NOT NULL AND TRIM(p.status) != ''", null, false);
		$this->db->order_by('project_status', 'asc');
		return $this->db->get()->result();
	}

	public function get_project_status_summary_counts() {
		$statusRows = $this->db
			->select('LOWER(TRIM(p.status)) as status_key, COUNT(*) as total_count', false)
			->from('project_details as p')
			->join('client_details as c', 'c.client_Id = p.client_Id', 'left')
			->where("TRIM(p.status) IS NOT NULL AND TRIM(p.status) != ''", null, false)
			->where("LOWER(TRIM(p.project_name)) NOT LIKE '%(general)%'", null, false)
			->where("LOWER(TRIM(p.project_name)) NOT LIKE '%- general%'", null, false)
			->where("LOWER(TRIM(p.project_name)) NOT LIKE '%client calls%'", null, false)
			->where("LOWER(TRIM(p.project_name)) NOT LIKE '%test%'", null, false)
			->where("LOWER(TRIM(p.project_name)) NOT LIKE '%trail project%'", null, false)
			->where("LOWER(TRIM(c.client_name)) NOT LIKE '%client calls%'", null, false)
			->where("LOWER(TRIM(c.client_name)) NOT LIKE '%elogic%'", null, false)
			->where("LOWER(TRIM(c.client_name)) NOT IN ('elogic (ather)', 'elogic points cloud training', 'elogicsolutions(raghava)', 'elogictech solutions')", null, false)
			->group_by('LOWER(TRIM(p.status))', false)
			->get()
			->result();

		$summary = array(
			'in_process' => 0,
			'closed' => 0,
			'on_hold' => 0,
			'total' => 0
		);

		foreach ($statusRows as $statusRow) {
			$statusKey = isset($statusRow->status_key) ? strtolower(trim((string)$statusRow->status_key)) : '';
			$count = isset($statusRow->total_count) ? (int)$statusRow->total_count : 0;

			if ($statusKey === 'process' || $statusKey === 'in process' || $statusKey === 'in_process') {
				$summary['in_process'] += $count;
			} elseif ($statusKey === 'closed') {
				$summary['closed'] += $count;
			} elseif ($statusKey === 'on hold' || $statusKey === 'on_hold') {
				$summary['on_hold'] += $count;
			}
		}
		$summary['total'] = $summary['in_process'] + $summary['closed'] + $summary['on_hold'];

		return $summary;
	}

	public function get_project_status_summary_counts_by_filters($params) {
		$department      = isset($params['department']) ? (array)$params['department'] : array();
		$client_Id       = isset($params['client_Id']) ? (array)$params['client_Id'] : array();
		$project_Id      = isset($params['project_Id']) ? (array)$params['project_Id'] : array();
		$project_manager = isset($params['project_manager']) ? (array)$params['project_manager'] : array();
		$man_days        = isset($params['man_days']) ? (array)$params['man_days'] : array();
		$project_status  = isset($params['project_status']) ? (array)$params['project_status'] : array();
		$from_year       = isset($params['from_year']) ? (array)$params['from_year'] : array();
		$from_month      = isset($params['from_month']) ? (array)$params['from_month'] : array();
		$to_year         = isset($params['to_year']) ? (array)$params['to_year'] : array();
		$to_month        = isset($params['to_month']) ? (array)$params['to_month'] : array();

		$this->db->select('LOWER(TRIM(p.status)) as status_key, COUNT(*) as total_count', false);
		$this->db->from('project_details as p');
		$this->db->where("TRIM(p.status) IS NOT NULL AND TRIM(p.status) != ''", null, false);
		// Summary only: exclude General variants by project name.
		$this->db->where("LOWER(COALESCE(p.project_name, '')) NOT LIKE '%general%'", null, false);

		if (!empty($department)) {
			$this->db->where_in('p.project_type', $department);
		}
		if (!empty($client_Id)) {
			$this->db->where_in('p.client_Id', $client_Id);
		}
		if (!empty($project_Id)) {
			$this->db->where_in('p.project_Id', $project_Id);
		}
		if (!empty($project_manager)) {
			$this->db->where_in('p.empId', $project_manager);
		}
		if (!empty($man_days)) {
			$escapedManDays = array();
			foreach ($man_days as $dayType) {
				$escapedManDays[] = $this->db->escape(strtolower(trim((string)$dayType)));
			}
			$this->db->where('LOWER(TRIM(p.man_days)) IN (' . implode(',', $escapedManDays) . ')', null, false);
		}
		if (!empty($project_status)) {
			$escapedStatuses = array();
			foreach ($project_status as $status) {
				$statusKey = strtolower(trim((string)$status));
				if ($statusKey === 'process' || $statusKey === 'in process' || $statusKey === 'in_process') {
					$escapedStatuses[] = $this->db->escape('process');
					$escapedStatuses[] = $this->db->escape('in process');
					$escapedStatuses[] = $this->db->escape('in_process');
					continue;
				}
				if ($statusKey === 'on hold' || $statusKey === 'on_hold') {
					$escapedStatuses[] = $this->db->escape('on hold');
					$escapedStatuses[] = $this->db->escape('on_hold');
					continue;
				}
				$escapedStatuses[] = $this->db->escape($statusKey);
			}
			$escapedStatuses = array_values(array_unique($escapedStatuses));
			$this->db->where('LOWER(TRIM(p.status)) IN (' . implode(',', $escapedStatuses) . ')', null, false);
		}

		$dateRange = $this->build_report_date_range($from_year, $from_month, $to_year, $to_month);
		$this->apply_report_date_range_filter($dateRange['fromDate'], $dateRange['toDate'], $dateRange['fromKey'], $dateRange['toKey']);

		$this->db->group_by('LOWER(TRIM(p.status))', false);
		$statusRows = $this->db->get()->result();

		$summary = array(
			'in_process' => 0,
			'closed' => 0,
			'on_hold' => 0,
			'total' => 0
		);
		foreach ($statusRows as $statusRow) {
			$statusKey = isset($statusRow->status_key) ? strtolower(trim((string)$statusRow->status_key)) : '';
			$count = isset($statusRow->total_count) ? (int)$statusRow->total_count : 0;
			if ($statusKey === 'process' || $statusKey === 'in process' || $statusKey === 'in_process') {
				$summary['in_process'] += $count;
			} elseif ($statusKey === 'closed') {
				$summary['closed'] += $count;
			} elseif ($statusKey === 'on hold' || $statusKey === 'on_hold') {
				$summary['on_hold'] += $count;
			}
		}
		$summary['total'] = $summary['in_process'] + $summary['closed'] + $summary['on_hold'];
		return $summary;
	}
}
