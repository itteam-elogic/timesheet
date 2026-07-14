<?php
if (!isset($rs_vs_ts_base_path) || $rs_vs_ts_base_path === '') {
    $rs_vs_ts_base_path = 'rs_vs_ts';
}
$this->load->view('clients/resouce_vs_timehseet');
