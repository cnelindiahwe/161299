<?php

class Addcontact extends MY_Controller {


	
	function index()
	{

		$this->load->helper('url');	
		/* load model and connect to db */
		$this->load->model('trakcontacts', '', TRUE);

    	//Call model routine
		
		foreach ($_POST as $key=>$value) {
			$required = array ("CompanyName","LastName","FirstName");
			if (in_array($key,$required) && $value==""){
				echo "Required field ".$key." is missing";
				redirect('contacts', 'refresh');
			}
			else{
				$FormValues[$key]=trim($value);
			}
		} 

		$newentry = $this->trakcontacts->AddEntry($FormValues);
		
		if($newentry)
			{
			$this->load->model('trakclients', '', TRUE);
			$currentclient = $this->trakclients->GetEntry($options = array('CompanyName' => $FormValues["CompanyName"]));
				if($currentclient){
					redirect('contacts/viewclientcontacts/'.$currentclient->ID, 'refresh');
				}
			}
		
			echo "There was an error adding your entry.";
			//echo $this->db->last_query();




		/*$this->load->helper(array('contacts'));
		$this->load->model('trakclients', '', TRUE);
		$ClientList = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		$templateVars['pageSidebar'] = displayClientContactsList($ClientList);		
		$templateVars['pageInput'] = getContactsForm();
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Contacts";
		$templateVars['pageType'] = "contacts";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtraktemplate');*/


	}
	
}

/* End of file addcontact.php */
/* Location: ./system/application/controllers/contacts/addcontact.php */
?>