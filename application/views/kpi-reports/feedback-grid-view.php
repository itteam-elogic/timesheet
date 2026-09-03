<?php $this->load->view('includes/cRMHeader'); ?>
<meta http-equiv="refresh" content="60">


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 15px 0;">
        <div>
            <h1 style="margin: 0; font-size: 28px; font-weight: 600; color: #333;">Feedback Grid View</h1>
        </div>
        <div>
            <a href="<?php echo base_url('kpi_reports/downloadFeedbackGridViewExcel?' . http_build_query($filters)); ?>" class="btn btn-success" style="padding: 10px 20px; font-weight: 600; border-radius: 4px; margin-right: 10px;">
                <i class="fa fa-download"></i> Download Excel
            </a>
            <a href="<?php echo base_url('kpi_reports/feedbackForm'); ?>" class="btn btn-primary" style="padding: 10px 20px; font-weight: 600; border-radius: 4px; margin-right: 10px;">
                <i class="fa fa-plus"></i> Submit New Feedback
            </a>
            <a href="<?php echo base_url('kpi_reports/feedbackReports'); ?>" class="btn btn-default" style="padding: 10px 20px; font-weight: 600; border-radius: 4px; background-color: #6c757d; color: white; border: none;">
                <i class="fa fa-list"></i> View Reports
            </a>
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

    <!-- Filters -->
    <div class="card mb-4" style="border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; padding: 15px 20px; border-radius: 8px 8px 0 0;">
            <h3 class="card-title" style="margin: 0; font-weight: 600; color: #333; font-size: 18px;">
                <i class="fa fa-filter" style="margin-right: 8px; color: #4361ee;"></i>Filters
            </h3>
        </div>
        <div class="card-body" style="padding: 25px;">
            <form method="get" action="<?php echo base_url('kpi_reports/feedbackGridView'); ?>" class="form-horizontal">
                <div class="row" style="display: flex; align-items: flex-start;">
                    <div class="col-md-3" style="padding-right: 15px; display: flex; flex-direction: column;">
                        <div class="form-group" style="margin-bottom: 20px; display: flex; flex-direction: column; flex: 1;">
                            <label style="font-weight: 600; margin-bottom: 8px; display: block; color: #333; min-height: 22px; line-height: 1.4;">From Date: <span class="required-star" style="color: red;">*</span></label>
                            <input type="date" name="from_date" class="form-control" value="<?php echo isset($filters['from_date']) ? $filters['from_date'] : ''; ?>" style="height: 40px; border-radius: 4px; border: 1px solid #ced4da; transition: border-color 0.3s; width: 100%;" required>
                        </div>
                    </div>
                    <div class="col-md-3" style="padding-left: 15px; padding-right: 15px; display: flex; flex-direction: column;">
                        <div class="form-group" style="margin-bottom: 20px; display: flex; flex-direction: column; flex: 1;">
                            <label style="font-weight: 600; margin-bottom: 8px; display: block; color: #333; min-height: 22px; line-height: 1.4;">To Date: <span class="required-star" style="color: red;">*</span></label>
                            <input type="date" name="to_date" class="form-control" value="<?php echo isset($filters['to_date']) ? $filters['to_date'] : ''; ?>" style="height: 40px; border-radius: 4px; border: 1px solid #ced4da; transition: border-color 0.3s; width: 100%;" required>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-left: 15px; display: flex; flex-direction: column;">
                        <div class="form-group" style="margin-bottom: 20px; display: flex; flex-direction: column; flex: 1;">
                            <label style="font-weight: 600; margin-bottom: 8px; display: block; color: #333; min-height: 22px; line-height: 1.4;">Reporting Manager / Team Member (comma separated):</label>
                            <input type="text" name="reporting_manager" id="reporting_manager" class="form-control" value="<?php echo isset($filters['reporting_manager']) ? $filters['reporting_manager'] : ''; ?>" placeholder="e.g., John Doe, Jane Smith, Bob Johnson" style="height: 40px; border-radius: 4px; border: 1px solid #ced4da; transition: border-color 0.3s; width: 100%;">
                            <small class="text-muted" style="font-size: 12px; color: #6c757d; margin-top: 5px; display: block; line-height: 1.4;">Start typing to see all active employees. Enter multiple names separated by commas</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 25px; font-weight: 600; border-radius: 4px; background-color: #4361ee; border: none; transition: background-color 0.3s;">
                            <i class="fa fa-filter" style="margin-right: 5px;"></i> APPLY FILTERS
                        </button>
                        <a href="<?php echo base_url('kpi_reports/feedbackGridView'); ?>" class="btn btn-default" style="padding: 10px 25px; font-weight: 600; border-radius: 4px; background-color: #6c757d; color: white; border: none; margin-left: 10px; transition: background-color 0.3s;">
                            <i class="fa fa-refresh" style="margin-right: 5px;"></i> CLEAR FILTERS
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Grid View by Month -->
    <?php if (!empty($grouped_feedback)): ?>
        <?php 
        $global_sno = 0; // Global counter across all months
        foreach ($grouped_feedback as $month => $feedback_list): ?>
            <?php 
            // Format month for display
            $month_date = DateTime::createFromFormat('Y-m', $month);
            $month_display = $month_date ? $month_date->format('F Y') : $month;
            ?>
            <div class="card mb-4" style="border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
                <div class="card-header" style="background-color: #4361ee; color: white; padding: 15px 20px; border-bottom: none;">
                    <h3 class="card-title" style="margin: 0; color: white; font-size: 18px; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                        <span>
                            <i class="fa fa-calendar" style="margin-right: 8px;"></i> <?php echo $month_display; ?>
                        </span>
                        <span class="badge" style="background-color: white; color: #4361ee; padding: 6px 12px; font-size: 14px; font-weight: 600; border-radius: 20px;">
                            <?php echo count($feedback_list); ?> Feedback<?php echo count($feedback_list) != 1 ? 's' : ''; ?>
                        </span>
                    </h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
                            <thead>
                                <tr style="background-color: #4361ee; color: white;">
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">SNO</th>
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Feedback Date</th>
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">From Date</th>
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">To Date</th>
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Department</th>
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Reporting Manager</th>
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Project Coordinator</th>
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Team Member</th>
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Type</th>
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Status</th>
                                    <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feedback_list as $feedback): ?>
                                    <?php $global_sno++; ?>
                                    <?php
                                    // Parse feedback_month to extract From Date and To Date
                                    $from_date_display = 'N/A';
                                    $to_date_display = 'N/A';
                                    
                                    if (!empty($feedback->feedback_month)) {
                                        // Format: "2026-JAN to 2026-Mar"
                                        if (preg_match('/(\d{4})-([A-Za-z]{3})\s+to\s+(\d{4})-([A-Za-z]{3})/i', $feedback->feedback_month, $matches)) {
                                            $from_year = $matches[1];
                                            $from_month_abbr = strtoupper($matches[2]);
                                            $to_year = $matches[3];
                                            $to_month_abbr = strtoupper($matches[4]);
                                            
                                            // Convert month abbreviation to number
                                            $month_map = array(
                                                'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
                                                'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08',
                                                'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'
                                            );
                                            
                                            if (isset($month_map[$from_month_abbr])) {
                                                $from_date_display = date('d M Y', strtotime($from_year . '-' . $month_map[$from_month_abbr] . '-01'));
                                            }
                                            
                                            if (isset($month_map[$to_month_abbr])) {
                                                // Get last day of the month
                                                $last_day = date('t', strtotime($to_year . '-' . $month_map[$to_month_abbr] . '-01'));
                                                $to_date_display = date('d M Y', strtotime($to_year . '-' . $month_map[$to_month_abbr] . '-' . $last_day));
                                            }
                                        }
                                    }
                                    ?>
                                    <tr style="transition: background-color 0.2s;">
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6; font-weight: 500;"><?php echo $global_sno; ?></td>
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo date('d M Y', strtotime($feedback->created_at)); ?></td>
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo $from_date_display; ?></td>
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo $to_date_display; ?></td>
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo $feedback->emp_department ? $feedback->emp_department : $feedback->department; ?></td>
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6; color: #1976d2; font-weight: 600;"><?php echo $feedback->reporting_manager_name ? $feedback->reporting_manager_name : 'N/A'; ?></td>
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo $feedback->project_coordinator_name ? $feedback->project_coordinator_name : 'N/A'; ?></td>
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo $feedback->team_member_name ? $feedback->team_member_name : 'N/A'; ?></td>
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;">
                                            <?php 
                                            // Handle multiple improvement areas stored as JSON
                                            $improvement_areas = array();
                                            if (!empty($feedback->feedback_type)) {
                                                // Try to decode as JSON first (for multiple selections)
                                                $decoded = json_decode($feedback->feedback_type, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    $improvement_areas = $decoded;
                                                } else {
                                                    // If not JSON, treat as single value (backward compatibility)
                                                    $improvement_areas = array($feedback->feedback_type);
                                                }
                                            }
                                            
                                            if (!empty($improvement_areas)): 
                                                foreach ($improvement_areas as $area): 
                                                    if (!empty(trim($area))):
                                            ?>
                                                <span class="badge badge-info" style="padding: 6px 12px; font-size: 12px; border-radius: 15px; background-color: #17a2b8; margin-right: 5px; margin-bottom: 5px; display: inline-block;">
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
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;">
                                            <span class="badge feedback-status-badge <?php echo $this->feedback_model->get_feedback_status_badge_class($feedback->status); ?>" style="padding: 6px 12px; font-size: 12px; border-radius: 15px;">
                                                <?php echo htmlspecialchars($this->feedback_model->get_feedback_status_label($feedback->status)); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;">
                                            <a href="<?php echo base_url('kpi_reports/viewFeedback/' . $feedback->feedback_id); ?>" class="btn btn-sm btn-info" title="View Details" style="padding: 6px 12px; border-radius: 4px; transition: all 0.3s;">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card" style="border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 40px;">
                <div class="alert alert-info" style="background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px 20px; border-radius: 6px; margin: 0;">
                    <i class="fa fa-info-circle" style="margin-right: 8px;"></i> No feedback found for the selected criteria.
                </div>
            </div>
        </div>
    <?php endif; ?>
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
.content-wrapper {
    padding: 20px;
    background-color: #f5f7fa;
    min-height: calc(100vh - 100px);
}
.card {
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
    transition: box-shadow 0.3s;
}
.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.page-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding: 15px 0;
}
.page-title h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
    color: #333;
}
.badge {
    display: inline-block;
    font-weight: 500;
}
.table {
    width: 100%;
    border-collapse: collapse;
}
.table thead tr {
    background-color: #4361ee !important;
    color: white !important;
}
.table tbody tr {
    transition: background-color 0.2s;
}
.table tbody tr:hover {
    background-color: #f0f4f8 !important;
    cursor: pointer;
}
.table tbody tr:nth-child(even) {
    background-color: #ffffff;
}
.table tbody tr:nth-child(odd) {
    background-color: #fafafa;
}
.form-control:focus {
    border-color: #4361ee;
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    outline: none;
}
.btn-primary:hover {
    background-color: #3651d4 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
}
.btn-default:hover {
    background-color: #5a6268 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
}
.btn-info:hover {
    background-color: #138496 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(23, 162, 184, 0.3);
}
.required-star {
    color: red;
    font-weight: bold;
}
.btn {
    border-radius: 4px;
    font-weight: 500;
    transition: all 0.3s;
}
.form-group label {
    color: #333;
    font-size: 14px;
}
.form-control {
    border: 1px solid #ced4da;
    border-radius: 4px;
    transition: border-color 0.3s, box-shadow 0.3s;
}
.alert {
    border-radius: 4px;
    margin-bottom: 20px;
}
/* Alignment for filter fields */
.form-horizontal .row {
    display: flex;
    align-items: flex-start;
}
.form-horizontal .form-group {
    display: flex;
    flex-direction: column;
    width: 100%;
}
.form-horizontal .form-group label {
    min-height: 22px;
    margin-bottom: 8px;
    display: block;
}
.form-horizontal input[type="date"],
.form-horizontal input[type="text"] {
    height: 40px;
    line-height: 1.5;
    padding: 6px 12px;
}
</style>

