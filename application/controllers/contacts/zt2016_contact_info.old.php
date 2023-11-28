zt2016_contact_info<?php

class Zt2016_contact_info extends MY_Controller {
	
	function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); 
		$this->load->helper(array('form', 'url', 'general', 'userpermissions', 'zt2016_clients', 'zt2016_contacts'));
		
		$this->load->model('zt2016_contacts_model', '', TRUE);
		
		$templateData['ZOWuser']= _getCurrentUser();

		//Identify contact to display 
		//Via URL
		$Contact_ID=$this->uri->segment(3);
		$Current_Client=$this->uri->segment(4);
		
		//if not via URL, via POST
		 if (empty ($Contact_ID)) {
		 	if ($this->input->post('Current_Contact')){
		 		$Contact_ID=$this->input->post('Current_Contact');
		 	} else {
				//If no contact ID, then check if client info was posted
				if ($this->input->post('Current_Client')){
					//if client info was posted, display 
					//the first (ordered alphabetically) contact name for the client
		 			$Current_Client=$this->input->post('Current_Client');
					
					if ($Current_Client=="all"){
						if ($templateData['ZOWuser']=="miguel" || $templateData['ZOWuser']=="sunil.singal" || $templateData['ZOWuser']=="jirka.blom" ) {
							redirect('contacts/zt2016_contacts', 'refresh');
						} else{
							redirect('contacts/zt2016_contacts_search', 'refresh');
						}
					}
					
					$Current_Client=str_replace( "~","&", $Current_Client);
					$Current_Client=str_replace( "_"," ", $Current_Client);
					
					//$this->load->model('zt2016_contacts_model', '', TRUE);
					$First_Client_Contact= $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $Current_Client,"limit"=>1, 'sortBy'=>'FirstName', 'sortDirection'=>'Asc'));
					$Contact_ID=$First_Client_Contact->ID;
					
					//var_dump ($First_Client_Contact);

				} else{
					//No contact ID
					if ($templateData['ZOWuser']=="miguel" ||$templateData['ZOWuser']=="sunil.singal" || $templateData['ZOWuser']=="jirka.blom" ) {
						redirect('contacts/zt2016_contacts', 'refresh');
					} else{
						redirect('contacts/zt2016_contacts_search', 'refresh');
					}
				}
		 	}
		 }
		
		if (empty ($Contact_ID) ) {
			//No contact ID
			if ($templateData['ZOWuser']=="miguel" ||$templateData['ZOWuser']=="sunil.singal" || $templateData['ZOWuser']=="jirka.blom" ) {
				redirect('contacts/zt2016_contacts', 'refresh');
			} else{
				redirect('contacts/zt2016_contacts_search', 'refresh');
			}
		}
		
		if (!is_numeric ($Contact_ID)) {
			// Non-numeric (name) contact ID
			
			$safeContactName=str_replace("~","&",$Contact_ID);
		    $safeContactName=str_replace("_"," ",$safeContactName);
					
			$NameFlag=0;
			
			 if (empty ($Current_Client)) {
			
				$ContactsTable = $this->zt2016_contacts_model->GetContact($options = array('sortBy'=>'FirstName', 'sortDirection'=>'Asc'));
			 }
			else{
				$Current_Client=str_replace( "~","&", $Current_Client);
				$Current_Client=str_replace( "_"," ", $Current_Client);
				$ContactsTable = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $Current_Client,'sortBy'=>'FirstName','sortDirection'=>'Asc'));
			}
						
			foreach ($ContactsTable as $ContactInfo){
				$checkname=$ContactInfo->FirstName." ".$ContactInfo->LastName;	
				if ($safeContactName == $checkname){
					$NameFlag=1;
					break;
				}

			}
			if ($NameFlag==0){
				$ContactInfo='';
			}
		} else{ 
			// Numeric (name) contact ID
			$ContactInfo = $this->zt2016_contacts_model->GetContact($options = array('ID' => $Contact_ID));
		}
		
		 if (empty ($ContactInfo) ) {
			 
			if ($templateData['ZOWuser']=="miguel" || $templateData['ZOWuser']=="sunil.singal"  || $templateData['ZOWuser']=="jirka.blom" ) {
				redirect('contacts/zt2016_contacts', 'refresh');
			} else{
				redirect('contacts/zt2016_contacts_search', 'refresh');
			}
		 }
		
		$ContactInfo->FullName = $ContactInfo->FirstName.' '. $ContactInfo->LastName;
		//echo $ContactInfo->FirstName.' '. $ContactInfo->LastName;
		//die();
		
		$templateData['title'] = 'Contact Information for '.$ContactInfo->FullName;

		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _create_contact_page($ContactInfo,$templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}

	// ################## display clients info ##################	
	function _create_contact_page($ContactInfo,$ZOWuser)
	{
					
		# retrieve all clients from db		
		$this->load->model('zt2016_clients_model', '', TRUE);
		$ClientsTable = $this->zt2016_clients_model->GetClient();

		# retrieve all current contact's company contacts from db		
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$ContactsTable = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $ContactInfo->CompanyName));

		if (empty($ContactsTable)){
			redirect('contacts/zt2016_contacts_search', 'refresh');
		}		
		
		# retrieve current contact's company info from db		
		$ClientInfo = $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $ContactInfo->CompanyName));

			if (empty($ClientInfo)){
				redirect('contacts/zt2016_contacts_search', 'refresh');
			}
			
		
		#Create page.

		$page_content=$this->_display_page($ContactInfo, $ContactsTable, $ClientsTable, $ClientInfo, $ZOWuser);

		return $page_content;
	
	}	


