<?php

class Viewclientcontacts extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 

		$this->load->helper(array('url','general','form','userpermissions'));
		$templateVars['ZOWuser']=_superuseronly(); 
		
		
		$clientid= $this->uri->segment(3);

		
		//Read form input values
		if (!isset($clientid) || $clientid=="" || $clientid=="all") {
			$clientid=$this->input->post('clientselector');
		}

		if (!isset($clientid) || $clientid=="" || $clientid=="all") {
			redirect('contacts');
		}
		
		
		
		/* load model and connect to db */
		$templateVars['current'] =$clientid;
		
		$this->load->model('trakclients', '', TRUE);
		$ClientList  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		foreach($ClientList  as $client)
		{
			if ($client->ID==$templateVars['current'] ){
				$CurrentClient=$client;
			}
		}	

		$this->load->helper(array('contacts'));

		if (!isset($CurrentClient)){$CurrentClient="All";}
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageInput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageInput'] .= $this->_topmenu($ClientList,$CurrentClient);
		$templateVars['pageInput'] .= _displayClientContactsTable($CurrentClient);
		$templateVars['pageInput'] .=_getContactsForm($ClientList,"",$CurrentClient);
		

		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Contacts";
		$templateVars['pageType'] = "contacts";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');

	}
	// ################## main page ##################	
	function _topmenu($ClientList,$CurrentClient)
	{
			$this->load->model('trakcontacts', '', TRUE);
			$ContactList = $this->trakcontacts->GetEntry($options = array('Trash'=>'0','CompanyName'=>$CurrentClient->CompanyName,));
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>".count($ContactList)." contacts for ".$CurrentClient->CompanyName."</h1>";
			$entries .= _displayClientContactsDropdown($ClientList,$CurrentClient->ID);	
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";

			return $entries;

	}
	
}

/* End of file addcontact.php */
/* Location: ./system/application/controllers/contacts/addcontact.php */
?>