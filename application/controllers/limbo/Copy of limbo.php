<?php

class Limbo extends MY_Controller {
	
	function index()
	{
		$this->load->library('ftp');
		$this->load->helper(array('url','limbo','userpermissions','form'));

	
		//Get user name
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$templateVars['pageOutput'] .= $this-> _getTopBar();
		$templateVars['pageOutput'] .= $this-> _getFTPcontent();
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageType'] = "limbo";
		$templateVars['pageName'] = "Limbo";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');		
		
	}
	// ################## top bar ##################	
	function  _getTopBar($currentdir="")
	{
			$TopBar ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$TopBar .="<h1>Limbo/".$currentdir."</h1>";
			$TopBar .=_ftpToolset($currentdir);
			$TopBar .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			$TopBar .="<a href=\"".site_url()."reports\" class=\"logout\">Reports</a>";
			$TopBar .="<a href=\"".site_url()."tracking\" class=\"logout\">Tracking</a>";
			$TopBar .="</div>";
		
			
			return $TopBar;

	}
	// ################## ftp content ##################
	function _getFTPcontent()
	{
		
		$this->ftp->connect();
		
		$list = $this->ftp->list_files('/private/limbo');
		$this->ftp->close();
		 return _createlistlimbolist($list);
	
	}
}	
/* End of file Limbo.php */
/* Location: ./system/application/controllers/limbo/limbo.php */
?>