<?php

class Updatecontact extends My_Controller {


	function index()
	{


		/* load model and connect to db */
		$this->load->model('trakcontacts', '', TRUE);
			$this->load->helper('url');

   		//Call model routine
		
		
		
		
			$fields=$_POST;
			$fields["ID"] = $this->uri->segment(3);
			
			$oldentry = $this->trakcontacts->GetEntry(array ("ID"=>$fields["ID"]));
			
			//### check that entry exists
			if (!$oldentry) {
				//redirect('contacts', 'refresh');
				echo "There was an error updating your entry.";
				echo "No old entry exists.";
			}
			$oldoriginator = $oldentry->FirstName." ".$oldentry->LastName;
			
			//### update entry
			foreach ($fields as $key=>$value) {
				$FormValues[$key]=trim($value);
			} 
			$uentry = $this->trakcontacts->UpdateEntry($FormValues);

			$neworiginator = $FormValues["FirstName"]." ". $FormValues["LastName"];
		
			
			if($uentry)
			{
			$this->load->model('trakentries', '', TRUE);
			$contactentries = $this->trakentries->GetEntry(array ("Originator"=>$oldoriginator));
				if ($contactentries ){
					foreach ($contactentries as $row){
						$uentry = $this->trakentries->UpdateEntry(array("id"=>$row->id,"Originator"=>$neworiginator));
					}
				}	
			//$this->load->model('trakclients', '', TRUE);
			//$currentclient = $this->trakclients->GetEntry($options = array('CompanyName' => $FormValues["CompanyName"]));
			//	if($currentclient){
					//redirect('contacts/viewclientcontacts/'.$currentclient->ID, 'refresh');
			///	}			
			
			$this->load->model('trakclients', '', TRUE);
			$currentclient = $this->trakclients->GetEntry($options = array('CompanyName' => $oldentry->CompanyName));
				if($currentclient){
					redirect('contacts/viewclientcontacts/'.$currentclient->ID, 'refresh');
				}
				

			
			}
			else {
				echo "There was an error adding your entry.";
				echo $this->db->last_query();
			}


		//redirect('newentry', 'refresh');
	}
}

/* End of file updatecontact.php */
/* Location: ./system/application/controllers/contacts/updatecontact.php */
?>