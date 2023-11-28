<?php

class Zt2016_contact_duplicate extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('form', 'url', 'general', 'userpermissions', 'zt2016_clients', 'zt2016_contacts', 'zt2016_timezone'));
		
		$zowuser=_superuseronly(); 
		
		$templateData['ZOWuser']= _getCurrentUser();

		//Identify contact to display 
		//Via URL
		$Contact_ID=$this->uri->segment(3);



		//if not via URL, via POST
		 if (empty ($Contact_ID) || !is_numeric ($Contact_ID)) {
		 	if ($this->input->post('Current_Contact')){
		 		$Contact_ID=$this->input->post('Current_Contact');
		 	} else {
				//If no contact ID, then check if client info was posted
				if ($this->input->post('Current_Client')){
					//if client info was posted, display 
					//the first (ordered alphabetically) contact name for the client
		 			$Currect_Client=$this->input->post('Current_Client');
					
					$Currect_Client=str_replace( "~","&", $Currect_Client);
					$Currect_Client=str_replace( "_"," ", $Currect_Client);
					
					$this->load->model('zt2016_contacts_model', '', TRUE);
					$First_Client_Contact= $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $Currect_Client,"limit"=>1, 'sortBy'=>'FirstName', 'sortDirection'=>'Asc'));
					$Contact_ID=$First_Client_Contact->ID;
					
					//var_dump ($First_Client_Contact);
					//die ($Currect_Client);
				} else{
					//No contact ID
					redirect('contacts/zt2016_contacts_search', 'refresh'); 
				}
		 	}
		 }
		

	
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$this->load->model('zt2016_clients_model', '', TRUE);
		
		
		$ContactInfo = $this->zt2016_contacts_model->GetContact($options = array('ID' => $Contact_ID));
		
		 if (empty ($ContactInfo) ) {
			redirect('contacts/zt2016_contacts', 'refresh');
		 }
		
		$ContactInfo->FullName = $ContactInfo->FirstName.' '. $ContactInfo->LastName;

		
		$templateData['title'] = 'Edit Contact Information for '.$ContactInfo->FullName;

		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _create_edit_contact_page($ContactInfo,$templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}
	

	// ################## display clients info ##################	
	function _create_edit_contact_page($ContactInfo,$ZOWuser)
	{
					
		# retrieve all clients from db
		$ClientsTable = $this->zt2016_clients_model->GetClient();

		# retrieve all current contact's company contacts from db		
		
		$ContactsTable = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $ContactInfo->CompanyName));
		
		
		# retrieve current contact's company info from db		
		$ClientInfo = $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $ContactInfo->CompanyName));

		if (empty($ClientInfo)){
			redirect('contacts/zt2016_contacts', 'refresh');
		}

		$SafeClientName=str_replace(" ", "_", $ContactInfo->CompanyName);
		$SafeClientName=str_replace("&", "~", $SafeClientName);
		$ClientInfo->SafeClientName=$SafeClientName;
		
		# countries list	
		# https://gist.github.com/DHS/1340150	
		$BasicCountriesList = array("","Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China, People's Republic of", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
		
		$CountriesList= array();
		foreach($BasicCountriesList  as $country){
		           $CountriesList [$country]=$country;
		}	
		
		
		# generate timezones list
		# http://php.net/manual/en/timezones.php	
		

		$TimezonesList = generate_timezone_array();
		
		#Create page.

		$page_content=$this->_display_edit_contact_page($ContactInfo,$ContactsTable,$ClientsTable,$ClientInfo,$ZOWuser,$TimezonesList,$CountriesList);

		return $page_content;


	
	}	


// ################## create page ##################	
	function  _display_edit_contact_page($ContactInfo,$ContactsTable,$ClientsTable,$ClientInfo,$ZOWuser,$TimezonesList,$CountriesList)
	{

		$page_content ='<div class="page_content">'."\n";



		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}


				
		########## panel head

		$attributes='id="contact-information-form"';
		$formurl=site_url().'contacts/zt2016_contact_create';
		
		$page_content.=form_open($formurl,$attributes )."\n";
		
		$page_content.='<div id="client_info_panel" class="panel panel-default"  style="margin-top:2em;">'."\n";
		$page_content.='<div class="panel-heading">'."\n";
		$page_content.=' <h3 class="panel-title">';
		$page_content.= "Duplicate <a href=\"".site_url()."contacts/zt2016_contact_info/".$ContactInfo->ID."\">".$ContactInfo->FullName."</a> <small>( ID ".$ContactInfo->ID."  - <a href=\"".site_url()."clients/zt2016_client_info/".$ClientInfo->SafeClientName."\">".$ContactInfo->CompanyName."</a>";
		$page_content.=' )</small> </h3>';
		
		######### buttons
		$page_content.= "<p class='top-buffer-10'>";
		
			# submit button
			$ndata = array('class' => 'submitButton btn btn-success btn-sm','value' => 'Duplicate Contact');
			$page_content .= form_submit($ndata)."\n";

			# cancel button
			$page_content.='<a href="'.site_url().'contacts/zt2016_contact_info/'.$ContactInfo->ID.'" class="btn btn-info btn-sm pull-right">Cancel</a>';
		

		$page_content.= '</p>'."\n";
		
		$page_content.= '</div>'."\n";

		########## panel body
		$page_content.='<div class="panel-body">'."\n";
				


		unset($ContactInfo->CompanyName);
		
		$page_content .=zt2016_get_Contact_Form($TimezonesList, $CountriesList, $ClientsTable, $ContactInfo);


		$page_content.='</div>'.'<!-- // class="panel-body" -->'."\n";
		$page_content .='</div><!-- // class="page_content" -->'."\n";
		$page_content.=form_close()."\n";
		
		return $page_content;
	}


}

/* End of file Zt2016_contact_edit.php */
/* Location: ./system/application/controllers/contacts/Zt2016_contact_edit.php */
?>