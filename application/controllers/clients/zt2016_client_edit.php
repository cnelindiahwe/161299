<?php

class Zt2016_client_edit extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','general','userpermissions','zt2016_clients','zt2016_timezone'));
		
		$zowuser=_superuseronly(); 
		
		$templateData['ZOWuser']= _getCurrentUser();
		

		$SafeclientName=$this->uri->segment(3);

		if ($SafeclientName=="All"){
					redirect('clients/zt2016_clients', 'refresh');
		}

		 if (empty ($SafeclientName)) {
		 	if ($this->input->post('Current_Client')){
		 		$SafeclientName=$this->input->post('Current_Client');
				if ($SafeclientName == "all") {
					//die ($SafeclientName);
					if ($templateData['ZOWuser']=="miguel" || $templateData['ZOWuser']=="sunil.singal" | $templateData['ZOWuser']=="alvaro.ollero") {						
						redirect('clients/zt2016_clients', 'refresh');
					} else{
						redirect('contacts/zt2016_contacts_search', 'refresh');
					}
				}
				
		 	} else{
					die ("no client name");
					redirect('clients/zt2016_clients', 'refresh');
		 	}
			
			 
		 }		 


		$clientName=str_replace("_", " ", $SafeclientName);
		$clientName=str_replace("~", "&", $clientName);

		$templateData['title'] = 'Edit Client Information for '.$clientName;
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _create_client_page($clientName,$SafeclientName,$templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}
	

	// ################## display clients info ##################	
	function _create_client_page($clientName,$SafeclientName,$ZOWuser)
	{
					
				

		# retrieve all clients from db		
		$this->load->model('zt2016_clients_model', '', TRUE);
		$ClientsTable = $this->zt2016_clients_model->GetClient();

		# retrieve all groups from db		
		$this->load->model('zt2016_groups_model', '', TRUE);
		$GroupsData = $this->zt2016_groups_model->GetGroup();

		$GroupList['DEFAULT']='';
		
		foreach ($GroupsData as $GroupsDetail){
			if ($GroupsDetail->GroupName!='DEFAULT'){
				$GroupList[$GroupsDetail->GroupName]=$GroupsDetail->GroupName;
			}
		}
		
		if($this->session->flashdata('FormValues')){
			# retrieve current client from flashdata	
			// http://stackoverflow.com/questions/1869091/how-to-convert-an-array-to-object-in-php
			$ClientInfo = (object)  $this->session->flashdata('FormValues');
			
			//var_dump($ClientInfo);
			//die();
		} else {
			# retrieve current client from db		
			$ClientInfo = $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $clientName));
			
			if (!$ClientInfo){
					if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" | $ZOWuser=="alvaro.ollero") {
						redirect('clients/zt2016_clients', 'refresh');
					} else{
						redirect('contacts/zt2016_contacts_search', 'refresh');
					}
			}
		}	


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

		$page_content=$this->_display_page($GroupList,$ClientsTable,$ClientInfo,$ZOWuser,$SafeclientName,$CountriesList,$TimezonesList);

		return $page_content;


	
	}	


// ################## create page ##################	
	function   _display_page ($GroupList,$ClientsTable,$ClientInfo,$ZOWuser,$SafeclientName,$CountriesList,$TimezonesList)
	{

		$page_content ='<div class="page_content">'."\n";

		######### client dropdown
		$page_content.=$this->_display_clients_control($ClientsTable,$ClientInfo);
		

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}


				
		########## panel head
		 		//Invoice status form
		//$attributes='class="form-inline" id="invoice-status-form"';
		$attributes='id="client-information-form"';
		$formurl=site_url().'clients/zt2016_client_update/'.$ClientInfo->ID;
		
		$page_content.=form_open($formurl,$attributes )."\n";
		
		$page_content.='<div id="client_info_panel" class="panel panel-default"  style="margin-top:2em;">'."\n";
		$page_content.='<div class="panel-heading">'."\n";
		$page_content.=' <h3 class="panel-title">';
		$page_content.= "Edit information for <a href=\"".site_url()."clients/zt2016_client_info/".$SafeclientName."\">".$ClientInfo->CompanyName."</a> <small>( ID ".$ClientInfo->ID." )</small>";
		$page_content.=' </h3>';
		
		######### buttons
		$page_content.= "<p class='top-buffer-10'>";
		
			# submit button
			$ndata = array('class' => 'submitButton btn btn-success btn-sm','value' => 'Update Data');
			$page_content .= form_submit($ndata)."\n";

			# cancel button	
			$page_content.='<a href="'.site_url().'clients/zt2016_client_info/'.$SafeclientName.'" class="btn btn-info btn-sm">Cancel</a>';
		
			# trash button
			$page_content.='<a href="'.site_url().'clients/zt2016_client_trash/'.$SafeclientName.'" class="btn btn-danger btn-sm pull-right btn-delete">Trash</a>';

		$page_content.= '</p>'."\n";
		
		$page_content.= '</div>'."\n";

		########## panel body
		$page_content.='<div class="panel-body">'."\n";
				


		$page_content .=zt2016_getClientForm($TimezonesList, $CountriesList,$GroupList, $ClientInfo);


		$page_content.='</div>'.'<!-- // class="panel-body" -->'."\n";
		$page_content .='</div><!-- // class="page_content" -->'."\n";


		return $page_content;
		
	}

// ################## clients control ##################	
	function   _display_clients_control($ClientsTable,$ClientInfo)
	{
		
		#top client dropdown
		$FormInfo['FormURL']="clients/zt2016_client_edit";
		$FormInfo['labeltext']= 'Edit data for';
		$FormInfo['id'] = 'client_dropdown_form';
		$FormInfo['class'] = 'form-inline';
		
	
		$clients_top_dropdown=zt2016_create_clientselector($ClientsTable,$ClientInfo,$FormInfo)."\n";
		

		return $clients_top_dropdown;
	
	}


}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>