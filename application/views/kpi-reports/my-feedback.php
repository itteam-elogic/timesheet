<?php $this->load->view('includes/cRMHeader'); ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 15px 0;">
        <div>
            <h1 style="margin: 0; font-size: 28px; font-weight: 600; color: #333;">My Feedback</h1>
        </div>
    </div>

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

    <div class="card">
        <div class="card-body">
            <?php if (!empty($feedback_list)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" style="width: 100%; border-collapse: collapse;">
                        <thead style="background-color: #4361ee; color: white;">
                            <tr>
                                <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Feedback Month</th>
                                <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Improvement Area</th>
                                <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Feedback For</th>
                                <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Given By</th>
                                <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Status</th>
                                <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Date</th>
                                <th style="padding: 12px; text-align: center; border: 1px solid #dee2e6;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feedback_list as $feedback): ?>
                                <tr>
                                    <td style="padding: 12px; border: 1px solid #dee2e6;">
                                        <?php echo htmlspecialchars($this->feedback_model->format_feedback_month_display($feedback->feedback_month, $feedback->created_at)); ?>
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #dee2e6;">
                                        <?php 
                                        // Handle multiple improvement areas stored as JSON
                                        $improvement_areas = array();
                                        if (!empty($feedback->feedback_type)) {
                                            $decoded = json_decode($feedback->feedback_type, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                $improvement_areas = array_filter($decoded);
                                            } else {
                                                $improvement_areas = array($feedback->feedback_type);
                                            }
                                        }
                                        
                                        if (!empty($improvement_areas)): 
                                            foreach ($improvement_areas as $area): 
                                                if (!empty(trim($area))):
                                        ?>
                                            <span class="badge badge-info" style="padding: 5px 10px; font-size: 12px; border-radius: 4px; background-color: #17a2b8; margin-right: 5px; margin-bottom: 5px; display: inline-block;">
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
                                    <td style="padding: 12px; border: 1px solid #dee2e6;">
                                        <?php echo $feedback->feedback_for ? $feedback->feedback_for : 'N/A'; ?>
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #dee2e6;">
                                        <?php 
                                        // If reporting_manager exists and is different from the employee, it means manager gave feedback
                                        if ($feedback->reporting_manager && $feedback->reporting_manager != $feedback->empId) {
                                            echo $feedback->reporting_manager_name ? $feedback->reporting_manager_name : 'Manager';
                                        } else {
                                            echo 'Self';
                                        }
                                        ?>
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #dee2e6;">
                                        <span class="badge feedback-status-badge <?php echo $this->feedback_model->get_feedback_status_badge_class($feedback->status); ?>" style="padding: 5px 10px; border-radius: 4px;">
                                            <?php echo htmlspecialchars($this->feedback_model->get_feedback_status_label($feedback->status)); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #dee2e6;">
                                        <?php echo date('d M Y', strtotime($feedback->created_at)); ?>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;">
                                        <a href="<?php echo base_url('kpi_reports/viewFeedback/' . $feedback->feedback_id); ?>" class="btn btn-sm btn-info" title="View Details" style="padding: 6px 12px; border-radius: 4px;">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center" style="padding: 40px; color: #999;">
                    <i class="fa fa-comments" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p style="font-size: 16px; font-style: italic;">No feedback found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.feedback-status-badge {
    font-weight: 600;
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
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 4px;
}
.table {
    margin-bottom: 0;
}
.badge-warning {
    background-color: #ffc107;
    color: #000;
}
.badge-success {
    background-color: #28a745;
    color: #fff;
}
</style>

<?php $this->load->view('includes/cRMFooter'); ?>

