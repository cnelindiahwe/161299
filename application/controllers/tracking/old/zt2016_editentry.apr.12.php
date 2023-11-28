<?php

class Zt2016_editentry extends MY_Controller {

	
	public function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		
		//$this->load->helper(array('form','url','clients','general','userpermissions'));
		
		$this->load->helper(array('form','url','userpermissions','zt2016_tracking','zt2016_zowcalendar','zt2016_timezone'));
		
		//$this->load->helper(array('form','url','clients','general','userpermissions'));

		$this->load->model('zt2016_entries_model', '', TRUE);
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$this->load->model('zt2016_clients_model', '', TRUE);

		//$zowuser=_superuseronly(); 		
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		$templateData['title'] = 'Edit entry';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_edit_entry_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData); 


	}
	

	// ################## display tracking page ##################	
	function _display_edit_entry_page($ZOWuser)
	{
	

		# load clients info	
		$ClientsData =  $this->zt2016_clients_model->GetClient();
		
		# load entry data
		$ActiveEntryID=$this->uri->segment(2);
		
		// No entry in URL
		if (!$ActiveEntryID){
			$this->session->set_flashdata('ErrorMessage', 'No rentry provided.');			
			redirect('tracking/zt2016_tracking', 'refresh'); 
		} 		


		#load Active Entry	
		$ActiveEntry=$this->zt2016_entries_model-> GetEntry($options = array('id'=>$ActiveEntryID));

		// No entry found for the ID provided
		if (!$ActiveEntry){
			$ErrorMessage='Entry '.$ActiveEntryID." not found.";
			$this->session->set_flashdata('ErrorMessage', $ErrorMessage);			
			redirect('tracking/zt2016_tracking', 'refresh'); 
								
		} 				
		
		#load entry client info	
		$EntryClientData =  $this->zt2016_clients_model->GetClient($options = array('CompanyName'=>$ActiveEntry->Client));

		
		#load Active Client's Contacts	
		$ActiveEntry=$this->zt2016_entries_model-> GetEntry($options = array('id'=>$ActiveEntryID));

		
		// No entry client 
		if (!$ActiveEntry){
			$ErrorMessage='Client '.$ActiveEntry->Client." not found.";
			$this->session->set_flashdata('ErrorMessage', $ErrorMessage);			
			redirect('tracking/zt2016_tracking', 'refresh'); 
								
		} 
		
		#load entry client contacts	
		$EntryContactsData =  $this->zt2016_contacts_model->GetContact($options = array('CompanyName'=>$ActiveEntry->Client));


		// No entry client contacts	
		if (!$EntryContactsData){
			$ErrorMessage='No contacts found for client '.$ActiveEntry->Client." not found.";
			$this->session->set_flashdata('ErrorMessage', $ErrorMessage);			
			redirect('tracking/zt2016_tracking', 'refresh'); 
			
		} 
				
		
		foreach($EntryContactsData as $Contact)
		{
			if ($Contact->FirstName." ".$Contact->LastName==$ActiveEntry->Originator){
				$EntryOriginatorData=$Contact;
				break;
			}
		}

		// No entry originator data foubnd
		if (!$EntryOriginatorData){
			$ErrorMessage=$currententry->Originator. ' not found as contact for client '.$ActiveEntry->Client.".";
			$this->session->set_flashdata('ErrorMessage', $ErrorMessage);			
			redirect('tracking/zt2016_tracking', 'refresh'); 
			
		} 	
		$TimezonesList = generate_timezone_array();
	
		
		######################################## Create page
	
		$page_content ="\n";
		
		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			$page_content.='<div class="alert alert-success" role="alert" style="">'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			$page_content.='<div class="alert alert-danger" role="alert" style="">'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}
		
	
		######### Top panel
		
		######### panel header
		$page_content.='<div class="panel panel-info" id="top-panel"><div class="panel-heading">'."\n"; 
			
			$page_content.=	"<h4>";
		    
			#### Entries summary
			$page_content.="Job ID ".$ActiveEntry->id." - ".$ActiveEntry->Code;
		
		
			#### Cancel edit button
			$page_content.= '<a href="'.site_url().'tracking/zt2016_tracking'.'" class="btn btn-info btn-sm pull-right">Cancel Edit</a>'."\n";


			$page_content.="</h4>\n";
			$page_content.="<div class='clearfix'></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body ">'."\n";
		
		

		#display job entry form
		$optionscall  = array('clientinfo' => $EntryClientData,'contactsinfo' => $EntryContactsData,'contactinfo' =>$EntryOriginatorData);
		$page_content.=$this-> _Display_Job_Entry_Form($ActiveEntry,$ClientsData,$TimezonesList, $optionscall);

		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";
		
				
		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";

  		//if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {
  			
  		//}

		return $page_content;

	}	

	
	
	// ------------------------------------------------------------------------
