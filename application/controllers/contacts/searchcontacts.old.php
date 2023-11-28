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
		
		_superuseronly(); 
		
		/* load model and connect to db */

		$fields=$_POST;
		$fields["ID"] = $this->uri->segment(3);

		
		$this->load->model('trakclients', '', TRUE);
		$ClientList  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));


		$this->load->helper(array('contacts'));

		if (!isset($CurrentClient)){$CurrentClient="All";}
		$templateVars['pageInput'] = $this-> _getContactpage($ClientList);
		$templateVars['pageInput'] .= $this->_displaySearchResults($fields);
		

		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Contacts";
		$templateVars['pageType'] = "contacts";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');

	}
	// ################## main page ##################	
	function  _getContactpage($ClientList)
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
		$ClientContacts = $this->trakcontacts->GetEntryLike($options = array('Trash' => '0','LastName' => $fields['searchedname'],'sortBy' => 'LastName','sortDirection'=>'ASC'));
		//echo $this->db->last_query();
		if ($ClientContacts ) {
			$searchContactsTable.= "<table id=\"currententries\">\n";
			$searchContactsTable .= "<thead>\n";
			$searchContactsTable .= "<tr><th class=\"header company\">Name</th><th class=\"header code\">Cell Phone</th><th class=\"header contact\">Email</th></tr>\n";
			$searchContactsTable .= "</thead>\n";
			
			$searchContactsTable .= "<tbody>\n";
			
			foreach($ClientContacts as $Contact)
			{
				$searchContactsTable.="<tr><td><a href=\"".base_url()."contacts/editcontact/$Contact->ID\">".$Contact->FirstName." ".$Contact->LastName."</a></td>";
				$searchContactsTable.="<td><a href=\"".base_url()."contacts/editcontact/$Contact->ID\">".$Contact->Cellphone1."</a></td><td><a href=\"".base_url()."contacts/editcontact/$Contact->ID\">".$Contact->Email1."</a></td></tr>";
			}
			$searchContactsTable.="</tbody></table>";
		}
		else{
			$searchContactsTable .="No contacts.";
			}
			

		
		return $searchContactsTable;
}

}

/* End of file addcontact.php */
/* Location: ./system/application/controllers/contacts/addcontact.php */
?>