// ################## create page ##################	
	function  _display_page($ContactInfo,$ContactsTable,$ClientsTable,$ClientInfo,$ZOWuser)
	{

		$page_content ='<div class="page_content">';
		
		######### top dropdowns
		$FormURL="contacts/zt2016_contact_info";
		## clients dropdown
		$page_content.= _display_clients_control($ClientsTable,$ClientInfo,$FormURL);
		## contacts dropdown
		$page_content.= _display_contacts_control($ContactsTable,$ContactInfo,$FormURL);

		######### Display success message
		
		if($this->session->flashdata('SuccessMessage')){		
			
			$page_content.='<div class="alert alert-success" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}
	
	    ######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}

		########## panel head
		$page_content.='<div id="client_info_panel" class="panel panel-default"  style="margin-top:2em;">'."\n";
		$page_content.='<div class="panel-heading">'."\n";
		
			########## client name, client code
		
			$SafeclientName=str_replace( "&","~", $ClientInfo->CompanyName);
			$SafeclientName=str_replace( " ","_", $SafeclientName);
		
			$page_content.=' <h3>';
		
			if ($ContactInfo->Active==0){
				$page_content.= '<del>';	
			}
			$page_content.= $ContactInfo->FullName;
		
			if ($ContactInfo->Gender=='F'){ 
				$page_content.=' <span class="text-muted fas fa-female"></span>';
			} elseif ($ContactInfo->Gender=='M'){ 
				$page_content.=' <span class="text-muted fas fa-male"></span>';				
			}
		
			$page_content.= ' <small> ';
			$page_content.="[".$ContactInfo->ID."] ";
			$page_content.= ' <a href="'.site_url().'clients/zt2016_client_info/'.$SafeclientName.'" ">'.$ClientInfo->CompanyName.'</a>';
			if ($ContactInfo->Active==0){
				$page_content.= ' (Inactive)</del>';	
			}
			$page_content.= '</small>';
			$page_content.= '</h3>'."\n";		
			$page_content.= '<p>'."\n";

			########## new job button
				
			$attributes='class="form-inline" id="newjobform" name="newjobform"';
			$formurl=site_url().'zt2016_new_job';
		
			$page_content.= '<form action="'.$formurl.'" '.$attributes.' method="post" accept-charset="utf-8" style="display:inline;">';
			$page_content.='	<div class="form-group">'."\n";
			$page_content.='		<input type="hidden" id="Current_Client" name="Current_Client" value="'.$SafeclientName.'">'."\n";
			$page_content.='		<input type="hidden" id="PastJobsOriginator" name="Current_Contact" value="'. $ContactInfo->ID.'">'."\n";
			$page_content.='		<input type="submit" id="newjobsubmit" name="newjobsubmit" value="New Job" class="btn btn-sm btn-info" >'."\n";
			$page_content.='	</div>'."\n";
			$page_content.="</form>"."\n";		
		
			########## latest jobs button
				
			$attributes='class="form-inline" id="pastjobsform" name="pastjobsform"';
			$formurl=site_url().'tracking/zt2016_past_jobs';
		
			$page_content.= '<form action="'.$formurl.'" '.$attributes.' method="post" accept-charset="utf-8" style="display:inline;">';
			$page_content.='	<div class="form-group">'."\n";
			$page_content.='		<input type="hidden" id="PastJobsClient" name="PastJobsClient" value="'.$ClientInfo->CompanyName.'">'."\n";
			$page_content.='		<input type="hidden" id="PastJobsOriginator" name="PastJobsOriginator" value="'. $ContactInfo->FullName.'">'."\n";
			$page_content.='		<input type="submit" name="molliesubmit" value="Past Jobs" class="btn btn-sm btn-default" >'."\n";
			$page_content.='	</div>'."\n";
			$page_content.="</form>"."\n";

			########## Report button
			
			$page_content.= '<a href="'.site_url().'reports/zt2016_annual_originator_figures/'.$ContactInfo->ID.'" class="btn btn-default btn-sm">Report</a>';		
		
			########## Linkedin button
			if($ContactInfo->SocialUrl!=""){
				$page_content.= '<a href="'.prep_url($ContactInfo->SocialUrl).'" target="_new" class="btn btn-primary btn-sm"><span class="fab fa-linkedin"></span> LinkedIn</a>';
			}
	
		
			########## edit button
			if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal"  || $ZOWuser=="jirka.blom" ) {
	 			$page_content.='<a href="'.site_url().'contacts/zt2016_contact_edit/'.$ContactInfo->ID.'" class="btn btn-success btn-xs  pull-right">Edit</a>';
	  		}
		
			$page_content.= '</p>'."\n";
			$page_content.= '</div>'."\n";
		
		########## panel body
		$page_content.='<div class="panel-body">'."\n";
		
			$page_content.='	<div class="row">'."\n";
			$page_content.='		<div class="col-sm-12" style="margin-bottom:2em;">'."\n";
			
			########## col 1	
	
				########## basic info
				$page_content.='				<div class="col-sm-4">'."\n";
					$page_content.=$this->_display_basic_information($ContactInfo,$ClientInfo);
		
					//$page_content.=$this->_display_location_information($ContactInfo,$ClientInfo);

					########## contact info	
					//if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {
					$page_content.=$this->_display_contact_information($ContactInfo);
					//}
			
				$page_content.='				</div><!--col 4-->'."\n";	
			
			########## col 2	
				$page_content.='				<div class="col-sm-4">'."\n";		
					########## contact notes
					$page_content.=$this->_display_contact_notes($ContactInfo,$ClientInfo);
	 		
				$page_content.='				</div><!--col 4-->'."\n";	
		
			########## col 3	
				$page_content.='				<div class="col-sm-4">'."\n";

				########## billing guidelines
		 		if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal"  || $ZOWuser=="jirka.blom" ) {		
					$page_content.=$this->_display_billing_guidelines($ContactInfo,$ClientInfo);
				}
					########## other comments
					$page_content.=$this->_display_other_comments($ContactInfo);

				$page_content.='				</div><!--col 4-->'."\n";	

	 		

			$page_content.='			</div><!--col 12-->'."\n";			

	 		$page_content.='</div><!--row--> '."\n";	
			
		$page_content.='</div>'.'<!-- // class="panel-body" -->'."\n";
		
		$page_content .='</div><!-- // class="page_content" -->'."\n";
		
		return $page_content;
	}