/**
 *  _getEntryForm
 *
 * Displays entry form
 *
 * @access	public
 * @return	string
 */


	function  _Display_Job_Entry_Form($current='',$ClientsData,$TimezonesList, $optionscall = array())
	{
		if (isset($current->id)) {
			if ($current->Status=="BILLED" || $current->Status=="PAID") {
				$BilledPaid=1;
			}
		}

		$attributes = array( 'id' => 'entryForm');
		if (isset($current->id)){
			$attributes['class'] = 'editEntry';
			$entryForm = form_open(site_url().'updateentry/'.$current->id, $attributes)."\n";
			$entryForm .="<fieldset>\n";
			$entryForm .="<input type='hidden' name='id' id='id' value='".$current->id."'/>\n";
			$entryForm .="<input type='hidden' name='clientcode' id='clientcode' value='".$current->Code."'/>\n";
			$entryForm .="</fieldset>\n";
			

			$subsections = array( 'Status'=>'Status','NewSlides'=>'New','EditedSlides'=>'Edits','Hours'=>'Hours','DateOut'=>'Date Out','TimeOut'=>'Time Out','TimeZoneOut'=>'Time Zone Out','Client'=>'Client','Originator'=>'Originator','WorkType'=>'Work Type','FileName'=>'FileName','DateIn'=>'Date In','TimeIn'=>'Time In','TimeZoneIn'=>'Time Zone In','ProjectName'=>'Project Name','RealTime'=>'Real Time','TentativeBy'=>'Tentative','ScheduledBy'=>'Scheduled','WorkedBy'=>'Worked','ProofedBy'=>'Proofed','CompletedBy'=>'Completed','EntryNotes'=>'Entry Notes','ContactNotes'=>'Contact Notes','ClientNotes'=>'Client Guidelines');
		}
		else {
			$attributes['class'] = 'newEntry';
			$entryForm = form_open(site_url().'addentry',$attributes)."\n";
			$subsections = array( 'Status'=>'Status','Client'=>'Client','Originator'=>'Originator','NewSlides'=>'New','EditedSlides'=>'Edits','Hours'=>'Hours','WorkType'=>'Work Type','DateOut'=>'Date Out','TimeOut'=>'Time Out','TimeZoneOut'=>'Time Zone Out','FileName'=>'FileName','DateIn'=>'Date In','TimeIn'=>'Time In','TimeZoneIn'=>'Time Zone In','ProjectName'=>'Project Name','RealTime'=>'Real Time','TentativeBy'=>'Tentative','ScheduledBy'=>'Scheduled By','WorkedBy'=>'Worked By','ProofedBy'=>'Proofed By','CompletedBy'=>'Completed By','EntryNotes'=>'Entry Notes'
		);
		}
		
		//buttons
		$entryForm .="<fieldset class=\"formbuttons zowtrakui-topbar\">";
		if (isset($current->id) && isset($BilledPaid) ) {
			$entryForm .= "<a href=\"".site_url()."tracking\" class=\"cancelEdit\">Cancel Edit (Cannot edit ".$current->Status." entries)</a>\n";
		}
		else {
			$ndata = array('name' => 'submit','class' => 'submitButton');
			if (isset($current->id)){
				$ndata ['value']='Update Entry';
			}
			else {
				$ndata ['value']='Add Entry';
			}
			$entryForm .= form_submit($ndata)."\n";

			if (isset($current->id)){
				$entryForm .= "<a href=\"".site_url()."trashentry/".$current->id."\" class=\"trashEntry\">Trash Entry</a>\n";
			}
		}		
		$entryForm .="</fieldset>";
		foreach ($subsections as $key=>$value){
			if ($key=="Originator"){
				$entryForm .="<fieldset  class=\"originfield\">\n";
			}
			else if ($key=="NewSlides"){
				$entryForm .="<fieldset  class=\"workhours\">\n<div>\n";;
			}
			else if ($key=="EditedSlides" || $key=="Hours" ){
				$entryForm .="<div>\n";
			}
			else {
				$entryForm .="<fieldset>\n";
			}
			
			if (isset($current->id)) {
					$entryForm .= form_label($value.':',$key);
				} else {
					$skipboxes=array('TentativeBy','ScheduledBy','WorkedBy','ProofedBy','CompletedBy');
					if (!in_array($key,$skipboxes)){
						$entryForm .= form_label($value.':',$key);
					}
				}
				//Regular inputs
			$inputboxes =array( 'TimeIn','TimeOut','Originator','NewSlides','EditedSlides','Hours','RealTime','FileName','ProjectName','TentativeBy','ScheduledBy','WorkedBy','ProofedBy','CompletedBy');
	
			if (in_array($key,$inputboxes)){
				$longboxes =array('Originator','FileName','ProjectName');
				$dateboxes =array('DateIn','DateOut');
				$timeboxes =array('TimeIn','TimeOut');
				$tinyboxes =array('NewSlides','EditedSlides','Hours','RealTime');
				if (in_array($key,$longboxes)){
					$size="25";
				}else if (in_array($key,$timeboxes)) {
					$size="4";
				}else if (in_array($key,$tinyboxes)) {
					$size="1";
				}else  {
					$size="3";
				}
				$ndata = array('name' => $key, 'size' => $size, 'class' => $key);
				
				if ($key=="Client") {
					$ndata['class']="EntryClient";
				}
				if ($key=="Originator") {
					$ndata['class']="Origin Originator";
				}
				else if ($key=="DateIn") {
					$ndata['class']="EntryDate";
				}
				else if ($key=="DateOut") {
					$ndata['class']="Deadline";
				}
				else if ($key=="NewSlides" || $key=="EditedSlides" || $key=="Hours") {
					$ndata['class']="workdone ".$key;
				}
 				else {
					$ndata['class']=$key;
				}
				if (isset($current->id)){
					$ndata['value'] =$current->$key;
				}
				if (isset($current->id)) {
					$entryForm .= form_input($ndata)."\n";
				} else {
					$skipboxes=array('TentativeBy','ScheduledBy','WorkedBy','ProofedBy','CompletedBy');
					if (!in_array($key,$skipboxes)){
						$entryForm .= form_input($ndata)."\n";
					}
				}
			}
			//Date in / date out
			else if ($key=='DateIn' || $key=='DateOut')
			{
				
					$size="8";
					 if ($key=='DateIn' ){
						$inputclass= 'EntryDate';
						}
					 if ($key=='DateOut' ){
						$inputclass= 'Deadline';
						}
					
					$inputclass.= ' '.$key;	
					$ndata = array('name' => $key, 'id' => $key, 'size' => $size, 'class' => $inputclass);
					if (isset($current->id)){
						$ndata['value'] =date( 'd-M-Y',strtotime(str_replace("/","-",$current->$key)));
					}
					$entryForm .= form_input($ndata)."\n";
				
			}
			//Timezones
			else if ($key=='TimeZoneIn' || $key=='TimeZoneOut')
			{
				if (isset($current->id)) {
					//$more = 'id="client_timezone" class="form-control"';
					
					if ($current->$key!='') {
						
						$entryForm .= form_dropdown($current->$key,$TimezonesList,$key);//,$more)
					}
					else {
						if (!empty($optionscall['contactinfo']->TimeZone)){
							$entryForm .= form_dropdown($optionscall['contactinfo']->TimeZone,$key);//,$more)
						}else {
							if (!empty($optionscall['clientinfo']->TimeZone)){
								$entryForm .= form_dropdown($optionscall['clientinfo']->TimeZone,$key);//,$more)
							}else{
								$entryForm .=form_dropdown('',$key);
							}
						}
						
					}
				}
				else {
					$entryForm .=form_dropdown('',$key);		
				}
			}
			//Status
			else if ($key=='Status')
			{
				if (isset($current->id) && isset($BilledPaid) ) {
					$entryForm .="<p>".$current->Status."</p>";
				}
				else {			
					$options = array('TENTATIVE' => 'Tentative','SCHEDULED' => 'Scheduled', 'IN PROGRESS'=>'In Progress', 'IN PROOFING'=>'In Proofing','COMPLETED'=>'Completed');
					$more = 'id="Status" class="Status"';
					if (isset($current->id)) {
						$selected=$current->Status;
					}
					else{
						$selected='SCHEDULED';
					}
					
					$entryForm .=form_dropdown('Status', $options,$selected,$more);
				}
			}			
			//WorkType
			else if ($key=='WorkType')
			{
				$options = array('Office' => 'Office', 'Non-Office'=>'Non-Office');
				$more = 'id="WorkType" class="WorkType"';	
				if (isset($current->id)) {
					$selected=$current->WorkType;
				}
				else{
					$selected='';
				}
				$entryForm .=form_dropdown('WorkType', $options,$selected,$more);
			}			
			//Client
			else if ($key=='Client')
			{
				$options = array(''  => '');
				foreach($ClientsData as $client)
				{
				$options[$client->CompanyName]=$client->CompanyName;
				}
				asort($options);		
				$more = 'id="Client" class="EntryClient Client"';			
				
				if (isset($current->id)) {
					$selected=$current->Client;
				}
				else{
					$selected='';
				}
				$entryForm .=form_dropdown('Client', $options,$selected ,$more);
			}			
			//Entry Notes
			else if ($key=='EntryNotes')
			{
				$ndata = array('name' => 'EntryNotes', 'rows' => '5', 'cols' => '68','class' => 'EntryNotes notesfield',);
				if (isset($current->id)) {
						$ndata['value']=$current->EntryNotes;
					}
				$entryForm .= form_textarea($ndata)."\n";

			}	
			//Client Notes		
			else if ($key=='ClientNotes')
			{
				$ndata = array('name' => 'ClientNotes',  'rows' => '5', 'cols' => '68','class' => 'ClientNotes notesfield',);
				if (isset($current->id)) {
						$ndata['value']=$optionscall['clientinfo']->ClientGuidelines;
					}
				$entryForm .= form_textarea($ndata)."\n";

			}			
			//ZoW Guidelines	
			/*
			else if ($key=='ZOWNotes')
			{
				$ndata = array('name' => 'ZOWNotes',  'rows' => '5', 'cols' => '68','class' => 'ZOWNotes notesfield',);
				if (isset($current->id)) {
						$ndata['value']=$optionscall['clientinfo']->ZOWGuidelines;
					}
				$entryForm .= form_textarea($ndata)."\n";

			}
			*/			
				//Contact Guidelines	
			else if ($key=='ContactNotes')
			{
				$ndata = array('name' => 'ContactNotes',  'rows' => '5', 'cols' => '68','class' => 'ContactNotes notesfield',);
				if (isset($current->id)) {
						$ndata['value']=$optionscall['contactinfo']->Notes;
					}
				$entryForm .= form_textarea($ndata)."\n";

			}			
			//Contact Guidelines	
			else if ($key=='ZOWNotes')
			{
				$ndata = array('name' => 'ClientNotes',  'rows' => '5', 'cols' => '68','class' => 'ClientNotes notesfield',);
				if (isset($current->id)) {
						$ndata['value']=$optionscall['clientinfo']->ZOWGuidelines;
					}
				$entryForm .= form_textarea($ndata)."\n";

			}			

		if ($key=="NewSlides" || $key=="EditedSlides" ){
				$entryForm .="</div>\n";
			}
			else if ($key=="Hours" ){
				$entryForm .="</div>\n</fieldset>\n";
			}
			else {
				$entryForm .="</fieldset>\n";
			}
			
			
	
		}
		

		$entryForm .= form_close()."\n";

		return $entryForm;

	}
}
	
	
	



/* End of file zt2016_tracking.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>