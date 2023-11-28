<?php

class Calculate_contacts extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('contacts','general','form','userpermissions', 'url'));
		
		$templateVars['ZOWuser']=_superuseronly(); 

		$this->load->model('trakclients', '', TRUE);
		$ClientList = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));

	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);


		$templateVars['pageOutput'] .= $this->_get_top_menu($ClientList);
		 //_displayClientContactsList($ClientList);		

		//$templateVars['pageInput'] .=_getContactsForm($ClientList);
		$templateVars['pageOutput'] .=$this->_getContacts_Main_Page($ClientList);
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Contacts";
		$templateVars['pageType'] = "contacts";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');
	}

	// ################## main page ##################	
	function  _get_top_menu($ClientList)
	{
			$this->load->model('trakcontacts', '', TRUE);
			$ContactList = $this->trakcontacts->GetEntry($options = array('Trash'=>'0'));
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>".count($ContactList)." Contacts</h1>";
			$entries .= _displayClientContactsDropdown($ClientList);	
			$entries .=_getSearchContactForm();
				//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";

			return $entries;

	}

	// ################## main page ##################	
	function  _getContacts_Main_Page($ClientList,$options=array())
	{
		$active_contacts="";
		

		$yeartotals="";
		 	$years = array()	;
	
			for ($i=2010; $i<=date("Y"); $i++){
				array_push($years, $i);
			}
			
	
			$current_year = date('Y');
			foreach ($years as $thisyear) {
				
				$yeartotals .="";
				
				$StartDate=$thisyear.'-1-1';
				$EndDate=$thisyear.'-12-31';
								$active_contacts.=$this->_calculate_contacts_total($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'ThisYear'=>$thisyear));
				$active_contacts.="</di>";

			} 		

		return $active_contacts;
		}
	// ################## main page ##################	
	function  _calculate_contacts_total($options=array())
	{
		 $originatorlist="";	
		 $originatorclientlist="";	
		 $originatorjobs="";	
		 $originatorrevenue="";
		 $originatorhours="";
		
		$thisyear=$options['ThisYear'];
		
 		//run db query	
 		$this->db->select('Client');
		$this->db->select('Originator');
		//$this->db->select_min('DateOut','StartDate') ;
		$this->db->select('count(id) as Jobs', FALSE);
		//$this->db->select_count('id','Jobs');
		$this->db->select_sum('InvoiceEntryTotal','Revenues');
		$this->db->select_sum('InvoiceTime','Hours');
		if (isset($options['StartDate']) && $options['StartDate']!="") {
			$this->db->where('DateOut >=',  $options['StartDate']);
		  	$this->db->where('DateOut <= ',  $options['EndDate']);
		}
		$this->db->where('Trash',0);
		$this->db->where("Invoice != 'NOT BILLED'");
		$this->db->group_by('Originator');
		$rawentries = $this->db->get('zowtrakentries');
		//if results exist, list them
		if ($rawentries) {
			//get client list
			$ClientTableRaw  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
			foreach ($ClientTableRaw as $row){
				 $ClientCurrency[$row->CompanyName] =$row->Currency;
			}
			$financial_totals ="<div class=\"yearpile\"><h3>By Originator</h3>";
			$financial_totals ="<table class=\"originatorsdata\"><thead><tr><th>Originator</th><th>Client</th><th>Revenues</th><th>Avg. Price</th><th>Currency</th><th>Jobs</th><th>Hours</th></tr></thead><tbody>";	
			foreach ($rawentries->result() as $row){
				 $financial_totals .="<tr><th scope=\"row\">".$row->Originator."</th>";
				 $financial_totals .="<td>".$row->Client."</td>";
				 if ($row->Revenues!=0){
					 $financial_totals .="<td>".number_format($row->Revenues,2)."</td>";
					 $financial_totals .="<td>".number_format($row->Revenues/$row->Hours,2)."</td>";
					 $financial_totals .="<td>".$ClientCurrency[$row->Client]."</td>";
					 $financial_totals .="<td>".$row->Jobs."</td>";				 	
					 $financial_totals .="<td>".number_format($row->Hours,1)."</td></tr>";
					 $originatorlist.=",".$row->Originator;	
					 $originatorclientlist.=",".$row->Client;	
					 $originatorjobs.=",".$row->Jobs;	
					 $originatorrevenue.=",".number_format($row->Revenues,2);
					 $originatorhours.=",".number_format($row->Hours,2);
				}
			}
			$financial_totals .="</tbody></table></div>";
			$this->db->set("OriginatorList", substr($originatorlist, 1));			
			$this->db->set("OriginatorClientList",substr($originatorclientlist, 1));	
			$this->db->set("OriginatorJobs", substr($originatorjobs, 1));					
			$this->db->set("OriginatorRevenue", substr($originatorrevenue, 1));
			$this->db->set("OriginatorHours", substr($originatorhours, 1));						
			$this->db->where('Year',$thisyear.'-01-01');
			$this->db->update('zowtrakyearsummaries');	
			
			
		}
		
		$financial_totals ="<div><h3>".$thisyear.": ".$rawentries->num_rows()." ctive contacts</h3>".$financial_totals;
		
		 return $financial_totals;

	}


	
}

/* End of file contacts.php */
/* Location: ./system/application/controllers/contacts/contacts.php */
?>