<?php

class Ajax_addentrynew extends MY_Controller {

	
	function index()
	{
		
		//read and assign values
		
		foreach ($_POST as $key=>$value) {
		  if ($key == 'DateIn') {
		   //Convert dates 
			$FormValues['DateIn'] = date( 'Y-m-d',strtotime(str_replace("/","-",$_POST['DateIn'])));
		  } 
		  
		  else if ($key == 'DateOut') {
		   //Convert dates 
			$FormValues['DateOut'] = date( 'Y-m-d',strtotime(str_replace("/","-",$_POST['DateOut'])));
		  }
		  
		  else if ($key == 'TimeIn') {
		   //Convert dates 
			$FormValues['TimeIn'] = $_POST['TimeIn'].":00";
		  }

		  else if ($key == 'Client') {
				$FormValues[$key]=$value;
				/* load client model and connect to db */
				$this->load->model('trakclients', '', TRUE);
				//Call model routine
				$clientcode = $this->trakclients->GetEntry($options = array('CompanyName' => trim($value)));
					$FormValues['Code']=$clientcode->ClientCode;

		  }
		  //else if ($key == 'clientContacts') {
			//	$FormValues['Originator']=trim($value);
		  //}
		  
		  else {
			$FormValues[$key]=trim($value);
		  }
		}
		$Owner_em= $this->session->userdata('user_email');
		$Owner_na= explode("@", $Owner_em);
		$Owner_fi= explode(".", $Owner_na[0]);
		if ($Owner_fi[0]) {
			$Owner= ucfirst($Owner_fi[0]);
		}
		else {
			$Owner= ucfirst($Owner_na[0]);
		}
		if (!isset($FormValues['Status'])){$FormValues['Status']='SCHEDULED';}
		switch ( $FormValues['Status']){
			case 'TENTATIVE':
				$FormValues['TentativeBy']=$Owner;
				$FormValues['ScheduledBy']=$Owner;
				$FormValues['WorkedBy']="";
				$FormValues['ProofedBy']="";
				$FormValues['CompletedBy']="";
				$FormValues['BilledBy']="";
				$FormValues['PaidBy']="";
				break;
			case 'SCHEDULED':
				$FormValues['TentativeBy']=$Owner;
				$FormValues['ScheduledBy']=$Owner;
				$FormValues['WorkedBy']="";
				$FormValues['ProofedBy']="";
				$FormValues['CompletedBy']="";
				$FormValues['BilledBy']="";
				$FormValues['PaidBy']="";
				break;
			case 'IN PROGRESS':
				$FormValues['TentativeBy']=$Owner;
				$FormValues['ScheduledBy']=$Owner;
				$FormValues['WorkedBy']=$Owner;
				$FormValues['ProofedBy']="";
				$FormValues['CompletedBy']="";
				$FormValues['BilledBy']="";
				$FormValues['PaidBy']="";
				break;
			case 'IN PROOFING':
				$FormValues['TentativeBy']=$Owner;
				$FormValues['ScheduledBy']=$Owner;
				$FormValues['WorkedBy']=$Owner;
				$FormValues['ProofedBy']=$Owner;
				$FormValues['CompletedBy']="";
				$FormValues['BilledBy']="";
				$FormValues['PaidBy']="";
				break;
			case 'COMPLETED':
				$FormValues['TentativeBy']=$Owner;
				$FormValues['ScheduledBy']=$Owner;
				$FormValues['WorkedBy']=$Owner;
				$FormValues['ProofedBy']=$Owner;
				$FormValues['CompletedBy']=$Owner;
				$FormValues['BilledBy']="";
				$FormValues['PaidBy']="";
				
				// create the DateTimeZone object
					$dtzone = new DateTimeZone($FormValues['TimeZoneOut']);
					
					// Get server current time
						$timestamp = time();
					//  convert the server timestamp into a string representing the local time
						$timenow = date('r', $timestamp);
					// now create the DateTime object for this time
						$dtime = new DateTime($timenow);
					// convert this to the client's timezone using the DateTimeZone object
						$dtime->setTimeZone($dtzone);	
					
					$FormValues['TimeOut']=$dtime->format('H:i:s');
					$FormValues['DateOut']=$dtime->format('y-m-d');/**/
				
					//$firstcontactiteration=$this->_firstcontactiteration($FormValues['Originator']);
				
				break;
		}

		/* load model and connect to db */
		$this->load->model('trakentries', '', TRUE);
    	//Call model routine
		$newentry = $this->trakentries->AddEntry($FormValues);
		
		if($newentry)
			{
			echo "Entry created";
			}
		else
			echo "There was an error adding your entry.";

	}

	// ##################  firstcontactiteration check ##################	
	function  _firstcontactiteration($originator)
	{
		$originatorname = explode(" ", $originator);
		$originator = $originatorname[count($originatorname)-1];
		/* load model and connect to db */
		$this->load->model('trakcontacts', '', TRUE);
		$this->trakcontacts->firstcontactiteration(array('Originator'=>$originator));
	}
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>