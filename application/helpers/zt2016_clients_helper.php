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


// ################## clients control ##################	
	if ( ! function_exists('_display_clients_control'))
	{
		function   _display_clients_control($ClientsTable,$ClientInfo,$FormURL)
		{

			#top client dropdown
			$FormInfo['FormURL']=$FormURL;
			$FormInfo['labeltext']= 'Client';
			$FormInfo['id'] = 'client_dropdown_form';
			$FormInfo['class'] = 'form-inline ';
			$FormInfo['style'] = 'padding:0 11px;';



			$clients_top_dropdown=zt2016_create_clientselector($ClientsTable,$ClientInfo,$FormInfo)."\n";


			return $clients_top_dropdown;

		}
	}


	// ------------------------------------------------------------------------
	/**
	* zt2016_clients_dropdown_control 
	*
	*/

	if ( ! function_exists('zt2016_clients_dropdown_control'))
	{
	
		// ################## clients control ##################	
		function   zt2016_clients_dropdown_control($ClientTable,$CurrentClient)
		{
	
		//Clients
	
			$options=array();
			foreach($ClientTable  as $client)
			{
				$safeclientName=str_replace("&", "~", $client->CompanyName);
				$safeclientName=str_replace(" ", "_", $safeclientName);
				$options[$safeclientName]=$client->CompanyName;
			}
			asort($options);
			
			if ($CurrentClient->CompanyName=="All") {
				$options=array('all'=>"All")+$options;
			}
			$more = 'id="client_dropdown_selector" class="selector form-control input"';
			if (isset($CurrentClient->BilledPaid) && $CurrentClient->BilledPaid==1){
				$more .=" disabled";
			}
			$selected=str_replace("&", "~",$CurrentClient->CompanyName);
			$selected=str_replace(" ", "_", $selected);
			
			//$clientscontrol .=form_label('Manage client materials:','client');
			//$more = 'id="client_dropdown_submit" class="clientcontrolsubmit form-control"';			
			$clientscontrol =form_dropdown('Current_Client', $options,$selected ,$more);
	
			return $clientscontrol;
		
		}
	}



	// ------------------------------------------------------------------------
	/**
	* zt2016_create_clientselector 
	*
	*/
	if ( ! function_exists('zt2016_create_clientselector'))
	{
	// ################## Generate client selector ##################	
		function zt2016_create_clientselector($ClientsTable,$ClientInfo,$FormInfo){
			
			$FormURL =$FormInfo['FormURL'];
			unset($FormInfo['FormURL']); 
			$Labeltext=$FormInfo['labeltext'];
			unset($FormInfo['labeltext']); 

			$client_selector=form_open(site_url().$FormURL,$FormInfo)."\n";
			//$client_selector .= '<div class="col-sm-6">';

		 	$client_selector.='				<div class="form-group ">'."\n";
	      	$client_selector.='					<div class="input-group ">'."\n";
	      	$client_selector.='						<span class="input-group-addon p-2 mt-1" id="client-addon1">'.$Labeltext.'</span>'."\n";
			$client_selector.= zt2016_clients_dropdown_control($ClientsTable,$ClientInfo)."\n";
	 		$client_selector.='					</div>'."\n";
	 		$client_selector.='				</div>'."\n";
		 	$client_selector.='				<div class="form-group">'."\n";
	      	$client_selector.='					<div class="input-group">'."\n";
			$more = 'id="client_dropdown_selector_submit" class="clientcontrolsubmit form-control"';
			$client_selector.=form_submit('client_dropdown_selector_submit', 'Go',$more);
	 		$client_selector.='					</div>'."\n";
	 		$client_selector.='				</div>'."\n";
			 $client_selector.= form_close()."\n";

			
 			return 	$client_selector;
		}
	
	}
 
	// ------------------------------------------------------------------------
	
	/**
	 * _getClientForm()
	 *
	 * Gets client form populated with data

	 */
	if ( ! function_exists('zt2016_getClientForm'))
	{
	function  zt2016_getClientForm( $TimezonesList, $CountriesList, $GroupList,$CurrentClient='')
	{

		$editClientForm ="\n";

		$subsections = array('Basic'=>'Basic Info', 'CompanyName'=>'Company Name','ClientCode'=>'Client Code','ZOWContact'=>'Primary ZOW Contact','Group'=>'Group','TimeZone'=>'Time Zone','Country'=>'Country','Contact'=>'Contact', 'ClientContact'=>'Primary Client Contact','Website'=>'Website','VATOther'=>'VAT Registration / Other','Address'=>'Address Line1','Address2'=>'Address Line2','Address3'=>'Address Line3','Address4'=>'Address Line4','ZIPCode'=>'ZipCode','City'=>'City',
		 'Pricing'=>'Pricing',  'Currency'=>'Currency','BasePrice'=>'Base Price','PriceEdits'=>'Edits Price','PaymentDueDate'=>'Payment Days','Discounts'=>'Discounts','VolDiscount1Trigger'=>'D. I hours','VolDiscount1Price'=>'D. I price','VolDiscount2Trigger'=>'D. II hours','VolDiscount2Price'=>'D. II price','VolDiscount3Trigger'=>'D. III hours','VolDiscount3Price'=>'D. III price','VolDiscount4Trigger'=>'D. IV hours','VolDiscount4Price'=>'D. IV price','Billing'=>'Billing','BillingGuidelines'=>'BillingGuidelines','Retainer'=>'Retainer (non-functional)','RetainerHours'=>'Retainer Hours','RetainerCycle'=>'Retainer Cycle','BilledBy'=>'Billed by', 
		'PONumber'=>'PO Number','BillingAddress'=>'Billing Address','Production'=>'Production', 'ClientDir'=>'Client Directory','GroupDir'=>'Group Directory','ClientGuidelines'=>'Client Guidelines', 'OfficeVersion'=>'Office Version','CustomApps'=>'Custom Apps','Other'=>'Other','OtherInfo'=>'Other Info');

		foreach ($subsections as $key=>$value){
			//Headers
			$Headers = array('Basic', "Contact", 'Pricing','Discounts',"Billing",'Retainer','Production','Other');
			$ColHeaders = array('Basic','Pricing','Production');
			if (in_array($key,$Headers)) {
					#### close inner item group
					if ($key!='Basic')	{
						$editClientForm .="	</div>\n";
					}
				$currentheader=strtolower(str_replace(" ","",$key));
				if (in_array($key,$ColHeaders)) {
					#### close column
					if ($key!='Basic')	{
						$editClientForm .="</div>\n";
					}
					$editClientForm .="<div class=\"col-sm-4 col-sm-12\">\n";
				}

				$editClientForm .="	<div class=\"item-group\">\n";
				
				if ($key=='Basic' || $key=='Pricing' ) {
					$editClientForm .="		<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary ".$currentheader."\">".$key."</h5></div>\n";
				}else{
					$editClientForm .="		<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary ".$currentheader."\">".$key."</h5></div>\n";
				}
			} else{ 
				### Textareas
				$textboxes = array("Address","VATOther","CustomApps","ClientGuidelines","BillingGuidelines","OtherInfo","Address2","Address3","Address4");
				if (in_array($key,$textboxes)) {
					$largeboxes = array("ClientGuidelines","ZOWGuidelines","OtherInfo",);
					//$largeboxes = array();
					if (in_array($key,$largeboxes)) { $rows=10; $cols=45;}
					else { $rows=4; $cols=35;}
					
					$ndata = array('name' => $key, 'id' => $key, 'rows' => $rows, 'cols' => $cols, 'class'=>'form-control');
					if (isset($CurrentClient->ID)) {
						$ndata['value']=$CurrentClient->$key;
					}
					$editClientForm .="		<div class=\"col-sm-12\">\n";
				 	$editClientForm .= "			".form_label($value.":",$key)."\n";
					$editClientForm .= "			".form_textarea($ndata,'','style="min-width: 100%"')."\n";

					}
				### dropdowns
				else if ($key=='ZOWContact') {
					$options = array(''=>'','Miguel' => 'Miguel', 'Paul'=>'Paul', 'Sunil'=>'Sunil');
					$more = 'id="ZOWContact" class="ZOWContact form-control"';	
					
					$editClientForm .="	<div class=\"col-sm-6 col-sm-12\">\n";
				 	$editClientForm .= "			".form_label($value.":",$key);
					$editClientForm .="			";
					if (isset($CurrentClient->ID)) {
						
						$editClientForm .= form_dropdown('ZOWContact', $options,$CurrentClient->$key,$more)."\n";
					}
					else {
						$editClientForm .= form_dropdown('ZOWContact', $options,'',$more)."\n";
					}

					}
				#### country
				else if ($key=="Country") {
					$options=$CountriesList;
					$more = 'class="form-control" required="yes"';
					$editClientForm .="		<div class=\"col-sm-12\">\n";
					$editClientForm .= "			".form_label($value.":",$key);				
					$editClientForm .="				";
					
					if (isset($CurrentClient->ID)) {
						
						$editClientForm .= form_dropdown('Country', $options,$CurrentClient->$key,$more)."\n";
					}
					else {
						$editClientForm .= form_dropdown('Country', $options,'',$more)."\n";
					}					
				}
				#### currency
				else if ($key=="Currency") {
					$options = array('EUR' => 'EUR', 'USD'=>'USD');
					$more = 'id="Currency" class="Currency form-control"';	
					
					$editClientForm .="	<div class=\"col-sm-6 col-sm-12\">\n";
				 	$editClientForm .= "			".form_label($value.":",$key);
					$editClientForm .="				";

					if (isset($CurrentClient->ID)) {
						$editClientForm .=form_dropdown('Currency', $options,$CurrentClient->$key,$more)."\n";
					}
					else {
						$editClientForm .=form_dropdown('Currency', $options,'',$more)."\n";
					}
				}	

				#### Group
				else if ($key=="Group") {
					//$options = array('' => '','ABFS'=>'ABFS','EDWARDS'=>'Edwards','INGB'=>'INGB',  'NASPERS'=>'Naspers', 'PHILIPS' => 'Philips','PORTICUS' => 'Porticus', 'INBEV'=>'SAB Miller/InBev','VIPHOR'=>'Viphor Pharma');

					$options=$GroupList;
					$more = 'id="Group" class="Group form-control"';	
					
					$editClientForm .="	<div class=\"col-sm-12\">\n";
				 	$editClientForm .= "			".form_label($value.":",$key);
					$editClientForm .="				";

					if (isset($CurrentClient->ID)) {
						$editClientForm .=form_dropdown('Group', $options,$CurrentClient->$key,$more)."\n";
					}
					else {
						$editClientForm .=form_dropdown('Group', $options,'',$more)."\n";
					}
				}	
			
				#### timezone
				else if ($key=='TimeZone') {

					$editClientForm .="		<div class=\"col-sm-12\">\n";
			 		$editClientForm .= "			".form_label($value.":",$key);

					$more = 'id="client_timezone" class="form-control"';
					$editClientForm .="				";

					if (isset($CurrentClient->ID)) {
						$editClientForm .=form_dropdown('TimeZone', $TimezonesList,$CurrentClient->$key ,$more )."\n";
					}
					else {
						$editClientForm .=form_dropdown('TimeZone', $TimezonesList,'',$more )."\n";
					}
					
				}
				
				### regukar inputs
				else {
					//Short Inputs
					$input3 = array("ClientCode","BasePrice",'PricePer0Hours','PriceEdits',"PaymentDueDate",'VolDiscount1Price','VolDiscount2Price','VolDiscount3Price','VolDiscount4Price','VolDiscount1Trigger','VolDiscount2Trigger','VolDiscount3Trigger','VolDiscount4Trigger','RetainerHours','OfficeVersion','RetainerHours','RetainerCycle',"ClientDir", "GroupDir");
					if (in_array($key,$input3)) {
						$size='3';
						}
					
					else {
						//Long Inputs
						$input55 = array('ClientDir','GroupDir');
						if (in_array($key,$input55)) {
								$size='45';
							}
							//Regular Size Inputs
							else {
								$size='25';
							}
					}
				$editClientForm .="		";	
				if ($size=='3') { 
					$editClientForm .="<div class=\"col-sm-6 col-sm-12\">\n";
				} else {
					$editClientForm .="<div class=\"col-sm-12\">\n";
				}
				$editClientForm .= "				".form_label($value.":",$key);
				
				$ndata = array('name' => $key, 'id' => $key, 'size' => $size, 'class'=>'form-control');
				
				if (isset($CurrentClient->ID)) {
					$ndata['value']=$CurrentClient->$key;
					
				} elseif ($key=='PriceEdits') {
					$ndata['value']=0.5;
					
				} elseif ($key=='PaymentDueDate') {
					$ndata['value']=30;
				}

				#### if required
				$requiredFields = array("CompanyName","ClientCode","BasePrice","PriceEdits","PaymentDueDate");
				
				if (in_array($key,$requiredFields)) {
						$editClientForm .= "\n				".form_input($ndata,'','required="true"')."\n";
				} 
				else{
						$editClientForm .= "\n				".form_input($ndata)."\n";
				}

			}

			$editClientForm .="		</div>\n";
			}
		}
		$editClientForm .="	</div>\n";
		
		$editClientForm .= form_close()."\n";
		$editClientForm .="<div class=\"clearfix\"></div>";
		return $editClientForm;/**/
	}
}