// ################## display basic information ##################	
	function   _display_basic_information ($ContactInfo,$ClientInfo)
	{
		
		########## headers
		$page_content='<h5  class="text-uppercase text-primary"">Basic information</h5>'."\n";
		$page_content.='<table class="table">'."\n";

		########## number of jobs
		$page_content.='	<tr>'."\n";
		$page_content.='	<td>Number of jobs</td>'."\n";
		
		$page_content.='	<td><strong>';
		
		#Number of jobs		
		$this->db->where('Originator',$ContactInfo->FullName);
		$this->db->where('Client',$ContactInfo->CompanyName);
		$NumberOfJobs = $this->db->get('zowtrakentries');
		
		if ($NumberOfJobs->num_rows>0)
		{
			$page_content.=$NumberOfJobs->num_rows; 
		}
		else{
			$page_content.="NONE";
		}
		$page_content.='</strong>';

		$page_content.='</td>' ."\n";
		$page_content.='	</tr>'."\n";		
		
		########## last date
		$page_content.='	<tr>'."\n";
		$page_content.='	<td>Last Job</td>'."\n";

		$page_content.='	<td><strong>';
		
			#last client date		
			$this->db->select_max('DateOut');
			$this->db->where('Originator',$ContactInfo->FullName);
		    $this->db->where('Client',$ContactInfo->CompanyName);
			$LastClientDateQuery = $this->db->get('zowtrakentries');
			
			$row = $LastClientDateQuery->row(); 
			if (!empty($row->DateOut)) 
			{
			  //die ($row->DateOut);
			  $LastClientDate= date("d - M - Y",strtotime($row->DateOut));
			  $difference =$this->_datedifference($LastClientDate);
			  $page_content.=$LastClientDate.'</strong><br/><small>'.$difference.'</small>';
			  
			 // $LastClientDate.= $difference;
			} else{
				$page_content.="NONE</strong>";
			}

			########## first date
		$page_content.='	<tr>'."\n";
		$page_content.='	<td>First Job</td>'."\n";
		
		$page_content.='	<td><strong>';
		
		if ($ContactInfo->FirstContactIteration!="0000-00-00")
		{
			$page_content.=date("d - M - Y",strtotime($ContactInfo->FirstContactIteration));
		}
		else{
			$page_content.="NONE";
		}
		$page_content.='</strong>';

		if ($ContactInfo->FirstContactIteration!="0000-00-00")
			{
			$page_content.='<br/><small>'.$this->_datedifference($ContactInfo->FirstContactIteration).'</small>';
		}
		$page_content.='</td>' ."\n";
		$page_content.='	</tr>'."\n";
		
		########## Prevent missing  timezone-related errors	
		if (empty($ContactInfo->TimeZone)){
			$contact_timezone =$ClientInfo->TimeZone;
		} else{
			$contact_timezone =$ContactInfo->TimeZone;
		}
		
		########## Location
		$page_content.='	<tr>'."\n";
		$page_content.='	<td>Location</td>'."\n";
		
		$contact_location = $ContactInfo->OfficeCity;
		
		if (empty($contact_location)){
			$contact_location_array = explode('/',$contact_timezone );

			$contact_location = $contact_location_array[count($contact_location_array)-1];

			if ($contact_location_array[count($contact_location_array)-2]=="America"){
				$contact_location_array[count($contact_location_array)-2]="USA";
			}
			$contact_location .= ", ".$contact_location_array[count($contact_location_array)-2];
			$contact_location = str_replace("_"," ",$contact_location);			
		}
		

		
		$page_content.='	<td>'.$contact_location.'</td>' ."\n";
		$page_content.='	</tr>'."\n";
		
		########## time zone
		$page_content.='	<tr>'."\n";
		$page_content.='	<td>Date and Time Now</td>'."\n";
		
		$date = new DateTime("now", new DateTimeZone($contact_timezone) );
		
		$page_content.='	<td><strong>'.$date->format('d - M - Y |  H:i').'</strong><br/><small>('.$contact_timezone.')</small></td>' ."\n";
		$page_content.='	</tr>'."\n";		
		
		//$page_content.='	<td><strong>'.$LastClientDate.'</strong><br/><small>'.$difference.'</small></td>' ."\n";
		$page_content.='</td>' ."\n";
		$page_content.='	</tr>'."\n";

		$page_content.='</table>'."\n";
	
		return $page_content;
	}

