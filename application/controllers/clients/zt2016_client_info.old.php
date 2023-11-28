<?php

class Zt2016_client_info extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		//$this->load->helper(array('form','url','clients','general','userpermissions'));
		$this->load->helper(array('form','url','general','userpermissions','zt2016_clients'));
		
	
		$templateData['ZOWuser']= _getCurrentUser();


		$SafeclientName=$this->uri->segment(3);


		 if (empty ($SafeclientName)) {
		 	if ($this->input->post('Current_Client')){
		 		$SafeclientName=$this->input->post('Current_Client');
				if ($SafeclientName == "all") {
					if ($templateData['ZOWuser']=="miguel" ||$templateData['ZOWuser']=="sunil.singal" || $templateData['ZOWuser']=="jirka.blom") {
						redirect('clients/zt2016_clients', 'refresh');
					} else{
						redirect('contacts/zt2016_contacts_search', 'refresh');
					}
				}
				
		 	} else{
					redirect('clients/zt2016_clients', 'refresh');
		 	}
			 
		 }
		$clientName=str_replace("_", " ", $SafeclientName);
		$clientName=str_replace("~", "&", $clientName);

		
		$templateData['title'] = 'Client Information for '.$clientName;

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

		# retrieve current client from db		

		$ClientInfo = $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $clientName));

			if (empty($ClientInfo)){

					$Message= $clientName." not found in clients table.";	
					// prevents unexplained random error  
					// where with existing client flashdata is set and displayed but user is not forwared 
					if ($Message!="favicon.ico not found in clients table."){
						$this->session->set_flashdata('ErrorMessage',$Message);
						
						if ($ZOWuser=="miguel" || $ZOWuser=="sunil.singal" || $ZOWuser=="jirka.blom") {
							redirect('clients/zt2016_clients', 'refresh');
						} else{
							redirect('contacts/zt2016_contacts_search', 'refresh');
						}						
						
					}

			}
			
			# check first iteration date
			//die ($ClientInfo->FirstClientIteration)	;		
			
			if ($ClientInfo->FirstClientIteration=="0" || $ClientInfo->FirstClientIteration=="0000-00-00")
			{
				$this->db->select_min('DateOut');
				$this->db->where('Client',$ClientInfo->CompanyName);
				$FirstClientDateQuery = $this->db->get('zowtrakentries');
			 	
			 	$row = $FirstClientDateQuery->row(); 
			
				if (!empty($row->DateOut)) {
					  $row = $FirstClientDateQuery->row(); 
					  $FirstClientDate= date("d - M - Y",strtotime($row->DateOut));
						
					  #if existing data found update clients table
					  $this->zt2016_clients_model->UpdateClient($options = array('CompanyName' => $clientName,'FirstClientIteration' =>$FirstClientDate));
					  $ClientInfo->FirstClientIteration=$FirstClientDate;
		
				} 
				else {
					$LastClientDate= "NONE";
				}
			}	
		# retrieve active client contacts from db		
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$ActiveClientContacts = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $clientName,'Active' => '1','sortBy' => 'FirstName','sortDirection' => 'Asc'));


		# retrieve ianactive client contacts from db		

		$InactiveClientContacts = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $clientName,'Active' => '0','sortBy' => 'FirstName','sortDirection' => 'Asc'));

		
		#Create page.

		$page_content=$this->_display_page($ClientsTable,$ClientInfo,$ZOWuser,$SafeclientName,$ActiveClientContacts,$InactiveClientContacts);

		return $page_content;


	
	}	


