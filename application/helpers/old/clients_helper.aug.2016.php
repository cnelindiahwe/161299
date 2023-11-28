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
if ( ! function_exists('getClients'))
{

	function  getClients()
	{
	
		//http://codeigniter.com/forums/viewthread/71493/
		//Loads model into helper
		$CI =& get_instance();
		$CI->load->model('trakclients', '', TRUE);
		$getentries = $CI->trakclients->GetEntry($options = array('Trash' => '0','sortBy'=> 'CompanyName','sortDirection'=> 'desc'));
		
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
if ( ! function_exists('GetTrashedClients'))
{

	function getTrashedClients()
	{
	
		//http://codeigniter.com/forums/viewthread/71493/
		//Loads model into helper
		$CI =& get_instance();
		$CI->load->model('trakclients', '', TRUE);
		$getentries = $CI->trakclients->GetEntry($options = array('Trash' => '1'));
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
if ( ! function_exists('getCurrentClient'))
{


	function  getCurrentClient()
	{
			$CI =& get_instance();
			$CI->load->model('trakclients', '', TRUE);
			$currentclient = $CI->trakclients->GetEntry($options = array('ID' => $CI->uri->segment(2)));
			if($currentclient)
				{
				return $currentclient;
				}
			else {
			 	echo 'There was a problem retrieving the current entry';
			}
	}

}
// ------------------------------------------------------------------------

/**
 * _getClientForm()
 *
 * Gets client form populated with data
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('_getClientForm'))
{


	// 
	function  _getClientForm($CurrentClient='')
	{
		$editClientForm="<h4 class=\"formdiv\"> Client Details</h4><div class=\"formdiv\">";
		$attributes = array( 'id' => 'clientform');
		if (isset($CurrentClient->ID)) {
			$editClientForm .= form_open(site_url().'clients/updateclient/'.$CurrentClient->ID,$attributes)."\n";
		}
		else {
			$editClientForm .= form_open(site_url().'clients/addclient',$attributes)."\n";
		}
		$editClientForm .="<fieldset class=\"formbuttons\">";
		$ndata = array('name' => 'submit','class' => 'submitButton');
		if (isset($CurrentClient->ID)) {
			$ndata['value'] = 'Update Client';
		}
		else {
			$ndata['value'] = 'Add Client';
		}
		$editClientForm .= form_submit($ndata)."\n";
		$editClientForm .="</fieldset>";
		$editClientForm .="<div>";
		$subsections = array('Contact Info'=>'Contact Info','ClientCode'=>'Client Code', 'BilledBy'=>'Billed by', 'CompanyName'=>'Company Name','Website'=>'Website','VATOther'=>'VAT Registration / Other','Address'=>'Address','ZIPCode'=>'ZipCode','City'=>'City','Country'=>'Country','TimeZone'=>'Time Zone', 'ZOWContact'=>'Primary ZOW Contact', 'ClientContact'=>'Primary Client Contact','Pricing Info'=>'Billing Info',  'Currency'=>'Currency','BasePrice'=>'Base Price','PriceEdits'=>'Edits Price','VolDiscount1Trigger'=>'Vol. Disc. 1 at','VolDiscount1Price'=>'Vol. Disc. 1 price','VolDiscount2Trigger'=>'Vol. Disc. 2 at','VolDiscount2Price'=>'Vol. Disc. 2 price','VolDiscount3Trigger'=>'Vol. Disc. 3 at','VolDiscount3Price'=>'Vol. Disc. 3 price','VolDiscount4Trigger'=>'Vol. Disc. 4 at','VolDiscount4Price'=>'Vol. Disc. 4 price','RetainerHours'=>'Retainer Hours','RetainerCycle'=>'Retainer Cycle','PaymentDueDate'=>'Payment Days',
		'Process Information'=>'Process Information', 'OfficeVersion'=>'Office Version','ClientDir'=>'Client Directory','GroupDir'=>'Group Directory','CustomApps'=>'Custom Apps','ClientGuidelines'=>'Client Guidelines','ZOWGuidelines'=>'ZOW Guidelines','OtherGuidelines'=>'Other Info', );

		foreach ($subsections as $key=>$value){
			//Headers
			$Headers = array('Pricing Info','Process Information',"Billing Address",'Contact Info');
			if (in_array($key,$Headers)) {
				$currentheader=strtolower(str_replace(" ","",$key));
				$editClientForm .="</div><div><h5 class=\"".$currentheader."\">".$key."</h5>\n";
			}else{
				$editClientForm .="<fieldset class=\"".$currentheader." ".$key."\">\n";
				$editClientForm .= form_label($value.":",$key);
				//Textboxes
				$textboxes = array("Address","VATOther","CustomApps","ClientGuidelines","ZOWGuidelines","OtherGuidelines",);
				if (in_array($key,$textboxes)) {
					$largeboxes = array("ClientGuidelines","ZOWGuidelines","OtherGuidelines",);
					if (in_array($key,$largeboxes)) { $rows=10; $cols=45;}
					else { $rows=4; $cols=35;}
					
					$ndata = array('name' => $key, 'id' => $key, 'rows' => $rows, 'cols' => $cols);
					if (isset($CurrentClient->ID)) {
						$ndata['value']=$CurrentClient->$key;
					}
					$editClientForm .= form_textarea($ndata)."\n";
					}
				else if ($key=='ZOWContact') {
					$options = array(''=>'','Miguel' => 'Miguel', 'Paul'=>'Paul', 'Sunil'=>'Sunil');
					$more = 'id="ZOWContact" class="ZOWContact"';	
					if (isset($CurrentClient->ID)) {
						$editClientForm .=form_dropdown('ZOWContact', $options,$CurrentClient->$key,$more);
					}
					else {
						$editClientForm .=form_dropdown('ZOWContact', $options,'',$more);
					}
					}
				else if ($key=='TimeZone') {
					if (isset($CurrentClient->ID)) {
						$editClientForm .= TimeZoneDropDown($CurrentClient->$key);
					}
					else {
						$editClientForm .= TimeZoneDropDown();
					}

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
				$ndata = array('name' => $key, 'id' => $key, 'size' => $size);
					if (isset($CurrentClient->ID)) {
						$ndata['value']=$CurrentClient->$key;
					}
				$editClientForm .= "\n".form_input($ndata)."\n";
				}
			$editClientForm .="</fieldset>\n";
			}
		}
		$editClientForm .="</div>";
		
		//$editClientForm .= "<h4>&nbsp;</h4>\n";
		$editClientForm .= form_close()."\n";
		$editClientForm .="<div class=\"clearfix\"></div>";
		return $editClientForm;/**/
	}
}
// ------------------------------------------------------------------------

/**
 * _getClientSelectorForm()
 *
 * Generates a clickable list of all existing clients 
 *
 * @access	public
 * @return	string
 */
if ( !function_exists('getClientSelectorForm'))
{
	function  getClientSelectorForm($ClientList,$Selected="" )
	{
		$ClientSelectorForm ="<h3>Active Clients</h3>";

		foreach($ClientList as $client)
		{
			if ($client->CompanyName==$Selected){ $ClientSelectorForm .="<strong>";}
			$ClientSelectorForm .="<a href=\"".base_url()."clients/editclient/$client->ID\">".$client->CompanyName."</a>";
			if ($client->CompanyName==$Selected){ $ClientSelectorForm .="</strong>";}
		}
		return $ClientSelectorForm;	


	}
}


// ------------------------------------------------------------------------

/**
 * _getClientMaterials()
 *
 * Generates a clickable list of templates and other materials for a given client
 *
 * @access	public
 * @return	string
 */
if ( !function_exists('getClientMaterials'))
{
	function  getClientMaterials($clientcode="")
	{
		if ($clientcode !=""){
			$clientcode=strtoupper($clientcode);
			$dir = $_SERVER['NFSN_SITE_ROOT'] . "protected/clientmaterials/".$clientcode."/";
			// Open a known directory, and proceed to read its contents
			if (is_dir($dir)) {
				$ClientMaterials = "<p><small>Looking at ".$dir."</small></p>";	
				// Returns array of files
					$files = scandir($dir);
					// Count number of files and store them to variable..
					$num_files = count($files)-2;
					
					if ($num_files >0){
							if ($dh = opendir($dir)) {
									while (($file = readdir($dh)) !== false) {
											if ($file!='.' && $file!='..') {
												$filedate = date ('F d Y ', filemtime($dir.$file));
												$ClientMaterials.= '<a href="http://'.$_SERVER['SERVER_NAME'].'/zowtrak2012/clients/ajax_downloadmaterials/'.$clientcode.'/'.$file.'">'.$file."</a> (".$filedate.")\n";
												$ClientMaterials.= '<a href="http://'.$_SERVER['SERVER_NAME'].'/zowtrak2012/clients/deleteclientfile/'.$clientcode.'/'.$file.'" class="deletefile"><img src="'.base_url().'/web/img/trash.gif"></a>'."\n<br/>";
											}
									}
									closedir($dh);
							}
							else {$ClientMaterials.= '<div class="alert alert-warning" role="alert">No files found for client code '.$clientcode." (code 1)</div>";}							
					}
					else {$ClientMaterials.= '<div class="alert alert-warning" role="alert">No files found for client code '.$clientcode." (code 2)</div>";}
			}
			else {$ClientMaterials= '<div class="alert alert-danger" role="alert">ERROR - No client directory found.</div>';}
		}
		else {$ClientMaterials= '<div class="alert alert-danger" role="alert">ERROR - No client code provided.</div>';}
		
		return $ClientMaterials;	


	}
}
/* End of file client_helper.php */
/* Location: ./system/application/helpers/client_helper.php */

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
			$dir = $_SERVER['NFSN_SITE_ROOT'] . "protected/clientmaterials/".$finaldir."/";
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
												$ClientMaterials.= '<a href="http://'.$_SERVER['SERVER_NAME'].'/zowtrak2012/clients/zt2016_downloadclientmaterials/'.$finaldir.'/'.$file.'">'.$file."</a>";
												$ClientMaterials.= "</td><td>\n";
												$ClientMaterials.= "(".$filedate.")\n";
												$ClientMaterials.= "</td>\n";
												if ($superuser ==1) {
													$ClientMaterials.= "<td>\n";
													$ClientMaterials.= '<a href="http://'.$_SERVER['SERVER_NAME'].'/zowtrak2012/clients/zt2016_deleteclientmaterials/'.$dirname.'/'.$file.'" class="deletefile"><img src="'.base_url().'/web/img/trash.gif"></a>'."\n<br/>";
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
/* End of file client_helper.php */
/* Location: ./system/application/helpers/client_helper.php */
