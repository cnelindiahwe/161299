<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ZOWTRAK
 *
 * @package		ZOWTRAK
 * @author		Zebra On WHeels
 * @copyright	Copyright (c) 2010 - 2009, Zebra On WHeels
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Client Helpers
 *
 * @package		ZOWTRAK
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Zebra On WHeels

 */

// ------------------------------------------------------------------------

/**
 * GetClients()
 *
 * Gets actives client table from DB
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('getContacts'))
{

	function  getContacts()
	{
	
		//http://codeigniter.com/forums/viewthread/71493/
		//Loads model into helper
		$CI =& get_instance();
		$CI->load->model('trakcontacts', '', TRUE);
		$getentries = $CI->trakclients->GetEntry($options = array('Trash' => '0','sortBy'=> 'LastName','sortDirection'=> 'desc'));
		return $getentries;

	}
}


// ------------------------------------------------------------------------

/**
 * GetTrashedClients()
 *
 * Gets actives client table from DB
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('GetTrashedContacts'))
{

	function getTrashedContacts()
	{
	
		//http://codeigniter.com/forums/viewthread/71493/
		//Loads model into helper
		$CI =& get_instance();
		$CI->load->model('trakcontacts', '', TRUE);
		$getentries = $CI->trakclients->GetEntry($options = array('Trash' => '1','sortBy'=> 'LastName','sortDirection'=> 'desc'));
		return $getentries;

	}
}


// ------------------------------------------------------------------------

/**
 * GetCurrentClient()
 *
 * Gets the selected client's data from DB
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('getCurrentContact'))
{
	function  getCurrentContact()
	{
			$CI =& get_instance();
			$CI->load->model('trakcontacts', '', TRUE);
			$currentclient = $CI->trakclients->GetEntry($options = array('ID' => $CI->uri->segment(2)));
			if($currentclient)
				{
				return $currentclient;
				}
			else {
			 	echo 'There was a problem retrieving the current entry';
			}
	}
// ------------------------------------------------------------------------




// ------------------------------------------------------------------------

/**
 * getContactsForm()
 *
 * Gets client form populated with data
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('_getContactsForm'))
{


	// 
	function  _getContactsForm($ClientList,$CurrentContact="",$CurrentClient="")
	{
		$attributes = array( 'id' => 'contactForm');
		if (!isset($CurrentContact->ID)) {
			$contactForm = form_open(site_url().'contacts/addcontact',$attributes)."\n";
		}
		else{
			$contactForm =form_open(site_url().'contacts/updatecontact/'.$CurrentContact->ID, $attributes)."\n";
		}
		$contactForm .="<fieldset class=\"formbuttons\">";
		if (!isset($CurrentContact->ID)) {
			$ndata = array('name' => 'submit','value' => 'Add Contact','class' => 'submitButton');
		}
		else{
			$ndata = array('name' => 'submit','value' => 'Update Contact','class' => 'submitButton');
		}
		$contactForm .= form_submit($ndata)."\n";

		if (isset($CurrentContact->ID)) {
			$contactForm .= "<a href=\"".site_url()."contacts/trashcontact/".$CurrentContact->ID."\" class=\"cancelEdit\">Trash Contact</a>\n";
		}	



		$contactForm .="</fieldset>";
		
		$subsections = array( 'ContactInfo'=>'Contact Info','Active'=>'Active','FirstName'=>'First Name','LastName'=>'Last Name','CompanyName'=>'Company Name','Title'=>'Title','TimeZone'=>'Time Zone','Email1'=>'Email 1', 'Email2'=>'Email 2', 'Cellphone1'=>'Cell phone 1','Cellphone2'=>'Cell phone 2','Officephone1'=>'Office phone 1','Officephone2'=>'Office phone 2','Homephone1'=>'Home phone 1','Homephone2'=>'Home phone 2','CompanyInfo'=>'Company Info','OfficeAddress'=>'Office Address','OfficeZipcode'=>'Office Zipcode','OfficeCity'=>'Office City','OfficeCountry'=>'Office Country','OtherInfo'=>'Other Info','HomeAddress'=>'Home Address','HomeZipcode'=>'Home Zipcode','HomeCity'=>'Home City','HomeCountry'=>'Home Country','Notes'=>'Other Notes' 
		);

		foreach ($subsections as $key=>$value){
			//Headers
			$Headers = array("ContactInfo",'CompanyInfo','OtherInfo');
			if (in_array($key,$Headers)) {
				$contactForm .="<h4>".$value."</h4><BR/>\n";
			}else{
			
				$contactForm .="<fieldset>\n";
				$contactForm .= form_label($value.":",$key);
				//Textboxes
				$textboxes = array("Notes");
				if (in_array($key,$textboxes)) {

					$ndata = array('name' => $key, 'id' => $key, 'rows' => '10', 'cols' => '45');
					if (isset($CurrentContact->ID)) {
						$ndata['value']=$CurrentContact->$key;
					}
					$contactForm .= form_textarea($ndata)."\n";
					}
				
				else if ($key=='TimeZone') {

					
					if (isset($CurrentContact->ID)) {
						if ($CurrentContact->TimeZone!='') {
							$contactForm .= TimeZoneDropDown($CurrentContact->TimeZone);
						}
						else {
							$contactForm .= TimeZoneDropDown($CurrentClient->TimeZone);
						}
					}
					else {
						if (isset($CurrentClient->ID)) {
							$contactForm .= TimeZoneDropDown($CurrentClient->TimeZone);
						}
						else
						{
							$contactForm .= TimeZoneDropDown();
						}
					
					}
					}

				else if ($key=='Active') {
						
						if (isset($CurrentContact->ID)) {
							$selected=$CurrentContact->$key;
						}
						else {
							$selected=1;
						}
						$activeoptions=array("1"=>"1","0"=>"0");
						$ndata = array('name' => $key, 'id' => $key);
						$contactForm .= form_dropdown( $key, $activeoptions,$selected);
					}

				else {
					//Short Inputs
					$input3 = array('CompanyName',);
					if (in_array($key,$input3)) {						
						$options = array(''  => '');
						foreach($ClientList as $client)
						{
							$options[$client->CompanyName]=$client->CompanyName;
						}
						asort($options);		
						$more = 'id="CompanyName" class="EntryClient"';			
						if(isset($CurrentClient->CompanyName)){
							$contactForm  .=form_dropdown('CompanyName', $options, $CurrentClient->CompanyName,$more);
						}
						else if(isset($CurrentContact->CompanyName)){
							$contactForm  .=form_dropdown('CompanyName', $options, $CurrentClient->CompanyName,$more);
						}
						else{
							$contactForm  .=form_dropdown('CompanyName', $options, '',$more);
}
						}
					
					else {
						//Long Inputs
						$input55 = array('LastName','FirstName','Title','Email1', 'Email2', 'Cellphone1','Cellphone2','Officephone1','Officephone2','Homephone1','Homephone2','OfficeAddress','OfficeZipcode','OfficeCity','OfficeCountry','HomeAddress','HomeZipcode','HomeCity','HomeCountry');
						if (in_array($key,$input55)) {
								$size='45';
							}
							//Regular Size Inputs
							else {
								$size='25';
							}
					$ndata = array('name' => $key, 'id' => $key, 'size' => $size);
					if (isset($CurrentContact->ID)) {
						$ndata['value']=$CurrentContact->$key;
					}
					$contactForm .= "\n".form_input($ndata)."\n";
					}
				}
			$contactForm .="</fieldset>\n";
			}
		}
		$contactForm .= "<div class=\"clearfix\">&nbsp;</div>\n";
		$contactForm .= form_close()."\n";
		return $contactForm;



	}

}

}
// ------------------------------------------------------------------------

/**
 * _displayClientContactsList()
 *
 * Generates a clickable list of all existing clients 
 *
 * @access	public
 * @return	string
 */