// ################## create page ##################	
	function   _display_page ($ClientsTable,$ClientInfo,$ZOWuser,$SafeclientName,$ActiveClientContacts,$InactiveClientContacts)
	{

		//$page_content ='<div class="page_content">';

		$page_content ="";
		######### client dropdown
		$page_content.=$this->_display_clients_control($ClientsTable,$ClientInfo);

		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$page_content.='<div class="alert alert-success" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}



		########## panel
		$page_content.='<div id="client_info_panel" class="panel panel-default"  style="margin-top:2em;">'."\n";
		
		
		
		########## panel head
		$page_content.='<div class="panel-heading">'."\n";
		
			########## client name, client code
			$page_content.=' <h3>';
			$page_content.= $ClientInfo->CompanyName;
			$page_content.= '<small>';
			$page_content.= ' ('.$ClientInfo->ClientCode.')';
			$page_content.= '</small></h3>'."\n";		
			
				
				##########  buttons
				$page_content.= '<div>'."\n";			
			
					########## invoices button
					if ($ZOWuser=="miguel" || $ZOWuser=="sunil.singal"  || $ZOWuser=="jirka.blom" ) {
						$page_content.='<a href="'.site_url().'invoicing/zt2016_client_invoices/'.$SafeclientName.'" class="btn btn-primary btn-sm">Invoices</a>';
					}

					########## new job button

					$attributes='class="form-inline" id="newjobform" name="newjobform"';
					$formurl=site_url().'zt2016_new_job';

					$page_content.= '<form action="'.$formurl.'" '.$attributes.' method="post" accept-charset="utf-8" style="display:inline;">';
					$page_content.='	<div class="form-group">'."\n";
					$page_content.='		<input type="hidden" id="Current_Client" name="Current_Client" value="'.$SafeclientName.'">'."\n";
					$page_content.='		<input type="submit" id="newjobsubmit" name="newjobsubmit" value="New Job" class="btn btn-sm btn-info" >'."\n";
					$page_content.='	</div>'."\n";
					$page_content.="</form>"."\n";

					########## past jobs button

					$attributes='class="form-inline" id="pastjobsform" name="pastjobsform"';
					$formurl=site_url().'tracking/zt2016_past_jobs';

					$page_content.= '<form action="'.$formurl.'" '.$attributes.' method="post" accept-charset="utf-8" style="display:inline;">';
					$page_content.='	<div class="form-group">'."\n";
					$page_content.='		<input type="hidden" id="PastJobsClient" name="PastJobsClient" value="'.$ClientInfo->CompanyName.'">'."\n";
					$page_content.='		<input type="submit" id="pastjobssubmit" name="pastjobssubmit" value="Past Jobs" class="btn btn-sm btn-default" >'."\n";
					$page_content.='	</div>'."\n";
					$page_content.="</form>"."\n";

					########## Report button
					$page_content.= '<a href="'.site_url().'reports/zt2016_annual_client_figures/'.$SafeclientName.'" class="btn btn-default btn-sm">Report</a>';		

					########## materials button
					$page_content.='<a href="'.site_url().'clients/zt2016_manageclientmaterials/'.$ClientInfo->ClientCode.'" class="btn btn-warning btn-sm">Materials</a>';

					########## Website button
					if($ClientInfo->Website!=""){
						$page_content.= '<a href="'.prep_url($ClientInfo->Website).'" target="_new" class="btn btn-info btn-sm">Website</a>';
					}

					########## new contact button
					if ($ZOWuser=="miguel" || $ZOWuser=="sunil.singal" || $ZOWuser=="jirka.blom") {
						$page_content.='<a href="'.site_url().'contacts/zt2016_contact_new/'.$ClientInfo->ID.'" class="btn btn-danger btn-sm ">New Contact</a>';
					}

					########## edit button
					if ($ZOWuser=="miguel" || $ZOWuser=="sunil.singal" || $ZOWuser=="jirka.blom") {
						$page_content.='<a href="'.site_url().'clients/zt2016_client_edit/'.$SafeclientName.'" class="btn btn-success btn-xs pull-right">Edit</a>';
					}



			$page_content.= '</div><!--//buttons-->'."\n";
		
		$page_content.= '</div><!--//panel-heading-->'."\n";
			
		
		
		
		########## panel body
		$page_content.='<div class="panel-body">'."\n";
		
		
		########## tabs
		$page_content.='<ul class="nav nav-tabs " role="tablist" >'."\n";
		$page_content.='<li role="presentation" class="active"><a href="#client-information" aria-controls="client-information" role="tab" data-toggle="tab">Client Information</a></li>'."\n";
		$page_content.='<li role="presentation"><a href="#client-contacts" aria-controls="client-contacts" role="tab" data-toggle="tab">Client Contacts</a></li>'."\n";
		$page_content.='</ul>'."\n";		
		

		$page_content.='<div class="tab-content">'."\n";
		
		########## tab 1
	  	$page_content.='	<div role="tabpanel" class="tab-pane active" id="client-information">'."\n";
		
		$page_content.='Panel 1';
			########## row 1	
			$page_content.='	<div class="row">'."\n";		
		
				########## col 1	
					$page_content.='			<div class="col-sm-4">'."\n";
					########## pricing info
					$page_content.=$this->_display_basic_information($ClientInfo,$ZOWuser);
					$page_content.='			</div><!--col 4-->'."\n";	
		
				########## col 2	
					$page_content.='			<div class="col-sm-4">'."\n";
					########## location info
					$page_content.=$this->_display_address($ClientInfo);
					$page_content.='			</div><!--col 4-->'."\n";			
		
				########## col 3	
					$page_content.='			<div class="col-sm-4">'."\n";
					########## history info
				 	$page_content.=$this->_display_history_information($ClientInfo);
					$page_content.='			</div><!--col 4-->'."\n";			

	 		$page_content.='</div><!--row--> '."\n";	
	
		
		
			########## row 2	
			$page_content.='	<div class="row">'."\n";		
		
				########## col 1	
					$page_content.='			<div class="col-sm-4">'."\n";
					########## production guidelines
					$page_content.=$this->_display_production_guidelines($ClientInfo);
					$page_content.='			</div><!--col 4-->'."\n";	

				########## col 2	
					$page_content.='			<div class="col-sm-4">'."\n";
					if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal"  || $ZOWuser=="jirka.blom") {
							########## billing guidelines
							$page_content.=$this->_display_billing_guidelines($ClientInfo);

					}
					$page_content.='			</div><!--col 4-->'."\n";			

				########## col 3	
					$page_content.='			<div class="col-sm-4">'."\n";
						########## other comments
						$page_content.=$this->_display_other_comments($ClientInfo);
					$page_content.='			</div><!--col 4-->'."\n";			
		
			$page_content.='</div><!--//row--> '."\n";			
		
		/**/
		$page_content.='	</div><!--//tabpanel--> '."\n";
		
		
		########## tab 2
	  	$page_content.='	<div role="tabpanel" class="tab-pane" id="client-contacts">'."\n";
		
		$page_content.=$this-> _display_client_contacts ($ActiveClientContacts,$InactiveClientContacts);
		$page_content.='</div><!--//tabpanel--> '."\n";
		
		
		$page_content.=' </div><!--// tab-content--> '."\n";
		
		$page_content.='</div>'.'<!-- // class="panel-body" -->'."\n";
		
		 $page_content .='</div>'.'<!-- // class="panel" -->'."\n";

		return $page_content;
	}



