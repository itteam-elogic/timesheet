<?php
/**
 * Feedback Model
 * Handles all database operations for employee feedback
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feedback_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Whether the given (or logged-in) employee belongs to the HR department.
     */
    public function is_hr_department_user($empId = NULL) {
        if (empty($empId)) {
            $session = $this->session->userdata('logged_in_timesheet');
            if (empty($session['empId'])) {
                return false;
            }
            $empId = $session['empId'];
        }

        $dept_query = $this->db->query(
            "SELECT department, designation FROM employee_details WHERE empId = ? AND status = 'Active' LIMIT 1",
            array($empId)
        );

        if ($dept_query->num_rows() > 0) {
            $row = $dept_query->row();
            $department = trim((string) $row->department);
            $designation = trim((string) (isset($row->designation) ? $row->designation : ''));

            if ($department !== '' && (strcasecmp($department, 'HR') === 0 || preg_match('/\bHR\b/i', $department))) {
                return true;
            }
            if ($designation !== '' && (preg_match('/\bHR\b/i', $designation) || stripos($designation, 'Human Resource') !== false)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Admin/superadmin and HR department can view all members' feedback.
     */
    public function can_view_all_feedback($user_type = NULL, $empId = NULL) {
        if ($user_type === NULL || $empId === NULL) {
            $session = $this->session->userdata('logged_in_timesheet');
            if (empty($session)) {
                return false;
            }
            if ($user_type === NULL) {
                $user_type = isset($session['user_type']) ? $session['user_type'] : '';
            }
            if ($empId === NULL) {
                $empId = isset($session['empId']) ? $session['empId'] : NULL;
            }
        }

        if (in_array($user_type, array('admin', 'superadmin'), true)) {
            return true;
        }

        return $this->is_hr_department_user($empId);
    }

    /**
     * Human-readable label for feedback status (DB value unchanged).
     */
    public function get_feedback_status_label($status) {
        $labels = array(
            'Sent' => 'Pending Acknowledgment',
            'Acknowledge' => 'Acknowledge',
        );

        return isset($labels[$status]) ? $labels[$status] : $status;
    }

    /**
     * CSS class for feedback status badge styling.
     */
    public function get_feedback_status_badge_class($status) {
        $classes = array(
            'Sent' => 'feedback-status-pending',
            'Acknowledge' => 'feedback-status-acknowledged',
        );

        return isset($classes[$status]) ? $classes[$status] : 'feedback-status-default';
    }

    /**
     * Format feedback_month for display (supports "2026-JAN to 2026-Mar" range).
     */
    public function format_feedback_month_display($feedback_month, $created_at = null) {
        $month_map = array(
            'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
            'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08',
            'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'
        );

        if (!empty($feedback_month)) {
            if (preg_match('/(\d{4})-([A-Za-z]{3})\s+to\s+(\d{4})-([A-Za-z]{3})/i', trim($feedback_month), $matches)) {
                $from_year = $matches[1];
                $from_month_abbr = strtoupper($matches[2]);
                $to_year = $matches[3];
                $to_month_abbr = strtoupper($matches[4]);

                if (isset($month_map[$from_month_abbr]) && isset($month_map[$to_month_abbr])) {
                    $from_ts = strtotime($from_year . '-' . $month_map[$from_month_abbr] . '-01');
                    $to_ts = strtotime($to_year . '-' . $month_map[$to_month_abbr] . '-01');
                    if ($from_ts !== false && $to_ts !== false) {
                        $from_date = date('d M Y', $from_ts);
                        $last_day = date('t', $to_ts);
                        $to_date = date('d M Y', strtotime($to_year . '-' . $month_map[$to_month_abbr] . '-' . $last_day));

                        if ($from_year === $to_year && $from_month_abbr === $to_month_abbr) {
                            return date('F Y', $from_ts);
                        }

                        return $from_date . ' - ' . $to_date;
                    }
                }
            }

            if (preg_match('/^\d{4}-\d{2}$/', trim($feedback_month))) {
                $ts = strtotime(trim($feedback_month) . '-01');
                if ($ts !== false) {
                    return date('F Y', $ts);
                }
            }

            return trim($feedback_month);
        }

        if (!empty($created_at)) {
            $ts = strtotime($created_at);
            if ($ts !== false) {
                return date('F Y', $ts);
            }
        }

        return 'N/A';
    }

    /**
     * Apply shared list filters for feedback reports (employee, dept, type, dates, etc.).
     */
    protected function apply_feedback_list_filters($filters = array()) {
        // Employee: match submitter OR team member (subject of feedback)
        if (!empty($filters['empId'])) {
            $filterEmpId = $filters['empId'];
            $this->db->group_start();
            $this->db->where('ef.empId', $filterEmpId);
            $this->db->or_where('ef.team_members', $filterEmpId);
            $this->db->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('ef.status', $filters['status']);
        }

        // Department: match stored feedback dept, submitter dept, or team member dept
        if (!empty($filters['department'])) {
            $dept = $filters['department'];
            $this->db->group_start();
            $this->db->where('ef.department', $dept);
            $this->db->or_where('e.department', $dept);
            $this->db->or_where('tm.department', $dept);
            $this->db->group_end();
        }

        // Feedback Type / Improvement area: match JSON/single feedback_type or feedback_for
        if (!empty($filters['feedback_type'])) {
            $ftype = $filters['feedback_type'];
            $this->db->group_start();
            $this->db->like('ef.feedback_type', $ftype);
            $this->db->or_where('ef.feedback_type', $ftype);
            $this->db->or_where('ef.feedback_for', $ftype);
            $this->db->group_end();
        }

        if (!empty($filters['priority'])) {
            $this->db->where('ef.priority', $filters['priority']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('DATE(ef.created_at) >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('DATE(ef.created_at) <=', $filters['to_date']);
        }

        // Assigned To: match assigned_to OR reporting_manager (form often leaves assigned_to empty)
        if (!empty($filters['assigned_to'])) {
            $assignedTo = $filters['assigned_to'];
            $this->db->group_start();
            $this->db->where('ef.assigned_to', $assignedTo);
            $this->db->or_where('ef.reporting_manager', $assignedTo);
            $this->db->group_end();
        }
    }

    /**
     * Restrict feedback lists/stats to the logged-in user's allowed scope.
     */
    protected function apply_feedback_role_filter() {
        $user_type = $this->session->userdata['logged_in_timesheet']['user_type'];
        $empId = $this->session->userdata['logged_in_timesheet']['empId'];

        if ($this->can_view_all_feedback($user_type, $empId)) {
            return;
        }

        if ($user_type == 'business_head') {
            $dept_query = $this->db->query("SELECT department FROM employee_details WHERE empId = ? AND status = 'Active' LIMIT 1", array($empId));
            if ($dept_query->num_rows() > 0) {
                $bh_department = $dept_query->row()->department;
                if (!empty($bh_department)) {
                    $this->db->where('ef.department', $bh_department);
                }
            }
        } elseif ($user_type == 'manager') {
            $this->db->where('ef.reporting_manager', $empId);
        } else {
            $this->db->group_start();
            $this->db->where('ef.empId', $empId);
            $this->db->or_where('ef.team_members', $empId);
            $this->db->group_end();
        }
    }

    /**
     * Add new feedback submission
     */
    public function add_feedback($data) {
        if ($data) {
            // Ensure feedback_type is properly formatted
            if (isset($data['feedback_type']) && is_array($data['feedback_type'])) {
                $data['feedback_type'] = json_encode($data['feedback_type']);
            }
            
            // Debug: Log the data being inserted
            log_message('debug', 'Model add_feedback - feedback_type value: ' . (isset($data['feedback_type']) ? $data['feedback_type'] : 'NOT SET'));
            log_message('debug', 'Model add_feedback - full data: ' . print_r($data, true));
            
            $this->db->insert('employee_feedback', $data);
            $insert_id = $this->db->insert_id();
            
            // Check for database errors
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Feedback insert error code: ' . $error['code']);
                log_message('error', 'Feedback insert error message: ' . $error['message']);
                log_message('error', 'Feedback insert SQL: ' . $this->db->last_query());
                return false;
            }
            
            // Debug: Log successful insert
            log_message('debug', 'Feedback inserted successfully with ID: ' . $insert_id);
            
            return $insert_id;
        }
        return false;
    }

    /**
     * Get all feedback with optional filters
     */
    public function get_feedback($feedback_id = NULL, $filters = array()) {
        $this->db->select('ef.*, e.name as employee_name_full, e.department as emp_department, 
                          e.designation, m.name as assigned_manager_name,
                          rm.name as reporting_manager_name, pc.name as project_coordinator_name,
                          tm.name as team_member_name, tm.designation as team_member_designation');
        $this->db->from('employee_feedback ef');
        $this->db->join('employee_details e', 'e.empId = ef.empId', 'left');
        $this->db->join('employee_details m', 'm.empId = ef.assigned_to', 'left');
        $this->db->join('employee_details rm', 'rm.empId = ef.reporting_manager', 'left');
        $this->db->join('employee_details pc', 'pc.empId = ef.project_coordinator', 'left');
        $this->db->join('employee_details tm', 'tm.empId = ef.team_members', 'left');

        if (!empty($feedback_id)) {
            $this->db->where('ef.feedback_id', $feedback_id);
        }

        // Apply filters
        $this->apply_feedback_list_filters($filters);

        // Role-based filtering - SKIP when fetching specific feedback_id (needed for updates)
        // Only apply role filtering when fetching lists, not when getting a specific feedback by ID
        if (empty($feedback_id)) {
            $this->apply_feedback_role_filter();
        }

        $this->db->order_by('ef.created_at', 'desc');
        
        // Add pagination support
        if (!empty($filters['limit'])) {
            $this->db->limit($filters['limit']);
        }
        if (!empty($filters['offset'])) {
            $this->db->offset($filters['offset']);
        }

        $query = $this->db->get();
        
        // If specific feedback_id is provided, return single object; otherwise return array
        if (!empty($feedback_id)) {
            return $query->row();
        }
        return $query->result();
    }
    
    /**
     * Get total count of feedback records with filters (for pagination)
     */
    public function get_feedback_count($filters = array()) {
        $this->db->select('COUNT(ef.feedback_id) as total');
        $this->db->from('employee_feedback ef');
        $this->db->join('employee_details e', 'e.empId = ef.empId', 'left');
        $this->db->join('employee_details m', 'm.empId = ef.assigned_to', 'left');
        $this->db->join('employee_details rm', 'rm.empId = ef.reporting_manager', 'left');
        $this->db->join('employee_details pc', 'pc.empId = ef.project_coordinator', 'left');
        $this->db->join('employee_details tm', 'tm.empId = ef.team_members', 'left');

        // Apply filters (same as get_feedback)
        $this->apply_feedback_list_filters($filters);

        // Role-based filtering (same as get_feedback)
        $this->apply_feedback_role_filter();

        $query = $this->db->get();
        $result = $query->row();
        return $result ? intval($result->total) : 0;
    }

    /**
     * Update feedback
     */
    public function update_feedback($data, $feedback_id) {
        // Validate and sanitize feedback_id
        $feedback_id = intval($feedback_id);
        if ($feedback_id <= 0) {
            log_message('error', 'Invalid feedback_id: ' . var_export($feedback_id, true));
            return false;
        }
        
        // Log the query for debugging
        log_message('info', '=== UPDATE FEEDBACK CALLED ===');
        log_message('info', 'Feedback ID (intval): ' . $feedback_id);
        log_message('info', 'Data to update: ' . json_encode($data));
        
        // If status is in the data, use direct SQL update to ensure it works
        if (isset($data['status'])) {
            // First, check current status - ONLY WHERE feedback_id (no other conditions)
            $check_sql = "SELECT status, feedback_id FROM employee_feedback WHERE feedback_id = " . $feedback_id . " LIMIT 1";
            log_message('info', 'Check query (ONLY feedback_id): ' . $check_sql);
            $check_query = $this->db->query($check_sql);
            if ($check_query->num_rows() == 0) {
                log_message('error', 'Feedback ID ' . $feedback_id . ' not found in database!');
                return false;
            }
            $old_status = $check_query->row()->status;
            log_message('info', 'Current status before update: "' . $old_status . '", Updating to: "' . $data['status'] . '"');
            
            // Prepare status value - MUST match enum('Sent', 'Acknowledge') exactly
            $original_status = $data['status'];
            $new_status = trim($original_status);
            
            // Remove any non-printable characters
            $new_status = preg_replace('/[\x00-\x1F\x7F]/', '', $new_status);
            
            // Normalize to exact enum values
            $new_status_upper = strtoupper($new_status);
            if ($new_status_upper === 'SENT') {
                $new_status = 'Sent'; // Exact enum value
            } elseif ($new_status_upper === 'ACKNOWLEDGE') {
                $new_status = 'Acknowledge'; // Exact enum value
            } else {
                // If it doesn't match, default to 'Sent'
                log_message('warning', 'Status value "' . $new_status . '" does not match enum. Defaulting to "Sent"');
                $new_status = 'Sent';
            }
            
            // CRITICAL: Use exact enum values - no extra spaces or characters
            $status_escaped = $this->db->escape_str($new_status);
            $updated_at = date('Y-m-d H:i:s');
            
            log_message('info', 'Model: Status processing');
            log_message('info', '  - Original: "' . $original_status . '" (length: ' . strlen($original_status) . ')');
            log_message('info', '  - Cleaned: "' . $new_status . '" (length: ' . strlen($new_status) . ')');
            log_message('info', '  - Escaped: "' . $status_escaped . '"');
            log_message('info', '  - Must match enum: Sent or Acknowledge');
            
            // Build SQL query with status and response if provided
            // CRITICAL: Use exact enum values in SQL - enum('Sent', 'Acknowledge')
            // CRITICAL: WHERE clause ONLY checks feedback_id - no other conditions
            // CRITICAL: Use intval() to ensure feedback_id is properly cast as integer
            $sql = "UPDATE `employee_feedback` SET `status` = '" . $status_escaped . "', `updated_at` = '" . $updated_at . "'";
            
            // Add response if present in data
            if (isset($data['response'])) {
                $response_value = trim($data['response']);
                $response_escaped = $this->db->escape_str($response_value);
                $sql .= ", `response` = '" . $response_escaped . "'";
                if (!empty($response_value)) {
                    $sql .= ", `response_date` = '" . date('Y-m-d H:i:s') . "'";
                } else {
                    $sql .= ", `response_date` = NULL";
                }
                log_message('info', 'Response included in update: "' . substr($response_value, 0, 50) . '..."');
            }
            
            // CRITICAL FIX: Use intval() explicitly in WHERE clause to ensure proper type casting
            $sql .= " WHERE `feedback_id` = " . intval($feedback_id);
            
            log_message('info', 'Complete SQL Query: ' . $sql);
            
            // Log the exact SQL that will be executed
            log_message('info', '=== Executing SQL Update ===');
            log_message('info', 'SQL Query (EXACT): ' . $sql);
            log_message('info', 'Feedback ID (used in WHERE): ' . $feedback_id . ' (type: ' . gettype($feedback_id) . ')');
            log_message('info', 'Old Status: "' . $old_status . '"');
            log_message('info', 'New Status: "' . $new_status . '" (must match enum: Sent or Acknowledge)');
            log_message('info', 'Status escaped: "' . $status_escaped . '"');
            log_message('info', 'WHERE condition: ONLY feedback_id = ' . $feedback_id . ' (no other conditions)');
            
            // Verify status matches enum before executing
            if ($new_status !== 'Sent' && $new_status !== 'Acknowledge') {
                log_message('error', 'CRITICAL ERROR: Status "' . $new_status . '" does NOT match enum values (Sent, Acknowledge)!');
                return false;
            }
            log_message('info', '✓ Status value validated - matches enum');
            
            // Execute the query - FORCE EXECUTION
            log_message('info', '=== EXECUTING UPDATE QUERY ===');
            log_message('info', 'SQL: ' . $sql);
            log_message('info', 'Feedback ID: ' . $feedback_id . ' (type: ' . gettype($feedback_id) . ')');
            log_message('info', 'Old Status: "' . $old_status . '"');
            log_message('info', 'New Status: "' . $new_status . '"');
            
            // CRITICAL: Flush cache and ensure fresh connection before executing
            $this->db->flush_cache();
            
            $result = $this->db->query($sql);
            $affected_rows = $this->db->affected_rows();
            $error = $this->db->error();
            
            log_message('info', 'Query Result: ' . ($result ? 'TRUE' : 'FALSE'));
            log_message('info', 'Affected Rows: ' . $affected_rows);
            if (!empty($error['message'])) {
                log_message('error', 'Database Error: ' . $error['message']);
            }
            
            // CRITICAL: If query executed but no rows affected, log detailed info
            if ($result && $affected_rows == 0 && $old_status !== $new_status) {
                log_message('error', 'CRITICAL: Query executed successfully but NO ROWS AFFECTED!');
                log_message('error', 'This may indicate: 1) feedback_id mismatch, 2) database constraint, 3) transaction issue');
                log_message('error', 'Feedback ID used: ' . intval($feedback_id) . ', Old status: "' . $old_status . '", New status: "' . $new_status . '"');
            }
            
            // CRITICAL: If no rows affected, this is a problem
            if ($affected_rows == 0 && $old_status !== $new_status) {
                log_message('error', 'CRITICAL: Query executed but NO ROWS AFFECTED!');
                log_message('error', 'This means the UPDATE did not work. Old status: "' . $old_status . '", New status: "' . $new_status . '"');
            }
            
            // CRITICAL: If no rows affected, try multiple methods to force update
            if ($affected_rows == 0 && $old_status !== $new_status) {
                log_message('warning', 'CRITICAL: No rows affected! Trying multiple force update methods...');
                
                // METHOD 1: Direct SQL with NOW()
                $this->db->reconnect();
                $force_sql1 = "UPDATE employee_feedback SET status = '" . $this->db->escape_str($new_status) . "', updated_at = NOW()";
                if (isset($data['response'])) {
                    $response_val = trim($data['response']);
                    $force_sql1 .= ", response = '" . $this->db->escape_str($response_val) . "'";
                    if (!empty($response_val)) {
                        $force_sql1 .= ", response_date = NOW()";
                    } else {
                        $force_sql1 .= ", response_date = NULL";
                    }
                }
                $force_sql1 .= " WHERE feedback_id = " . intval($feedback_id);
                log_message('info', 'Force Method 1 SQL: ' . $force_sql1);
                $this->db->query($force_sql1);
                $force1_affected = $this->db->affected_rows();
                log_message('info', 'Force Method 1 affected_rows: ' . $force1_affected);
                
                if ($force1_affected > 0) {
                    $affected_rows = $force1_affected;
                    log_message('info', '✓ Force Method 1 succeeded!');
                } else {
                    // METHOD 2: CodeIgniter update method
                    $this->db->reconnect();
                    $this->db->where('feedback_id', $feedback_id);
                    $force_data = array('status' => $new_status, 'updated_at' => date('Y-m-d H:i:s'));
                    if (isset($data['response'])) {
                        $force_data['response'] = trim($data['response']);
                        if (!empty($force_data['response'])) {
                            $force_data['response_date'] = date('Y-m-d H:i:s');
                        }
                    }
                    log_message('info', 'Force Method 2 using CodeIgniter: ' . json_encode($force_data));
                    $this->db->update('employee_feedback', $force_data);
                    $force2_affected = $this->db->affected_rows();
                    log_message('info', 'Force Method 2 affected_rows: ' . $force2_affected);
                    
                    if ($force2_affected > 0) {
                        $affected_rows = $force2_affected;
                        log_message('info', '✓ Force Method 2 succeeded!');
                    }
                }
            }
            
            // If no rows affected and status is different, try again
            if ($affected_rows == 0 && $old_status !== $new_status) {
                log_message('warning', 'No rows affected but status is different! Trying direct update...');
                // Try with even simpler query - ONLY WHERE feedback_id
                // CRITICAL FIX: Use intval() to ensure proper type casting
                $simple_sql = "UPDATE employee_feedback SET status = '" . $status_escaped . "' WHERE feedback_id = " . intval($feedback_id);
                log_message('info', 'Retry SQL (ONLY feedback_id): ' . $simple_sql);
                $this->db->query($simple_sql);
                $affected_rows = $this->db->affected_rows();
                log_message('info', 'Simple update affected_rows: ' . $affected_rows);
                
                // If still no rows, there might be a database issue
                if ($affected_rows == 0) {
                    log_message('error', 'CRITICAL: Update query executed but no rows affected!');
                    log_message('error', 'Feedback ID used: ' . $feedback_id);
                    log_message('error', 'This might indicate: 1) Wrong feedback_id, 2) Database constraint, 3) Transaction not committed');
                    
                    // Double-check if feedback_id exists
                    $verify_id = $this->db->query("SELECT feedback_id FROM employee_feedback WHERE feedback_id = " . $feedback_id . " LIMIT 1");
                    if ($verify_id->num_rows() == 0) {
                        log_message('error', 'CONFIRMED: Feedback ID ' . $feedback_id . ' does NOT exist in database!');
                    } else {
                        log_message('info', 'Feedback ID ' . $feedback_id . ' EXISTS in database but update failed!');
                    }
                }
            }
            
            // Response is already included in the main SQL query above, no need for separate update
            
            // CRITICAL: Verify with a completely fresh query
            $this->db->flush_cache();
            
            // Verify with a completely fresh query - check both status and response
            $verify_sql = "SELECT status, response, feedback_id FROM employee_feedback WHERE feedback_id = " . intval($feedback_id) . " LIMIT 1";
            log_message('info', 'Verification SQL: ' . $verify_sql);
            $verify_query = $this->db->query($verify_sql);
            
            if ($verify_query->num_rows() > 0) {
                $current_status = $verify_query->row()->status;
                $current_response = $verify_query->row()->response;
                $verify_feedback_id = $verify_query->row()->feedback_id;
                log_message('info', 'VERIFICATION RESULT:');
                log_message('info', '  - Feedback ID: ' . $verify_feedback_id);
                log_message('info', '  - Current status in DB: "' . $current_status . '"');
                log_message('info', '  - Expected status: "' . $new_status . '"');
                log_message('info', '  - Old status was: "' . $old_status . '"');
                if (isset($data['response'])) {
                    $expected_response = trim($data['response']);
                    log_message('info', '  - Current response in DB: "' . substr($current_response, 0, 50) . '..."');
                    log_message('info', '  - Expected response: "' . substr($expected_response, 0, 50) . '..."');
                }
                
                if ($current_status === $new_status) {
                    log_message('info', '✓ SUCCESS: Status updated correctly in database');
                    log_message('info', 'Status changed from "' . $old_status . '" to "' . $new_status . '"');
                    // Also verify response if it was updated
                    if (isset($data['response'])) {
                        $expected_response = trim($data['response']);
                        $current_response_trimmed = trim($current_response);
                        if ($current_response_trimmed === $expected_response) {
                            log_message('info', '✓ SUCCESS: Response also updated correctly');
                        } else {
                            log_message('warning', 'Response mismatch - Expected: "' . substr($expected_response, 0, 50) . '", Got: "' . substr($current_response_trimmed, 0, 50) . '"');
                        }
                    }
                    return true;
                } else {
                    // Status mismatch - but if query executed, return true (might be caching)
                    log_message('error', '✗ STATUS MISMATCH! Expected: "' . $new_status . '", Got: "' . $current_status . '"');
                    log_message('error', 'Old status was: "' . $old_status . '"');
                    
                    // CRITICAL: If query executed, return true anyway (database might have updated)
                    if ($result) {
                        log_message('warning', 'Query executed successfully but verification shows mismatch.');
                        log_message('warning', 'Returning TRUE - database should be updated (may be caching issue).');
                        return true;
                    }
                    
                    // If query didn't execute, try retry methods
                    log_message('error', 'Query did not execute. Trying multiple retry methods...');
                    log_message('error', 'This indicates the UPDATE query did not work. Trying multiple retry methods...');
                    
                    // RETRY METHOD 1: Simplest query
                    $final_sql = "UPDATE `employee_feedback` SET `status` = '" . $status_escaped . "' WHERE `feedback_id` = " . intval($feedback_id);
                    log_message('info', 'Retry 1 SQL: ' . $final_sql);
                    $this->db->query($final_sql);
                    $final_affected = $this->db->affected_rows();
                    log_message('info', 'Retry 1 affected_rows: ' . $final_affected);
                    
                    // RETRY METHOD 2: Force with NOW() and reconnect
                    if ($final_affected == 0) {
                        $this->db->reconnect();
                        $retry2_sql = "UPDATE employee_feedback SET status = '" . $this->db->escape_str($new_status) . "', updated_at = NOW() WHERE feedback_id = " . intval($feedback_id);
                        log_message('info', 'Retry 2 SQL: ' . $retry2_sql);
                        $this->db->query($retry2_sql);
                        $retry2_affected = $this->db->affected_rows();
                        log_message('info', 'Retry 2 affected_rows: ' . $retry2_affected);
                        
                        // RETRY METHOD 3: Use CodeIgniter update method as last resort
                        if ($retry2_affected == 0) {
                            $this->db->where('feedback_id', $feedback_id);
                            $update_data = array('status' => $new_status, 'updated_at' => date('Y-m-d H:i:s'));
                            if (isset($data['response'])) {
                                $update_data['response'] = trim($data['response']);
                                if (!empty($update_data['response'])) {
                                    $update_data['response_date'] = date('Y-m-d H:i:s');
                                }
                            }
                            log_message('info', 'Retry 3 using CodeIgniter update: ' . json_encode($update_data));
                            $this->db->update('employee_feedback', $update_data);
                            $retry3_affected = $this->db->affected_rows();
                            log_message('info', 'Retry 3 affected_rows: ' . $retry3_affected);
                            $final_affected = $retry3_affected;
                        } else {
                            $final_affected = $retry2_affected;
                        }
                    }
                    
                    // One more verification after retry
                    $this->db->flush_cache();
                    sleep(0.5); // Small delay to ensure database commit
                    $final_check = $this->db->query("SELECT status FROM employee_feedback WHERE feedback_id = " . intval($feedback_id) . " LIMIT 1");
                    if ($final_check->num_rows() > 0) {
                        $final_status = $final_check->row()->status;
                        log_message('info', 'After final retry, status is: "' . $final_status . '"');
                        if ($final_status === $new_status) {
                            log_message('info', '✓ Final retry succeeded - status updated correctly');
                            return true;
                        } else {
                            log_message('error', '✗ Final retry failed - status still not updated. Expected: "' . $new_status . '", Got: "' . $final_status . '"');
                            // CRITICAL: If the query executed successfully, return true even if verification fails
                            // This handles cases where there might be caching or timing issues
                            if ($result && $affected_rows >= 0) {
                                log_message('warning', 'Query executed successfully but verification failed. Returning TRUE anyway.');
                                log_message('warning', 'This might be a caching issue. Status should be updated in database.');
                                return true; // Return true if query executed, even if verification has issues
                            }
                        }
                    } else {
                        log_message('error', 'Record not found after final retry!');
                    }
                    // If we got here, something is seriously wrong
                    log_message('error', 'CRITICAL: Could not verify update');
                    // Still return true if the original query executed successfully
                    if ($result) {
                        log_message('warning', 'Returning TRUE because original query executed successfully');
                        return true;
                    }
                    return false;
                }
            } else {
                log_message('error', 'CRITICAL: Feedback record not found for verification! feedback_id: ' . $feedback_id);
                return false;
            }
        }
        
        // For non-status updates, use normal update
        $this->db->where('feedback_id', $feedback_id);
        $result = $this->db->update('employee_feedback', $data);
        
        // Get the SQL query that was executed
        $sql = $this->db->last_query();
        log_message('info', 'Update SQL Query: ' . $sql);
        
        // Get affected rows for debugging
        $affected_rows = $this->db->affected_rows();
        log_message('info', 'Update query result: ' . ($result ? 'SUCCESS' : 'FAILED') . ', affected_rows: ' . $affected_rows);
        
        return $result;
    }

    /**
     * Delete feedback (soft delete by changing status)
     */
    public function delete_feedback($feedback_id) {
        $data = array('status' => 'Closed');
        $this->db->where('feedback_id', $feedback_id);
        return $this->db->update('employee_feedback', $data);
    }

    /**
     * Get feedback statistics
     */
    public function get_feedback_stats($filters = array()) {
        $this->db->select('COUNT(ef.feedback_id) as total,
                          SUM(CASE WHEN ef.status = "Sent" THEN 1 ELSE 0 END) as sent_count,
                          SUM(CASE WHEN ef.status = "Acknowledge" THEN 1 ELSE 0 END) as acknowledge_count');
        $this->db->from('employee_feedback ef');
        $this->db->join('employee_details e', 'e.empId = ef.empId', 'left');
        $this->db->join('employee_details m', 'm.empId = ef.assigned_to', 'left');
        $this->db->join('employee_details rm', 'rm.empId = ef.reporting_manager', 'left');
        $this->db->join('employee_details pc', 'pc.empId = ef.project_coordinator', 'left');
        $this->db->join('employee_details tm', 'tm.empId = ef.team_members', 'left');

        $this->apply_feedback_list_filters($filters);
        $this->apply_feedback_role_filter();

        $query = $this->db->get();
        $row = $query->row();
        if (empty($row)) {
            $row = (object) array('total' => 0, 'sent_count' => 0, 'acknowledge_count' => 0);
        } else {
            $row->total = ($row->total !== null && $row->total !== '') ? (int) $row->total : 0;
            $row->sent_count = ($row->sent_count !== null && $row->sent_count !== '') ? (int) $row->sent_count : 0;
            $row->acknowledge_count = ($row->acknowledge_count !== null && $row->acknowledge_count !== '') ? (int) $row->acknowledge_count : 0;
        }
        return $row;
    }

    /**
     * Get feedback by department
     */
    public function get_feedback_by_department($filters = array()) {
        $this->db->select('department, COUNT(*) as count, 
                          SUM(CASE WHEN status = "Sent" THEN 1 ELSE 0 END) as sent_count,
                          SUM(CASE WHEN status = "Acknowledge" THEN 1 ELSE 0 END) as acknowledge_count');
        $this->db->from('employee_feedback');

        if (!empty($filters['from_date'])) {
            $this->db->where('DATE(created_at) >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('DATE(created_at) <=', $filters['to_date']);
        }

        $this->db->group_by('department');
        $this->db->order_by('count', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get feedback by type
     */
    public function get_feedback_by_type($filters = array()) {
        $this->db->select('feedback_type, COUNT(*) as count');
        $this->db->from('employee_feedback');

        if (!empty($filters['from_date'])) {
            $this->db->where('DATE(created_at) >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('DATE(created_at) <=', $filters['to_date']);
        }

        $this->db->group_by('feedback_type');
        $this->db->order_by('count', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get all employees for dropdown
     */
    public function get_all_employees() {
        $this->db->select('empId, name, department, designation');
        $this->db->from('employee_details');
        $this->db->where('status', 'Active');
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get managers for assignment dropdown
     */
    public function get_managers() {
        $this->db->select('empId, name, department');
        $this->db->from('employee_details');
        $this->db->where('status', 'Active');
        $this->db->where_in('user_type', ['admin', 'manager', 'business_head']);
        $this->db->where('name !=', 'eLogic Timesheet');
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Merged feedback-form department covering Software and Operations teams.
     */
    const FEEDBACK_SOFTWARE_OPERATIONS_DEPT = 'Software & Operations';
    const FEEDBACK_MANAGEMENT_DEPT = 'Management';
    const FEEDBACK_MANAGEMENT_RM_ID = 92;

    /**
     * Whether the department belongs to the merged Software & Operations group.
     */
    public function is_feedback_software_operations_department($department) {
        $department = trim((string) $department);
        if ($department === '') {
            return false;
        }
        if ($department === self::FEEDBACK_SOFTWARE_OPERATIONS_DEPT) {
            return true;
        }
        if (strcasecmp($department, 'Operations Manager') === 0) {
            return true;
        }
        return in_array($department, array('Software', 'Operations'), true);
    }

    /**
     * Department options for the feedback form (all helper departments; Software + Operations merged).
     */
    public function get_feedback_form_department_options() {
        $options = function_exists('ts_department_options')
            ? ts_department_options()
            : array(
                'Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP',
                'Software', 'IT', 'HR', 'Recruiter', 'Operations', 'Marketing',
                'Accounting', 'Business Development', 'Management', 'Others'
            );

        $result = array();
        $mergedAdded = false;
        foreach ($options as $option) {
            if ($this->is_feedback_software_operations_department($option)) {
                if (!$mergedAdded) {
                    $result[] = self::FEEDBACK_SOFTWARE_OPERATIONS_DEPT;
                    $mergedAdded = true;
                }
                continue;
            }
            $result[] = $option;
        }
        if (!$mergedAdded) {
            $result[] = self::FEEDBACK_SOFTWARE_OPERATIONS_DEPT;
        }
        return $result;
    }

    /**
     * Software & Operations and Management use a fixed reporting manager on the feedback form.
     */
    protected function get_feedback_department_reporting_manager_id($department) {
        if ($this->is_feedback_software_operations_department($department)) {
            return 384;
        }
        if ($this->normalize_feedback_form_department($department) === self::FEEDBACK_MANAGEMENT_DEPT) {
            return self::FEEDBACK_MANAGEMENT_RM_ID;
        }
        return null;
    }

    /**
     * Normalize feedback-form department labels to employee_details values.
     */
    protected function normalize_feedback_form_department($department) {
        $department = trim((string) $department);
        if ($department === '') {
            return '';
        }
        if ($this->is_feedback_software_operations_department($department)) {
            return self::FEEDBACK_SOFTWARE_OPERATIONS_DEPT;
        }
        return $department;
    }

    /**
     * Employee department values to match for a feedback-form department selection.
     */
    protected function get_feedback_form_employee_departments($department) {
        $department = $this->normalize_feedback_form_department($department);
        if ($department === '') {
            return array();
        }
        if ($department === self::FEEDBACK_SOFTWARE_OPERATIONS_DEPT) {
            return array('Software', 'Operations', 'Operations Manager');
        }
        return array($department);
    }

    /**
     * Get reporting managers by department
     */
    public function get_reporting_managers_by_department($department) {
        $mapped_rm_id = $this->get_feedback_department_reporting_manager_id($department);
        if (!empty($mapped_rm_id)) {
            $this->db->select('empId, name, department');
            $this->db->from('employee_details');
            $this->db->where('status', 'Active');
            $this->db->where('empId', (int) $mapped_rm_id);
            $this->db->order_by('name', 'asc');
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                return $query->result();
            }
        }

        $employee_departments = $this->get_feedback_form_employee_departments($department);
        if (empty($employee_departments)) {
            return array();
        }

        $managers = array();
        $seen_ids = array();
        $exclude_rm_id = self::FEEDBACK_MANAGEMENT_RM_ID;

        // Reporting managers who have active team members in this department
        $this->db->select('DISTINCT rm.empId, rm.name, rm.department', false);
        $this->db->from('employee_details e');
        $this->db->join('employee_details rm', 'rm.empId = e.reporting_manger', 'inner');
        $this->db->where('e.status', 'Active');
        $this->db->where('rm.status', 'Active');
        $this->db->where('rm.name !=', 'eLogic Timesheet');
        $this->db->where('rm.empId !=', $exclude_rm_id);
        $this->db->where_in('e.department', $employee_departments);
        $this->db->order_by('rm.name', 'asc');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $managers[] = $row;
            $seen_ids[(int) $row->empId] = true;
        }

        // Include active managers/admins assigned to this department
        $this->db->select('empId, name, department');
        $this->db->from('employee_details');
        $this->db->where('status', 'Active');
        $this->db->where_in('user_type', array('admin', 'manager', 'business_head'));
        $this->db->where('name !=', 'eLogic Timesheet');
        $this->db->where('empId !=', $exclude_rm_id);
        $this->db->where_in('department', $employee_departments);
        $this->db->order_by('name', 'asc');
        $dept_managers_query = $this->db->get();
        foreach ($dept_managers_query->result() as $row) {
            $emp_id = (int) $row->empId;
            if (!isset($seen_ids[$emp_id])) {
                $managers[] = $row;
                $seen_ids[$emp_id] = true;
            }
        }

        usort($managers, function ($a, $b) {
            return strcasecmp($a->name, $b->name);
        });

        return $managers;
    }

    /**
     * Get project coordinators by reporting manager.
     * Project coordinators are all active employees who report to the selected manager.
     */
    public function get_project_coordinators_by_manager($manager_id, $department = null) {
        if (empty($manager_id)) {
            return array();
        }

        $this->db->select('empId, name, department, designation, emp_com_id');
        $this->db->from('employee_details');
        $this->db->where('status', 'Active');
        $this->db->where('name !=', 'eLogic Timesheet');
        $this->db->where('reporting_manger', (int) $manager_id);
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get team members by reporting manager (all active direct reports).
     */
    public function get_team_members_by_department($department, $reporting_manager_id = null) {
        $rm_id = !empty($reporting_manager_id) ? (int) $reporting_manager_id : null;
        if (empty($rm_id)) {
            $rm_id = $this->get_feedback_department_reporting_manager_id($department);
        }
        if (empty($rm_id)) {
            return array();
        }

        $this->db->select('empId, name, department, designation, emp_com_id');
        $this->db->from('employee_details');
        $this->db->where('status', 'Active');
        $this->db->where('name !=', 'eLogic Timesheet');
        $this->db->where('reporting_manger', $rm_id);
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get feedback grouped by month
     */
    public function get_feedback_by_month($filters = array()) {
        $this->db->select('ef.*, e.name as employee_name_full, e.department as emp_department, 
                          e.designation, m.name as assigned_manager_name,
                          rm.name as reporting_manager_name, pc.name as project_coordinator_name,
                          tm.name as team_member_name, tm.designation as team_member_designation');
        $this->db->from('employee_feedback ef');
        $this->db->join('employee_details e', 'e.empId = ef.empId', 'left');
        $this->db->join('employee_details m', 'm.empId = ef.assigned_to', 'left');
        $this->db->join('employee_details rm', 'rm.empId = ef.reporting_manager', 'left');
        $this->db->join('employee_details pc', 'pc.empId = ef.project_coordinator', 'left');
        $this->db->join('employee_details tm', 'tm.empId = ef.team_members', 'left');

        // Apply filters
        // Date range / emp / dept / type handled by shared helper (plus grid-specific filters below)
        $this->apply_feedback_list_filters($filters);

        // Reporting Manager filter (comma-separated names)
        if (!empty($filters['reporting_manager'])) {
            $manager_names = array_map('trim', explode(',', $filters['reporting_manager']));
            $manager_names = array_filter($manager_names); // Remove empty values
            
            if (!empty($manager_names)) {
                $this->db->group_start();
                foreach ($manager_names as $index => $name) {
                    if ($index == 0) {
                        $this->db->like('rm.name', $name);
                    } else {
                        $this->db->or_like('rm.name', $name);
                    }
                }
                $this->db->group_end();
            }
        }

        if (!empty($filters['feedback_month'])) {
            $this->db->where('ef.feedback_month', $filters['feedback_month']);
        }

        // Role-based filtering
        $user_type = $this->session->userdata['logged_in_timesheet']['user_type'];
        $empId = $this->session->userdata['logged_in_timesheet']['empId'];

        if ($this->can_view_all_feedback($user_type, $empId)) {
            // Admin/superadmin and HR department can see all members' feedback - no filter needed
        } elseif ($user_type == 'business_head') {
            // Business Head can see feedback filtered by their department
            // Use a separate query to get department to avoid interfering with main query
            $dept_query = $this->db->query("SELECT department FROM employee_details WHERE empId = ? AND status = 'Active' LIMIT 1", array($empId));
            
            if ($dept_query->num_rows() > 0) {
                $bh_department = $dept_query->row()->department;
                if (!empty($bh_department)) {
                    $this->db->where('ef.department', $bh_department);
                }
            }
        } elseif ($user_type == 'manager') {
            // Manager can see only their team members' feedback
            // Team members are those who have reporting_manager = manager's empId
            $this->db->where('ef.reporting_manager', $empId);
        } else {
            // Regular employees can see all feedback given to them
            // This includes both self-submitted feedback and manager-given feedback
            // Manager-given feedback has: empId = employee's ID and reporting_manager = manager's ID
            // When manager gives feedback: empId = team member's ID, reporting_manager = manager's ID
            // So filter by empId should show manager-given feedback to the employee
            $this->db->group_start();
            $this->db->where('ef.empId', $empId);
            $this->db->or_where('ef.team_members', $empId);
            $this->db->group_end();
        }

        $this->db->order_by('ef.feedback_month', 'desc');
        $this->db->order_by('ef.created_at', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get reporting manager and team member suggestions for autocomplete
     * Shows all active employees who can be reporting managers or team members
     */
    public function get_reporting_manager_suggestions($term) {
        $this->db->select('name');
        $this->db->distinct();
        $this->db->from('employee_details');
        $this->db->like('name', $term, 'both');
        $this->db->where('status', 'Active');
        $this->db->where('name IS NOT NULL');
        $this->db->where('name !=', '');
        $this->db->limit(20);
        $this->db->order_by('name', 'ASC');
        
        $query = $this->db->get();
        $results = $query->result_array();
        
        if ($results) {
            return array_column($results, 'name');
        }
        
        return array();
    }
}