<script>
$(document).ready(function() {
    // Check if jQuery UI autocomplete is available
    if (typeof $.ui !== 'undefined' && $.ui.autocomplete) {
        // Helper function to get already selected values
        function getSelectedValues() {
            var inputVal = $("#reporting_manager").val();
            if (!inputVal || inputVal.trim() === '') {
                return [];
            }
            var terms = inputVal.split(',').map(function(term) {
                return term.trim();
            }).filter(function(term) {
                return term.length > 0;
            });
            return terms;
        }
        
        // Helper function to check if a value is already selected
        function isAlreadySelected(value) {
            var selected = getSelectedValues();
            return selected.indexOf(value.trim()) !== -1;
        }
        
        // Initialize autocomplete with source function
        $("#reporting_manager").autocomplete({
            minLength: 2,
            delay: 300,
            source: function(request, response) {
                var inputVal = $("#reporting_manager").val();
                var allParts = inputVal.split(',');
                var allTerms = allParts.map(function(term) {
                    return term.trim();
                }).filter(function(term) {
                    return term.length > 0; // Filter out empty terms (like trailing comma)
                });
                
                // Get the last term (what user is currently typing)
                var activeSearchTerm = allTerms.length > 0 ? allTerms[allTerms.length - 1] : '';
                
                // If active search term has less than 2 characters, don't search
                if (activeSearchTerm.length < 2) {
                    response([]);
                    return;
                }
                
                // Get already confirmed selected values (all terms except the last one)
                var confirmedSelected = [];
                if (allTerms.length > 1) {
                    confirmedSelected = allTerms.slice(0, -1).filter(function(term) {
                        return term.length > 0;
                    });
                }
                
                // Determine which terms to search with
                // Search with all terms that are not already confirmed as selected
                // This allows searching with multiple terms like "John, Jane" to find results matching either
                var searchTerms = [];
                
                // If there are multiple terms, search with all of them (except if they're already confirmed selected)
                if (allTerms.length > 1) {
                    // Search with all terms that might be search queries
                    // The last term is definitely a search query
                    // Previous terms might be search queries if they're not exact matches of selected values
                    searchTerms.push(activeSearchTerm);
                    
                    // Also include previous terms if they're not in confirmed selected (they might be partial searches)
                    for (var i = 0; i < allTerms.length - 1; i++) {
                        var term = allTerms[i];
                        if (term.length >= 2 && !isAlreadySelected(term)) {
                            searchTerms.push(term);
                        }
                    }
                } else {
                    // Single term search
                    searchTerms = [activeSearchTerm];
                }
                
                // Remove duplicates from search terms
                var uniqueSearchTerms = [];
                var seenTerms = {};
                searchTerms.forEach(function(term) {
                    if (!seenTerms[term]) {
                        seenTerms[term] = true;
                        uniqueSearchTerms.push(term);
                    }
                });
                
                if (uniqueSearchTerms.length === 0) {
                    response([]);
                    return;
                }
                
                // Search with all unique terms in parallel
                var searchPromises = uniqueSearchTerms.map(function(searchTerm) {
                    return $.ajax({
                        url: "<?php echo base_url('kpi_reports/autosuggest_reporting_managers'); ?>",
                        dataType: "json",
                        type: "GET",
                        data: {
                            term: searchTerm
                        }
                    });
                });
                
                // Execute all searches in parallel
                $.when.apply($, searchPromises).then(function() {
                    try {
                        var allSuggestions = [];
                        var seenValues = {};
                        
                        // Process all search results
                        for (var i = 0; i < arguments.length; i++) {
                            var data = arguments[i];
                            var suggestions = [];
                            
                            // Handle different response formats
                            if (Array.isArray(data)) {
                                suggestions = data;
                            } else if (data && Array.isArray(data[0])) {
                                suggestions = data[0];
                            } else if (typeof data === 'string') {
                                try {
                                    var parsed = JSON.parse(data);
                                    suggestions = Array.isArray(parsed) ? parsed : [];
                                } catch (e) {
                                    suggestions = [];
                                }
                            }
                            
                            // Add unique suggestions
                            suggestions.forEach(function(item) {
                                var value = typeof item === 'string' ? item : (item.value || item.label || item);
                                var normalizedValue = value.trim();
                                
                                // Skip if already seen or already in confirmed selected
                                if (!seenValues[normalizedValue]) {
                                    var isInConfirmed = confirmedSelected.some(function(sel) {
                                        return sel.toLowerCase() === normalizedValue.toLowerCase();
                                    });
                                    
                                    if (!isInConfirmed) {
                                        seenValues[normalizedValue] = true;
                                        allSuggestions.push(item);
                                    }
                                }
                            });
                        }
                        
                        response(allSuggestions);
                    } catch (e) {
                        console.log("Error processing search results: " + e);
                        response([]);
                    }
                }).fail(function(xhr, status, error) {
                    console.log("Autocomplete AJAX error: " + error);
                    console.log("Status: " + status);
                    response([]);
                });
            },
            select: function(event, ui) {
                event.preventDefault();
                var currentVal = $(this).val();
                var selectedValue = typeof ui.item === 'string' ? ui.item : (ui.item.value || ui.item.label || ui.item);
                
                // Check if already selected
                if (isAlreadySelected(selectedValue)) {
                    return false;
                }
                
                // Get existing terms
                var terms = getSelectedValues();
                
                // Add the new selected value
                terms.push(selectedValue);
                
                // Update the input value with comma and space at the end for easy continuation
                $(this).val(terms.join(', ') + ', ');
                
                // Set cursor position at the end to allow immediate typing
                var input = this;
                setTimeout(function() {
                    input.focus();
                    var len = input.value.length;
                    input.setSelectionRange(len, len);
                }, 10);
                
                return false;
            },
            focus: function(event, ui) {
                event.preventDefault();
            }
        });
        
        // Handle input to allow typing after comma
        $("#reporting_manager").on('keydown', function(e) {
            // Allow backspace to remove last selected value
            if (e.keyCode === 8) { // Backspace
                var cursorPos = this.selectionStart;
                var val = $(this).val();
                
                // If cursor is at the start or after a comma+space, remove the previous term
                if (cursorPos === 0) {
                    return; // Allow normal backspace
                }
                
                // Check if we're at the start of a term (after ", ")
                var beforeCursor = val.substring(0, cursorPos);
                if (beforeCursor.endsWith(', ')) {
                    e.preventDefault();
                    var terms = getSelectedValues();
                    if (terms.length > 0) {
                        terms.pop(); // Remove last term
                        $(this).val(terms.length > 0 ? terms.join(', ') : '');
                    }
                }
            }
        });
    } else {
        console.error("jQuery UI Autocomplete is not loaded!");
    }
});
</script>

<style>
.ui-autocomplete {
    max-height: 220px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    background-color: #ffffff !important;
    border: 1px solid #ccc !important;
    border-radius: 4px !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1) !important;
    font-size: 14px !important;
    font-family: 'Segoe UI', Tahoma, sans-serif !important;
    padding: 4px 0 !important;
    z-index: 1050 !important;
}
.ui-menu-item-wrapper {
    padding: 8px 12px !important;
    cursor: pointer !important;
}
.ui-menu-item-wrapper:hover {
    background-color: #f0f0f0 !important;
}
.ui-state-active {
    background-color: #e3f2fd !important;
    border: none !important;
}
</style>

<?php $this->load->view('includes/cRMFooter'); ?>

