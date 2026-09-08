<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Management_plan_model extends CI_Model {

	private $excludedClientIdList = null;

	public function __construct() {
		parent::__construct();
	}

	private function get_elogic_client_ids() {
		return array('363','374','370','369','368','367','364','361','355','270','262','253','236','210','85','78','74','49','34','32','428');
	}

	private function get_all_excluded_client_ids() {
		if ($this->excludedClientIdList !== null) {
			return $this->excludedClientIdList;
		}

		$ids = $this->get_elogic_client_ids();
		$nameRows = $this->db->query("
			SELECT client_Id
			FROM client_details
			WHERE LOWER(TRIM(client_name)) LIKE '%elogic%'
				OR LOWER(TRIM(client_name)) LIKE '%it team%'
		")->result();
		foreach ($nameRows as $nameRow) {
			if (isset($nameRow->client_Id) && $nameRow->client_Id !== '') {
				$ids[] = (string)$nameRow->client_Id;
			}
		}

		$this->excludedClientIdList = array_values(array_unique($ids));
		return $this->excludedClientIdList;
	}

	private function excluded_clients_sql() {
		$escapedClientIds = array();
		foreach ($this->get_all_excluded_client_ids() as $clientId) {
			$escapedClientIds[] = $this->db->escape($clientId);
		}
		if (empty($escapedClientIds)) {
			return '0';
		}
		return implode(',', $escapedClientIds);
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

	private function build_timesheet_date_filter_sql($fromDate, $toDate, $dateColumn = 'erd.emp_report_dates') {
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
			$conditions[] = '(pim.invoice_year * 100 + pim.invoice_month) >= ' . (int)$fromKey;
		}
		if ($toKey !== null) {
			$conditions[] = '(pim.invoice_year * 100 + pim.invoice_month) <= ' . (int)$toKey;
		}
		if (empty($conditions)) {
			return '';
		}
		return ' AND ' . implode(' AND ', $conditions);
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

	private function build_project_visibility_sql($filters) {
		$fromDate = $filters['fromDate'];
		$toDate = $filters['toDate'];
		$fromKey = $filters['fromKey'];
		$toKey = $filters['toKey'];
		$tsDateFilter = $filters['tsDateFilter'];
		if ($fromDate === '' && $toDate === '') {
			return '';
		}

		$conditions = array();
		$overlapSql = $this->build_project_period_overlap_sql($fromDate, $toDate);
		if ($overlapSql !== '') {
			$conditions[] = $overlapSql;
		}
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
		if (empty($conditions)) {
			return '';
		}
		return ' AND (' . implode(' OR ', $conditions) . ')';
	}

	private function visible_production_projects_sql($filters) {
		$excludedClients = $filters['excludedClients'];
		$generalExclusion = $filters['generalExclusion'];
		$clientIdFilterSql = isset($filters['clientIdFilterSql']) ? $filters['clientIdFilterSql'] : '';
		$visibilitySql = $this->build_project_visibility_sql($filters);
		return "
			SELECT p.project_Id, p.client_Id, p.project_name
			FROM project_details p
			WHERE p.client_Id NOT IN ({$excludedClients})
			{$generalExclusion}
			{$clientIdFilterSql}
			{$visibilitySql}
		";
	}

	private function general_timesheet_totals_sql($filters, $groupByMonth = false) {
		$tsDateFilter = $filters['tsDateFilter'];
		$erdClientFilterSql = isset($filters['erdClientFilterSql']) ? $filters['erdClientFilterSql'] : '';
		$yearSelect = $groupByMonth ? ', YEAR(erd.emp_report_dates) AS year_val, MONTH(erd.emp_report_dates) AS month_val' : '';
		$yearGroup = $groupByMonth ? ', YEAR(erd.emp_report_dates), MONTH(erd.emp_report_dates)' : '';
		$baseNameSql = "LOWER(TRIM(REPLACE(REPLACE(gp.project_name, ' - (General)', ''), '(General)', '')))";
		return "
			SELECT gp.client_Id,
				{$baseNameSql} AS base_project_name
				{$yearSelect},
				SUM(erd.emp_time_hours) AS general_timesheet_hours,
				MAX(erd.emp_report_dates) AS timesheet_date
			FROM emp_record_details erd
			INNER JOIN project_details gp ON gp.project_Id = erd.project_Id AND gp.client_Id = erd.client_Id
			WHERE LOWER(TRIM(gp.project_name)) LIKE '%(general)%'
			{$tsDateFilter}
			{$erdClientFilterSql}
			GROUP BY gp.client_Id, {$baseNameSql}{$yearGroup}
		";
	}

	private function production_timesheet_totals_sql($filters, $groupByMonth = false) {
		$tsDateFilter = str_replace('erd.emp_report_dates', 'emp_report_dates', $filters['tsDateFilter']);
		$erdClientFilterSql = str_replace('erd.client_Id', 'client_Id', isset($filters['erdClientFilterSql']) ? $filters['erdClientFilterSql'] : '');
		$yearSelect = $groupByMonth ? ', YEAR(emp_report_dates) AS year_val, MONTH(emp_report_dates) AS month_val' : '';
		$yearGroup = $groupByMonth ? ', YEAR(emp_report_dates), MONTH(emp_report_dates)' : '';
		return "
			SELECT project_Id, client_Id
				{$yearSelect},
				SUM(emp_time_hours) AS timesheet_hours,
				MAX(emp_report_dates) AS timesheet_date
			FROM emp_record_details
			WHERE 1=1
			{$tsDateFilter}
			{$erdClientFilterSql}
			AND emp_report_dates IS NOT NULL
			AND emp_report_dates != '0000-00-00'
			GROUP BY project_Id, client_Id{$yearGroup}
		";
	}

	private function prepare_filters($params) {
		$client_Id  = isset($params['client_Id']) ? (array)$params['client_Id'] : array();
		$from_year  = isset($params['from_year']) ? (array)$params['from_year'] : array();
		$from_month = isset($params['from_month']) ? (array)$params['from_month'] : array();
		$to_year    = isset($params['to_year']) ? (array)$params['to_year'] : array();
		$to_month   = isset($params['to_month']) ? (array)$params['to_month'] : array();

		$dateRange = $this->build_report_date_range($from_year, $from_month, $to_year, $to_month);
		$excludedClients = $this->excluded_clients_sql();

		$clientFilterSql = '';
		$clientIdFilterSql = '';
		$erdClientFilterSql = '';
		if (!empty($client_Id)) {
			$escapedClients = array();
			foreach ($client_Id as $id) {
				$id = trim((string)$id);
				if ($id !== '') {
					$escapedClients[] = $this->db->escape($id);
				}
			}
			if (!empty($escapedClients)) {
				$inList = implode(',', $escapedClients);
				$clientFilterSql = ' AND c.client_Id IN (' . $inList . ')';
				$clientIdFilterSql = ' AND p.client_Id IN (' . $inList . ')';
				$erdClientFilterSql = ' AND erd.client_Id IN (' . $inList . ')';
			}
		}

		return array(
			'fromDate' => $dateRange['fromDate'],
			'toDate' => $dateRange['toDate'],
			'fromKey' => $dateRange['fromKey'],
			'toKey' => $dateRange['toKey'],
			'excludedClients' => $excludedClients,
			'generalExclusion' => " AND LOWER(COALESCE(p.project_name, '')) NOT LIKE '%general%'",
			'tsDateFilter' => $this->build_timesheet_date_filter_sql($dateRange['fromDate'], $dateRange['toDate'], 'erd.emp_report_dates'),
			'invoiceDateFilter' => $this->build_invoice_date_filter_sql($dateRange['fromKey'], $dateRange['toKey']),
			'clientFilterSql' => $clientFilterSql,
			'clientIdFilterSql' => $clientIdFilterSql,
			'erdClientFilterSql' => $erdClientFilterSql
		);
	}

	private function month_project_visibility_sql($projectAlias = 'p', $yearExpr = 'gen.year_val', $monthExpr = 'gen.month_val') {
		$monthStart = "DATE(CONCAT({$yearExpr}, '-', LPAD({$monthExpr}, 2, '0'), '-01'))";
		$monthEnd = 'LAST_DAY(' . $monthStart . ')';
		return '(
			(
				(' . $projectAlias . '.project_start_date IS NOT NULL AND ' . $projectAlias . '.project_start_date != \'0000-00-00\'
					AND ' . $projectAlias . '.project_end_date IS NOT NULL AND ' . $projectAlias . '.project_end_date != \'0000-00-00\'
					AND DATE(' . $projectAlias . '.project_start_date) <= ' . $monthEnd . '
					AND DATE(' . $projectAlias . '.project_end_date) >= ' . $monthStart . ')
				OR
				(' . $projectAlias . '.project_start_date IS NOT NULL AND ' . $projectAlias . '.project_start_date != \'0000-00-00\'
					AND (' . $projectAlias . '.project_end_date IS NULL OR ' . $projectAlias . '.project_end_date = \'0000-00-00\')
					AND DATE(' . $projectAlias . '.project_start_date) <= ' . $monthEnd . ')
				OR
				((' . $projectAlias . '.project_start_date IS NULL OR ' . $projectAlias . '.project_start_date = \'0000-00-00\')
					AND ' . $projectAlias . '.project_end_date IS NOT NULL AND ' . $projectAlias . '.project_end_date != \'0000-00-00\'
					AND DATE(' . $projectAlias . '.project_end_date) >= ' . $monthStart . ')
			)
			OR EXISTS (
				SELECT 1
				FROM emp_record_details erd_m
				WHERE erd_m.project_Id = ' . $projectAlias . '.project_Id
					AND erd_m.client_Id = ' . $projectAlias . '.client_Id
					AND DATE(erd_m.emp_report_dates) >= ' . $monthStart . '
					AND DATE(erd_m.emp_report_dates) <= ' . $monthEnd . '
			)
			OR EXISTS (
				SELECT 1
				FROM project_invoice_monthly pim_m
				WHERE pim_m.project_Id = ' . $projectAlias . '.project_Id
					AND pim_m.invoice_year = ' . $yearExpr . '
					AND pim_m.invoice_month = ' . $monthExpr . '
			)
		)';
	}

	private function timesheet_hours_select_sql($filters, $groupByMonth = false) {
		$visibleSql = $this->visible_production_projects_sql($filters);
		$productionTsSql = $this->production_timesheet_totals_sql($filters, $groupByMonth);
		$generalTsSql = $this->general_timesheet_totals_sql($filters, $groupByMonth);

		if (!$groupByMonth) {
			return "
				SELECT
					p.client_Id,
					SUM(COALESCE(ts.timesheet_hours, 0) + COALESCE(gen.general_timesheet_hours, 0)) AS timesheet_hours,
					MAX(CASE
						WHEN ts.timesheet_date IS NULL THEN gen.timesheet_date
						WHEN gen.timesheet_date IS NULL THEN ts.timesheet_date
						WHEN ts.timesheet_date >= gen.timesheet_date THEN ts.timesheet_date
						ELSE gen.timesheet_date
					END) AS timesheet_date
				FROM ({$visibleSql}) p
				LEFT JOIN (
					{$productionTsSql}
				) ts ON ts.project_Id = p.project_Id AND ts.client_Id = p.client_Id
				LEFT JOIN (
					{$generalTsSql}
				) gen ON gen.client_Id = p.client_Id AND gen.base_project_name = LOWER(TRIM(p.project_name))
				GROUP BY p.client_Id
			";
		}

		$excludedClients = $filters['excludedClients'];
		$generalExclusion = $filters['generalExclusion'];
		$clientIdFilterSql = isset($filters['clientIdFilterSql']) ? $filters['clientIdFilterSql'] : '';
		$monthVisibilitySql = $this->month_project_visibility_sql('p', 'gen.year_val', 'gen.month_val');

		return "
			SELECT
				combined.client_Id,
				combined.year_val,
				combined.month_val,
				SUM(combined.timesheet_hours) AS timesheet_hours,
				MAX(combined.timesheet_date) AS timesheet_date
			FROM (
				SELECT p.client_Id,
					ts.year_val,
					ts.month_val,
					ts.timesheet_hours,
					ts.timesheet_date
				FROM ({$visibleSql}) p
				INNER JOIN (
					{$productionTsSql}
				) ts ON ts.project_Id = p.project_Id AND ts.client_Id = p.client_Id
				UNION ALL
				SELECT p.client_Id,
					gen.year_val,
					gen.month_val,
					gen.general_timesheet_hours AS timesheet_hours,
					gen.timesheet_date
				FROM project_details p
				INNER JOIN (
					{$generalTsSql}
				) gen ON gen.client_Id = p.client_Id AND gen.base_project_name = LOWER(TRIM(p.project_name))
				WHERE p.client_Id NOT IN ({$excludedClients})
				{$generalExclusion}
				{$clientIdFilterSql}
				AND {$monthVisibilitySql}
			) combined
			GROUP BY combined.client_Id, combined.year_val, combined.month_val
		";
	}

	public function get_management_plan_report($params) {
		$filters = $this->prepare_filters($params);
		$fromDate = $filters['fromDate'];
		$toDate = $filters['toDate'];
		$excludedClients = $filters['excludedClients'];
		$generalExclusion = $filters['generalExclusion'];
		$invoiceDateFilter = $filters['invoiceDateFilter'];
		$clientFilterSql = $filters['clientFilterSql'];
		$timesheetSql = $this->timesheet_hours_select_sql($filters, false);

		$periodFilterSql = '';
		if ($fromDate !== '' || $toDate !== '') {
			$fromEsc = $this->db->escape($fromDate !== '' ? $fromDate : '0001-01-01');
			$toEsc = $this->db->escape($toDate !== '' ? $toDate : '9999-12-31');
			$periodFilterSql = "
				AND (
					ts.timesheet_date IS NOT NULL
					OR COALESCE(inv.invoice_hours, 0) > 0
					OR (
						dates.client_start_date IS NOT NULL
						AND dates.client_start_date != '0000-00-00'
						AND dates.client_start_date <= {$toEsc}
						AND (
							dates.client_end_date IS NULL
							OR dates.client_end_date = '0000-00-00'
							OR dates.client_end_date >= {$fromEsc}
						)
					)
				)";
		}

		$sql = "
			SELECT
				c.client_Id,
				c.client_name,
				dates.client_start_date AS start_date,
				dates.client_end_date AS end_date,
				ts.timesheet_date,
				COALESCE(ts.timesheet_hours, 0) AS timesheet_hours,
				COALESCE(inv.invoice_hours, 0) AS invoice_hours
			FROM client_details c
			INNER JOIN (
				SELECT p.client_Id,
					MIN(CASE WHEN p.project_start_date IS NOT NULL AND p.project_start_date != '0000-00-00' THEN p.project_start_date END) AS client_start_date,
					MAX(CASE WHEN p.project_end_date IS NOT NULL AND p.project_end_date != '0000-00-00' THEN p.project_end_date END) AS client_end_date
				FROM project_details p
				WHERE p.client_Id NOT IN ({$excludedClients})
				{$generalExclusion}
				GROUP BY p.client_Id
			) dates ON dates.client_Id = c.client_Id
			LEFT JOIN (
				{$timesheetSql}
			) ts ON ts.client_Id = c.client_Id
			LEFT JOIN (
				SELECT p.client_Id, SUM(pim.invoice_hours) AS invoice_hours
				FROM project_invoice_monthly pim
				INNER JOIN project_details p ON p.project_Id = pim.project_Id
				WHERE p.client_Id NOT IN ({$excludedClients})
				{$generalExclusion}
				{$invoiceDateFilter}
				GROUP BY p.client_Id
			) inv ON inv.client_Id = c.client_Id
			WHERE c.client_Id NOT IN ({$excludedClients})
			{$clientFilterSql}
			{$periodFilterSql}
			ORDER BY COALESCE(dates.client_end_date, dates.client_start_date) DESC, c.client_name ASC
		";

		return $this->db->query($sql)->result();
	}

	public function get_month_wise_by_client($params, $clientIds = array()) {
		$filters = $this->prepare_filters($params);
		if (!empty($clientIds)) {
			$escapedClients = array();
			foreach ((array)$clientIds as $id) {
				$id = trim((string)$id);
				if ($id !== '') {
					$escapedClients[] = $this->db->escape($id);
				}
			}
			if (!empty($escapedClients)) {
				$inList = implode(',', $escapedClients);
				$filters['clientIdFilterSql'] = ' AND p.client_Id IN (' . $inList . ')';
				$filters['erdClientFilterSql'] = ' AND erd.client_Id IN (' . $inList . ')';
			}
		}

		$excludedClients = $filters['excludedClients'];
		$generalExclusion = $filters['generalExclusion'];
		$invoiceDateFilter = $filters['invoiceDateFilter'];
		$clientIdFilterSql = $filters['clientIdFilterSql'];
		$timesheetSql = $this->timesheet_hours_select_sql($filters, true);

		$sql = "
			SELECT
				combined.client_Id,
				combined.year_val,
				combined.month_val,
				SUM(combined.invoice_hours) AS invoice_hours,
				SUM(combined.timesheet_hours) AS timesheet_hours,
				MAX(combined.timesheet_date) AS timesheet_date
			FROM (
				SELECT p.client_Id,
					pim.invoice_year AS year_val,
					pim.invoice_month AS month_val,
					SUM(pim.invoice_hours) AS invoice_hours,
					0 AS timesheet_hours,
					NULL AS timesheet_date
				FROM project_invoice_monthly pim
				INNER JOIN project_details p ON p.project_Id = pim.project_Id
				WHERE p.client_Id NOT IN ({$excludedClients})
				{$generalExclusion}
				{$invoiceDateFilter}
				{$clientIdFilterSql}
				GROUP BY p.client_Id, pim.invoice_year, pim.invoice_month
				UNION ALL
				SELECT ts.client_Id,
					ts.year_val,
					ts.month_val,
					0 AS invoice_hours,
					ts.timesheet_hours,
					ts.timesheet_date
				FROM (
					{$timesheetSql}
				) ts
			) combined
			GROUP BY combined.client_Id, combined.year_val, combined.month_val
			ORDER BY combined.client_Id ASC, combined.year_val DESC, combined.month_val DESC
		";

		return $this->db->query($sql)->result();
	}

	public function get_filter_clients() {
		$excludedClients = $this->excluded_clients_sql();
		$clients = $this->db->query("
			SELECT c.client_Id, c.client_name
			FROM client_details c
			WHERE c.client_Id NOT IN ({$excludedClients})
			ORDER BY c.client_name ASC
		")->result();
		foreach ($clients as $client) {
			if (isset($client->client_name)) {
				$client->client_name = ucfirst(str_replace("'", " ", (string)$client->client_name));
			}
		}
		return $clients;
	}

	public function get_filter_years() {
		$currentYear = (int)date('Y');
		$years = array();
		for ($year = $currentYear; $year >= 2015; $year--) {
			$years[] = (object)array('year' => $year);
		}
		return $years;
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
}
