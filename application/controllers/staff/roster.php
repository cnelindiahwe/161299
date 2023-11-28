<?php

class Roster extends MY_Controller {


	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('zowtrakui','userpermissions'));
		
		
		$templateVars['ZOWuser']=_getCurrentUser();



		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		

		$templateVars['pageOutput'] .= $this->_getTopMenu();

		$templateVars['pageOutput'] .= "<div class=\"content\">";
		$templateVars['pageOutput'] .= $this->_getZOWofficestimes();

		$templateVars['pageOutput'] .= "</div><!-- content -->";
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "fin_trends";
		$templateVars['pageType'] = "fin_trends";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	// ################## top ##################	

	function  _getTopMenu()

	{
			$TopBar ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$TopBar.="<h1>Staff - Roster</h1>";

			
			//Add logout button
			$TopBar .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			//page switcher
			$TopBar .=_createpageswitcher();
			//Tracking button
			$TopBar .="<a href=\"".site_url()."tracking\" class=\"logout\">Tracking</a>";

			$TopBar.="</div>";
		
			
			return $TopBar;

	}



 	function  _getZOWofficestimes() {
 		
		// create the DateTimeZone object for later
			$Atzone = new DateTimeZone('Europe/Berlin');
			$Mtzone= new DateTimeZone('Asia/Kolkata');	
		
		// Get server current time
			$timestamp = time();
		//  convert the server timestamp into a string representing the local time
			$timenow = date('r', $timestamp);
		// now create the DateTime object for this time
			$Atime = new DateTime($timenow);
			$Mtime = new DateTime($timenow);
		// convert this to the office's timezone using the DateTimeZone object
			$Atime->setTimeZone($Atzone);
			$Mtime->setTimeZone($Mtzone);	
 		
		$ZOWofficetimes="<div>";
		$ZOWofficetimes="<h3>Current time</h3>";
		$ZOWofficetimes.="Amsterdam: ";
		$ZOWofficetimes.= $Atime->format('g:i a (F-j-Y)'); 
		$ZOWofficetimes.="<br/><br/>";
		$ZOWofficetimes.="Mumbai: ";
		$ZOWofficetimes.= $Mtime->format('g:i a (F-j-Y)'); 
		
		return $ZOWofficetimes;
		
		
 	}
 	
 	
 		


/* End of file fin_totals.php */
/* Location: ./system/application/controllers/financials/fin_totals.php */
}

?>