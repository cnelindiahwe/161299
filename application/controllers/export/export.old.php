<?php

class Export extends MY_Controller {


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
		
		$templateVars['pageInput'] = "";
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
			$entries .= "<a href=\"export/invoices2csv\">Invoices</a>";
			$entries .= "<a href=\"export/databasebackup\">DB BackUp</a>";
			$entries .= "<a href=\"export/phpinfo\">PHP Info</a>";
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

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