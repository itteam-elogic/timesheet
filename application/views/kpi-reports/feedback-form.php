<?php $this->load->view('includes/cRMHeader'); ?>

<div class="content-wrapper">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 15px 0;">
        <div>
            <h1 style="margin: 0; font-size: 28px; font-weight: 600; color: #333;">
                <?php echo (isset($is_edit_mode) && $is_edit_mode) ? 'Edit Feedback Form' : 'Employee Feedback Form'; ?>
            </h1>
        </div>
        <div>
            <a href="<?php echo base_url('kpi_reports/feedbackReports'); ?>" class="btn btn-default" style="padding: 10px 20px; font-weight: 600; border-radius: 4px; background-color: #6c757d; color: white; border: none;">
                <i class="fa fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('error'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php 
            $form_action = (isset($is_edit_mode) && $is_edit_mode) ? 'kpi_reports/updateFeedbackForm' : 'kpi_reports/submitFeedback';
            echo form_open_multipart($form_action, array('class' => 'form-horizontal', 'id' => 'feedbackForm')); 
            
            // Add hidden field for feedback_id in edit mode
            if (isset($is_edit_mode) && $is_edit_mode && isset($feedback)) {
                echo form_hidden('feedback_id', $feedback->feedback_id);
            }
            ?>
            
            <div class="form-group">
                <label class="control-label col-md-3">Department: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <select class="form-control" name="department" id="department" required onchange="loadReportingManagers(); loadTeamMembers();">
                        <option value="">Select Department</option>
                        <?php 
                        $selected_dept = '';
                        if (isset($is_edit_mode) && $is_edit_mode && isset($feedback)) {
                            $selected_dept = $feedback->department;
                        } elseif (isset($current_emp)) {
                            $selected_dept = $current_emp->department;
                        }
                        ?>
                        <option value="Architectural" <?php echo ($selected_dept == 'Architectural') ? 'selected' : ''; ?>>Architectural</option>
                        <option value="Structural" <?php echo ($selected_dept == 'Structural') ? 'selected' : ''; ?>>Structural</option>
                        <option value="MEP" <?php echo ($selected_dept == 'MEP') ? 'selected' : ''; ?>>MEP</option>
                        <option value="3D Visualization" <?php echo ($selected_dept == '3D Visualization') ? 'selected' : ''; ?>>3D Visualization</option>
                        <option value="HR" <?php echo ($selected_dept == 'HR') ? 'selected' : ''; ?>>HR</option>
                        <option value="Software" <?php echo ($selected_dept == 'Software') ? 'selected' : ''; ?>>Software</option>
                        <option value="IT" <?php echo ($selected_dept == 'IT') ? 'selected' : ''; ?>>IT</option>
                        <option value="Operations Manager" <?php echo ($selected_dept == 'Operations Manager') ? 'selected' : ''; ?>>Operations Manager</option>
                        <option value="Business Development" <?php echo ($selected_dept == 'Business Development') ? 'selected' : ''; ?>>Business Development</option>
                        <option value="Others" <?php echo ($selected_dept == 'Others') ? 'selected' : ''; ?>>Others</option>
                    </select>
                    <?php echo form_error('department', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <!-- Sub-Categories Section -->
            <div class="form-group">
                <label class="control-label col-md-3">Sub-Categories:</label>
                <div class="col-md-6">
                    <div id="subCategoriesContainer">
                        <?php 
                        $sub_categories = isset($sub_categories) ? $sub_categories : array();
                        if (!empty($sub_categories) && is_array($sub_categories)) {
                            foreach ($sub_categories as $sub_cat) {
                                if (!empty(trim($sub_cat))) {
                                    echo '<div class="sub-category-item" style="margin-bottom: 10px;">';
                                    echo '<input type="text" class="form-control sub-category-input" name="sub_categories[]" value="' . htmlspecialchars($sub_cat) . '" placeholder="Enter sub-category">';
                                    echo '<button type="button" class="btn btn-sm btn-danger remove-sub-category" style="margin-left: 5px;"><i class="fa fa-times"></i></button>';
                                    echo '</div>';
                                }
                            }
                        }
                        if (empty($sub_categories)) {
                            echo '<div class="sub-category-item" style="margin-bottom: 10px;">';
                            echo '<input type="text" class="form-control sub-category-input" name="sub_categories[]" placeholder="Enter sub-category">';
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-info" id="addSubCategory" style="margin-top: 10px;">
                        <i class="fa fa-plus"></i> Add Sub-Category
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Reporting Manager: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <select class="form-control" name="reporting_manager" id="reporting_manager" required onchange="loadProjectCoordinators();">
                        <option value="">Select Reporting Manager</option>
                    </select>
                    <?php echo form_error('reporting_manager', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Project Coordinator:</label>
                <div class="col-md-6">
                    <select class="form-control" name="project_coordinator" id="project_coordinator">
                        <option value="">Select Project Coordinator</option>
                    </select>
                    <?php echo form_error('project_coordinator', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Team Member:</label>
                <div class="col-md-6">
                    <select class="form-control" name="team_members" id="team_members">
                        <option value="">Select Team Member</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Feedback Month: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label" style="font-weight: normal; margin-bottom: 5px;">From Month:</label>
                            <input type="month" class="form-control" name="feedback_month_from" id="feedback_month_from" 
                                   value="<?php echo (isset($feedback_month_from) && !empty($feedback_month_from)) ? $feedback_month_from : date('Y-m'); ?>" required>
                            <?php echo form_error('feedback_month_from', '<label class="error">', '</label>'); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label" style="font-weight: normal; margin-bottom: 5px;">To Month:</label>
                            <input type="month" class="form-control" name="feedback_month_to" id="feedback_month_to" 
                                   value="<?php echo (isset($feedback_month_to) && !empty($feedback_month_to)) ? $feedback_month_to : date('Y-m'); ?>" required>
                            <?php echo form_error('feedback_month_to', '<label class="error">', '</label>'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Feedback Type: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <select class="form-control" name="feedback_for" id="feedback_for" required>
                        <option value="">Select Feedback Type</option>
                        <?php 
                        $selected_feedback_for = (isset($is_edit_mode) && $is_edit_mode && isset($feedback)) ? $feedback->feedback_for : '';
                        ?>
                        <option value="Monthly KPI Review" <?php echo ($selected_feedback_for == 'Monthly KPI Review') ? 'selected' : ''; ?>>Monthly KPI Review</option>
                        <option value="General Feedback" <?php echo ($selected_feedback_for == 'General Feedback') ? 'selected' : ''; ?>>General Feedback</option>
                    </select>
                    <?php echo form_error('feedback_for', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Improvement area: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <select class="form-control" name="feedback_type[]" id="feedback_type" multiple="multiple" required>
                        <?php 
                        $selected_types = isset($feedback_types) ? $feedback_types : array();
                        $all_types = array(
                            'Productivity & Efficiency',
                            'Quality Improvement',
                            'Technical Knowledge & Skill Development',
                            'Ownership & Accountability',
                            'Innovation',
                            'Communication & Coordination'
                        );
                        foreach ($all_types as $type) {
                            $selected = in_array($type, $selected_types) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($type) . '" ' . $selected . '>' . htmlspecialchars($type) . '</option>';
                        }
                        ?>
                    </select>
                    <small class="text-muted" style="display: block; margin-top: 5px;">Hold Ctrl (or Cmd on Mac) to select multiple options</small>
                    <?php echo form_error('feedback_type', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Strengths & Achievements:</label>
                <div class="col-md-6">
                    <textarea class="form-control" name="strengths_achievements" id="strengths_achievements" rows="6" placeholder="Please provide strengths and achievements..."><?php echo (isset($is_edit_mode) && $is_edit_mode && isset($feedback)) ? htmlspecialchars($feedback->strengths_achievements) : ''; ?></textarea>
                    <?php echo form_error('strengths_achievements', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Feedback for improvement: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <textarea class="form-control" name="feedback_message" id="feedback_message" rows="6" placeholder="Please provide your detailed feedback..." required><?php echo (isset($is_edit_mode) && $is_edit_mode && isset($feedback)) ? htmlspecialchars($feedback->feedback_message) : ''; ?></textarea>
                    <?php echo form_error('feedback_message', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Attach File:</label>
                <div class="col-md-6">
                    <input type="file" class="form-control" name="attached_file" id="attached_file" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                    <small class="text-muted">Allowed formats: jpg, jpeg, png, gif, pdf, doc, docx, xls, xlsx, txt (Max: 10MB)</small>
                    <?php if (isset($is_edit_mode) && $is_edit_mode && isset($feedback) && !empty($feedback->attached_file)): ?>
                        <div style="margin-top: 10px;">
                            <p style="margin: 5px 0;"><strong>Current File:</strong></p>
                            <a href="<?php echo base_url($feedback->attached_file); ?>" target="_blank" class="btn btn-sm btn-info">
                                <i class="fa fa-download"></i> View Current File
                            </a>
                            <small class="text-muted" style="display: block; margin-top: 5px;">Upload a new file to replace the current one</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-offset-3 col-md-6">
                    <button type="submit" id="submitFeedbackBtn" class="btn btn-primary" style="padding: 10px 25px; font-weight: 600; border-radius: 4px;">
                        <i class="fa fa-<?php echo (isset($is_edit_mode) && $is_edit_mode) ? 'save' : 'paper-plane'; ?>"></i> 
                        <?php echo (isset($is_edit_mode) && $is_edit_mode) ? 'Update Feedback' : 'Submit Feedback'; ?>
                    </button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
// Get logged-in user's empId
var loggedInEmpId = <?php echo isset($this->session->userdata['logged_in_timesheet']['empId']) ? $this->session->userdata['logged_in_timesheet']['empId'] : 'null'; ?>;

// Edit mode data
var isEditMode = <?php echo (isset($is_edit_mode) && $is_edit_mode) ? 'true' : 'false'; ?>;
var editFeedbackData = <?php echo (isset($is_edit_mode) && $is_edit_mode && isset($feedback)) ? json_encode($feedback) : 'null'; ?>;

// Initial data loading moved to the select2 initialization section below

function loadReportingManagers() {
    var department = $('#department').val();
    $('#reporting_manager').html('<option value="">Loading...</option>');
    $('#project_coordinator').html('<option value="">Select Project Coordinator</option>');
    
    // Trigger select2 update
    $('#reporting_manager').trigger('change.select2');
    $('#project_coordinator').trigger('change.select2');
    
    if (!department) {
        $('#reporting_manager').html('<option value="">Select Reporting Manager</option>');
        $('#reporting_manager').trigger('change.select2');
        return;
    }
    
    $.ajax({
        url: '<?php echo base_url('kpi_reports/getReportingManagersByDept'); ?>',
        type: 'POST',
        data: { department: department },
        dataType: 'json',
        success: function(data) {
            var options = '<option value="">Select Reporting Manager</option>';
            var loggedInUserFound = false;
            var editModeSelected = false;
            
            if (data && data.length > 0) {
                $.each(data, function(index, manager) {
                    var selected = '';
                    // In edit mode, select the saved reporting manager
                    if (isEditMode && editFeedbackData && editFeedbackData.reporting_manager == manager.empId) {
                        selected = ' selected';
                        editModeSelected = true;
                    }
                    // Check if logged-in user matches this manager (only if not in edit mode)
                    else if (!isEditMode && loggedInEmpId && manager.empId == loggedInEmpId) {
                        selected = ' selected';
                        loggedInUserFound = true;
                    }
                    options += '<option value="' + manager.empId + '"' + selected + '>' + manager.name + '</option>';
                });
            }
            
            $('#reporting_manager').html(options);
            $('#reporting_manager').trigger('change.select2');
            
            // If logged-in user was found and selected, automatically load project coordinators
            if ((loggedInUserFound && loggedInEmpId) || editModeSelected) {
                loadProjectCoordinators();
            }
        },
        error: function() {
            $('#reporting_manager').html('<option value="">Error loading managers</option>');
            $('#reporting_manager').trigger('change.select2');
        }
    });
}

function loadProjectCoordinators() {
    var manager_id = $('#reporting_manager').val();
    $('#project_coordinator').html('<option value="">Loading...</option>');
    $('#project_coordinator').trigger('change.select2');
    
    if (!manager_id) {
        $('#project_coordinator').html('<option value="">Select Project Coordinator</option>');
        $('#project_coordinator').trigger('change.select2');
        return;
    }
    
    $.ajax({
        url: '<?php echo base_url('kpi_reports/getProjectCoordinatorsByManager'); ?>',
        type: 'POST',
        data: { manager_id: manager_id },
        dataType: 'json',
        success: function(data) {
            var options = '<option value="">Select Project Coordinator</option>';
            if (data && data.length > 0) {
                $.each(data, function(index, coordinator) {
                    var selected = '';
                    // In edit mode, select the saved project coordinator
                    if (isEditMode && editFeedbackData && editFeedbackData.project_coordinator == coordinator.empId) {
                        selected = ' selected';
                    }
                    var displayName = coordinator.name;
                    if (coordinator.emp_com_id) {
                        displayName += ' (' + coordinator.emp_com_id + ')';
                    }
                    if (coordinator.designation) {
                        displayName += ' - ' + coordinator.designation;
                    }
                    options += '<option value="' + coordinator.empId + '"' + selected + '>' + displayName + '</option>';
                });
            } else {
                options += '<option value="">No coordinators found</option>';
            }
            $('#project_coordinator').html(options);
            $('#project_coordinator').trigger('change.select2');
        },
        error: function() {
            $('#project_coordinator').html('<option value="">Error loading coordinators</option>');
            $('#project_coordinator').trigger('change.select2');
        }
    });
}

function loadTeamMembers() {
    var department = $('#department').val();
    $('#team_members').html('<option value="">Loading...</option>');
    $('#team_members').trigger('change.select2');
    
    if (!department) {
        $('#team_members').html('<option value="">Select Team Member</option>');
        $('#team_members').trigger('change.select2');
        return;
    }
    
    $.ajax({
        url: '<?php echo base_url('kpi_reports/getTeamMembersByDept'); ?>',
        type: 'POST',
        data: { department: department },
        dataType: 'json',
        success: function(data) {
            var options = '<option value="">Select Team Member</option>';
            if (data && data.length > 0) {
                $.each(data, function(index, member) {
                    var selected = '';
                    // In edit mode, select the saved team member
                    if (isEditMode && editFeedbackData && editFeedbackData.team_members == member.empId) {
                        selected = ' selected';
                    }
                    var displayName = member.name;
                    if (member.emp_com_id) {
                        displayName += ' (' + member.emp_com_id + ')';
                    }
                    options += '<option value="' + member.empId + '"' + selected + '>' + displayName + '</option>';
                });
            }
            $('#team_members').html(options);
            $('#team_members').trigger('change.select2');
        },
        error: function() {
            $('#team_members').html('<option value="">Error loading team members</option>');
            $('#team_members').trigger('change.select2');
        }
    });
}
</script>

<style>
.required-star {
    color: red;
}
.form-group {
    margin-bottom: 20px;
}
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 4px;
}
.alert {
    margin-bottom: 20px;
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

<script>
$(document).ready(function() {
    // Initialize select2 for all dropdowns
    $('#department, #reporting_manager, #project_coordinator, #team_members, #feedback_for').select2({
        width: '100%'
    });
    
    // Initialize select2 for multiple selection improvement area
    $('#feedback_type').select2({
        width: '100%',
        placeholder: 'Select Improvement area(s)',
        allowClear: true
    });
    
    // Load initial data if department is pre-selected
    var dept = $('#department').val();
    if (dept) {
        loadReportingManagers();
        loadTeamMembers();
    }
    
    // Also check on department change if logged-in user should be auto-selected
    $('#department').on('change', function() {
        // Small delay to ensure managers are loaded before checking
        setTimeout(function() {
            if (loggedInEmpId && $('#reporting_manager option[value="' + loggedInEmpId + '"]').length > 0) {
                if ($('#reporting_manager').val() != loggedInEmpId && !isEditMode) {
                    $('#reporting_manager').val(loggedInEmpId).trigger('change.select2');
                    loadProjectCoordinators();
                }
            }
        }, 500);
    });
    
    // Sub-category add/remove functionality
    $('#addSubCategory').on('click', function() {
        var newItem = '<div class="sub-category-item" style="margin-bottom: 10px;">';
        newItem += '<input type="text" class="form-control sub-category-input" name="sub_categories[]" placeholder="Enter sub-category">';
        newItem += '<button type="button" class="btn btn-sm btn-danger remove-sub-category" style="margin-left: 5px;"><i class="fa fa-times"></i></button>';
        newItem += '</div>';
        $('#subCategoriesContainer').append(newItem);
    });
    
    $(document).on('click', '.remove-sub-category', function() {
        $(this).closest('.sub-category-item').remove();
    });
    
    // Disable submit button after form submission to prevent multiple submissions
    $('#feedbackForm').on('submit', function(e) {
        // Ensure Select2 multiple values are properly submitted
        var selectedTypes = $('#feedback_type').val();
        if (!selectedTypes || selectedTypes.length === 0) {
            e.preventDefault();
            alert('Please select at least one Improvement area.');
            return false;
        }
        
        // Debug: Log selected values before submit
        console.log('Selected feedback types:', selectedTypes);
        
        var $submitBtn = $('#submitFeedbackBtn');
        $submitBtn.prop('disabled', true);
        $submitBtn.css({
            'background-color': '#6c757d',
            'border-color': '#6c757d',
            'cursor': 'not-allowed',
            'opacity': '0.6'
        });
        var btnText = isEditMode ? 'Updating...' : 'Submitting...';
        $submitBtn.html('<i class="fa fa-spinner fa-spin"></i> ' + btnText);
    });
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
