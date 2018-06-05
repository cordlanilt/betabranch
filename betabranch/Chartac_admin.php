<?php
error_reporting(0);
if ( ! defined('BASEPATH') )
    exit( 'No direct script access allowed' );
//error_reporting(E_ALL);
class Chartac_admin extends CI_Controller {

 
	 
	function __construct() {

        parent::__construct();
        $this->load->model('Login_mdl'); 
		$this->load->helper('url','form','image');		 
		$this -> load -> library('email');
 		$this->load->model('Login_mdl');
		$this->load->model('AdminChartac_mdl');
        	$this->load->model('/clients/Clients_mdl'); 
	 	$this->load->model('Report_mdl');
		$this->load->library('breadcrumbs');
		$this->load->helper(array('my_pdf'));   //  Load helper
		$this -> authentication -> check_login_auth();
 		 	
    } 
	 
  
	function index(){
	 	//echo USER_ROLE_ID;EXIT;
	//error_reporting(0);
	//print_r( $data['result']);exit;
	$data=$this ->  authentication -> check_authentication();	
	$data['user_result'] = $data['result'];
	// echo '<pre>';print_r($data);exit;	
	$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
	$data['title']="Create New Clients";
	$this -> AdminChartac_mdl->insert_importing_data();
	if($data['result']->is_updated_profile==0){
			$data['form_path']='/Chartac_admin/index'; 
			$this->breadcrumbs->push('Edit Profile', '/edit_login/user_register');
			$data['bread_crumbs']=$this->breadcrumbs->show();
			$data['title']="Edit Profile";
			$data['profle_result']=$this -> AdminChartac_mdl->get_ca_clients_data(CHARTAC_CLIENT_ID);
			$data['page']='/chartac_admin/update_ca_firms_client.php';
			$this->load->view('chartac_admin/index_page',$data);
			// print_r($data);
				}
			else{	
				$data['get_admin_configuration_steps'] = $this -> AdminChartac_mdl->get_admin_configuration_steps(CHARTAC_CLIENT_ID);
				$data['title']="Dashboard";
				$this->breadcrumbs->push('Dashboard', '/edit_login/user_register');
				$data['bread_crumbs']=$this->breadcrumbs->show();
				$data['page']='/chartac_admin/index';
				$this->load->view('chartac_admin/index_page',$data);
			}
				// echo '<pre>';print_r( $data);exit;
	 }
	 
	 function view_staff_list(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_users_data']=$this -> AdminChartac_mdl->get_all_users_data_list(CHARTAC_CLIENT_ID);	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('View All Users', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="View All Users";
		$data['page']="/chartac_admin/view_all_staffs";
		$this->load->view('chartac_admin/index_page',$data); 
	 }
	
	 
	 function update_client_profile(){
		 
		 $hidden_chartac_client_id	= $_POST['hidden_chartac_client_id'];
		 $clients_file_path="images/".$_POST['hidden_chartac_client_id'];
		 //$result12=$this -> AdminChartac_mdl->get_profile_path($_SESSION['user_id']);
		 /*if($_FILES["document"]["name"] != ""){
				if(file_exists($clients_file_path)){
					$imageDirStaff=$clients_file_path. '/' ."admin";
					if(file_exists($imageDirStaff)){
					$imagePath=$imageDirStaff.'/' . basename($_FILES["document"]["name"]);
					move_uploaded_file($_FILES['document']['tmp_name'],$imagePath);								
					}else{
					mkdir($imageDirStaff,0777);	
					$imagePath=$imageDirStaff.'/' . basename($_FILES["document"]["name"]);
					move_uploaded_file($_FILES['document']['tmp_name'],$imagePath);
					}
				}else{
					$imageDir = "images/".$_POST['hidden_chartac_client_id'].'/';
					mkdir($imageDir,0777);
					$imageDirStaff=$imageDir. '/' ."admin";
					mkdir($imageDirStaff,0777);					
					$imagePath=$imageDirStaff.'/' . basename($_FILES["document"]["name"]);
					move_uploaded_file($_FILES['document']['tmp_name'],$imagePath);
				}
			 $image_profile_pic = $_FILES["document"]["name"];
			$this ->AdminChartac_mdl->update_admin_profile_photo($image_profile_pic);
		 }
		 if($_POST['hiddenImage']!=NULL){
			$image_profile_pic  	=	  $_POST['hiddenImage'];
			$this ->AdminChartac_mdl->update_admin_profile_photo($image_profile_pic);  
			
		 }*/
		
		// echo $imagePath;exit;
		 $data=array(
					 'alternative_email'=>$_POST['alternative_email'],
					 'established_date'=>strtotime($_POST['established_date']),
					 'house_number_name'=>$_POST['house_number_name'],
					 'street'=>$_POST['street'],
					 'city'=>$_POST['city'],
					 'state'=>$_POST['state'],
					 'postal_code'=>$_POST['postal_code'],
					 'phone'=>$_POST['phone_number'],
					 'company_history'=>$_POST['company_history'],
					 'tan_number'=>$_POST['tan_number'],
					 'st'=>$_POST['st'],
					 'frn'=>$_POST['frn'],
					 'pan_number'=>$_POST['client_pan_number'],
					 'first_name'=>$_POST['first_name'],
					 'contact_person_email'=>$_POST['contact_person_email'],
					 'mobile'=>$_POST['contact_person_number'],
					 'is_updated_profile'=>1 );
		$this -> AdminChartac_mdl->update_chartac_client_data($hidden_chartac_client_id,$data);	 
		redirect('/Chartac_admin/index');
	 }
	 
	 function view_users_data(){
		//   echo '<pre>';print_r($data['result']);exit;
		$data								=	$this ->  authentication -> check_authentication();
		$data['user_result'] 				= 	$data['result'];
		$id									=	encryptor('decrypt', $this ->uri->segment(3));
		$data['user_roles'] 				= 	$this -> AdminChartac_mdl->get_user_roles();
		$data['admins'] 					=	$this-> AdminChartac_mdl->admin_count_roles(CHARTAC_CLIENT_ID);
		$data['test']					 	=   $this -> AdminChartac_mdl->chk_all_leave_type_updated($id);
		$data['ca_assoc_reporter_list']		=	$this -> AdminChartac_mdl->get_all_reporter(CHARTAC_CLIENT_ID);	
		$data['user_data']					=	$this->AdminChartac_mdl->get_user_view_data($id);
		$data['get_leave_type_list_data']	=	$this -> AdminChartac_mdl->get_leave_type_and_number_of_leave_data($id);	
		$data['get_role_log_data']			=	$this -> AdminChartac_mdl->get_role_log_data($id);	
		$data['get_designation_log']		=	$this -> AdminChartac_mdl->get_designation_log($id);//echo $this->db->last_query();exit;	
		$data['get_ctc_log_data']			=	$this -> AdminChartac_mdl->get_ctc_log_data($id);	 
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');   
		$this->breadcrumbs->push('View All Users', '/Chartac_admin/view_staff_list');
		$this->breadcrumbs->push('View Profile', '/edit_login/user_register'); 
		$data['bread_crumbs']				=   $this->breadcrumbs->show();
		$data['title']						=	"View Profile";
		$data['page']						=	"chartac_admin/view_profile";
		$this->load->view('chartac_admin/index_page',$data); 
		 
	 }
	 
	 function update_user_profile(){
		 
		$count 						= count($_POST['hidden_leave_type_id']);
		for($i=0; $i<$count; $i++) {
			$update_data 			= array( 'no_of_days'=> $_POST['number_of_days'][$i] );
			$leave_type_id 			= $_POST['hidden_leave_type_id'][$i];
			$this -> AdminChartac_mdl -> update_users_leave_number_data($update_data,$leave_type_id);
		} 
		/*if($_POST['new_role']!=NULL){
			if($_POST['new_role']!= $_POST['prev_role']){
				$data						=  array(
													'chartac_client_id'		=>  CHARTAC_CLIENT_ID,
													'user_id'				=>  $_POST['hidden_user_id'],
												    'new_role_id'			=>  $_POST['new_role'],
													'prev_role_id'			=>  $_POST['prev_role'],
													'created_date'			=>  strtotime(date('d-m-Y H:i:s')) );
				$this -> AdminChartac_mdl -> insert_user_role_log($data);
			}
			
		}*/
		if($_POST['prev_designation']!= $_POST['designation']){
			$data						=  array(
													'chartac_client_id'		=>  CHARTAC_CLIENT_ID,
													'user_id'				=>  $_POST['hidden_user_id'],
												    'new_role_id'			=>  $_POST['designation'],
													'prev_role_id'			=>  $_POST['prev_designation'],
													'created_date'			=>  strtotime(date('d-m-Y H:i:s')) );
				$this -> AdminChartac_mdl -> insert_user_role_log($data);
		}
		$data = array('status'					=>$_POST['user_status'],
					  'first_name'				=>$_POST['first_name'],
					  'user_role_id' 			=> $_POST['user_role_id'],
					  'leave_approve_manager_id'=>$_POST['leave_approve_manager_id'],
					  'designation'				=>$_POST['designation']
		);
				
		$extra_data=array( 
			 		'employee_id'=>$_POST['employee_id'],
					 
					 'doj'=>strtotime($_POST['date_of_joining']) 
					);

		 $user_id	= $_POST['hidden_user_id'];
		 
		 $this -> AdminChartac_mdl->update_users_data($user_id,$data,$extra_data);
		 $notification_data = array('generated_category_id'=>'4',
									'generated_sub_category_id'=>'4'
									);
		if($_POST['prev_ctc'] != $_POST['new_ctc']){
			$cts_Log		=  array(
							'user_id'=> $_POST['hidden_user_id'], 
							'chartac_client_id'=>CHARTAC_CLIENT_ID,
							'prev_ctc'=>$_POST['prev_ctc'],
							'new_ctc'=>$_POST['new_ctc'],
							'created_date'			=>  strtotime(date('d-m-Y H:i:s')) );
						
			$this->load->model('userdb');
			$this -> userdb->add_ctc_data($cts_Log);
			$user_id			= $_POST['hidden_user_id'];
			$update_data		= array('ctc'=>$_POST['new_ctc']);
			$this -> AdminChartac_mdl->update_users_extra($user_id,$update_data);
		}							
		if($_POST['user_status'] == 0){
			$this -> AdminChartac_mdl -> create_notification($user_id,$notification_data);
		}
		
		redirect('/Chartac_admin/view_staff_list');
	 }
	 
	 function create_new_user(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$max_user_id=$this ->AdminChartac_mdl->get_last_user_id();
		$data['max_user_id']	= $max_user_id[0]->user_id+1;
		$category_id=2;
		
		$data['user_list']  = $this -> AdminChartac_mdl->get_users_list_by_company();
		// echo '<pre>';print_r($data['user_list']);exit;
		
		$data['user_roles'] = $this -> AdminChartac_mdl->get_user_roles();
		$data['get_prefix_suffix_list_for_employeeid']=$this -> AdminChartac_mdl->get_prefix_suffix_list_for_employeeid($category_id);	
		//print_r($data['get_prefix_suffix_list_for_employeeid']);exit;	 
		
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');  
		$this->breadcrumbs->push('All Users', $this->uri->segment(1).'/view_staff_list');
		$this->breadcrumbs->push('Create Users', '/edit_login/user_register');  

		
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Create Users";
		$data['page']="/chartac_admin/register";
		// echo '<pre>';print_r($data);exit;
		//This is for testing the cherry picking the commits
		$this->load->view('chartac_admin/index_page',$data); 
	 }
	//function to get Reporter List based on user role
	public function getReporterList(){
		$data=$this ->  authentication -> check_authentication();
		$role_id					= 	$_GET['role_id'];
		$data						= 	array();
		$data						=	$this -> AdminChartac_mdl->get_all_reporter(CHARTAC_CLIENT_ID);	
		$json 						= 	array();
		foreach($data as $d){
			if($role_id == 3){
				if($d->user_role_id!=3){
					$json[] 	= 	array(
										'id' => $d->user_id,
										'name' => $d->first_name ,
										'role_id' => $d->user_role_id 
									);
				}
			}else if($role_id == 4){
				if($d->user_role_id==4){
					$json[] 	= 	array(
										'id' => $d->user_id,
										'name' => $d->first_name ,
										'role_id' => $d->user_role_id 
									);
				}
			}else{
				$json[] = array(
				'id' => $d->user_id,
				'name' => $d->first_name ,
				'role_id' => $d->user_role_id 
			  );
			}	
		}
		echo json_encode($json);		
	}
	 
public function register(){
		
		//echo $_POST['employee_id'];exit;
//	$this->is_logged_in();
//echo $_POST['designation_id'];exit;
	$random_password	=	substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 6 );
	$data=array(
				'user_role_id'=>$_POST['user_role_id'],
				'leave_approve_manager_id'=>$_POST['approve_manager'],
				'first_name'  =>$_POST['full_name'],
				'email'		 =>$_POST['email'],
				'designation'=>$_POST['designation'], 
				'password'	 =>sha1($random_password),
				'chartac_client_id'=>$_POST['hidden_chartac_client_id'],
				'is_timesheet_mandatory'=>0,
				'status'=>1
				);


	
	$userEmail			=	$_POST['email'];
	$userName			=	$_POST['email'];
	$passWord			=	$random_password;
	$full_name 			= 	$_POST['full_name'];
    $subject 			= 	"Welcome To corDL"; 
    $id 				= 	"";
    $res 				= 	$this -> AdminChartac_mdl->get_all_email_list($id);

    if(count($res) > 0 ){
    	$admin_email 	= 	$res[0]->email;
    	$admin_pass 	= 	$res[0]->password;
    }else{
    	$admin_email 	=	"";
    	$admin_pass 	= 	"";
    }
    

