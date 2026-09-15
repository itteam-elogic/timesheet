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

	/**
	 * Daily RS vs TS emails at 11:30 AM and 1:00 PM IST to laxmikanth@elogictech.com.
	 * Login-free trigger for Windows Task Scheduler.
	 * Call via: GET /cron/rs_vs_ts_notifications?key=YOUR_CRON_KEY&slot=11am|1pm
	 */
	public function rs_vs_ts_notifications() {
		date_default_timezone_set('Asia/Kolkata');
		ignore_user_abort(true);
		@set_time_limit(180);

		$cronKey = $this->config->item('rs_vs_ts_cron_key');
		$hasSession = !empty($this->session->userdata['logged_in_timesheet']);
		$keyOk = !empty($cronKey) && $this->input->get('key') === $cronKey;
		if (!$hasSession && !$keyOk) {
			header('HTTP/1.0 403 Forbidden');
			echo 'Forbidden';
			return;
		}

		$query = array();
		if (!empty($cronKey)) {
			$query['key'] = $cronKey;
		}
		$slot = $this->input->get('slot');
		if (!empty($slot)) {
			$query['slot'] = $slot;
		}
		$target = site_url('clients/send_rs_vs_ts_report_cron');
		if (!empty($query)) {
			$target .= '?' . http_build_query($query);
		}

		$ctx = stream_context_create(array(
			'http' => array(
				'method' => 'GET',
				'timeout' => 180,
				'ignore_errors' => true
			)
		));
		$body = @file_get_contents($target, false, $ctx);
		header('Content-Type: text/plain; charset=utf-8');
		if ($body === false) {
			echo 'Failed to trigger RS vs TS email via ' . $target;
			return;
		}
		echo $body;
	}

	/**
	 * Daily Resource Schedule emails at 11:30 AM and 1:00 PM IST to laxmikanth@elogictech.com.
	 * Same content as the Resource Schedule Sent button.
	 * Login-free trigger for Windows Task Scheduler.
	 * Call via: GET /cron/resource_schedule_notifications?key=YOUR_CRON_KEY&slot=11am|1pm
	 */
	public function resource_schedule_notifications() {
		date_default_timezone_set('Asia/Kolkata');
		ignore_user_abort(true);
		@set_time_limit(180);

		$cronKey = $this->config->item('resource_schedule_cron_key');
		if (empty($cronKey)) {
			$cronKey = $this->config->item('rs_vs_ts_cron_key');
		}
		$hasSession = !empty($this->session->userdata['logged_in_timesheet']);
		$keyOk = !empty($cronKey) && $this->input->get('key') === $cronKey;
		if (!$hasSession && !$keyOk) {
			header('HTTP/1.0 403 Forbidden');
			echo 'Forbidden';
			return;
		}

		$query = array();
		if (!empty($cronKey)) {
			$query['key'] = $cronKey;
		}
		$slot = $this->input->get('slot');
		if (!empty($slot)) {
			$query['slot'] = $slot;
		}
		$target = site_url('resource_schedule/send_today_resource_schedule_email_cron');
		if (!empty($query)) {
			$target .= '?' . http_build_query($query);
		}

		$ctx = stream_context_create(array(
			'http' => array(
				'method' => 'GET',
				'timeout' => 180,
				'ignore_errors' => true
			)
		));
		$body = @file_get_contents($target, false, $ctx);
		header('Content-Type: text/plain; charset=utf-8');
		if ($body === false) {
			echo 'Failed to trigger resource schedule email via ' . $target;
			return;
		}
		echo $body;
	}
}