// ################## display basic info ##################	
	function   _display_basic_information ($ClientInfo,$ZOWuser)
	{
			
		
		########## basic info
		
		
		$page_content='<h5  class="text-uppercase text-primary">Basic Information</h5>'."\n";
		$page_content.='<table class="table">'."\n";


		if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal"  || $ZOWuser=="jirka.blom" ) {
			########## base price
			$page_content.='	<tr>'."\n";
			$page_content.='	<td>';
			$page_content.='	Base Price</td>'."\n";
			$page_content.='	<td><strong>'.$ClientInfo->BasePrice." ".$ClientInfo->Currency.'</strong></td>' ."\n";
			$page_content.='	</tr>'."\n";

			########## Edits
			$page_content.='	<tr>'."\n";
			$page_content.='	<td>';
			$page_content.='	Edits Price</td>'."\n";
			$page_content.='	<td><strong>X '.$ClientInfo->PriceEdits.'</strong></td>' ."\n";
			$page_content.='	</tr>'."\n";

			########## Payment days
			$page_content.='	<tr>'."\n";
			$page_content.='	<td>';
			$page_content.='	Payment Days</td>'."\n";
			$page_content.='	<td><strong>'.$ClientInfo->PaymentDueDate.'</strong></td>' ."\n";
			$page_content.='	</tr>'."\n";


			########## discounts

			if ($ClientInfo->VolDiscount1Trigger!=0){	

				$page_content.='	<tr>'."\n";
				$page_content.='	<td>';
				$page_content.='	Volume Discount I</td>'."\n";
				$page_content.='	<td><strong>'.$ClientInfo->VolDiscount1Price.' <small>'. $ClientInfo->Currency.'</small> if > '.$ClientInfo->VolDiscount1Trigger.' <small>hours</small></strong></td>' ."\n";
				$page_content.='	</tr>'."\n";


				if ($ClientInfo->VolDiscount2Trigger!=0){

					$page_content.='	<tr>'."\n";
					$page_content.='	<td>';
					$page_content.='	Volume Discount II</td>'."\n";
					$page_content.='	<td><strong>'.$ClientInfo->VolDiscount2Price.' <small>'.$ClientInfo->Currency.'</small> if > '.$ClientInfo->VolDiscount2Trigger.' <small>hours</small></strong></td>' ."\n";
					$page_content.='	</tr>'."\n";

					if ($ClientInfo->VolDiscount3Trigger!=0){

							$page_content.='	<tr>'."\n";
							$page_content.='	<td>';
							$page_content.='	Volume Discount III</td>'."\n";
							$page_content.='	<td><strong>'.$ClientInfo->VolDiscount3Price.' <small>'.$ClientInfo->Currency.'</small> if > '.$ClientInfo->VolDiscount3Trigger.' <small>hours</small></strong></td>' ."\n";
							$page_content.='	</tr>'."\n";

						if ($ClientInfo->VolDiscount4Trigger!=0){

								$page_content.='	<tr>'."\n";
								$page_content.='	<td>';
								$page_content.='	IV</td>'."\n";
								$page_content.='	<td><strong>'.$ClientInfo->VolDiscount2Price.' <small>'.$ClientInfo->Currency.'</small> if > '.$ClientInfo->VolDiscount4Trigger.' <small>hours</small></strong></td>' ."\n";
								$page_content.='	</tr>'."\n";
						}
					}
				}
			}
		
		}
		
		########## group
		if (!empty($ClientInfo->Group)){
			$ClientGroup=$ClientInfo->Group;
		} else{
			$ClientGroup="DEFAULT";
		}
		
		$page_content.='	<tr>'."\n";
			$page_content.='	<td>Group</td>'."\n";	
		
			if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal"  || $ZOWuser=="jirka.blom" ) {				
				$page_content.='	<td><a href="'.site_url().'groups/zt2016_group_info/'.$ClientGroup.'">'.$ClientGroup.'</a></td>'."\n";			
			} else{
				$page_content.='	<td>'.$ClientGroup.'</a></td>'."\n";	
			}
		
			
		$page_content.='	</tr>'."\n";

		##########primary contact
		if (!empty($ClientInfo->ClientContact)){
			$page_content.='	<tr>'."\n";
				$page_content.='	<td>Primary Contact</td>'."\n";		
				$page_content.='	<td>'.$ClientInfo->ClientContact.'</td>'."\n";
			$page_content.='	</tr>'."\n";
		}
		
		
		
		$page_content.='</table>'."\n";
	
		return $page_content;
	}	
	
	
