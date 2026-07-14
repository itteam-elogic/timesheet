<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Lms_Model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}
  

	// Read data using username and password
	
	
	public function insert_lms_videos($data){  // Add Client Model 
	
	 if($data):
//	         $this->db->trans_start();
	   		 $this->db->insert('lms_videos', $data);	
//	         $this->db->trans_complete();
             return $this->db->insert_id();
	   endif;
	
	}
    
    
    public function update_lms_videos($data , $videoId){  // Add Client Model 
	
        $this->db->where('videoId', $videoId);
		
	    $update = $this->db->update('lms_videos', $data);
		
		if($update):
			
			  return true; 
			
		endif;
        
	
	}
    
    function updateVideoAttachments($video_id, $videoFile, $videoDocs){
       $data = array(
			'uplode_file_location' => !empty($videoFile) ? $videoFile : NULL,
            'video_doc1' => !empty($videoDocs[0]) ? $videoDocs[0] : NULL,
            'video_doc2' => !empty($videoDocs[1]) ? $videoDocs[1] : NULL,
		);
		$this->db->where('videoId', $video_id);
		$this->db->update('lms_videos', $data);
	}

	public function getVideoDetails($video_id)
    {
        return $this->db->select('*')
		->from('lms_videos as v')						
		->where('v.videoId', $video_id)->get()->result();
    }
    
    public function listOfLmsVideos($empId, $lms_id = NULL){
        
        if(!empty($lms_id)):
            $getVideos = $this->db->select('*, lc.name as categoryName')
						->from('lms_videos as v')						
						->join('lms_video_categories as lc ', 'lc.catId = v.catId', 'left')
                        ->where('v.videoId', $lms_id)->where('v.lms_status','0')->get();
        
        else:
            $getVideos = $this->db->select('v.*,e.name,e.empId, a.completion_date, b.completed_date, lc.name as categoryName')
						->from('lms_videos as v')
                        ->join('lms_video_categories as lc ', 'lc.catId = v.catId', 'left')
						->join('employee_details as e ', 'v.created_emp_id = e.empId', 'left')
                        ->join('(select MIN(completion_date) as completion_date,videoId FROM `lms_video_assignments` WHERE `empId` = '.$empId .' GROUP BY videoId) as a', 'a.videoId = v.videoId', 'left')
                        ->join('(select completed_date,videoId FROM `lms_video_progress` WHERE `empId` = '.$empId .') as b', 'b.videoId = v.videoId', 'left')
						->where('v.lms_status','0')
						->order_by('b.completed_date' , 'asc')
                        ->order_by('a.completion_date' , 'desc')
                        ->order_by('videoId' , 'desc')->get();
        endif;
        
        
        return $getVideos->result();
        
    }
    
    
    public function member_watching_videos($data){
        
                
        if($data):
	   
	   		 $this->db->insert('lms_member_watching_videos', $data);	
	   
	   endif;
        
    }
    
    public function recentLmsVideos($catId){
        
            $getVideos = $this->db->select('v.*,e.name,e.empId')
						->from('lms_videos as v')
						->join('employee_details as e ', 'v.created_emp_id = e.empId', 'left')
                        ->where('v.catId',$catId)
						->where('v.lms_status','0')
						->order_by('videoId' , 'desc')->limit(10)->get();
        return $getVideos->result();
        
        
    }

	function get_video_progress($empId, $videoId)
    {
        $this->db->where('empId', $empId);
        $this->db->where('videoId', $videoId);
        $query = $this->db->get('lms_video_progress');

        return $query->row_array();
    }

	function updateVideoDuration($video_id, $video_duration){
		$data = array(
			'video_duration' => $video_duration,
		);
		$this->db->where('videoId', $video_id);
		$this->db->update('lms_videos', $data);
	}

    function update_video_progress($empId, $videoId, $watch_time, $spent_time)
    {
        $progress = $this->get_video_progress($empId, $videoId);

        if ($progress) {
            // If progress record exists, update it
            $data = array(
                'spent_time' => $spent_time,
				'watch_time' => $watch_time,
                'updated_at' => date('Y-m-d H:i:s')
            );
            $this->db->where('id', $progress['id']);
            $this->db->update('lms_video_progress', $data);
        } else {
            // If no progress record exists, insert a new one
            $data = array(
                'empId' => $empId,
                'videoId' => $videoId,
                'watch_time' => $watch_time,
				'spent_time' => $spent_time,
                'started_date' => date('Y-m-d')
            );
            $this->db->insert('lms_video_progress', $data);
        }
    }

    

	public function logStartTime($user_id, $video_id) {
        $data = array(
            'user_id' => $user_id,
            'video_id' => $video_id,
     		'start_time' => date('Y-m-d H:i:s')
        );

        $this->db->insert('lms_video_tracking', $data);
    }

    public function logEndTime($empId, $videoId, $watch_time, $spent_time) {
        if($spent_time >= $watch_time){ 
            $data = array(
                'spent_time' => $watch_time,
                'watch_time' => $watch_time,
                'completed_date' => date('Y-m-d')
            );
            $this->db->where('empId', $empId);
            $this->db->where('videoId', $videoId);
            $this->db->update('lms_video_progress', $data);
        }
        // $this->db->where('user_id', $user_id);
        // $this->db->where('video_id', $video_id);
        // $this->db->where('end_time IS NULL'); // Only update if end_time is not set
        // $this->db->update('lms_video_tracking', array('end_time' => date('Y-m-d H:i:s')));
    }

	public function getVideoReport($video_id) {
        $this->db->select('v.empId,  v.spent_time, CONCAT(e.name , " (" , e.user_type, ")") as empName');
        $this->db->from('lms_video_progress as v');
		$this->db->join('employee_details as e', 'e.empId=v.empId', 'left');
        $this->db->where('v.videoId', $video_id);
        $query = $this->db->get();

        $video_report = $query->result_array();
		$total_users_watched = count($video_report);
        $total_duration_left = 0;
		$videoDuration = $this->getVideoDuration($video_id);
		// echo "<pre>"; print_r($video_report);
        foreach ($video_report as &$user) {
            $user['time_left'] = $videoDuration - $user['spent_time'];
        }
		// exit;

        return array(
            'videoId' => $video_id,
            'total_users_watched' => $total_users_watched,
            'video_duration' => $videoDuration,
            'employees' => $video_report
        );
    }

	private function getVideoDuration($video_id) {
        $this->db->select('video_duration');
        $this->db->from('lms_videos');
        $this->db->where('videoId', $video_id);
        $query = $this->db->get();

        return $query->row()->video_duration;
    }


    public function fetchEmployeeByKey($searchKey){  
        $userQ  = $this->db->select('*')
        ->from('employee_details')
        ->like('name', $searchKey)
        ->order_by('name' , 'asc')->get();
                 
        return $userQ->result();
     
     }

     public function fetchVideosByKey($searchKey){  
        $userQ  = $this->db->select('videoId, lms_video_name as videoName')
        ->from('lms_videos')
        ->like('lms_video_name', $searchKey)
        ->order_by('lms_video_name' , 'asc')->get();
                 
        return $userQ->result();
     
     }

     public function fetchReportByEmp($payload){
        // echo "<pre>";print_r($payload);exit;
         $query = NULL;
        if(!empty($payload['empId'])){
            $this->db->select('l.videoId,  ,e.empId,  v.spent_time, 
            CONCAT(e.name , " (" , e.user_type, ")") as empName, e.name, 
            l.lms_video_name, l.lms_video_type, v.started_date, v.completed_date, 
            CONCAT(l.lms_video_name , " (" , l.lms_video_type, ")") as videoName, l.video_duration');
            $this->db->from('lms_videos l');
            $this->db->join('lms_video_progress as v', "l.videoId=v.videoId and v.empId='".$payload['empId']."'", 'left');
            $this->db->join('employee_details as e', "e.empId=v.empId and e.empId='".$payload['empId']."'", 'left');
            if(!empty($payload['videoId'])){
                $this->db->where('l.videoId', $payload['videoId']);
            }
            if(!empty($payload['report_from_date']) && !empty($payload['report_to_date'])){
                $this->db->where('v.started_date >=', $payload['report_from_date']);
                $this->db->where('v.started_date <=', $payload['report_to_date']);
            }
            $query = $this->db->get();
        }elseif(/*!empty($payload['videoId']) &&*/ empty($payload['empId'])){
            $this->db->select('l.videoId,  ,e.empId,  v.spent_time, 
            CONCAT(e.name , " (" , e.user_type, ")") as empName, e.name, 
            l.lms_video_name, l.lms_video_type, v.started_date, v.completed_date, 
            CONCAT(l.lms_video_name , " (" , l.lms_video_type, ")") as videoName, l.video_duration');
            $this->db->from('lms_videos l');
            $this->db->join('lms_video_progress as v', "l.videoId=v.videoId", 'left');
            $this->db->join('employee_details as e', "e.empId=v.empId", 'left');
            if(!empty($payload['videoId'])){
                $this->db->where('l.videoId', $payload['videoId']);
            }
            if(!empty($payload['report_from_date']) && !empty($payload['report_to_date'])){
                $this->db->where('v.started_date >=', $payload['report_from_date']);
                $this->db->where('v.started_date <=', $payload['report_to_date']);
            }
            $query = $this->db->get();
        }
        if(!empty($query)){ 
        $video_report = array_filter($query->result_array(), function($k) {
            return $k['empId'] != '';
        });
         
        $total_videos_watched = count($video_report);
        foreach ($video_report as &$user) {
            $user['time_left'] = $user['video_duration'] - $user['spent_time'];
        }
//		exit;

        return array(
            'total_videos_watched' => $total_videos_watched,
            'report' => $video_report
        );
        }else{
            return array(
                'total_videos_watched' => 0,
                'report' => []
            );
        }
     }

     public function getEmployeeDetails($empId){ // Getting List of Users
	
        $employeeNQ  = $this->db->select('empId,username,name')->from('employee_details')
        ->where('status','Active')->where('empId' , $empId)->get();
        
        return $employeeNQ->row_array();
       
   }
    
    public function getVideoDetArr($videoId){ // Getting List of Users
	
        $vquery  = $this->db->select('videoId, lms_video_name as videoName')->from('lms_videos')
        ->where('videoId' , $videoId)->get();
        
        return $vquery->row_array();
       
   }

   public function assignVideoToEmployee($data) {
        $this->db->insert('lms_video_assignments', $data);    
   }

   public function getAllEmployees(){  
        $userQ  = $this->db->select('*')
        ->from('employee_details')
        ->order_by('name' , 'asc')->get();
             
        return $userQ->result();
 
    }

    public function getEmployeeAssignments($videoId) {
        $this->db->select('e.name, la.completion_date');
        $this->db->from('lms_video_assignments as la');
        $this->db->join('employee_details as e', 'la.empId = e.empId', 'left');
        $this->db->where('la.videoId', $videoId);

        return $this->db->get()->result();
    }


    public function getAllAssignments($userId) {
        $this->db->select('MIN(lv.completion_date), lv.videoId, v.*');
        $this->db->from('lms_video_assignments as lv');
        $this->db->join('lms_videos as v', 'v.videoId = lv.videoId');
        $this->db->where('lv.empId', $userId);

        return $this->db->get()->result();
    }


    public function searchVideos($catId, $empId, $searchKey = NULL){
        
            $getVideos = $this->db->select('v.*,e.name,e.empId, a.completion_date, b.completed_date')
						->from('lms_videos as v')
						->join('employee_details as e ', 'v.created_emp_id = e.empId', 'left')
                        ->join('(select MIN(completion_date) as completion_date,videoId FROM `lms_video_assignments` WHERE `empId` = '.$empId .' GROUP BY videoId) as a', 'a.videoId = v.videoId', 'left')
                        ->join('(select completed_date,videoId FROM `lms_video_progress` WHERE `empId` = '.$empId .') as b', 'b.videoId = v.videoId', 'left')
						->where('v.lms_status','0')
                        ->where('v.catId',!empty($catId) ? $catId : '0')
                        ->like('v.lms_video_name', $searchKey)
						->order_by('b.completed_date' , 'asc')
                        ->order_by('a.completion_date' , 'desc')
                        ->order_by('videoId' , 'desc')->get();
        
        
        return $getVideos->result();
        
    }
    
    
    public function get_categories() {
        return $this->db->get('lms_video_categories')->result();
    }

    public function get_categories_hierarchy() {
        $categories = $this->db->get('lms_video_categories')->result_array();
        return $this->build_hierarchy($categories);
    }

    private function build_hierarchy($categories, $parent_id = 0) {
        $hierarchy = array();
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parent_id) {
                $children = $this->build_hierarchy($categories, $category['catId']);
                if ($children) {
                    $category['children'] = $children;
                }
                $hierarchy[] = $category;
            }
        }
        return $hierarchy;
    }
    
    
    public function getFirstLevelSubCategories($parentCatId){
        $categories = $this->db->select('*')->from('lms_video_categories')->where('parent_id', !empty($parentCatId) ? $parentCatId : NULL)->get()->result_array();
        return $categories;
    }
    
    
    public function listOfLmsVideosByCategory($catId, $empId, $lms_id = NULL){
        
        if(!empty($lms_id)):
            $getVideos = $this->db->select('*, lc.name as categoryName')
						->from('lms_videos as v')						
						->join('lms_video_categories as lc ', 'lc.catId = v.catId', 'left')
                        ->where('v.videoId', $lms_id)->where('v.lms_status','0')->get();
        
        else:
            $getVideos = $this->db->select('v.*,e.name,e.empId, a.completion_date, b.completed_date, lc.name as categoryName')
						->from('lms_videos as v')
                        ->join('lms_video_categories as lc ', 'lc.catId = v.catId', 'left')
						->join('employee_details as e ', 'v.created_emp_id = e.empId', 'left')
                        ->join('(select MIN(completion_date) as completion_date,videoId FROM `lms_video_assignments` WHERE `empId` = '.$empId .' GROUP BY videoId) as a', 'a.videoId = v.videoId', 'left')
                        ->join('(select completed_date,videoId FROM `lms_video_progress` WHERE `empId` = '.$empId .') as b', 'b.videoId = v.videoId', 'left')
						->where('v.catId',!empty($catId) ? $catId : '0')
                        ->where('v.lms_status','0')
						->order_by('b.completed_date' , 'asc')
                        ->order_by('a.completion_date' , 'desc')
                        ->order_by('videoId' , 'desc')->get();
        endif;
        
        
        return $getVideos->result();
        
    }
    
    
    public function get_category_and_parents($category_id) {
        $category = $this->db->get_where('lms_video_categories', array('catId' => $category_id))->row_array();
        if ($category['parent_id'] !== null) {
            $parent_category = $this->get_category_and_parents($category['parent_id']);
            return array_merge($parent_category, array($category));
        } else {
            return array($category);
        }
    }
    
    public function getCategoryDetails($catId = null){
        if(!empty($catId)){
            return $this->db->select('*')->from('lms_video_categories')->where('catId', $catId)->get()->row_array();
        }else{ 
        return [];
        }
    }
    
    public function getAllCategories(){
        return $this->db->get('lms_video_categories')->result_array();
    }
    
     public function record_count() {
        return $this->db->count_all('lms_video_categories');
    }
    
    
   
    public function get_all_categories_for_courses() {
        $query = $this->db->get_where('lms_video_categories', array('parent_id' => NULL));
        return $query->result_array();
    }

    function get_all_subcategories_for_courses($cat_id) {
        $query = $this->db->get_where('lms_video_categories', array('parent_id' => $cat_id));
        return $query->result_array();
    } 
    

    function saveCategory($arrData, $catId = NULL){
        if(!empty($catId)){
            $this->db->where('catId', $catId);
            $update = $this->db->update('lms_video_categories', $arrData);
        }else{ 
            $this->db->insert('lms_video_categories', $arrData);        
            return $this->db->insert_id();
        }
    }


    
   

    
   

}

