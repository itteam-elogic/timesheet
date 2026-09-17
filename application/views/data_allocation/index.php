<?php $this->load->view('includes/cRMHeader');
$managers = isset($managers) ? $managers : array();
$logs = isset($logs) ? $logs : array();
?>
<style>
.da-card {
	border: 1px solid #e3e8ef;
	border-radius: 10px;
	box-shadow: 0 2px 10px rgba(31, 80, 118, 0.07);
	overflow: hidden;
	margin-bottom: 20px;
}
.da-toolbar {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 14px 18px;
	background: #f8fafc;
	border-bottom: 1px solid #e8edf3;
}
.da-toolbar h3 {
	margin: 0;
	font-size: 16px;
	font-weight: 700;
	color: #1f5076;
}
.da-form-grid {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 16px;
	padding: 18px;
}
.da-form-group label {
	display: block;
	font-weight: 700;
	color: #1f5076;
	margin-bottom: 6px;
}
.da-modules {
	padding: 0 18px 18px;
}
.da-modules label {
	margin-right: 18px;
	font-weight: 600;
}
.da-actions {
	padding: 0 18px 18px;
}
.da-count-row {
	display: flex;
	gap: 12px;
	flex-wrap: wrap;
	padding: 0 18px 14px;
}
.da-count {
	background: #eef6fb;
	border: 1px solid #d5e6f3;
	border-radius: 8px;
	padding: 10px 14px;
	min-width: 130px;
}
.da-count strong {
	display: block;
	font-size: 20px;
	color: #1f5076;
}
.da-count span {
	color: #5a6a7a;
	font-size: 12px;
	text-transform: uppercase;
	font-weight: 700;
}
.da-table thead th {
	background: linear-gradient(to bottom, #337ab7, #2c5aa0);
	color: #fff;
	font-weight: 700;
	font-size: 12px;
	text-transform: uppercase;
	padding: 11px 10px;
	white-space: nowrap;
}
.da-table tbody td {
	padding: 8px 10px;
	vertical-align: middle;
	font-size: 13px;
}
.da-center { text-align: center; }
.da-hidden { display: none; }
.da-note {
	color: #6c757d;
	margin: 0 0 8px;
}
.da-section {
	padding: 0 18px 18px;
}
.da-tabs {
	display: flex;
	gap: 8px;
	margin: 0 0 16px;
	flex-wrap: wrap;
}
.da-tab {
	border: 1px solid #c5d5e4;
	background: #fff;
	color: #1f5076;
	border-radius: 8px;
	padding: 8px 14px;
	font-weight: 700;
	cursor: pointer;
}
.da-tab.is-active {
	background: #1f5076;
	border-color: #1f5076;
	color: #fff;
}
.da-search {
	max-width: 280px;
}
.da-muted {
	color: #6c757d;
	font-size: 12px;
	margin: 0 0 10px;
}
</style>
<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1>Data Allocation</h1>
			<p class="da-note">Transfer records from one manager to another. Use <strong>Allocate by client</strong> to choose specific clients and move those clients plus their related projects and tasks to the selected manager.</p>
		</div>
	</div>

	<div class="da-tabs">
		<button type="button" class="da-tab is-active" data-panel="panelManagerTransfer">Transfer by manager</button>
		<button type="button" class="da-tab" data-panel="panelClientTransfer">Allocate by client</button>
	</div>

	<div id="panelManagerTransfer">
	<div class="card da-card">
		<div class="da-toolbar">
			<h3>Transfer manager data</h3>
		</div>
		<div class="da-form-grid">
			<div class="da-form-group">
				<label for="from_empId">From Manager</label>
				<select class="form-control" id="from_empId" name="from_empId">
					<option value="">Choose from manager</option>
					<?php foreach ($managers as $manager): ?>
						<option value="<?php echo (int)$manager->empId; ?>">
							<?php echo htmlspecialchars($manager->name); ?>
							[empId <?php echo (int)$manager->empId; ?>]
							<?php echo ($manager->status !== 'Active') ? ' ('.$manager->status.')' : ''; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="da-form-group">
				<label for="to_empId">To Manager</label>
				<select class="form-control" id="to_empId" name="to_empId">
					<option value="">Choose to manager</option>
					<?php foreach ($managers as $manager): ?>
						<?php if ($manager->status !== 'Active') { continue; } ?>
						<option value="<?php echo (int)$manager->empId; ?>">
							<?php echo htmlspecialchars($manager->name); ?>
							[empId <?php echo (int)$manager->empId; ?>]
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="da-modules">
			<label><input type="checkbox" class="da-module" value="clients" checked> Clients (client_details.empId)</label>
			<label><input type="checkbox" class="da-module" value="projects" checked> Projects (project_details.empId)</label>
			<label><input type="checkbox" class="da-module" value="tasks" checked> Tasks (task_details.empId)</label>
		</div>
		<div class="da-actions">
			<button type="button" class="btn btn-info" id="previewAllocation">
				<i class="fa fa-search"></i> Preview
			</button>
			<button type="button" class="btn btn-success" id="transferAllocation" disabled>
				<i class="fa fa-exchange"></i> Transfer Selected
			</button>
		</div>
		<div id="previewSummary" class="da-hidden">
			<div class="da-count-row">
				<div class="da-count"><span>Clients</span><strong id="countClients">0</strong></div>
				<div class="da-count"><span>Projects</span><strong id="countProjects">0</strong></div>
				<div class="da-count"><span>Tasks</span><strong id="countTasks">0</strong></div>
			</div>
		</div>
	</div>

	<div id="previewTables" class="da-hidden">
		<div class="card da-card">
			<div class="da-toolbar">
				<h3>Clients</h3>
				<label><input type="checkbox" class="da-check-all" data-target=".client-check" checked> Select all</label>
			</div>
			<div class="da-section">
				<div class="table-responsive">
					<table class="table table-bordered table-striped da-table" id="clientsTable">
						<thead>
							<tr>
								<th class="da-center"></th>
								<th>S.No</th>
								<th>Client ID</th>
								<th>Client</th>
								<th>empId</th>
								<th>Department</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="card da-card">
			<div class="da-toolbar">
				<h3>Projects</h3>
				<label><input type="checkbox" class="da-check-all" data-target=".project-check" checked> Select all</label>
			</div>
			<div class="da-section">
				<div class="table-responsive">
					<table class="table table-bordered table-striped da-table" id="projectsTable">
						<thead>
							<tr>
								<th class="da-center"></th>
								<th>S.No</th>
								<th>Project ID</th>
								<th>Project No</th>
								<th>Project</th>
								<th>Client</th>
								<th>empId</th>
								<th>PM Name</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="card da-card">
			<div class="da-toolbar">
				<h3>Tasks</h3>
				<label><input type="checkbox" class="da-check-all" data-target=".task-check" checked> Select all</label>
			</div>
			<div class="da-section">
				<div class="table-responsive">
					<table class="table table-bordered table-striped da-table" id="tasksTable">
						<thead>
							<tr>
								<th class="da-center"></th>
								<th>S.No</th>
								<th>Task ID</th>
								<th>Task</th>
								<th>Client</th>
								<th>Project</th>
								<th>empId</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	</div>

	<div id="panelClientTransfer" class="da-hidden">
		<div class="card da-card">
			<div class="da-toolbar">
				<h3>Allocate by client</h3>
			</div>
			<div class="da-section">
				<p class="da-muted">Choose a from manager, select one or more clients, then allocate those clients and all related projects and tasks to the to manager.</p>
			</div>
			<div class="da-form-grid">
				<div class="da-form-group">
					<label for="client_from_empId">From Manager</label>
					<select class="form-control" id="client_from_empId" name="client_from_empId">
						<option value="">Choose from manager</option>
						<?php foreach ($managers as $manager): ?>
							<option value="<?php echo (int)$manager->empId; ?>">
								<?php echo htmlspecialchars($manager->name); ?>
								[empId <?php echo (int)$manager->empId; ?>]
								<?php echo ($manager->status !== 'Active') ? ' ('.$manager->status.')' : ''; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="da-form-group">
					<label for="client_to_empId">To Manager</label>
					<select class="form-control" id="client_to_empId" name="client_to_empId">
						<option value="">Choose to manager</option>
						<?php foreach ($managers as $manager): ?>
							<?php if ($manager->status !== 'Active') { continue; } ?>
							<option value="<?php echo (int)$manager->empId; ?>">
								<?php echo htmlspecialchars($manager->name); ?>
								[empId <?php echo (int)$manager->empId; ?>]
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="da-actions">
				<button type="button" class="btn btn-info" id="loadClientAllocation">
					<i class="fa fa-users"></i> Load Clients
				</button>
				<button type="button" class="btn btn-primary" id="previewClientPackage" disabled>
					<i class="fa fa-search"></i> Preview Related
				</button>
				<button type="button" class="btn btn-success" id="transferClientPackage" disabled>
					<i class="fa fa-exchange"></i> Allocate Selected Clients
				</button>
			</div>
			<div id="clientPreviewSummary" class="da-hidden">
				<div class="da-count-row">
					<div class="da-count"><span>Clients</span><strong id="clientCountClients">0</strong></div>
					<div class="da-count"><span>Related Projects</span><strong id="clientCountProjects">0</strong></div>
					<div class="da-count"><span>Related Tasks</span><strong id="clientCountTasks">0</strong></div>
				</div>
			</div>
		</div>

		<div id="clientPickCard" class="card da-card da-hidden">
			<div class="da-toolbar">
				<h3>Clients</h3>
				<label><input type="checkbox" id="clientPickAll" checked> Select all</label>
			</div>
			<div class="da-section">
				<input type="text" class="form-control da-search" id="clientPickSearch" placeholder="Search client...">
			</div>
			<div class="da-section">
				<div class="table-responsive">
					<table class="table table-bordered table-striped da-table" id="clientPickTable">
						<thead>
							<tr>
								<th class="da-center"></th>
								<th>S.No</th>
								<th>Client ID</th>
								<th>Client</th>
								<th>empId</th>
								<th>Status</th>
								<th>Projects</th>
								<th>Tasks</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="clientRelatedTables" class="da-hidden">
			<div class="card da-card">
				<div class="da-toolbar">
					<h3>Related projects</h3>
				</div>
				<div class="da-section">
					<div class="table-responsive">
						<table class="table table-bordered table-striped da-table" id="clientProjectsTable">
							<thead>
								<tr>
									<th>S.No</th>
									<th>Project ID</th>
									<th>Project No</th>
									<th>Project</th>
									<th>Client</th>
									<th>empId</th>
									<th>PM Name</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="card da-card">
				<div class="da-toolbar">
					<h3>Related tasks</h3>
				</div>
				<div class="da-section">
					<p class="da-muted da-hidden" id="clientTasksTruncated"></p>
					<div class="table-responsive">
						<table class="table table-bordered table-striped da-table" id="clientTasksTable">
							<thead>
								<tr>
									<th>S.No</th>
									<th>Task ID</th>
									<th>Task</th>
									<th>Client</th>
									<th>Project</th>
									<th>empId</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card da-card">
		<div class="da-toolbar">
			<h3>Recent transfers</h3>
		</div>
		<div class="da-section">
			<div class="table-responsive">
				<table class="table table-bordered table-striped da-table" id="logsTable">
					<thead>
						<tr>
							<th>Date</th>
							<th>Type</th>
							<th>From</th>
							<th>To</th>
							<th>Clients</th>
							<th>Projects</th>
							<th>Tasks</th>
							<th>By</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($logs)): ?>
							<?php foreach ($logs as $log): ?>
								<?php $logType = (strpos((string)$log->modules, 'by_client') !== false) ? 'By client' : 'By manager'; ?>
								<tr>
									<td><?php echo !empty($log->created_at) ? date('d-M-Y h:i A', strtotime($log->created_at)) : '-'; ?></td>
									<td><?php echo htmlspecialchars($logType); ?></td>
									<td><?php echo htmlspecialchars($log->from_name); ?></td>
									<td><?php echo htmlspecialchars($log->to_name); ?></td>
									<td class="da-center"><?php echo (int)$log->clients_count; ?></td>
									<td class="da-center"><?php echo (int)$log->projects_count; ?></td>
									<td class="da-center"><?php echo (int)$log->tasks_count; ?></td>
									<td><?php echo htmlspecialchars($log->transferred_by); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="8" class="text-center">No transfers yet.</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function escapeHtml(value) {
		if (value === null || value === undefined) {
			return '';
		}
		return String(value)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	function rowVal(row, keys) {
		for (var i = 0; i < keys.length; i++) {
			if (row[keys[i]] !== undefined && row[keys[i]] !== null && row[keys[i]] !== '') {
				return row[keys[i]];
			}
		}
		return '';
	}

	function selectedModules() {
		var modules = [];
		$('.da-module:checked').each(function() {
			modules.push($(this).val());
		});
		return modules;
	}

	function collectIds(selector) {
		var ids = [];
		$(selector + ':checked').each(function() {
			var id = parseInt($(this).val(), 10);
			if (id) {
				ids.push(id);
			}
		});
		return ids;
	}

	function isAllChecked(selector) {
		return $(selector).length > 0 && $(selector + ':checked').length === $(selector).length;
	}

	$('#from_empId, #to_empId, #client_from_empId, #client_to_empId').select2({ width: '100%' });

	$('.da-tab').on('click', function() {
		var panel = $(this).attr('data-panel');
		$('.da-tab').removeClass('is-active');
		$(this).addClass('is-active');
		$('#panelManagerTransfer, #panelClientTransfer').addClass('da-hidden');
		$('#' + panel).removeClass('da-hidden');
		$('#from_empId, #to_empId, #client_from_empId, #client_to_empId').next('.select2-container').css('width', '100%');
	});
	if (window.location.hash === '#by-client') {
		$('.da-tab[data-panel="panelClientTransfer"]').trigger('click');
	}

	$('#previewAllocation').on('click', function() {
		var fromEmpId = parseInt($('#from_empId').val(), 10);
		if (!fromEmpId) {
			alert('Please choose a from manager.');
			return;
		}
		if (!selectedModules().length) {
			alert('Please choose at least one data type.');
			return;
		}
		var $btn = $(this);
		var original = $btn.html();
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
		$.ajax({
			url: '<?php echo base_url("data_allocation/preview"); ?>',
			type: 'POST',
			dataType: 'json',
			data: { from_empId: fromEmpId },
			success: function(res) {
				if (!res || !res.success) {
					alert(res && res.message ? res.message : 'Unable to load preview.');
					return;
				}
				renderPreview(res);
			},
			error: function() {
				alert('Failed to load preview. Please try again.');
			},
			complete: function() {
				$btn.prop('disabled', false).html(original);
			}
		});
	});

	function renderPreview(res) {
		var clients = res.clients || [];
		var projects = res.projects || [];
		var tasks = res.tasks || [];
		$('#countClients').text(clients.length);
		$('#countProjects').text(projects.length);
		$('#countTasks').text(tasks.length);
		$('#previewSummary').removeClass('da-hidden');
		$('#previewTables').removeClass('da-hidden');
		$('#transferAllocation').prop('disabled', false);

		var clientRows = '';
		$.each(clients, function(i, row) {
			var clientId = parseInt(rowVal(row, ['client_Id', 'client_id']), 10);
			clientRows += '<tr>' +
				'<td class="da-center"><input type="checkbox" class="client-check" value="' + clientId + '" checked></td>' +
				'<td class="da-center">' + (i + 1) + '</td>' +
				'<td class="da-center">' + clientId + '</td>' +
				'<td>' + escapeHtml(row.client_name) + '</td>' +
				'<td class="da-center">' + escapeHtml(row.empId) + '</td>' +
				'<td>' + escapeHtml(row.department) + '</td>' +
				'<td>' + escapeHtml(row.status) + '</td>' +
				'</tr>';
		});
		$('#clientsTable tbody').html(clientRows || '<tr><td colspan="7" class="text-center">No clients found for this empId.</td></tr>');

		var projectRows = '';
		$.each(projects, function(i, row) {
			var projectId = parseInt(rowVal(row, ['project_Id', 'project_id']), 10);
			projectRows += '<tr>' +
				'<td class="da-center"><input type="checkbox" class="project-check" value="' + projectId + '" checked></td>' +
				'<td class="da-center">' + (i + 1) + '</td>' +
				'<td class="da-center">' + projectId + '</td>' +
				'<td>' + escapeHtml(row.project_number) + '</td>' +
				'<td>' + escapeHtml(row.project_name) + '</td>' +
				'<td>' + escapeHtml(row.client_name) + '</td>' +
				'<td class="da-center">' + escapeHtml(row.empId) + '</td>' +
				'<td>' + escapeHtml(row.p_manager) + '</td>' +
				'<td>' + escapeHtml(row.status) + '</td>' +
				'</tr>';
		});
		$('#projectsTable tbody').html(projectRows || '<tr><td colspan="9" class="text-center">No projects found for this empId.</td></tr>');

		var taskRows = '';
		$.each(tasks, function(i, row) {
			var taskId = parseInt(rowVal(row, ['task_Id', 'task_id']), 10);
			taskRows += '<tr>' +
				'<td class="da-center"><input type="checkbox" class="task-check" value="' + taskId + '" checked></td>' +
				'<td class="da-center">' + (i + 1) + '</td>' +
				'<td class="da-center">' + taskId + '</td>' +
				'<td>' + escapeHtml(row.task_name) + '</td>' +
				'<td>' + escapeHtml(row.client_name) + '</td>' +
				'<td>' + escapeHtml(row.project_name) + '</td>' +
				'<td class="da-center">' + escapeHtml(row.empId) + '</td>' +
				'<td>' + escapeHtml(row.status) + '</td>' +
				'</tr>';
		});
		$('#tasksTable tbody').html(taskRows || '<tr><td colspan="8" class="text-center">No tasks found for this empId.</td></tr>');
	}

	$(document).on('change', '.da-check-all', function() {
		$($(this).attr('data-target')).prop('checked', this.checked);
	});

	$('#transferAllocation').on('click', function() {
		var fromEmpId = parseInt($('#from_empId').val(), 10);
		var toEmpId = parseInt($('#to_empId').val(), 10);
		var modules = selectedModules();
		if (!fromEmpId || !toEmpId) {
			alert('Please choose both from and to managers.');
			return;
		}
		if (fromEmpId === toEmpId) {
			alert('From manager and to manager must be different.');
			return;
		}
		if (!modules.length) {
			alert('Please choose at least one data type.');
			return;
		}

		var transferAllClients = $.inArray('clients', modules) !== -1 && isAllChecked('.client-check');
		var transferAllProjects = $.inArray('projects', modules) !== -1 && isAllChecked('.project-check');
		var transferAllTasks = $.inArray('tasks', modules) !== -1 && isAllChecked('.task-check');
		var clientIds = ($.inArray('clients', modules) !== -1 && !transferAllClients) ? collectIds('.client-check') : [];
		var projectIds = ($.inArray('projects', modules) !== -1 && !transferAllProjects) ? collectIds('.project-check') : [];
		var taskIds = ($.inArray('tasks', modules) !== -1 && !transferAllTasks) ? collectIds('.task-check') : [];
		var total = (transferAllClients ? parseInt($('#countClients').text(), 10) : clientIds.length)
			+ (transferAllProjects ? parseInt($('#countProjects').text(), 10) : projectIds.length)
			+ (transferAllTasks ? parseInt($('#countTasks').text(), 10) : taskIds.length);
		if (!total) {
			alert('Please preview and select at least one record.');
			return;
		}

		var fromName = $('#from_empId option:selected').text().replace(/\s+/g, ' ').trim();
		var toName = $('#to_empId option:selected').text().replace(/\s+/g, ' ').trim();
		if (!confirm('Transfer ' + total + ' selected record(s) from ' + fromName + ' to ' + toName + '?')) {
			return;
		}

		var $btn = $(this);
		var original = $btn.html();
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Transferring...');
		$.ajax({
			url: '<?php echo base_url("data_allocation/transfer"); ?>',
			type: 'POST',
			dataType: 'json',
			data: {
				from_empId: fromEmpId,
				to_empId: toEmpId,
				modules: modules.join(','),
				client_ids: clientIds,
				project_ids: projectIds,
				task_ids: taskIds,
				transfer_all_clients: transferAllClients ? 1 : 0,
				transfer_all_projects: transferAllProjects ? 1 : 0,
				transfer_all_tasks: transferAllTasks ? 1 : 0
			},
			success: function(res) {
				alert(res && res.message ? res.message : 'Transfer request completed.');
				if (res && res.success) {
					window.location.reload();
				}
			},
			error: function(xhr) {
				var msg = 'Failed to transfer data. Please try again.';
				if (xhr && xhr.responseText) {
					try {
						var parsed = JSON.parse(xhr.responseText);
						if (parsed && parsed.message) {
							msg = parsed.message;
						}
					} catch (e) {
						var text = String(xhr.responseText).replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
						if (text) {
							msg = text.substring(0, 400);
						}
					}
				}
				alert(msg);
			},
			complete: function() {
				$btn.prop('disabled', false).html(original);
			}
		});
	});

	function selectedClientIds() {
		return collectIds('.client-pick');
	}

	function resetClientPackagePreview() {
		$('#clientPreviewSummary').addClass('da-hidden');
		$('#clientRelatedTables').addClass('da-hidden');
		$('#clientCountClients').text('0');
		$('#clientCountProjects').text('0');
		$('#clientCountTasks').text('0');
		$('#clientProjectsTable tbody').empty();
		$('#clientTasksTable tbody').empty();
		$('#clientTasksTruncated').addClass('da-hidden').text('');
		$('#transferClientPackage').prop('disabled', true);
	}

	function updateClientPickButtons() {
		var hasClients = selectedClientIds().length > 0;
		$('#previewClientPackage').prop('disabled', !hasClients);
		if (!hasClients) {
			$('#transferClientPackage').prop('disabled', true);
		}
	}

	function renderClientPick(clients) {
		var rows = '';
		$.each(clients, function(i, row) {
			var clientId = parseInt(rowVal(row, ['client_Id', 'client_id']), 10);
			rows += '<tr>' +
				'<td class="da-center"><input type="checkbox" class="client-pick" value="' + clientId + '" checked></td>' +
				'<td class="da-center">' + (i + 1) + '</td>' +
				'<td class="da-center">' + clientId + '</td>' +
				'<td>' + escapeHtml(row.client_name) + '</td>' +
				'<td class="da-center">' + escapeHtml(row.empId) + '</td>' +
				'<td>' + escapeHtml(row.status) + '</td>' +
				'<td class="da-center">' + escapeHtml(row.projects_count) + '</td>' +
				'<td class="da-center">' + escapeHtml(row.tasks_count) + '</td>' +
				'</tr>';
		});
		$('#clientPickTable tbody').html(rows || '<tr><td colspan="8" class="text-center">No clients found for this manager.</td></tr>');
		$('#clientPickCard').removeClass('da-hidden');
		$('#clientPickAll').prop('checked', clients.length > 0);
		$('#clientPickSearch').val('');
		resetClientPackagePreview();
		updateClientPickButtons();
	}

	function renderClientPackage(res) {
		var clients = res.clients || [];
		var projects = res.projects || [];
		var tasks = res.tasks || [];
		var counts = res.counts || {};
		$('#clientCountClients').text(counts.clients || clients.length || 0);
		$('#clientCountProjects').text(counts.projects || projects.length || 0);
		$('#clientCountTasks').text(counts.tasks || tasks.length || 0);
		$('#clientPreviewSummary').removeClass('da-hidden');
		$('#clientRelatedTables').removeClass('da-hidden');
		$('#transferClientPackage').prop('disabled', clients.length <= 0);

		var projectRows = '';
		$.each(projects, function(i, row) {
			var projectId = parseInt(rowVal(row, ['project_Id', 'project_id']), 10);
			projectRows += '<tr>' +
				'<td class="da-center">' + (i + 1) + '</td>' +
				'<td class="da-center">' + projectId + '</td>' +
				'<td>' + escapeHtml(row.project_number) + '</td>' +
				'<td>' + escapeHtml(row.project_name) + '</td>' +
				'<td>' + escapeHtml(row.client_name) + '</td>' +
				'<td class="da-center">' + escapeHtml(row.empId) + '</td>' +
				'<td>' + escapeHtml(row.p_manager) + '</td>' +
				'<td>' + escapeHtml(row.status) + '</td>' +
				'</tr>';
		});
		$('#clientProjectsTable tbody').html(projectRows || '<tr><td colspan="8" class="text-center">No related projects found for the selected clients.</td></tr>');

		var taskRows = '';
		$.each(tasks, function(i, row) {
			var taskId = parseInt(rowVal(row, ['task_Id', 'task_id']), 10);
			taskRows += '<tr>' +
				'<td class="da-center">' + (i + 1) + '</td>' +
				'<td class="da-center">' + taskId + '</td>' +
				'<td>' + escapeHtml(row.task_name) + '</td>' +
				'<td>' + escapeHtml(row.client_name) + '</td>' +
				'<td>' + escapeHtml(row.project_name) + '</td>' +
				'<td class="da-center">' + escapeHtml(row.empId) + '</td>' +
				'<td>' + escapeHtml(row.status) + '</td>' +
				'</tr>';
		});
		$('#clientTasksTable tbody').html(taskRows || '<tr><td colspan="7" class="text-center">No related tasks found for the selected clients.</td></tr>');

		if (res.tasks_truncated) {
			$('#clientTasksTruncated').removeClass('da-hidden').text('Showing first ' + (res.preview_limit || tasks.length) + ' of ' + (counts.tasks || 0) + ' related tasks. All related tasks will be allocated.');
		} else {
			$('#clientTasksTruncated').addClass('da-hidden').text('');
		}
	}

	$('#loadClientAllocation').on('click', function() {
		var fromEmpId = parseInt($('#client_from_empId').val(), 10);
		if (!fromEmpId) {
			alert('Please choose a from manager.');
			return;
		}
		var $btn = $(this);
		var original = $btn.html();
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
		$.ajax({
			url: '<?php echo base_url("data_allocation/preview_clients"); ?>',
			type: 'POST',
			dataType: 'json',
			data: { from_empId: fromEmpId },
			success: function(res) {
				if (!res || !res.success) {
					alert(res && res.message ? res.message : 'Unable to load clients.');
					return;
				}
				renderClientPick(res.clients || []);
			},
			error: function() {
				alert('Failed to load clients. Please try again.');
			},
			complete: function() {
				$btn.prop('disabled', false).html(original);
			}
		});
	});

	$('#client_from_empId').on('change', function() {
		$('#clientPickCard').addClass('da-hidden');
		$('#clientPickTable tbody').empty();
		$('#previewClientPackage').prop('disabled', true);
		resetClientPackagePreview();
	});

	$('#clientPickAll').on('change', function() {
		$('.client-pick').prop('checked', this.checked);
		resetClientPackagePreview();
		updateClientPickButtons();
	});

	$(document).on('change', '.client-pick', function() {
		var all = $('.client-pick').length;
		var checked = $('.client-pick:checked').length;
		$('#clientPickAll').prop('checked', all > 0 && all === checked);
		resetClientPackagePreview();
		updateClientPickButtons();
	});

	$('#clientPickSearch').on('keyup', function() {
		var q = $.trim($(this).val()).toLowerCase();
		$('#clientPickTable tbody tr').each(function() {
			var text = $(this).text().toLowerCase();
			$(this).toggle(q === '' || text.indexOf(q) !== -1);
		});
	});

	$('#previewClientPackage').on('click', function() {
		var fromEmpId = parseInt($('#client_from_empId').val(), 10);
		var clientIds = selectedClientIds();
		if (!fromEmpId) {
			alert('Please choose a from manager.');
			return;
		}
		if (!clientIds.length) {
			alert('Please choose at least one client.');
			return;
		}
		var $btn = $(this);
		var original = $btn.html();
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
		$.ajax({
			url: '<?php echo base_url("data_allocation/preview_client_package"); ?>',
			type: 'POST',
			dataType: 'json',
			data: {
				from_empId: fromEmpId,
				client_ids: clientIds
			},
			success: function(res) {
				if (!res || !res.success) {
					alert(res && res.message ? res.message : 'Unable to load related records.');
					return;
				}
				renderClientPackage(res);
			},
			error: function() {
				alert('Failed to load related records. Please try again.');
			},
			complete: function() {
				$btn.prop('disabled', false).html(original);
				updateClientPickButtons();
			}
		});
	});

	$('#transferClientPackage').on('click', function() {
		var fromEmpId = parseInt($('#client_from_empId').val(), 10);
		var toEmpId = parseInt($('#client_to_empId').val(), 10);
		var clientIds = selectedClientIds();
		if (!fromEmpId || !toEmpId) {
			alert('Please choose both from and to managers.');
			return;
		}
		if (fromEmpId === toEmpId) {
			alert('From manager and to manager must be different.');
			return;
		}
		if (!clientIds.length) {
			alert('Please choose at least one client.');
			return;
		}

		var fromName = $('#client_from_empId option:selected').text().replace(/\s+/g, ' ').trim();
		var toName = $('#client_to_empId option:selected').text().replace(/\s+/g, ' ').trim();
		var clientCount = clientIds.length;
		var projectCount = parseInt($('#clientCountProjects').text(), 10) || 0;
		var taskCount = parseInt($('#clientCountTasks').text(), 10) || 0;
		if (!confirm('Allocate ' + clientCount + ' client(s) and related records (' + projectCount + ' project(s), ' + taskCount + ' task(s)) from ' + fromName + ' to ' + toName + '?')) {
			return;
		}

		var $btn = $(this);
		var original = $btn.html();
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Allocating...');
		$.ajax({
			url: '<?php echo base_url("data_allocation/transfer_by_client"); ?>',
			type: 'POST',
			dataType: 'json',
			data: {
				from_empId: fromEmpId,
				to_empId: toEmpId,
				client_ids: clientIds
			},
			success: function(res) {
				alert(res && res.message ? res.message : 'Allocation request completed.');
				if (res && res.success) {
					window.location.hash = 'by-client';
					window.location.reload();
				}
			},
			error: function(xhr) {
				var msg = 'Failed to allocate selected clients. Please try again.';
				if (xhr && xhr.responseText) {
					try {
						var parsed = JSON.parse(xhr.responseText);
						if (parsed && parsed.message) {
							msg = parsed.message;
						}
					} catch (e) {
						var text = String(xhr.responseText).replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
						if (text) {
							msg = text.substring(0, 400);
						}
					}
				}
				alert(msg);
			},
			complete: function() {
				$btn.prop('disabled', false).html(original);
			}
		});
	});
</script>
<?php $this->load->view('includes/cRMFooter'); ?>
