<?php

class zt2016_employee_profile extends MY_Controller {

	
	function index()
	{
        
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','general','userpermissions','zt2016_clients','zt2016_timezone'));
		
		$zowuser=_superuseronly(); 
		

        //Read form input values
        $employeeid= $this->uri->segment(3);

        $this->load->model('zt2016_employee_model', '', TRUE);
		$employeeData = $this->zt2016_employee_model->getemployeedata($options = array('employeeid'=> $employeeid));

        #load groups info	
		$this->load->model('zt2016_users_model', '', TRUE);
		//$GroupsData = $this->zt2016_groups_model->GetUser($options = array('Trash'=>'0','sortBy'=>'GroupName','sortDirection'=>'ASC	'));
		$UsersData = $this->zt2016_users_model->GetUser($options = array('user_id'=> $employeeid));
        // print_r($UsersData);
        // die;

		$templateData['title'] = 'Profile';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _display_page($templateData['ZOWuser'],$UsersData,$employeeData); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    function _display_page ($ZOWuser,$UserDetails,$employeeData)
	{
        // 		ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(-1);
        // print_r($employeeData);
        $basicData = $employeeData['basicData'];
        $familydata = $employeeData['familydata'];
        $education = $employeeData['education'];
        $experience = $employeeData['experience'];

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
       
       $formattedDate = date("jS M Y", strtotime($UserDetails->user_date));
       
		$page_content = '
        
             <!-- Page Wrapper -->
             <style>
             .modal-dialog{
                 vertical-align:unset!important;
             }
             .edit-icon{
                padding-top: 6px;
            }
            .personal-info li .text{
                display:inline-block;
            }
             </style>
                    <div class="">
                    
                        <!-- Page Content -->
                        <div class="panel panel-info">
                        
                            <!-- Page Header -->
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col">
                                        <h3 class="page-title">Profile</h3>
                                    </div>
                                    <div class="col-auto float-end ms-auto mt-4">
                                    <a href="'.base_url().'employee/zt2016_edit_employee/'.$UserDetails->user_id.'" class="btn add-btn btn-success">Edit</a>
                                    
                                </div>
                                </div>
                               
                             
                            </div>
                            <!-- /Page Header -->
                            
                            <div class="content container-fluid">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="profile-view">
                                                <div class="profile-img-wrap">
                                                    <div class="profile-img">
                                                        <a href="#"><img alt="" src="'.$path.'"></a>
                                                    </div>
                                                </div>
                                                <div class="profile-basic">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="profile-info-left">
                                                                <h3 class="user-name m-t-0 mb-0">'.$UserDetails->fname.' '.$UserDetails->lname.'</h3>
                                                                <h6 class="text-muted">'.$role_name.'</h6>
                                                                <div class="staff-id">Employee ID : FT-0001</div>
                                                                <div class="small doj text-muted">Date of Join : '.$formattedDate.'</div>
                                                        
                                                            </div>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <ul class="personal-info">
                                                                <li>
                                                                    <div class="title">Phone:</div>
                                                                    <div class="text"><a href="">'.$basicData->phone.'</a></div>
                                                                </li>
                                                                <li>
                                                                    <div class="title">Email:</div>
                                                                    <div class="text"><a href="">'.$UserDetails->user_email.'</a></div>
                                                                </li>
                                                                <li>
                                                                    <div class="title">Birthday:</div>
                                                                    <div class="text">'.$basicData->dob.'</div>
                                                                </li>
                                                                <li>
                                                                    <div class="title">Address:</div>
                                                                    <div class="text">'.$basicData->address.'</div>
                                                                </li>
                                                                <li>
                                                                    <div class="title">Gender:</div>
                                                                    <div class="text">'.$basicData->gender.'</div>
                                                                </li>
                                                               
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card tab-box">
                                <div class="row user-tabs">
                                    <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                                        <ul class="nav nav-tabs nav-tabs-bottom">
                                            <li class="nav-item"><a href="#emp_profile" data-bs-toggle="tab" class="nav-link active">Profile</a></li>
                                            <li class="nav-item"><a href="#emp_projects" data-bs-toggle="tab" class="nav-link">Projects</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-content">
                            
                                <!-- Profile Info Tab -->
                                <div id="emp_profile" class="pro-overview tab-pane fade show active">
                                    <div class="row">
                                        <div class="col-md-6 d-flex">
                                            <div class="card profile-box flex-fill">
                                                <div class="card-body">
                                                    <h3 class="card-title">Personal Informations </h3>
                                                    <ul class="personal-info">
                                                    <li>
                                                        <div class="title">Phone:</div>
                                                        <div class="text w-50">'.$basicData->phone.'</div>
                                                    </li>
                                                    <li>
                                                        <div class="title">Email:</div>
                                                        <div class="text w-50"><a href="mailto:'.$UserDetails->user_mail.'">'.$UserDetails->user_email.'</a></div>
                                                    </li>
                                                    <li>
                                                        <div class="title">Birthday:</div>
                                                        <div class="text" w-50>'.$basicData->dob.'</div>
                                                    </li>
                                                    <li>
                                                        <div class="title">Address:</div>
                                                        <div class="text w-50">'.$basicData->address.'</div>
                                                    </li>
                                                    <li>
                                                        <div class="title">Gender:</div>
                                                        <div class="text w-50">'.$basicData->gender.'</div>
                                                    </li>
                                                </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex">
                                            <div class="card profile-box flex-fill">
                                                <div class="card-body">
                                                    <h3 class="card-title">Emergency Contact</h3>
                                                    <h5 class="section-title">Primary</h5>
                                                    <ul class="personal-info">
                                                        <li>
                                                            <div class="title">Name</div>
                                                            <div class="text w-50">'.$basicData->PrimaryName.'</div>
                                                        </li>
                                                        <li>
                                                            <div class="title">Relationship</div>
                                                            <div class="text w-50">'.$basicData->PrimaryRelationship.'</div>
                                                        </li>
                                                        <li>
                                                            <div class="title">Phone </div>
                                                            <div class="text w-50">'.$basicData->PrimaryPhone.'</div>
                                                        </li>
                                                    </ul>
                                                    <hr>
                                                    <h5 class="section-title">Secondary</h5>
                                                    <ul class="personal-info">
                                                        <li>
                                                            <div class="title">Name</div>
                                                            <div class="text w-50">'.$basicData->SecondryName.'</div>
                                                        </li>
                                                        <li>
                                                            <div class="title">Relationship</div>
                                                            <div class="text w-50">'.$basicData->SecondryRelationship.'</div>
                                                        </li>
                                                        <li>
                                                            <div class="title">Phone </div>
                                                            <div class="text w-50">'.$basicData->SecondryPhone.'</div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 d-flex">
                                            <div class="card profile-box flex-fill">
                                                <div class="card-body">
                                                    <h3 class="card-title">Bank information</h3>
                                                    <ul class="personal-info">
                                                        <li>
                                                            <div class="title">Bank name</div>
                                                            <div class="text w-50">'.$basicData->BankName.'</div>
                                                        </li>
                                                        <li>
                                                            <div class="title">Bank account No.</div>
                                                            <div class="text w-50">'.$basicData->AccountNumber.'</div>
                                                        </li>
                                                        <li>
                                                            <div class="title">IFSC Code</div>
                                                            <div class="text w-50">'.$basicData->IFSCCode.'</div>
                                                        </li>
                                                        <li>
                                                            <div class="title">PAN No</div>
                                                            <div class="text w-50">'.$basicData->PANNO.'</div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex">
                                            <div class="card profile-box flex-fill">
                                                <div class="card-body">
                                                    <h3 class="card-title">Family Informations</h3>
                                                    <div class="table-responsive">
                                                        <table class="table table-nowrap">
                                                            <thead>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th>Relationship</th>
                                                                    <th>Date of Birth</th>
                                                                    <th>Phone</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>';
                                                            if($familydata){
                                                                $index = 1;
                                                                foreach($familydata as $data){
                                                            $page_content.='
                                                                <tr>
                                                                    <td>'.$data->familyName.'</td>
                                                                    <td>'.$data->familyRelationship.'</td>
                                                                    <td>'.$data->familydob.'</td>
                                                                    <td>'.$data->familyPhone.'</td>
                                                                    <td class="text-end">
                                                                        
                                                                    </td>
                                                                </tr>';
                                                                }
                                                            }
                                                                $page_content.='
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 d-flex">
                                            <div class="card profile-box flex-fill">
                                                <div class="card-body">
                                                    <h3 class="card-title">Education Informations </h3>
                                                    <div class="experience-box">
                                                        <ul class="experience-list">';
                                                        if($education){
                                                            foreach($education as $data){
                                                        $page_content.='
                                                            <li>
                                                                <div class="experience-user">
                                                                    <div class="before-circle"></div>
                                                                </div>
                                                                <div class="experience-content">
                                                                    <div class="timeline-content">
                                                                        <a href="#/" class="name">'.$data->Institution.'</a>
                                                                        <div>'.$data->Subject.'</div>
                                                                        <span class="time">'.$data->year.'</span>
                                                                    </div>
                                                                </div>
                                                            </li>';
                                                            }
                                                        }
                                                            $page_content.='
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex">
                                            <div class="card profile-box flex-fill">
                                                <div class="card-body">
                                                    <h3 class="card-title">Experience</h3>
                                                    <div class="experience-box">
                                                        <ul class="experience-list">';
                                                        if($experience){
                                                            foreach($experience as $data){
                                                        $page_content.='
                                                            <li>
                                                                <div class="experience-user">
                                                                    <div class="before-circle"></div>
                                                                </div>
                                                                <div class="experience-content">
                                                                    <div class="timeline-content">
                                                                        <a href="#/" class="name">'.$data->JobPosition.'</a>
                                                                        <span class="time">'.$data->PeriodFrom.' </span>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            ';
                                                            }
                                                        }
                                                            $page_content.='

                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Profile Info Tab -->
                                
                                <!-- Projects Tab -->
                                <div class="tab-pane fade" id="emp_projects">
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="dropdown profile-action">
                                                        <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a data-bs-target="#edit_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                            <a data-bs-target="#delete_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                        </div>
                                                    </div>
                                                    <h4 class="project-title"><a href="project_view">Office Management</a></h4>
                                                    <small class="block text-ellipsis m-b-15">
                                                        <span class="text-xs">1</span> <span class="text-muted">open tasks, </span>
                                                        <span class="text-xs">9</span> <span class="text-muted">tasks completed</span>
                                                    </small>
                                                    <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. When an unknown printer took a galley of type and
                                                        scrambled it...
                                                    </p>
                                                    <div class="pro-deadline m-b-15">
                                                        <div class="sub-title">
                                                            Deadline:
                                                        </div>
                                                        <div class="text-muted">
                                                            17 Apr 2019
                                                        </div>
                                                    </div>
                                                    <div class="project-members m-b-15">
                                                        <div>Project Leader :</div>
                                                        <ul class="team-members">
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Jeffery Lalor"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-16.jpg"></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="project-members m-b-15">
                                                        <div>Team :</div>
                                                        <ul class="team-members">
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="John Doe"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-02.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Richard Miles"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-09.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="John Smith"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-10.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Mike Litorus"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-05.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="all-users">+15</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <p class="m-b-5">Progress <span class="text-success float-end">40%</span></p>
                                                    <div class="progress progress-xs mb-0">
                                                        <div style="width: 40%" title="" data-bs-toggle="tooltip" role="progressbar" class="progress-bar bg-success" data-original-title="40%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="dropdown profile-action">
                                                        <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a data-bs-target="#edit_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                            <a data-bs-target="#delete_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                        </div>
                                                    </div>
                                                    <h4 class="project-title"><a href="project_view">Project Management</a></h4>
                                                    <small class="block text-ellipsis m-b-15">
                                                        <span class="text-xs">2</span> <span class="text-muted">open tasks, </span>
                                                        <span class="text-xs">5</span> <span class="text-muted">tasks completed</span>
                                                    </small>
                                                    <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. When an unknown printer took a galley of type and
                                                        scrambled it...
                                                    </p>
                                                    <div class="pro-deadline m-b-15">
                                                        <div class="sub-title">
                                                            Deadline:
                                                        </div>
                                                        <div class="text-muted">
                                                            17 Apr 2019
                                                        </div>
                                                    </div>
                                                    <div class="project-members m-b-15">
                                                        <div>Project Leader :</div>
                                                        <ul class="team-members">
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Jeffery Lalor"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-16.jpg"></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="project-members m-b-15">
                                                        <div>Team :</div>
                                                        <ul class="team-members">
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="John Doe"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-02.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Richard Miles"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-09.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="John Smith"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-10.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Mike Litorus"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-05.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="all-users">+15</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <p class="m-b-5">Progress <span class="text-success float-end">40%</span></p>
                                                    <div class="progress progress-xs mb-0">
                                                        <div style="width: 40%" title="" data-bs-toggle="tooltip" role="progressbar" class="progress-bar bg-success" data-original-title="40%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="dropdown profile-action">
                                                        <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a data-bs-target="#edit_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                            <a data-bs-target="#delete_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                        </div>
                                                    </div>
                                                    <h4 class="project-title"><a href="project_view">Video Calling App</a></h4>
                                                    <small class="block text-ellipsis m-b-15">
                                                        <span class="text-xs">3</span> <span class="text-muted">open tasks, </span>
                                                        <span class="text-xs">3</span> <span class="text-muted">tasks completed</span>
                                                    </small>
                                                    <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. When an unknown printer took a galley of type and
                                                        scrambled it...
                                                    </p>
                                                    <div class="pro-deadline m-b-15">
                                                        <div class="sub-title">
                                                            Deadline:
                                                        </div>
                                                        <div class="text-muted">
                                                            17 Apr 2019
                                                        </div>
                                                    </div>
                                                    <div class="project-members m-b-15">
                                                        <div>Project Leader :</div>
                                                        <ul class="team-members">
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Jeffery Lalor"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-16.jpg"></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="project-members m-b-15">
                                                        <div>Team :</div>
                                                        <ul class="team-members">
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="John Doe"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-02.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Richard Miles"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-09.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="John Smith"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-10.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Mike Litorus"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-05.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="all-users">+15</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <p class="m-b-5">Progress <span class="text-success float-end">40%</span></p>
                                                    <div class="progress progress-xs mb-0">
                                                        <div style="width: 40%" title="" data-bs-toggle="tooltip" role="progressbar" class="progress-bar bg-success" data-original-title="40%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="dropdown profile-action">
                                                        <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a data-bs-target="#edit_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                            <a data-bs-target="#delete_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                        </div>
                                                    </div>
                                                    <h4 class="project-title"><a href="project_view">Hospital Administration</a></h4>
                                                    <small class="block text-ellipsis m-b-15">
                                                        <span class="text-xs">12</span> <span class="text-muted">open tasks, </span>
                                                        <span class="text-xs">4</span> <span class="text-muted">tasks completed</span>
                                                    </small>
                                                    <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
                                                        typesetting industry. When an unknown printer took a galley of type and
                                                        scrambled it...
                                                    </p>
                                                    <div class="pro-deadline m-b-15">
                                                        <div class="sub-title">
                                                            Deadline:
                                                        </div>
                                                        <div class="text-muted">
                                                            17 Apr 2019
                                                        </div>
                                                    </div>
                                                    <div class="project-members m-b-15">
                                                        <div>Project Leader :</div>
                                                        <ul class="team-members">
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Jeffery Lalor"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-16.jpg"></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="project-members m-b-15">
                                                        <div>Team :</div>
                                                        <ul class="team-members">
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="John Doe"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-02.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Richard Miles"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-09.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="John Smith"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-10.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-bs-toggle="tooltip" title="Mike Litorus"><img alt="" src="<?php echo base_url();?>/assets/img/profiles/avatar-05.jpg"></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="all-users">+15</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <p class="m-b-5">Progress <span class="text-success float-end">40%</span></p>
                                                    <div class="progress progress-xs mb-0">
                                                        <div style="width: 40%" title="" data-bs-toggle="tooltip" role="progressbar" class="progress-bar bg-success" data-original-title="40%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Projects Tab -->
                                
                                <!-- Bank Statutory Tab -->
                                <div class="tab-pane fade" id="bank_statutory">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="card-title"> Basic Salary Information</h3>
                                            <form>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Salary basis <span class="text-danger">*</span></label>
                                                            <select class="select">
                                                                <option>Select salary basis type</option>
                                                                <option>Hourly</option>
                                                                <option>Daily</option>
                                                                <option>Weekly</option>
                                                                <option>Monthly</option>
                                                            </select>
                                                       </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Salary amount <small class="text-muted">per month</small></label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">$</span>
                                                                <input type="text" class="form-control" placeholder="Type your salary amount" value="0.00">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Payment type</label>
                                                            <select class="select">
                                                                <option>Select payment type</option>
                                                                <option>Bank transfer</option>
                                                                <option>Check</option>
                                                                <option>Cash</option>
                                                            </select>
                                                       </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <h3 class="card-title"> PF Information</h3>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">PF contribution</label>
                                                            <select class="select">
                                                                <option>Select PF contribution</option>
                                                                <option>Yes</option>
                                                                <option>No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">PF No. <span class="text-danger">*</span></label>
                                                            <select class="select">
                                                                <option>Select PF contribution</option>
                                                                <option>Yes</option>
                                                                <option>No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Employee PF rate</label>
                                                            <select class="select">
                                                                <option>Select PF contribution</option>
                                                                <option>Yes</option>
                                                                <option>No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Additional rate <span class="text-danger">*</span></label>
                                                            <select class="select">
                                                                <option>Select additional rate</option>
                                                                <option>0%</option>
                                                                <option>1%</option>
                                                                <option>2%</option>
                                                                <option>3%</option>
                                                                <option>4%</option>
                                                                <option>5%</option>
                                                                <option>6%</option>
                                                                <option>7%</option>
                                                                <option>8%</option>
                                                                <option>9%</option>
                                                                <option>10%</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Total rate</label>
                                                            <input type="text" class="form-control" placeholder="N/A" value="11%">
                                                        </div>
                                                    </div>
                                               </div>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Employee PF rate</label>
                                                            <select class="select">
                                                                <option>Select PF contribution</option>
                                                                <option>Yes</option>
                                                                <option>No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Additional rate <span class="text-danger">*</span></label>
                                                            <select class="select">
                                                                <option>Select additional rate</option>
                                                                <option>0%</option>
                                                                <option>1%</option>
                                                                <option>2%</option>
                                                                <option>3%</option>
                                                                <option>4%</option>
                                                                <option>5%</option>
                                                                <option>6%</option>
                                                                <option>7%</option>
                                                                <option>8%</option>
                                                                <option>9%</option>
                                                                <option>10%</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Total rate</label>
                                                            <input type="text" class="form-control" placeholder="N/A" value="11%">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <hr>
                                                <h3 class="card-title"> ESI Information</h3>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">ESI contribution</label>
                                                            <select class="select">
                                                                <option>Select ESI contribution</option>
                                                                <option>Yes</option>
                                                                <option>No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">ESI No. <span class="text-danger">*</span></label>
                                                            <select class="select">
                                                                <option>Select ESI contribution</option>
                                                                <option>Yes</option>
                                                                <option>No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Employee ESI rate</label>
                                                            <select class="select">
                                                                <option>Select ESI contribution</option>
                                                                <option>Yes</option>
                                                                <option>No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Additional rate <span class="text-danger">*</span></label>
                                                            <select class="select">
                                                                <option>Select additional rate</option>
                                                                <option>0%</option>
                                                                <option>1%</option>
                                                                <option>2%</option>
                                                                <option>3%</option>
                                                                <option>4%</option>
                                                                <option>5%</option>
                                                                <option>6%</option>
                                                                <option>7%</option>
                                                                <option>8%</option>
                                                                <option>9%</option>
                                                                <option>10%</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label class="col-form-label">Total rate</label>
                                                            <input type="text" class="form-control" placeholder="N/A" value="11%">
                                                        </div>
                                                    </div>
                                               </div>
                                                
                                                <div class="submit-section">
                                                    <button class="btn btn-primary submit-btn" type="submit">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Bank Statutory Tab -->
                                
                            </div>
                        </div>
                        <!-- /Page Content -->
                        
                        <!-- Profile Modal -->
                      
                        <div id="profile_info" class="modal custom-modal fade" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Profile Information</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="profile-img-wrap edit-img">
                                                        <img class="inline-block" src="<?php echo base_url();?>/assets/img/profiles/avatar-02.jpg" alt="user">
                                                        <div class="fileupload btn">
                                                            <span class="btn-text">edit</span>
                                                            <input class="upload" type="file">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>First Name</label>
                                                                <input type="text" class="form-control" value="John">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Last Name</label>
                                                                <input type="text" class="form-control" value="Doe">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Birth Date</label>
                                                                <div class="cal-icon">
                                                                    <input class="form-control datetimepicker" type="text" value="05/06/1985">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Gender</label>
                                                                <select class="select form-control">
                                                                    <option value="male selected">Male</option>
                                                                    <option value="female">Female</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Address</label>
                                                        <input type="text" class="form-control" value="4487 Snowbird Lane">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>State</label>
                                                        <input type="text" class="form-control" value="New York">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Country</label>
                                                        <input type="text" class="form-control" value="United States">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Pin Code</label>
                                                        <input type="text" class="form-control" value="10523">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Phone Number</label>
                                                        <input type="text" class="form-control" value="631-889-3206">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Department <span class="text-danger">*</span></label>
                                                        <select class="select">
                                                            <option>Select Department</option>
                                                            <option>Web Development</option>
                                                            <option>IT Management</option>
                                                            <option>Marketing</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Designation <span class="text-danger">*</span></label>
                                                        <select class="select">
                                                            <option>Select Designation</option>
                                                            <option>Web Designer</option>
                                                            <option>Web Developer</option>
                                                            <option>Android Developer</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Reports To <span class="text-danger">*</span></label>
                                                        <select class="select">
                                                            <option>-</option>
                                                            <option>Wilmer Deluna</option>
                                                            <option>Lesley Grauer</option>
                                                            <option>Jeffery Lalor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="submit-section">
                                                <button class="btn btn-primary submit-btn">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Profile Modal -->
                        
                        <!-- Personal Info Modal -->
                        <div id="personal_info_modal" class="modal custom-modal fade" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Personal Information</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Passport No</label>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Passport Expiry Date</label>
                                                        <div class="cal-icon">
                                                            <input class="form-control datetimepicker" type="text">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Tel</label>
                                                        <input class="form-control" type="text">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nationality <span class="text-danger">*</span></label>
                                                        <input class="form-control" type="text">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Religion</label>
                                                        <div class="cal-icon">
                                                            <input class="form-control" type="text">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Marital status <span class="text-danger">*</span></label>
                                                        <select class="select form-control">
                                                            <option>-</option>
                                                            <option>Single</option>
                                                            <option>Married</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Employment of spouse</label>
                                                        <input class="form-control" type="text">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>No. of children </label>
                                                        <input class="form-control" type="text">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="submit-section">
                                                <button class="btn btn-primary submit-btn">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Personal Info Modal -->
                        
                        <!-- Family Info Modal -->
                        <div id="family_info_modal" class="modal custom-modal fade" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"> Family Informations</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form>
                                            <div class="form-scroll">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h3 class="card-title">Family Member <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Name <span class="text-danger">*</span></label>
                                                                    <input class="form-control" type="text">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Relationship <span class="text-danger">*</span></label>
                                                                    <input class="form-control" type="text">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Date of birth <span class="text-danger">*</span></label>
                                                                    <input class="form-control" type="text">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Phone <span class="text-danger">*</span></label>
                                                                    <input class="form-control" type="text">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h3 class="card-title">Education Informations <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Name <span class="text-danger">*</span></label>
                                                                    <input class="form-control" type="text">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Relationship <span class="text-danger">*</span></label>
                                                                    <input class="form-control" type="text">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Date of birth <span class="text-danger">*</span></label>
                                                                    <input class="form-control" type="text">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Phone <span class="text-danger">*</span></label>
                                                                    <input class="form-control" type="text">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="add-more">
                                                            <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="submit-section">
                                                <button class="btn btn-primary submit-btn">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Family Info Modal -->
                        
                        <!-- Emergency Contact Modal -->
                        <div id="emergency_contact_modal" class="modal custom-modal fade" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Personal Information</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form>
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">Primary Contact</h3>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Name <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Relationship <span class="text-danger">*</span></label>
                                                                <input class="form-control" type="text">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Phone <span class="text-danger">*</span></label>
                                                                <input class="form-control" type="text">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Phone 2</label>
                                                                <input class="form-control" type="text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">Primary Contact</h3>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Name <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Relationship <span class="text-danger">*</span></label>
                                                                <input class="form-control" type="text">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Phone <span class="text-danger">*</span></label>
                                                                <input class="form-control" type="text">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Phone 2</label>
                                                                <input class="form-control" type="text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="submit-section">
                                                <button class="btn btn-primary submit-btn">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Emergency Contact Modal -->
                        
                        <!-- Education Modal -->
                        <div id="education_info" class="modal custom-modal fade" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"> Education Informations</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form>
                                            <div class="form-scroll">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h3 class="card-title">Education Informations <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <input type="text" value="Oxford University" class="form-control floating">
                                                                    <label class="focus-label">Institution</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <input type="text" value="Computer Science" class="form-control floating">
                                                                    <label class="focus-label">Subject</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <div class="cal-icon">
                                                                        <input type="text" value="01/06/2002" class="form-control floating datetimepicker">
                                                                    </div>
                                                                    <label class="focus-label">Starting Date</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <div class="cal-icon">
                                                                        <input type="text" value="31/05/2006" class="form-control floating datetimepicker">
                                                                    </div>
                                                                    <label class="focus-label">Complete Date</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <input type="text" value="BE Computer Science" class="form-control floating">
                                                                    <label class="focus-label">Degree</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <input type="text" value="Grade A" class="form-control floating">
                                                                    <label class="focus-label">Grade</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h3 class="card-title">Education Informations <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <input type="text" value="Oxford University" class="form-control floating">
                                                                    <label class="focus-label">Institution</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <input type="text" value="Computer Science" class="form-control floating">
                                                                    <label class="focus-label">Subject</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <div class="cal-icon">
                                                                        <input type="text" value="01/06/2002" class="form-control floating datetimepicker">
                                                                    </div>
                                                                    <label class="focus-label">Starting Date</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <div class="cal-icon">
                                                                        <input type="text" value="31/05/2006" class="form-control floating datetimepicker">
                                                                    </div>
                                                                    <label class="focus-label">Complete Date</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <input type="text" value="BE Computer Science" class="form-control floating">
                                                                    <label class="focus-label">Degree</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus focused">
                                                                    <input type="text" value="Grade A" class="form-control floating">
                                                                    <label class="focus-label">Grade</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="add-more">
                                                            <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="submit-section">
                                                <button class="btn btn-primary submit-btn">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Education Modal -->
                        
                        <!-- Experience Modal -->
                        <div id="experience_info" class="modal custom-modal fade" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Experience Informations</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form>
                                            <div class="form-scroll">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h3 class="card-title">Experience Informations <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus">
                                                                    <input type="text" class="form-control floating" value="Digital Devlopment Inc">
                                                                    <label class="focus-label">Company Name</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus">
                                                                    <input type="text" class="form-control floating" value="United States">
                                                                    <label class="focus-label">Location</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus">
                                                                    <input type="text" class="form-control floating" value="Web Developer">
                                                                    <label class="focus-label">Job Position</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus">
                                                                    <div class="cal-icon">
                                                                        <input type="text" class="form-control floating datetimepicker" value="01/07/2007">
                                                                    </div>
                                                                    <label class="focus-label">Period From</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus">
                                                                    <div class="cal-icon">
                                                                        <input type="text" class="form-control floating datetimepicker" value="08/06/2018">
                                                                    </div>
                                                                    <label class="focus-label">Period To</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h3 class="card-title">Experience Informations <a href="javascript:void(0);" class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus">
                                                                    <input type="text" class="form-control floating" value="Digital Devlopment Inc">
                                                                    <label class="focus-label">Company Name</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus">
                                                                    <input type="text" class="form-control floating" value="United States">
                                                                    <label class="focus-label">Location</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus">
                                                                    <input type="text" class="form-control floating" value="Web Developer">
                                                                    <label class="focus-label">Job Position</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus">
                                                                    <div class="cal-icon">
                                                                        <input type="text" class="form-control floating datetimepicker" value="01/07/2007">
                                                                    </div>
                                                                    <label class="focus-label">Period From</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group form-focus">
                                                                    <div class="cal-icon">
                                                                        <input type="text" class="form-control floating datetimepicker" value="08/06/2018">
                                                                    </div>
                                                                    <label class="focus-label">Period To</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="add-more">
                                                            <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="submit-section">
                                                <button class="btn btn-primary submit-btn">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Experience Modal -->
                        
                    </div>
                    <!-- /Page Wrapper -->

</div>
<!-- end main wrapper-->
		';


		return $page_content;
		
	}	



}

/* End of file zt2016_attemdamce_employee.php */
/* Location: ./system/application/controllers/employee/zt2016_employee.php */
?>