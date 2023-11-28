<?php

class Searchcontacts extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('contacts','general','form','userpermissions', 'url'));
		
		$templateVars['ZOWuser']=_superuseronly(); 
		
		

		$fields=$_POST;
		if (!isset($fields['searchedname'])){
			redirect('contacts');
		}
		else if ($fields['searchedname']=="") {
			redirect('contacts');
		}

		
		$this->load->model('trakclients', '', TRUE);
		$ClientList  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));


		$this->load->helper(array('contacts'));

		if (!isset($CurrentClient)){$CurrentClient="All";}
		$templateVars['pageInput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageInput'] .= $this-> _getTopPage($ClientList);
		$templateVars['pageInput'] .= $this->_displaySearchResults($fields);
		

		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Contacts";
		$templateVars['pageType'] = "contacts";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');

	}
	// ################## main page ##################	
	function  _getTopPage($ClientList)
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

//######################## Display search results ##########################
function _displaySearchResults($fields) 
{
		$searchContactsTable ="<h3>Search results for ".$fields['searchedname']."</h3>";
		$this->load->model('trakcontacts', '', TRUE);
		$foundcontacts = $this->trakcontacts->SearchOriginator($options = array('Originator' => $fields['searchedname'],'sortBy' => 'LastName','sortDirection'=>'ASC'));


		if ($foundcontacts ) {
			$searchContactsTable.= "<table id=\"currententries\">\n";
			$searchContactsTable .= "<thead>\n";
			$searchContactsTable .= "<tr><th class=\"header company\">Name</th<th class=\"header contact\">Client</th><th class=\"header contact\">Email</th><th class=\"header code\">Cell Phone</th></tr>\n";
			$searchContactsTable .= "</thead>\n";
			
			$searchContactsTable .= "<tbody>\n";
			
			foreach($foundcontacts as $Contact)
			{
				$searchContactsTable.="<tr><td><a href=\"".base_url()."contacts/editcontact/$Contact->ID\">".$Contact->FirstName." ".$Contact->LastName."</a></td>";
				$searchContactsTable.="<td>".$Contact->CompanyName."</td><td>".$Contact->Email1."</td><td>".$Contact->Cellphone1."</td></tr>";
			}
			$searchContactsTable.="</tbody></table>";
		}
		else{
			$searchContactsTable .="No contacts.<br/>";
			//$searchContactsTable .= $this->db->last_query();
			}
			

		/**/
		return $searchContactsTable;
}

}

/* End of file addcontact.php */
/* Location: ./system/application/controllers/contacts/addcontact.php */
?>