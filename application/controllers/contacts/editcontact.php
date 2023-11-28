<?php

class Editcontact extends MY_Controller {


	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('form','url','clients','contacts','general','userpermissions', 'url'));

		$templateVars['ZOWuser']=_superuseronly(); 
		
		
		$templateVars['current'] =$this->uri->segment(3);
		
		$this->load->model('trakcontacts', '', TRUE);
		$CurrentContact  = $this->trakcontacts->GetEntry($options = array('ID'=>$templateVars['current']));

		$this->load->model('trakclients', '', TRUE);
		$ClientList = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		foreach($ClientList  as $client)
		{
			if ($client->CompanyName==$CurrentContact->CompanyName  ){
				$CurrentClient=$client;
			}
		}	
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageInput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageInput'] .= $this->	_getContactpage($ClientList,$CurrentClient);	
		$templateVars['pageInput'] .= _displayClientContactsTable($CurrentClient);
		$templateVars['pageInput'].= _getContactsForm($ClientList,$CurrentContact,$CurrentClient);

		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageType'] = "contacts";
		$templateVars['pageName'] = "Edit contact";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');
		

	}
	
	// ################## top ##################	
	function  _getContactpage($ClientList,$CurrentClient)
	{
			$this->load->model('trakcontacts', '', TRUE);
			$ContactList = $this->trakcontacts->GetEntry($options = array('Trash'=>'0','CompanyName'=>$CurrentClient->CompanyName));

			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>".count($ContactList)." Contacts</h1>";
						$entries .= _displayClientContactsDropdown($ClientList,$CurrentClient->ID);	
						
			//Add cancel button
			$entries .="<a href=\"http://localhost/mysites/ZOWtrak2012/contacts\" class=\"cancelEdit\">Cancel Edit</a>";
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
			return $entries;

	}
	
	
	function  _getCurrent()
	{
			$this->load->model('trakclients', '', TRUE);
			$currententry = $this->trakclients->GetEntry($options = array('ID' => $this->uri->segment(3)));
			if($currententry)
				{
				return $currententry;
				}
			else {
			 	echo 'There was a problem retrieving the current entry';
			}
	}



	// ################## Create form ##################	
	function  _getClientForm($current)
	{
	
		$attributes = array( 'id' => 'newclient');
		$editClientForm = form_open(site_url().'clients/updateclient/'.$current->ID,$attributes)."\n";
		$editClientForm .="<fieldset class=\"formbuttons\">";
		$ndata = array('name' => 'submit','value' => 'Update Client','class' => 'submitButton');
		$editClientForm .= form_submit($ndata)."\n";
		$editClientForm .= "<a href=\"".site_url()."clients/trashentry/".$current->ID."\" class=\"cancelEdit\">Trash Client</a>\n";
		$editClientForm .= "<a href=\"".site_url()."clients\" class=\"cancelEdit\">Cancel Edit</a>\n";		
		
		$editClientForm .="</fieldset>";
		
		$subsections = array( 'Billing Address'=>'Billing Address', 'CompanyName'=>'Company Name','VATOther'=>'VAT Registration / Other','Address'=>'Address','ZIPCode'=>'ZipCode','City'=>'City','Country'=>'Country','TimeZone'=>'Time Zone','Website'=>'Website',
		'Contact Information'=>'Contact Information', 'ZOWContact'=>'Primary ZOW Contact', 'ClientContact'=>'Primary Client Contact',
		'Billing Info'=>'Billing Info', 'ClientCode'=>'Client Code', 'BilledBy'=>'Billed by', 'Currency'=>'Currency','BasePrice'=>'Base Price','VolDiscount1Trigger'=>'Vol. Disc. 1 at','VolDiscount1Price'=>'Vol. Disc. 1 price','VolDiscount2Trigger'=>'Vol. Disc. 2 at','VolDiscount2Price'=>'Vol. Disc. 2 price','VolDiscount3Trigger'=>'Vol. Disc. 3 at','VolDiscount3Price'=>'Vol. Disc. 3 price','VolDiscount4Trigger'=>'Vol. Disc. 4 at','VolDiscount4Price'=>'Vol. Disc. 4 price','PriceEdits'=>'Edits Price','RetainerHours'=>'Retainer Hours','RetainerCycle'=>'Retainer Cycle',
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
				$textboxes = array("Address","VATOther","CustomApps","ClientGuidelines","ZOWGuidelines","OtherGuidelines",);
				if (in_array($key,$textboxes)) {
					$ndata = array('name' => $key, 'id' => $key, 'rows' => '4', 'cols' => '25', 'value' => $current->$key);
					$editClientForm .= form_textarea($ndata)."\n";
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
				$ndata = array('name' => $key, 'id' => $key, 'size' => $size, 'value' => $current->$key);
				$editClientForm .= "\n".form_input($ndata)."\n";
				}
			$editClientForm .="</fieldset>\n";
			}
		}
		$editClientForm .= "<h4>&nbsp;</h4>\n";
		$editClientForm .= form_close()."\n";
		return $editClientForm;
	}



}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>