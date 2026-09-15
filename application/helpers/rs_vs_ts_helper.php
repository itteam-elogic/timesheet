<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sort key so Pradip Chauhan row appears immediately after Syed Afsar.
 */
function rs_vs_ts_manager_sort_key($managerName)
{
	$key = strtolower(preg_replace('/\s+/', ' ', trim((string)$managerName)));
	if ($key === 'pradip chauhan') {
		return 'syed afsar ';
	}
	return $key;
}

/**
 * Compare two manager names for RS vs TS display (summary, grid, email).
 */
function rs_vs_ts_compare_manager_names($nameA, $nameB)
{
	return strcasecmp(
		rs_vs_ts_manager_sort_key($nameA),
		rs_vs_ts_manager_sort_key($nameB)
	);
}

/**
 * Daily auto-email windows: 11:00 AM and 1:00 PM IST.
 */
function rs_vs_ts_due_slots($requestedSlot = null)
{
	$allowed = array('11am', '1pm');
	$requestedSlot = strtolower(trim((string)$requestedSlot));
	if ($requestedSlot !== '' && in_array($requestedSlot, $allowed, true)) {
		return array($requestedSlot);
	}
	$hour = (int) date('G');
	$due = array();
	if ($hour >= 11) {
		$due[] = '11am';
	}
	if ($hour >= 13) {
		$due[] = '1pm';
	}
	return $due;
}

function rs_vs_ts_slot_cron_name($slot)
{
	$CI =& get_instance();
	$prefix = $CI->config->item('rs_vs_ts_cron_slot_prefix');
	if (empty($prefix)) {
		$prefix = 'rs_vs_ts';
	}
	return $prefix . '_' . $slot;
}

function rs_vs_ts_ensure_cron_table()
{
	$CI =& get_instance();
	$CI->db->query("CREATE TABLE IF NOT EXISTS `app_cron_runs` (
		`cron_name` varchar(100) NOT NULL,
		`last_run_at` datetime NOT NULL,
		PRIMARY KEY (`cron_name`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8");
}

function rs_vs_ts_claim_slot($slot)
{
	$CI =& get_instance();
	rs_vs_ts_ensure_cron_table();
	$name = rs_vs_ts_slot_cron_name($slot);
	$today = date('Y-m-d');
	$row = $CI->db->get_where('app_cron_runs', array('cron_name' => $name))->row();
	if ($row && substr($row->last_run_at, 0, 10) === $today) {
		return false;
	}
	$now = date('Y-m-d H:i:s');
	if ($row) {
		$CI->db->where('cron_name', $name)->update('app_cron_runs', array('last_run_at' => $now));
	} else {
		$CI->db->insert('app_cron_runs', array(
			'cron_name' => $name,
			'last_run_at' => $now
		));
	}
	return true;
}

function rs_vs_ts_unclaim_slot($slot)
{
	$CI =& get_instance();
	rs_vs_ts_ensure_cron_table();
	$name = rs_vs_ts_slot_cron_name($slot);
	$CI->db->where('cron_name', $name)->update('app_cron_runs', array(
		'last_run_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
	));
}
