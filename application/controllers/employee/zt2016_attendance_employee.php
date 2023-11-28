<?php

class zt2016_attendance_employee extends MY_Controller {

	
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
		


		$templateData['title'] = 'Attendance';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _display_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    function _display_page ($ZOWuser)
	{

       
		$page_content = '
		<!-- Page Wrapper -->
		<div class="panel panel-info">
			
				<!-- Page Header -->
				<div class="panel-heading">
					<div class="row">
						<div class="col-sm-12">
							<h3 class="page-title">Attendance</h3>
						</div>
					</div>
				</div>
				<!-- /Page Header -->
				<div class="content container-fluid">
				<div class="row">
					<div class="col-md-4">
						<div class="card punch-status">
							<div class="card-body">
								<h5 class="card-title">Timesheet <small class="text-muted">11 Mar 2019</small></h5>
								<div class="punch-det">
									<h6>Punch In at</h6>
									<p>Wed, 11th Mar 2019 10.00 AM</p>
								</div>
								<div class="punch-info">
									<div class="punch-hours">
										<span>3.45 hrs</span>
									</div>
								</div>
								<div class="punch-btn-section">
									<button type="button" class="btn btn-primary punch-btn">Punch Out</button>
								</div>
								<div class="statistics">
									<div class="row">
										<div class="col-md-6 col-6 text-center">
											<div class="stats-box">
												<p>Break</p>
												<h6>1.21 hrs</h6>
											</div>
										</div>
										<div class="col-md-6 col-6 text-center">
											<div class="stats-box">
												<p>Overtime</p>
												<h6>3 hrs</h6>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card att-statistics">
							<div class="card-body">
								<h5 class="card-title">Statistics</h5>
								<div class="stats-list">
									<div class="stats-info">
										<p>Today <strong>3.45 <small>/ 8 hrs</small></strong></p>
										<div class="progress">
											<div class="progress-bar bg-primary" role="progressbar" style="width: 31%" aria-valuenow="31" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>
									<div class="stats-info">
										<p>This Week <strong>28 <small>/ 40 hrs</small></strong></p>
										<div class="progress">
											<div class="progress-bar bg-warning" role="progressbar" style="width: 31%" aria-valuenow="31" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>
									<div class="stats-info">
										<p>This Month <strong>90 <small>/ 160 hrs</small></strong></p>
										<div class="progress">
											<div class="progress-bar bg-success" role="progressbar" style="width: 62%" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>
									<div class="stats-info">
										<p>Remaining <strong>90 <small>/ 160 hrs</small></strong></p>
										<div class="progress">
											<div class="progress-bar bg-danger" role="progressbar" style="width: 62%" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>
									<div class="stats-info">
										<p>Overtime <strong>4</strong></p>
										<div class="progress">
											<div class="progress-bar bg-info" role="progressbar" style="width: 22%" aria-valuenow="22" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card recent-activity">
							<div class="card-body">
								<h5 class="card-title">Today Activity</h5>
								<ul class="res-activity-list">
									<li>
										<p class="mb-0">Punch In at</p>
										<p class="res-activity-time">
											<i class="fa fa-clock-o"></i>
											10.00 AM.
										</p>
									</li>
									<li>
										<p class="mb-0">Punch Out at</p>
										<p class="res-activity-time">
											<i class="fa fa-clock-o"></i>
											11.00 AM.
										</p>
									</li>
									<li>
										<p class="mb-0">Punch In at</p>
										<p class="res-activity-time">
											<i class="fa fa-clock-o"></i>
											11.15 AM.
										</p>
									</li>
									<li>
										<p class="mb-0">Punch Out at</p>
										<p class="res-activity-time">
											<i class="fa fa-clock-o"></i>
											1.30 PM.
										</p>
									</li>
									<li>
										<p class="mb-0">Punch In at</p>
										<p class="res-activity-time">
											<i class="fa fa-clock-o"></i>
											2.00 PM.
										</p>
									</li>
									<li>
										<p class="mb-0">Punch Out at</p>
										<p class="res-activity-time">
											<i class="fa fa-clock-o"></i>
											7.30 PM.
										</p>
									</li>
								</ul>
							</div>
						</div>
					</div>
					
				</div>

				<!-- Search Filter -->
				<div class="row filter-row">
					<div class="col-sm-3">  
						<div class="form-group form-focus">
							<div class="cal-icon">
								<input type="text" class="form-control floating datetimepicker">
							</div>
							<label class="focus-label">Date</label>
						</div>
					</div>
					<div class="col-sm-3"> 
						<div class="form-group custom-select">
							<select class="select floating"> 
								<option>Select Month</option>
								<option>Jan</option>
								<option>Feb</option>
								<option>Mar</option>
								<option>Apr</option>
								<option>May</option>
								<option>Jun</option>
								<option>Jul</option>
								<option>Aug</option>
								<option>Sep</option>
								<option>Oct</option>
								<option>Nov</option>
								<option>Dec</option>
							</select>
						</div>
					</div>
					<div class="col-sm-3"> 
						<div class="form-group custom-select">
							<select class="select floating"> 
								<option>Select Year</option>
								<option>2019</option>
								<option>2018</option>
								<option>2017</option>
								<option>2016</option>
								<option>2015</option>
							</select>
						</div>
					</div>
					<div class="col-sm-3">  
						<div class="d-grid">
							<a href="#" class="btn btn-success"> Search </a>  
						</div>
					</div>     
				</div>
				<!-- /Search Filter -->
				
				<div class="row">
					<div class="col-lg-12">
						<div class="table-responsive">
							<table class="table table-striped custom-table mb-0">
								<thead>
									<tr>
										<th>#</th>
										<th>Date </th>
										<th>Punch In</th>
										<th>Punch Out</th>
										<th>Production</th>
										<th>Break</th>
										<th>Overtime</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>1</td>
										<td>19 Feb 2019</td>
										<td>10 AM</td>
										<td>7 PM</td>
										<td>9 hrs</td>
										<td>1 hrs</td>
										<td>0</td>
									</tr>
									<tr>
										<td>2</td>
										<td>20 Feb 2019</td>
										<td>10 AM</td>
										<td>7 PM</td>
										<td>9 hrs</td>
										<td>1 hrs</td>
										<td>0</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				</div>
			<!-- /Page Content -->
			
		</div>
		<!-- Page Wrapper -->


</div>
<!-- end main wrapper-->
		';


		return $page_content;
		
	}	



}

/* End of file zt2016_attemdamce_employee.php */
/* Location: ./system/application/controllers/employee/zt2016_employee.php */
?>