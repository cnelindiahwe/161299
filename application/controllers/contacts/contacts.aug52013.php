<?php

class Contacts extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('contacts','general','form','userpermissions', 'url'));
		
		$templateVars['ZOWuser']=_superuseronly(); 

		$this->load->model('trakclients', '', TRUE);
		$ClientList = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));

	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageInput'] =  _getmanagerbar($templateVars['ZOWuser']);


		$templateVars['pageInput'] .= $this->_getContactpage($ClientList);
		 _displayClientContactsList($ClientList);		

		$templateVars['pageInput'] .=_getContactsForm($ClientList);
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Contacts";
		$templateVars['pageType'] = "contacts";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');
	}

	// ################## main page ##################	
	function  _getContactpage($ClientList)
	{
			$this->load->model('trakcontacts', '', TRUE);
			$ContactList = $this->trakcontacts->GetEntry($options = array('Trash'=>'0'));
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>".count($ContactList)." Contacts</h1>";
			$entries .= _displayClientContactsDropdown($ClientList);	
			$entries .=_getSearchContactForm();
				//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";

			return $entries;

	}

	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>