<?php

class Trash extends MY_Controller {


	function index()
	{
		
		$this->load->helper(array('form','url','userpermissions'));
		
		
		//Get user name
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$trashed = $this-> _getTrashedEntries();
		$templateVars['pageOutput'] .= $this-> _getTopBar($trashed);
		$templateVars['pageOutput'] .= $trashed;
		$templateVars['pageOutput'] .= "<br/>";
		$templateVars['pageOutput'] .= $this-> _getTrashedClients();
		$templateVars['pageOutput'] .= "<br/>";
		$templateVars['pageOutput'] .= $this-> _getTrashedContacts();
		
		$templateVars['pageInput'] = "";
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
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}



	// ################## Trashed entry list ##################	
	function  _getTrashedEntries()
	{
		$this->load->model('trakentries', '', TRUE);
		
		$getentries = $this->trakentries->GetEntry($options = array('Trash' => '1'));
		
		if($getentries)
		{
			$entries="";
			//$entries.= $this->db->last_query();
			$entries .= "<table id=\"currententries\">\n";
			$entries .= "<thead>\n";
			$entries .= "<tr><th class=\"header status\">Status</th><th class=\"header client\">Client</th><th class=\"header date\">Date</th><th class=\"header originator\">Originator</th><th class=\"header slides\">New Slides</th><th class=\"header slides\">Edits Slides</th><th class=\"header slides\"># Hours</th><th class=\"filename\">File Name</th><th class=\"button edit\"></th><th class=\"button edit\"></th></tr>\n";
			$entries .= "</thead>\n";
			
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
				$entries .= "<td class=\"button delete\"><a href=\"deleteentry/".$project->id . "\" class=\"delete\">Delete</a></td>";
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
	// ################## Trashed Client list ##################	
	function  _getTrashedClients()
	{		$this->load->model('trakclients', '', TRUE);
		
		$getentries = $this->trakclients->GetEntry($options = array('Trash' => '1'));
		
		if($getentries)
		{
			$entries= "<table id=\"currententries\">\n";
			$entries .= "<thead>\n";
			$entries .= "<tr><th class=\"header company\">Client Name</th><th class=\"header contact\">ZOW Contact</th><th class=\"header Retainer\">Retainer</th><th class=\"header BasePrice\">'BasePrice</th><th class=\"header retainer\">Retainer</th><th class=\"header currency\">Currency</th><th class=\"header version\">Version</th><th class=\"button edit\"></th><th class=\"button edit\"></th></tr>\n";
			$entries .= "</thead>\n";
			
			$entries .= "<tbody>\n";
			foreach($getentries as $project)
			{
				$entries .= "<tr>";
				$entries .= "<td class=\"company\">".$project->CompanyName . "</td>";
				$entries .= "<td class=\"contact\">".$project->ZOWContact . "</td>";

				$entries .= "<td class=\"PricePer0Hours\">".$project->BasePrice. "</td>";



				$entries .= "<td class=\"PriceEdits\">".$project->PriceEdits . "</td>";

				$entries .= "<td class=\"retainer\">".$project->RetainerHours . "</td>";
				$entries .= "<td class=\"currency\">".$project->Currency . "</td>";
				$entries .= "<td class=\"version\">".$project->OfficeVersion . "</td>";




				$entries .= "<td class=\"button edit\"><a href=\"".site_url()."trash/untrashclient/".$project->ID . "\" class=\"edit\">Restore</a></td>";
				$entries .= "<td class=\"button delete\"><a href=\"".site_url()."trash/deleteclient/".$project->ID . "\" class=\"delete\">Delete</a></td>";
				$entries .= "</tr>\n";
			}
			$entries .= "</tbody>\n";
			$entries .= "</table>\n";

		}
		else
		{
			$entries = "No trashed clients";
		}
		return $entries;
	}


	// ################## Trashed Client list ##################	
	function  _getTrashedContacts()
	{		$this->load->model('trakcontacts', '', TRUE);
		
		$getentries = $this->trakcontacts->GetEntry($options = array('Trash' => '1'));
		
		if($getentries)
		{
			$entries= "<table id=\"currententries\">\n";
			$entries .= "<thead>\n";
			$entries .= "<tr><th class=\"header company\">Contact Name</th><th class=\"header Company\">Company</th><th></th><th></th></tr>\n";
			$entries .= "</thead>\n";
			
			$entries .= "<tbody>\n";
			foreach($getentries as $project)
			{
				$entries .= "<tr>";
				$entries .= "<td class=\"company\">".$project->FirstName . " ".$project->LastName."</td>";
				$entries .= "<td class=\"contact\">".$project->CompanyName . "</td>";
				$entries .= "<td class=\"button edit\"><a href=\"".site_url()."trash/untrashcontact/".$project->ID . "\" class=\"edit\">Restore</a></td>";
				$entries .= "<td class=\"button delete\"><a href=\"".site_url()."trash/deletecontact/".$project->ID . "\" class=\"delete\">Delete</a></td>";
				$entries .= "</tr>\n";
			}
			$entries .= "</tbody>\n";
			$entries .= "</table>\n";

		}
		else
		{
			$entries = "No trashed contacts";
		}
		return $entries;
	}


}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>