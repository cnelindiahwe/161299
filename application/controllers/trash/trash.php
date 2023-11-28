<?php

class Trash extends MY_Controller {


	function index()
	{
		
		$this->load->helper(array('form','url','userpermissions'));
		
		
		//Get user name
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$trashed = $this-> _getTrashedEntries($templateVars['ZOWuser']);
		$templateVars['pageOutput'] .= $this-> _getTopBar($trashed);
		$templateVars['pageOutput'] .= $trashed;
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageType'] = "trash";
		$templateVars['pageName'] = "Trash";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	
	// ################## top bar ##################	
	function  _getTopBar($trashed)
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			if ($trashed!="No trashed contacts") {
				$entries .="<h1>Trashed Job(s)</h1>";
			} else 
			{
				$entries .="<h1>".$trashed."</h1>";
			}
			$entries .="<a href=\"".site_url()."tracking\">Tracking</a>";
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			$entries .="</div>";
		
			
			return $entries;

	}



	// ################## Trashed entry list ##################	
	function  _getTrashedEntries($ZOWuser)
	{
		$this->load->model('trakentries', '', TRUE);
		
		$getentries = $this->trakentries->GetEntry($options = array('Trash' => '1'));
		
		if($getentries)
		{
			$entries="";
			//$entries.= $this->db->last_query();
			$entries .= "<table id=\"currententries\">\n";
			$entries .= "<thead>\n";
			$entries .= "<tr><th class=\"header status\">Status</th><th class=\"header client\">Client</th><th class=\"header date\">Date</th><th class=\"header originator\">Originator</th><th class=\"header slides\">New Slides</th><th class=\"header slides\">Edits Slides</th><th class=\"header slides\"># Hours</th><th class=\"filename\">File Name</th><th class=\"button edit\"></th>";
			if ($ZOWuser=="miguel" ||	$ZOWuser=="sunil.singal") { 
				$entries .= "<th class=\"button edit\"></th></tr>\n";
			}
			$entries .= "</tr>\n</thead>\n";
			
			$entries .= "<tbody>\n";
			foreach($getentries as $project)
			{
				$entries .= "<tr>";
				$entries .= "<td class=\"status\">".$project->Status . "</td>";
				$entries .= "<td class=\"client\">".$project->Client . "</td>";
				//Converts MySQL date
				$mysqldate = date( 'd/M/Y',strtotime($project->DateIn));
				$entries .= "<td class=\"date\">".$mysqldate. "</td>";

				$entries .= "<td class=\"originator\">".$project->Originator . "</td>";
				$entries .= "<td class=\"slides\">".$project->NewSlides . "</td>";
				$entries .= "<td class=\"slides\">".$project->EditedSlides . "</td>";
				$entries .= "<td class=\"slides\">".$project->Hours . "</td>";
				$entries .= "<td class=\"filename\">".$project->FileName . "</td>";
				$entries .= "<td class=\"button edit\"><a href=\"untrashentry/".$project->id . "\" class=\"restore\">Restore</a></td>";
			if ($ZOWuser=="miguel" ||	$ZOWuser=="sunil.singal") { 
				$entries .= "<td class=\"button delete\"><a href=\"deleteentry/".$project->id . "\" class=\"delete\">Delete</a></td>";
			}
			$entries .= "</tr>\n";
			}
			$entries .= "</tbody>\n";
			$entries .= "</table>\n";

		}
		else
		{
			$entries = "No trashed jobs";
		}
		return $entries;
	}
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>