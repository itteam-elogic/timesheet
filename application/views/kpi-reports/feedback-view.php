<?php $this->load->view('includes/cRMHeader'); ?>

<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Feedback Details</h1>
        </div>
        <div>
            <a href="<?php echo base_url('kpi_reports/feedbackReports'); ?>" class="btn btn-default back-to-reports-btn">
                <i class="fa fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <?php if (!empty($feedback)): ?>
        <?php 
        // Handle both array and object formats for backward compatibility
        $fb = is_array($feedback) ? $feedback[0] : $feedback; 
        ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Feedback #<?php echo $fb->feedback_id; ?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Team Member:</th>
                                <td><?php echo $fb->team_member_name ? $fb->team_member_name : ($fb->employee_name_full ? $fb->employee_name_full : $fb->employee_name); ?></td>
                            </tr>
                            <tr>
                                <th>Department:</th>
                                <td><?php echo $fb->emp_department ? $fb->emp_department : $fb->department; ?></td>
                            </tr>
                            <tr>
                                <th>Sub-Categories:</th>
                                <td>
                                    <?php 
                                    // Display sub-categories if available
                                    $sub_categories = array();
                                    if (!empty($fb->sub_categories)) {
                                        $decoded = json_decode($fb->sub_categories, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                            $sub_categories = array_filter($decoded); // Remove empty values
                                        }
                                    }
                                    if (!empty($sub_categories)): 
                                        foreach ($sub_categories as $index => $sub_cat): 
                                            if (!empty(trim($sub_cat))):
                                    ?>
                                        <span class="badge badge-secondary" style="margin-right: 5px; margin-bottom: 5px; display: inline-block; padding: 6px 12px; font-size: 13px;">
                                            <?php echo htmlspecialchars(trim($sub_cat)); ?>
                                        </span>
                                    <?php 
                                            endif;
                                        endforeach; 
                                    else: 
                                    ?>
                                        <span style="color: #6c757d; font-style: italic;">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Designation:</th>
                                <td><?php echo $fb->team_member_designation ? $fb->team_member_designation : ($fb->designation ? $fb->designation : 'N/A'); ?></td>
                            </tr>
                            <?php if (!empty($fb->feedback_for)): ?>
                            <tr>
                                <th>Feedback Type:</th>
                                <td><?php echo htmlspecialchars($fb->feedback_for); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th>Improvement area:</th>
                                <td>
                                    <?php 
                                    // Handle multiple improvement areas stored as JSON
                                    $improvement_areas = array();
                                    if (!empty($fb->feedback_type)) {
                                        // Try to decode as JSON first (for multiple selections)
                                        $decoded = json_decode($fb->feedback_type, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                            $improvement_areas = $decoded;
                                        } else {
                                            // If not JSON, treat as single value (backward compatibility)
                                            $improvement_areas = array($fb->feedback_type);
                                        }
                                    }
                                    
                                    if (!empty($improvement_areas)): 
                                        foreach ($improvement_areas as $area): 
                                            if (!empty(trim($area))):
                                    ?>
                                        <span class="badge badge-info" style="margin-right: 5px; margin-bottom: 5px; display: inline-block; padding: 6px 12px; font-size: 13px;">
                                            <?php echo htmlspecialchars(trim($area)); ?>
                                        </span>
                                    <?php 
                                            endif;
                                        endforeach; 
                                    else: 
                                    ?>
                                        <span style="color: #6c757d; font-style: italic;">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge feedback-status-badge <?php echo $this->feedback_model->get_feedback_status_badge_class($fb->status); ?>">
                                        <?php echo htmlspecialchars($this->feedback_model->get_feedback_status_label($fb->status)); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Reporting Manager:</th>
                                <td><?php echo $fb->reporting_manager_name ? $fb->reporting_manager_name : 'Not Assigned'; ?></td>
                            </tr>
                            <tr>
                                <th>Assigned To:</th>
                                <td><?php echo $fb->assigned_manager_name ? $fb->assigned_manager_name : 'Not Assigned'; ?></td>
                            </tr>
                            <tr>
                                <th>Created Date:</th>
                                <td><?php echo date('d M Y, h:i A', strtotime($fb->created_at)); ?></td>
                            </tr>
                            <?php if ($fb->response_date): ?>
                            <tr>
                                <th>Response Date:</th>
                                <td><?php echo date('d M Y, h:i A', strtotime($fb->response_date)); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Strengths & Achievements:</h4>
                        <div class="well" style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; min-height: 100px;">
                            <?php echo nl2br(htmlspecialchars($fb->strengths_achievements ? $fb->strengths_achievements : 'N/A')); ?>
                        </div>

                        <?php if ($fb->feedback_message): ?>
                            <h4 class="mt-4">Feedback for improvement:</h4>
                            <div class="well" style="background-color: #fff3cd; padding: 15px; border-radius: 4px; min-height: 100px;">
                                <?php echo nl2br(htmlspecialchars($fb->feedback_message)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($fb->response): ?>
                            <h4 class="mt-4">Team Member Response:</h4>
                            <div class="well" style="background-color: #e7f3ff; padding: 15px; border-radius: 4px; min-height: 100px;">
                                <?php echo nl2br(htmlspecialchars($fb->response)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            Feedback not found.
        </div>
    <?php endif; ?>
</div>

<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.table th {
    background-color: #f8f9fa;
}
.well {
    word-wrap: break-word;
}

.feedback-status-badge {
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 15px;
    color: #fff !important;
    border: none;
    display: inline-block;
    white-space: nowrap;
}
.feedback-status-pending {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
}
.feedback-status-acknowledged {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
}
.feedback-status-default {
    background-color: #6c757d !important;
}

/* Back to Reports Button Styling */
.back-to-reports-btn {
    background-color: #6c757d !important;
    color: white !important;
    border-color: #6c757d !important;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 4px;
}

.back-to-reports-btn:hover {
    background-color: #5a6268 !important;
    border-color: #545b62 !important;
    color: white !important;
}

/* Select2 Dropdown Styling */
.select2-container--default .select2-selection--single {
    height: 40px !important;
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    background-color: white !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 37px !important;
    padding-left: 8px !important;
    color: #333 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 5px !important;
}

.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #4361ee !important;
}

.select2-dropdown {
    border: 1px solid #4361ee !important;
    border-radius: 4px !important;
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #ddd !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #4361ee !important;
    color: white !important;
}
</style>


<?php $this->load->view('includes/cRMFooter'); ?>

