<?php

class Limbodir extends MY_Controller {
	
	function index()
	{

		$this->load->helper(array('url','limbo','userpermissions','form'));

		//check that user is not going back to base dir	
		if (uri_string()=="limbo/limbodir" ) redirect(base_url()."limbo/limbo/");	
		
		$finaldir= str_replace("limbo/limbodir/", "", uri_string());
		$finaldir= str_replace("%20", " ", $finaldir);
		if ($finaldir=="") redirect(base_url()."limbo/limbo/");

		
		//Get user name
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$templateVars['pageOutput'] .= $this-> _getTopBar($finaldir);
		$templateVars['pageOutput'] .= $this-> _getdircontent($finaldir);;
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageType'] = "limbo";
		$templateVars['pageName'] = "Limbo";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');		
	}
	// ################## top bar ##################	
	function  _getTopBar($currentdir)
	{
			$TopBar ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$TopBar .="<h1>Limbo</h1>";
			$TopBar .=_limboToolset($currentdir);
			$TopBar .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			$TopBar .="<a href=\"".site_url()."reports\" class=\"logout\">Reports</a>";
			$TopBar .="<a href=\"".site_url()."tracking\" class=\"logout\">Tracking</a>";
			$TopBar .="</div>";
		
			
			return $TopBar;

	}


	function _getdircontent($finaldir)
	{
			$list=array();
			$dir = $_SERVER['NFSN_SITE_ROOT'] . 'protected/limbo/'.$finaldir;
				// Open a known directory, and proceed to read its contents
				if (is_dir($dir)) {
					if ($dh = opendir($dir)) {
							while (($file = readdir($dh)) !== false) {
									if ($file!='.') {
										$list[]= $finaldir."/".$file;
									}
							}
							closedir($dh);
					}
					else {echo "No files found";}
				}
		
		
		 return _createlistlimbolist($list,$finaldir);
	
	}
	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>