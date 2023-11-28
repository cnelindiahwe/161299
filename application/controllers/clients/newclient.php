<?php

class newclient extends MY_Controller {

	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
	
		$this->load->helper(array('clients','general','form','userpermissions'));
		

		$this->load->model('trakclients', '', TRUE);
		$ClientList = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageInput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageInput'] .= $this->_get_top_menu($ClientList);
		$templateVars['pageInput'] .= _getClientForm();

		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Clients";
		$templateVars['pageType'] = "clients";
		$templateVars['pageJavascript'] = "newclient";

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');



	}
	
	// ################## top bar ##################	
	function  _get_top_menu($ClientList)
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>New Client</h1>";
			$entries .="<a href=\"".site_url()."clients/\" class=\"newclient\">Cancel creation of new client</a></h3>\n";
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";			
			$entries .=$this->_clientscontrol($ClientList);

			//Add logout button


			$entries .="</div>";
		
			
			return $entries;

	}
	
	
	// ################## clients control ##################	
	function   _clientscontrol($Clientlist)
	{
		$attributes['id'] = 'clientcontrol';
		$clientscontrol= form_open(site_url()."clients/editclient\n",$attributes);

			
			//Clients

				$options=array();
				foreach($Clientlist as $client)
				{
				$options[$client->CompanyName]=$client->CompanyName;
				}
				asort($options);
				$options=array('all'=>"All")+$options;		
				$more = 'id="clientselector" class="selector"';			
				$selected='all';
				$clientscontrol .=form_label('View / edit existing client details::','client');
				$clientscontrol .=form_dropdown('client', $options,$selected ,$more);
				$more = 'id="clientcontrolsubmit" class="clientcontrolsubmit"';			
				$clientscontrol .=form_submit('clientcontrolsubmit', 'Edit',$more);
		$clientscontrol .= form_close()."\n";

		return $clientscontrol;
	
	}
	


	
	
	// ################## Create form ##################	
	function  _getClientForm()
	{

		$attributes = array( 'id' => 'newclient');
		$editClientForm = form_open(site_url().'clients/addclient',$attributes)."\n";
		$editClientForm .="<fieldset class=\"formbuttons\">";
		$ndata = array('name' => 'submit','value' => 'Add client','class' => 'submitButton');
		$editClientForm .= form_submit($ndata)."\n";
		$editClientForm .="</fieldset>";
		
		$subsections = array( 'Billing Address'=>'Billing Address', 'CompanyName'=>'Company Name','Website'=>'Website','VatOther'=>'VAT Registration / Other','Address'=>'Address','ZipCode'=>'ZipCode','City'=>'City','Country'=>'Country','TimeZone'=>'Time Zone',
		'Contact Information'=>'Contact Information', 'ZOWContact'=>'Primary ZOW Contact', 'ClientContact'=>'Primary Client Contact',
		'Billing Info'=>'Billing Info', 'ClientCode'=>'Client Code', 'BilledBy'=>'Billed by', 'Currency'=>'Currency','BasePrice'=>'Base Price','VolDiscount1Trigger'=>'Vol. Disc. 1 at','VolDiscount1Price'=>'Vol. Disc. 1 price','VolDiscount2Trigger'=>'Vol. Disc. 1 at','VolDiscount2Price'=>'Vol. Disc. 2 price','VolDiscount3Trigger'=>'Vol. Disc. 3 at','VolDiscount3Price'=>'Vol. Disc. 4 price','VolDiscount4Price'=>'Vol. Disc. 4 price','PriceEdits'=>'Edits Price','RetainerHours'=>'Retainer Hours','RetainerCycle'=>'Retainer Cycle',
		'Process Information'=>'Process Information', 'OfficeVersion'=>'Office Version','CustomApps'=>'Custom Apps','ClientDir'=>'Client Directory','ClientGuidelines'=>'Client Guidelines','ZOWGuidelines'=>'ZOW Guidelines','OtherGuidelines'=>'Other Info',
		);

		foreach ($subsections as $key=>$value){
			//Headers
			$Headers = array("Billing Address",'Billing Info','Contact Information','Process Information');
			if (in_array($key,$Headers)) {
				$editClientForm .="<h4>".$key."</h4>\n";
			}else{
				$editClientForm .="<fieldset>\n";
				$editClientForm .= form_label($value.":",$key);
				//Textboxes
				$textboxes = array("Address","VatOther","CustomApps","ClientGuidelines","ZOWGuidelines","OtherGuidelines",);
				if (in_array($key,$textboxes)) {
					$ndata = array('name' => $key, 'id' => $key, 'rows' => '4', 'cols' => '25');
					$editClientForm .= form_textarea($ndata)."\n";
					}
				else if ($key=='ZOWContact') {
					$options = array(''=>'','Miguel' => 'Miguel', 'Paul'=>'Paul', 'Sunil'=>'Sunil');
					$more = 'id="ZOWContact" class="ZOWContact"';	
							$Owner_em= $this->session->userdata('user_email');
							$Owner_na= explode("@", $Owner_em);
							$Owner_fi= explode(".", $Owner_na[0]);
							if ($Owner_fi[0]) {
								$Owner= ucfirst ($Owner_fi[0]);
							}
							else {
								$Owner= ucfirst ($Owner_na[0]);
							}
					
					$editClientForm .=form_dropdown('ZOWContact', $options,$Owner,$more);
					}
				else if ($key=='TimeZone') {
					$editClientForm .= TimeZoneDropDown();

					}
				else if ($key=='TimeZone') {
					$options = array(''=>'','Miguel' => 'Miguel', 'Paul'=>'Paul', 'Sunil'=>'Sunil');
					$more = 'id="ZOWContact" class="ZOWContact"';	
							$Owner_em= $this->session->userdata('user_email');
							$Owner_na= explode("@", $Owner_em);
							$Owner_fi= explode(".", $Owner_na[0]);
							if ($Owner_fi[0]) {
								$Owner= ucfirst ($Owner_fi[0]);
							}
							else {
								$Owner= ucfirst ($Owner_na[0]);
							}
					
					$editClientForm .=form_dropdown('ZOWContact', $options,$Owner,$more);
					}
				else {
					//Short Inputs
					$input3 = array("TimeZone","ClientCode","Currency",'PricePer0Hours','VolDiscount1Price','VolDiscount2Price','VolDiscount3Price','VolDiscount4Price','VolDiscount1Trigger','VolDiscount2Trigger','VolDiscount3Trigger','VolDiscount4Trigger','PriceEdits','RetainerHours','OfficeVersion',);
					if (in_array($key,$input3)) {
						$size='3';
						}
					
					else {
						//Long Inputs
						$input55 = array('ClientDir',);
						if (in_array($key,$input55)) {
								$size='45';
							}
							//Regular Size Inputs
							else {
								$size='25';
							}
					}
				$ndata = array('name' => $key, 'id' => $key, 'size' => $size);
				$editClientForm .= "\n".form_input($ndata)."\n";
				}
			$editClientForm .="</fieldset>\n";
			}
		}
		$editClientForm .= "<h4>&nbsp;</h4>\n";
		$editClientForm .= form_close()."\n";
		return $editClientForm;
	}


	
	// ################## Create form ##################	
	function  _getClients()
	{
	
		$this->load->model('trakclients', '', TRUE);
		$getentries = $this->trakclients->GetEntry();
		return $getentries;

	}
	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>