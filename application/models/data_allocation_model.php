<?php
/**
 * Transfer manager-owned records by empId:
 * client_details.empId (int), project_details.empId (int), task_details.empId (varchar).
 */
if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Data_allocation_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->ensureLogTable();
	}

	public function getManagers() {
		return $this->db->select('empId, name, username, user_type, status, department, email')
			->from('employee_details')
			->where_in('user_type', array('manager', 'business_head'))
			->order_by('status', 'asc')
			->order_by('name', 'asc')
			->get()
			->result();
	}

	public function getEmployeeById($empId) {
		$empId = (int)$empId;
		if ($empId <= 0) {
			return null;
		}
		return $this->db->select('empId, name, username, user_type, status, department, email')
			->from('employee_details')
			->where('empId', $empId)
			->limit(1)
			->get()
			->row();
	}

	public function getPreview($fromEmpId) {
		$fromEmpId = (int)$fromEmpId;
		$fromEmp = $this->getEmployeeById($fromEmpId);
		if (empty($fromEmp)) {
			return array(
				'success' => false,
				'message' => 'From manager was not found.',
			);
		}

		$clients = $this->getClientsByEmpId($fromEmpId);
		$projects = $this->getProjectsByEmpId($fromEmpId);
		$tasks = $this->getTasksByEmpId($fromEmpId);

		return array(
			'success' => true,
			'from_manager' => $fromEmp,
			'counts' => array(
				'clients' => count($clients),
				'projects' => count($projects),
				'tasks' => count($tasks),
			),
			'clients' => $clients,
			'projects' => $projects,
			'tasks' => $tasks,
		);
	}

	public function transfer($fromEmpId, $toEmpId, $modules, $selectedIds, $transferAll = array()) {
		$fromEmpId = (int)$fromEmpId;
		$toEmpId = (int)$toEmpId;
		$fromEmp = $this->getEmployeeById($fromEmpId);
		$toEmp = $this->getEmployeeById($toEmpId);

		if (empty($fromEmp) || empty($toEmp)) {
			return array('success' => false, 'message' => 'Please choose a valid from and to manager.');
		}
		if ($fromEmpId === $toEmpId) {
			return array('success' => false, 'message' => 'From manager and to manager must be different.');
		}

		if (!is_array($modules)) {
			$modules = array_filter(array_map('trim', explode(',', (string)$modules)));
		}
		$allowed = array('clients', 'projects', 'tasks');
		$modules = array_values(array_intersect($allowed, $modules));
		if (empty($modules)) {
			return array('success' => false, 'message' => 'Please choose at least one data type to transfer.');
		}

		$selectedIds = is_array($selectedIds) ? $selectedIds : array();
		$transferAll = is_array($transferAll) ? $transferAll : array();
		$now = date('Y-m-d H:i:s');
		$fromEmpKey = (string)$fromEmpId;
		$toEmpKey = (string)$toEmpId;
		$toManagerName = $this->fitManagerName($toEmp->name);
		$counts = array(
			'clients' => 0,
			'projects' => 0,
			'tasks' => 0,
		);

		$clientPk = $this->columnName('client_details', array('client_Id', 'client_id'));
		$projectPk = $this->columnName('project_details', array('project_Id', 'project_id'));
		$taskPk = $this->columnName('task_details', array('task_Id', 'task_id'));

		if (in_array('clients', $modules, true)) {
			$ids = $this->idsForModule('clients', $selectedIds, $transferAll);
			if ($ids !== false) {
				$this->db->where('empId', $fromEmpId);
				if (is_array($ids)) {
					$this->db->where_in($clientPk, $ids);
				}
				$ok = $this->db->update('client_details', $this->withTimestamp('client_details', array(
					'empId' => $toEmpId,
				), $now));
				if ($ok === false) {
					return $this->dbFail('Could not update client_details.');
				}
				$counts['clients'] = (int)$this->db->affected_rows();
			}
		}

		if (in_array('projects', $modules, true)) {
			$ids = $this->idsForModule('projects', $selectedIds, $transferAll);
			if ($ids !== false) {
				$projectData = array(
					'empId' => $toEmpId,
					'p_manager' => $toManagerName,
				);
				if ($this->db->field_exists('who_allocated_project_empId', 'project_details')) {
					$projectData['who_allocated_project_empId'] = $toEmpId;
				}
				$this->db->where('empId', $fromEmpId);
				if (is_array($ids)) {
					$this->db->where_in($projectPk, $ids);
				}
				$ok = $this->db->update('project_details', $this->withTimestamp('project_details', $projectData, $now));
				if ($ok === false) {
					return $this->dbFail('Could not update project_details.');
				}
				$counts['projects'] = (int)$this->db->affected_rows();

				if ($this->db->field_exists('who_allocated_project_empId', 'project_details')) {
					$this->db->where('empId', $toEmpId);
					if (is_array($ids)) {
						$this->db->where_in($projectPk, $ids);
					}
					$whoOk = $this->db->update('project_details', $this->withTimestamp('project_details', array(
						'who_allocated_project_empId' => $toEmpId,
					), $now));
					if ($whoOk === false) {
						return $this->dbFail('Could not update who_allocated_project_empId.');
					}
					$whoRows = (int)$this->db->affected_rows();
					if ($counts['projects'] <= 0 && $whoRows > 0) {
						$counts['projects'] = $whoRows;
					}
				}
			}
		}

		if (in_array('tasks', $modules, true)) {
			$ids = $this->idsForModule('tasks', $selectedIds, $transferAll);
			if ($ids !== false) {
				$this->db->group_start();
				$this->db->where('empId', $fromEmpKey);
				$this->db->or_where('empId', $fromEmpId);
				$this->db->group_end();
				if (is_array($ids)) {
					$this->db->where_in($taskPk, $ids);
				}
				$ok = $this->db->update('task_details', $this->withTimestamp('task_details', array(
					'empId' => $toEmpKey,
				), $now));
				if ($ok === false) {
					return $this->dbFail('Could not update task_details.');
				}
				$counts['tasks'] = (int)$this->db->affected_rows();
			}
		}

		$total = $counts['clients'] + $counts['projects'] + $counts['tasks'];
		if ($total <= 0) {
			return array('success' => false, 'message' => 'No matching records were transferred. Please preview and select records first.');
		}

		$transferredBy = '';
		$sessionUser = $this->session->userdata('logged_in_timesheet');
		if (!empty($sessionUser['username'])) {
			$transferredBy = $sessionUser['username'];
		}

		if ($this->db->table_exists('data_allocation_log')) {
			$this->db->insert('data_allocation_log', array(
				'from_empId' => $fromEmpId,
				'from_name' => $fromEmp->name,
				'to_empId' => $toEmpId,
				'to_name' => $toEmp->name,
				'modules' => implode(',', $modules),
				'clients_count' => $counts['clients'],
				'projects_count' => $counts['projects'],
				'tasks_count' => $counts['tasks'],
				'sow_count' => 0,
				'transferred_by' => $transferredBy,
				'created_at' => $now,
			));
		}

		return array(
			'success' => true,
			'counts' => $counts,
			'from_name' => $fromEmp->name,
			'to_name' => $toEmp->name,
			'message' => 'Transferred ' . $total . ' record(s) from ' . $fromEmp->name . ' to ' . $toEmp->name . ' by empId.',
		);
	}

	public function getRecentLogs($limit = 20) {
		if (!$this->db->table_exists('data_allocation_log')) {
			return array();
		}
		return $this->db->select('*')
			->from('data_allocation_log')
			->order_by('id', 'desc')
			->limit((int)$limit)
			->get()
			->result();
	}

	private function getClientsByEmpId($fromEmpId) {
		$pk = $this->columnName('client_details', array('client_Id', 'client_id'));
		return $this->db->select('c.' . $pk . ' as client_Id, c.client_name, c.department, c.status, c.empId, c.created_at')
			->from('client_details as c')
			->where('c.empId', $fromEmpId)
			->order_by('c.client_name', 'asc')
			->get()
			->result();
	}

	private function getProjectsByEmpId($fromEmpId) {
		$projectPk = $this->columnName('project_details', array('project_Id', 'project_id'));
		$clientPk = $this->columnName('client_details', array('client_Id', 'client_id'));
		return $this->db->select('p.' . $projectPk . ' as project_Id, p.project_name, p.project_number, p.status, p.p_manager, p.empId, p.who_allocated_project_empId, c.client_name')
			->from('project_details as p')
			->join('client_details as c', 'c.' . $clientPk . ' = p.' . $clientPk, 'left')
			->where('p.empId', $fromEmpId)
			->order_by('p.project_number', 'desc')
			->get()
			->result();
	}

	private function getTasksByEmpId($fromEmpId) {
		$fromEmpKey = (string)$fromEmpId;
		$taskPk = $this->columnName('task_details', array('task_Id', 'task_id'));
		$clientPk = $this->columnName('client_details', array('client_Id', 'client_id'));
		$projectPk = $this->columnName('project_details', array('project_Id', 'project_id'));
		$this->db->select('t.' . $taskPk . ' as task_Id, t.task_name, t.status, t.empId, t.created_at, c.client_name, p.project_name');
		$this->db->from('task_details as t');
		$this->db->join('client_details as c', 'c.' . $clientPk . ' = t.' . $clientPk, 'left');
		$this->db->join('project_details as p', 'p.' . $projectPk . ' = t.' . $projectPk, 'left');
		$this->db->group_start();
		$this->db->where('t.empId', $fromEmpKey);
		$this->db->or_where('t.empId', $fromEmpId);
		$this->db->group_end();
		$this->db->order_by('t.' . $taskPk, 'desc');
		return $this->db->get()->result();
	}

	private function idsForModule($module, $selectedIds, $transferAll) {
		if (!empty($transferAll[$module])) {
			return true;
		}
		$ids = $this->normalizeIds(isset($selectedIds[$module]) ? $selectedIds[$module] : array());
		if (empty($ids)) {
			return false;
		}
		return $ids;
	}

	private function normalizeIds($ids) {
		$clean = array();
		if (!is_array($ids)) {
			$ids = array($ids);
		}
		foreach ($ids as $id) {
			$id = (int)$id;
			if ($id > 0) {
				$clean[$id] = $id;
			}
		}
		return array_values($clean);
	}

	private function fitManagerName($name) {
		$name = trim((string)$name);
		if (strlen($name) <= 30) {
			return $name;
		}
		return substr($name, 0, 30);
	}

	private function columnName($table, $candidates) {
		foreach ($candidates as $column) {
			if ($this->db->field_exists($column, $table)) {
				return $column;
			}
		}
		return $candidates[0];
	}

	private function withTimestamp($table, $data, $now) {
		if ($this->db->field_exists('updated_at', $table)) {
			$data['updated_at'] = $now;
		}
		return $data;
	}

	private function dbFail($fallback) {
		$error = $this->db->error();
		$message = $fallback;
		if (!empty($error['message'])) {
			$message .= ' ' . $error['message'];
		}
		return array('success' => false, 'message' => $message);
	}

	private function ensureLogTable() {
		if ($this->db->table_exists('data_allocation_log')) {
			return;
		}
		$this->db->query("CREATE TABLE IF NOT EXISTS `data_allocation_log` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`from_empId` int(11) NOT NULL,
			`from_name` varchar(255) DEFAULT NULL,
			`to_empId` int(11) NOT NULL,
			`to_name` varchar(255) DEFAULT NULL,
			`modules` varchar(255) DEFAULT NULL,
			`clients_count` int(11) NOT NULL DEFAULT 0,
			`projects_count` int(11) NOT NULL DEFAULT 0,
			`tasks_count` int(11) NOT NULL DEFAULT 0,
			`sow_count` int(11) NOT NULL DEFAULT 0,
			`transferred_by` varchar(100) DEFAULT NULL,
			`created_at` datetime DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}
}
