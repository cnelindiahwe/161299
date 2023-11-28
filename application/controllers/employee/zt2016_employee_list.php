<?php

class zt2016_employee_list extends MY_Controller {

	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		//$this->load->helper(array('form','url','general','userpermissions'));
		
		$this->load->helper(array('form','url','general','userpermissions'));
		
		$zowuser=_superuseronly(); 
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		$templateData['title'] = 'Users';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_users($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData); 


	}
	

	// ################## display clients info ##################	
	function  _display_users($ZOWuser)
	{
					

		#load groups info	
		$this->load->model('zt2016_users_model', '', TRUE);
		//$GroupsData = $this->zt2016_groups_model->GetUser($options = array('Trash'=>'0','sortBy'=>'GroupName','sortDirection'=>'ASC	'));
		$UsersData = $this->zt2016_users_model->GetUser();
		
		
		#Create page
		$page_content ="\n";
		
		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			$page_content.='<div class="alert alert-success" role="alert" style="">'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			$page_content.='<div class="alert alert-danger" role="alert" style="">'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}
		
		
		######### panel header
		$page_content.='<div class="panel panel-info"><div class="panel-heading">'."\n"; 
		$page_content.='<h4>'.count($UsersData)." existing employees";

		
		########## New group button
		//$page_content.= '<a href="'.site_url().'groups/zt2016_group_new'.'" class="btn btn-success btn-sm pull-right">New Group</a>'."\n";


		$page_content.="</h4>\n";
		$page_content.='<a href="javascript:void(0);" class="btn add-btn add_user_btn" style="float: right;margin-top: -32px;">Add User</a>';
		$page_content.="<div class='clearfix'></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body">'."\n";
		// $page_content.='<div id="table_loading_message">Loading ... </div>'."\n";

		
		#fetch users table
		$page_content .= $this-> _users_table($UsersData)	;		


		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";


  		if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="jirka.blom") {
  			
  		}

		return $page_content;

	}	


	// ################## create users table ##################	
	function   _users_table($UsersData)
	{

		$UsersTable ='<div class="row filter-row mt-5">
		<div class="col-sm-6 col-md-3">  
			<div class="form-group form-focus">
				<input type="text" class="form-control floating fbn">
				<label class="focus-label ">Name</label>
			</div>
		</div>
		
		<div class="col-sm-6 col-md-3"> 
			<div class="form-group custom-select">
				<select class="select floating select2-hidden-accessible fbr" data-select2-id="select2-data-4-cx14" tabindex="-1" aria-hidden="true"> 
					<option value="" data-select2-id="select2-data-6-7lj6">Select Role</option>
					<option value="Normal">Normal</option>
					<option value="Manager">Manager</option>
				</select>
			</div>
		</div>
		<div class="col-sm-6 col-md-3"> 
		</div>
		<div class="col-sm-6 col-md-3">  
			<a href="javascript:void(0);" class="btn btn-success w-100 search_filter"> Search </a>  
		</div>     
	</div>
	<div class="table-responsive"><table class="table table-striped custom-table  zwt_dataTable no-footer" style="width:100%" id="">'."\n";
		$UsersTable .="<thead><tr><th data-sortable=\"true\">ID</th><th data-sortable=\"true\">Name</th><th data-sortable=\"true\">Email</th><th data-sortable=\"true\">Role</th><th data-sortable=\"true\">Created Date</th><th data-sortable=\"true\"> Action </th></thead>\n";
		//$UsersTable .="<tfoot><tr><th data-sortable=\"true\">ID</th><th data-sortable=\"true\">User</th><th data-sortable=\"true\"Since</th><th data-sortable=\"true\">Last Login</th></tfoot>\n";
		$UsersTable .="<tbody>\n";
		
		
		foreach($UsersData as $UserDetails)
		{
			if($UserDetails->dp == ''){
				$path = base_url().'web/assets/usersprofile/u_dafault.png';
			}else{
				$path = base_url().'web/assets/usersprofile/'.$UserDetails->dp;

			}
			if($UserDetails->user_type == 1){
				$role ='bg-inverse-info';
				$role_name ='Normal';
			}else{
				$role ='bg-inverse-danger';
				$role_name ='Manager';


			}
			$UsersTable .="<tr>\n";
			$UsersTable .="<td>".$UserDetails->user_id."</td>\n";
			$UsersTable .='<td><a href="'.base_url().'employee/zt2016_employee_profile/'.$UserDetails->user_id.'" class="avatar"><img src="'.$path.'" alt=""></a>'.$UserDetails->fname.' '.$UserDetails->lname.'</td>';
			$UsersTable .="<td>".$UserDetails->user_email."</td>\n";
			$UsersTable .='<td><span class="badge '.$role.'" style="display: inline-block !important;">'.$role_name.'</span></td>';
			$UsersTable .="<td>".date("d-m-Y", strtotime($UserDetails->user_date))."</td>\n";
			$UsersTable .='<td class="text-end">
			<div class="dropdown dropdown-action">
				<a href="javascript:void(0);" class="action-icon  dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="true"><i class="material-icons">more_vert</i></a>
				<div class="dropdown-menu dropdown-menu-right" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);" data-popper-placement="bottom-end">
					<a class="dropdown-item" href="'.base_url().'employee/zt2016_employee_profile/'.$UserDetails->user_id.'"  data-id="'.$UserDetails->user_id.'" ><i class="fa fa-pencil m-r-5"></i> Edit</a>
					<a class="dropdown-item delete_hsd" href="javascript:void(0);"   data-id="'.$UserDetails->user_id.'" ><i class="fa fa-trash-o m-r-5"></i> Delete</a>
				</div>
			</div></td>';		
			$UsersTable .="</tr>\n";
			
		}
		
		
		$UsersTable .="</tbody>\n";
		$UsersTable .="</table></div>\n";




		return $UsersTable;
	
	}
