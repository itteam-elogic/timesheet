<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LmsCategory extends CI_Controller {

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
		$this->load->helper('url');
		// Load form helper library
		$this->load->helper('form');
		// Load form validation library
		$this->load->library('form_validation');
		// Load session library
		$this->load->library('session');
		$this->load->library('excel'); // load excel library
		$this->load->helper('text');
		$this->load->helper('category');
		// Load database		
		$this->load->model('timesheet_login');

		$this->load->model('defaulter_model');
	    
        $this->load->model('lms_model');
         $this->load->library('pagination');
        
        $this->load->model('service_agreement_model');
				
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
	
	public function index(){  // Search Employee Lime Log
//       
	
        $member_emp_id = $this->session->userdata['logged_in_timesheet']['empId']; // Loged In
	    $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
        $search_term =  $this->input->post('search_term');
        
//        if(!empty($search_term)){
//            $data['search_term']  = $search_term;
//            $data['catArr']  = $this->lms_model->searchCategories($catId, $member_emp_id, $search_term);         
//
//        }else{
//                $data['catArr'] = $this->lms_model->get_categories_hierarchy($member_emp_id); 
//        }
        
        $config = array();
        $config["base_url"] = base_url() . "lmscategory/index";
        $config["total_rows"] = $this->lms_model->record_count();
        $config["per_page"] = 10;
        $config["uri_segment"] = 3;

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		// $data["categories"] = $this->lms_model->get_categories_hierarchy($config["per_page"], $page);
		// echo "<pre>";print_r($data['categories']);exit;
		$categories = $this->lms_model->getAllCategories();
    
		// Organize categories into a nested structure
		$nestedCategories = $this->buildNestedCategories($categories);
		
		// Pass nested categories to the view
		$data['categories'] = $nestedCategories;

		$data['categories_json'] = json_encode($nestedCategories);
		// echo "<pre>";print_r($data['categories']);exit;

        // $data["links"] = $this->pagination->create_links();

        $this->load->view('lms-category/index',$data);
        
	}

    public function buildNestedCategories($categories, $parentId = 0)
    {
        $nestedCategories = array();
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildNestedCategories($categories, $category['catId']);
                if ($children) {
                    $category['children'] = $children;
                }
				$category['text'] = $category['name'];
                $nestedCategories[] = $category;
            }
        }
        return $nestedCategories;
    }


    
    
    public function addCategory($lms_id = NULL){        
        $data['categories_hierarchy'] = $this->lms_model->get_categories_hierarchy();
//        echo "<pre>";print_r($data['categories_hierarchy']);exit;
        $this->load->view('lms-category/add_category', $data);
        
    }

	public function editCategory($catId){        
		$data['categories_details'] = $this->lms_model->getCategoryDetails($catId);
        $data['categories_hierarchy'] = $this->lms_model->get_categories_hierarchy();
		// echo "<pre>";print_r($data['categories_details']);exit;
        $this->load->view('lms-category/edit_category', $data);
        
    }

	public function getUpwardsParentCategoriesById(){
        $catId = $this->input->post('catId');
        $hierarchy = $this->lms_model->get_category_and_parents($catId);
		// echo "<pre>";print_r($hierarchy);exit;
        echo json_encode($hierarchy);
    }

	public function saveCategory(){
		$catId = NUll;

		if(!empty($this->input->post('catId'))){
			$data = array(
				'created_emp_id'	=> $this->session->userdata['logged_in_timesheet']['empId'],
				'parent_id'			=> $this->input->post('parent_id'),
				'name'				=> $this->input->post('name'),
				'updated_date'		=> date('Y-m-d H:i:s'),
			);
			$catId = $this->input->post('catId');
			$newCat = $this->lms_model->saveCategory($data, $catId);
		}else{
			$data = array(
				'created_emp_id'	=> $this->session->userdata['logged_in_timesheet']['empId'],
				'parent_id'			=> $this->input->post('parent_id'),
				'name'				=> $this->input->post('name'),
				'created_date'		=> date('Y-m-d H:i:s'),
			);
			$newCat = $this->lms_model->saveCategory($data);
		}
		$this->session->set_flashdata('success', 'Category has been successfully saved.');
		redirect('lmscategory/index');
	}
    
   

}