<?php

class Zt2016_client_new extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','clients','general','userpermissions','zt2016_clients','zt2016_timezone'));
		
		$zowuser=_superuseronly(); 
		


		$templateData['title'] = 'New Client';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _new_client_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}
	

	// ################## display clients info ##################	
	function _new_client_page($ZOWuser)
	{
					

		# retrieve all groups from db		
		$this->load->model('zt2016_groups_model', '', TRUE);
		$GroupsData = $this->zt2016_groups_model->GetGroup();

		$GroupList['DEFAULT']='';
		
		foreach ($GroupsData as $GroupsDetail){
			if ($GroupsDetail->GroupName!='DEFAULT'){
				$GroupList[$GroupsDetail->GroupName]=$GroupsDetail->GroupName;
			}
		}
		
		# check if it is a new client from existing group

		
		$PostData = $this->input->post('ClientFormValues');

		#  if not a new client from existing group, use DEFAULT group settings
		if (!isset($PostData['GroupName'])){			
			$PostData['GroupName']='DEFAULT';			
		}

		$FormData= $this->zt2016_groups_model->GetGroup($options = array('GroupName'=>$PostData['GroupName'])); 

		if (!isset($FormData)){
					$Message=$PostData['GroupName']." group not found. Unable to set defeault price for new client.";
					$this->session->set_flashdata('ErrorMessage',$Message);
					redirect('clients/zt2016_clients/'.$SafeclientName, 'refresh');		
		}
		
		
		if (!isset($FormData)){
					$Message=$PostData['GroupName']." group not found. Unable to set defeault price for new client.";
					$this->session->set_flashdata('ErrorMessage',$Message);
					redirect('clients/zt2016_client_new', 'refresh');		
		}
		

		$FormData = (array) $FormData;

		$FormData['ID']= 99999999999;
		$FormData['Group']= $FormData['GroupName'];
		$FormData['PriceEdits']=0.5;
		$FormData['ZOWContact']=ucfirst($ZOWuser);
		
		$RestofValues=array('CompanyName','ClientCode','Contact','ClientContact','Website','VATOther','Address','ZIPCode','City','Pricing','Currency','VolDiscount1Trigger','VolDiscount1Price','VolDiscount2Trigger','VolDiscount2Price','VolDiscount3Trigger','VolDiscount3Price','VolDiscount4Trigger','VolDiscount4Price','BillingGuidelines','Retainer','RetainerHours','RetainerCycle','BilledBy','ClientDir','GroupDir','ClientGuidelines','OfficeVersion','CustomApps','OtherInfo');
		foreach ($RestofValues as $Key){
			$FormData[$Key]='';
		}
		$FormData = (object) $FormData;

		//}


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
			$page_content=$this->_display_page($ZOWuser,$CountriesList,$TimezonesList,$GroupList,$FormData);

		return $page_content;


	
	}	


// ################## create page ##################	
	function   _display_page ($ZOWuser,$CountriesList,$TimezonesList,$ClientInfo,$FormData)
	{
		

		$GroupData=$FormData;

		if (!empty($GroupData->DefaultCountry)){$GroupData->Country=$GroupData->DefaultCountry;}
		if (!empty($GroupData->DefaultTimeZone)){$GroupData->TimeZone=$GroupData->DefaultTimeZone;}
		if (!empty($GroupData->DefaultCurrency)){$GroupData->Currency=$GroupData->DefaultCurrency;}
		if (!empty($GroupData->DefaultPrice)){$GroupData->BasePrice=$GroupData->DefaultPrice;}
		if (!empty($GroupData->DefaultPaymentDays)){$GroupData->PaymentDueDate=$GroupData->DefaultPaymentDays;}

		
		
		$page_content ='<div class="page_content">'."\n";

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" >'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";

		}
				
		########## panel head
		 //Invoice status form
		//$attributes='class="form-inline" id="invoice-status-form"';
		$attributes='id="client-information-form"';
		$formurl=site_url().'clients/zt2016_client_create';
		
		$page_content.=form_open($formurl,$attributes )."\n";
		
		$page_content.='<div id="client_info_panel" class="panel panel-default" >'."\n";
		$page_content.='<div class="panel-heading">'."\n";
		$page_content.=' <h4>';
		if (empty($GroupData->Group)){
			$page_content.= "New client";
		} else {
			$page_content.= "New ".$GroupData->Group." client";
		}
		
		$page_content.=' </h4>';
		
		$ndata = array('class' => 'submitButton','value' => 'Create New Client');
	
		$page_content .= form_submit($ndata)."\n";
		$page_content.= '</div>'."\n";

		########## panel body
		$page_content.='<div class="panel-body">'."\n";

		$page_content .=zt2016_getClientForm($TimezonesList, $CountriesList, $ClientInfo,$GroupData);

		$page_content.='</div>'.'<!-- // class="panel-body" -->'."\n";
		$page_content .='</div><!-- // class="page_content" -->'."\n";


		return $page_content;
		
	}



}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>