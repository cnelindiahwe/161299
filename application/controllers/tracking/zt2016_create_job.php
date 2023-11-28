<?php

class Zt2016_create_job extends MY_Controller {

	public function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('url','userpermissions'));
		
		$this->load->model('zt2016_entries_model', '', TRUE);


		$NewJobFields=array();
		
		### Load form data
		foreach ($_POST as $key => $value) {
			$NewJobFields[$key]= $value;
			//var_dump ($NewJobFields);
			//die();
		}
		
		### check that required fields have been submitted		
		$required= array("ZOWUser","Client", "Code", "DateIn", "TimeIn", "TimeZoneIn", "DateOut", "TimeOut", "TimeZoneOut", "Originator", "NewSlides", "EditedSlides", "Hours", "FileName", "Status");
		
		foreach($required as $field){
			if(!isset($NewJobFields[$field])) {
				$Message='Field '.$field.' not provied. New job has NOT been created.';						
				$this->session->set_flashdata('ErrorMessage',$Message);
		
				redirect('tracking/zt2016_tracking', 'refresh'); 

			} 
		} 

		
		### add additional fields			
		$NewJobFields['ScheduledDateOut']=$NewJobFields['DateOut'];
		$NewJobFields['ScheduledTimeOut']=$NewJobFields['TimeOut'];
		$ZOWUser=$NewJobFields['ZOWUser'];
		
		# fill in job history according to status
		if ($NewJobFields['Status']=="TENTATIVE"){
			$NewJobFields['CompletedBy']= "";
			$NewJobFields['ProofedBy']= "";	
			$NewJobFields['WorkedBy']= "";
			$NewJobFields['WorkedBy_2']= "";
			$NewJobFields['WorkedBy_3']= "";
			$NewJobFields['ScheduledBy']= "";
			if($NewJobFields['TentativeBy']=="")  $NewJobFields['TentativeBy']=$ZOWUser;
		}
		elseif ($NewJobFields['Status']=="SCHEDULED" ){
			$NewJobFields['CompletedBy']= "";
			$NewJobFields['ProofedBy']= "";
			$NewJobFields['WorkedBy']= "";
			$NewJobFields['WorkedBy_2']= "";
			$NewJobFields['WorkedBy_3']= "";
			if($NewJobFields['ScheduledBy']=="")  $NewJobFields['ScheduledBy']=$ZOWUser;
			if($NewJobFields['TentativeBy']=="")  $NewJobFields['TentativeBy']=$ZOWUser;
		}
		elseif ($NewJobFields['Status']=="IN PROGRESS"){
			$NewJobFields['CompletedBy']= "";
			$NewJobFields['ProofedBy']= "";
			if($NewJobFields['WorkedBy']=="")  $NewJobFields['WorkedBy']=$ZOWUser;
			if($NewJobFields['WorkedBy_2']=="")  $NewJobFields['WorkedBy_2']="";
			if($NewJobFields['WorkedBy_3']=="")  $NewJobFields['WorkedBy_3']="";
			if($NewJobFields['ScheduledBy']=="")  $NewJobFields['ScheduledBy']=$ZOWUser;
			if($NewJobFields['TentativeBy']=="")  $NewJobFields['TentativeBy']=$ZOWUser;
		}
		elseif ($NewJobFields['Status']=="IN PROOFING"){
			$NewJobFields['CompletedBy']= "";
			if($NewJobFields['ProofedBy']=="")  $NewJobFields['ProofedBy']=$ZOWUser;
			if($NewJobFields['WorkedBy']=="")  $NewJobFields['WorkedBy']=$ZOWUser;
			if($NewJobFields['WorkedBy_2']=="")  $NewJobFields['WorkedBy_2']="";
			if($NewJobFields['WorkedBy_3']=="")  $NewJobFields['WorkedBy_3']="";
			if($NewJobFields['ScheduledBy']=="")  $NewJobFields['ScheduledBy']=$ZOWUser;
			if($NewJobFields['TentativeBy']=="")  $NewJobFields['TentativeBy']=$ZOWUser;
		}					
		elseif ($NewJobFields['Status']=="COMPLETED") {
			if($NewJobFields['CompletedBy']=="")  $NewJobFields['CompletedBy']=$ZOWUser;
			if($NewJobFields['ProofedBy']=="")  $NewJobFields['ProofedBy']=$ZOWUser;
			if($NewJobFields['WorkedBy']=="")  $NewJobFields['WorkedBy']=$ZOWUser;
			if($NewJobFields['WorkedBy_2']=="")  $NewJobFields['WorkedBy_2']="";
			if($NewJobFields['WorkedBy_3']=="")  $NewJobFields['WorkedBy_3']="";
			if($NewJobFields['ScheduledBy']=="")  $NewJobFields['ScheduledBy']=$ZOWUser;
			if($NewJobFields['TentativeBy']=="")  $NewJobFields['TentativeBy']=$ZOWUser;
		}
		if(isset($NewJobFields['WorkedBy_2']) && !empty($NewJobFields['WorkedBy_2'])){
			$NewJobFields['has_multi_worked'] = 1;
		}else if(isset($NewJobFields['WorkedBy_3']) && !empty($NewJobFields['WorkedBy_3'])){
			$NewJobFields['has_multi_worked'] = 1;
		}else{
			$NewJobFields['has_multi_worked'] = 0;
		}

		
		#clean up company name
		$NewJobFields['Client']=str_replace("_", " ", $NewJobFields['Client']);
		$NewJobFields['Client']=str_replace("~", "&", $NewJobFields['Client']);
		
		
		
		#update job details in entries db

		$new_job_id = $this->zt2016_entries_model->AddEntry($NewJobFields);						

		if ($new_job_id) {
			$Message='Job #'.$new_job_id.' has been created.';						
			$this->session->set_flashdata('SuccessMessage',$Message);			
		} 
		else{
			$Message='Job could not be created when inserting data in DB.';						
			$this->session->set_flashdata('ErrorMessage',$Message);			
			
		}

	redirect('tracking/zt2016_tracking', 'refresh'); 


	}
}