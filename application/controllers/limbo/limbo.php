<?php

class Limbo extends MY_Controller {
	
	function index()
	{
		
		$this->load->helper(array('zowtrakui','url','limbo','userpermissions','form'));

	
		//Get user name
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$templateVars['pageOutput'] .= $this-> _getTopBar();
		$templateVars['pageOutput'] .= $this-> _getdircontent();
		
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
			$TopBar .="<h1>Limbo</h1>";
			$TopBar .=_limboToolset($currentdir);
			$TopBar .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			$TopBar .=_createpageswitcher();
			$TopBar .="<a href=\"".site_url()."tracking\" class=\"logout\">Tracking</a>";
			$TopBar .="</div>";
		
			
			return $TopBar;

	}
	// ################## ftp content ##################
	function _getdircontent()
	{
			$list=array();
			$filesizes=array();
			//$dir = $_SERVER['NFSN_SITE_ROOT'] . "protected/limbo/";
			$dir = $_SERVER['DOCUMENT_ROOT']."/zowtempa/etc/limbo/";
		
				// Open a known directory, and proceed to read its contents
				if (is_dir($dir)) {
					if ($dh = opendir($dir)) {
							while (($file = readdir($dh)) !== false) {
									if ($file!='.' && $file!='..') {
										$list[]= $file;
										$filesizes[]=filesize($dir.$file);
										
									}
							}
							closedir($dh);
					}
					else {echo "No files found".$clientcode." ".$dir;}
				}
		
		
		 return _createlistlimbolist($list,$filesizes);
	
	}
}	
/* End of file Limbo.php */
/* Location: ./system/application/controllers/limbo/limbo.php */
?>