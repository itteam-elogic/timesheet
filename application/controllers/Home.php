<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	
	public function __construct() {
		
		parent::__construct();
		// Load form helper library
		$this->load->helper('form');
		// Load form validation library
		$this->load->library('form_validation');
		// Load session library
		$this->load->library('session');
		// Load database		
		$this->load->model('timesheet_login');
		
		$this->load->model('client_model');
		
		$this->load->model('project_model');
		
		$this->load->model('task_model');
		
		$this->load->model('emptimelog_model');
		
		$this->load->model('defaulter_model');
		
    }
	
	public function index(){
		
		if(!empty($this->session->userdata['logged_in_timesheet'])){
		
			$this->load->view('home');
			
		}else{
		
			$this->load->view('login');
			
		}	
			
	}
	
	
	
	// Check for user login process
	public function login() {
		
		$data = array(
				'username' => $this->input->post('username'),
				'password' => $this->input->post('password')
		);
		
		$result = $this->timesheet_login->login($data);
		
	if ($result == TRUE) {
		
		$username = $this->input->post('username');
		
		$result = $this->timesheet_login->user_information($username);
		
		if ($result != false) {
			$session_data = array(
			'empId' => $result[0]->empId,
			'name' => $result[0]->name,
			'username' => $result[0]->username,
			'user_type' => $result[0]->user_type,
			'email' => $result[0]->email,
			'designation' =>$result[0]->designation,
			);
			// Add user data in session
			$this->session->set_userdata('logged_in_timesheet', $session_data);
			
			 redirect('home', 'refresh');
		}
	}else{	
	
		$data = array(
			'error_message' => 'Invalid Username or Password'
		);
		   $this->load->view('login', $data);
	  }
	  
    }
    
	public function logout() {
	   
	    // Removing session data
			//$sess_array = array('username' => '');
			
			$this->session->unset_userdata('logged_in_timesheet');
			
			redirect('home', 'refresh');
	
    }
	



}
