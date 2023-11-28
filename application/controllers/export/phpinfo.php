<?php

class Phpinfo extends MY_Controller {


	function index()
	{
		
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 


				$this->load->helper(array('userpermissions','form','url'));
		$templateVars['ZOWuser']=_superuseronly(); 		
		
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$Worktype="";
		$templateVars['pageOutput'] .= $this->_gettopmenu();
		
		$templateVars['pageInput'] = phpinfo();
		$templateVars['baseurl'] = site_url();
		$templateVars['pageType'] = "export";
		$templateVars['pageName'] = "Export";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	
	// ################## top ##################	

	function  _gettopmenu()

	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Export</h1>";
			$entries .="<a href=\"export/entries2csv\">Entries</a>";
			$entries .="<a href=\"export/clients2csv\">Clients</a>";
			$entries .= "<a href=\"export/contacts2csv\">Contacts</a>";
			$entries .= "<a href=\"export/databasebackup\">DB BackUp</a>";
			$entries .= "<a href=\"export/phpinfo\">PHP Info</a>";
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

		
	}
	
}
		