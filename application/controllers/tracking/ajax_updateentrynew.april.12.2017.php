<?php

class Ajax_updateentrynew extends MY_Controller {

	
	function index()
	{


		/* */
		//load model and connect to db 
		$this->load->model('trakentries', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		$this->load->helper('url');

   		
		
		//read form	values
			$fields=$_POST;
			$fields["id"] = $_POST['id'];
			
			$fields['DateIn'] = date( 'Y-m-d',strtotime(str_replace("/","-",$_POST['DateIn'])));
			$fields['DateOut'] = date( 'Y-m-d',strtotime(str_replace("/","-",$_POST['DateOut'])));
			//$fields['Originator'] = $_POST['clientContacts'];
			foreach ($fields as $key=>$value) {
				$FormValues[$key]=trim($value);
			  	if ($key == 'Client') {

					//Call model routine
					$clientcode = $this->trakclients->GetEntry($options = array('CompanyName' => trim($value)));
						$FormValues['Code']=$clientcode->ClientCode;

		  		}
			}

		

		 
			//Determine owener and fill out fields
			$Owner_em= $this->session->userdata('user_email');
			$Owner_na= explode("@", $Owner_em);
			$Owner_fi= explode(".", $Owner_na[0]);
			if ($Owner_fi[0]) {
				$Owner= ucfirst($Owner_fi[0]);
			}
			else {
				$Owner= ucfirst($Owner_na[0]);
			} 
			switch ( $FormValues['Status']){
				case 'TENTATIVE':
					if ($FormValues['TentativeBy']=="") $FormValues['TentativeBy']=$Owner;
					$FormValues['ScheduledBy']="";
					$FormValues['WorkedBy']="";
					$FormValues['ProofedBy']="";
					$FormValues['CompletedBy']="";
					$FormValues['BilledBy']="";
					$FormValues['PaidBy']="";
					$FormValues['ScheduledTimeOut']="";
					$FormValues['ScheduledDateOut']="";
					break;
				case 'SCHEDULED':
					if ($FormValues['TentativeBy']=="") $FormValues['TentativeBy']=$Owner;
					if ($FormValues['ScheduledBy']=="") $FormValues['ScheduledBy']=$Owner;
					$FormValues['WorkedBy']="";
					$FormValues['ProofedBy']="";
					$FormValues['CompletedBy']="";
					$FormValues['BilledBy']="";
					$FormValues['PaidBy']="";
					$FormValues['ScheduledTimeOut']="";
					$FormValues['ScheduledDateOut']="";
					break;
				case 'IN PROGRESS':
					if ($FormValues['TentativeBy']=="") $FormValues['TentativeBy']=$Owner;
					if ($FormValues['ScheduledBy']=="") $FormValues['ScheduledBy']=$Owner;
					if ($FormValues['WorkedBy']=="") $FormValues['WorkedBy']=$Owner;
					$FormValues['ProofedBy']="";
					$FormValues['CompletedBy']="";
					$FormValues['BilledBy']="";
					$FormValues['PaidBy']="";
					$FormValues['ScheduledTimeOut']="";
					$FormValues['ScheduledDateOut']="";
					break;
				case 'IN PROOFING':
					if ($FormValues['TentativeBy']=="") $FormValues['TentativeBy']=$Owner;
					if ($FormValues['ScheduledBy']=="") $FormValues['ScheduledBy']=$Owner;
					if ($FormValues['WorkedBy']=="") $FormValues['WorkedBy']=$Owner;
					if ($FormValues['ProofedBy']=="") $FormValues['ProofedBy']=$Owner;
					$FormValues['CompletedBy']="";
					$FormValues['BilledBy']="";
					$FormValues['PaidBy']="";
					$FormValues['ScheduledTimeOut']="";
					$FormValues['ScheduledDateOut']="";
					break;
				case 'COMPLETED':
					if ($FormValues['TentativeBy']=="") $FormValues['TentativeBy']=$Owner;
					if ($FormValues['ScheduledBy']=="") $FormValues['ScheduledBy']=$Owner;
					if ($FormValues['WorkedBy']=="") $FormValues['WorkedBy']=$Owner;
					if ($FormValues['ProofedBy']=="") $FormValues['ProofedBy']=$Owner;
					if ($FormValues['CompletedBy']=="") $FormValues['CompletedBy']=$Owner;
					$FormValues['BilledBy']="";
					$FormValues['PaidBy']="";
					$FormValues['ScheduledTimeOut']=$FormValues['TimeOut'];
					$FormValues['ScheduledDateOut']=$FormValues['DateOut'];

				  $this->db->from('zowtrakentries');
				  $this->db->where('id >=',$fields["id"]);
				  $this->db->select('Status');
				  $query = $this->db->get();
				  $oldstatus=$query->row(0)->Status;
					 if ($oldstatus!=$FormValues['Status']) {
	
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
			}

			//Call model routine
			$clientcode = $this->trakclients->GetEntry($options = array('CompanyName' => $FormValues['Client']));
			$fields['Code']=$clientcode->ClientCode;
			
			
		$uentry = $this->trakentries->UpdateEntry($FormValues);
		
		if($uentry)
			{
			echo "Entry changed";
			}
		else
		{
			echo "There was an error editing the entry.";
			echo $this->db->last_query();
		}
		
		//redirect('newentry', 'refresh');

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
/* Location: ./system/application/controllers/updateentry.php */
?>