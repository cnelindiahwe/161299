<?php

class Dump extends MY_Controller {

	function Dump()
	{
	
		// load parent controller	
		parent::MY_Controller();	
	}
	
	function index()
	{
		
		$this->load->helper(array('form','url'));
		
		

		$templateVars['pageOutput'] = "<h3>Clients:</h3>";
		$templateVars['pageOutput'] .= $this-> _getClientsDump();
		$templateVars['pageOutput'] .= "<h3>Entries:</h3>";
		$templateVars['pageOutput'] .= $this-> _getEntriesDump();
		$templateVars['pageOutput'] .= "<br/>";
		
		$templateVars['pageInput'] = "Raw database output (all fields, all entries)";
		$templateVars['baseurl'] = site_url();
		$templateVars['pageType'] = "dump";
		$templateVars['pageName'] = "Dump";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtraktemplate');


	}
	




	// ################## Trashed entry list ##################	
	function  _getEntriesDump()
	{
		$this->load->model('trakentries', '', TRUE);
		
		$getentries = $this->trakentries->GetEntry($options = array( 'sortBy'=> 'id','sortDirection'=> 'desc'));
		
		if($getentries)
		{

			$entries="";
			//$entries.= $this->db->last_query();
			
			//Get header names
			$headers =$getentries[0] ;
			$dbfields= get_object_vars($headers);
			$entries .= "<table id=\"entriesdump\">\n";
			$entries .= "<thead>\n";
			$entries .= "<tr>\n";
			foreach ($dbfields as $key=>$value)
				{
				   $entries .= "<th class=\"".$key."\">".$key."</th>";
				} 
			$entries .= "</tr>\n";
			$entries .= "</thead>\n";


			
			$entries .= "<tbody>\n";
			foreach($getentries as $row)
			{
				$entries .= "<tr>";
				$dbfields= get_object_vars($row);
				//$entries .= "<td class=\"".$key."\">".$key."</td>";
				foreach ($dbfields as $key=>$value)
					{
					   if  ($key=="DateIn" OR $key=="DateOut" ){
					   		$mysqldate = date( 'd/M/Y',strtotime($value));
							$entries .= "<td class=\"".$key."\">".$mysqldate."</td>";
					   }
					   else {
					   
					   $entries .= "<td class=\"".$key."\">".$value."</td>";
					  }
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
	// ################## Trashed Client list ##################	
	function  _getClientsDump()
	{		$this->load->model('trakclients', '', TRUE);
		
		$getentries = $this->trakclients->GetEntry($options = array( 'sortBy'=> 'ID','sortDirection'=> 'desc'));
		
		if($getentries)
		{

			
			$headers =$getentries[0] ;
			$dbfields= get_object_vars($headers);
			$entries= "";
			$entries.= "<table id=\"clientsdump\">\n";
			$entries .= "<thead>\n";
			$entries .= "<tr>\n";
			foreach ($dbfields as $key=>$value)
				{
				   $entries .= "<th class=\"".$key."\">".$key."</th>";
				} 
			$entries .= "</tr>\n";
			$entries .= "</thead>\n";
			
			$entries .= "<tbody>\n";
			foreach($getentries as $row)
			{
				$entries .= "<tr>";
				$dbfields= get_object_vars($row);
				//$entries .= "<td class=\"".$key."\">".$key."</td>";
				foreach ($dbfields as $key=>$value)
					{
					   $entries .= "<td class=\"".$key."\">".$value."</td>";
					} 
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





}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>