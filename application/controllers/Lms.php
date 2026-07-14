<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lms extends CI_Controller {

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
		$this->load->library('excel'); // load excel library
		$this->load->helper('text');
		
		// Load database		
		$this->load->model('timesheet_login');
	    
        $this->load->model('notification_model');
        $this->load->model('lms_model');
        
        $this->load->model('service_agreement_model');
				
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
	
	public function index($catId=null){  // Search Employee Lime Log
	
        $member_emp_id = $this->session->userdata['logged_in_timesheet']['empId']; // Loged In
	    $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
        $search_term =  $this->input->post('search_term');
        if($userType == 'developer' || $userType == 'manager' || $userType == 'business_head' ||  $userType == 'admin') : 
        $data['catInfo'] = $this->lms_model->getCategoryDetails($catId); 
        $data['subCategories'] = $this->lms_model->getFirstLevelSubCategories($catId); 
        $data['breadcrumbs'] = $this->lms_model->get_category_and_parents($catId);
        endif;
        
        if(!empty($search_term)){
            $data['search_term']  = $search_term;
            $data['getLmsVideos']  = $this->lms_model->searchVideos($catId, $member_emp_id, $search_term);         

        }else{
            if($userType == 'developer' || $userType == 'manager' || $userType == 'business_head' || $userType == 'admin') : 
            $data['getLmsVideos'] = $this->lms_model->listOfLmsVideosByCategory($catId, $member_emp_id); 
            else:
//            echo "<pre>";print_r($data['catInfo']);    exit;
//            echo "<pre>";print_r($data['breadcrumbs']);exit;
                $data['getLmsVideos'] = $this->lms_model->listOfLmsVideos($member_emp_id); 
            endif;
//            exit;

        }
        
            
            // echo "<pre>";print_r($this->session->userdata['logged_in_timesheet']);exit;
            if($userType == 'developer' || $userType == 'manager' || $userType == 'business_head' || $userType == 'admin') : 
                // $data['assignments'] = $this->lms_model->getAllAssignments($member_emp_id);
          
                if($member_emp_id == '421' ||  $member_emp_id == '1' ):
		
						$data['getLmsVideos'] = $this->lms_model->listOfLmsVideos($member_emp_id);	
					
						$this->load->view('learning_management_system/list_of_learning_videos',$data);
			     else:
		
					$this->load->view('learning_management_system/videos_by_category',$data);
			endif;
		
		   else:
                
                $this->load->view('learning_management_system/list_of_learning_videos',$data);
        
            endif;
	}
    
    
    public function add($lms_id = NULL){        
        $data['categories_hierarchy'] = $this->lms_model->get_categories_hierarchy();
//        echo "<pre>";print_r($data);exit;
        if(empty($lms_id)) : 
                    
			     $this->load->view('learning_management_system/add_learning_video', $data);
			 
		else:
			 
              $data['viewdetails'] = $this->lms_model->getVideoDetails($lms_id);
//               echo "<pre>";print_r($data['viewdetails']);exit;    
			  $this->load->view('learning_management_system/edit_learning_video', $data);
					
		endif;	
                
        
    }
    
    
    public function viewInformation($video_id){
        
        if(!empty($video_id)):
        
            $data['viewdetails'] = $this->lms_model->getVideoDetails($video_id);
            $data['video_report'] = $this->lms_model->getVideoReport($video_id);
            // echo "<pre>"; print_r($data);
            // exit;
            
            $this->load->view('learning_management_system/view_learning_video' , $data);
        
        endif;
        
    }

    public function assignVideoToEmployee($video_id){
        
        if(!empty($video_id)):
        
            $data['viewdetails'] = $this->lms_model->getVideoDetails($video_id);
            $data['video_report'] = $this->lms_model->getVideoReport($video_id);
            // echo "<pre>"; print_r($data);
            // exit;
            $data['employees'] = $this->lms_model->getAllEmployees();
            $data['assignments'] = $this->lms_model->getEmployeeAssignments($video_id);
            // echo "<pre>"; print_r($data['assignments']);
            // exit;
            $data['videoInfo'] = $data['viewdetails'][0];
            $this->load->view('learning_management_system/assign_videos' , $data);
        endif;
        
    }

    public function assignVideo() {
        // Get form data
        $videoId = $this->input->post('video_id');
        $completionDate = $this->input->post('completion_date');
        $employeeIds = $this->input->post('employee_ids');
        $selectAll = $this->input->post('select_all');

        // Check if the video and completion date are provided
        if (!$videoId || !$completionDate) {
            // Handle error - video or completion date missing
            echo "Video or completion date missing!";
            return;
        }

        // Assign the video to the selected employees
        if ($selectAll) {
            // Assign to all employees
            $data = array(
                'created_emp_id' 				    => $this->session->userdata['logged_in_timesheet']['empId'],
                'assign_to_all' 			        => true,
                'videoId' 				            => $videoId,
                'completion_date' 			        => $completionDate,
                'created_by'    			       => date('Y-m-d H:i:s'),
            );
            $this->lms_model->assignVideoToEmployee($data);
            $vd = $this->lms_model->getVideoDetails($videoId);
            $this->notification_model->add_notification($vd[0]->lms_video_name .' | Complete Before | '. date_format(date_create($completionDate),'d M, Y'));
        } else {
            // Assign to specific employees
            $vd = $this->lms_model->getVideoDetails($videoId);
            // echo "<pre>".$vd[0]->lms_video_name;print_r($vd);exit;
            foreach ($employeeIds as $employeeId) {
                $data = array(
                    'created_emp_id' 				    => $this->session->userdata['logged_in_timesheet']['empId'],
                    'empId' 			                => $employeeId,
                    'videoId' 				            => $videoId,
                    'completion_date' 			        => $completionDate,
                    'created_by'    			       => date('Y-m-d H:i:s'),
               );
               $this->lms_model->assignVideoToEmployee($data);
               $this->notification_model->add_notification_employee($employeeId, $videoId, $vd[0]->lms_video_name .' | Complete Before | '. date_format(date_create($completionDate),'d M, Y'));
            }
        }
        // Load necessary models

        redirect('lms/assignVideoToEmployee/'. $videoId);

    }
    
    
	// Learning Management system videos save to database query Start 
    
    
        public function add_learning_videos(){
          if(!empty($this->input->post('lms_video_name'))) :             
            
			
            
            $data = array(
                    'created_emp_id' 				   => $this->session->userdata['logged_in_timesheet']['empId'],
                    'lms_video_name' 			       => $this->input->post('lms_video_name'),
                    'catId' 				           => $this->input->post('catId'),
//                    'uplode_file_location' 			   => $this->UploadImage(),
                    'lms_desc' 		                   => $this->input->post('lms_desc'),
                    'lms_status'		               => $this->input->post('lms_status'),
                    'created_by'    			       => date('Y-m-d H:i:s'),
                    'updated_by' 		 		       => date('Y-m-d H:i:s'),
			   );
            $catId = $this->input->post('catId');
            $videoId = $this->lms_model->insert_lms_videos($data);
//            echo "geeg". $videoId;exit;
            $path = realpath(APPPATH.'../lms_videos/');
            
            if(!file_exists($path.'/'.$this->input->post('catId')))
            {
                mkdir($path.'/'.$this->input->post('catId'),0777,TRUE);
            }
            if(!file_exists($path.'/'.$this->input->post('catId').'/'.$videoId)){
               mkdir($path.'/'.$this->input->post('catId').'/'.$videoId,0777,TRUE);
            }

//            exit($path.'/'.$this->input->post('catId').'/'.$videoId);
            $videoFile = $this->UploadImage($path.'/'.$this->input->post('catId').'/'.$videoId); 
			$videoDocs = $this->UploadDocument($path.'/'.$this->input->post('catId').'/'.$videoId); 
//            echo "<pre>";print_r($videoDocs);echo "<pre>";print_r($videoFile);
            if($videoId && ($videoFile || $videoDoc)){
                $this->lms_model->updateVideoAttachments($videoId, $videoFile, $videoDocs);
            }
//            exit($videoId);
            	
            if($data['lms_status'] == 0){
                $this->notification_model->add_notification('New Video Added# '. $data['lms_video_name']);
            }
		
		    redirect('lms');
            
            endif;
            
        }    
    
    // Learning Management system videos save to database query END
    
    
    //updating learning management videos functionality Start
    
        public function edit_learning_videos(){


          if(!empty($this->input->post('lms_video_name'))) :             
            
			$videoId = $this->input->post('videoId');
            
            $data = array(
                    'created_emp_id' 				   => $this->session->userdata['logged_in_timesheet']['empId'],
                    'lms_video_name' 			       => $this->input->post('lms_video_name'),
                    'catId' 				           => $this->input->post('catId'),
                    'lms_desc' 		                   => $this->input->post('lms_desc'),
                    'lms_status'		               => $this->input->post('lms_status'),
                    'created_by'    			       => date('Y-m-d H:i:s'),
                    'updated_by' 		 		       => date('Y-m-d H:i:s'),
			   );
            $catId = $this->input->post('catId');
            if($videoId)
            $videoId = $this->lms_model->update_lms_videos($data , $videoId );
//            echo "geeg". $videoId;exit;
            $path = realpath(APPPATH.'../lms_videos/');
            
            if(!file_exists($path.'/'.$this->input->post('catId')))
            {
                mkdir($path.'/'.$this->input->post('catId'),0777,TRUE);
                
            }
            if(!file_exists($path.'/'.$this->input->post('catId').'/'.$videoId))
            {
               mkdir($path.'/'.$this->input->post('catId').'/'.$videoId,0777,TRUE);
            }
//            exit($path.'/'.$this->input->post('catId').'/'.$videoId);
            $videoFile = $this->UploadImage($path.'/'.$this->input->post('catId').'/'.$videoId); 
			$videoDocs = $this->UploadDocument($path.'/'.$this->input->post('catId').'/'.$videoId); 
            
            if($videoId && ($videoFile || $videoDoc)){
                $this->lms_model->updateVideoAttachments($videoId, $videoFile, $videoDocs);
            }
            	
            if($data['lms_status'] == 0){
                $this->notification_model->add_notification('New Video Added# '. $data['lms_video_name']);
            }
		
		    redirect('lms');
            
            endif;
            



        }

        
    //updating learning management videos functionality Start
        
        
   
    //file upload function start here //
    public function UploadImage($path=null){
            $config['upload_path'] = !empty($path) ? $path : 'lms_videos/';
			$config['allowed_types'] = 'mp4|jpg|jpeg|png|gif|pdf|ppt|mpg';
			$config['file_name'] = $_FILES['uplode_file_location']['name'];
			$config['overwrite']     = false;
			$config['max_size']	 = '1000000000000000';
			 //$this->upload->initialize($config);
//        echo "<pre>";print_r($config);exit;
			 $this->load->library('upload', $config);
		   //Load upload library and initialize configuration
					if($this->upload->do_upload('uplode_file_location')){
						$uploadData = $this->upload->data();
						return $filename = $uploadData['file_name']; 
					}
//       echo "<pre>";print_r($config); exit;
	  }
    
    public function UploadDocument($path=null){
        $docs = [];
        for($i=1;$i<=2;$i++){
            $config =[];
            if(!empty($_FILES['video_document'.$i]) && !empty($_FILES['video_document'.$i]['name'])){ 
                $config['upload_path'] = !empty($path) ? $path : 'lms_videos/';
                $config['allowed_types'] = 'mp4|jpg|jpeg|png|gif|pdf|ppt|mpg';
                $config['file_name'] = $_FILES['video_document'.$i]['name'];
                $config['overwrite']     = false;
                $config['max_size']	 = '1000000000000000';
                 $this->upload->initialize($config);
//                $this->load->library('upload', $config);
               //Load upload library and initialize configuration
                if($this->upload->do_upload('video_document'.$i)){
                    $uploadData = $this->upload->data();
                    array_push($docs, $uploadData['file_name']); 
                }
            }
        }
	    return $docs;
      }
	
 //file upload function start here END //	
	
 //Member watching videos information save to database START
    
    public function memberWachingVideos(){
        
        
         $member_emp_id = $this->session->userdata['logged_in_timesheet']['empId']; // Loged In
        
          $lms_videoID =  $this->input->post('watching_id');
        
        
        
         if(!empty($lms_videoID)) :
            
            $data = array(
                        'video_id' 				           => $lms_videoID,
                        'emp_id	' 			               => $member_emp_id,
                        'emp_name' 				           => 'kanth',
                        'mem_watch_date' 			       => date('Y-m-d'),
                        'status'		                   => '0',
                        'created_by'    			       => date('Y-m-d H:i:s'),
                        'updated_by' 		 		       => date('Y-m-d H:i:s'),
                   );
            
            $this->lms_model->member_watching_videos($data);
		
        endif;
        
    }
    
//Member watching videos information save to database END 
    
//Member watching video details and timetraker to save to database
    
    public function member_watching_video_info($lms_id = NULL){
        
      if(!empty($lms_id)):
        
        $member_emp_id = $this->session->userdata['logged_in_timesheet']['empId']; // Loged In

        $data['viewdetails'] = $this->lms_model->getVideoDetails($lms_id);
        $videoDetails = $data['viewdetails'][0];
//        echo "<pre>";print_r($videoDetails);exit;
        $catId = $videoDetails->catId;
        $data['catInfo'] = $this->lms_model->getCategoryDetails($catId); 
        $data['subCategories'] = $this->lms_model->getFirstLevelSubCategories($catId); 
        $data['breadcrumbs'] = $this->lms_model->get_category_and_parents($catId);
        
        if(!empty($member_emp_id)):
            // Get the user's watch time progress for this video
            $progress = $this->lms_model->get_video_progress($member_emp_id, $lms_id);

            // If progress exists, use it; otherwise, start from the beginning
            $watch_time = ($progress) ? $progress['watch_time'] : 0;

            // Pass video details and watch time to the view
            $data['watch_time'] = $watch_time;
            $spent_time = ($progress && $progress['spent_time']) ? $progress['spent_time'] : 0;
            $data['spent_time'] = $spent_time;
            $video_duration = ($videoDetails->video_duration) ? $videoDetails->video_duration : 0;
            $data['video_duration'] = $video_duration;
            // echo "<pre>"; print_r($data);exit;
        endif;

		$this->load->view('learning_management_system/member_watching_video_view_information' , $data);
        
        endif;
        
        
    }


    function update_watch_time($video_id, $watch_time, $spent_time)
    {
        $member_emp_id = $this->session->userdata['logged_in_timesheet']['empId'];

        // Update the user's watch time progress for this video
        $this->lms_model->update_video_progress($member_emp_id, $video_id, $watch_time, $spent_time);

    }

    public function updateVideoDuration($video_id, $video_duration){
        $member_emp_id = $this->session->userdata['logged_in_timesheet']['empId'];
        // Update the user's watch time progress for this video
        $this->lms_model->updateVideoDuration($video_id, $video_duration);

    }


    public function logStartTime($video_id) {
        $member_emp_id = $this->session->userdata['logged_in_timesheet']['empId'];
        // Log start time when user starts watching the video
        $this->lms_model->logStartTime($member_emp_id, $video_id);
    }

    public function logEndTime($video_id, $watch_time, $spent_time) {
        $member_emp_id = $this->session->userdata['logged_in_timesheet']['empId'];
        // Log end time when user finishes watching the video
        $this->lms_model->logEndTime($member_emp_id, $video_id, $watch_time, $spent_time);
        $vd = $this->lms_model->getVideoDetails($video_id);
        $this->notification_model->notification_on_complete_assignment(
            $member_emp_id, 
            $video_id, 
            $this->session->userdata['logged_in_timesheet']['name']. ' |  '.$vd[0]->lms_video_name .' | Completed on | '. date('M j, Y'));
    }


    public function searchVideos(){
        
    }
    
    
    
    public function catTopics($topic = NULL){ 
        
        if($topic == 'architectural'):
        
            $this->load->view('learning_management_system/new-design/lms_category_details');
        
        elseif($topic == 'topices'):
        
            $this->load->view('learning_management_system/new-design/lms_category_course_details');
        
        elseif($topic == 'topic_wise_videos_list'):
            
            $this->load->view('learning_management_system/new-design/topic_wise_videos');
        
        
        else:
        
            $this->load->view('learning_management_system/new-design/lms_service_details');
        
                
        endif;
    }


    
    

}