// ################## display location info  ##################	
	function   _display_address ($ClientInfo)
	{
			
		
		########## contact info
		$page_content='<h5  class="text-uppercase text-primary">Location Information</h5>'."\n";
		$page_content.='<table class="table">'."\n";
		
		########## Location
		if ($ClientInfo->Country!=''){
			$client_location=$ClientInfo->Country;
		}
		
		if ($ClientInfo->City!=''){
			if (isset($client_location)){
				$client_location.=", ".$ClientInfo->City;
			} else{
				$client_location=$ClientInfo->City;
			}
			
		}
		if (isset($client_location)){
		
			$page_content.='	<tr>'."\n";
				$page_content.='		<td>Location</td>'."\n";
				$page_content.='		<td>'.$client_location.'</td>' ."\n";
			$page_content.='	</tr>'."\n";		
		}
		########## time zone
		$page_content.='	<tr>'."\n";
		$page_content.='	<td>Date and Time Now</td>'."\n";
		
		$date = new DateTime("now", new DateTimeZone($ClientInfo->TimeZone) );
		
		$page_content.='	<td><strong>'.$date->format('H:i d-M-Y').'</strong><br/><small>('.$ClientInfo->TimeZone.')</small></td>' ."\n";
		$page_content.='	</tr>'."\n";	

		########## address
		if ($ClientInfo->Address!=''){
		
			$page_content.='	<tr>'."\n";
			$page_content.='		<td>Address</td>'."\n";
			$page_content.='		<td class="address">';
			$page_content.=$ClientInfo->Address."<br/>";
			$page_content.='		</td>' ."\n";
			$page_content.='	</tr>'."\n";
		}

		$page_content.='</table>'."\n";
	
	return $page_content;
	}	

