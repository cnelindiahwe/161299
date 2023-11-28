<?php

class Active_clients extends MY_Controller {

	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('clients','general','form','userpermissions', 'url'));
		
		$templateVars['ZOWuser']=_superuseronly(); 
		

		$this->load->model('trakclients', '', TRUE);
		$ClientList = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
	
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageOutput'] .= $this->_get_top_menu($ClientList,1);
		$templateVars['pageOutput'] .=$this->_non_repeating_clients ($ClientList);	
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Clients";
		$templateVars['pageType'] = "clients";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}

	
	// ################## top bar ##################	
	function  _get_top_menu($ClientList,$ViewType)
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			
			$entries .=$this->_clientscontrol($ClientList);
			if ($ViewType==1) { $entries .="<a href=\"".site_url()."clients/newclient\" class=\"newclient\">Create New Client</a></h3>\n";}
			else if ($ViewType==2) {$entries .="<a href=\"".site_url()."clients/\" class=\"newclient\">Cancel Edit</a></h3>\n";}
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

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
				$clientscontrol .=form_label('Client:','client');
				$clientscontrol .=form_dropdown('client', $options,$selected ,$more);
				$more = 'id="clientcontrolsubmit" class="clientcontrolsubmit"';			
				$clientscontrol .=form_submit('clientcontrolsubmit', 'Edit',$more);
		$clientscontrol .= form_close()."\n";

		return $clientscontrol;
	
	}
	
		// ################## clients control ##################	
	function   _non_repeating_clients ($ClientList)
	{

		$query = "SELECT Client,  MAX(DateOut) AS Firstdate  FROM zowtrakentries WHERE Trash = 0 GROUP BY Client ORDER BY Firstdate DESC ";
		$rawentries =$this->db->query($query);
		$getentries=$rawentries->result();
		$Prevyear=date( "Y",strtotime('now'));
		$clientlist_byage="";
		$yearcount=0;
		$clientlist_byage="";
		$currentyear=date('Y');
		$clientlist_running="";
				foreach($getentries as $client)
				{
					$Firstyear=date( "Y",strtotime($client->Firstdate));
					$Firstdate=date( "F Y",strtotime($client->Firstdate));
					$yearcount++;					
					if ($Firstyear!=$Prevyear) {
						if ($Prevyear!=$currentyear){
							$qualifier="non-repeating";
						}else {
							$qualifier="active";
							$currentyearcount=$yearcount;
						}
						$clientlist_byage.="<div class=\"yearpile\"><h3>".$Prevyear.": ".$yearcount." ".$qualifier." clients</h3>";						
						$clientlist_byage.=$clientlist_running;
						$clientlist_byage.="</div><div class=\"yearpile\">";
						$yearcount=0;
						$clientlist_running="";
					}
					$safeclient=str_replace("&","~",$client->Client);
					$clientlist_running.="<strong><a href=\"clients/editclient/".$safeclient."\">".$client->Client."</strong></a> (".$Firstdate.")<br/>";
					$Prevyear=$Firstyear;
				}
				$clientlist_byage.="<div class=\"yearpile\"><h3>".$Prevyear.": ".$yearcount." non-repeating clients</h3>";						
				$clientlist_byage.=$clientlist_running;
				$clientlist_byage.="</div><div class=\"yearpile\">";
				$yearcount=0;
				$clientlist_running="";
				$clientlist_byage.="</div>";
				$clientlist_byage.="</div><!--content-->";
				

				$clientlist_header="<div class=\"content\">";
				$clientlist_header.="<h3>".$currentyearcount." Clients in ".$currentyear;
				$clientlist_header.=" (".count($ClientList)." Total Clients)</h3>";				
				$clientlist_byage=$clientlist_header.$clientlist_byage;
		return $clientlist_byage;
	
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