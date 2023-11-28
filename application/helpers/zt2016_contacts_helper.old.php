<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ZOWTRAK
 *
 * @package		ZOWTRAK
 * @author		Zebra On WHeels
 * @copyright	Copyright (c) 2009 - 2017, Zebra On WHeels
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Contact Helper
 *
 * @package		ZOWTRAK
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Zebra On WHeels

 */


	// ------------------------------------------------------------------------
	/**
	* zt2016_create_contacts_selector 
	*
	*/

	if ( ! function_exists(' _display_contacts_control'))
	{

		// ################## contacts control ##################	
		function   _display_contacts_control($ContactsTable,$ContactInfo,$FormURL)
		{

			#top contacts dropdown
			$FormInfo['FormURL']=$FormURL;
			$FormInfo['labeltext']= 'Contact';
			$FormInfo['id'] = 'contact_dropdown_form';
			$FormInfo['class'] = 'form-inline';


			$clients_top_dropdown=zt2016_create_contacts_selector($ContactsTable,$ContactInfo,$FormInfo)."\n";


			return $clients_top_dropdown;

		}
	}


	// ------------------------------------------------------------------------
	/**
	* zt2016_create_contacts_selector 
	*
	*/
	if ( ! function_exists('zt2016_create_contacts_selector '))
	{
	// ################## Generate client selector ##################	
		function zt2016_create_contacts_selector ($ContactsTable,$ContactInfo,$FormInfo){

			$FormURL =$FormInfo['FormURL'];
			unset($FormInfo['FormURL']); 
			$Labeltext =$FormInfo['labeltext'];
			unset($FormInfo['labeltext']); 
			
			$contact_selector=form_open(site_url().$FormURL,$FormInfo)."\n";
			
			if(isset($ContactInfo->ActiveEntryClient)){
				$contact_selector.="<input type='hidden' name='Current_Client' value='".$ContactInfo->ActiveEntryClient."'/>\n";
			}
		 	$contact_selector.='				<div class="form-group">'."\n";
	      	$contact_selector.='					<div class="input-group ">'."\n";
	      	$contact_selector.='						<span class="input-group-addon" id="contacts-addon1">'.$Labeltext.'</span>'."\n";
			$contact_selector.= zt2016_contacts_dropdown_control($ContactsTable,$ContactInfo);
	 		$contact_selector.='					</div>'."\n";
	 		$contact_selector.='				</div>'."\n";
		 	$contact_selector.='				<div class="form-group">'."\n";
	     	$contact_selector.='					<div class="input-group">'."\n";
			$more = 'id="contact_dropdown_selector_submit" class="contactcontrolsubmit form-control"';
			$contact_selector.=form_submit('contact_dropdown_selector_submit', 'Go',$more)."\n";
	 		$contact_selector.='					</div>'."\n";
	 		$contact_selector.='				</div>'."\n";
			$contact_selector.= form_close()."\n";
			
 			return 	$contact_selector;
		}
	
	}


	// ------------------------------------------------------------------------
	/**
	* zt2016_contacts_dropdown_control 
	*
	*/

	if ( ! function_exists('zt2016_contacts_dropdown_control'))
	{
	
		// ################## clients control ##################	
		function   zt2016_contacts_dropdown_control($ContactsTable,$ContactInfo)
		{
	
			// add blank if no contact is selected
			$options=array();
			
			//Build contact list
			if (empty($ContactInfo) || (isset($ContactInfo->ActiveEntryClient) && !isset ($ContactInfo->ID)))  {$options[""]="Name";}
			//if (empty($ContactInfo) ) {$options[""]="Name";}
		
			foreach($ContactsTable as $Contact)
			{
				if ($Contact->Active==1){
					$options[$Contact->ID]=$Contact->FirstName.' '. $Contact->LastName;
				} else {
					$options[$Contact->ID]=$Contact->FirstName.' '. $Contact->LastName." (NA)";
				}
			}
			asort($options);
			
			//$options=array('all'=>"All")+$options;		
			//$more = 'id="contact_dropdown_selector" class="selector form-control input" required';
			$more = 'id="contact_dropdown_selector" class="selector form-control input"';
			
			if (isset($ContactInfo->BilledPaid) && $ContactInfo->BilledPaid==1){
				$more .= " disabled";
			}
			
			if (empty($ContactInfo) || (isset($ContactInfo->ActiveEntryClient) && !isset ($ContactInfo->ID))) {
				$selected="";	
			} else{
				$selected=$ContactInfo->ID;
			}
			
			
			//$clientscontrol .=form_label('Manage client materials:','client');
			//$more = 'id="client_dropdown_submit" class="clientcontrolsubmit form-control"';			
			$contactscontrol =form_dropdown('Current_Contact', $options,$selected ,$more);
	
			return $contactscontrol;
		
		}
	}




 
		// ------------------------------------------------------------------------
	
	/**
	 * zt2016_get_Contact_Form
	 *
	 * Gets client form populated with data

	 */
	if ( ! function_exists('zt2016_get_Contact_Form'))
	{
	function  zt2016_get_Contact_Form( $TimezonesList, $CountriesList,$ClientsTable , $CurrentContact='')
	{

		$editContactForm ="\n";

		$subsections = array('Basic'=>'Basic Info','Active'=>'Active','FirstName'=>'First Name','LastName'=>'Last name','Email1'=>'Email','CompanyName'=>'Company Name','TimeZone'=>'Time Zone','Title'=>'Title','Gender'=>'Gender','SocialUrl'=>'SocialUrl', 'Email2'=>'Email 2', 'Phones'=>'Phone Numbers','Cellphone1'=>'Cellphone 1','Cellphone2'=>'Cellphone 2','Officephone1'=>'Office Phone 1','Officephone2'=>'Office Phone 2','Homephone1'=>'Home Phone 1','Homephone2'=>'Home Phone 2','Office Details'=>'Office Details','OfficeAddress'=>'Office Address','OfficeZipcode'=>'Office Zipcode','OfficeCity'=>'Office City','OfficeCountry'=>'Office Country','Home Details'=>'Home Details','HomeAddress'=>'Home Address','HomeZipcode'=>'Home Zipcode','HomeCity'=>'Home City','HomeCountry'=>'Home Country', 'Guidelines'=>'Guidelines and Notes','ContactProductionGuidelines'=>'Contact Production Guidelines','ContactBillingGuidelines'=>'Contact Billing Guidelines','Notes'=>'Notes');
							 

		foreach ($subsections as $key=>$value){
			//Headers
			$Headers = array('Basic', "Phones", 'Office Details','Home Details','Guidelines');
			$ColHeaders = array('Basic', 'Office Details','Guidelines');
			if (in_array($key,$Headers)) {
					#### close inner item group
					if ($key!='Basic')	{
						$editContactForm .="	</div>\n";
					}
				$currentheader=strtolower(str_replace(" ","",$key));
				if (in_array($key,$ColHeaders)) {
					#### close column
					if ($key!='Basic')	{
						$editContactForm .="</div>\n";
					}
					$editContactForm .="<div class=\"col-sm-4\">\n";
				}

				$editContactForm .="	<div class=\"item-group\">\n";
				
				if ($key=='Basic' || $key=='Pricing' ) {
					$editContactForm .="		<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary ".$currentheader."\">".$key."</h5></div>\n";
				}else{
					$editContactForm .="		<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-info ".$currentheader."\">".$key."</h5></div>\n";
				}
			} else{ 
				### Textareas
				$textboxes = array("Notes",'ContactBillingGuidelines','ContactProductionGuidelines');
				if (in_array($key,$textboxes)) {
					$largeboxes = array("Notes",'ContactBillingGuidelines','ContactProductionGuidelines');
					//$largeboxes = array();
					if (in_array($key,$largeboxes)) { $rows=10; $cols=45;}
					else { $rows=4; $cols=35;}
					
					$ndata = array('name' => $key, 'id' => $key, 'rows' => $rows, 'cols' => $cols, 'class'=>'form-control');
					if (isset($CurrentContact->ID) && isset($CurrentContact->$key)) {
						$ndata['value']=$CurrentContact->$key;
					}
					$editContactForm .="		<div class=\"col-sm-12\">\n";
				 	$editContactForm .= "			".form_label($value.":",$key)."\n";
					$editContactForm .= "			".form_textarea($ndata,'','style="min-width: 100%"')."\n";

					}
				### dropdowns
				### Active
				else if ($key=='Active') {
					$options = array(''=>'','1' => '1', '0'=>'0');
					$more = 'id="Active" class=" form-control"';	
					
					$editContactForm .="	<div class=\"col-sm-3\">\n";
				 	$editContactForm .= "			".form_label($value.":",$key);
					$editContactForm .="			";
					if (isset($CurrentContact->ID)) {
						
						$editContactForm .= form_dropdown('Active', $options,$CurrentContact->$key,$more)."\n";
					}
					else {
						$editContactForm .= form_dropdown('Active', $options,'1',$more)."\n";
					}

				}
				### Gender
				else if ($key=='Gender') {
					$options = array(''=>'', 'F'=>'Female', 'M'=>'Male');
					$more = 'id="Gender" class=" form-control"';	
					
					$editContactForm .="	<div class=\"col-sm-4\">\n";
				 	$editContactForm .= "			".form_label($value.":",$key);
					$editContactForm .="			";
					if (isset($CurrentContact->ID) && isset($CurrentContact->$key)) {
						
						$editContactForm .= form_dropdown('Gender', $options,$CurrentContact->$key,$more)."\n";
					}
					else {
						$editContactForm .= form_dropdown('Gender', $options,'1',$more)."\n";
					}

				}			
				#### country
				else if ($key=="OfficeCountry" ||$key=="HomeCountry") {
					$options=$CountriesList;
					$more = 'class="form-control" id="'.$key."\"";
					$editContactForm .="		<div class=\"col-sm-12\">\n";
					$editContactForm .= "			".form_label($value.":",$key);				
					$editContactForm .="				";
					
					if (isset($CurrentContact->ID) && isset($CurrentContact->$key)) {
						
						$editContactForm .= form_dropdown($key, $options,$CurrentContact->$key,$more)."\n";
					}
					else {
						$editContactForm .= form_dropdown($key, $options,'',$more)."\n";
					}					
				}
	

				#### client
				else if ($key=="CompanyName") {
					
					$editContactForm .="		<div class=\"col-sm-12\">\n";
			 		$editContactForm .= "			".form_label($value.":",$key);

					$options=array();
					foreach($ClientsTable  as $client)
					{
						$options[$client->CompanyName]=$client->CompanyName;
					}
					asort($options);
					$options=array(''=>"")+$options;		
	
					
					$more = 'id="CompanyName" class="form-control" required="true"';
					$editContactForm .="				";

					if (isset($CurrentContact->ID) && isset($CurrentContact->$key)) {
						$editContactForm .=form_dropdown('CompanyName', $options,$CurrentContact->$key ,$more )."\n";
					}
					else {
						$editContactForm .=form_dropdown('CompanyName', $options,'',$more )."\n";
					}
				
				}	
			
				#### timezone
				else if ($key=='TimeZone') {

					$editContactForm .="		<div class=\"col-sm-12\">\n";
			 		$editContactForm .= "			".form_label($value.":",$key);

					$more = 'id="Timezone" class="form-control" ';
					$editContactForm .="				";

					if (isset($CurrentContact->ID) && isset($CurrentContact->$key)) {
						$editContactForm .=form_dropdown('TimeZone', $TimezonesList,$CurrentContact->$key ,$more )."\n";
					}
					else {
						$editContactForm .=form_dropdown('TimeZone', $TimezonesList,'',$more )."\n";
					}
					
				}

				#### email
				else if ($key=='Email1' || $key=='Email2') {

				$editContactForm .="<div class=\"col col-sm-12\">\n";
				$editContactForm .= "				".form_label($value.":",$key);
					$ndata = array('name' => $key, 'id' => $key, 'size' => 25,'type'=>'email', 'class'=>'form-control');	
					
					if (isset($CurrentContact->ID) && isset($CurrentContact->$key)) {

						$ndata['value']=$CurrentContact->$key;
					}
				
					if ($key=='Email1') {
						$editContactForm .= "\n				".form_input($ndata,'','required="true"')."\n";
					} else {
						$editContactForm .= "\n				".form_input($ndata,'')."\n";
					}
					

				}			
				
				
				### regular inputs
				else {
					//Short Inputs
					$input3 = array("FirstName");
					$input2 = array( "SocialUrl");
					if (in_array($key,$input3)) {
						$size='3';
					}
					elseif (in_array($key,$input2)) {
						$size='2';
					}					
					/*else {
						//Long Inputs
						$input55 = array();
						if (in_array($key,$input55)) {
								$size='45';
							}
							//Regular Size Inputs
					*/		
					else {
						$size='25';
					}
					//}
				$editContactForm .="		";	
				if ($size=='3') { 
					$editContactForm .="<div class=\"col col-sm-9\">\n";
				} 
				elseif ($size=='2') {
					$editContactForm .="<div class=\"col col-sm-8\">\n";
				}
				else {
					$editContactForm .="<div class=\"col col-sm-12\">\n";
				}
				$editContactForm .= "				".form_label($value.":",$key);
				
				$ndata = array('name' => $key, 'id' => $key, 'size' => $size, 'class'=>'form-control');
				
				if (isset($CurrentContact->ID) && isset($CurrentContact->$key)) {
					$ndata['value']=$CurrentContact->$key;
					
				} 
				#### if required
				$requiredFields = array("Active","FirstName","LastName");
				
				if (in_array($key,$requiredFields)) {
						$editContactForm .= "\n				".form_input($ndata,'','required="true"')."\n";
				} 
				else{
						$editContactForm .= "\n				".form_input($ndata)."\n";
				}

			}

			$editContactForm .="		</div>\n";
			}
		}
		$editContactForm .="	</div>\n";
		
		$editContactForm .= form_close()."\n";
		$editContactForm .="<div class=\"clearfix\"></div>";
		return $editContactForm;/**/
	}

}



/* End of file zt2016_clients_helper.php */
/* Location: ./system/application/helpers/zt2016_clients_helper */