// ################## display history information ##################	
	function   _display_history_information ($ClientInfo)
	{
			
		
		########## company name
		$page_content='<h5  class="text-uppercase text-primary"">Basic information</h5>'."\n";
		$page_content.='<table class="table">'."\n";

		########## number of jobs
		$page_content.='	<tr>'."\n";
		$page_content.='	<td>Number of jobs</td>'."\n";
		
		$page_content.='	<td><strong>';
		
		#Number of jobs		
		$this->db->where('Client',$ClientInfo->CompanyName);
		$NumberOfJobs = $this->db->get('zowtrakentries');

		$page_content.=number_format($NumberOfJobs->num_rows); 

		$page_content.='</strong>';


		$page_content.='</td>' ."\n";
		$page_content.='	</tr>'."\n";		

		
		########## last date
		$page_content.='	<tr>'."\n";
		$page_content.='	<td>Last Job</td>'."\n";

		$page_content.='	<td><strong>';
		
			#last client date		
			$this->db->select_max('DateOut');
			$this->db->where('Client',$ClientInfo->CompanyName);
			$LastClientDateQuery = $this->db->get('zowtrakentries');
			
			$row = $LastClientDateQuery->row(); 
			if (!empty($row->DateOut)) 
			{
			  //die ($row->DateOut);
			  $LastClientDate= date("d - M - Y",strtotime($row->DateOut));
			  $difference =$this->_datedifference($LastClientDate);
			  $page_content.=$LastClientDate.'</strong><br/><small>'.$difference.'</small>';
			  
			 // $LastClientDate.= $difference;
			} else{
				$page_content.="NONE</strong>";
			}


		$page_content.='</td>' ."\n";
		$page_content.='	</tr>'."\n";

		
		########## first date
		$page_content.='	<tr>'."\n";
		$page_content.='	<td>First Job</td>'."\n";
		
		$page_content.='	<td><strong>';
		
		if ($ClientInfo->FirstClientIteration!="0000-00-00")
		{
			$page_content.=date("d - M - Y",strtotime($ClientInfo->FirstClientIteration));
		}
		else{
			$page_content.="NONE";
		}
		$page_content.='</strong>';

		if ($ClientInfo->FirstClientIteration!="0000-00-00")
			{
			$page_content.='<br/><small>'.$this->_datedifference($ClientInfo->FirstClientIteration).'</small>';
		}
		$page_content.='</td>' ."\n";
		$page_content.='	</tr>'."\n";	

		$page_content.='</table>'."\n";
	
		return $page_content;
	}


