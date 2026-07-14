<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notification_model extends CI_Model {
    public function get_unviewed_notifications($empId) {
        $this->db->select('n.id, n.message, en.videoId, n.notification_type');
        $this->db->from('notifications as n');
        $this->db->join('employee_notifications  as en', 'en.notification_id = n.id');
        $this->db->where('en.empId', $empId);
        $this->db->where('en.viewed', 0);
        return $this->db->get()->result_array();
    }

    public function mark_notification_as_viewed($empId, $notification_id) {
        $this->db->where('empId', $empId);
        $this->db->where('notification_id', $notification_id);
        $this->db->update('employee_notifications', array('viewed' => 1));
    }

    public function add_notification($message) {
        $logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
        $this->db->insert('notifications', array(
            'message' => $message, 'created_at'=> date('Y-m-d H:i:s'),
            'notification_type'=>'GENERAL'));
        $notification_id = $this->db->insert_id();
        $employessIds = $this->getAllEmployeeIds();
        // 
        foreach ($employessIds as $emp) {
            $data = array(
                'empId' => $emp['empId'],
                'assigned_by' => $logedInUser,
                'notification_id' => $notification_id
            );
            $this->db->insert('employee_notifications', $data);
        }
    }

    public function add_notification_employee($empId, $videoId, $message) {
        $logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];	
        $this->db->insert('notifications', array(
            'message' => $message, 'created_at'=> date('Y-m-d H:i:s'),
            'notification_type'=>'LMS_ASSIGNMENT'));
        $notification_id = $this->db->insert_id();
        $data = array(
            'empId' => $empId,
            'videoId' => $videoId,
            'assigned_by' => $logedInUser,
            'notification_id' => $notification_id
        );
        $this->db->insert('employee_notifications', $data);
    }

    public function notification_on_complete_assignment($empId, $videoId, $message) {
        $logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];	
        $checkVideoAlreadyCompleted = $this->checkVideoAlreadyCompleted($empId, $videoId);
        if(count($checkVideoAlreadyCompleted) == 0){ 
            $this->db->insert('notifications', array(
                'message' => $message, 'created_at'=> date('Y-m-d H:i:s'),
                'notification_type'=>'LMS_ASSIGNMENT_COMPLETE'));
            $notification_id = $this->db->insert_id();
            $assigneeIds = $this->getAssinessByVideo($empId, $videoId);
            foreach ($assigneeIds as $userId) {
                $data = array(
                    'empId' => $userId['created_emp_id'],
                    'videoId' => $videoId,
                    'assigned_by' => $logedInUser,
                    'notification_id' => $notification_id
                );
                $this->db->insert('employee_notifications', $data);
            }
        }
    }

    private function getAllEmployeeIds(){
        return $this->db->select('empId')->from('employee_details')->where('status', 'Active')->get()->result_array();
    }

    private function checkVideoAlreadyCompleted($empId, $videoId){
        return $this->db->select('empId')->from('lms_video_assignments')
        ->where('empId', $empId)
        ->where('videoId', $videoId)
        ->where('completion_date', NULL)->get()->result_array();
    }

    private function getAssinessByVideo($empId, $videoId){
        return $this->db->select('created_emp_id')->from('lms_video_assignments')
        ->where('empId', $empId)->where('videoId', $videoId)->get()->result_array();
    }
}

?>