    $result				=	$this->email->send_user_registration_mail($userEmail, $userEmail, $subject, $random_password,$full_name,$admin_email,$admin_pass);
		if($result){
		$last_insert_id = $this -> AdminChartac_mdl->user_registration($data); 
		$extra_data 	= array(
							'user_id' => $last_insert_id, 
							'employee_id'=>$_POST['employee_id'],
							'ctc'=>$_POST['ctc'],
							'chartac_client_id'=>$_POST['hidden_chartac_client_id'],
							'doj'=>strtotime($_POST['date_of_joining'])  	,
							'timesheet_start_date'=>strtotime($_POST['date_of_joining'])  	);
		
		
		$this->load->model('userdb');
		$this -> userdb->add_user_extra_data($extra_data);
		
		redirect("chartac_admin/view_staff_list");
		}else{
		echo "email not sent";
		//exit;
		}
		
	}
	
	function registration(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$max_user_id=$this ->AdminChartac_mdl->get_last_user_id();
		$data['max_user_id']	= $max_user_id[0]->user_id+1;
		$category_id=2;
		$data['get_prefix_suffix_list_for_employeeid']=$this -> AdminChartac_mdl->get_prefix_suffix_list_for_employeeid($category_id);
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('View All Users', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Create Users";
		$data['msg']="Successfully registered";
		$data['page']="/chartac_admin/register";
		$this->load->view('chartac_admin/index_page',$data);
	}
	
	/*****Added by Ranganath Date:06-09-15*******
	View Company profile and update details*/
	function view_company_profile(){
	$data=$this ->  authentication -> check_authentication();
	$data['user_result'] = $data['result'];
	$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
	$this->breadcrumbs->push('Dashboard', '/edit_login/user_register');
	$data['bread_crumbs']=$this->breadcrumbs->show();
	$data['title']="Owners Profile";
	$data['profle_result']=$this -> AdminChartac_mdl->get_ca_clients_data(CHARTAC_CLIENT_ID);
	///$data['states_list'] = $this -> AdminChartac_mdl->states_list();
	//$data['get_all_multiple_address_list'] = $this -> AdminChartac_mdl ->get_all_multiple_address_list();
	//$data['cities_list'] = $this -> AdminChartac_mdl->cities_list();
	$data['page']='/chartac_admin/update_ca_firms_client.php';
	$this->load->view('chartac_admin/index_page',$data);
	}
	
	
	
	
	
	function user_clients_access_control(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_users_data']=$this -> AdminChartac_mdl->get_all_users_data_for_client_db_access($chartac_client_id);	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('User Clients Access Control', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="User Clients Access Control";
		$data['page']="/chartac_admin/user_clients_access_control";
		$this->load->view('chartac_admin/index_page',$data);
	}
	
	
	
	function view_ca_clients(){
		$data=$this ->  authentication -> check_authentication(); 
		$data['user_result'] = $data['result'];
		$data['get_all_clients_data']=$this -> AdminChartac_mdl->get_all_clients_data_list_admin();	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');   
		$this->breadcrumbs->push('View All Clients', '/Chartac_admin/view_staff_list'); 
		$data['bread_crumbs']				=   $this->breadcrumbs->show();
		$data['title']="View Clients ";
		$data['page']='/chartac_admin/view_all_clients';
		$this->load->view('chartac_admin/index_page',$data);
	}
	
	function view_ca_client(){
		$data=$this ->  authentication -> check_authentication(); 
		$data['user_result'] = $data['result'];
		$ca_firms_id	= encryptor('decrypt', $this ->uri->segment(3));
		//$ca_firms_id	= $this ->uri->segment(3);
        $data['get_ca_clients_data']=$this -> AdminChartac_mdl->get_ca_client_details($ca_firms_id);	
		$data['subservice_list']		=	$this -> AdminChartac_mdl->get_all_sub_type_services();	
		$data['place_of_supply_list']	=	$this -> AdminChartac_mdl->get_all_place_of_supply_list();	
		$data['type_of_clients']=$this ->  AdminChartac_mdl->get_all_active_type_of_client_list_edit();	
		$data['type_of_industries_list']=	$this -> AdminChartac_mdl->get_all_type_of_industries_list(); 
		$data['partner_list']=$this -> AdminChartac_mdl->get_all_partner_lists();
		
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_super_admin/index');  
		$this->breadcrumbs->push('View Clients', '/Chartac_admin/view_ca_clients');
		$this->breadcrumbs->push('Clients Details', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		//print_r($data['get_ca_clients_data']);exit;        
		$data['title']=$data['get_ca_clients_data'][0]->company_name;
		//$data['page']="pages/view_ca_client_details";
		$data['view_ca_clients']='EDIT';
     	$data['page']='/chartac_admin/view_ca_client_details';
		$this->load->view('chartac_admin/index_page',$data);
		
	}
	
	function view_ca_quick_client(){
		$data=$this ->  authentication -> check_authentication(); 
		$data['user_result'] = $data['result'];
		$ca_firms_id	= encryptor('decrypt', $this ->uri->segment(3)); 
		//$ca_firms_id	= $this ->uri->segment(3); 
		$data['get_ca_clients_data']=$this -> AdminChartac_mdl->get_ca_client_details($ca_firms_id);	
		$data['subservice_list']		=	$this -> AdminChartac_mdl->get_all_sub_type_services();	
		$data['type_of_clients']=$this ->  AdminChartac_mdl->get_all_active_type_of_client_list();	
		$data['place_of_supply_list']	=	$this -> AdminChartac_mdl->get_all_place_of_supply_list();	
		$data['type_of_industries_list']=	$this -> AdminChartac_mdl->get_all_type_of_industries_list(); 
		$data['partner_list']=$this -> AdminChartac_mdl->get_all_partner_lists();
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', 'Chartac_admin/index');
		$this->breadcrumbs->push('View Clients', '/Chartac_admin/view_ca_clients');
		$this->breadcrumbs->push('View Profile', '/edit_login/user_register'); 
		$data['bread_crumbs']=$this->breadcrumbs->show();
	//	print_r($data['get_ca_clients_data']);exit;
		$data['title']=$data['get_ca_clients_data'][0]->first_name;
		//$data['page']="pages/view_ca_client_details";
		$data['view_ca_clients']='EDIT';
     	$data['page']='/chartac_admin/view_ca_quickclient_details';
		$this->load->view('chartac_admin/index_page',$data);
	}
	
	function convert_client(){
		$ca_firms_id= $_POST['create_quick_client_id'];
 		$is_quick_client = 1;
	//	$chartac_client_id	=	CHARTAC_CLIENT_ID;
		$insert_data=array('company_name'=>$_POST['company_name'],
		'is_quick_client' => 1
		);
		//print_r($insert_data);exit;
		$this -> AdminChartac_mdl->update_ca_clients($insert_data,$ca_firms_id);
		echo ca_firms_id;
		
	}
	
	
	
	
 
		function add_new_clients(){
	 
	$chartac_client_id	=	CHARTAC_CLIENT_ID;

	if($this -> uri ->segment(3)!="" && $_FILES['file']['name']!=""){
	$result12=$this -> AdminChartac_mdl->get_ca_firm_client_profile_path($this -> uri ->segment(3));
	$image_names=$result12[0]->file_path;
	}
	/*if($_FILES['file']['name']!=""){
	$image_names = implode(',',$_FILES['file']['name']);
	}*/		
	if($_POST['partner_id']==""){
			
			$partner_id=0;
			
		}else{
			$partner_id=$_POST['partner_id'];
			
		}	
		$is_quick_client = 1;
		$insert_data=array('company_name'=>$_POST['company_name'],
				'chartac_client_id'=>$chartac_client_id,
				'trading_name'=>$_POST['trading_name'],
				'type_of_industry'=>$_POST['type_of_industry'],
				'tally_reference_client_name'=>$_POST['tally_reference_client_name'],
				'tally_reference_for_advance'=>$_POST['tally_reference_for_advance'],
				'established_date'=>strtotime($_POST['established_date']),
				'address'=>$_POST['address'],
				'street'=>$_POST['street'],
				'city'=>$_POST['city'],
				'state'=>$_POST['state'],
				'postal_code'=>$_POST['postal_code'],
				'email'=>$_POST['email'],
				'phone'=>$_POST['phone_number'],
				'about_company'=>$_POST['about_company'],
				'tan_number'=>$_POST['tan_number'],
				'tin_number'=>$_POST['tin_number'],
				'pan_number'=>$_POST['pan_number'],
				'client_adhar_card'=>$_POST['client_adhar_card'],
				'first_name'=>$_POST['first_name'],
				'type_of_client_ids'=>$_POST['type_of_client_ids'],
				'first_name'=>$_POST['first_name'],
				'designation'=>$_POST['designation'],
				'contact_person_email'=>$_POST['contact_person_email'],
				'contact_person_number'=>$_POST['contact_person_number'],
				'type_of_client_ids'=>$_POST['type_of_client_ids'],
				'status'=>$_POST['r3'],
				//'file_path'=>$image_names,
				'clients_unique_id'=>$_POST['clients_unique_id'],
				'place_of_supply_code'=>$_POST['place_of_supply_code'],
				'place_of_supply_name'=>$_POST['place_of_supply_name'],
				'gst_number'=>$_POST['gst_number'],
				'partner_id'=>$partner_id,
				'is_quick_client'=>1
				);
		if($this -> uri ->segment(3)!=""){
		$chartac_client_id=CHARTAC_CLIENT_ID;

		$ca_firms_id=$this -> uri ->segment(3);
		$clients_file_path="images/$chartac_client_id";
		
		/*	if($_FILES['file']['name']!=""){

		$image_clients_img=$clients_file_path.'/'."clients";
		if(file_exists($image_clients_img)){
		}else{
					mkdir($image_clients_img,0777);
		}
		$clientImageDirStaff=$image_clients_img. '/' ."$ca_firms_id" ;
		//$clientImageDirStaff="images/1/1/profile";
			if(!file_exists($clientImageDirStaff)){
			mkdir($clientImageDirStaff,0777);
		}
		$profile_a=		$clientImageDirStaff .'/' . 'profile/';
		
		if(!file_exists($profile_a)){
			mkdir($profile_a,0777);
		}
		
		$j = 0;     // Variable for indexing uploaded image.
		$target_path = $profile_a;     // Declaring Path for uploaded images.
		$image_names11="";
		for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
		
		 $image_names11=$_FILES['file']['name'][$i];
		// Loop to get individual element from the array
		
		$file_extension = end($ext); // Store extensions in the variable.
		 $target_path1 = $target_path . "$image_names11";     // Set the target path with a new name of image.
		$j = $j + 1;      // Increment the number of uploaded images according to the files in array.
		if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $target_path1)) {
		// If file moved to uploads folder.
		echo $j. ').<span id="noerror">Image uploaded successfully!.</span><br/><br/>';
		} else {     //  If File Was Not Moved.
		echo $j. ').<span id="error">please try again!.</span><br/><br/>';
		}
			
		}			
		}*/
		if($insert_data['company_name']=="")
		{
			echo "Something has gone wrong..Please try again!";exit;
		}
		$this -> AdminChartac_mdl->update_ca_clients($insert_data,$ca_firms_id);
		redirect('/Chartac_admin/view_ca_clients');
		}else{		
		$ca_firms_id=$this -> AdminChartac_mdl->insert_new_clients($insert_data);
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$clients_file_path="images/$chartac_client_id";
		
		$image_clients_img=$clients_file_path.'/'."clients";
		if(file_exists($image_clients_img)){
			
		}else{
					mkdir($image_clients_img,0777);
		}
		$clientImageDirStaff=$image_clients_img. '/' ."$ca_firms_id" ;
		//$clientImageDirStaff="images/1/1/profile";
		if(!file_exists($clientImageDirStaff)){
			mkdir($clientImageDirStaff,0777);
		}
		$profile_a=		$clientImageDirStaff .'/' . 'profile/';
		
		if(!file_exists($profile_a)){
			mkdir($profile_a,0777);
		}
		
		$j = 0;     // Variable for indexing uploaded image.
		$target_path = $profile_a;     // Declaring Path for uploaded images.
		$image_names11="";
		for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
		
		 $image_names11=$_FILES['file']['name'][$i];
		// Loop to get individual element from the array
		//$validextensions = array("jpeg", "jpg", "png");      // Extensions which are allowed.
		//echo $ext = explode('.', basename($_FILES['file']['name'][$i]));   // Explode file name from dot(.)
		
		$file_extension = end($ext); // Store extensions in the variable.
		 $target_path1 = $target_path . "$image_names11";     // Set the target path with a new name of image.
		$j = $j + 1;      // Increment the number of uploaded images according to the files in array.
		if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $target_path1)) {
		// If file moved to uploads folder.
		echo $j. ').<span id="noerror">Image uploaded successfully!.</span><br/><br/>';
		} else {     //  If File Was Not Moved.
		echo $j. ').<span id="error">please try again!.</span><br/><br/>';
		}
			
		}		
		redirect('/Chartac_admin/view_all_clients');
		}
	}
	
	function services_list(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_main_services_list']	=	$this -> AdminChartac_mdl->get_all_main_services_list();
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_super_admin/index');   
		$this->breadcrumbs->push('Services List', '/edit_login/user_register'); 
		$data['bread_crumbs']=$this->breadcrumbs->show();		
		$data['title']="Services";
		$data['service_list_flag']="SERVICE";
		$data['page']="/chartac_admin/services_list";
		$this->load->view('chartac_admin/index_page',$data); 
	}
	
	function create_new_services(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		if($this ->uri->segment(3)!=""){
		$id	=  encryptor('decrypt', $this ->uri->segment(3));	
		$data['get_services_list_by_id']=$this -> AdminChartac_mdl->get_services_list_by_id($id); 
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_super_admin/index');   
		$this->breadcrumbs->push('Services List', '/chartac_admin/services_list');  
		$this->breadcrumbs->push('Update Service', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();		
		$data['title']="Update Service";
		$data['flag_set']="UPDATE";
		$data['page']="/chartac_admin/create_services_list";
		$this->load->view('chartac_admin/index_page',$data);	
		}else{ 
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_super_admin/index');   
		$this->breadcrumbs->push('Services List', '/chartac_admin/services_list');  
		$this->breadcrumbs->push('Update Service', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();		
		$data['title']="Create New Service";
		$data['flag_set']="CREATE";
		$data['srvicesCount']				=	$this -> AdminChartac_mdl->srvicesCount();	
		$data['page']="/chartac_admin/create_services_list";
		$this->load->view('chartac_admin/index_page',$data);
		}		
	}
	function add_new_services(){
		$chartac_client_id=CHARTAC_CLIENT_ID;

		$data=array(
					'chartac_client_id'=>$chartac_client_id,
					'service_name'=>$_POST['create_service_name'],
					'display_name_as'=>$_POST['create_service_display_name'],
					'description'=>$_POST['create_service_description'],
					'status'=>$_POST['service_status']
					);
		if($this ->uri->segment(3)!=""){
			$service_type_id	=	$this ->uri->segment(3);
			$this -> AdminChartac_mdl->update_services_list($service_type_id,$data);
		redirect('/Chartac_admin/services_list');

		}else{
		$this -> AdminChartac_mdl->add_services_list($data);
		redirect('/Chartac_admin/services_list');
		}			
		
	}

        function Checking_duplicate_services(){
		$chartac_client_id	=	CHARTAC_CLIENT_ID;
		$service_name 		= 	$this->input->post('service_name');
		$id					=	$this->input->post('chartac_client_id');
        $duplicate_service	=	$this -> AdminChartac_mdl->check_service($service_name,$chartac_client_id);
		if(count($duplicate_service)>0)
		{
			$data   = array('count'=>1,'service_id'=>$duplicate_service[0]->clients_service_type_id);
		}
		else
		{
		  $data   = array('count'=>0);
		}		
		echo json_encode($data);
	}
	function Checking_duplicate_leave_type(){
		$chartac_client_id		=	CHARTAC_CLIENT_ID;
		$leave_type 			= 	$this->input->post('leave_type');
        $duplicate_leave_type	=	$this -> AdminChartac_mdl->Checking_duplicate_leave_type($leave_type,$chartac_client_id);
		if(count($duplicate_leave_type)>0)
		{
			$data   = array('count'=>1,'id'=>$duplicate_leave_type[0]->id);
		}
		else
		{
		  $data   = array('count'=>0);
		}		
		echo json_encode($data);
	}
	public function checkLeaveCount(){
		$chartac_client_id		=	CHARTAC_CLIENT_ID;
        $leave_type				=	$this -> AdminChartac_mdl->checkLeaveCount($chartac_client_id);
		if(count($leave_type)>0)
		{
			$data   = array('count'=>count($leave_type));
		}
		else
		{
		  $data   = array('count'=>0);
		}		
		echo json_encode($data);
	}
	function Checking_duplicate_sub_services(){
		$service_id 			= 	$this->input->post('service_id');
		$sub_service_name		=	$this->input->post('sub_service_name');
        $duplicate_sub_service	=	$this -> AdminChartac_mdl->check_sub_service($service_id,$sub_service_name);//echo $this->db->last_query();
			if(count($duplicate_sub_service)>0)
			{
				$data   = array('count'=>1,'sub_id'=>$duplicate_sub_service[0]->clients_sub_service_type_id);
			}
			else
			{
			  $data   = array('count'=>0);
			}		
			echo json_encode($data);
		
	}
	
	function new_client_register(){
		
	$client_data=array('first_name'=>$_POST['full_name'],
							'company'=>$_POST['company_name'],
							'email'=>$_POST['email']);
	$chartac_client_id=$this -> AdminChartac_mdl->new_client_registration($client_data);		
	$random_password	=	substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 6 );
	$user_data=array(
				'user_role_id'=>1,
				'first_name'  =>$_POST['full_name'],
				'email'=> $_POST['email'],
				'password'	 =>sha1($random_password),
				'chartac_client_id'=>$chartac_client_id,
				'status'=>1,
				'designation'=>'Admin');	
	
	$full_name=$_POST['full_name'];			
	$userEmail=$_POST['email'];
	$userName=$_POST['email'];
    $subject = "Welcome To corDL";
    $msg="";
    $result=$this->email->send_mail_for_new_client($userEmail, $userEmail, $subject, $msg,$random_password,$full_name);

		if($result){
			$this -> AdminChartac_mdl->user_registration($user_data);
		echo "email sent";
		redirect(base_url());
		}else{
			echo "am here";
			echo "email not sent";
		//exit;
		}

	}
	
	function sub_service_list(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Sub-Services', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		
		if($this ->uri->segment(3)!=""){
			$id 	= $this ->uri->segment(3);
			$data['get_all_sub_services_list']=$this -> AdminChartac_mdl->get_all_sub_services_list($id);	
		}else{
			$id = "";
			$data['get_all_sub_services_list']=$this -> AdminChartac_mdl->get_all_sub_services_list($id);	
		}	
		$data['title']="Sub-Services";
		$data['service_list_flag']="SUBSERVICE";
		$data['page']="/chartac_admin/services_list";
		$this->load->view('chartac_admin/index_page',$data); 
	}
	
	
	
	
	
	
	function bulk_sub_service_deactivate(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];  
		$ids	=	explode(",",$_POST['multiple_subservice_ids']);
		for($i=0;$i<count($ids);$i++){ 
			$res	= $this -> AdminChartac_mdl->bulk_sub_service_deactivate($_POST['bulk_operation_status'], $ids[$i]); 
		} 
		// echo '<pre>';print_r($ids);exit;
		
	}
	
	
	
	
	
	function create_new_sub_services(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		if($this ->uri->segment(3)!=""){
		$id	=  encryptor('decrypt', $this ->uri->segment(3));	
		$data['get_all_main_services_list']=$this -> AdminChartac_mdl->get_all_main_services_list();	
		$data['get_sub_services_list_by_id']=$this -> AdminChartac_mdl->get_sub_services_list_by_id($id);	
		$data['checklist_data'] = $this -> AdminChartac_mdl->get_checklist_data_by_seuservice_id($id);
		$data['subsrvicesCount']			=	$this -> AdminChartac_mdl->subsrvicesCount(); 
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');   
		$this->breadcrumbs->push('All Sub Service','/Chartac_admin/sub_service_list');
		$this->breadcrumbs->push('Update sub Service', '/edit_login/user_register');  
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Update Sub Service";
		$data['flag_set']="UPDATE";
		$data['page']="/chartac_admin/create_sub_services_list";
		$this->load->view('chartac_admin/index_page',$data);	
		}else{
		$data['get_all_main_services_list']	=	$this -> AdminChartac_mdl->get_all_main_services_list();	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');   
		$this->breadcrumbs->push('All Sub Service','/Chartac_admin/sub_service_list');
		$this->breadcrumbs->push('Create sub Service', '/edit_login/user_register');  
		$data['bread_crumbs']				=	$this->breadcrumbs->show();
		$data['subsrvicesCount']			=	$this -> AdminChartac_mdl->subsrvicesCount();	
		$data['title']="Create New Sub Service";
		$data['flag_set']="CREATE";
		$data['page']="/chartac_admin/create_sub_services_list";
		$this->load->view('chartac_admin/index_page',$data);
		}		
		
	}
	
