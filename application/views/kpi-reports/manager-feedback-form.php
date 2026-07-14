<?php $this->load->view('includes/cRMHeader'); ?>

<div class="content-wrapper">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 15px 0;">
        <div>
            <h1 style="margin: 0; font-size: 28px; font-weight: 600; color: #333;">Give Feedback to Employee</h1>
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

            <?php echo form_open_multipart('kpi_reports/submitManagerFeedback', array('class' => 'form-horizontal', 'id' => 'managerFeedbackForm')); ?>
            
            <div class="form-group">
                <label class="control-label col-md-3">Employee: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <select class="form-control" name="employee_id" id="employee_id" required>
                        <option value="">Select Employee</option>
                        <?php if (!empty($team_members)): ?>
                            <?php foreach ($team_members as $member): ?>
                                <option value="<?php echo $member->empId; ?>">
                                    <?php echo $member->name; ?>
                                    <?php if ($member->emp_com_id): ?>
                                        (<?php echo $member->emp_com_id; ?>)
                                    <?php endif; ?>
                                    <?php if ($member->designation): ?>
                                        - <?php echo $member->designation; ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php echo form_error('employee_id', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Feedback Month: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label" style="font-weight: normal; margin-bottom: 5px;">From Month:</label>
                            <input type="month" class="form-control" name="feedback_month_from" id="feedback_month_from" value="<?php echo date('Y-m'); ?>" required>
                            <?php echo form_error('feedback_month_from', '<label class="error">', '</label>'); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label" style="font-weight: normal; margin-bottom: 5px;">To Month:</label>
                            <input type="month" class="form-control" name="feedback_month_to" id="feedback_month_to" value="<?php echo date('Y-m'); ?>" required>
                            <?php echo form_error('feedback_month_to', '<label class="error">', '</label>'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Improvement area: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <select class="form-control" name="feedback_type[]" id="feedback_type" multiple="multiple" required>
                        <option value="Productivity & Efficiency">Productivity & Efficiency</option>
                        <option value="Quality Improvement">Quality Improvement</option>
                        <option value="Technical Knowledge & Skill Development">Technical Knowledge & Skill Development</option>
                        <option value="Ownership & Accountability">Ownership & Accountability</option>
                        <option value="Innovation">Innovation</option>
                        <option value="Communication & Coordination">Communication & Coordination</option>
                    </select>
                    <small class="text-muted" style="display: block; margin-top: 5px;">Hold Ctrl (or Cmd on Mac) to select multiple options</small>
                    <?php echo form_error('feedback_type', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Feedback for: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <select class="form-control" name="feedback_for" id="feedback_for" required>
                        <option value="">Select Feedback Type</option>
                        <option value="Monthly KPI Review">Monthly KPI Review</option>
                        <option value="General Feedback">General Feedback</option>
                    </select>
                    <?php echo form_error('feedback_for', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Strengths & Achievements:</label>
                <div class="col-md-6">
                    <textarea class="form-control" name="strengths_achievements" id="strengths_achievements" rows="6" placeholder="Please provide strengths and achievements..."></textarea>
                    <?php echo form_error('strengths_achievements', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Feedback for improvement: <span class="required-star">*</span></label>
                <div class="col-md-6">
                    <textarea class="form-control" name="feedback_message" id="feedback_message" rows="6" placeholder="Please provide your detailed feedback..." required></textarea>
                    <?php echo form_error('feedback_message', '<label class="error">', '</label>'); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Attach File:</label>
                <div class="col-md-6">
                    <input type="file" class="form-control" name="attached_file" id="attached_file" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                    <small class="text-muted">Allowed formats: jpg, jpeg, png, gif, pdf, doc, docx, xls, xlsx, txt (Max: 10MB)</small>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-offset-3 col-md-6">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 25px; font-weight: 600; border-radius: 4px;">
                        <i class="fa fa-paper-plane"></i> Submit Feedback
                    </button>
                </div>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>

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
</style>

<script>
$(document).ready(function() {
    // Initialize select2 for dropdowns
    $('#employee_id, #feedback_for').select2({
        width: '100%'
    });
    
    // Initialize select2 for multiple selection improvement area
    $('#feedback_type').select2({
        width: '100%',
        placeholder: 'Select Improvement area(s)',
        allowClear: true
    });
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>