if ( !function_exists('_displayClientContactsList'))
{
	function  _displayClientContactsList($ClientList,$Selected="" )
	{
		$ClientSelectorForm ="<h3>Contacts by client:<br/>Active Clients</h3>";
		foreach($ClientList as $client)
		{
			if ($client->CompanyName==$Selected){ $ClientSelectorForm .="<strong>";}
			$ClientSelectorForm .="<a href=\"".base_url()."contacts/viewclientcontacts/$client->ID\">".$client->CompanyName."</a>";
			if ($client->CompanyName==$Selected){ $ClientSelectorForm .="</strong>";}
		}
	$ClientSelectorForm .="<a href=\"".base_url()."contacts/viewclientcontacts/\">View All</a>";
		return $ClientSelectorForm;	


	}
}
// ------------------------------------------------------------------------

/**
 * _displayClientContactsList()
 *
 * Generates a clickable list of all existing clients 
 *
 * @access	public
 * @return	string
 */
if ( !function_exists('_displayClientContactsDropdown'))
{
	function  _displayClientContactsDropdown($ClientList,$Selected="" )
	{


		$attributes['id'] = 'clientcontrol';
			$ClientSelectorForm= form_open(site_url()."contacts/viewclientcontacts/\n",$attributes);

			
			//Clients
				$options=array();
				foreach($ClientList as $client)
				{
				$options[$client->ID]=$client->CompanyName;

				}
				asort($options);	
				$more = 'id="clientselector" class="clientselector"';			
				$selected=$Selected;
				$options=array(''=>"All")+$options;

				$ClientSelectorForm .=form_label('Client:','clientselector');
				$ClientSelectorForm .=form_dropdown('clientselector', $options,$selected ,$more);
				$more = 'id="clientsubmit" class="clientsubmit"';			
				$ClientSelectorForm .=form_submit('clientsubmit', 'Edit',$more);
				$ClientSelectorForm .= form_close()."\n";

		return $ClientSelectorForm;	

	}
}
// ------------------------------------------------------------------------

