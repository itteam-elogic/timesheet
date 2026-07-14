<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('ts_department_options')) {
	function ts_department_options() {
		return array(
			'Architectural',
			'Structural',
			'3D Visualization',
			'2D Auto CAD',
			'MEP',
			'Software',
			'IT',
			'HR',
			'Recruiter',
			'Operations',
			'Marketing',
			'Accounting',
			'Business Development',
			'Others'
		);
	}
}

if (!function_exists('ts_primary_delivery_departments')) {
	function ts_primary_delivery_departments() {
		return array(
			'Architectural',
			'Structural',
			'3D Visualization',
			'2D Auto CAD',
			'MEP'
		);
	}
}

if (!function_exists('ts_department_summary_order')) {
	function ts_department_summary_order() {
		return array(
			'Architectural',
			'Structural',
			'3D Visualization',
			'2D Auto CAD',
			'MEP',
			'Software',
			'IT',
			'HR',
			'Recruiter',
			'Operations',
			'Marketing',
			'Accounting',
			'Business Development'
		);
	}
}

if (!function_exists('ts_normalize_primary_delivery_department')) {
	/**
	 * Map a raw department label to a canonical ts_primary_delivery_departments() name.
	 *
	 * @param string $dept
	 * @return string Canonical name, or trimmed original when not a primary delivery dept
	 */
	function ts_normalize_primary_delivery_department($dept)
	{
		$dept = (string) $dept;
		$dept = preg_replace('/[\x{00A0}\x{2000}-\x{200B}\x{FEFF}]/u', ' ', $dept);
		$dept = trim(preg_replace('/\s+/u', ' ', $dept));
		if ($dept === '') {
			return '';
		}

		$canonical = function_exists('ts_primary_delivery_departments')
			? ts_primary_delivery_departments()
			: array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');

		if (in_array($dept, $canonical, true)) {
			return $dept;
		}

		$lower = strtolower($dept);
		foreach ($canonical as $name) {
			if (strtolower($name) === $lower) {
				return $name;
			}
		}

		$compact = preg_replace('/\s+/', '', $lower);
		foreach ($canonical as $name) {
			if (preg_replace('/\s+/', '', strtolower($name)) === $compact) {
				return $name;
			}
		}

		$aliases = array(
			'2d autocad' => '2D Auto CAD',
			'2d auto-cad' => '2D Auto CAD',
			'2dautocad' => '2D Auto CAD',
			'3d visualization' => '3D Visualization',
			'3dvisualization' => '3D Visualization',
		);
		if (isset($aliases[$lower])) {
			return $aliases[$lower];
		}
		if (isset($aliases[$compact])) {
			return $aliases[$compact];
		}

		return $dept;
	}
}