// ################## display production guidelines ##################	
	function   _display_production_guidelines ($ClientInfo)
	{

		$page_content='<h5  class="text-uppercase text-primary">Production Guidelines</h5>'."\n";
		$page_content.='<div class="well well-sm guidelines">'."\n";

		$page_content.='<table class="table ">'."\n";

		########## custom apps		
		if ($ClientInfo->CustomApps!=""){

			$page_content.='	<tr>'."\n";
			$page_content.='	<td><span class="text-muted">Custom Applications:</span><br/>'."\n";
			$page_content.='	<strong>'.$ClientInfo->CustomApps.'</strong></td>' ."\n";
			$page_content.='	</tr>'."\n";

		}
		
		########## client guidelines		
		if ($ClientInfo->ClientGuidelines!=""){

			$page_content.='	<tr>'."\n";
			$page_content.='	<td><span class="text-muted">Custom Guidelines:</span><br/'."\n";
			$page_content.='	<strong>'.$ClientInfo->ClientGuidelines.'</strong></td>' ."\n";
			$page_content.='	</tr>'."\n";
			
		}
		
		$page_content.='</table>'."\n";
		//$page_content.='</div><!-- well -->'."\n";

		$page_content.='</div><!-- guidelines -->'."\n";

		return $page_content;

	}

// ################## display billing guidelines ##################	
	function   _display_billing_guidelines ($ClientInfo)
	{
		
		########## billing notes
	 		
		$page_content='<h5  class="text-uppercase text-primary">Billing Guidelines</h5>'."\n";
		$page_content.='<div class="well well-sm guidelines">'."\n";

		########## client guidelines		
		if ($ClientInfo->BillingGuidelines!=""){

			$page_content.=$ClientInfo->BillingGuidelines."\n";

		}

		$page_content.='</div><!-- well -->'."\n";

		return $page_content;
	}	

// ################## display other guidelines ##################	
	function   _display_other_comments ($ClientInfo)
	{
		$page_content='<h5  class="text-uppercase text-primary">Other Guidelines</h5>'."\n";
		
		$page_content.="<div class='well guidelines'>"."\n";

			if ($ClientInfo->OtherInfo!=''){
				$page_content.=$ClientInfo->OtherInfo;
			}

		$page_content.='</div><!-- guidelines -->'."\n";
	
		return $page_content;

	}
	
	
