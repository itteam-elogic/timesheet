<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Format hours for KPI report display (no unit suffix).
 */
if (!function_exists('kpi_hours_display')) {
    function kpi_hours_display($value)
    {
        $value = (float) $value;
        if (fmod($value, 1.0) == 0.0) {
            return (string) (int) $value;
        }
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}

/**
 * Department summary table hours cell (numeric only).
 */
if (!function_exists('kpi_summary_hours_cell')) {
    function kpi_summary_hours_cell($hours)
    {
        return kpi_hours_display($hours);
    }
}

/**
 * Department summary table hours cell for client report.
 */
if (!function_exists('client_report_dept_kpi_hours_cell')) {
    function client_report_dept_kpi_hours_cell($value)
    {
        if ($value === null || $value === '') {
            return '-';
        }
        return kpi_summary_hours_cell($value);
    }
}

/**
 * Department summary table % cell for client report.
 */
if (!function_exists('client_report_dept_kpi_pct_cell')) {
    function client_report_dept_kpi_pct_cell($value)
    {
        if ($value === null || $value === '') {
            return '-';
        }
        return (int) round((float) $value) . '%';
    }
}

/**
 * Department summary table decimal cell for client report.
 */
if (!function_exists('client_report_dept_kpi_num_cell')) {
    function client_report_dept_kpi_num_cell($value)
    {
        if ($value === null || $value === '') {
            return '-';
        }
        return number_format((float) $value, 2, '.', '');
    }
}

/**
 * Department summary table difference cell for client report (HTML).
 */
if (!function_exists('client_report_dept_kpi_diff_cell')) {
    function client_report_dept_kpi_diff_cell($value)
    {
        if ($value === null || $value === '') {
            return '-';
        }
        $n = (float) $value;
        $color = $n >= 0 ? '#28a745' : '#dc3545';
        $text = client_report_dept_kpi_num_cell($value);
        return '<span style="color:' . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . ';">'
            . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</span>';
    }
}

/**
 * Parse structured client-report grid filters from request-style grid array.
 *
 * @param array $grid Keys: clients, pms, project
 * @return array{client_terms: array, pm_terms: array, project_term: string}
 */
if (!function_exists('client_report_grid_filter_terms')) {
    function client_report_grid_filter_terms(array $grid)
    {
        $clientTerms = array();
        if (!empty($grid['clients']) && is_array($grid['clients'])) {
            foreach ($grid['clients'] as $c) {
                $t = strtolower(trim((string) $c));
                if ($t !== '') {
                    $clientTerms[] = $t;
                }
            }
        }

        $pmTerms = array();
        if (!empty($grid['pms']) && is_array($grid['pms'])) {
            foreach ($grid['pms'] as $p) {
                $t = strtolower(trim((string) $p));
                if ($t !== '') {
                    $pmTerms[] = $t;
                }
            }
        }

        $projectTerm = '';
        if (!empty($grid['project'])) {
            $projectTerm = strtolower(trim((string) $grid['project']));
        }

        return array(
            'client_terms' => array_values(array_unique($clientTerms)),
            'pm_terms' => array_values(array_unique($pmTerms)),
            'project_term' => $projectTerm,
        );
    }
}

/**
 * True when haystack contains any of the filter terms (case-insensitive substring).
 *
 * @param string $haystack
 * @param array $terms
 * @return bool
 */
if (!function_exists('client_report_matches_any_term')) {
    function client_report_matches_any_term($haystack, array $terms)
    {
        $haystack = strtolower(trim((string) $haystack));
        if ($haystack === '' || empty($terms)) {
            return false;
        }
        foreach ($terms as $term) {
            if ($term !== '' && strpos($haystack, $term) !== false) {
                return true;
            }
        }
        return false;
    }
}

/**
 * Parse a project/client date for KPI client report display.
 *
 * @param mixed $dateStr
 * @return int|false Unix timestamp or false when invalid
 */
if (!function_exists('client_report_client_date_ts')) {
    function client_report_client_date_ts($dateStr)
    {
        if (empty($dateStr) || $dateStr == '0000-00-00' || $dateStr == '0000-00-00 00:00:00') {
            return false;
        }
        $timestamp = strtotime($dateStr);
        if ($timestamp === false || $timestamp < 0) {
            return false;
        }
        if (date('Y-m-d', $timestamp) == '1970-01-01') {
            return false;
        }
        return $timestamp;
    }
}

/**
 * Resolve client-row start/end dates (SQL MIN/MAX), with fallback to project row fields.
 *
 * @param array $data Grouped client data from client report
 * @return array{start_ts: int|false, end_ts: int|false}
 */
if (!function_exists('client_report_resolve_client_dates')) {
    function client_report_resolve_client_dates(array $data)
    {
        $earliestStart = false;
        $latestEnd = false;

        $considerStart = function ($dateStr) use (&$earliestStart) {
            $ts = client_report_client_date_ts($dateStr);
            if ($ts !== false && ($earliestStart === false || $ts < $earliestStart)) {
                $earliestStart = $ts;
            }
        };
        $considerEnd = function ($dateStr) use (&$latestEnd) {
            $ts = client_report_client_date_ts($dateStr);
            if ($ts !== false && ($latestEnd === false || $ts > $latestEnd)) {
                $latestEnd = $ts;
            }
        };

        if (!empty($data['client_start_date'])) {
            $considerStart($data['client_start_date']);
        }
        if (!empty($data['client_end_date'])) {
            $considerEnd($data['client_end_date']);
        }

        if (!empty($data['projects']) && is_array($data['projects'])) {
            foreach ($data['projects'] as $proj) {
                if (!empty($proj->client_start_date)) {
                    $considerStart($proj->client_start_date);
                }
                if (!empty($proj->client_end_date)) {
                    $considerEnd($proj->client_end_date);
                }
                if (!empty($proj->project_start_date)) {
                    $considerStart($proj->project_start_date);
                }
                $endDateToUse = null;
                if (!empty($proj->project_end_date)
                    && $proj->project_end_date != '0000-00-00'
                    && $proj->project_end_date != '0000-00-00 00:00:00') {
                    $endDateToUse = $proj->project_end_date;
                } elseif (!empty($proj->last_work_date)) {
                    $endDateToUse = $proj->last_work_date;
                }
                if ($endDateToUse !== null) {
                    $considerEnd($endDateToUse);
                }
            }
        }

        return array(
            'start_ts' => $earliestStart,
            'end_ts' => $latestEnd,
        );
    }
}

/**
 * Format client-row date for grid / Excel (d-M-Y).
 *
 * @param int|false|null $timestamp
 * @return string
 */
if (!function_exists('client_report_format_client_date_display')) {
    function client_report_format_client_date_display($timestamp)
    {
        if ($timestamp === false || $timestamp === null) {
            return '';
        }
        $formattedDate = date('d-M-Y', $timestamp);
        return ($formattedDate != '01-Jan-1970') ? $formattedDate : '';
    }
}

/**
 * Legacy OR filter across merged search terms (free-text / comma-separated search only).
 *
 * @param array $grouped
 * @param array $terms Lowercase terms
 * @return array
 */
if (!function_exists('client_report_filter_grouped_or_terms')) {
    function client_report_filter_grouped_or_terms(array $grouped, array $terms)
    {
        if (empty($terms)) {
            return $grouped;
        }

        $filtered = array();
        foreach ($grouped as $clientId => $data) {
            $clientName = isset($data['client_name']) ? $data['client_name'] : '';
            $clientPmName = isset($data['client_pm_name']) ? $data['client_pm_name'] : '';
            if ($clientPmName === '' && !empty($data['projects'][0]->client_pm_name)) {
                $clientPmName = $data['projects'][0]->client_pm_name;
            }

            $clientMatches = client_report_matches_any_term($clientName, $terms)
                || client_report_matches_any_term($clientPmName, $terms);

            $matchingProjects = array();
            if (!empty($data['projects']) && is_array($data['projects'])) {
                foreach ($data['projects'] as $proj) {
                    $projName = isset($proj->project_name) ? $proj->project_name : '';
                    $projPm = isset($proj->pm_name) ? $proj->pm_name : '';
                    if (client_report_matches_any_term($projName, $terms)
                        || client_report_matches_any_term($projPm, $terms)) {
                        $matchingProjects[] = $proj;
                    }
                }
            }

            if ($clientMatches) {
                $filtered[$clientId] = $data;
            } elseif (!empty($matchingProjects)) {
                $data['projects'] = $matchingProjects;
                $filtered[$clientId] = $data;
            }
        }

        return $filtered;
    }
}

/**
 * Filter grouped client-report data (matches grid / Excel / department summary scope).
 * When a project filter is set, only matching project rows are kept (PM/client filters AND).
 *
 * @param array $grouped
 * @param array $grid Keys: clients, pms, project
 * @param string $search Legacy merged search string
 * @return array
 */
if (!function_exists('client_report_filter_grouped_data')) {
    function client_report_filter_grouped_data(array $grouped, array $grid, $search = '')
    {
        $parsed = client_report_grid_filter_terms($grid);
        $clientTerms = $parsed['client_terms'];
        $pmTerms = $parsed['pm_terms'];
        $projectTerm = $parsed['project_term'];

        $legacyTerms = array();
        if ($search !== '') {
            foreach (array_filter(array_map('trim', explode(',', (string) $search))) as $term) {
                $t = strtolower($term);
                if ($t !== '') {
                    $legacyTerms[] = $t;
                }
            }
            $legacyTerms = array_values(array_unique($legacyTerms));
        }

        $hasStructured = !empty($clientTerms) || !empty($pmTerms) || $projectTerm !== '';
        if (!$hasStructured && empty($legacyTerms)) {
            return $grouped;
        }
        if (!$hasStructured) {
            return client_report_filter_grouped_or_terms($grouped, $legacyTerms);
        }

        $filtered = array();
        foreach ($grouped as $clientId => $data) {
            $clientName = isset($data['client_name']) ? (string) $data['client_name'] : '';
            $clientPmName = isset($data['client_pm_name']) ? (string) $data['client_pm_name'] : '';
            if ($clientPmName === '' && !empty($data['projects'][0]->client_pm_name)) {
                $clientPmName = (string) $data['projects'][0]->client_pm_name;
            }
            $projects = (!empty($data['projects']) && is_array($data['projects'])) ? $data['projects'] : array();

            if ($projectTerm !== '') {
                $matchingProjects = array();
                foreach ($projects as $proj) {
                    $projName = isset($proj->project_name) ? (string) $proj->project_name : '';
                    if (strpos(strtolower($projName), $projectTerm) === false) {
                        continue;
                    }
                    if (!empty($clientTerms) && !client_report_matches_any_term($clientName, $clientTerms)) {
                        continue;
                    }
                    if (!empty($pmTerms)) {
                        $projPm = isset($proj->pm_name) ? (string) $proj->pm_name : '';
                        if (!client_report_matches_any_term($clientPmName, $pmTerms)
                            && !client_report_matches_any_term($projPm, $pmTerms)) {
                            continue;
                        }
                    }
                    $matchingProjects[] = $proj;
                }
                if (!empty($matchingProjects)) {
                    $data['projects'] = $matchingProjects;
                    $filtered[$clientId] = $data;
                }
                continue;
            }

            $passesClient = empty($clientTerms) || client_report_matches_any_term($clientName, $clientTerms);
            $clientPmMatches = !empty($pmTerms) && client_report_matches_any_term($clientPmName, $pmTerms);

            if ($passesClient && empty($pmTerms)) {
                $filtered[$clientId] = $data;
                continue;
            }

            if ($passesClient && $clientPmMatches) {
                $filtered[$clientId] = $data;
                continue;
            }

            if ($passesClient && !empty($pmTerms)) {
                $matchingProjects = array();
                foreach ($projects as $proj) {
                    $projPm = isset($proj->pm_name) ? (string) $proj->pm_name : '';
                    if (client_report_matches_any_term($projPm, $pmTerms)) {
                        $matchingProjects[] = $proj;
                    }
                }
                if (!empty($matchingProjects)) {
                    $data['projects'] = $matchingProjects;
                    $filtered[$clientId] = $data;
                }
                continue;
            }

            if (empty($clientTerms) && $clientPmMatches) {
                $filtered[$clientId] = $data;
                continue;
            }

            if (empty($clientTerms) && !empty($pmTerms)) {
                $matchingProjects = array();
                foreach ($projects as $proj) {
                    $projPm = isset($proj->pm_name) ? (string) $proj->pm_name : '';
                    if (client_report_matches_any_term($projPm, $pmTerms)) {
                        $matchingProjects[] = $proj;
                    }
                }
                if (!empty($matchingProjects)) {
                    $data['projects'] = $matchingProjects;
                    $filtered[$clientId] = $data;
                }
            }
        }

        return $filtered;
    }
}

/**
 * Filter flat client-report project rows using the same rules as the grouped filter.
 *
 * @param array $projects
 * @param array $grid
 * @param string $search
 * @return array
 */
if (!function_exists('client_report_filter_project_rows')) {
    function client_report_filter_project_rows(array $projects, array $grid, $search = '')
    {
        if (empty($projects)) {
            return $projects;
        }

        $grouped = array();
        foreach ($projects as $proj) {
            $clientId = isset($proj->client_Id) ? (string) $proj->client_Id : '';
            if ($clientId === '') {
                $clientId = 'row_' . count($grouped);
            }
            if (!isset($grouped[$clientId])) {
                $grouped[$clientId] = array(
                    'client_name' => isset($proj->client_name) ? $proj->client_name : '',
                    'client_pm_name' => isset($proj->client_pm_name) ? $proj->client_pm_name : '',
                    'projects' => array(),
                );
            }
            $grouped[$clientId]['projects'][] = $proj;
        }

        $filtered = client_report_filter_grouped_data($grouped, $grid, $search);
        $rows = array();
        foreach ($filtered as $data) {
            if (empty($data['projects']) || !is_array($data['projects'])) {
                continue;
            }
            foreach ($data['projects'] as $proj) {
                $rows[] = $proj;
            }
        }

        return $rows;
    }
}

/**
 * Search terms from consolidated KPI manager filter (comma-separated or array).
 */
if (!function_exists('kpi_consolidated_search_terms')) {
    function kpi_consolidated_search_terms($search)
    {
        if ($search === null || $search === '') {
            return array();
        }
        if (is_array($search)) {
            return array_values(array_filter(array_map('trim', $search)));
        }
        return array_values(array_filter(array_map('trim', explode(',', (string) $search))));
    }
}

/**
 * Column heading: "Team" when a manager is filtered, else "Reporting Manager".
 */
if (!function_exists('kpi_consolidated_team_column_heading')) {
    function kpi_consolidated_team_column_heading($search)
    {
        return !empty(kpi_consolidated_search_terms($search)) ? 'Team' : 'Reporting Manager';
    }
}

/**
 * Team cell label when filtering by manager on consolidated report.
 * - Row is the filtered manager → "{Name} - Team"
 * - Row is another reporting manager → filtered manager name (e.g. show Nikhil instead of Pradip)
 */
if (!function_exists('kpi_consolidated_team_display_label')) {
    function kpi_consolidated_team_display_label($mgrId, $actualManagerName, $managerNamesById, $search)
    {
        $terms = kpi_consolidated_search_terms($search);
        if (empty($terms)) {
            $actual = trim((string) $actualManagerName);
            if ($actual === '' || $actual === '--') {
                if ($mgrId !== '' && isset($managerNamesById[$mgrId])) {
                    $actual = trim((string) $managerNamesById[$mgrId]);
                }
            }
            return $actual !== '' ? $actual : '--';
        }
        $primaryLabel = $terms[0];
        foreach (kpi_consolidated_filter_manager_profiles($search) as $profile) {
            if (!empty($profile['name'])) {
                $primaryLabel = $profile['name'];
                break;
            }
        }
        if ($mgrId === '__self__') {
            return $primaryLabel;
        }
        if (kpi_consolidated_is_filtered_manager_team_row($mgrId, $search)) {
            return $primaryLabel . ' - Team';
        }
        return $primaryLabel;
    }
}

/**
 * Manager profile(s) matching search (empId, name, department) — prefers exact name match.
 */
if (!function_exists('kpi_consolidated_filter_manager_profiles')) {
    function kpi_consolidated_filter_manager_profiles($search)
    {
        $terms = kpi_consolidated_search_terms($search);
        if (empty($terms)) {
            return array();
        }
        $allKpi = function_exists('ts_primary_delivery_departments')
            ? ts_primary_delivery_departments()
            : array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');
        $CI = get_instance();
        $profiles = array();
        $seen = array();
        foreach ($terms as $term) {
            $rows = $CI->db->select('empId, name, department')
                ->from('employee_details')
                ->where('status', 'Active')
                ->where('name', $term)
                ->get()
                ->result();
            if (empty($rows)) {
                $rows = $CI->db->select('empId, name, department')
                    ->from('employee_details')
                    ->where('status', 'Active')
                    ->like('name', $term, 'both')
                    ->get()
                    ->result();
            }
            foreach ($rows as $r) {
                $empId = isset($r->empId) ? $r->empId : '';
                if ($empId === '' || isset($seen[$empId])) {
                    continue;
                }
                $d = trim((string) $r->department);
                if ($d === '' || !in_array($d, $allKpi, true)) {
                    continue;
                }
                $seen[$empId] = true;
                $profiles[] = array(
                    'empId' => $empId,
                    'name' => trim((string) $r->name),
                    'department' => $d,
                );
            }
        }
        return $profiles;
    }
}

/**
 * Delivery departments for the employee record(s) matching the manager search name(s).
 */
if (!function_exists('kpi_consolidated_filter_manager_departments')) {
    function kpi_consolidated_filter_manager_departments($search)
    {
        $profiles = kpi_consolidated_filter_manager_profiles($search);
        if (empty($profiles)) {
            return array();
        }
        $depts = array();
        foreach ($profiles as $p) {
            if (!empty($p['department'])) {
                $depts[] = $p['department'];
            }
        }
        return array_values(array_unique($depts));
    }
}

if (!function_exists('kpi_consolidated_filter_manager_emp_ids')) {
    function kpi_consolidated_filter_manager_emp_ids($search)
    {
        $profiles = kpi_consolidated_filter_manager_profiles($search);
        $ids = array();
        foreach ($profiles as $p) {
            if (!empty($p['empId'])) {
                $ids[] = $p['empId'];
            }
        }
        return $ids;
    }
}

if (!function_exists('kpi_consolidated_is_filtered_manager_team_row')) {
    function kpi_consolidated_is_filtered_manager_team_row($mgrId, $search)
    {
        if ($mgrId === '' || $mgrId === '__self__') {
            return false;
        }
        foreach (kpi_consolidated_filter_manager_emp_ids($search) as $id) {
            if ((string) $id === (string) $mgrId) {
                return true;
            }
        }
        return false;
    }
}

/**
 * Ordered Department Summary rows for one department.
 * With manager search: own timesheet row, then "- Team" only (no other reporting managers).
 *
 * @return array<int, array{mgrId: string, stats: array}>
 */
if (!function_exists('kpi_consolidated_dept_summary_display_rows')) {
    function kpi_consolidated_dept_summary_display_rows($dept, array $mgrBlocks, array $managerSelfTotalsByDept, $search, $showAllDeptRows)
    {
        $hasSearch = !empty(kpi_consolidated_search_terms($search));
        $filterEmpIds = $hasSearch ? kpi_consolidated_filter_manager_emp_ids($search) : array();
        $filterEmpIdStr = array();
        foreach ($filterEmpIds as $id) {
            $filterEmpIdStr[(string) $id] = true;
        }
        $rows = array();
        if ($hasSearch && isset($managerSelfTotalsByDept[$dept])) {
            $selfStats = $managerSelfTotalsByDept[$dept];
            if (!empty($selfStats['totalHours']) || $showAllDeptRows) {
                $rows[] = array('mgrId' => '__self__', 'stats' => $selfStats);
            }
        }
        $mgrIds = array_keys($mgrBlocks);
        if (!$hasSearch) {
            foreach ($mgrIds as $mgrId) {
                $stats = $mgrBlocks[$mgrId];
                if (!empty($stats['count']) || $showAllDeptRows) {
                    $rows[] = array('mgrId' => (string) $mgrId, 'stats' => $stats);
                }
            }
            return $rows;
        }
        foreach ($mgrIds as $mgrId) {
            if (!isset($filterEmpIdStr[(string) $mgrId])) {
                continue;
            }
            $stats = $mgrBlocks[$mgrId];
            if (!empty($stats['count']) || $showAllDeptRows) {
                $rows[] = array('mgrId' => (string) $mgrId, 'stats' => $stats);
            }
        }
        return $rows;
    }
}

/**
 * Departments to render in consolidated Department Summary (narrows to manager's dept when searching).
 */
if (!function_exists('kpi_consolidated_summary_departments_to_show')) {
    function kpi_consolidated_summary_departments_to_show($departmentsToShow, $search)
    {
        $mgrDepts = kpi_consolidated_filter_manager_departments($search);
        if (empty($mgrDepts)) {
            return $departmentsToShow;
        }
        $filtered = array_values(array_intersect($departmentsToShow, $mgrDepts));
        return !empty($filtered) ? $filtered : $mgrDepts;
    }
}

/**
 * Normalize KPI search input (multi-select array or comma-separated string) for display and queries.
 */
if (!function_exists('kpi_normalize_search_display')) {
    function kpi_normalize_search_display($search)
    {
        if (is_array($search)) {
            $terms = array_values(array_unique(array_filter(array_map('trim', $search), function ($term) {
                return $term !== '';
            })));
            return implode(', ', $terms);
        }

        $terms = array_values(array_unique(array_filter(array_map('trim', explode(',', (string) $search)), function ($term) {
            return $term !== '';
        })));
        return implode(', ', $terms);
    }
}

/**
 * Month-wise KPI date range label, e.g. "JAN - 2026 to JUN - 2026".
 */
if (!function_exists('kpi_month_wise_range_label')) {
    function kpi_month_wise_range_label($from_date, $to_date)
    {
        if (empty($from_date) || empty($to_date)) {
            return '';
        }

        $shortMonths = array(
            1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AUG',
            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DEC',
        );

        try {
            $from = new DateTime($from_date);
            $to = new DateTime($to_date);
        } catch (Exception $e) {
            return '';
        }

        $fromMonth = (int) $from->format('n');
        $toMonth = (int) $to->format('n');
        $fromLabel = (isset($shortMonths[$fromMonth]) ? $shortMonths[$fromMonth] : strtoupper($from->format('M')))
            . ' - ' . $from->format('Y');
        $toLabel = (isset($shortMonths[$toMonth]) ? $shortMonths[$toMonth] : strtoupper($to->format('M')))
            . ' - ' . $to->format('Y');

        if ($fromLabel === $toLabel) {
            return $fromLabel;
        }

        return $fromLabel . ' to ' . $toLabel;
    }
}

/**
 * Build Individual KPI summary table heading (department, date range, search names).
 */
if (!function_exists('kpi_month_wise_summary_heading')) {
    function kpi_month_wise_summary_heading($department, $search, $from_date, $to_date)
    {
        $department = trim((string) $department);
        if ($department === '__all__') {
            $department = '';
        }

        $heading = ($department !== '') ? ($department . ' KPI Report') : 'KPI Report';
        $rangeLabel = kpi_month_wise_range_label($from_date, $to_date);
        if ($rangeLabel !== '') {
            $heading .= ' - ' . $rangeLabel;
        }

        $searchDisplay = kpi_normalize_search_display($search);
        if ($searchDisplay !== '') {
            $heading .= ' ( ' . $searchDisplay . ' )';
        }

        return $heading;
    }
}

if (!function_exists('kpi_month_wise_is_member_summary_mode')) {
    function kpi_month_wise_is_member_summary_mode($search)
    {
        return kpi_normalize_search_display($search) !== '';
    }
}

if (!function_exists('kpi_month_wise_summary_month_pairs')) {
    function kpi_month_wise_summary_month_pairs($from_date, $to_date)
    {
        $pairs = array();
        if (!empty($from_date) && !empty($to_date)) {
            try {
                $start = new DateTime($from_date);
                $end = new DateTime($to_date);
                $cursor = clone $start;
                $cursor->modify('first day of this month');
                while ($cursor <= $end) {
                    $pairs[] = array(
                        'month' => (int) $cursor->format('n'),
                        'year' => (int) $cursor->format('Y'),
                    );
                    $cursor->modify('+1 month');
                }
            } catch (Exception $e) {
                $pairs = array();
            }
        }
        if (empty($pairs)) {
            $pairs[] = array(
                'month' => (int) date('n', strtotime('first day of previous month')),
                'year' => (int) date('Y', strtotime('first day of previous month')),
            );
        }
        return $pairs;
    }
}

if (!function_exists('kpi_month_wise_month_hours_map')) {
    function kpi_month_wise_month_hours_map()
    {
        return array(
            1 => 178.5, 2 => 170.0, 3 => 161.5, 4 => 187.0,
            5 => 178.5, 6 => 178.5, 7 => 195.5, 8 => 170.0,
            9 => 187.0, 10 => 170.0, 11 => 170.0, 12 => 187.0,
        );
    }
}

if (!function_exists('kpi_month_wise_fetch_production_hours')) {
    function kpi_month_wise_fetch_production_hours($kpi_reports_model, $empId, $department, $month, $year, array $preload = array())
    {
        if (isset($preload['production'][$empId][$month][$year])) {
            return $preload['production'][$empId][$month][$year];
        }
        if (!$kpi_reports_model) {
            return '0@#===0@#===0';
        }
        if ($department === 'MEP') {
            return $kpi_reports_model->empProductionHoursMonthWiseMEP($empId, $month, $year);
        }
        return $kpi_reports_model->empProductionHoursMonthWiseAllStatus($empId, $month, $year);
    }
}

if (!function_exists('kpi_month_wise_employee_period_totals')) {
    function kpi_month_wise_employee_period_totals($kpiResult, array $monthPairs, array $preload, $kpi_reports_model = null)
    {
        $empId = isset($kpiResult->empId) ? $kpiResult->empId : null;
        $department = isset($kpiResult->department) ? $kpiResult->department : '';
        if ($empId === null || $empId === '') {
            return null;
        }

        $monthHoursMap = kpi_month_wise_month_hours_map();
        $monthlyTotals = array(
            'totalProd' => 0, 'totalGen' => 0, 'totalElog' => 0,
            'totalHours' => 0, 'totalWorkHrs' => 0, 'validMonths' => 0,
        );

        foreach ($monthPairs as $pair) {
            $month = (int) $pair['month'];
            $yearForMonth = (int) $pair['year'];
            $hoursStr = kpi_month_wise_fetch_production_hours(
                $kpi_reports_model,
                $empId,
                $department,
                $month,
                $yearForMonth,
                $preload
            );
            $hours = explode('@#===', (string) $hoursStr);
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

        if ($monthlyTotals['validMonths'] === 0 || $monthlyTotals['totalHours'] <= 0) {
            return null;
        }

        return $monthlyTotals;
    }
}

if (!function_exists('kpi_month_wise_metrics_from_period_totals')) {
    function kpi_month_wise_metrics_from_period_totals(array $monthlyTotals)
    {
        $totalProd = $monthlyTotals['totalProd'];
        $totalGen = $monthlyTotals['totalGen'];
        $totalElog = $monthlyTotals['totalElog'];
        $totalHours = $monthlyTotals['totalHours'];
        $totalWorkHrs = $monthlyTotals['totalWorkHrs'];
        $totalUtilHours = $totalProd + $totalGen;

        return array(
            'totalProd' => $totalProd,
            'totalGen' => $totalGen,
            'totalElog' => $totalElog,
            'totalHours' => $totalHours,
            'totalWorkHrs' => $totalWorkHrs,
            'totalUtilHours' => $totalUtilHours,
            'productivity' => $totalHours > 0 ? round(($totalProd / $totalHours) * 100) : 0,
            'projectGen' => $totalHours > 0 ? round(($totalGen / $totalHours) * 100) : 0,
            'elogicGen' => $totalHours > 0 ? round(($totalElog / $totalHours) * 100) : 0,
            'availability' => $totalWorkHrs > 0 ? round(($totalHours / $totalWorkHrs) * 100) : 0,
            'utilization' => $totalHours > 0 ? round(($totalUtilHours / $totalHours) * 100) : 0,
        );
    }
}

if (!function_exists('kpi_month_wise_build_department_summary_rows')) {
    function kpi_month_wise_build_department_summary_rows(
        array $getkpiReportsSummary,
        array $monthPairs,
        array $preload,
        $kpi_reports_model,
        array $departments,
        $departmentFilter = ''
    ) {
        $primaryDepartments = function_exists('ts_primary_delivery_departments')
            ? ts_primary_delivery_departments()
            : array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');

        if (!empty($departmentFilter) && $departmentFilter !== '__all__') {
            $departments = in_array($departmentFilter, $departments, true) ? array($departmentFilter) : array($departmentFilter);
        } elseif (empty($departments)) {
            $departments = $primaryDepartments;
        }

        $totals = array();
        foreach ($getkpiReportsSummary as $kpiResult) {
            $dept = isset($kpiResult->department) ? trim((string) $kpiResult->department) : '';
            if ($dept === '' || !in_array($dept, $primaryDepartments, true)) {
                continue;
            }
            if (!empty($departmentFilter) && $departmentFilter !== '__all__' && $dept !== $departmentFilter) {
                continue;
            }

            $monthlyTotals = kpi_month_wise_employee_period_totals($kpiResult, $monthPairs, $preload, $kpi_reports_model);
            if ($monthlyTotals === null) {
                continue;
            }

            if (!isset($totals[$dept])) {
                $totals[$dept] = array(
                    'count' => 0,
                    'totalProd' => 0, 'totalGen' => 0, 'totalElog' => 0,
                    'totalHours' => 0, 'totalWorkHrs' => 0, 'totalUtilHours' => 0,
                );
            }

            $totals[$dept]['count']++;
            $totals[$dept]['totalProd'] += $monthlyTotals['totalProd'];
            $totals[$dept]['totalGen'] += $monthlyTotals['totalGen'];
            $totals[$dept]['totalElog'] += $monthlyTotals['totalElog'];
            $totals[$dept]['totalHours'] += $monthlyTotals['totalHours'];
            $totals[$dept]['totalWorkHrs'] += $monthlyTotals['totalWorkHrs'];
            $totals[$dept]['totalUtilHours'] += ($monthlyTotals['totalProd'] + $monthlyTotals['totalGen']);
        }

        $rows = array();
        foreach ($departments as $dept) {
            if (!isset($totals[$dept]) || $totals[$dept]['count'] === 0) {
                continue;
            }
            $rows[] = array(
                'label' => $dept,
                'metrics' => kpi_month_wise_metrics_from_period_totals($totals[$dept]),
                'row_attr' => "data-department='" . htmlspecialchars($dept, ENT_QUOTES, 'UTF-8') . "'",
            );
        }

        return $rows;
    }
}

if (!function_exists('kpi_month_wise_build_member_summary_rows')) {
    function kpi_month_wise_build_member_summary_rows(
        array $getkpiReportsSummary,
        array $monthPairs,
        array $preload,
        $kpi_reports_model
    ) {
        $rows = array();
        foreach ($getkpiReportsSummary as $kpiResult) {
            $monthlyTotals = kpi_month_wise_employee_period_totals($kpiResult, $monthPairs, $preload, $kpi_reports_model);
            if ($monthlyTotals === null) {
                continue;
            }

            $label = isset($kpiResult->name) ? trim((string) $kpiResult->name) : '';
            if ($label === '' && isset($kpiResult->emp_com_id)) {
                $label = (string) $kpiResult->emp_com_id;
            }
            $empId = isset($kpiResult->empId) ? $kpiResult->empId : '';

            $rows[] = array(
                'label' => $label,
                'metrics' => kpi_month_wise_metrics_from_period_totals($monthlyTotals),
                'row_attr' => "data-employee='" . htmlspecialchars((string) $empId, ENT_QUOTES, 'UTF-8') . "'",
            );
        }

        return $rows;
    }
}

if (!function_exists('kpi_month_wise_summary_table_row_html')) {
    function kpi_month_wise_summary_table_row_html($label, array $metrics, $rowAttr = '')
    {
        $attr = trim((string) $rowAttr);
        $openTag = $attr !== '' ? "<tr {$attr}>" : '<tr>';

        return $openTag
            . "<td class='kpi-dept-col-dept'>" . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . "</td>"
            . "<td class='kpi-dept-col-hrs kpi-dept-bg-prod kpi-dept-hrs-group-start' title='Productivity Hours'>" . kpi_summary_hours_cell($metrics['totalProd']) . "</td>"
            . "<td class='kpi-dept-col-hrs kpi-dept-bg-proj' title='Project General Hours'>" . kpi_summary_hours_cell($metrics['totalGen']) . "</td>"
            . "<td class='kpi-dept-col-hrs kpi-dept-bg-elog' title='eLogic General Hours'>" . kpi_summary_hours_cell($metrics['totalElog']) . "</td>"
            . "<td class='kpi-dept-col-hrs kpi-dept-bg-avail' title='Availability Hours'>" . kpi_summary_hours_cell($metrics['totalHours']) . "</td>"
            . "<td class='kpi-dept-col-hrs kpi-dept-bg-util' title='Utilization Hours'>" . kpi_summary_hours_cell($metrics['totalUtilHours']) . "</td>"
            . "<td class='kpi-dept-col-total kpi-dept-bg-total' title='Total Available Hours'>" . kpi_summary_hours_cell($metrics['totalHours']) . "</td>"
            . "<td class='kpi-dept-col-pct kpi-dept-bg-prod kpi-dept-pct-group-start' title='Productivity'>" . (int) $metrics['productivity'] . "%</td>"
            . "<td class='kpi-dept-col-pct kpi-dept-bg-proj' title='Project General'>" . (int) $metrics['projectGen'] . "%</td>"
            . "<td class='kpi-dept-col-pct kpi-dept-bg-elog' title='eLogic General'>" . (int) $metrics['elogicGen'] . "%</td>"
            . "<td class='kpi-dept-col-pct kpi-dept-bg-avail' title='Availability'>" . (int) $metrics['availability'] . "%</td>"
            . "<td class='kpi-dept-col-pct kpi-dept-bg-util' title='Utilization'>" . (int) $metrics['utilization'] . "%</td>"
            . '</tr>';
    }
}
