<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_allocation extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->model('timesheet_login');
		$this->load->model('data_allocation_model');

		if (empty($this->session->userdata['logged_in_timesheet'])) {
			redirect('home/login');
		}

		$username = isset($this->session->userdata['logged_in_timesheet']['username'])
			? $this->session->userdata['logged_in_timesheet']['username']
			: '';
		if ($username !== 'kanth') {
			redirect('home');
		}
	}

	public function index() {
		$data['managers'] = $this->data_allocation_model->getManagers();
		$data['logs'] = $this->data_allocation_model->getRecentLogs(20);
		$this->load->view('data_allocation/index', $data);
	}

	public function preview() {
		$this->jsonStart();
		$fromEmpId = (int)$this->input->post('from_empId');
		if ($fromEmpId <= 0) {
			$this->jsonOut(array('success' => false, 'message' => 'Please choose a from manager.'));
			return;
		}
		$this->jsonOut($this->data_allocation_model->getPreview($fromEmpId));
	}

	public function transfer() {
		$this->jsonStart();
		$fromEmpId = (int)$this->input->post('from_empId');
		$toEmpId = (int)$this->input->post('to_empId');
		$modules = $this->input->post('modules');
		if (!is_array($modules)) {
			$modules = array_filter(array_map('trim', explode(',', (string)$modules)));
		}
		$selectedIds = array(
			'clients' => $this->input->post('client_ids'),
			'projects' => $this->input->post('project_ids'),
			'tasks' => $this->input->post('task_ids'),
		);
		$transferAll = array(
			'clients' => ((string)$this->input->post('transfer_all_clients') === '1'),
			'projects' => ((string)$this->input->post('transfer_all_projects') === '1'),
			'tasks' => ((string)$this->input->post('transfer_all_tasks') === '1'),
		);

		$this->jsonOut($this->data_allocation_model->transfer($fromEmpId, $toEmpId, $modules, $selectedIds, $transferAll));
	}

	private function jsonStart() {
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
		$this->output->enable_profiler(false);
		$this->db->db_debug = false;
		header('Content-Type: application/json; charset=utf-8');
	}

	private function jsonOut($payload) {
		if (!is_array($payload)) {
			$payload = array('success' => false, 'message' => 'Unexpected transfer response.');
		}
		$json = json_encode($payload);
		if ($json === false) {
			$json = json_encode(array(
				'success' => !empty($payload['success']),
				'message' => isset($payload['message']) ? utf8_encode((string)$payload['message']) : 'Unable to encode response.',
			));
		}
		echo $json;
	}
}