// ################## display contact info  ##################	
	function _display_contact_information($ContactInfo) 
	{
		
		$page_content='<h5  class="text-uppercase text-primary"">Contact Information</h5>'."\n";
		$page_content.='<table class="table">'."\n";
	
		########## email1
		$page_content.='	<tr>'."\n";
		$page_content.='	<td>Email</td>'."\n";
		$page_content.='	<td>'.$ContactInfo->Email1.'</td>' ."\n";
		$page_content.='	</tr>'."\n";

		########## additional info		
		$additional_fields = array('Email2', 'Cellphone1', 'Cellphone2', 'Officephone1', 'Officephone2', 'Homephone1', 'Homephone2');
		
		foreach($ContactInfo as $key => $value){
		   // do things with current name, which is in variable $name
		   // each itteration it will be next name from your array: bob, bob2
		  	if (in_array($key, $additional_fields) && !empty($value)) {
				
				if ($key=="Email2"){
					$key="Email 2";
				} else{
					$key = str_replace("phone"," phone",$key);
					$key = str_replace("1","",$key);
					$key = str_replace("2"," 2",$key);
				}
				
				$page_content.='	<tr>'."\n";
				$page_content.='	<td>'.$key.'</td>'."\n";
				$page_content.='	<td>'.$value.'</td>' ."\n";
				$page_content.='	</tr>'."\n";			
			}
			
		}
		
		########## office info		
		
		if (!empty($ContactInfo->OfficeAddress) || !empty($ContactInfo->OfficeZipcode) || !empty($ContactInfo->OfficeCity) || !empty($ContactInfo->OfficeCountry)){

			$page_content.='	<tr>'."\n";
			$page_content.='	<td>Office Address</td>'."\n";
			$page_content.='	<td>';
			if (!empty($ContactInfo->OfficeAddress)) {$page_content.=$ContactInfo->OfficeAddress.'<br/>';}
			if (!empty($ContactInfo->OfficeZipcode)) {$page_content.=$ContactInfo->OfficeZipcode.'<br/>';}
			if (!empty($ContactInfo->OfficeCity)) {$page_content.=$ContactInfo->OfficeCity.'<br/>';}
			if (!empty($ContactInfo->OfficeCountry)) {$page_content.=$ContactInfo->OfficeCountry.'<br/>';}
			$page_content.='	</td>' ."\n";
			$page_content.='	</tr>'."\n";
			
		}

		########## home info		
		
		if (!empty($ContactInfo->HomeAddress) || !empty($ContactInfo->HomeZipcode) || !empty($ContactInfo->HomeCity) || !empty($ContactInfo->HomeCountry)){

			$page_content.='	<tr>'."\n";
			$page_content.='	<td>Home Address</td>'."\n";
			$page_content.='	<td>';
			if (!empty($ContactInfo->HomeAddress)) {$page_content.=$ContactInfo->HomeAddress.'<br/>';}
			if (!empty($ContactInfo->HomeZipcode)) {$page_content.=$ContactInfo->HomeZipcode.'<br/>';}
			if (!empty($ContactInfo->HomeCity)) {$page_content.=$ContactInfo->HomeCity.'<br/>';}
			if (!empty($ContactInfo->HomeCountry)) {$page_content.=$ContactInfo->HomeCountry.'<br/>';}
			$page_content.='	</td>' ."\n";
			$page_content.='	</tr>'."\n";
			
		}
				
		$page_content.='</table>'."\n";
	
		return $page_content;
	}	
	