function add_new_sub_services(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		 if( strtotime($_POST['deadline']) > strtotime(date('d-m-Y'))  ){
			 
				$deadline_date = strtotime($_POST['deadline']);
			}else{
			$deadline_date="";
		}
		
	 
		$data=array(
					'chartac_client_id'=>$chartac_client_id,
					'service_type_id'=>$_POST['select_service'],
					'sub_service_name'=>$_POST['create_sub_service_name'],
					'sub_service_display_name'=>$_POST['create_sub_service_display_name'],
					'tally_reference_sub_services_name'=>$_POST['tally_reference_sub_services_name'],
					'description'=>$_POST['create_sub_service_description'],
					'status'=>$_POST['sub_service_status'],

					);
		//print_r($data);exit;
		if($this ->uri->segment(3)!=""){
            $data=array(
					'chartac_client_id'=>$chartac_client_id,
					'service_type_id'=>$_POST['select_service'],
					'sub_service_name'=>$_POST['create_sub_service_name'],
					'sub_service_display_name'=>$_POST['create_sub_service_display_name'],
					'tally_reference_sub_services_name'=>$_POST['tally_reference_sub_services_name'],
					'description'=>$_POST['create_sub_service_description'],
					'status'=>$_POST['sub_service_status'],
					'deadline_date'=>$deadline_date,
					);	
         // print_r($data);		exit;			
			$service_type_id	=	$this ->uri->segment(3);
			
			/*Changing Bulk recurring tasks*/
			if($_POST['number_of_days_before'] >0 && $_POST['deadline'] !=""){ 
				$recurring_before = $_POST['number_of_days_before'];
				$deadline = strtotime($_POST['deadline']);
				$Date3 =  date('Y-m-d',$deadline);
				$create_before = strtotime(date('Y-m-d', strtotime($Date3. ' - ' . $recurring_before . 'days')));
				
				 
				$recurring_task_data = array('deadline'=>$deadline,
											'frequency_date' => $deadline,
											'recurring_task_creating_date'=>$create_before,
											'create_before'=>$recurring_before
											); 
				$this -> AdminChartac_mdl -> update_bulk_recurring_tasks($service_type_id,$recurring_task_data ); 
			}
			
			
			$title = $_POST['checklist_title'];
			if (is_array($title)) {
				// Data array for specfic clients_sub_service_id from AdminChartac_mdl
				$db_results = array();
				$results = $this -> AdminChartac_mdl->get_checklist_data_by_seuservice_id($this ->uri->segment(3));
				if (is_array($results) && count($results) > 0) {
					// collecting only specfic fileds value for merging
					foreach ($results as $result) {
						array_push($db_results, $result->title);
					}
					// $db_results=array_column($results, 'bhu_release_attributes_id');
				}
				if (is_array($db_results)) {
					
					// Contents that needs to delete from DB for existing release_id
					$delete_contents = array_diff($db_results, $title);
					if (is_array($delete_contents) && count($delete_contents) > 0) {
						foreach ($delete_contents as $value) {
							if (!empty($value)) {																
								//Get note id by release_id $ note description for soft delete
								 $this->AdminChartac_mdl->delete_subservice_checklist_data($service_type_id,$value); 
							}
						}
					}
					// Contents that needs to add to DB for existing release_id
					$insert_contents = array_diff($title, $db_results);
					if (is_array($insert_contents) && count($insert_contents) > 0) {
					
						foreach ($insert_contents as $value) {
							$value=trim($value);
							if (!empty($value)) {
								$release_note ['clients_sub_service_id'] = $service_type_id;
								$release_note ['title'] = $value;
 								$this->AdminChartac_mdl->insert_subservice_checklist_data($release_note);
							}
						}
					}
					
				}
				
			}
			
			 
			//echo "am here";exit;
			$this -> AdminChartac_mdl->update_sub_services_list($service_type_id,$data);
			redirect('/Chartac_admin/sub_service_list');

		}else{
			$insert_id = $this -> AdminChartac_mdl->add_sub_services_list($data);
			//Insert multiple checklist data
			$checklist_data =array();
			//$insert_id = 1;
			$count = count($_POST['checklist_title']);
			for($i=0; $i<$count; $i++) {
				$val=trim($_POST['checklist_title'][$i]);
				if (!empty($val)) {
					$checklist_data[$i] = array(
							'clients_sub_service_id' => $insert_id,
							'title' => $val						 
					);
				}
			}	
		 
		if(count($checklist_data)>0){ 
			$this -> AdminChartac_mdl->insert_batch_subservice_checklist($checklist_data);
		}
		redirect('/Chartac_admin/sub_service_list');
		}			
	}
	function service_tax_list(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_service_tax_list']=$this -> AdminChartac_mdl->get_all_service_tax_list();	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Tax List', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Tax List";
		$data['service_list_flag']="SERVICE";
		$data['page']="/chartac_admin/service_tax_list";
		$this->load->view('chartac_admin/index_page',$data); 
	}
	
	function create_new_service_tax(){
		
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		if($this ->uri->segment(3)!=""){			
		$data['get_service_tax_by_id']=$this -> AdminChartac_mdl->get_service_tax_by_id($this ->uri->segment(3));	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Create Services', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Update Tax";
		$data['flag_set']="UPDATE";
		$data['page']="/chartac_admin/create_service_tax";
		$this->load->view('chartac_admin/index_page',$data);	
		}else{
		$data['get_all_main_services_list']=$this -> AdminChartac_mdl->get_all_main_services_list();	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Create New Tax', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Create New Tax";
		$data['flag_set']="CREATE";
		$data['page']="/chartac_admin/create_service_tax";
		$this->load->view('chartac_admin/index_page',$data);
		}			
		
	}
	
	function add_new_service_tax_details(){		
	 
		$from_date_format	= $_POST['from_date'];	 
		$chartac_client_id=CHARTAC_CLIENT_ID;

		$data=array(
					'chartac_client_id'=>$chartac_client_id,
					'title'=>$_POST['create_service_tax_title'],
					'service_tax_percentage'=>$_POST['create_service_tax_percentage'],
					'from_date'=>strtotime($from_date_format),
					'description'=>$_POST['create_service_description'],
					'status'=>$_POST['service_tax_status'],
					'tax_type'=>$_POST['tax_type'],
					'tax_applicable_for'=>$_POST['tax_applicable_for']
					);
		if($this ->uri->segment(3)!=""){
			$service_tax_id	=	$this ->uri->segment(3);
			$this -> AdminChartac_mdl->update_service_tax_list($service_tax_id,$data);
		redirect('/Chartac_admin/service_tax_list');
		}else{
		//print_r($data);exit;
		$this -> AdminChartac_mdl->add_service_tax_list($data);
		redirect('/Chartac_admin/service_tax_list');
		}
	}
	function prefix_suffix_list(){
		
		$category_id	=	$this ->uri->segment(3);
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		if($category_id==1){
			$data['prefix_suffix_flag']="create_new_prefix_suffix";
			$data['data_title']	="INVOICE ID GENERATION";
			$data['title']="Invoice Prefix / Suffix List";
		}else if($category_id==2){
			$data['prefix_suffix_flag']="create_new_prefix_suffix_for_employee_id";
			$data['data_title']="EMPLOYEE ID GENERATION";
			$data['title']="Employee ID Prefix / Suffix List";
		}
		$data['category']		=	$category_id;
		$data['get_all_prefix_suffix_list']=$this -> AdminChartac_mdl->get_all_prefix_suffix_list($category_id);	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Prefix & Suffix List', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['page']="/chartac_admin/prefix_suffix_list";
		$this->load->view('chartac_admin/index_page',$data); 
		
	}
	
	function create_new_prefix_suffix(){
		
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		
		if($this ->uri->segment(3)!=""){
		$data['get_prefix_suffix_by_id']=$this -> AdminChartac_mdl->get_prefix_suffix_by_id($this ->uri->segment(3));	
		//print_r($data['get_sub_services_list_by_id']);exit;	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Update Prefix & Suffix', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Update Invoice Prefix & Suffix";
		$data['flag_set']="UPDATE";
		$data['category']=1;
		$data['page']="/chartac_admin/create_prefix_suffix";
		$this->load->view('chartac_admin/index_page',$data);	
		}else{
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Create Prefix & Suffix', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Create Invoice Prefix & Suffix";
		$data['flag_set']="CREATE";
		$data['category']=1;
		$data['page']="/chartac_admin/create_prefix_suffix";
		$this->load->view('chartac_admin/index_page',$data);
		}			
		
	}
	
	 
	
	function update_invoice_prefix_suffix(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Create Prefix & Suffix', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$category_id = 1;
		$data['get_all_prefix_suffix_list']=$this -> AdminChartac_mdl->get_all_prefix_suffix_list($category_id);	
		
		if(count($data['get_all_prefix_suffix_list']) == 0){
			$data['title']="Create Invoice Prefix & Suffix";
			$data['flag_set']="CREATE";	
		}else{
			$data['title']="Update Invoice Prefix & Suffix";
			$data['flag_set']="UPDATE";	
		}	 
		$data['page']="/chartac_admin/update_invoice_prefix_suffix";
		$this->load->view('chartac_admin/index_page',$data);
	}
	
	
	function create_new_prefix_suffix_for_employee_id(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		if($this ->uri->segment(3)!=""){
		$id = encryptor('decrypt', $this ->uri->segment(3));	
		$data['get_prefix_suffix_by_id']=$this -> AdminChartac_mdl->get_prefix_suffix_by_id($id);	
		//print_r($data['get_sub_services_list_by_id']);exit;	 
		
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Prefix / Suffix List','Chartac_admin/prefix_suffix_list/2');   
		$this->breadcrumbs->push('Create Prefix / Suffix List', $this->uri->segment(1).'/prefix_suffix_list/2');
	
		
		
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Update Employee ID Prefix / Suffix";
		$data['flag_set']="UPDATE";
		$data['category']=2;
		$data['page']="/chartac_admin/create_prefix_suffix";
		$this->load->view('chartac_admin/index_page',$data);	
		}else{
		
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Prefix / Suffix List','Chartac_admin/prefix_suffix_list/2');   
		$this->breadcrumbs->push('Create Prefix / Suffix List', $this->uri->segment(1).'/prefix_suffix_list/2');
	
	
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Create Employee ID Prefix / Suffix";
		$data['flag_set']="CREATE";
		$data['category']=2;
		$data['page']="/chartac_admin/create_prefix_suffix";
		$this->load->view('chartac_admin/index_page',$data);
		}				
		
	}
	function add_invoice_prefix_suffix(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		if($_POST['prefix_title']!="" && $_POST['suffix_title']==""){
		$prefix_suffix_status=1;//Only for prefix				
		}else if($_POST['prefix_title']=="" && $_POST['suffix_title']!=""){
		$prefix_suffix_status=2;//Only for suffix	
		}else{
		$prefix_suffix_status=3;//Both prefix and suffix	
		}
		
		if($_POST['starting_number'] != $_POST['hidden_starting_number']){
			$invoices_count = 0;
		}else{
			$invoices_count = $_POST['invoices_count'];
		}
		$data=array('chartac_client_id'=>$chartac_client_id,
					'prefix_title'=>$_POST['prefix_title'],
					'suffix_title'=>$_POST['suffix_title'],
					'from_date' => strtotime($_POST['applicable_from_date']),
					'to_date' => strtotime($_POST['applicable_till_date']),	
					'starting_number' => $_POST['starting_number'],
					'number_of_zero' => $_POST['number_of_zero'],
					'invoices_count'=>$invoices_count,	
					'category'=>1,					
					'description'=>$_POST['description'],
					'status'=>$_POST['prefix_suffix_status'],
					'prefix_suffix_status'=>$prefix_suffix_status
					);
			if($this -> uri ->segment(3) !=""){
				$id = $this -> uri ->segment(3);
				$last_insert_id = $this -> AdminChartac_mdl->add_invoice_prefix_suffix_table($id,$data);
				redirect("/Chartac_admin/update_invoice_prefix_suffix");
				
			}else{
				$id = "";
				$last_insert_id = $this -> AdminChartac_mdl->add_invoice_prefix_suffix_table($id,$data);
				redirect("/Chartac_admin/update_invoice_prefix_suffix");
			}
		
	}
	
	
	function add_prefix_suffix(){
		  
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$cat_id=$_POST['hidden_category_id'];//if category id 2 Employee Id
		if($_POST['prefix_title'] !="" && $_POST['suffix_title']==""){
			$prefix_suffix_status=1;//Only for prefix
		}else if($_POST['prefix_title']=="" && $_POST['suffix_title'] !=""){
			$prefix_suffix_status=2;//Only for suffix
		}else{
			$prefix_suffix_status=3;//Both prefix and suffix
		}
	 
		$data=array(
					'chartac_client_id'=>$chartac_client_id,
					'prefix_title'=>$_POST['prefix_title'],
					'suffix_title'=>$_POST['suffix_title'],
					'category'=>$_POST['hidden_category_id'],
					'description'=>$_POST['description'],
					'status'=>$_POST['prefix_suffix_status'],
					'prefix_suffix_status'=>$prefix_suffix_status
					);
	 
		 
		if($_POST['prefix_suffix_id'] > 0){
			$prefix_suffix_id	=	$_POST['prefix_suffix_id'];
		 	$this -> AdminChartac_mdl->update_prefix_suffix_table($prefix_suffix_id,$data);
			
		 
		echo $prefix_suffix_id;
		//redirect("/Chartac_admin/prefix_suffix_list/$cat_id");
		}else{
		 
		$prefix_suffix_id=$this -> AdminChartac_mdl->add_prefix_suffix_table($data);
		//redirect("/Chartac_admin/prefix_suffix_list/$cat_id");
		echo $prefix_suffix_id;
		}	
	}
	function type_of_industry(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_type_of_industries_list']=$this -> AdminChartac_mdl->get_all_type_of_industries_list();	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Type of Industry', '/edit_login/user_register');   
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Type of Industry";
		$data['page']="/chartac_admin/type_of_industries_list";
		$this->load->view('chartac_admin/index_page',$data); 
	}
	
	function create_new_type_of_industries(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		if($this ->uri->segment(3)!=""){
		$id	=  encryptor('decrypt', $this ->uri->segment(3));	
		$data['get_type_of_indusries_id']=$this -> AdminChartac_mdl->get_type_of_industries_by_id($id);	
		//print_r($data['get_sub_services_list_by_id']);exit;	 
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Type of Industries List', '/Chartac_admin/type_of_industry');
		$this->breadcrumbs->push('Update Type of Industry', '/edit_login/user_register');   
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['industriesCount'] 	= 	$this->AdminChartac_mdl->industriesCount();
		$data['title']="Update Type of Industry";
		$data['flag_set']="UPDATE";
		$data['page']="/chartac_admin/create_type_of_industries";
		$this->load->view('chartac_admin/index_page',$data);	
		}else{
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Type of Industries List', '/Chartac_admin/type_of_industry');
		$this->breadcrumbs->push('Create Type of Industry', '/edit_login/user_register');   
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Create Type of Industry";
		$data['flag_set']="CREATE";
		$data['industriesCount'] 	= 	$this->AdminChartac_mdl->industriesCount();
		$data['page']="/chartac_admin/create_type_of_industries";
		$this->load->view('chartac_admin/index_page',$data);
		}		
		
	}
	
	function add_type_of_industries(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=array(
					'chartac_client_id'=>$chartac_client_id,
					'title'=>$_POST['type_of_industries_name'],
					'description'=>$_POST['description'],
					'status'=>$_POST['type_of_industry_status']
					);
		if($_POST['add_type_of_industries']>0){
			$id	=	$_POST['add_type_of_industries'];
			$this -> AdminChartac_mdl->update_type_of_industries($id,$data);
		//redirect('/Chartac_admin/type_of_industry');
		echo $id;
		}else{
			//print_r($data);exit;
		$id=$this -> AdminChartac_mdl->add_type_of_industries($data);
		//redirect('/Chartac_admin/type_of_industry');
		echo $id;
		}			
		
		
	}
	//Function to get list of industries
	function list_of_industries(){
		$chartac_client_id					=	CHARTAC_CLIENT_ID;
		$result								= 	$this->AdminChartac_mdl->get_industries_list($chartac_client_id);
		$newarray 							= 	array();
		foreach($result as $r){
			$newarray[]						=	$r->title;
		}
		 
		header('Content-Type: application/json');
		echo json_encode($newarray); 
		
	}
	//Function to get list of industries
	function list_of_leave_type(){
		$chartac_client_id					=	CHARTAC_CLIENT_ID;
		$result								= 	$this->AdminChartac_mdl->get_all_leave_type_data();
		$newarray							=   array();
		if(count($result)>0){
			foreach($result as $r)
			{
				$newarray[]					=	$r->leave_type_title;
			}
		
		}
		header('Content-Type: application/json');
		echo json_encode($newarray); 
	}
	//Function to get list of industries
	function list_of_services(){
		$chartac_client_id					=	CHARTAC_CLIENT_ID;
		$result								= 	$this->AdminChartac_mdl->get_service_list($chartac_client_id);
		$newarray 							=  array();
		if(count($result)>0){
			foreach($result as $r){
				$newarray[]					=	$r->service_name;
			}
		}
		header('Content-Type: application/json');
		echo json_encode($newarray); 
	}
	//Function to get list of industries
	function list_of_sub_services(){
		$chartac_client_id					=	CHARTAC_CLIENT_ID;
		$result								= 	$this->AdminChartac_mdl->get_sub_service_list($chartac_client_id);
		$newarray 							=  array();
		if(count($result)>0){
			foreach($result as $r)
			{
				$newarray[]						=	$r->sub_service_name;
			}
		}
		header('Content-Type: application/json');
		echo json_encode($newarray); 
	}
	
	//function to check industries is already exists or not
    function checkIndustries(){
		$chartac_client_id					=	CHARTAC_CLIENT_ID;
		$result								= 	$this->AdminChartac_mdl->checkIndustries($chartac_client_id); 
		if(count($result)>0){ 
			$data							=	array('count'=>1,'id'=>$result[0]->id);
		}else{
			$data							=	array('count'=>0);
		}

		echo json_encode($data);
	}	
	//Function to check sub services status
	function CheckingSubServicesStatus(){
		$chartac_client_id					=	CHARTAC_CLIENT_ID;
		$result								= 	$this->AdminChartac_mdl->activeSubServicesCount($chartac_client_id);
        if(count($result)>0){
			$data							=	array('count'=>count($result));
		}else{
			$data							=	array('count'=>0);
		}	
		echo json_encode($data);
	}
	function type_of_client(){
		
		$data									=	$this ->  authentication -> check_authentication();
		$data['user_result'] 					=	$data['result'];
		$data['get_all_type_of_client_list']	=	$this -> AdminChartac_mdl->get_all_type_of_client_list();	
		$data['bread_crumbs']					=	'';
		$data['title']							=	"Type of Clients";
		$data['page']							=	"/chartac_admin/type_of_client_list";
		$this->load->view('chartac_admin/index_page',$data); 
	}
	//Function to list the client name
	function list_of_client(){
		$chartac_client_id					=	CHARTAC_CLIENT_ID;
		$result								= 	$this->AdminChartac_mdl->list_of_client($chartac_client_id); 
		$newarray							=   array();
        if(count($result)>0) 
			foreach($result as $r)
			{
				$newarray[]	=$r->client_type_name;
			}
		header('Content-Type: application/json');
		echo json_encode($newarray);
	}
	
	function create_new_type_of_client(){
	$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		
		if($this ->uri->segment(3)!=""){
		$id	=  encryptor('decrypt', $this ->uri->segment(3));	
		$data['get_type_of_client_by_id']=$this -> AdminChartac_mdl->get_type_of_client_by_id($id);	
		//print_r($data['get_sub_services_list_by_id']);exit;	  
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Type of Clients', '/Chartac_admin/type_of_client');
		$this->breadcrumbs->push('Update Type of Client', '/edit_login/user_register');   
		$data['bread_crumbs']=$this->breadcrumbs->show(); 
		$data['title']="Update Type of Client";
		$data['flag_set']="UPDATE";
		$data['clientTotal']	=	$this->AdminChartac_mdl->clientTotal();
		$data['page']="/chartac_admin/create_type_of_client";
		$this->load->view('chartac_admin/index_page',$data);	
		}else{
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Type of Clients', '/Chartac_admin/type_of_client');
		$this->breadcrumbs->push('Create Type of Client', '/edit_login/user_register');   
		$data['bread_crumbs']	=	$this->breadcrumbs->show();
		$data['clientTotal']	=	$this->AdminChartac_mdl->clientTotal();
		$data['title']			=	"Create Type of Client";
		$data['flag_set']		=	"CREATE";
		$data['page']			=	"/chartac_admin/create_type_of_client";
		$this->load->view('chartac_admin/index_page',$data);
		}			
		
	}
	
	function add_type_of_clients(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=array(
					'chartac_client_id'=>$chartac_client_id,
					'client_type_name'=>$_POST['type_of_client_name'],
					'display_client_type'=>$_POST['type_of_client_name'],
					'description'=>$_POST['description'],
					'status'=>$_POST['type_of_client_status']
					);
					
		if($_POST['add_type_of_client_id']>0){
			$id	=	$_POST['add_type_of_client_id'];
			$this -> AdminChartac_mdl->update_type_of_client($id,$data);
		//redirect('/Chartac_admin/type_of_client');
		echo $id;
		}else{

		$id=$this -> AdminChartac_mdl->add_type_of_client($data);
		//redirect('/Chartac_admin/type_of_client');
		echo $id;
		}			
		
	}
	function Checking_duplicate_type_of_client(){
		$chartac_client_id					=	CHARTAC_CLIENT_ID;
		$result								= 	$this->AdminChartac_mdl->Checking_duplicate_type_of_client($chartac_client_id);
        if(count($result)>0){
			$data							=	array('count'=>1,'type_id'=>$result[0]->client_type_id);
		}else{
			$data							=	array('count'=>0);
		}	
		echo json_encode($data);
	}
	/**Added By:Ranganath**
	******Posting prefix suffix id and max user id*******
	*******print based on prefix and suffix with max user id*******/
	function get_prefix_suffix_emp_id(){	
		$prefix_suffix_id	=	$_POST['prefix_suffix_id'];
		$max_user_id		=	$_POST['max_user_id'];
		$data['get_client_address']		=	$this -> AdminChartac_mdl->get_prefix_suffix_emp_id($prefix_suffix_id,$max_user_id);	
	}
	
	/*****Added Ranganath*****
	******Import client through csv file******
	***/
	function import_clients(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		//print_r($data['get_all_prefix_suffix_list']);exit;
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Import Clients', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Import Clients";
		$data['page']="/chartac_admin/import_clients";
		$this->load->view('chartac_admin/index_page',$data); 
		}
		