/**
 * _getSearchContactForm()
 *
 * Generates a clickable list of all existing clients 
 *
 * @access	public
 * @return	string
 */
if ( !function_exists('_getSearchContactForm'))
{
	// ################## Search contact form ##################	
	function  _getSearchContactForm()
	{
	$attributes = array( 'id' => 'searchcontact');
		$SearchContactForm = form_open(site_url().'contacts/searchcontacts',$attributes)."\n";
		$SearchContactForm .="<fieldset>";
		$SearchContactForm .= form_label("Name:",'searchedname');
		$ndata = array('name' => 'searchedname', 'id' => 'searchedname', 'size' =>25);
		$SearchContactForm .= "\n".form_input($ndata)."\n";
		$SearchContactForm .="</fieldset>";
		$SearchContactForm .="<fieldset>";
		$ndata = array('name' => 'submit','value' => 'Search Contacts','class' => 'contacts');
		$SearchContactForm .= form_submit($ndata)."\n";
		$SearchContactForm .="</fieldset>";
		$SearchContactForm .= form_close()."\n";
		return $SearchContactForm;
	}
}

// ------------------------------------------------------------------------

/**
 * _getSearchContactForm()
 *
 * Generates a clickable list of all existing clients 
 *
 * @access	public
 * @return	string
 */

if ( !function_exists('_displayClientContactsTable'))
{
	function  _displayClientContactsTable($CurrentClient)
	{
		$ClientContactsTable ="<h3>Contacts for ";
			if ($CurrentClient=="All"){
			$ClientContactsTable .="All";
			}
			else{
			$ClientContactsTable .=$CurrentClient->CompanyName;
		}
		$ClientContactsTable .="</h3>";
		$CI =& get_instance();
		$CI->load->model('trakcontacts', '', TRUE);
		if ($CurrentClient=="All"){
			$ClientContacts = $CI->trakcontacts->GetEntry($options = array('Trash' => '0','sortBy' => 'FirstName','sortDirection'=>'ASC'));
		}
		else{
		 $ClientContacts = $CI->trakcontacts->GetEntry($options = array('CompanyName' => $CurrentClient->CompanyName,'Trash' => '0','sortBy' => 'FirstName','sortDirection'=>'ASC'));
		}
		//echo  $CI->db->last_query();
		if ($ClientContacts ) {
			$ClientContactsTable.= "<table id=\"currententries\">\n";
			$ClientContactsTable .= "<thead>\n";
			$ClientContactsTable .= "<tr><th class=\"header company\">Name</th><th class=\"header code\">Cell Phone</th><th class=\"header contact\">Email</th></tr>\n";
			$ClientContactsTable .= "</thead>\n";
			
			$ClientContactsTable .= "<tbody>\n";
			
			foreach($ClientContacts as $Contact)
			{
				$ClientContactsTable.="<tr><td><a href=\"".base_url()."contacts/editcontact/$Contact->ID\">".$Contact->FirstName." ".$Contact->LastName."</a></td>";
				$ClientContactsTable.="<td><a href=\"".base_url()."contacts/editcontact/$Contact->ID\">".$Contact->Cellphone1."</a></td><td><a href=\"".base_url()."contacts/editcontact/$Contact->ID\">".$Contact->Email1."</a></td></tr>";
			}
			$ClientContactsTable.="</tbody></table>";
		}
		else{
			$ClientContactsTable .="No contacts.";
			}

		
		return $ClientContactsTable;
	}
}
/* End of file client_helper.php */
/* Location: ./system/application/helpers/client_helper.php */