/* Generates a clickable list of templates and other materials for a given client
 *
 * @access	public
 * @return	string
 */
if ( !function_exists('zt2016_getClientMaterials'))
{
	function  zt2016_getClientMaterials($dirname="",$superuser)
	{
	   
		if ($dirname !=""){
			$finaldir="";
			$dir = explode ("/",$dirname);
			$toplimit=count($dir)-1;
			for ($i = 1; $i <= $toplimit; $i++) {
			   $finaldir .=$dir[$i]."/";
			}
			$finaldir=rtrim($finaldir,"/");
			$clientcode=$dir[0];
			
		$dir = dirname(dirname(dirname(__FILE__)))."/zowtempa/etc/clientmaterials/".$finaldir."/";
	
			// Open a known directory, and proceed to read its contents
			if (is_dir($dir)) {
				//$ClientMaterials = "<p><small class='text-muted'>Looking at ".$dir."</small></p>";
				$ClientMaterials = "<p></p>";	
				// Returns array of files
					$files = scandir($dir);
					// Count number of files and store them to variable..
					$num_files = count($files)-2;
					
					if ($num_files >0){
							if ($dh = opendir($dir)) {
									$ClientMaterials.='<div class="table-responsive">';
									$ClientMaterials.='<table class="table">';
									while (($file = readdir($dh)) !== false) {
											if ($file!='.' && $file!='..') {
												$filedate = date ('F d Y ', filemtime($dir.$file));
												$ClientMaterials.= "<tr><td>\n";
												$ClientMaterials.= '<a href="https://'.$_SERVER['SERVER_NAME'].'/zowtrak2012/clients/zt2016_downloadclientmaterials/'.$finaldir.'/'.$file.'">'.$file."</a>";
												$ClientMaterials.= "</td><td>\n";
												$ClientMaterials.= "(".$filedate.")\n";
												$ClientMaterials.= "</td>\n";
												if ($superuser ==1) {
													$ClientMaterials.= "<td>\n";
													$ClientMaterials.= '<a href="https://'.$_SERVER['SERVER_NAME'].'/zowtrak2012/clients/zt2016_deleteclientmaterials/'.$dirname.'/'.$file.'" class="deletefile"><img src="'.base_url().'/web/img/trash.gif"></a>'."\n<br/>";
													$ClientMaterials.= "</td>\n";
												}
												$ClientMaterials.= "</tr>\n";
											}
									}
									closedir($dh);
									$ClientMaterials.='</table>';
									$ClientMaterials.='</div>';
							}
							else {$ClientMaterials.= '<div class="alert alert-warning" role="alert">No files found for name '.$finaldir." (code 1)</div>";}							
					}
					else {$ClientMaterials.= '<div class="alert alert-warning" role="alert">No files found found for name '.$finaldir." (code 2)</div>";}
			}
			else {$ClientMaterials= '<div class="alert alert-danger" role="alert">ERROR - No client directory for '.$finaldir.' found.</div>';}
		}
		else {$ClientMaterials= '<div class="alert alert-danger" role="alert">ERROR - No client code provided.</div>';}
		
		return $ClientMaterials;	


	}
}


/* End of file zt2016_clients_helper.php */
/* Location: ./system/application/helpers/zt2016_clients_helper */