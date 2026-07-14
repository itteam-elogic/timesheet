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
