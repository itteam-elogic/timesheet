<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<script type="text/javascript">
    setTimeout(function(){
        location.reload();
    }, 60000); // 60000 milliseconds = 1 minute
</script>

<?php
                    
//$key->department;
$mepManagers = ['146', '230', '149', '455'];
$arcManagers = ['41','394', '270', '47', '182', '71', '53', '155'];
$departments = ['Architectural', 'Structural', '3D Visualization'];
$monthValue  = date('n');                   
$empId = $this->session->userdata['logged_in_timesheet']['empId'];
 
if(!empty($this->session->userdata['logged_in_timesheet']['username'])): 
		    $userDetails =  $this->timesheet_login->user_information($this->session->userdata['logged_in_timesheet']['username']);
		    foreach($userDetails as $key) { }

endif;


?>




<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Reports</h1>
        </div>
    </div>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <div class="square-cards-container">
<div class="square-cards-grid">

    <!-- Card 1: Dashboard & Graphs -->
<!--
    <?php if (in_array($this->session->userdata['logged_in_timesheet']['user_type'], ['admin', 'business_head', 'manager'])): ?>
        <div class="square-card-wrapper">
            <a href="<?php echo base_url('kpi_reports/getMonthWiseEmpData_mep?month_id=' . $monthValue); ?>" class="square-card">
                <div class="square-card-content">
                    <div class="square-icon-box">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Dashboard & Graphs</h3>
                    <div class="square-hover-effect"></div>
                </div>
            </a>
        </div>
    <?php endif; ?>
-->

    <!-- Card 2: KPI Reports -->
    <?php
    // Check if user is a regular employee/team member (not admin, manager, business_head, developer)
    $user_type_index = $this->session->userdata['logged_in_timesheet']['user_type'];
    $is_regular_employee_index = !in_array($user_type_index, ['admin', 'superadmin', 'manager', 'business_head', 'developer']);
    ?>
    
    <?php if (!$is_regular_employee_index): ?>
        <div class="square-card-wrapper">
            <?php
            if (in_array($empId, $mepManagers)) {
                $link = base_url('kpi_reports/getMonthWiseEmpData_mep?month_id=' . $monthValue);
            } elseif (in_array($empId, $arcManagers)) {
                $link = base_url('kpi_reports/getMonthWiseEmpData?month_id=' . $monthValue);
            } elseif ($this->session->userdata['logged_in_timesheet']['user_type'] == 'developer' && in_array($key->department, $departments)) {
                $link = base_url('kpi_reports/getMonthWiseEmpData?month_id=' . $monthValue);
            } else {
                $link = base_url('kpi_reports/getMonthWiseEmpData?month_id=' . $monthValue);
            }
            ?>
            <a href="<?php echo $link; ?>" class="square-card">
                <div class="square-card-content">
                    <div class="square-icon-box">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <h3>KPI Reports</h3>
                    <div class="square-hover-effect"></div>
                </div>
            </a>
        </div>
    <?php endif; ?>

    <!-- Card 3: Client Reports -->
    <?php if (in_array($this->session->userdata['logged_in_timesheet']['user_type'], ['admin', 'business_head', 'manager'])): ?>
        <div class="square-card-wrapper">
            <a href="<?php echo base_url('kpi_reports/clientReport?month_id=' . $monthValue); ?>" class="square-card">
                <div class="square-card-content">
                    <div class="square-icon-box">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Client Reports</h3>
                    <div class="square-hover-effect"></div>
                </div>
            </a>
        </div>
    <?php endif; ?>

     <div class="square-card-wrapper">
        <a href="<?php echo base_url('clients/cs_reports'); ?>" class="square-card">
            <div class="square-card-content">
                <div class="square-icon-box">
                  <i class="fas fa-chart-line"></i>
                </div>
                <h3> Client TSR </h3>
                <div class="square-hover-effect"></div>
            </div>
        </a>
    </div>

      <div class="square-card-wrapper">
        <a href="<?php echo base_url(); ?>execution_plan" class="square-card">
            <div class="square-card-content">
                <div class="square-icon-box">
                  <i class="fas fa-cogs"></i>
                </div>
                <h3> Execution Plan</h3>
                <div class="square-hover-effect"></div>
            </div>
        </a>
    </div>
    
    <!-- Card 4: Feedback Reports (for managers/admins) -->
    <!-- Card 5: Feedback Grid View (for all employees) -->
      <div class="square-card-wrapper">
        <a href="<?php echo base_url('kpi_reports/feedbackReports'); ?>" class="square-card">
            <div class="square-card-content">
                <div class="square-icon-box">
                    <i class="fas fa-edit"></i>
                </div>
                <h3>Feedback</h3>
                <div class="square-hover-effect"></div>
            </div>
        </a>
    </div>



</div> <!-- .square-cards-grid -->

    </div> <!-- .square-cards-container -->
</div> <!-- .content-wrapper -->


<style>
.square-cards-container {
    display: flex;
    justify-content: center;
    align-items: flex-start; /* Pushes cards to top */
    min-height: 100vh;
    padding: 4rem 2rem 2rem; /* Extra top padding if needed */
    background-color: #f8f9fa;
}


.square-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 300px)); /* fixed max size */
    justify-content: center; /* center cards even if one */
    gap: 2rem;
    width: 100%;
    /* max-width: 1200px; */
}

    .square-card-wrapper {
        aspect-ratio: 1/1; /* Ensures perfect square */
    }

    .square-card {
        display: block;
        height: 100%;
        background: white;
        border-radius: 12px;
        text-decoration: none;
        color: #2b2d42;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .square-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    }

    .square-card-content {
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        position: relative;
        z-index: 2;
    }

    .square-icon-box {
        width: 70px;
        height: 70px;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
        transition: all 0.3s ease;
    }

    .square-card:hover .square-icon-box {
        transform: rotate(15deg);
        box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
    }

.square-card h3 {
    margin: 0;
    font-size: 2.5rem; /* Larger font */
    font-weight: 700;
    text-align: center;
    transition: all 0.3s ease;
}

    .square-card:hover h3 {
        color: #3a0ca3;
    }

    .square-hover-effect {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .square-card:hover .square-hover-effect {
        opacity: 1;
    }

    @media (max-width: 768px) {
        .square-cards-grid {
            grid-template-columns: 1fr;
            max-width: 350px;
        }
        
        .square-card-content {
            padding: 1.5rem;
        }
        
        .square-icon-box {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
    }
</style>

    <!-- Inlude Footer here -->
    <?php $this->load->view('includes/cRMFooter'); ?>
    <!-- Inlude Footer here END-->