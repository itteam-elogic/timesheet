<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emp_record_inactivity extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('timesheet_login');
        $this->load->model('emp_record_inactivity_model');

        if (empty($this->session->userdata['logged_in_timesheet'])) {
            redirect('home/login');
        }
    }

    public function index() {
        $data = $this->buildViewData($this->buildFiltersFromRequest());
        $this->load->view('emp_record_inactivity/inactivity_report', $data);
    }

    public function search() {
        $data = $this->buildViewData($this->buildFiltersFromRequest());
        $this->load->view('emp_record_inactivity/inactivity_report', $data);
    }

    public function getProjectsByClient() {
        $clientId = trim((string)$this->input->post('client_Id'));
        $selectedProjectId = trim((string)$this->input->post('project_Id'));
        $projects = $this->emp_record_inactivity_model->getProjectsByClient($clientId);

        echo '<option value="">All Projects</option>';
        foreach ($projects as $project) {
            $pid = (int)$project->project_Id;
            $selected = ($selectedProjectId !== '' && (int)$selectedProjectId === $pid) ? ' selected' : '';
            echo '<option value="' . $pid . '"' . $selected . '>'
                . htmlspecialchars($project->project_name, ENT_QUOTES, 'UTF-8')
                . '</option>';
        }
    }

    private function buildFiltersFromRequest() {
        $fromYear = $this->input->get_post('from_year');
        $fromMonth = $this->input->get_post('from_month');
        $toYear = $this->input->get_post('to_year');
        $toMonth = $this->input->get_post('to_month');

        $resolved = $this->emp_record_inactivity_model->resolveYearMonthDateRange(
            $fromYear,
            $fromMonth,
            $toYear,
            $toMonth
        );

        return array(
            'from_date' => $resolved['from_date'],
            'to_date' => $resolved['to_date'],
            'from_year' => $resolved['from_year'],
            'from_month' => $resolved['from_month'],
            'to_year' => $resolved['to_year'],
            'to_month' => $resolved['to_month'],
            'department' => trim((string)$this->input->get_post('department')),
            'client_Id' => trim((string)$this->input->get_post('client_Id')),
            'project_Id' => trim((string)$this->input->get_post('project_Id')),
            'project_status' => trim((string)$this->input->get_post('project_status')),
        );
    }

    private function buildViewData($filters) {
        $clientId = isset($filters['client_Id']) ? $filters['client_Id'] : '';
        $dropdowns = $this->emp_record_inactivity_model->getFilterDropdownData($clientId);

        return array(
            'records' => $this->emp_record_inactivity_model->getInactiveTimesheetRecords($filters),
            'filters' => $filters,
            'clients' => $dropdowns['clients'],
            'projects' => $dropdowns['projects'],
            'departments' => $dropdowns['departments'],
            'projectStatuses' => $dropdowns['projectStatuses'],
            'canCloseProjects' => $this->canCloseProcessProjects(),
        );
    }

    public function closeSelectedProcessProjects() {
        $this->updateSelectedProjectStatus('close');
    }

    public function reopenSelectedProcessProjects() {
        $this->updateSelectedProjectStatus('reopen');
    }

    public function toggleProjectStatus() {
        if (!$this->canCloseProcessProjects()) {
            $this->jsonResponse(false, 'You are not authorized to update project status.');
            return;
        }

        $projectId = (int)$this->input->post('project_id');
        $currentStatus = strtolower(trim((string)$this->input->post('current_status')));

        if ($projectId <= 0) {
            $this->jsonResponse(false, 'Invalid project selected.');
            return;
        }

        if ($currentStatus === 'process') {
            $newStatus = 'Closed';
        } elseif ($currentStatus === 'closed') {
            $newStatus = 'Process';
        } else {
            $this->jsonResponse(false, 'Only Process or Closed status can be toggled.');
            return;
        }

        $updated = $this->emp_record_inactivity_model->updateProjectStatus($projectId, $newStatus, $currentStatus === 'process' ? 'Process' : 'Closed');
        if ($updated) {
            $label = ($newStatus === 'Process') ? 'In Process' : 'Closed';
            $this->jsonResponse(true, 'Project status updated to ' . $label . '.', array(
                'project_id' => $projectId,
                'new_status' => $newStatus,
                'display_status' => $newStatus,
            ));
            return;
        }

        $this->jsonResponse(false, 'Project status was not updated. Please refresh and try again.');
    }

    private function updateSelectedProjectStatus($action) {
        if (!$this->canCloseProcessProjects()) {
            $this->jsonResponse(false, 'You are not authorized to update project status.');
            return;
        }

        $fromStatus = ($action === 'close') ? 'Process' : 'Closed';
        $toStatus = ($action === 'close') ? 'Closed' : 'Process';
        $projectIds = $this->normalizeProjectIds($this->input->post('project_ids'));

        if (empty($projectIds)) {
            $this->jsonResponse(false, 'Please select at least one project.');
            return;
        }

        $updatedCount = $this->emp_record_inactivity_model->updateProjectsStatusByIds($projectIds, $fromStatus, $toStatus);
        $label = ($toStatus === 'Process') ? 'In Process' : 'Closed';

        $this->jsonResponse(true, $updatedCount > 0
            ? ($updatedCount . ' project(s) status updated to ' . $label . '.')
            : ('No ' . $fromStatus . ' projects were updated. Selected projects may already be ' . $toStatus . '.'), array(
            'updated' => $updatedCount,
        ));
    }

    private function normalizeProjectIds($rawProjectIds) {
        $projectIds = array();
        if (!is_array($rawProjectIds)) {
            return $projectIds;
        }
        foreach ($rawProjectIds as $rawId) {
            $rawId = (int)$rawId;
            if ($rawId > 0) {
                $projectIds[] = $rawId;
            }
        }
        return array_values(array_unique($projectIds));
    }

    private function jsonResponse($success, $message, $extra = array()) {
        $payload = array_merge(array(
            'success' => (bool)$success,
            'message' => $message,
        ), $extra);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    private function canCloseProcessProjects() {
        $userType = isset($this->session->userdata['logged_in_timesheet']['user_type'])
            ? $this->session->userdata['logged_in_timesheet']['user_type']
            : '';
        return in_array($userType, array('admin', 'superadmin', 'business_head', 'manager'), true);
    }
}
