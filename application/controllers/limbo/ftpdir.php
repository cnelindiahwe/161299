<?php

class Ftpdir extends MY_Controller {
	
	function index()
	{
		$this->load->library('ftp');
		$this->load->helper(array('url','limbo','userpermissions','form'));

		//check that user is not going back to base dir	
		if (uri_string()=="limbo/ftpdir" ) redirect(base_url()."limbo/limbo/");	
		
		$finaldir= str_replace("limbo/ftpdir/", "", uri_string());
		$finaldir= str_replace("%20", " ", $finaldir);
		if ($finaldir=="") redirect(base_url()."limbo/limbo/");
		
		
		//Get user name
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$templateVars['pageOutput'] .= $this-> _getTopBar($finaldir);
		$templateVars['pageOutput'] .= $this-> _getFTPcontent($finaldir);;
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageType'] = "limbo";
		$templateVars['pageName'] = "Limbo";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');		
	}
	// ################## top bar ##################	
	function  _getTopBar($currentdir="Home")
	{
			$TopBar ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$TopBar .="<h1>Limbo/".$currentdir."/</h1>";
			$TopBar .=_ftpToolset($currentdir);
			$TopBar .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			$TopBar .="<a href=\"".site_url()."reports\" class=\"logout\">Reports</a>";
			$TopBar .="<a href=\"".site_url()."tracking\" class=\"logout\">Tracking</a>";
			$TopBar .="</div>";
		
			
			return $TopBar;

	}
	// ################## ftp content ##################
	function _getFTPcontent($finaldir)
	{

		
		$this->ftp->connect();
		
		$list = $this->ftp->list_files('/private/limbo/'.$finaldir.'/');
		$this->ftp->close();	
		return _createlistlimbolist($list,$finaldir);
	}


	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>