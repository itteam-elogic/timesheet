<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('project_model');
	}

	/**
	 * Auto-send Notification on Completion of hours emails for all open projects.
	 * Call via: GET /cron/project_hours_notifications
	 * Closed projects are skipped.
	 */
	public function project_hours_notifications() {
		$result = $this->project_model->processHoursCompletionNotifications(null, true);
		header('Content-Type: text/plain; charset=utf-8');
		echo isset($result['message']) ? $result['message'] : 'Done';
		echo "\n" . date('Y-m-d H:i:s');
	}
}
