<?php $this->load->view('includes/cRMHeader');

if (!function_exists('hn_format_hours')) {
	function hn_format_hours($hours) {
		if ($hours === '' || $hours === null) {
			return '0';
		}
		return rtrim(rtrim(number_format((float)$hours, 2, '.', ''), '0'), '.');
	}
}

$projects = isset($projects) ? $projects : array();
?>
<style>
.hn-table-card {
	border: 1px solid #e3e8ef;
	border-radius: 10px;
	box-shadow: 0 2px 10px rgba(31, 80, 118, 0.07);
	overflow: hidden;
}
.hn-table-toolbar {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 14px 18px;
	background: #f8fafc;
	border-bottom: 1px solid #e8edf3;
}
.hn-table-toolbar h3 {
	margin: 0;
	font-size: 16px;
	font-weight: 700;
	color: #1f5076;
}
#hoursNotificationTable thead th {
	background: linear-gradient(to bottom, #337ab7, #2c5aa0);
	color: #fff;
	font-weight: 700;
	font-size: 12px;
	text-transform: uppercase;
	padding: 11px 10px;
	white-space: nowrap;
}
#hoursNotificationTable tbody td {
	padding: 10px;
	vertical-align: middle;
	font-size: 13px;
}
.hn-num {
	text-align: right;
	white-space: nowrap;
}
.hn-center {
	text-align: center;
	white-space: nowrap;
}
</style>
<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1>Project Hours Notifications</h1>
			<p style="color:#6c757d; margin:0;">Notifications follow Estimated Hours / Notify Hours. Closed projects and projects that already received the final estimated-hours email are hidden.</p>
		</div>
	</div>
	<div class="card hn-table-card">
		<div class="hn-table-toolbar">
			<h3>Project-wise notifications <span class="badge"><?php echo count($projects); ?></span></h3>
			<div>
				<button type="button" class="btn btn-success" id="sendSelectedNotifications">
					<i class="fa fa-paper-plane"></i> Send Selected
				</button>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-striped" id="hoursNotificationTable">
					<thead>
						<tr>
							<th class="hn-center"><input type="checkbox" id="checkAllProjects"></th>
							<th>S.No</th>
							<th>Client</th>
							<th>Project</th>
							<th>PM Name</th>
							<th>Notify Hours</th>
							<th>Estimated Hours</th>
							<th>Completed Hours</th>
							<th>Remaining Hours</th>
							<th>Remaining Sends</th>
							<th>Last Sent</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($projects)): ?>
							<?php $sno = 1; foreach ($projects as $project): ?>
								<?php
									$estimated = isset($project->estimated_hours) ? (float)$project->estimated_hours : 0;
									$completed = isset($project->completed_hours) ? (float)$project->completed_hours : 0;
									$remaining = isset($project->remaining_hours) ? (float)$project->remaining_hours : ($estimated - $completed);
									$pmName = !empty($project->manager_name) ? $project->manager_name : (!empty($project->p_manager) ? $project->p_manager : '-');
									$lastSent = !empty($project->last_sent_at) ? date('d-M-Y h:i A', strtotime($project->last_sent_at)) : '-';
									$canSend = !empty($project->can_send);
									$isFinal = !empty($project->is_final);
									$maxSends = isset($project->max_sends) ? (int)$project->max_sends : 0;
									$remainingSends = isset($project->remaining_sends) ? (int)$project->remaining_sends : 0;
								?>
								<tr data-project-id="<?php echo (int)$project->project_Id; ?>">
									<td class="hn-center">
										<?php if ($canSend): ?>
											<input type="checkbox" class="project-check" value="<?php echo (int)$project->project_Id; ?>">
										<?php endif; ?>
									</td>
									<td class="hn-center"><?php echo $sno++; ?></td>
									<td><?php echo htmlspecialchars($project->client_name); ?></td>
									<td><?php echo htmlspecialchars($project->project_name); ?></td>
									<td><?php echo htmlspecialchars($pmName); ?></td>
									<td class="hn-num"><?php echo htmlspecialchars(hn_format_hours($project->notif_hours)); ?></td>
									<td class="hn-num"><?php echo htmlspecialchars(hn_format_hours($estimated)); ?></td>
									<td class="hn-num"><?php echo htmlspecialchars(hn_format_hours($completed)); ?></td>
									<td class="hn-num"><?php echo htmlspecialchars(hn_format_hours($remaining)); ?></td>
									<td class="hn-center"><?php echo (int)$remainingSends; ?> / <?php echo (int)$maxSends; ?></td>
									<td class="hn-center last-sent-cell"><?php echo htmlspecialchars($lastSent); ?></td>
									<td class="hn-center">
										<?php if ($canSend): ?>
											<button type="button" class="btn btn-<?php echo $isFinal ? 'warning' : 'primary'; ?> btn-sm send-hours-notification" data-project-id="<?php echo (int)$project->project_Id; ?>">
												<i class="fa fa-paper-plane"></i> <?php echo $isFinal ? 'Send Final' : 'Send'; ?>
											</button>
										<?php else: ?>
											<button type="button" class="btn btn-default btn-sm" disabled>Waiting next <?php echo htmlspecialchars(hn_format_hours($project->notif_hours)); ?> hrs</button>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="12" class="text-center">No open projects are waiting for hours notifications.</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function sendOneProjectNotification(projectId, $btn) {
		projectId = parseInt(projectId, 10);
		if (!projectId) {
			alert('Please choose a project.');
			return;
		}
		var originalHtml = $btn.html();
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
		$.ajax({
			url: '<?php echo base_url("projects/send_hours_notification"); ?>',
			type: 'POST',
			dataType: 'json',
			data: { project_Id: projectId },
			success: function(res) {
				alert(res && res.message ? res.message : 'Notification request completed.');
				if (res && res.success) {
					window.location.reload();
				}
			},
			error: function() {
				alert('Failed to send notification. Please try again.');
			},
			complete: function() {
				$btn.prop('disabled', false).html(originalHtml);
			}
		});
	}

	function sendSelectedProjectNotifications($btn) {
		var ids = [];
		$('.project-check:checked').each(function() {
			var id = parseInt($(this).val(), 10);
			if (id) {
				ids.push(id);
			}
		});
		if (!ids.length) {
			alert('Please choose at least one project.');
			return;
		}
		var originalHtml = $btn.html();
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
		$.ajax({
			url: '<?php echo base_url("projects/send_hours_notifications_bulk"); ?>',
			type: 'POST',
			dataType: 'json',
			data: { project_Ids: ids },
			success: function(res) {
				alert(res && res.message ? res.message : 'Notification request completed.');
				if (res && res.success) {
					window.location.reload();
				}
			},
			error: function() {
				alert('Failed to send notification. Please try again.');
			},
			complete: function() {
				$btn.prop('disabled', false).html(originalHtml);
			}
		});
	}

	$(document).on('click', '.send-hours-notification', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		sendOneProjectNotification($(this).attr('data-project-id'), $(this));
	});

	$('#checkAllProjects').on('change', function() {
		$('.project-check').prop('checked', this.checked);
	});

	$('#sendSelectedNotifications').on('click', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		sendSelectedProjectNotifications($(this));
	});
</script>
<?php $this->load->view('includes/cRMFooter'); ?>