/* 	function import_clients_excel_file(){
	if($_FILES['file']['tmp_name']!=""){
		$filename=$_FILES["file"]["tmp_name"];
		$this -> AdminChartac_mdl->import_clients_excel_file($_FILES['file']['tmp_name']);	
		redirect("/Chartac_admin/view_ca_clients");
		}
	} */
	
	function employee_id_prefix_validation(){
		echo $prefix_title=$_POST['prefix_title'];
		echo "what";exit;
		$email_result	=	$this->AdminChartac_mdl->employee_id_prefix_validation($prefix_title);
		
	}
	function get_main_service_table(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$hostname = DB_HOSTNAME;
		$username = 'root';
		if($_SERVER['SERVER_NAME'] == "chartac.com"){
		$password = '';	
		}else{
		$password = DB_PASSWORD;//ranganathaws
		}
		$database = DB_NAME;
		$conn = mysqli_connect($hostname,$username,$password);
		mysqli_select_db($conn,$database) or die ("no database");
		// Fetch Record from Database
		$output = "";
		$header = '';
		$table = "clients_service_type_table"; // Enter Your Table Name 
		$sql = "select service_name,clients_service_type_id from $table where chartac_client_id=$chartac_client_id";
		$result = mysqli_query($conn,$sql);
		$clients_sub_service_type_table="clients_sub_service_type_table";
		$sql1="update importing_list_table set status=1 where table_name='".$clients_sub_service_type_table."' ";
		$qq=mysqli_query($conn,$sql1);
		//echo $qq;exit;
		$columns_total = mysqli_num_fields($result);
		
		for ($i = 0; $i < $columns_total; $i++) {
		$heading = mysqli_fetch_field_direct($result, $i);
		$output .= '"'.$heading->name.'",';
		}
		$ii=0;
		while ($row = mysqli_fetch_array($result)) {
		if($ii==0)	{
		$output .= "\n"; 	
		$ii++;	
		}
		for ($i = 0; $i < $columns_total; $i++) {
		$output .='"'.$row["$i"].'",';
		}
		$output .="\n";
		}

		//$filename = fopen("services.csv","w");
		$filename = "list_of_services.csv";

		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);		
		echo $output;
		
		exit;
	}

	function manage_demo_import_files(){
	 
	$chartac_client_id=CHARTAC_CLIENT_ID;
		$demo_id = $this ->uri->segment(3);
		$hostname = DB_HOSTNAME;
		$username = 'root';
	if($_SERVER['SERVER_NAME'] == "chartac.com"){
		$password = '';	
		}else{
		$password = DB_PASSWORD;//ranganathaws
		}
		$database = DB_NAME;
		$conn = mysqli_connect($hostname,$username,$password);
		mysqli_select_db($conn,$database) or die ("no database");
		$output = "";
		if($demo_id == 1){
			$table = "type_of_client_table"; // Enter Your Table Name 
		$sql = "select client_type_name,client_type_id from $table where chartac_client_id = $chartac_client_id and status = 1";
			$result = mysqli_query($conn,$sql);

			//echo $qq;exit;
			$columns_total = mysqli_num_fields($result);
			
			for ($i = 0; $i < $columns_total; $i++) {
			$heading = mysqli_fetch_field_direct($result, $i);
			$output .= '"'.$heading->name.'",';
			}
			$ii=0;
			while ($row = mysqli_fetch_array($result)) {
			if($ii==0)	{
			$output .= "\n"; 	
			$ii++;	
			}
			for ($i = 0; $i < $columns_total; $i++) {
			$output .='"'.$row["$i"].'",';
			}
			$output .="\n";
			}
			$filename = "type_of_client.csv";

			
		}else if($demo_id == 2){
			$table = "type_of_industries"; // Enter Your Table Name 
			$sql = "select title,id from $table where chartac_client_id = $chartac_client_id  and status = 1";
			$result = mysqli_query($conn,$sql);

			//echo $qq;exit;
			$columns_total = mysqli_num_fields($result);
			
			for ($i = 0; $i < $columns_total; $i++) {
			$heading = mysqli_fetch_field_direct($result, $i);
			$output .= '"'.$heading->name.'",';
			}
			$ii=0;
			while ($row = mysqli_fetch_array($result)) {
			if($ii==0)	{
			$output .= "\n"; 	
			$ii++;	
			}
			for ($i = 0; $i < $columns_total; $i++) {
			$output .='"'.$row["$i"].'",';
			}
			$output .="\n";
			}
			$filename = "type_of_industries.csv";
		}else if($demo_id == 3){
			$table = "users_table"; // Enter Your Table Name 
			$sql = "select first_name,user_id from $table where chartac_client_id = $chartac_client_id  AND user_role_id = 4 and status = 1  ";
			$result = mysqli_query($conn,$sql);

			//echo $qq;exit;
			$columns_total = mysqli_num_fields($result);
			
			for ($i = 0; $i < $columns_total; $i++) {
			$heading = mysqli_fetch_field_direct($result, $i);
			$output .= '"'.$heading->name.'",';
			}
			$ii=0;
			while ($row = mysqli_fetch_array($result)) {
			if($ii==0)	{
			$output .= "\n"; 	
			$ii++;	
			}
			for ($i = 0; $i < $columns_total; $i++) {
			$output .='"'.$row["$i"].'",';
			}
			$output .="\n";
			}
			$filename = "list_of_partners.csv";
		}else if($demo_id == 4){
			$table = "users_table"; // Enter Your Table Name 
			$sql = "select first_name,user_id from $table where chartac_client_id = $chartac_client_id  AND user_role_id = 4 OR user_role_id = 3 and status = 1 AND chartac_client_id = $chartac_client_id  ";
			$result = mysqli_query($conn,$sql);
			$columns_total = mysqli_num_fields($result);
			
			for ($i = 0; $i < $columns_total; $i++) {
			$heading = mysqli_fetch_field_direct($result, $i);
			$output .= '"'.$heading->name.'",';
			}
			$ii=0;
			while ($row = mysqli_fetch_array($result)) {
			if($ii==0)	{
			$output .= "\n"; 	
			$ii++;	
			}
			for ($i = 0; $i < $columns_total; $i++) {
			$output .='"'.$row["$i"].'",';
			}
			$output .="\n";
			}
			$filename = "list_of_reporters.csv";	
			
		}else if($demo_id == 5){
			$table = "ca_firms_client_table"; // Enter Your Table Name 
			$sql = "select company_name,first_name,is_quick_client,ca_firms_client_id,tally_reference_client_name from $table where chartac_client_id = $chartac_client_id  AND  status = 1  ";
			$result = mysqli_query($conn,$sql);
			$columns_total = mysqli_num_fields($result);
			
			$column_array = array('CLIENT NAME','ca_firms_client_id');
			
			foreach ($column_array as $column_res){
				$output .=$column_res .',';
			}
			
		 
			$ii=0;
			while ($row = mysqli_fetch_array($result)) {
			if($ii==0)	{
			$output .= "\n"; 	
			$ii++;	
			}
			for ($i = 0; $i < $columns_total; $i++) {
			if($i == 2){
							if($row['is_quick_client'] == 2){
								if($row['company_name']!=''){
									$output .= $row['company_name'].',';
								}else{
									$output .= $row['first_name'].',';
								}
							}else{
								$output .= $row['company_name'].',';
							}
							
						}else if($i == 3){
							
							$output .='"'.$row["$i"].'",';
						}
						// else if($i == 4){
							
							// $output .='"'.$row["$i"].'",';
						// }
			
			}
			$output .="\n";
			}
			
		// echo '<pre>';	print_r($output);exit;   
			$filename = "list_of_clients.csv";	
		}else if($demo_id == 6){
			$table = "clients_sub_service_type_table"; // Enter Your Table Name  
			$sql = "select sub_service_name,clients_sub_service_type_id from $table where chartac_client_id = $chartac_client_id  AND  status = 1  ";
			$result = mysqli_query($conn,$sql);
			$columns_total = mysqli_num_fields($result);
			
			for ($i = 0; $i < $columns_total; $i++) {
			$heading = mysqli_fetch_field_direct($result, $i);
			$output .= '"'.$heading->name.'",';
			}
			$ii=0;
			while ($row = mysqli_fetch_array($result)) {
			if($ii==0)	{
			$output .= "\n"; 	
			$ii++;	
			}
			for ($i = 0; $i < $columns_total; $i++) {
			$output .='"'.$row["$i"].'",';
			}
			$output .="\n";
			}
			$filename = "list_of_sub_services.csv";	
		}else if($demo_id == 7){
			$table = "users_table"; // Enter Your Table Name 
			$sql = "select first_name,user_id from $table where chartac_client_id = $chartac_client_id  AND  status = 1 AND user_role_id !=1 ";
			$result = mysqli_query($conn,$sql);
			$columns_total = mysqli_num_fields($result);
			
			for ($i = 0; $i < $columns_total; $i++) {
			$heading = mysqli_fetch_field_direct($result, $i);
			$output .= '"'.$heading->name.'",';
			}
			$ii=0;
			while ($row = mysqli_fetch_array($result)) {
			if($ii==0)	{
			$output .= "\n"; 	
			$ii++;	
			}
			for ($i = 0; $i < $columns_total; $i++) {
			$output .='"'.$row["$i"].'",';
			}
			$output .="\n";
			}
			$filename = "list_of_users.csv";	
		}
		
		
			header('Content-type: application/csv');
			header('Content-Disposition: attachment; filename='.$filename);		
			echo $output;
			
			exit;
		
	}
	
	function import_table_list(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['import_details'] = $this->AdminChartac_mdl->get_import_table_data();
		
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Import Type of Client', '/edit_login/user_register');   
		$data['bread_crumbs']=$this->breadcrumbs->show();

		$data['title']="Import" ." " .$data['import_details'][0]->title;
		$data['table_name']=$data['import_details'][0]->table_name;
		$data['column_status']=$data['import_details'][0]->status;		
		$data['page']="/chartac_admin/import_data";
		$this->load->view('chartac_admin/index_page',$data); 
	}
	
	function manage_importing_data(){
		
	 	if($_POST['importing_table_name'] == "type_of_client_table"){
			$this -> AdminChartac_mdl->import_clients_excel_data($_FILES['file']['tmp_name']);	
			redirect("/Chartac_admin/type_of_client");
		}

	 	else if($_POST['importing_table_name'] == "type_of_industries"){
			$this -> AdminChartac_mdl->import_clients_excel_data($_FILES['file']['tmp_name']);	
			redirect("/Chartac_admin/type_of_industry");
		} 	

	 	else if( $_POST['importing_table_name'] == "clients_service_type_table" ){
			$this -> AdminChartac_mdl->import_clients_excel_data($_FILES['file']['tmp_name']);	
			redirect("/Chartac_admin/services_list");
		} 	
	 	else if($_POST['importing_table_name'] == "clients_sub_service_type_table"){
				$this -> AdminChartac_mdl->import_clients_excel_data($_FILES['file']['tmp_name']);	
				redirect("/Chartac_admin/sub_service_list");
		}
		else if($_POST['importing_table_name'] =="ca_firms_client_table"){
			$this -> AdminChartac_mdl->import_clients_excel_data($_FILES['file']['tmp_name']);	
			redirect("/Chartac_admin/view_ca_clients");
		}
		else if($_POST['importing_table_name'] =="task_table" && $_POST['importing_title_name'] == "Recurring Task"){
			$this -> AdminChartac_mdl->import_clients_excel_data($_FILES['file']['tmp_name']);	
			redirect("/Chartac_admin/index");
		}
		else if($_POST['importing_table_name'] =="task_table" && $_POST['importing_title_name'] == "Regular Task"){
			$this -> AdminChartac_mdl->import_clients_excel_data($_FILES['file']['tmp_name']);	
			redirect("/Chartac_admin/index");
		}
		else if($_POST['importing_table_name'] =="holiday_table"){
			$holiday_id = $this -> AdminChartac_mdl->import_clients_excel_data($_FILES['file']['tmp_name']);
			$id	=  encryptor('encrypt', $holiday_id);
			redirect("/Chartac_admin/holiday_lists/$id");		
			}
		//print_r($_POST);exit;
	}
	function create_quick_links(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		if($this ->uri->segment(3)!=""){
		$data['get_quick_link_list_by_id']=$this -> AdminChartac_mdl->get_quick_link_list_by_id($this ->uri->segment(3));	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Quick links List', '/admin/Multiple_companies/quick_links_list');
		$this->breadcrumbs->push('Update Quick links', '/edit_login/user_register');   
		$data['bread_crumbs']=$this->breadcrumbs->show(); 
		$data['title']="Update Quick link";
		$data['flag_set']="UPDATE";
		$data['page']="/chartac_admin/create_new_quick_link";
		$this->load->view('chartac_admin/index_page',$data); 
		}else{
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Quick links List', '/admin/Multiple_companies/quick_links_list');
		$this->breadcrumbs->push('Create Quick links', '/edit_login/user_register');   
		$data['bread_crumbs']=$this->breadcrumbs->show(); 
		$data['title']="Create New Link";
		$data['flag_set']="CREATE";
		$data['page']="/chartac_admin/create_new_quick_link";
		$this->load->view('chartac_admin/index_page',$data); 
		}		
	}
	
		function quick_links_list(){
		
			$data=$this ->  authentication -> check_authentication();
			$data['user_result'] = $data['result'];
			$data['get_all_quick_links_list']=$this -> AdminChartac_mdl->get_all_quick_links_list();	
			$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
			$this->breadcrumbs->push('Quick links', '/edit_login/user_register');
			$data['bread_crumbs']=$this->breadcrumbs->show();
			$data['title']="Quick links";
			$data['page']="/chartac_admin/quick_links_list";
			$this->load->view('chartac_admin/index_page',$data); 
		
	}
	function add_quick_links(){
			$chartac_client_id=CHARTAC_CLIENT_ID;
			$data=array(
						'chartac_client_id'=>$chartac_client_id,
						'title'=>$_POST['create_quick_link_title'],
						'link'=>$_POST['create_quick_link'],
						'description'=>$_POST['create_quick_link_description']
						);
			if($this ->uri->segment(3)!=""){
				$service_type_id	=	$this ->uri->segment(3);
				$this -> AdminChartac_mdl->update_quick_links_list($service_type_id,$data);
				redirect('/Chartac_admin/quick_links_list');

			}else{
			$this -> AdminChartac_mdl->add_quick_links_list($data);
			redirect('/Chartac_admin/quick_links_list');
			}			
		
	}
	function delete_quick_link($id){
		$this -> AdminChartac_mdl->delete_quick_link($id);	
		redirect('Chartac_admin/quick_links_list');
	}

	function task_based_invoice(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_task_based_invoice']=$this -> AdminChartac_mdl->get_task_based_invoice();
		// echo '<pre>';print_r($data['get_task_based_invoice']);exit;
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Other Controls', '/edit_login/user_register');   
		$data['bread_crumbs']=$this->breadcrumbs->show();

		$data['title']="Other Controls";
		$data['page']="/chartac_admin/task_based_invoice";
		$this->load->view('chartac_admin/index_page',$data); 
	}
	
	function update_config_task_description(){
		$update_data = array('description'=>$_POST['editval']);
		$config_id = $_POST['id'];
		$this -> AdminChartac_mdl->update_config_task_description($config_id,$update_data);
	}
	function update_config_task_status(){
		$update_data = array('status'=>$_POST['status']);
		$config_id = $_POST['id'];
		$this -> AdminChartac_mdl->update_config_task_description($config_id,$update_data);
	}
	
	
	
	
	function user_access_for_invoice(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_users_data']=$this -> AdminChartac_mdl->get_all_users_data_for_invoice_access($chartac_client_id);	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Invoice users & status', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Invoice users & status";
		$data['page']="/chartac_admin/user_access_for_invoice";
		$this->load->view('chartac_admin/index_page',$data);
	}
	
	
	
	function bulk_invoice_control_access(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];   
	 

		 // echo '<pre>';print_r($_POST);exit;
				 
	
	
		$update_data=array( 
				'is_export_to_tally'						=>	$_POST['is_export_to_tally'],
				'is_invoice_access'							=>	$_POST['normal_invoice_access_bulk'],
				'is_invoice_create_edit'					=>	$_POST['edit_normal_invoice_access_bulk'],
				'is_invoice_view_download'					=>	$_POST['download_invoice_access_bulk'],
				'is_manage_normal_invoice_access'			=>	$_POST['manage_normal_invoice_access_bulk'],
				'is_create_normal_invoice_access'			=>	$_POST['create_normal_invoice_access_bulk'],
				'is_preview_normal_invoice_access'			=>	$_POST['preview_invoice_access_bulk'],
				'is_genarate_normal_invoice_access'			=>	$_POST['genarate_invoice_access_bulk'],
				'is_view_normal_invoice_access'				=>	$_POST['view_invoice_access_bulk'],
				'is_history_normal_invoice_access'			=>	$_POST['history_invoice_access_bulk'],
				'is_cancel_normal_invoice_access'			=>	$_POST['cancel_invoice_access_bulk'],
				'is_remindme_normal_invoice_access'			=>	$_POST['remindme_invoice_access_bulk'],
				'is_proceed_remindme_normal_invoice_access'	=>	$_POST['proceed_remindme_invoice_access_bulk'],
				'is_unbilled_normal_invoice_access'			=>	$_POST['unbilled_invoice_access_bulk'],
				'is_proceed_unbilled_normal_invoice_access'	=>	$_POST['proceed_unbilled_invoice_access_bulk'], 
				'is_rec_invoice_access'						=>	$_POST['rec_invoice_access_bulk'],
				'is_manage_rec_invoice_access'				=>	$_POST['manage_rec_invoice_access_bulk'],
				'is_create_rec_invoice_access'				=>	$_POST['create_rec_invoice_access_bulk'],
				'is_edit_rec_invoice_access'				=>	$_POST['edit_rec_invoice_access_bulk'],
				'is_autogenarated_rec_invoice_access'		=>	$_POST['autogenarated_rec_invoice_access_bulk'],
				'is_edit_autogenarated_rec_invoice_access'	=>	$_POST['edit_autogenarated_rec_invoice_access_bulk'],
				'is_view_autogenarated_rec_invoice_access'	=>	$_POST['view_autogenarated_rec_invoice_access_bulk'],
				'is_download_autogenarated_rec_invoice_access'=>	$_POST['download_autogenarated_rec_invoice_access_bulk'],
				'is_history_autogenarated_rec_invoice_access'=>	$_POST['history_autogenarated_rec_invoice_access_bulk'],
				'is_cancel_autogenarated_rec_invoice_access'=>	$_POST['cancel_autogenarated_rec_invoice_access_bulk'],
				'is_bulk_autogenarated_rec_invoice_access'	=>	$_POST['bulk_autogenarated_rec_invoice_access_bulk'],
					);
					
					
		$ids	=	explode(",",$_POST['multiple_user_ids']); 
		// echo '<pre>';print_r($update_data);exit;
		for($i=0;$i<count($ids);$i++){ 
			$res	= $this -> AdminChartac_mdl->bulk_invoice_control_access($ids[$i],$update_data); 
		} 
		// echo '<pre>';print_r($ids);exit;
		
	}
	
	
	function update_user_access_to_invoice(){
			$column_name = $_POST['column_name']; 
		
		/*Inactive invoice access **/
		if($column_name == "is_invoice_access" && $_POST['status'] == 0 ){ 
			$update_data = array('is_invoice_access'=>0,
								'is_invoice_create_edit' => 0,
								'is_invoice_view_download' => 0
			);				
		}else{
			$update_data = array($column_name=>$_POST['status']);	
		}
		
		$user_id = $_POST['id']; 
 		$this -> AdminChartac_mdl->update_users_invoice_config_data($user_id,$update_data);
	}
	
	
	
	
	
	
	
	
	
	
	function update_user_access_to_client_database(){
		$column_name = $_POST['column_name']; 
		
		/*Inactive invoice access **/
		if($column_name == "is_all_client_db_access" && $_POST['status'] == 0 ){ 
			$update_data = array(
									'is_all_client_db_access'					=>	0,
									'is_all_client_access' 						=> 	0,
									'is_my_related_client_access' 				=> 	0,
									'is_reporting_manager_client_access'		=> 	0,
									'is_create_client_access'					=>	0,
									'is_edit_client_access' 					=> 	0,
									'is_active_deactive_client_access' 			=> 	0,
								);				
		}else if($column_name == "is_all_client_access" && $_POST['status'] == 1 ){ 
			$update_data = array( 
									'is_all_client_access' 						=> 	1,
									'is_my_related_client_access' 				=> 	1,
									'is_reporting_manager_client_access'		=> 	1, 
								);				
		}else if($column_name == "is_my_related_client_access" && $_POST['status'] == 1 ){ 
			$update_data = array(  
									'is_my_related_client_access' 				=> 	1, 
								);				
		}else if($column_name == "is_my_related_client_access" && $_POST['status'] == 0 ){ 
			$update_data = array(  
									'is_all_client_access' 						=> 	0,
									'is_my_related_client_access' 				=> 	0, 
								);				
		}else if($column_name == "is_reporting_manager_client_access" && $_POST['status'] == 1 ){ 
			$update_data = array(  
									'is_reporting_manager_client_access' 		=> 	1, 
								);				
		}else if($column_name == "is_reporting_manager_client_access" && $_POST['status'] == 0 ){ 
			$update_data = array(  
									'is_all_client_access' 						=> 	0,
									'is_reporting_manager_client_access' 		=> 	0, 
								);				
		}else{
			$update_data = array($column_name=>$_POST['status']);	
		}
		
		$user_id = $_POST['id']; 
 		$this -> AdminChartac_mdl->update_users_invoice_config_data($user_id,$update_data);
	}
	
	
	
		
	
		
	
	function bulk_client_database_access(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];   
		$update_data	=	"";
		// echo '<pre>';print_r($_POST);exit; 
		$update_data = array(
								'is_all_client_db_access'					=>	$_POST['is_all_client_db_access'],
								'is_all_client_db_access'					=>	$_POST['is_all_client_db_access'],
								'is_all_client_access' 						=> 	$_POST['is_all_client_access'],
								'is_my_related_client_access' 				=> 	$_POST['is_my_related_client_access'],
								'is_reporting_manager_client_access'		=> 	$_POST['is_reporting_manager_client_access'],
								'is_create_client_access'					=>	$_POST['is_create_client_access'],
								'is_edit_client_access' 					=> 	$_POST['is_edit_client_access'],
								// 'is_active_deactive_client_access' 			=> 	$_POST['is_active_deactive_client_access'],
							);				
	 
		 
		
		$ids	=	explode(",",$_POST['multiple_user_ids']);
		
		
		// echo '<pre>';print_r($update_data);exit;
		for($i=0;$i<count($ids);$i++){ 
			$res	= $this -> AdminChartac_mdl->bulk_client_database_access($ids[$i],$update_data); 
		} 
		// echo '<pre>';print_r($ids);exit;
		
	}
	
	
	
	function getTaskList(){
	$ca_firm_client_id = $_POST['ca_firm_client_id'];
	$getTaskListResult	= $this->AdminChartac_mdl->getTaskList($ca_firm_client_id);
	echo $getTaskListResult;
	}
	
	function getSelectedService(){
		$task_id = $_POST['task_id'];
		$getServiceResult	= $this->AdminChartac_mdl->getSelectedService($task_id);
		echo $getServiceResult;
	}
	
	function auto_suggestion_users_search(){
	$search_result	=	$this -> AdminChartac_mdl->auto_suggestion_users_search();			
	if(count($search_result)>0){
		foreach($search_result as $result){
			
			echo "<li id=$result->user_id>". '@' .$result->user_unique_id .'</li>';
			//echo ;
		}
		}
		else{
		//echo '<li>Not Found</li>';		
		}	
		
	}
	function getDuplicateInvoiceNumber(){
		$res = $this -> AdminChartac_mdl->getDuplicateInvoiceNumber();
		if($res){
			echo "1";
		}else{
			echo "0";
		}
		
	}
	//Suspending user get open and reopen task count.
	function getTaskOpenTaskList(){
		$res_count = $this -> AdminChartac_mdl->getTaskOpenTaskList($_POST['user_id']);	  
		/* echo 		$res_count;
		exit; */

	}
	function updateStepStatusCount(){
	$chartac_client_id	=	CHARTAC_CLIENT_ID;	
	$config_steps = 2;
	$result =  $this -> AdminChartac_mdl->getCountConfigStepsStatus($chartac_client_id,$config_steps);
	
		if( $result[0]->step_status_count != 2){
			$step_status_count = 2;
			$this -> AdminChartac_mdl->updateStepStatusCount($chartac_client_id,$step_status_count,$config_steps);
			$next_status_count = 1;
			$config_steps1 = 3;
			$this -> AdminChartac_mdl->updateStepStatusCount($chartac_client_id,$next_status_count,$config_steps1);
		}
	
	}

	/* function email_list(){	
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_email_list']=$this -> AdminChartac_mdl->get_all_email_list_for_admin();	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Email List', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Email List";
		$data['page']="/chartac_admin/email_list";
		$this->load->view('chartac_admin/index_page',$data); 
	
	} */

	function create_admin_email(){ 
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_email_list']=$this -> AdminChartac_mdl->get_all_email_list_for_admin();
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index'); 
		$data['bread_crumbs']=$this->breadcrumbs->show();
		if(count($data['get_all_email_list']) == 0){
			$this->breadcrumbs->push('Create Email', '/edit_login/user_register');
			$data['title']="Create Email";
			$data['flag_set']="CREATE";	
		}else{
			$this->breadcrumbs->push('Update Email', '/edit_login/user_register');
			$data['title']="Update Email";
			$data['flag_set']="UPDATE";	
		}	
		$data['page']="/chartac_admin/create_admin_email";
		$this->load->view('chartac_admin/index_page',$data);
		
			
	}

	function add_new_admin_email(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$user_id = USER_ID;
		$received_mail_status = 0;
		if(isset($_POST['received_mail'])){
			$received_mail_status = 1;
		}
		$data=array(
					'chartac_client_id'=>$chartac_client_id,
					'user_id' => $user_id,
					'email'=>$_POST['admin_email_id'],
					'password'=>$_POST['admin_email_password'],
					'datetime'=>strtotime(date('d-m-Y H:i:s')),
					'status'=>$received_mail_status
					);
		if($this ->uri->segment(3)!=""){
			$id	=	$this ->uri->segment(3);
			$this -> AdminChartac_mdl->add_new_admin_email($id,$data);
		redirect('/Chartac_admin/create_admin_email');
		}else{
		$id = "";
		$this -> AdminChartac_mdl->add_new_admin_email($id,$data);
		redirect('/Chartac_admin/create_admin_email');
		}			
	}
	
	//Delete recurring task list
	function delete_recurring_task(){
			$id = $_POST['task_id'];
			$this -> AdminChartac_mdl->delete_recurring_task($id);
	}

	function user_access_for_timesheet(){		
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_users_data']=$this -> AdminChartac_mdl->get_all_users_data_for_timesheet_access($chartac_client_id);	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Timesheet users & status', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Timesheet applicable to";
		$data['page']="/chartac_admin/user_access_for_timesheet";
		$this->load->view('chartac_admin/index_page',$data);
	}

	function bulk_timesheet_control_access(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];   
		
		//  echo '<pre>';print_r($_POST);
				 
	
	
		$update_data=array(
						'is_timesheet_mandatory'				=> $_POST['bulk_timesheet_mandatory'],
						'is_timesheet_task_mandatory'					=>	$_POST['bulk_task_mandatory']
					);
		$date 			= 	strtotime($_POST['bulk_timesheet_start_date'] );
		$update_start_date_data 	= 	array('timesheet_start_date'=>$date);			
		$ids	=	explode(",",$_POST['multiple_user_ids']);
		
		
		// echo '<pre>';print_r($update_data);
		// echo '<pre>';print_r($update_start_date_data);
		// echo '<pre>';print_r($ids);exit;
		for($i=0;$i<count($ids);$i++){ 
			$this -> AdminChartac_mdl->update_users_data($ids[$i],$update_data);
			if($_POST['bulk_change_startDate']==1)
				$this -> AdminChartac_mdl->update_users_extra($ids[$i],$update_start_date_data); 
		} 
	}
	
	function update_user_access_to_timesheet(){
	 
		$update_data = array('is_timesheet_mandatory'=>$_POST['status']);
		$user_id = $_POST['id'];
		$this -> AdminChartac_mdl->update_users_data($user_id,$update_data);
	}

	function update_user_task_mandatory(){
		if($_POST['task_mandatory']=='true'){  
			$update_data = array('is_timesheet_task_mandatory'=>1);
		}else{
			$update_data = array('is_timesheet_task_mandatory'=>0);
		}
		
		$user_id 	= $_POST['user_id'];
		$this -> AdminChartac_mdl->update_users_data($user_id,$update_data);
	}
	function update_user_start_date(){
			if($_POST['compulsary_status']==1){  
			$update_data = array('is_timesheet_mandatory'=>1);
			$user_id 	= $_POST['user_id'];
			$this -> AdminChartac_mdl->update_users_data($user_id,$update_data);
			
			
		}
			$date 			= 	strtotime($_POST['start_date'] );
			$update_data 	= 	array('timesheet_start_date'=>$date);
			$user_id 		= 	$_POST['user_id'];
			$this -> AdminChartac_mdl->update_users_extra($user_id,$update_data);
			echo 1;
	}
	function add_new_timesheet_configuration(){
		// echo '<pre>';print_r($_POST);exit;
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=array(
					'chartac_client_id'=>$chartac_client_id,
					'no_of_working_days'=>$_POST['no_of_working_days'],
					'no_of_working_hours_per_day'=>$_POST['number_of_hours'],
					'status'=>1
					);
			if($this ->uri->segment(3)!=""){
			$id	=	$this ->uri->segment(3);
			$this -> AdminChartac_mdl->add_new_timesheet_configuration($id,$data);
			$message = 'Working days & hours are  updated successfully!';
			$this->session->set_flashdata('suc', $message);
			redirect('/Chartac_admin/create_new_timesheet_configuration');	
			
			}else{
			$id = "";
			$this -> AdminChartac_mdl->add_new_timesheet_configuration($id,$data);
			$message = 'Working days & hours are  added successfully!';
			$this->session->set_flashdata('suc', $message);
			redirect('/Chartac_admin/create_new_timesheet_configuration');
			}		
	}
	
	function create_new_timesheet_configuration(){
		
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$id="";
		$data['get_all_timesheet_configuration_list'] = $this -> AdminChartac_mdl->get_timesheet_configuration_data($id);
		if(count($data['get_all_timesheet_configuration_list']) > 0){
			$this->breadcrumbs->push('Edit Timesheet Configuration', '/edit_login/user_register');
			$data['title']="Edit Timesheet Configuration";
			$data['flag_set'] = "UPDATE";
		}else{
			$this->breadcrumbs->push('Time sheet Configuration', '/edit_login/user_register');
			$data['title']="Time sheet Configuration";
			$data['flag_set']="CREATE";
		}
		 
		$data['bread_crumbs']=$this->breadcrumbs->show();
		
		$data['page']="/chartac_admin/create_timesheet_configuration_data";
		$this->load->view('chartac_admin/index_page',$data);
	}
	
	//list of holidays
	function holiday_lists(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
	if($this ->uri->segment(3)!=""){
		$id	=  encryptor('decrypt', $this ->uri->segment(3));
		$data['get_holiday_list_data']=$this -> AdminChartac_mdl->get_holiday_list_data($id);
		$data['get_holiday_list_for_edit']=$this -> AdminChartac_mdl->get_holiday_list_for_edit($id);	
		// echo'<pre>';print_r($data['get_holiday_list_for_edit']);exit;
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Holiday Lists', 'Chartac_admin/holiday_lists');
		$this->breadcrumbs->push('Edit Holiday List', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show(); 
		$data['title']="Edit Holiday List";
		$data['page']="/chartac_admin/create_holiday_list";
		$data['flag_set'] = "CREATE";
		// echo'<pre>';print_r($data);exit;
		$this->load->view('chartac_admin/index_page',$data);	
	}else{
		$id="";
		$data['get_holiday_list_data']=$this -> AdminChartac_mdl->get_holiday_list_data($id);
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Holiday List', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Holiday List";
		$data['page']="/chartac_admin/holiday_list";
		$this->load->view('chartac_admin/index_page',$data);	
	}
		
	}
	
	function create_holiday_list(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Create Holiday List', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Create Holiday List";
		$data['flag_set']="CREATE";
		$data['page']="/chartac_admin/create_holiday_list";
		$this->load->view('chartac_admin/index_page',$data);
	}
	
	//Updating holiday list data
	function update_holiday_list(){
		//error_reporting(E_ALL);
		if(isset($_POST['sync_with_calendar'])){
			$sync_with_cal = 1;
		}else{
			$sync_with_cal = 0;
		}
		
		$count = count($_POST['holiday_date']);
		$id = $this -> uri -> segment(3);
		for($i=0; $i<$count; $i++) {
			$substr_date	= $_POST['holiday_date'][$i];
			$replace_date= str_replace('/','-',$substr_date);
			$month = substr($replace_date, 0, 2);
			$day = substr($replace_date, 3, 2);
			$year = substr($replace_date, 6, 4);
			$final_date = $day . '-' .$month .'-' .$year;
			$strtotime	= strtotime($final_date);
			
			if($_POST['title'][$i] !=""){
				$data[$i] = array(
						   'holiday_id' => $id,
						   'holiday_date' => $strtotime,
						   'title' => $_POST['title'][$i],
						   'description' => $_POST['description'][$i],
						   'status' => '1',
						   );
			$event_date = $year . '-' .$month .'-' .$day;	
			$sync_data =  array(
						   'holiday_id' => $id,
						   'holiday_date' =>$event_date,
						   'title' => $_POST['title'][$i],
						   'description' => $_POST['description'][$i],
						   'status' => '1'   );
				if($event_date > date('Y-m-d')){
					$this -> AdminChartac_mdl -> sync_with_calendar($sync_data);
				}
			}
				
			}
		//print_r($data);exit;
		$this -> AdminChartac_mdl->insert_batch_holiday_list_details($data,$id);					
		redirect("/Chartac_admin/holiday_lists");
		
	}
	
	function getTimeSheetDuplicateEntry(){
			$date = $_POST['timesheet_date'];
			$res = $this -> AdminChartac_mdl->getTimeSheetDuplicateEntry($date);
			if(count($res) > 0){
				echo "1";
			}else{
				
				$data['timesheet_details']	=	$this -> AdminChartac_mdl->get_assignee_timesheet_details($date); 
				//echo count($data['timesheet_details']);
				//$data['page']='pages/render_timesheet_html';
				if(count($data['timesheet_details']) > 0){
					$data['ca_firms_clients_list']	=	$this -> AdminChartac_mdl->get_ca_firms_client_data();
					$data['subservice_list']		=	$this -> AdminChartac_mdl->get_all_sub_type_services();
					$this->load->view('pages/render_timesheet_html',$data);					
				}else{
					echo "0";
				}
				
			}
	}
	
	//list of holidays
	function leave_type_list(){
	  $data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		if($this ->uri->segment(3)!=""){
			$id	=  encryptor('decrypt', $this ->uri->segment(3));
			
			$data['get_leave_type_list']=$this -> AdminChartac_mdl->get_leave_type_list_data($id);	
			$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');
			$this->breadcrumbs->push('Type of leaves', '/Chartac_admin/leave_type_list');
			$this->breadcrumbs->push('Update Leave Type', '/edit_login/user_register');
			$data['bread_crumbs']=$this->breadcrumbs->show();
			$data['title']="Update Type of leaves";
			$data['page']="/chartac_admin/create_leave_type";
			$data['flag_set'] = "UPDATE";
			$this->load->view('chartac_admin/index_page',$data);	
		}else{	
			$id="";
			$data['get_leave_type_list_data']=$this -> AdminChartac_mdl->get_leave_type_list_data($id);	
			$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
			$this->breadcrumbs->push('Type of leaves', '/edit_login/user_register');   
	 		$data['bread_crumbs']=$this->breadcrumbs->show();
			$data['title']="Type of leaves";
			$data['page']="/chartac_admin/leave_type_list";
			$this->load->view('chartac_admin/index_page',$data);	
		}
	}
	/*********************************Reporting and analytics**************************** */
	function load_report(){
		$data=$this ->  authentication -> check_authentication();
		// echo "calls";
		if($this ->uri->segment(3)!=""){
			// echo $this ->uri->segment(3);exit;
			$data=$this ->  authentication -> check_authentication();
			$data['user_result'] = $data['result'];
			// $data['id'] = $this ->uri->segment(3);
			$data['report'] = $this->Report_mdl->getReport($this ->uri->segment(3));

			// echo '<pre>';print_r($data);
			// exit;
			if(USER_ROLE_ID!=1){
				$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');
				$this->breadcrumbs->push('View Reports', '/edit_login/user_register');
			}else{
				$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');
				$this->breadcrumbs->push('Quick Reports', '/Chartac_admin/quick_report_list');
				$this->breadcrumbs->push('View Reports', '/edit_login/user_register');
			}
			$data['bread_crumbs']=$this->breadcrumbs->show();
			$data['title']="View Report : ".$data['report'][0]->reportName;
			$data['page']="/report/view_report";
			$this->load->view('chartac_admin/index_page',$data);
		}else{
			$res = $this->Report_mdl->sessionToken();
			echo $res;
		}
	}

	function load_reportD3(){
			$data=$this ->  authentication -> check_authentication();
			$data['user_result'] = $data['result'];
		    $res = $this->Report_mdl->getReportsD3(CHARTAC_CLIENT_ID);
			header('Content-Type: application/json');
		    echo json_encode($res);
			}
	function load_report_emp_wrk_hrs(){
				$data=$this ->  authentication -> check_authentication();
				$data['user_result'] = $data['result'];
				$res = $this->Report_mdl->get_emp_wrk_hrs(CHARTAC_CLIENT_ID);
				header('Content-Type: application/json');
				echo json_encode($res);
				//echo $res;
				}
	function load_report_client_invoice(){
			    $data=$this ->  authentication -> check_authentication();
				$data['user_result'] = $data['result'];
				$res = $this->Report_mdl->get_client_invoice(CHARTAC_CLIENT_ID);
				header('Content-Type: application/json');
				echo json_encode($res);
				//echo $res;
				}
	function load_report_emp_estimate_wrk(){
				$data=$this ->  authentication -> check_authentication();
				$data['user_result'] = $data['result'];
				$res = $this->Report_mdl->get_emp_estimate_wrk(CHARTAC_CLIENT_ID);
				header('Content-Type: application/json');
				echo json_encode($res);
				//echo $res;
				}

	function load_report_access(){
		$id = $_POST['id'];
		$result = $this->Report_mdl->report_access($id);
		$newarray							=   array();
        if(count($result)>0) 
			foreach($result as $r)
			{
				$newarray[]	=$r->reportID;
			}
			$reports = $this->Report_mdl->getActiveReports();
		foreach($reports as $rr)
		{
			if(in_array($rr->reportID,$newarray)){
				$rr->status = 1;
			}
			else{
				$rr->status = 0;
			}
		}
		header('Content-Type: application/json');
		echo json_encode($reports);
	}

	function quick_report_list(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		// echo '<pre>';print_r($data);exit;
		$data['get_user_id']=$data['result']->user_id;
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Quick Reports', '/Chartac_admin/quick_report_list');   
		$data['reports'] = $this->Report_mdl->getActiveReports();
	    $data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Quick Reports";
		$data['page']="/chartac_admin/quick_report_list";
		$this->load->view('chartac_admin/index_page',$data);
	}
	function quick_report_listD3(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_user_id']=$data['result']->user_id;
		//$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		//$this->breadcrumbs->push('Quick Reports', '/edit_login/user_register');   
	    //$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Quick Reports D3[Test]";
		$data['page']="/chartac_admin/view_d3report";
		$this->load->view('chartac_admin/index_page',$data);
	}

	function access_report_list(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_users_data']=$this -> AdminChartac_mdl->get_all_user_data($chartac_client_id);
		// echo '<pre>';print_r($data['get_all_users_data']);exit;
		$data['get_user_id']=$data['result']->user_id;
		$data['get_all_users_data']=$this->Report_mdl->load_report_access($data['get_all_users_data']);
		// echo '<pre>';print_r($data['get_all_users_data']);exit;		
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Quick Reports', '/edit_login/user_register');   
		$data['viz'] = $this->Report_mdl->getActiveReports();
		// echo '<pre>';print_r($data['viz']);exit;
	    $data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Report Accessibility";
		$data['page']="/chartac_admin/access_report_list";
		$this->load->view('chartac_admin/index_page',$data);
	}

	function report_access_control(){
		echo $this->Report_mdl->setAccessControls($_POST['user_id'],$_POST['reports']);
	}
	/******************************************************************************** */

	function custom_report_list(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_user_id']=$data['result']->user_id;
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Quick Reports', '/edit_login/user_register');   
	    $data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Custom Reports";
		$data['page']="/chartac_admin/custom_report_list";
		$this->load->view('chartac_admin/index_page',$data);
		
	}
	
	//Create type of leaves
	function create_type_of_leaves(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data['checkLeaveCount']= $this -> AdminChartac_mdl->checkLeaveCount($chartac_client_id); 
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Type of leaves', '/Chartac_admin/leave_type_list');
		$this->breadcrumbs->push('Create Leave Type', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Create Leave Type";
		$data['flag_set']="CREATE";
		$data['page']="/chartac_admin/create_leave_type";
		$this->load->view('chartac_admin/index_page',$data);
	}
	
	
	function add_new_leave_type(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=array(
					'chartac_client_id'=>$chartac_client_id,
					'leave_type_title' => $_POST['leave_title'],
					'display_leave_title'=>$_POST['display_leave_title'],
					'no_of_leaves'=>$_POST['number_of_leaves'],
					'carry_forward'=>$_POST['carry_forward'],
					'status'=>$_POST['leave_type_status'],
					);
		if($_POST['add_new_leave_type_id']>0){
			$id	=	$_POST['add_new_leave_type_id'];
			$this -> AdminChartac_mdl->add_new_leave_type($id,$data);
		//redirect('/Chartac_admin/leave_type_list');
		echo $id;
		}else{
		$id = "";
          $id=$this -> AdminChartac_mdl->add_new_leave_type($id,$data);
		//redirect('/Chartac_admin/leave_type_list');
		//echo $id;
		echo 1;
		}			
	}	
	//Holiday list for Leave
	function getHolidayListForLeave(){
		$start= str_replace('/','-',$_POST['start']);	
		$holiday_date = strtotime($start);
 		$this ->AdminChartac_mdl->getHolidayListForLeave($holiday_date);
	}
	
	function delete_subservice_id(){
		$id = $_POST['subservice_id'];
		$this -> AdminChartac_mdl->delete_subservice_id($id);	
	}
	function getDesktopPushNotificationData(){
		$status = 0;//Fetch desktop notification
		$notification_data = $this -> AdminChartac_mdl->get_all_task_notifications($status);	
		echo json_encode($notification_data);
	}
	//Turn on and turn off desktop notification
	function turnon_desktop_notification(){
		$update_data = array('is_turn_on_notification_status'=>$_POST['status']);
		$user_id = $_POST['user_id'];
		$this -> AdminChartac_mdl->update_users_data($user_id,$update_data);
	}
	
	function update_desktop_push_notification(){
		$update_data = array('is_desktop_notification_seen'=>$_POST['status']);
		$notification_id = $_POST['notification_id'];
		$this -> AdminChartac_mdl->update_desktop_push_notification($notification_id,$update_data);
	}

	function close_multiple_tasks(){
		$updated_date = strtotime(date('d-m-Y'));
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$explode_task_id = explode(',',$_POST['multiple_task_ids']);
		$update_data =array();
		$data_multiple_task_updates = array();
		for($i=0;$i<count($explode_task_id);$i++){
			$update_data[$i] =  array(
				'task_id' => $explode_task_id[$i],
				'billing_task_status'=>$_POST['bulk_operation_status'],
				'updated_date'=>$updated_date,
				'task_status_id'=>$_POST['task_status']	);
			$task_result = $this -> AdminChartac_mdl -> get_task_data_for_closing_task($explode_task_id[$i]);

			$thread_data=array(
				'task_id'=>$explode_task_id[$i],
				'thread_priority_id'=>$task_result[0]->priority_id,
				'task_description'=>$task_result[0]->task_description,
				'user_id'=>USER_ID,
				'deadline'=>$task_result[0]->deadline,
				'reporter'=>$task_result[0]->reporter,
				'assignee'=>$task_result[0]->assignee,
				'task_status_id'=>4,
				'thread_created_date'=>$updated_date);

			$notification_data = array('generated_category_id'=>'1','generated_sub_category_id'=>104);
			$diff_flag = 0;
			$explode_assignee = explode(',',$task_result[0]->assignee);
			$this -> AdminChartac_mdl -> create_task_notification($explode_task_id[$i],$notification_data,$task_result[0]->reporter,$explode_assignee,$diff_flag,104);
			$array_data = array();
			$array_data['task_status']=4;
			$this -> AdminChartac_mdl -> update_multilple_thread_data($thread_data,$task_result[0]->task_status_id);
		}
		$this -> AdminChartac_mdl -> update_multiple_closing_task($update_data);
	}
	
	function check_user_old_password(){
	 
		$oldPassword = $_POST['old_pass'];
	 
		$userEmail = $_POST['userEmailId'];
		$old_password_count = $this -> AdminChartac_mdl -> get_user_old_password($oldPassword,$userEmail);
		echo $old_password_count[0]->old_password_count;	
	}
	function change_password(){
		if($_POST['new_password'] == $_POST['new_password']){
			$update_password = array('password'=>sha1($_POST['new_password']));
			$userEmail = $_POST['hiddenUserEmailId'];
			$this -> Login_mdl -> update_new_user_password($userEmail,$update_password);
		}
		redirect('/Chartac_admin/index');
	}
	function get_client_primary_contact_designation(){
		$ca_firm_id	=	$_POST['ca_firm_client_id'];
		$data['get_client_address']		=	$this -> AdminChartac_mdl->get_client_primary_contact_designation($ca_firm_id);
	}
	function get_employee_gender(){
		$user_id = $_POST['user_id'];
		$this -> AdminChartac_mdl -> get_employee_gender($user_id);
	}
	function load_template_by_type_selected(){
		$data['get_address_for_invoice'] = $this -> AdminChartac_mdl ->get_address_for_invoice();
		if($_POST['template_type'] == 1){
			$data['ca_firms_clients_list']	=	$this -> AdminChartac_mdl->get_all_clients_data();
			$data['ca_assoc_users_list']	=	$this -> AdminChartac_mdl->get_all_users(CHARTAC_CLIENT_ID);
			$html=$this->load->view('/pages/templates/pt_certificate', $data, true);
			echo $html;
		}
		else if($_POST['template_type'] == 2){
			$data['ca_firms_clients_list']	=	$this -> AdminChartac_mdl->get_all_clients_data();
			$data['ca_assoc_users_list']	=	$this -> AdminChartac_mdl->get_all_partner_lists(CHARTAC_CLIENT_ID);
			$html=$this->load->view('/pages/templates/power_of_attorney', $data, true);
			echo $html;
		}
	}

	function send_test_mail(){
		$data=$this ->  authentication -> check_authentication();
		$firm_name = $data['firm_name']; 
		$user_name = $_POST['hidden_admin_mail_id'];
		$password = $_POST['hidden_admin_password'];
		$test_mail = $_POST['test_mail']; 
		$this->email->send_user_test_mail($user_name,$password,$test_mail,$firm_name);
		redirect("/Chartac_admin/create_admin_email"); 
	}
	function  getDuplicateApplyLeaveList(){
		$start_date = $_POST['start'];
		$this -> AdminChartac_mdl->getDuplicateApplyLeaveList($start_date);
	}
	 
	function get_city_list_by_state_id(){
		$state_id = $_POST['state_id'];
		$getCityListByStateId	= $this->AdminChartac_mdl->getCityListByStateId($state_id);
		echo $getCityListByStateId;
	}	
	function add_update_multiple_address(){
		$data = array('title'=>$_POST['title'],
					'chartac_client_id'=>$_POST['chartac_client_id'],
					'house_number_name'=>$_POST['house_number_name'],
					'street'=>$_POST['street'],
					'state_id'=>$_POST['state_id'],
					'city_id'=>$_POST['city_id'],
					'postal_code'=>$_POST['postal_code'],
					'status' => 1		 );
		$id = $_POST['id'];
		$status = $this->AdminChartac_mdl->add_update_multiple_address($id,$data);
		if($status){
			echo true;
		}else{
			echo false;
		}
	}
	
	function emp_id_duplication(){
	
		$emp_id = htmlspecialchars($_POST['employee_id'],ENT_QUOTES,'UTF-8');
		$chartac_client_id = htmlspecialchars( $_POST['chartac_client_id'],ENT_QUOTES,'UTF-8');
		$email_result	=	$this->AdminChartac_mdl->get_employee_id_duplication($chartac_client_id,$emp_id);
	}

	function daily_activities_list(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_daily_activities_data']=$this -> AdminChartac_mdl->get_all_daily_activities_data(); 
		$data['title'] = "Daily Activity List";
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Daily Activity List', '/edit_login/user_register');   
		$data['bread_crumbs']=$this->breadcrumbs->show();

		$data['page']="/chartac_admin/daily_activity_list";
		$this->load->view('chartac_admin/index_page',$data);
	}
	
	function create_daily_activity(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
	
		if($this ->uri->segment(3)!=""){
			$id	=  encryptor('decrypt', $this ->uri->segment(3));
			$data['get_daily_activities_data_by_id']=$this -> AdminChartac_mdl->get_daily_activities_data_by_id($id);
			$data['daily_activity_master_data']	=	$this -> AdminChartac_mdl->get_daily_activity_master_data();
			//print_r($data['get_sub_services_list_by_id']);exit;
			$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
			$this->breadcrumbs->push('Daily Activity List', '/Chartac_admin/daily_activities_list');
			$this->breadcrumbs->push('Update Daily Activity List"', '/edit_login/user_register');
			$data['bread_crumbs']=$this->breadcrumbs->show();
			$data['title']="Update Daily Activity";
			$data['flag_set']="UPDATE";
			$data['page']="/chartac_admin/create_daily_activity_list";
			$this->load->view('chartac_admin/index_page',$data);
		}else{
			$data['daily_activity_master_data']	=	$this -> AdminChartac_mdl->get_daily_activity_master_data();
			$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
			$this->breadcrumbs->push('Daily Activity List', '/Chartac_admin/daily_activities_list');
			$this->breadcrumbs->push('Create Daily Activity List"', '/edit_login/user_register');
			$data['bread_crumbs']=$this->breadcrumbs->show();
			$data['title']="Create Daily Activity List";
			$data['flag_set']="CREATE";
			$data['page']="/chartac_admin/create_daily_activity_list";
			$this->load->view('chartac_admin/index_page',$data);
		}
	
	}
	
	
	function create_update_activity_data(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=array(
				'chartac_client_id'=>$chartac_client_id,
				'daily_activity_id'=>$_POST['select_activity_data'],
				'time'=>$_POST['activity_time'], 
				'status'=>$_POST['type_of_activity_status']
		);
		 
		if($_POST['create_update_activity_data_id']>0){
			$id	=	$_POST['create_update_activity_data_id'];
			$this -> AdminChartac_mdl->add_update_config_activity_data($id,$data);
			//redirect('/Chartac_admin/daily_activities_list');
		    echo $id;
		}else{
			$id="";
			$id = $this -> AdminChartac_mdl->add_update_config_activity_data($id,$data);
			//redirect('/Chartac_admin/daily_activities_list');
		    echo $id=1; 
		}
	
	}
	
	function get_all_email_address(){
		$sub_service_id = $_POST['sub_service_id'];
		$this -> AdminChartac_mdl->get_all_email_address_count($sub_service_id);		
 	}
 	
 	
 	function get_all_firm_email_address(){
 		$selected_firm_id = $_POST['selected_firm_id'];
 		$this -> AdminChartac_mdl->get_all_firm_email_address_count($selected_firm_id); 		
 	}
 	
 	function select_quick_all_clients_email_count(){
 		
 		$chartac_client_id = $_POST['chartac_client_id'];
 		$status = $_POST['selected_status']; 
 		$this -> AdminChartac_mdl->select_quick_all_clients_email_count($chartac_client_id,$status); 
 	}
 	
 	function getUserLeaveForTaskCreation(){
 		$d_date = strtotime(str_replace('/','-',$_POST['deadline_date'])); 
 		$dead_line_date = date('Y-m-d',$d_date);
 		$this -> AdminChartac_mdl -> getUserLeaveForTaskCreationData($dead_line_date);
 		 
 	}
 	
 	function getUserTaskOnLeaveDate(){
 		$this ->  AdminChartac_mdl -> getUserTaskOnLeaveDateDetails();
 		 
 	}
 	
 	
 	function list_clients(){
 		$data=$this ->  authentication -> check_authentication();
 		$data['user_result'] = $data['result'];
 		$string = $this -> uri->segment(3);	
 		$str_count = strlen( $this -> uri->segment(3) );
 	 	  $uri_id =substr($string, 1,$str_count-1);
 		 $selected_data = substr($string, 0,1); 
 		 $data['get_all_clients_data']=$this -> AdminChartac_mdl->get_clients_list_based_industries_admin($selected_data,$uri_id);
 	 	$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
 		$this->breadcrumbs->push('View Clients', '/edit_login/user_register');
 		$data['bread_crumbs']=$this->breadcrumbs->show();
 		$data['title']="View Clients ";
 		$data['page']='/chartac_admin/view_all_clients';
 		$this->load->view('chartac_admin/index_page',$data);
 	}
 	function check_user_unique_duplication(){
 		$this -> AdminChartac_mdl->check_user_unique_duplication();
 	}
 	
 	function task_access_control(){
 		$chartac_client_id = CHARTAC_CLIENT_ID; 
 		$data=$this ->  authentication -> check_authentication();
 		$data['user_result'] = $data['result'];
 		$data['partner_result'] = $this -> AdminChartac_mdl->get_task_access_control($chartac_client_id); 
 		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i> Home', '/Chartac_admin/index'); 
		$this->breadcrumbs->push('Task Access Control', '/edit_login/user_register');   
		$data['bread_crumbs']=$this->breadcrumbs->show(); 
 		$data['title']="Task Access Control";
 		$data['page']='/chartac_admin/task_access_control';
 		$this->load->view('chartac_admin/index_page',$data); 
 	}
 	
 	function update_task_access_users(){ 
 		$this -> AdminChartac_mdl->update_task_access_users(); 
 	}
 	
 	function disableClientsGetTaskCount(){
 		 $this -> AdminChartac_mdl->disableClientsGetTaskCount();
 		 
	 }

	 function register_quick_client(){
    
		$is_quick_client = 2; 
    	$chartac_client_id = CHARTAC_CLIENT_ID;
    	$data = array(		'chartac_client_id'				=>	$chartac_client_id,
							'partner_id' 					=> 	$_POST['partner_id'], 
							'place_of_supply_name' 			=> 	$_POST['place_of_supply_name'],
							'place_of_supply_code'			=> 	$_POST['place_of_supply_code'],
							'tally_reference_client_name' 	=> 	$_POST['tally_reference_client_name'],
							'tally_reference_for_advance' 	=> 	$_POST['tally_reference_for_advance'],
							'first_name'					=> 	$_POST['first_name'],
							'email'	   						=> 	$_POST['email'],
							'contact_person_number'			=>	$_POST['contact_person_number'],
							'company_name' 					=> 	$_POST['company_name'],
							'is_quick_client' 				=>	$is_quick_client,
							'type_of_client_ids'			=>	$_POST['type_of_client_id'],
							'status'						=>	$_POST['r3'],
							'created_by'					=>	$_SESSION['last_user_id'],
							'type_of_creation'				=>	1,
					);
					// echo '<pre>';print_r($_POST);
					// echo '<pre>';print_r($data);
					// exit;
	
     if($_POST['create_quick_client_id']>0){
    	
			$id1 = $_POST['create_quick_client_id']; 
     	//	$id = encryptor('decrypt', $id1);
    		$this -> Clients_mdl->insert_new_clients($data,$id1);
			echo $id1;
    	}else{
    		
			$result 	=	$this -> Clients_mdl->check_quick_clients($_POST['email'],$_POST['contact_person_number']);
			// print_r($result);exit;
			// print_r($_POST); 
			
			if(empty($result)){
				$id = "";
				$id=	$this -> Clients_mdl->insert_new_clients($data,$id);
				echo $id;
			}else{
				echo "Mobile or Email Id Already Registered";
			}
		}
    	//redirect('/Clients/view_all_clients');
    
    }
	 
	 function email_configuration(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$id="";
		$data['get_all_email_configuration_list'] = $this -> AdminChartac_mdl->get_email_configuration_data($id);
		$decryptPassword = $this->decryptText($data['get_all_email_configuration_list'][0]->password);
		//decryptPassword will contains 16 bytes string to remove extra spaces use this method preg_replace("/\s+|[[:^print:]]/", "",$value);
		$data['get_all_email_configuration_list'][0]->password=encryptor('encrypt',$decryptPassword);
		if(count($data['get_all_email_configuration_list']) > 0){
		// if(0){			
			$this->breadcrumbs->push('Edit Email Configuration', '/edit_login/user_register');
			$data['title']="Edit Email Configuration";
			// print_r($data['get_all_email_configuration_list'][0]);exit;
			$data['flag_set'] = "UPDATE";
		}else{
			$this->breadcrumbs->push('Email Configuration', '/edit_login/user_register');
			$data['title']="Email Configuration";
			$data['flag_set']="CREATE";
		}
		 
		$data['bread_crumbs']=$this->breadcrumbs->show();
		// echo '<pre>';print_r($data);exit;
		$data['page']="/chartac_admin/email_configuration";
		$this->load->view('chartac_admin/index_page',$data);

	}

	 function add_new_email_configuration(){
		$chartac_client_id=CHARTAC_CLIENT_ID;
		// echo '<pre>';print_r($_POST);
		$encryptPassword=$this->encryptText($_POST['pwd']);
		$data=array(
					'chartac_client_id'=>$chartac_client_id,
					'host'=>$_POST['host'],
					'email'=>$_POST['email'],
					'password'=>$encryptPassword,
					'port'=>$_POST['port'],
					'smtpsecure'=>$_POST['smtp_checkbox'],
					'smtpsecuremode'=>$_POST['type_of_secures_mode'],
					'status'=>$_POST['configsettings']
					);
					// $id= $data['chartac_client_id'];
					// echo "here";
					// print_r($this ->uri->segment(3));
					// print_r($data);exit;
			if($this ->uri->segment(3)!=""){
			$id	=	$this ->uri->segment(3);
			$this -> AdminChartac_mdl->create_new_email_config($id,$data);
			$message = 'Email configuration are  updated successfully!';
			$this->session->set_flashdata('suc', $message);
			redirect('/Chartac_admin/email_configuration');	
			
			}else{
			$id = "";

			$res = $this -> AdminChartac_mdl->create_new_email_config($id,$data);
			$message = 'Email configuration are  added successfully!';
			$this->session->set_flashdata('suc', $message);
			redirect('/Chartac_admin/email_configuration');
			}		
	}

			/**
	 * 
	 * 		Todo : 
	 * 		custom encrypt and decrypt is in my_email,send_email_queue,Chartac_admin make this as one class and call the method
	 * 
	 */
	function encryptText($plaintext){
		$key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
		# show key size use either 16, 24 or 32 byte keys for AES-128, 192
		# and 256 respectively
		$key_size =  strlen($key);
		# create a random IV to use with CBC encoding
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
						 $plaintext, MCRYPT_MODE_CBC, $iv);
		# prepend the IV for it to be available for decryption
		$ciphertext = $iv . $ciphertext;
		# encode the resulting cipher text so it can be represented by a string
		$ciphertext_base64 = base64_encode($ciphertext);
		return $ciphertext_base64;
	}
	function decryptText($ciphertext_base64){
		$key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");

		# show key size use either 16, 24 or 32 byte keys for AES-128, 192
		# and 256 respectively
		$key_size =  strlen($key);
		# create a random IV to use with CBC encoding
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$ciphertext_dec = base64_decode($ciphertext_base64);
		
		# retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
		$iv_dec = substr($ciphertext_dec, 0, $iv_size);
		
		# retrieves the cipher text (everything except the $iv_size in the front)
		$ciphertext_dec = substr($ciphertext_dec, $iv_size);
	
		# may remove 00h valued characters from end of plain text
		$plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
										$ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

		return $plaintext_dec;
	}
	
	function user_task_accessibility(){
		
		if(isset($_POST['user_id'])){ 
				$user_id								=	$_POST['user_id'];   
				
				
				if($_POST['is_rt_delete']=='on'){
					$is_rt_delete			=	1;
				}
				
				if($_POST['is_nt_access']=='on'){
					$is_nt_access			=	1;
				}
				else{
					$is_nt_access			=	0;
				} 
				if($_POST['is_rt_access']=='on'){
					$is_rt_access			=	1;
				}else{
					$is_rt_access			=	0;
				}
				if($_POST['is_nt_create_access']=='on'){
					$is_nt_create_access	=	1;
				}else{
					$is_nt_create_access	=	0;
				}
				if($_POST['is_nt_edit_assignee_access']=='on'){
					$is_nt_edit_assignee_access		=	1;
				}else{
					$is_nt_edit_assignee_access		=	0;
				}
				if($_POST['is_nt_edit_reporter_access']=='on'){
					$is_nt_edit_reporter_access		=	1;
				}else{
					$is_nt_edit_reporter_access		=	0;
				}
				if($_POST['is_nt_edit_duedate_access']=='on'){
					$is_nt_edit_duedate_access		=	1;
				}else{
					$is_nt_edit_duedate_access		=	0;
				}
				if($_POST['is_nt_edit_task_status_access']=='on'){
					$is_nt_edit_task_status_access	=	1;
				}else{
					$is_nt_edit_task_status_access	=	0;
				}
				if($_POST['is_rt_create_access']=='on'){
					$is_rt_create_access			=	1;
				}else{
					$is_rt_create_access			=	0;
				}
				if($_POST['is_rt_edit_assignee_access']=='on'){
					$is_rt_edit_assignee_access		=	1;
				}else{
					$is_rt_edit_assignee_access		=	0;
				}
				if($_POST['is_rt_edit_reporter_access']=='on'){
					$is_rt_edit_reporter_access		=	1;
				}else{
					$is_rt_edit_reporter_access		=	0;
				}
				if($_POST['is_rt_edit_duedate_access']=='on'){
					$is_rt_edit_duedate_access		=	1;
				}else{
					$is_rt_edit_duedate_access		=	0;
				}
				if($_POST['is_rt_stop_reccuring']=='on'){
					$is_rt_stop_reccuring	=	1;
				}else{
					$is_rt_stop_reccuring	=	0;
				}
				if($_POST['is_rt_edit_recurring_frequency_access']=='on'){
					$is_rt_edit_recurring_frequency_access	=	1;
				}else{
					$is_rt_edit_recurring_frequency_access	=	0;
				}
				if($_POST['is_rt_edit_create_before_access']=='on'){
					$is_rt_edit_create_before_access		=	1;
				}else{
					$is_rt_edit_create_before_access		=	0;
				}
				
				if($_POST['is_nt_st_create']=='on'){
					$is_nt_st_create		=	1;
				}else{
					$is_nt_st_create		=	0;
				}
				
				
				if($_POST['is_nt_st_create_in_edit']=='on'){
					$is_nt_st_create_in_edit		=	1;
				}else{
					$is_nt_st_create_in_edit		=	0;
				}
				if($_POST['is_nt_st_edit_update']=='on'){
					$is_nt_st_edit_update		=	1;
				}else{
					$is_nt_st_edit_update		=	0;
				}
				if($_POST['is_nt_st_delete']=='on'){
					$is_nt_st_delete		=	1;
				}else{
					$is_nt_st_delete		=	0;
				}
				if($_POST['is_nt_st_lock_check']=='on'){
					$is_nt_st_lock_check		=	1;
				}else{
					$is_nt_st_lock_check		=	0;
				}
				if($_POST['is_nt_st_title']=='on'){
					$is_nt_st_title		=	1;
				}else{
					$is_nt_st_title		=	0;
				}		 
				if($_POST['is_nt_st_status']=='on'){
					$is_nt_st_status		=	1;
				}else{
					$is_nt_st_status		=	0;
				}		 
				if($_POST['is_nt_st_assignee']=='on'){
					$is_nt_st_assignee		=	1;
				}else{
					$is_nt_st_assignee		=	0;
				}
				if($_POST['is_nt_st_reporter']=='on'){
					$is_nt_st_reporter		=	1;
				}else{
					$is_nt_st_reporter		=	0;
				} 
				
				if($_POST['is_nt_st_duedate']=='on'){
					$is_nt_st_duedate		=	1;
				}else{
					$is_nt_st_duedate		=	0;
				} 
				if($_POST['is_rt_st_create']=='on'){
					$is_rt_st_create		=	1;
				}else{
					$is_rt_st_create		=	0;
				} 
				
				
				if($_POST['is_rt_st_create_in_edit']=='on'){
					$is_rt_st_create_in_edit		=	1;
				}else{
					$is_rt_st_create_in_edit		=	0;
				} 
				 
				if($_POST['is_rt_st_edit_update']=='on'){
					$is_rt_st_edit_update		=	1;
				}else{
					$is_rt_st_edit_update		=	0;
				} 
				if($_POST['is_rt_st_delete']=='on'){
					$is_rt_st_delete		=	1;
				}else{
					$is_rt_st_delete		=	0;
				}  
				if($_POST['is_rt_st_lock_check']=='on'){
					$is_rt_st_lock_check		=	1;
				}else{
					$is_rt_st_lock_check		=	0;
				} 
				if($_POST['is_rt_st_title']=='on'){
					$is_rt_st_title		=	1;
				}else{
					$is_rt_st_title		=	0;
				}  
				 

				if($_POST['is_rt_st_assignee']=='on'){
					$is_rt_st_assignee		=	1;
				}else{
					$is_rt_st_assignee		=	0;
				}  
				if($_POST['is_rt_st_reporter']=='on'){
					$is_rt_st_reporter		=	1;
				}else{
					$is_rt_st_reporter		=	0;
				} 
				if($_POST['is_rt_st_status']=='on'){
					$is_rt_st_status		=	1;
				}else{
					$is_rt_st_status		=	0;
				} 
				
				
				if($_POST['is_nt_file_upload_access']=='on'){
					$is_nt_file_upload_access			=	1;
				}
  
				if($_POST['is_nt_st_file_upload']=='on'){
					$is_nt_st_file_upload			=	1;
				}

  
						
			  $data=array(
						'is_rt_delete'							=>	$is_rt_delete,
						'is_nt_access'							=>	$is_nt_access,
						'is_rt_access' 							=>	$is_rt_access, 
						'is_nt_create_access'					=>	$is_nt_create_access,
						'is_nt_edit_assignee_access'			=>	$is_nt_edit_assignee_access,
						'is_nt_edit_reporter_access'			=>	$is_nt_edit_reporter_access,
						'is_nt_edit_duedate_access'				=>	$is_nt_edit_duedate_access,
						'is_nt_edit_task_status_access'			=>	$is_nt_edit_task_status_access,
						'is_rt_create_access'					=>	$is_rt_create_access,
						'is_rt_edit_assignee_access'			=>	$is_rt_edit_assignee_access,
						'is_rt_edit_reporter_access'			=>	$is_rt_edit_reporter_access,
						'is_rt_edit_duedate_access'				=>	$is_rt_edit_duedate_access,
						'is_rt_stop_reccuring'					=>	$is_rt_stop_reccuring,
						'is_rt_edit_recurring_frequency_access'	=>	$is_rt_edit_recurring_frequency_access,
						'is_rt_edit_create_before_access'		=>	$is_rt_edit_create_before_access, 
						'is_nt_st_create'						=>	$is_nt_st_create,
						'is_nt_st_create_in_edit'				=>	$is_nt_st_create_in_edit,
						'is_nt_st_edit_update'					=>	$is_nt_st_edit_update,
						'is_nt_st_delete'						=>	$is_nt_st_delete,
						'is_nt_st_lock_check'					=>	$is_nt_st_lock_check,
						'is_nt_st_title'						=>	$is_nt_st_title,
						'is_nt_st_status'						=>	$is_nt_st_status,
						'is_nt_st_assignee'						=>	$is_nt_st_assignee,
						'is_nt_st_reporter'						=>	$is_nt_st_reporter,
						'is_nt_st_duedate'						=>	$is_nt_st_duedate,
						'is_rt_st_create'						=>	$is_rt_st_create,
						'is_rt_st_create_in_edit'				=>	$is_rt_st_create_in_edit,
						'is_rt_st_edit_update'					=>	$is_rt_st_edit_update,
						'is_rt_st_delete'						=>	$is_rt_st_delete,
						'is_rt_st_lock_check'					=>	$is_rt_st_lock_check,
						'is_rt_st_title'						=>	$is_rt_st_title, 
						'is_rt_st_assignee'						=>	$is_rt_st_assignee,
						'is_rt_st_reporter'						=>	$is_rt_st_reporter,
						'is_rt_st_status'						=>	$is_rt_st_status,
						'is_nt_file_upload'						=>	$is_nt_file_upload_access,
						'is_nt_st_file_upload'					=>	$is_nt_st_file_upload,
					);
					
					// echo $user_id;
					// echo '<pre>';print_r($data);exit;
					
					$this -> AdminChartac_mdl->update_user_task_access_control_data($user_id,$data);	 
		
		
		}
		
		
		$chartac_client_id=CHARTAC_CLIENT_ID;
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];
		$data['get_all_users_data']=$this -> AdminChartac_mdl->get_all_users_data_for_task_access($chartac_client_id);	
		$this->breadcrumbs->push('<i class="fa fa-dashboard"></i>  Home', '/Chartac_admin/index');
		$this->breadcrumbs->push('Tasks Accessibility', '/edit_login/user_register');
		$data['bread_crumbs']=$this->breadcrumbs->show();
		$data['title']="Tasks Accessibility";
		$data['access_mode']=0;
		$data['page']="/chartac_admin/user_access_for_tasks";
		$this->load->view('chartac_admin/index_page',$data);
	}

	function bulk_task_control_access(){
		$data=$this ->  authentication -> check_authentication();
		$data['user_result'] = $data['result'];   
		
		 // echo '<pre>';print_r($_POST);exit;
				 
	
	
		$update_data=array(
						'is_task_access'						=>	$_POST['task_access_control_bulk'],
						'is_nt_access'							=>	$_POST['is_nt_access_bulk'],
						'is_rt_access' 							=>	$_POST['is_rt_access_bulk'], 
						'is_nt_create_access'					=>	$_POST['is_nt_create_access_bulk'],
						'is_nt_edit_assignee_access'			=>	$_POST['is_nt_edit_assignee_access_bulk'],
						'is_nt_edit_reporter_access'			=>	$_POST['is_nt_edit_reporter_access_bulk'],
						'is_nt_edit_duedate_access'				=>	$_POST['is_nt_edit_duedate_access_bulk'],
						'is_nt_edit_task_status_access'			=>	$_POST['is_nt_edit_task_status_access_bulk'],
						'is_rt_create_access'					=>	$_POST['is_rt_create_access_bulk'],
						'is_rt_edit_assignee_access'			=>	$_POST['is_rt_edit_assignee_access_bulk'],
						'is_rt_edit_reporter_access'			=>	$_POST['is_rt_edit_reporter_access_bulk'],
						'is_rt_edit_duedate_access'				=>	$_POST['is_rt_edit_duedate_access_bulk'],
						'is_rt_stop_reccuring'					=>	$_POST['is_rt_edit_task_status_access_bulk'],
						'is_rt_edit_recurring_frequency_access'	=>	$_POST['is_rt_edit_recurring_frequency_access_bulk'],
						'is_rt_edit_create_before_access'		=>	$_POST['is_rt_edit_create_before_access_bulk'],
						'is_nt_st_create'						=>	$_POST['is_nt_st_create_bulk'],
						'is_nt_st_create_in_edit'				=>	$_POST['is_nt_st_create_in_edit_bulk'],
						'is_nt_st_edit_update'					=>	$_POST['is_nt_st_edit_update_bulk'],
						'is_nt_st_delete'						=>	$_POST['is_nt_st_delete_bulk'],
						'is_nt_st_lock_check'					=>	$_POST['is_nt_st_lock_check_bulk'],
						'is_nt_st_title'						=>	$_POST['is_nt_st_title_bulk'],
						'is_nt_st_status'						=>	$_POST['is_nt_st_status_bulk'],
						'is_nt_st_assignee'						=>	$_POST['is_nt_st_assignee_bulk'],
						'is_nt_st_reporter'						=>	$_POST['is_nt_st_reporter_bulk'],
						'is_nt_st_duedate'						=>	$_POST['is_nt_st_duedate_bulk'],
						'is_rt_st_create'						=>	$_POST['is_rt_st_create_bulk'],
						'is_rt_st_create_in_edit'				=>	$_POST['is_rt_st_create_in_edit_bulk'],
						'is_rt_st_edit_update'					=>	$_POST['is_rt_st_edit_update_bulk'],
						'is_rt_st_delete'						=>	$_POST['is_rt_st_delete_bulk'],
						'is_rt_st_lock_check'					=>	$_POST['is_rt_st_lock_check_bulk'],
						'is_rt_st_title'						=>	$_POST['is_rt_st_title_bulk'], 
						'is_rt_st_assignee'						=>	$_POST['is_rt_st_assignee_bulk'],
						'is_rt_st_reporter'						=>	$_POST['is_rt_st_reporter_bulk'],
						'is_rt_st_status'						=>	$_POST['is_rt_st_status_bulk'],
						'is_nt_file_upload'						=>	$_POST['is_nt_file_upload_bulk'],
						'is_nt_st_file_upload'					=>	$_POST['is_nt_st_file_upload_bulk'],
						'is_rt_delete'							=>	$_POST['is_rt_delete_bulk'],
					);
		$ids	=	explode(",",$_POST['multiple_user_ids']);
		
		
		// echo '<pre>';print_r($update_data);exit;
		for($i=0;$i<count($ids);$i++){ 
			$res	= $this -> AdminChartac_mdl->bulk_task_control_access($ids[$i],$update_data); 
		} 
		// echo '<pre>';print_r($ids);exit;
		
	}
	
	function checkSMTPConnection(){
		$client_name = $this->AdminChartac_mdl->get_ca_clients_data(CHARTAC_CLIENT_ID);
		if(!$this->email->send_user_test_mail($_POST,$client_name[0]->company)){
			echo 0;
		}else{
			echo 1;
		}
		
	}
	
	
}