public function delete_user(){
	 $this->uri->segment(3);
	$this->load->model('zt2016_users_model', '', TRUE);
	
	 $data_delete = $this->zt2016_users_model->deleteUser(['user_id' => $this->uri->segment(3)]);
	
	if($data_delete){
		$this->session->set_flashdata('SuccessMessage','Success: User delete successfully...');
			
		
		redirect('user');
	}else{
		redirect('user');
	}
			$this->load->view('users/edit_user',$data);
}

	public function edit_user(){
// 	    error_reporting(E_ALL);
// ini_set('display_errors', '1');
        $this->load->helper(array('form','url','general','userpermissions'));
		$this->load->library('SimpleLoginSecure');
		$this->load->model('zt2016_users_model', '', TRUE);
		$this->load->library('form_validation');
		if(!empty($this->input->post('email'))){
				$dp =$_POST['old_user_pic'] ? str_replace('_!r!_','.',$_POST['old_user_pic']) : '';

			 $user_id = $this->input->post('user_id');
			  $email = $this->input->post('email');
			  $user_type = $this->input->post('user_type')?:'';
			  $visibility = $this->input->post('visibility')?:0;
			$fname = $this->input->post('fname');
			$lname = $this->input->post('lname');
			$pass = $this->input->post('pass');
			
			if(isset($_FILES['add_user_pic']) && !empty($_FILES['add_user_pic']['name'])){ 
				$profile_path = FCPATH.'web/assets/usersprofile/';
				$ext = pathinfo($_FILES['add_user_pic']['name'], PATHINFO_EXTENSION);
				$file_name = $fname.'_'.$lname.'_'.rand().'.'.$ext;
			
				$file_tmp = $_FILES['add_user_pic']['tmp_name'];
				move_uploaded_file($file_tmp, $profile_path.$file_name);
				$dp = $file_name;
			 }
			
			$option=array(
				'user_id' => $user_id,
				'fname' => $fname,
				'lname' => $lname,
				'user_email' => $email,
				'user_type' => $user_type,
				'visibility' => $visibility,
				'dp' => $dp
			);
		    $this->form_validation->set_rules('email', 'Email', 'required|min_length[4]|max_length[255]|valid_email');

			if(isset($pass) && !empty($this->input->post('pass'))){
				$option['user_pass'] =md5($this->input->post('pass'));
				$this->form_validation->set_rules('pass', 'Password', 'required|min_length[4]|max_length[255]');
			}
			if(isset($pass) && !empty($this->input->post('pass'))){
			
				$option['user_pass'] =md5($this->input->post('pass'));
				
				
			}
			 if ($this->form_validation->run() == false) {
				// echo 'validation error';	
				unlink($profile_path.'/'.$file_name);
				$this->session->set_flashdata('ErrorMessage',validation_errors());
				redirect('user/edit/'.$user_id.'?time='.time());
			}else{
			    $this->db->where('user_id',$user_id);
                $query_c=$this->db->get('users');
            if($query_c->result()[0]->user_email != $option['user_email']){
                $this->CI =& get_instance();
        		$this->CI->db->where('user_email', $option['user_email']); 
        		$this->CI->db->where('status', 0); 
        		$query = $this->CI->db->get_where('users');
        		if ($query->num_rows() > 0){
        		    $this->session->set_flashdata('ErrorMessage','Error: Email is already exist...');
        				redirect('user/edit/'.$user_id.'?time='.time());
        		}
            }
			    
        
			    $update = $this->zt2016_users_model->UpdateUser_data($option);
			
			if($update){
			$this->session->set_flashdata('SuccessMessage','Success: User edit successfully...');
				redirect('user');
			}else{
				$this->session->set_flashdata('ErrorMessage','Error:Something Went Wrong(Need To Update ) ...');
				redirect('user/edit/'.$user_id.'?time='.time());
			}
		}
		}else{
			$this->load->helper(array('form','url','general','userpermissions'));
			$data_get = $this->zt2016_users_model->get_single_user($this->uri->segment(3));
			$data['user_id'] =$this->uri->segment(3);
			$data['ZOWuser'] = _getCurrentUser();
		$data['result'] = $data_get[0];
		$data['title'] = 'Users';
			$this->load->view('users/edit_user',$data);
		}
		

	}
	public function adduser()
	{
		
		
		$this->load->model('zt2016_users_model', '', TRUE);
		$this->load->helper(array('form','url','general','userpermissions'));

		$this->load->library('SimpleLoginSecure');
		if(!empty($this->input->post('email')) && !empty($this->input->post('pass'))){
			$fname = $this->input->post('fname');
			 $lname = $this->input->post('lname');
			 $visibility = $this->input->post('visibility')?:1;
			 $user_type = $this->input->post('user_type');
			 $dp = '';
			 if(isset($_FILES['add_user_pic']) && !empty($_FILES['add_user_pic']['name'])){ 
				$profile_path = FCPATH.'web/assets/usersprofile/';
				$ext = pathinfo($_FILES['add_user_pic']['name'], PATHINFO_EXTENSION);
				$file_name = $fname.'_'.$lname.'_'.rand().'.'.$ext;
				$file_tmp = $_FILES['add_user_pic']['tmp_name'];
				move_uploaded_file($file_tmp, $profile_path.$file_name);
				$dp = $file_name;
			 }

		$email = $this->input->post('email');
		 $user_pass = $this->input->post('pass');
		 $this->load->library('form_validation');
			
			//validate incoming variables
			$this->form_validation->set_rules('email', 'Email', 'required|min_length[4]|max_length[255]|valid_email');
			$this->form_validation->set_rules('pass', 'Password', 'required|min_length[4]|max_length[255]');

				
			if ($this->form_validation->run() == false) {
				// echo 'validation error';	
				unlink($profile_path.'/'.$file_name);
				$this->session->set_flashdata('ErrorsMessage',validation_errors());
				redirect('user/add?time='.time());
			} else {
			
					//Log user
					$create_acount  =  $this->simpleloginsecure->create($dp,$fname,$lname,$email, $user_pass,false,$user_type,$visibility);
					if($create_acount == 20){
					    $this->session->set_flashdata('SuccessMessage','Success: User create successfully...');
					    redirect('user');
					}elseif($create_acount == 12){
					    	$this->session->set_flashdata('ErrorsMessage','Error: Email is already exist...');
						    redirect('user/add?time='.time());
					}else {
				$this->session->set_flashdata('ErrorsMessage','Error: Something went wrong...');

						redirect('user/add?time='.time());
				
					}
				// 	die;
						
// 					if($this->simpleloginsecure->create($dp,$fname,$lname,$email, $user_pass,false,$user_type,$visibility)) {
// 			$this->session->set_flashdata('SuccessMessage','Success: User create successfully...');
					
// 					redirect('user');
						
// 					} else {
// 				$this->session->set_flashdata('ErrorsMessage','Error: Something went wrong...');

// 						redirect('user/add');
				
// 					}			
				
				}
				}else{
					$data['title'] = 'Users';
					$data['ZOWuser']= _getCurrentUser();

					$this->load->view('users/adduser',$data);

				}
		
	}
}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>