<?php

class Zt2016_update_job extends MY_Controller {

	public function index()
	{
		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		
		$this->load->helper(array('url','userpermissions'));
		
		$this->load->model('zt2016_entries_model', '', TRUE);
		$this->load->model('zt2016_contacts_model', '', TRUE);
		
		
		#Read the job id number from the URL	
		$ActiveEntryID=$this->uri->segment(2);
		
		#Get the current user	
		$CurrentZOWUser= _getCurrentUser();
		$CurrentZOWUser_id= _getCurrentUser_id();
		
		$ZOWUserArray=explode(".", $CurrentZOWUser);
		if (isset($ZOWUserArray[1])){
			if ($ZOWUserArray[1]=="Poojari"){
				$ZOWUser= $ZOWUserArray[1];
			}else{
				$ZOWUser= $ZOWUserArray[0];
			}
		} else{
			$ZOWUser= $ZOWUserArray[0];
		}
		$ZOWUser=$CurrentZOWUser_id;//ucwords($ZOWUser);

		#load Active Entry	
		$ActiveEntryRaw=$this->zt2016_entries_model-> GetEntry($options = array('id'=>$ActiveEntryID));

			# No entry found for the ID provided
			if (!$ActiveEntryRaw){
				die( "Entry ".$ActiveEntryID." does not exist!!");			

			}
		
		$ActiveEntryDB=array();
		$ActiveEntryForm=array();
		
		// Load DB data		
		foreach ($ActiveEntryRaw as $key => $value) {
			$ActiveEntryDB[$key]= $value;
		}	
		

		// Load form data
		foreach ($_POST as $key => $value) {
			$ActiveEntryForm[$key]= $value;

		}

	
		$ChangeFlag=0;
		$ChangedFields=array();
		
		
		
		# discard entries without originator
		if (!isset($ActiveEntryForm['Originator'])){

			$NoChangeMessage="You must select a originator before saving the job.";
			$this->session->set_flashdata('ErrorMessage', $NoChangeMessage);
			
			if (isset($ActiveEntryForm['Current_Client'])) {
				
				$ActiveEntryClient=str_replace("_", " ",  $ActiveEntryForm['Current_Client']);
				$ActiveEntryClient=str_replace("~", "&", $ActiveEntryClient);
				$this->session->set_flashdata('Current_Client', $ActiveEntryClient);
			}
			
			redirect('zt2016_edit_job/'.$ActiveEntryForm['id'], 'refresh'); 					
		}		
		
		# Fields to ignore when comparing form data to db data
		$SkipFields = array("ScheduledTimeOut","ScheduledDateOut","Invoice","InvoicePrice","InvoiceTime","InvoiceEntryTotal","InvoiceEditsMultiplier","Trash","RealTime","BilledBy","PaidBy");

		
		
		# Cycle through values to checking for changes
		foreach ($ActiveEntryRaw as $key => $value) {

			if (!in_array($key,$SkipFields)) {
								
				
				# convert new lines to <br />
				//$ActiveEntryDB[$key]=nl2br($ActiveEntryDB[$key]);
				//$ActiveEntryForm[$key]=nl2br($ActiveEntryForm[$key]);				

				# retrieve originator name
				if ($key=="Originator") {
					if(isset($ActiveEntryForm['Originator'])){
						$OriginatorName= $this->zt2016_contacts_model->GetContact($options = array('ID' => $ActiveEntryForm['Originator']));
						$ActiveEntryForm['Originator']	= $OriginatorName->FirstName." ". $OriginatorName->LastName;			
					}
					else {

					}

				} 
				
				# add seconds to time inputs
				else if($key=="TimeIn" ||$key=="TimeOut"){ 
					$ActiveEntryForm[$key] = date("H:i:s", strtotime($ActiveEntryForm[$key]));					
				}
				
				# if form data is not the same as db data
				if ($ActiveEntryDB[$key]!=$ActiveEntryForm[$key]){					
					$ChangeFlag=1;
					array_push($ChangedFields, $key);
				}
 			}
		}
		
		
		if ($ChangeFlag==1) {
			

			### create update MySQL query array
			$UpdateOptions = array('id'=>$ActiveEntryDB['id']);
			$new_key_check = array_flip(array('NewSlides_2','NewSlides_3','EditedSlides_2','EditedSlides_3','Hours_2','Hours_3','WorkedBy_2','WorkedBy_3'));
			#insert changed form fields in update MySQL query array
			foreach ($ChangedFields as $ChangedSingleKey){

				if(array_key_exists($ChangedSingleKey,$new_key_check) && empty($ActiveEntryForm[$ChangedSingleKey])){
					$UpdateOptions[$ChangedSingleKey] = '0';
				}else{
					$UpdateOptions[$ChangedSingleKey]=$ActiveEntryForm[$ChangedSingleKey];

				}
			}

			
			# Update status fields if status has changed
			//if (in_array("Status",$ChangedFields)) {
			
				if ($ActiveEntryForm['Status']=="TENTATIVE"){
					$UpdateOptions['CompletedBy']= "";
					$UpdateOptions['ProofedBy']= "";	
					$UpdateOptions['WorkedBy']= "";
					$UpdateOptions['WorkedBy_2']= "";
					$UpdateOptions['WorkedBy_3']= "";
					$UpdateOptions['ScheduledBy']= "";
					if(empty($UpdateOptions['TentativeBy']) && empty($ActiveEntryDB['TentativeBy'])){$UpdateOptions['TentativeBy']= $ZOWUser;}
				}

				else if ($ActiveEntryForm['Status']=="SCHEDULED" ){
					$UpdateOptions['CompletedBy']= "";
					$UpdateOptions['ProofedBy']= "";
					$UpdateOptions['WorkedBy']= "";
					$UpdateOptions['WorkedBy_2']= "";
					$UpdateOptions['WorkedBy_3']= "";
					if(empty($UpdateOptions['ScheduledBy']) && empty($ActiveEntryDB['ScheduledBy'])){$UpdateOptions['ScheduledBy']= $ZOWUser;}
					if(empty($UpdateOptions['TentativeBy']) && empty($ActiveEntryDB['TentativeBy'])){$UpdateOptions['TentativeBy']= $ZOWUser;}
				}
				
				else if ($ActiveEntryForm['Status']=="IN PROGRESS"){
					$UpdateOptions['CompletedBy']= "";
					$UpdateOptions['ProofedBy']= "";
					
					if(empty($UpdateOptions['WorkedBy']) && empty($ActiveEntryDB['WorkedBy'])){$UpdateOptions['WorkedBy']= $ZOWUser;}
					if(empty($UpdateOptions['WorkedBy_2']) && empty($ActiveEntryDB['WorkedBy_2'])){$UpdateOptions['WorkedBy_2']= "0";}
					if(empty($UpdateOptions['WorkedBy_3']) && empty($ActiveEntryDB['WorkedBy_3'])){$UpdateOptions['WorkedBy_3']= "0";}
					if(empty($UpdateOptions['ScheduledBy']) && empty($ActiveEntryDB['ScheduledBy'])){$UpdateOptions['ScheduledBy']= $ZOWUser;}
					if(empty($UpdateOptions['TentativeBy']) && empty($ActiveEntryDB['TentativeBy'])){$UpdateOptions['TentativeBy']= $ZOWUser;}
						
				}
				else if ($ActiveEntryForm['Status']=="IN PROOFING"){
					$UpdateOptions['CompletedBy']= "";
					
					if(empty($UpdateOptions['ProofedBy']) && empty($ActiveEntryDB['ProofedBy'])){$UpdateOptions['ProofedBy']= $ZOWUser;}
					if(empty($UpdateOptions['WorkedBy']) && empty($ActiveEntryDB['WorkedBy'])){$UpdateOptions['WorkedBy']= $ZOWUser;}
					if(empty($UpdateOptions['WorkedBy_2']) && empty($ActiveEntryDB['WorkedBy_2'])){$UpdateOptions['WorkedBy_2']= "0";}
					if(empty($UpdateOptions['WorkedBy_3']) && empty($ActiveEntryDB['WorkedBy_3'])){$UpdateOptions['WorkedBy_3']= "0";}
					if(empty($UpdateOptions['ScheduledBy']) && empty($ActiveEntryDB['ScheduledBy'])){$UpdateOptions['ScheduledBy']= $ZOWUser;}
					if(empty($UpdateOptions['TentativeBy']) && empty($ActiveEntryDB['TentativeBy'])){$UpdateOptions['TentativeBy']= $ZOWUser;}
				}					
				else if ($ActiveEntryForm['Status']=="COMPLETED") {
					if(empty($UpdateOptions['CompletedBy']) && empty($ActiveEntryDB['CompletedBy'])){$UpdateOptions['CompletedBy']= $ZOWUser;}
					if(empty($UpdateOptions['ProofedBy']) && empty($ActiveEntryDB['ProofedBy'])){$UpdateOptions['ProofedBy']= $ZOWUser;}
					if(empty($UpdateOptions['WorkedBy']) && empty($ActiveEntryDB['WorkedBy'])){$UpdateOptions['WorkedBy']= $ZOWUser;}
					if(empty($UpdateOptions['WorkedBy_2']) && empty($ActiveEntryDB['WorkedBy_2'])){$UpdateOptions['WorkedBy_2']= "0";}
					if(empty($UpdateOptions['WorkedBy_3']) && empty($ActiveEntryDB['WorkedBy_3'])){$UpdateOptions['WorkedBy_3']= "0";}
					if(empty($UpdateOptions['ScheduledBy']) && empty($ActiveEntryDB['ScheduledBy'])){$UpdateOptions['ScheduledBy']= $ZOWUser;}
					if(empty($UpdateOptions['TentativeBy']) && empty($ActiveEntryDB['TentativeBy'])){$UpdateOptions['TentativeBy']= $ZOWUser;}
				
					if ($ActiveEntryDB['Status']!=$ActiveEntryForm['Status']){
	
						// create the DateTimeZone object
						$dtzone = new DateTimeZone($ActiveEntryForm['TimeZoneOut']);
						
						// Get server current time
							$timestamp = time();
						//  convert the server timestamp into a string representing the local time
							$timenow = date('r', $timestamp);
						// now create the DateTime object for this time
							$dtime = new DateTime($timenow);
						// convert this to the client's timezone using the DateTimeZone object
							$dtime->setTimeZone($dtzone);	
						
						$UpdateOptions['TimeOut']=$dtime->format('H:i:s');
						$UpdateOptions['DateOut']=$dtime->format('y-m-d');
					}
				}
				
				if((isset($UpdateOptions['WorkedBy_2']) && !empty($UpdateOptions['WorkedBy_2']) && $UpdateOptions['WorkedBy_2'] != 0 ) || (!empty($ActiveEntryForm['WorkedBy_2']) &&  !empty($ActiveEntryForm['WorkedBy_2']) !='0'  ) ){
					$UpdateOptions['has_multi_worked'] = 1;
				} else if((isset($UpdateOptions['WorkedBy_3']) && !empty($UpdateOptions['WorkedBy_3']) && $UpdateOptions['WorkedBy_3'] !=0) || ( !empty($ActiveEntryForm['WorkedBy_3']) && !empty($ActiveEntryForm['WorkedBy_3']) !='0' )){
					$UpdateOptions['has_multi_worked'] = 1;
				}else{
					$UpdateOptions['has_multi_worked'] = 0;
				}
				
			
			//}
		
			if ($ActiveEntryForm['Status']!="COMPLETED") {			
				$UpdateOptions['ScheduledTimeOut']=$ActiveEntryForm['TimeOut'];
				$UpdateOptions['ScheduledDateOut']=$ActiveEntryForm['DateOut'];					
			}
						

			$UpdateOptions['BilledBy']="";
			$UpdateOptions['PaidBy']="";
			

		// 	echo '<pre>';
		// print_r($UpdateOptions);
		// echo '</pre>';
		// die('top');
			#update job details in entries db
			$updated_contact = $this->zt2016_entries_model->UpdateEntry($UpdateOptions);						
			
			

			
			$Message='Job #'.$ActiveEntryForm['id'].' has been updated.';						
			//$Message.= "<br /><br />".$this->db->last_query();
				
			$this->session->set_flashdata('SuccessMessage',$Message);


		} else {
			
			# If no changes between DB and from data, go to trackign page and display error message
			
			$NoChangeMessage="The job details entered are the same as those stored in the system. Job not updated.";
			$this->session->set_flashdata('ErrorMessage', $NoChangeMessage);			

		}

		redirect('tracking/zt2016_tracking', 'refresh'); 


	}
}