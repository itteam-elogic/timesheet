<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emp_record_inactivity extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('timesheet_login');
        $this->load->model('emp_record_inactivity_model');

        if (empty($this->session->userdata['logged_in_timesheet'])) {
            redirect('home/login');
        }
    }

    public function index() {
        $data = $this->buildViewData($this->buildFiltersFromRequest());
        $this->load->view('emp_record_inactivity/inactivity_report', $data);
    }

    public function search() {
        $data = $this->buildViewData($this->buildFiltersFromRequest());
        $this->load->view('emp_record_inactivity/inactivity_report', $data);
    }

    public function downloadExcel() {
        $filters = $this->buildFiltersFromRequest();
        $records = $this->emp_record_inactivity_model->getInactiveTimesheetRecords($filters);

        $this->load->library('excel');
        $objPHPExcel = $this->excel;
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $sheet->setTitle('Inactivity Report');

        $lastCol = 'J';
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'Timesheet Inactivity Report');
        $sheet->getStyle('A1')->applyFromArray(array(
            'font' => array('bold' => true, 'size' => 16, 'color' => array('rgb' => '1F5076')),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ));
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', $this->buildInactivityExportFilterSummary($filters));
        $sheet->getStyle('A2')->applyFromArray(array(
            'font' => array('size' => 10, 'color' => array('rgb' => '5A6A7A')),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        ));
        $sheet->getRowDimension(2)->setRowHeight(22);

        $headerRow = 4;
        $headers = array(
            'S.No', 'Client', 'Project', 'Project ID', 'Department', 'Status',
            'Start Date', 'End Date', 'Last Log Date', 'Days Inactive',
        );
        $sheet->fromArray($headers, null, 'A' . $headerRow);

        $headerStyle = array(
            'font' => array('bold' => true, 'size' => 11, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '337AB7'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '2C5AA0'),
                ),
            ),
        );
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(24);

        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(34);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(14);
        $sheet->getColumnDimension('I')->setWidth(16);
        $sheet->getColumnDimension('J')->setWidth(14);

        $dataBorder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 'DEE2E6'),
                ),
            ),
            'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
        );
        $rowAltFill = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F8FAFC'),
            ),
        );

        $rowNum = $headerRow + 1;
        $i = 1;
        foreach ($records as $row) {
            $daysInactive = ($row->days_since_last_entry !== null && $row->days_since_last_entry !== '')
                ? (int)$row->days_since_last_entry
                : null;
            $dept = !empty($row->department) ? $row->department : '-';
            $statusLabel = $this->formatInactivityStatusLabel($row->project_status);
            $lastLog = !empty($row->emp_report_dates)
                ? date('d-M-Y', strtotime($row->emp_report_dates))
                : 'Never';

            $sheet->setCellValue('A' . $rowNum, $i);
            $sheet->setCellValue('B' . $rowNum, (string)$row->client_name);
            $sheet->setCellValue('C' . $rowNum, (string)$row->project_name);
            $sheet->setCellValue('D' . $rowNum, (int)$row->project_Id);
            $sheet->setCellValue('E' . $rowNum, $dept);
            $sheet->setCellValue('F' . $rowNum, $statusLabel);
            $sheet->setCellValue('G' . $rowNum, $this->formatInactivityExportDate(isset($row->project_start_date) ? $row->project_start_date : ''));
            $sheet->setCellValue('H' . $rowNum, $this->formatInactivityExportDate(isset($row->project_end_date) ? $row->project_end_date : ''));
            $sheet->setCellValue('I' . $rowNum, $lastLog);
            $sheet->setCellValue('J' . $rowNum, $daysInactive !== null ? $daysInactive : '-');

            $sheet->getStyle('A' . $rowNum . ':' . $lastCol . $rowNum)->applyFromArray($dataBorder);
            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $rowNum . ':' . $lastCol . $rowNum)->applyFromArray($rowAltFill);
            }

            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $rowNum . ':I' . $rowNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $rowNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $rowNum . ':C' . $rowNum)->getAlignment()->setWrapText(true);

            $statusStyle = $this->getInactivityStatusExcelStyle($row->project_status);
            if ($statusStyle !== null) {
                $sheet->getStyle('F' . $rowNum)->applyFromArray($statusStyle);
            }
            $sheet->getStyle('F' . $rowNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            if ($lastLog === 'Never') {
                $sheet->getStyle('I' . $rowNum)->applyFromArray(array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFE8CC'),
                    ),
                    'font' => array('bold' => true, 'color' => array('rgb' => '9A5200')),
                ));
            }

            $daysStyle = $this->getInactivityDaysExcelStyle($daysInactive);
            if ($daysStyle !== null) {
                $sheet->getStyle('J' . $rowNum)->applyFromArray($daysStyle);
            }

            $rowNum++;
            $i++;
        }

        if ($i === 1) {
            $sheet->mergeCells('A' . $rowNum . ':' . $lastCol . $rowNum);
            $sheet->setCellValue('A' . $rowNum, 'No inactive records found for the selected filters.');
            $sheet->getStyle('A' . $rowNum)->applyFromArray(array(
                'font' => array('italic' => true, 'color' => array('rgb' => '6C757D')),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            ));
        } else {
            $sheet->freezePane('A' . ($headerRow + 1));
        }

        $filename = 'timesheet_inactivity_report_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    private function buildInactivityExportFilterSummary($filters) {
        $parts = array();
        $fromTs = !empty($filters['from_date']) ? strtotime($filters['from_date']) : false;
        $toTs = !empty($filters['to_date']) ? strtotime($filters['to_date']) : false;
        if ($fromTs && $toTs) {
            $parts[] = 'Period: ' . date('d-M-Y', $fromTs) . ' to ' . date('d-M-Y', $toTs);
        }
        if (!empty($filters['department'])) {
            $parts[] = 'Department: ' . $filters['department'];
        }
        if (!empty($filters['client_Id'])) {
            $parts[] = 'Client ID: ' . $filters['client_Id'];
        }
        if (!empty($filters['project_Id'])) {
            $parts[] = 'Project ID: ' . $filters['project_Id'];
        }
        if (!empty($filters['project_status'])) {
            $parts[] = 'Status: ' . $this->formatInactivityStatusLabel($filters['project_status']);
        }
        $parts[] = 'Generated: ' . date('d-M-Y H:i');
        return implode('  |  ', $parts);
    }

    private function getInactivityStatusExcelStyle($status) {
        $key = strtolower(trim((string)$status));
        if ($key === 'process') {
            return array(
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'D4EDDA')),
                'font' => array('bold' => true, 'color' => array('rgb' => '155724')),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            );
        }
        if ($key === 'closed') {
            return array(
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'F8D7DA')),
                'font' => array('bold' => true, 'color' => array('rgb' => '721C24')),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            );
        }
        if (strpos($key, 'hold') !== false) {
            return array(
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFF3CD')),
                'font' => array('bold' => true, 'color' => array('rgb' => '856404')),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            );
        }
        if ($status === '' || $status === '—') {
            return null;
        }
        return array(
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E9ECEF')),
            'font' => array('color' => array('rgb' => '495057')),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        );
    }

    private function getInactivityDaysExcelStyle($daysInactive) {
        if ($daysInactive === null) {
            return null;
        }
        if ($daysInactive >= 180) {
            return array(
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFCDD2')),
                'font' => array('bold' => true, 'color' => array('rgb' => 'B71C1C')),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            );
        }
        if ($daysInactive >= 90) {
            return array(
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFE0B2')),
                'font' => array('bold' => true, 'color' => array('rgb' => 'BF360C')),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            );
        }
        return array(
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFF3CD')),
            'font' => array('bold' => true, 'color' => array('rgb' => '856404')),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        );
    }

    private function formatInactivityExportDate($dateValue) {
        if (empty($dateValue) || $dateValue === '0000-00-00' || $dateValue === '0000-00-00 00:00:00') {
            return '-';
        }
        $timestamp = strtotime($dateValue);
        return ($timestamp !== false) ? date('d-M-Y', $timestamp) : '-';
    }

    private function formatInactivityStatusLabel($status) {
        $key = strtolower(trim((string)$status));
        if ($key === 'process') {
            return 'In Process';
        }
        if ($status === '' || $status === '—') {
            return '-';
        }
        return (string)$status;
    }

    public function getProjectsByClient() {
        $clientId = trim((string)$this->input->post('client_Id'));
        $selectedProjectId = trim((string)$this->input->post('project_Id'));
        $projects = $this->emp_record_inactivity_model->getProjectsByClient($clientId);

        echo '<option value="">All Projects</option>';
        foreach ($projects as $project) {
            $pid = (int)$project->project_Id;
            $selected = ($selectedProjectId !== '' && (int)$selectedProjectId === $pid) ? ' selected' : '';
            echo '<option value="' . $pid . '"' . $selected . '>'
                . htmlspecialchars($project->project_name, ENT_QUOTES, 'UTF-8')
                . '</option>';
        }
    }

    private function buildFiltersFromRequest() {
        $fromYear = $this->input->get_post('from_year');
        $fromMonth = $this->input->get_post('from_month');
        $toYear = $this->input->get_post('to_year');
        $toMonth = $this->input->get_post('to_month');

        $resolved = $this->emp_record_inactivity_model->resolveYearMonthDateRange(
            $fromYear,
            $fromMonth,
            $toYear,
            $toMonth
        );

        return array(
            'from_date' => $resolved['from_date'],
            'to_date' => $resolved['to_date'],
            'from_year' => $resolved['from_year'],
            'from_month' => $resolved['from_month'],
            'to_year' => $resolved['to_year'],
            'to_month' => $resolved['to_month'],
            'department' => trim((string)$this->input->get_post('department')),
            'client_Id' => trim((string)$this->input->get_post('client_Id')),
            'project_Id' => trim((string)$this->input->get_post('project_Id')),
            'project_status' => trim((string)$this->input->get_post('project_status')),
        );
    }

    private function buildViewData($filters) {
        $clientId = isset($filters['client_Id']) ? $filters['client_Id'] : '';
        $dropdowns = $this->emp_record_inactivity_model->getFilterDropdownData($clientId);

        return array(
            'records' => $this->emp_record_inactivity_model->getInactiveTimesheetRecords($filters),
            'filters' => $filters,
            'clients' => $dropdowns['clients'],
            'projects' => $dropdowns['projects'],
            'departments' => $dropdowns['departments'],
            'projectStatuses' => $dropdowns['projectStatuses'],
            'canCloseProjects' => $this->canCloseProcessProjects(),
        );
    }

    public function closeSelectedProcessProjects() {
        $this->updateSelectedProjectStatus('close');
    }

    public function reopenSelectedProcessProjects() {
        $this->updateSelectedProjectStatus('reopen');
    }

    public function toggleProjectStatus() {
        if (!$this->canCloseProcessProjects()) {
            $this->jsonResponse(false, 'You are not authorized to update project status.');
            return;
        }

        $projectId = (int)$this->input->post('project_id');
        $currentStatus = strtolower(trim((string)$this->input->post('current_status')));

        if ($projectId <= 0) {
            $this->jsonResponse(false, 'Invalid project selected.');
            return;
        }

        if ($currentStatus === 'process') {
            $newStatus = 'Closed';
        } elseif ($currentStatus === 'closed') {
            $newStatus = 'Process';
        } else {
            $this->jsonResponse(false, 'Only Process or Closed status can be toggled.');
            return;
        }

        $updated = $this->emp_record_inactivity_model->updateProjectStatus($projectId, $newStatus, $currentStatus === 'process' ? 'Process' : 'Closed');
        if ($updated) {
            $label = ($newStatus === 'Process') ? 'In Process' : 'Closed';
            $this->jsonResponse(true, 'Project status updated to ' . $label . '.', array(
                'project_id' => $projectId,
                'new_status' => $newStatus,
                'display_status' => $newStatus,
            ));
            return;
        }

        $this->jsonResponse(false, 'Project status was not updated. Please refresh and try again.');
    }

    private function updateSelectedProjectStatus($action) {
        if (!$this->canCloseProcessProjects()) {
            $this->jsonResponse(false, 'You are not authorized to update project status.');
            return;
        }

        $fromStatus = ($action === 'close') ? 'Process' : 'Closed';
        $toStatus = ($action === 'close') ? 'Closed' : 'Process';
        $projectIds = $this->normalizeProjectIds($this->input->post('project_ids'));

        if (empty($projectIds)) {
            $this->jsonResponse(false, 'Please select at least one project.');
            return;
        }

        $updatedCount = $this->emp_record_inactivity_model->updateProjectsStatusByIds($projectIds, $fromStatus, $toStatus);
        $label = ($toStatus === 'Process') ? 'In Process' : 'Closed';

        $this->jsonResponse(true, $updatedCount > 0
            ? ($updatedCount . ' project(s) status updated to ' . $label . '.')
            : ('No ' . $fromStatus . ' projects were updated. Selected projects may already be ' . $toStatus . '.'), array(
            'updated' => $updatedCount,
        ));
    }

    private function normalizeProjectIds($rawProjectIds) {
        $projectIds = array();
        if (!is_array($rawProjectIds)) {
            return $projectIds;
        }
        foreach ($rawProjectIds as $rawId) {
            $rawId = (int)$rawId;
            if ($rawId > 0) {
                $projectIds[] = $rawId;
            }
        }
        return array_values(array_unique($projectIds));
    }

    private function jsonResponse($success, $message, $extra = array()) {
        $payload = array_merge(array(
            'success' => (bool)$success,
            'message' => $message,
        ), $extra);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    private function canCloseProcessProjects() {
        $userType = isset($this->session->userdata['logged_in_timesheet']['user_type'])
            ? $this->session->userdata['logged_in_timesheet']['user_type']
            : '';
        return in_array($userType, array('admin', 'superadmin', 'business_head', 'manager'), true);
    }
}