// ################## display contact and client guidelines ##################	
	function _display_contact_notes($ContactInfo,$ClientInfo)
	{

		$page_content='<h5  class="text-uppercase text-primary">Contact Production Guidelines</h5>'."\n";
		$page_content.='<div class="well well-sm guidelines">'."\n";
		$page_content.=$ContactInfo->ContactProductionGuidelines;
		$page_content.='</div><!-- guidelines -->'."\n";

		return $page_content;

	}
	
// ################## display contact and client guidelines ##################	
	function _display_billing_guidelines($ContactInfo,$ClientInfo)
	{

		$page_content='<h5  class="text-uppercase text-primary">Contact Billing Guidelines</h5>'."\n";
		$page_content.='<div class="well well-sm guidelines">'."\n";
		$page_content.=$ContactInfo->ContactBillingGuidelines;
		$page_content.='</div><!-- guidelines -->'."\n";		
		return $page_content;

	}
	
// ################## display contact and client guidelines ##################	
	function _display_other_comments($ContactInfo)
	{

		$page_content='<h5  class="text-uppercase text-primary">Other Information</h5>'."\n";
		$page_content.='<div class="well well-sm guidelines">'."\n";
		$page_content.=$ContactInfo->Notes;
		$page_content.='</div><!-- guidelines -->'."\n";
	
		return $page_content;

	}	
	
// ################## Difference between dates ##################	

	function   _datedifference($date1)
	{

			$date1 = new DateTime($date1);
			$date2 = new DateTime;
			$interval = $date1->diff($date2);
			
			$difference = " (";
			if  ($interval->y > 0){
				$difference .=	$interval->y . " years";
			}
			
			if  ($interval->y > 0 || $interval->m > 0){
				if ($interval->y > 0){
					$difference .=", ";
				}
				$difference .=	$interval->m." months";
			}
			if  ($interval->y == 0 && $interval->m == 0){

				$difference .=	$interval->d." days";
			}

			$difference .= " ago)";
			//$page_content.="difference " . $interval->y . " years, " . $interval->m." months, ".$interval->d." days ";

			return $difference;
	}		

}

/* End of file Zt2016_contact_info.php */
/* Location: ./system/application/controllers/contacts/Zt2016_contact_info.php */
?>