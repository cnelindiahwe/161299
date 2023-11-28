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
 * _getClientForm()
 *
 * Gets client form populated with data
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('zt2016_getClientForm'))
{


	// 
	function  zt2016_getClientForm($CurrentClient='', $TimezonesList, $CountriesList)
	{


		$editClientForm ="<div class=\"form-group\">";
		$subsections = array('Contact Info'=>'Contact Info', 'CompanyName'=>'Company Name','ClientCode'=>'Client Code', 'BilledBy'=>'Billed by','Website'=>'Website','VATOther'=>'VAT Registration / Other','Address'=>'Address','ZIPCode'=>'ZipCode','City'=>'City','Country'=>'Country','TimeZone'=>'Time Zone', 'ZOWContact'=>'Primary ZOW Contact', 'ClientContact'=>'Primary Client Contact','Pricing Info'=>'Billing Info',  'Currency'=>'Currency','BasePrice'=>'Base Price','PriceEdits'=>'Edits Price','VolDiscount1Trigger'=>'Vol. Disc. 1 at','VolDiscount1Price'=>'Vol. Disc. 1 price','VolDiscount2Trigger'=>'Vol. Disc. 2 at','VolDiscount2Price'=>'Vol. Disc. 2 price','VolDiscount3Trigger'=>'Vol. Disc. 3 at','VolDiscount3Price'=>'Vol. Disc. 3 price','VolDiscount4Trigger'=>'Vol. Disc. 4 at','VolDiscount4Price'=>'Vol. Disc. 4 price','RetainerHours'=>'Retainer Hours','RetainerCycle'=>'Retainer Cycle','PaymentDueDate'=>'Payment Days',
		'Process Information'=>'Process Information', 'OfficeVersion'=>'Office Version','ClientDir'=>'Client Directory','GroupDir'=>'Group Directory','CustomApps'=>'Custom Apps','ClientGuidelines'=>'Client Guidelines','BillingGuidelines'=>'BillingGuidelines','OtherGuidelines'=>'Other Info', );

		foreach ($subsections as $key=>$value){
			//Headers
			$Headers = array('Pricing Info','Process Information',"Billing Address",'Contact Info');
			if (in_array($key,$Headers)) {
				$currentheader=strtolower(str_replace(" ","",$key));
				$editClientForm .="</div><div class=\"col-sm-4\"><h5 class=\"text-uppercase text-primary ".$currentheader."\">".$key."</h5>\n";
			}else{
				$editClientForm .="<fieldset class=\"".$currentheader." ".$key."\">\n";
				$editClientForm .= form_label($value.":",$key);
				//Textboxes
				$textboxes = array("Address","VATOther","CustomApps","ClientGuidelines","BillingGuidelines","OtherGuidelines",);
				if (in_array($key,$textboxes)) {
					$largeboxes = array("ClientGuidelines","ZOWGuidelines","OtherGuidelines",);
					if (in_array($key,$largeboxes)) { $rows=10; $cols=45;}
					else { $rows=4; $cols=35;}
					
					$ndata = array('name' => $key, 'id' => $key, 'rows' => $rows, 'cols' => $cols, 'class'=>'form-control');
					if (isset($CurrentClient->ID)) {
						$ndata['value']=$CurrentClient->$key;
					}
					$editClientForm .= form_textarea($ndata)."\n";
					}
				else if ($key=='ZOWContact') {
					$options = array(''=>'','Miguel' => 'Miguel', 'Paul'=>'Paul', 'Sunil'=>'Sunil');
					$more = 'id="ZOWContact" class="ZOWContact form-control"';	
					if (isset($CurrentClient->ID)) {
						$editClientForm .=form_dropdown('ZOWContact', $options,$CurrentClient->$key,$more);
					}
					else {
						$editClientForm .=form_dropdown('ZOWContact', $options,'',$more);
					}
					}
				else if ($key=='TimeZone') {
					$more = 'id="client_timezone" class="form-control"';
					if (isset($CurrentClient->ID)) {
						$editClientForm .= TimeZoneDropDown($CurrentClient->$key);
						
					}
					else {
						$editClientForm .= TimeZoneDropDown();
					}
					
					$editClientForm .=form_dropdown('Timezones', $TimezonesList,$CurrentClient->$key ,$more );
				}
				else {
					//Short Inputs
					$input3 = array("TimeZone","ClientCode","Currency","BasePrice",'PricePer0Hours','VolDiscount1Price','VolDiscount2Price','VolDiscount3Price','VolDiscount4Price','VolDiscount1Trigger','VolDiscount2Trigger','VolDiscount3Trigger','VolDiscount4Trigger','PriceEdits','RetainerHours','OfficeVersion','RetainerHours','RetainerCycle',"PaymentDueDate");
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
				$ndata = array('name' => $key, 'id' => $key, 'size' => $size, 'class'=>'form-control');
					if (isset($CurrentClient->ID)) {
						$ndata['value']=$CurrentClient->$key;
					}
				$editClientForm .= "\n".form_input($ndata)."\n";
				if ($key=="Country") {
					$more = 'class="form-control"';
					$editClientForm .=form_dropdown('Country', $CountriesList,$CurrentClient->$key,$more);
				}
				}
			$editClientForm .="</fieldset>\n";
			}
		}
		$editClientForm .="</div>";
		
		$editClientForm .= form_close()."\n";
		$editClientForm .="<div class=\"clearfix\"></div>";
		return $editClientForm;/**/
	}
}

// ------------------------------------------------------------------------

/**
zt2016_clients_dropdown_control($ClientTable,$CurrentClient)
 *
 * Displays a dropdown selector with the clients' names
 *
 * @access	public
 * @return	string
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
		$options=array('all'=>"All")+$options;		
		$more = 'id="client_dropdown_selector" class="selector form-control input"';			
		$selected=str_replace("&", "~",$CurrentClient->CompanyName);
		$selected=str_replace(" ", "_", $selected);
		
		//$clientscontrol .=form_label('Manage client materials:','client');
		//$more = 'id="client_dropdown_submit" class="clientcontrolsubmit form-control"';			
		$clientscontrol =form_dropdown('Current_Client', $options,$selected ,$more);

		return $clientscontrol;
	
	}
}



	// ################## Generate client selector ##################	
		function zt2016_create_clientselector($ClientsTable,$ClientInfo,$FormInfo){
		


		$client_selector=form_open(site_url().$FormInfo['FormURL'],$FormInfo);
	 	$client_selector.='				<div class="form-group">'."\n";
      	$client_selector.='					<div class="input-group ">'."\n";
      	$client_selector.='						<span class="input-group-addon" id="basic-addon1">'.$FormInfo['labeltext'].'</span>'."\n";
		$client_selector.= zt2016_clients_dropdown_control($ClientsTable,$ClientInfo);
 		$client_selector.='					</div>';
 		$client_selector.='				</div>';
	 	$client_selector.='				<div class="form-group">'."\n";
      	$client_selector.='					<div class="input-group">'."\n";
		$more = 'id="client_dropdown_selector_submit" class="clientcontrolsubmit form-control"';
		$client_selector.=form_submit('client_dropdown_selector_submit', 'Go',$more);
		$client_selector.= form_close()."\n";
 		$client_selector.='					</div>'."\n";
 		$client_selector.='				</div>'."\n";
 		$client_selector.='			</form>'."\n";
 		return 	$client_selector;
	}


/* End of file zt2016_clients_helper.php */
/* Location: ./system/application/helpers/zt2016_clients_helper */