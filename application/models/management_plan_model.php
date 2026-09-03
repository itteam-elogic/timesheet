<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Management_plan_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	private function get_elogic_client_ids() {
		return array('363','374','370','369','368','367','364','361','355','270','262','253','236','210','85','78','74','49','34','32','428');
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

	private function excluded_clients_sql() {
		$escapedClientIds = array();
		foreach ($this->get_elogic_client_ids() as $clientId) {
			$escapedClientIds[] = $this->db->escape($clientId);
		}
		return implode(',', $escapedClientIds);
	}

	private function excluded_client_name_condition($clientIdColumn = 'c.client_Id') {
		return $clientIdColumn . " NOT IN (
			SELECT cexc.client_Id
			FROM client_details cexc
			WHERE LOWER(TRIM(cexc.client_name)) LIKE '%elogic%'
				OR LOWER(TRIM(cexc.client_name)) LIKE '%it team%'
		)";
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
			'nameExclusionC' => ' AND ' . $this->excluded_client_name_condition('c.client_Id'),
			'nameExclusionP' => ' AND ' . $this->excluded_client_name_condition('p.client_Id'),
			'nameExclusionErd' => ' AND ' . $this->excluded_client_name_condition('erd.client_Id')
		);
	}

	public function get_management_plan_report($params) {
		$filters = $this->prepare_filters($params);
		$fromDate = $filters['fromDate'];
		$toDate = $filters['toDate'];
		$excludedClients = $filters['excludedClients'];
		$generalExclusion = $filters['generalExclusion'];
		$tsDateFilter = $filters['tsDateFilter'];
		$invoiceDateFilter = $filters['invoiceDateFilter'];
		$clientFilterSql = $filters['clientFilterSql'];
		$nameExclusionC = $filters['nameExclusionC'];
		$nameExclusionP = $filters['nameExclusionP'];
		$nameExclusionErd = $filters['nameExclusionErd'];

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
						AND DATE(dates.client_start_date) <= {$toEsc}
						AND (
							dates.client_end_date IS NULL
							OR DATE(dates.client_end_date) >= {$fromEsc}
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
				COALESCE(inv.invoice_hours, 0) AS invoice_hours
			FROM client_details c
			INNER JOIN (
				SELECT p.client_Id,
					MIN(CASE WHEN p.project_start_date IS NOT NULL AND p.project_start_date != '0000-00-00' THEN p.project_start_date END) AS client_start_date,
					MAX(CASE WHEN p.project_end_date IS NOT NULL AND p.project_end_date != '0000-00-00' THEN p.project_end_date END) AS client_end_date
				FROM project_details p
				WHERE p.client_Id NOT IN ({$excludedClients})
				{$nameExclusionP}
				{$generalExclusion}
				GROUP BY p.client_Id
			) dates ON dates.client_Id = c.client_Id
			LEFT JOIN (
				SELECT erd.client_Id, MAX(erd.emp_report_dates) AS timesheet_date
				FROM emp_record_details erd
				INNER JOIN project_details p ON p.project_Id = erd.project_Id AND p.client_Id = erd.client_Id
				WHERE erd.client_Id NOT IN ({$excludedClients})
				{$nameExclusionErd}
				{$generalExclusion}
				{$tsDateFilter}
				GROUP BY erd.client_Id
			) ts ON ts.client_Id = c.client_Id
			LEFT JOIN (
				SELECT p.client_Id, SUM(pim.invoice_hours) AS invoice_hours
				FROM project_invoice_monthly pim
				INNER JOIN project_details p ON p.project_Id = pim.project_Id
				WHERE p.client_Id NOT IN ({$excludedClients})
				{$nameExclusionP}
				{$generalExclusion}
				{$invoiceDateFilter}
				GROUP BY p.client_Id
			) inv ON inv.client_Id = c.client_Id
			WHERE c.client_Id NOT IN ({$excludedClients})
			{$nameExclusionC}
			{$clientFilterSql}
			{$periodFilterSql}
			ORDER BY COALESCE(dates.client_end_date, dates.client_start_date) DESC, c.client_name ASC
		";

		return $this->db->query($sql)->result();
	}

	public function get_month_wise_by_client($params) {
		$filters = $this->prepare_filters($params);
		$excludedClients = $filters['excludedClients'];
		$generalExclusion = $filters['generalExclusion'];
		$tsDateFilter = $filters['tsDateFilter'];
		$invoiceDateFilter = $filters['invoiceDateFilter'];
		$clientIdFilterSql = $filters['clientIdFilterSql'];
		$erdClientFilterSql = str_replace('p.client_Id', 'erd.client_Id', $clientIdFilterSql);
		$nameExclusionP = $filters['nameExclusionP'];
		$nameExclusionErd = $filters['nameExclusionErd'];

		$sql = "
			SELECT
				months.client_Id,
				months.year_val,
				months.month_val,
				COALESCE(inv.invoice_hours, 0) AS invoice_hours,
				COALESCE(ts.timesheet_hours, 0) AS timesheet_hours,
				ts.timesheet_date
			FROM (
				SELECT p.client_Id, pim.invoice_year AS year_val, pim.invoice_month AS month_val
				FROM project_invoice_monthly pim
				INNER JOIN project_details p ON p.project_Id = pim.project_Id
				WHERE p.client_Id NOT IN ({$excludedClients})
				{$nameExclusionP}
				{$generalExclusion}
				{$invoiceDateFilter}
				{$clientIdFilterSql}
				GROUP BY p.client_Id, pim.invoice_year, pim.invoice_month
				UNION
				SELECT erd.client_Id, YEAR(erd.emp_report_dates) AS year_val, MONTH(erd.emp_report_dates) AS month_val
				FROM emp_record_details erd
				INNER JOIN project_details p ON p.project_Id = erd.project_Id AND p.client_Id = erd.client_Id
				WHERE erd.client_Id NOT IN ({$excludedClients})
				{$nameExclusionErd}
				{$generalExclusion}
				{$tsDateFilter}
				{$erdClientFilterSql}
				AND erd.emp_report_dates IS NOT NULL
				AND erd.emp_report_dates != '0000-00-00'
				GROUP BY erd.client_Id, YEAR(erd.emp_report_dates), MONTH(erd.emp_report_dates)
			) months
			LEFT JOIN (
				SELECT p.client_Id, pim.invoice_year AS year_val, pim.invoice_month AS month_val, SUM(pim.invoice_hours) AS invoice_hours
				FROM project_invoice_monthly pim
				INNER JOIN project_details p ON p.project_Id = pim.project_Id
				WHERE p.client_Id NOT IN ({$excludedClients})
				{$nameExclusionP}
				{$generalExclusion}
				{$invoiceDateFilter}
				{$clientIdFilterSql}
				GROUP BY p.client_Id, pim.invoice_year, pim.invoice_month
			) inv ON inv.client_Id = months.client_Id AND inv.year_val = months.year_val AND inv.month_val = months.month_val
			LEFT JOIN (
				SELECT erd.client_Id,
					YEAR(erd.emp_report_dates) AS year_val,
					MONTH(erd.emp_report_dates) AS month_val,
					SUM(erd.emp_time_hours) AS timesheet_hours,
					MAX(erd.emp_report_dates) AS timesheet_date
				FROM emp_record_details erd
				INNER JOIN project_details p ON p.project_Id = erd.project_Id AND p.client_Id = erd.client_Id
				WHERE erd.client_Id NOT IN ({$excludedClients})
				{$nameExclusionErd}
				{$generalExclusion}
				{$tsDateFilter}
				{$erdClientFilterSql}
				AND erd.emp_report_dates IS NOT NULL
				AND erd.emp_report_dates != '0000-00-00'
				GROUP BY erd.client_Id, YEAR(erd.emp_report_dates), MONTH(erd.emp_report_dates)
			) ts ON ts.client_Id = months.client_Id AND ts.year_val = months.year_val AND ts.month_val = months.month_val
			ORDER BY months.client_Id ASC, months.year_val DESC, months.month_val DESC
		";

		return $this->db->query($sql)->result();
	}

	public function get_filter_clients() {
		$this->db->select('c.client_Id, c.client_name');
		$this->db->from('client_details as c');
		$this->db->where_not_in('c.client_Id', $this->get_elogic_client_ids());
		$this->db->where($this->excluded_client_name_condition('c.client_Id'), null, false);
		$this->db->order_by('c.client_Id', 'desc');
		$clients = $this->db->get()->result();
		foreach ($clients as $client) {
			if (isset($client->client_name)) {
				$client->client_name = ucfirst(str_replace("'", " ", (string)$client->client_name));
			}
		}
		return $clients;
	}

	public function get_filter_years() {
		$excludedClientsSql = $this->excluded_clients_sql();
		$currentYear = (int)date('Y');

		$nameExclusionC = ' AND ' . $this->excluded_client_name_condition('c.client_Id');

		$sql = "SELECT DISTINCT year_val AS year FROM (
			SELECT YEAR(p.project_start_date) AS year_val
			FROM project_details p
			INNER JOIN client_details c ON c.client_Id = p.client_Id
			WHERE c.client_Id NOT IN ({$excludedClientsSql})
			{$nameExclusionC}
			AND p.project_start_date IS NOT NULL
			AND p.project_start_date != '0000-00-00'
			AND LOWER(COALESCE(p.project_name, '')) NOT LIKE '%general%'
			UNION
			SELECT YEAR(erd.emp_report_dates) AS year_val
			FROM emp_record_details erd
			INNER JOIN project_details p ON p.project_Id = erd.project_Id AND p.client_Id = erd.client_Id
			INNER JOIN client_details c ON c.client_Id = erd.client_Id
			WHERE c.client_Id NOT IN ({$excludedClientsSql})
			{$nameExclusionC}
			AND erd.emp_report_dates IS NOT NULL
			AND erd.emp_report_dates != '0000-00-00'
			AND LOWER(COALESCE(p.project_name, '')) NOT LIKE '%general%'
			UNION
			SELECT pim.invoice_year AS year_val
			FROM project_invoice_monthly pim
			INNER JOIN project_details p ON p.project_Id = pim.project_Id
			INNER JOIN client_details c ON c.client_Id = p.client_Id
			WHERE c.client_Id NOT IN ({$excludedClientsSql})
			{$nameExclusionC}
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
}