// ################## display client contacts ##################	
	function   _display_client_contacts ($ActiveClientContacts,$InactiveClientContacts)
	{
		$page_content='';
		
		########## row 1	
		$page_content.='	<div class="row">'."\n";		

			########## col 1	
			$page_content.='			<div class="col-sm-6">'."\n";		
		
				if ($ActiveClientContacts) {
					$page_content.='<h5  class="text-uppercase text-primary">'.count($ActiveClientContacts).' Active Contacts</h5>'."\n";
					$page_content.=$this->_contacts_table($ActiveClientContacts,'active');
				}else{
					$page_content.='<h5  class="text-uppercase text-primary">No Active Contacts</h5>'."\n";
				}
			
			$page_content.='			</div><!--col 6-->'."\n";
			
			########## col 2	
			$page_content.='			<div class="col-sm-6">'."\n";	
	
				if (!empty($InactiveClientContacts)) {
					$page_content.='<h5  class="text-uppercase text-primary">'.count($InactiveClientContacts).' Inactive Contacts</h5>'."\n";
					$page_content.=$this->_contacts_table($InactiveClientContacts,'inactive');
				}else{
					$page_content.='<h5  class="text-uppercase text-primary">No inactive Contacts</h5>'."\n";
				}		
				return $page_content;
		$page_content.='			</div><!--col 6-->'."\n";
		
		$page_content.='</div><!--//row--> '."\n";		
		

	}
		
	
	// ################## contacts table ##################	
	function   _contacts_table($ContactsData, $status)
	{
		
		
		$ContactsTable ='<table class="table table-striped table-condensed responsive contacts_table" style="width:100%;" id="'.$status.'_contacts_table">'."\n";
		$ContactsTable .="<thead><tr><th data-sortable=\"true\">Name</th><th data-sortable=\"true\">Jobs</th><th data-sortable=\"true\">First Year</th><th data-sortable=\"true\">Last Year</th></tr></thead>\n";
		//$ContactsTable .="<tfoot><tr><th></th><th></th><th data-sortable=\"true\">First Year</th><th data-sortable=\"true\">Last Year</th><th data-sortable=\"true\">Group</th><th data-sortable=\"true\"</th><th data-sortable=\"true\"></th></tr></tfoot>\n";
		$ContactsTable .="<tbody>\n";
		
		foreach($ContactsData as $ContactDetails)
		{

			$SafeClientName=str_replace(" ", "_", $ContactDetails->CompanyName);
			$SafeClientName=str_replace("&", "~", $SafeClientName);
			$contact_name =$ContactDetails->FirstName.' '. $ContactDetails->LastName;
			
			if ($ContactDetails->Active==1) {
				$ContactsTable.= "<tr>";
			} 
			else {
				$ContactsTable.= "<tr class=\"inactive-contact\">";
			}
			
			# Contact name			

			$contact_info_link ='<a href="'.Base_Url().'contacts/zt2016_contact_info/'.$ContactDetails->ID.'">'.$contact_name.'</a>';
			$ContactsTable .= '<td>'.$contact_info_link.'</td>';	

	
			
			
			# Number of jobs			

	
			$array = array('Originator' => $contact_name, 'Client' => $ContactDetails->CompanyName);
			$this->db->where('Originator',$contact_name);
			$this->db->where($array); 
			$ContactEntries = $this->db->get('zowtrakentries');
			
			if (!empty($ContactEntries)){
				$ContactJobs =  $ContactEntries->num_rows();($ContactEntries);				
			} else{
				$ContactJobs =0;
			}
			
			
			$ContactsTable .="<td>".$ContactJobs."</td>\n";
			
			
			
			#first client date	

			$ContactsTable .="<td>".date("Y",strtotime($ContactDetails->FirstContactIteration))."</td>\n";
			
			
			#last client date			

			$this->db->select_max('DateOut');
			$this->db->where('Originator',$contact_name);
			$LastContactDateQuery = $this->db->get('zowtrakentries');
			
			
			if ($LastContactDateQuery->num_rows() > 0)
			{
			   $row = $LastContactDateQuery->row(); 
			   $LastContactDate= date("Y",strtotime($row->DateOut));
			} else{
			
				$LastContactDate= "NONE";
			}
			
			$ContactsTable .="<td>".$LastContactDate."</td>\n";

			

			$ContactsTable .="</tr>\n";
			
		}
		/**/
		
		$ContactsTable .="</tbody>\n";
		$ContactsTable .="</table>\n";
		
		
		return $ContactsTable;
	
	}		
// ################## Difference between dates ##################	

	function   _datedifference($date1)
	{

			$date1 = new DateTime($date1);
			$date2 = new DateTime;
			$interval = $date1->diff($date2);
			
			$difference = " (";
			if  ($interval->y > 0){
				$difference .=	$interval->y . " years";
			}
			
			if  ($interval->y > 0 || $interval->m > 0){
				if ($interval->y > 0){
					$difference .=", ";
				}
				$difference .=	$interval->m." months";
			}
			if  ($interval->y == 0 && $interval->m == 0){

				$difference .=	$interval->d." days";
			}

			$difference .= " ago)";
			//$page_content.="difference " . $interval->y . " years, " . $interval->m." months, ".$interval->d." days ";

			return $difference;
	}		

// ################## clients control ##################	
	function   _display_clients_control($ClientsTable,$ClientInfo)
	{
		
		#top client dropdown
		$FormInfo['FormURL']="clients/zt2016_client_info";
		$FormInfo['labeltext']= 'Company';
		$FormInfo['id'] = 'client_dropdown_form';
		$FormInfo['class'] = 'form-inline';
	
		$clients_top_dropdown=zt2016_create_clientselector($ClientsTable,$ClientInfo,$FormInfo)."\n";

		return $clients_top_dropdown;
	
	}

}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>