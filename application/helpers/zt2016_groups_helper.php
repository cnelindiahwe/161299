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
 * CodeIgniter group Helpers
 *
 * @package		ZOWTRAK
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Zebra On WHeels

 */


// ################## groups control ##################	
	if ( ! function_exists('_display_groups_control'))
	{
		function   _display_groups_control($GroupsTable,$GroupInfo,$FormURL)
		{

			#top group dropdown
			$FormInfo['FormURL']=$FormURL;
			$FormInfo['labeltext']= 'Group';
			$FormInfo['id'] = 'groups_dropdown_form';
			$FormInfo['class'] = 'form-inline';


			$groups_top_dropdown=zt2016_create_group_selector($GroupsTable,$GroupInfo,$FormInfo)."\n";


			return $groups_top_dropdown;

		}
	}
		
	// ------------------------------------------------------------------------
	/**
	* zt2016_create_group_selector 
	*
	*/
	if ( ! function_exists('zt2016_create_group_selector'))
	{
	// ################## Generate group selector ##################	
		function zt2016_create_group_selector($GroupsTable,$GroupInfo,$FormInfo){
			
			$FormURL =$FormInfo['FormURL'];
			unset($FormInfo['FormURL']); 
			$Labeltext=$FormInfo['labeltext'];
			unset($FormInfo['labeltext']); 
			$group_selector=form_open(site_url().$FormURL,$FormInfo)."\n";
		 	$group_selector.='				<div class="form-group">'."\n";
	      	$group_selector.='					<div class="input-group ">'."\n";
	      	$group_selector.='						<span class="input-group-addon" id="group-addon1">'.$Labeltext.'</span>'."\n";
			$group_selector.= zt2016_groups_dropdown_control($GroupsTable,$GroupInfo)."\n";
	 		$group_selector.='					</div>'."\n";
	 		$group_selector.='				</div>'."\n";
		 	$group_selector.='				<div class="form-group">'."\n";
	      	$group_selector.='					<div class="input-group">'."\n";
			$more = 'id="group_dropdown_selector_submit" class="groupcontrolsubmit form-control"';
			$group_selector.=form_submit('group_dropdown_selector_submit', 'Go',$more);
	 		$group_selector.='					</div>'."\n";
	 		$group_selector.='				</div>'."\n";
			$group_selector.= form_close()."\n";
			
 			return 	$group_selector;
		}
	
	}

	// ------------------------------------------------------------------------
	/**
	* zt2016_groups_dropdown_control 
	*
	*/

	if ( ! function_exists('zt2016_groups_dropdown_control'))
	{
	
		// ################## groups control ##################	
		function   zt2016_groups_dropdown_control($GroupsTable,$GroupInfo)
		{
	
		//groups
	
			$options=array();
			foreach($GroupsTable  as $GroupDetails)
			{
				$options[$GroupDetails->GroupName]=$GroupDetails->GroupName;
			}
			asort($options);
			
			#if ($Currentgroup->CompanyName=="All") {
			#	$options=array('all'=>"All")+$options;
			#}
			$more = 'id="group_dropdown_selector" class="selector form-control input"';

			$selected=$GroupInfo->GroupName;
			
			//$groupscontrol .=form_label('Manage group materials:','group');
			//$more = 'id="group_dropdown_submit" class="groupcontrolsubmit form-control"';			
			$groupscontrol =form_dropdown('Current_Group', $options,$selected ,$more);
	
			return $groupscontrol;
		
		}
	}


	// ------------------------------------------------------------------------
	/**
	* zt2016_groups_edit_form 
	*
	*/

	if ( ! function_exists('zt2016_groups_edit_form'))
	{
	
		// ################## groups control ##################	
		function   zt2016_groups_edit_form($FormData)
		{
	
		$ZOWuser=$FormData['ZOWuser'];	

		# countries list	
		# https://gist.github.com/DHS/1340150	
		$BasicCountriesList = array("","Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China, People's Republic of", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
		
		$CountriesList= array();
		foreach($BasicCountriesList  as $country){
		           $CountriesList [$country]=$country;
		}		
		# generate timezones list
		# http://php.net/manual/en/timezones.php	

		$TimezonesList = generate_timezone_array(); #zt2016_timezone helper
		
		
		# Create group form
		$GroupForm ="\n";
			
		$subsections = array('Name'=>'Name','ID'=>'ID','GroupName'=>'Group Name','Pricing'=>'Pricing', 'DefaultPrice'=>'Default Price','DefaultCurrency'=>'Default Currency','DefaultPaymentDays'=>'Default Payment Days','Location'=>'Location','DefaultCountry'=>'Default Country','DefaultTimeZone'=>'Default Time Zone');

			
		
		$FormURL="groups/zt2016_create_group";
		//$FormInfo="";
		
		$attributes='id="group-data-form"';
		if (isset($FormData['ID'])) {
			$formurl=site_url().'groups/zt2016_group_update';
		}else{
			$formurl=site_url().'groups/zt2016_group_create';
		}

		
		$GroupForm.=form_open($formurl,$attributes )."\n";
		
			
		foreach ($subsections as $key=>$value){
			
			#Headers
			$Headers = array('Name', "Pricing", 'Location');
			
			if (in_array($key,$Headers)) {
				
				#### close inner item group
				if ($key!='Name')	{
					$GroupForm .="	</div><!--item-group-->\n";#item-group
					$GroupForm .="</div><!--col-sm-4-->\n";#col-sm-4
				}
				$GroupForm .="<div class=\"col-sm-4 col-sm-12\">\n";			
				$GroupForm .="	<div class=\"item-group\">\n";
				$currentheader=strtolower(str_replace(" ","",$key));
				$GroupForm .="		<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary ".$currentheader."\">".$key."</h5></div>\n";

			}	
			
			#regular fields
			else {
			$GroupForm .="		<div class=\"col-sm-12\">\n";
				if ($key!='ID')	{
					$GroupForm .="            ".form_label($value.":",$key)."\n";
				}

				

			#### groupname
			if ($key=='GroupName') {
					
				if (isset($FormData[$key])) {
					$GroupForm .= "            ".form_input($key,$FormData[$key],'required="true" class="form-control" id="GroupName"')."\n";
				}
				else {
					$GroupForm .= "            ".form_input($key,'','required="true" class="form-control" id="GroupName"')."\n";
				}
					
					$data = array(
					  'id' => 'GroupFormSubmit',
					  'name' => 'GroupFormSubmit',
					  'class' => 'btn btn-success btn',
					  'style' => 'margin-top:1.4em;margin-bottom:1.4em;',
					);
				
					if (isset($FormData['GroupName'])) {			
						$data['value']="Update Group";				
					} else {
						$data['value']="Create New Group";
					}				
				
					$GroupForm.= form_submit($data);
					
				}
				
			#### price
			else if ($key=='DefaultPrice') {				
				$data = array(
							  'name'        => $key,
							  'id'          => $key,
							  'type'   		=> 'Number',
							  'required'    => 'true',
							  'class'       => 'form-control',
							  'step'	    => '0.01',
							);
				
				if (isset($FormData[$key])) {
					$data['value']= number_format($FormData[$key],2);
				}
				
				$GroupForm .= "            ".form_input($data)."\n";
			}
				
			#### currency
			else if ($key=="DefaultCurrency") {
				$options = array('EUR' => 'EUR', 'USD'=>'USD');
				$more = 'id="Currency" class="Currency form-control required="true""';	


				if (isset($FormData[$key])) {
					$GroupForm .=form_dropdown($key, $options,$FormData[$key],$more)."\n";
				}
				else {
					$GroupForm .=form_dropdown($key, $options,'',$more)."\n";
				}
			}
				
			#### price
			else if ($key=='DefaultPaymentDays') {				
				$data = array(
							  'name'        => $key,
							  'id'          => $key,
							  'type'   		=> 'Number',
							  'min'   		=> 0,
							  'max'   		=> 90,
							  'required'    => 'true',
							  'class'       => 'form-control',
							);
				
				if (isset($FormData[$key])) {
					$data['value']= number_format($FormData[$key]);
				}
				
				$GroupForm .= "            ".form_input($data)."\n";
			}				
			#### payment days
			else if ($key=='GroupName') {

					$GroupForm .= "            ".form_input($key,'','required="true" class="form-control" id="GroupName"')."\n";
					
					$data = array(
					  'id' => 'NewGroupSubmit',
					  'name' => 'NewGroupSubmit',
					  'value' => 'Create New Group',
					  'class' => 'btn btn-success btn',
					  'style' => 'margin-top:1.4em;margin-bottom:1.4em;',
					);
					$GroupForm.= form_submit($data);
					
				}			
				
			#### country
			else if ($key=="DefaultCountry") {
				$options=$CountriesList;
				$more = 'class="form-control"';

				#$GroupForm .="            ".form_label($value.":",$key)."\n";				


				if (isset($FormData[$key])) {

					$GroupForm .= form_dropdown($key, $options,$FormData[$key],$more)."\n";
				}
				else {
					$GroupForm .= form_dropdown($key, $options,'Netherlands',$more)."\n";
				}					
			}
			
			#### timezone
			else if ($key=='DefaultTimeZone') {


				#$GroupForm .= "			".form_label($value.":",$key);

				$more = 'id="group_timezone" class="form-control" required="true"';
				$GroupForm .="				";

				if (isset($FormData[$key])) {
					$GroupForm .=form_dropdown($key, $TimezonesList,$FormData[$key],$more )."\n";
				}
				else {
					$GroupForm .=form_dropdown($key, $TimezonesList,'Europe/Amsterdam',$more )."\n";
				}

			}

			else if ($key=='ID') {
				if (isset($FormData['ID'])){
					$GroupForm .=form_hidden($key,$FormData[$key]);
				}

			}



			### other
			else{	
				if (isset($FormData[$key])) {
					$GroupForm .= "            ".form_input($key,$FormData[$key],'required="true" class="form-control"')."\n";
				}
				else {
					$GroupForm .= "            ".form_input($key,'','required="true" class="form-control"')."\n";
				}
			}

			$GroupForm .="		</div><!---col 12--->\n";
			}
		}
		
		$GroupForm .= form_close()."\n";
		

		return $GroupForm;

		
		}
	}



/* End of file zt2016_groups_helper.php */
/* Location: ./system/application/helpers/zt2016_groups_helper */