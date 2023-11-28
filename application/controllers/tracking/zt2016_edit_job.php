<?php

class Zt2016_edit_job extends MY_Controller {

	
	public function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		
		$this->load->helper(array('form','url','userpermissions','zt2016_tracking','zt2016_entries','zt2016_zowcalendar','zt2016_timezone','zt2016_clients','zt2016_contacts'));

		$this->load->model('zt2016_entries_model', '', TRUE);
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$this->load->model('zt2016_clients_model', '', TRUE);
		$this->load->model('zt2016_users_model', '', TRUE);
		$UsersData = $this->zt2016_users_model->GetUser_ascfname();
		$new_user_arr = array();
// 		$new_user_arr['']='';
		$new_user_arr['']='';
		foreach($UsersData as $user_list){
				$name = ucfirst($user_list->fname);
			
			$new_user_arr[$user_list->user_id]=$name;

		}
//	print_r($new_user_arr);
			
		$templateData['user_array']= $new_user_arr;		
		$templateData['ZOWuser']= _getCurrentUser();	

		
		# Load posted form data, if any		
		if (!empty($_POST)){
		// 	echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// die('top');
			foreach ($_POST as $key => $value) {
				$templateData[$key]= $value;
			}
		}
		
		# Load flash data, if any		
		else{
			
			if($this->session->flashdata('Current_Client')){	
				$templateData['Current_Client']=str_replace("_", " ",  $this->session->flashdata('Current_Client'));
				$templateData['Current_Client']=str_replace("~", "&", $templateData['Current_Client']);
			}


			if ($this->session->flashdata('PastJobsViewType')){
				$this->session->set_flashdata('PastJobsViewType',$this->session->flashdata('PastJobsViewType'));
			}
			if ($this->session->flashdata('PastJobsClient')){
				$this->session->set_flashdata('PastJobsClient',$this->session->flashdata('PastJobsClient'));
			}

			if ($this->session->flashdata('PastJobsOriginator')){
				$this->session->set_flashdata('PastJobsOriginator',$this->session->flashdata('PastJobsOriginator'));
			}

			if ($this->session->flashdata('PastJobsViewType')=="list" && $this->session->flashdata('NumberPastJobs')){
				$this->session->set_flashdata('NumberPastJobs',$this->session->flashdata('NumberPastJobs'));
			}
			if ($this->session->flashdata('PastJobsViewType')=="date" && $this->session->flashdata('PastJobsDate')){
				$this->session->set_flashdata('PastJobsDate',$this->session->flashdata('PastJobsDate'));
			}
			if ($this->session->flashdata('PastJobsViewType')=="calendar" && $this->session->flashdata('CalendarMonth')){
				$this->session->set_flashdata('CalendarMonth',$this->session->flashdata('CalendarMonth'));
			}
			if ($this->session->flashdata('PastJobsViewType')=="calendar" && $this->session->flashdata('CalendarYear')){
				$this->session->set_flashdata('CalendarYear',$this->session->flashdata('CalendarYear'));
			}
		}
		
		if(isset($templateData['Current_Contact'])){
			$templateData['Originator']=$templateData['Current_Contact'];	
		}
		
		$templateData['title'] = 'Edit Job';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_edit_entry_page($templateData); 

		$this->load->view('admin_temp/main_temp',$templateData); 


	}
	

	// ################## display page ##################	
	function _display_edit_entry_page( $FormEntryData)
	{

		##########################################		
		################# Load data
		##########################################		
		

			# load entry id from url
			$OriginalDBEntryID=$this->uri->segment(2);
				// No entry in URL
				if (!$OriginalDBEntryID){
					$this->session->set_flashdata('ErrorMessage', 'No entry id provided.');			
					redirect('tracking/zt2016_tracking', 'refresh'); 
				} 		


			#load Database Entry	
			$OriginalDBEntry=$this->zt2016_entries_model-> GetEntry($options = array('id'=>$OriginalDBEntryID));
		
				// No entry found for the ID provided
				if (!$OriginalDBEntry){
					$ErrorMessage='Entry '.$OriginalDBEntryID." not found.";
					$this->session->set_flashdata('ErrorMessage', $ErrorMessage);			
					redirect('tracking/zt2016_tracking', 'refresh'); 

				}

				
			$ZOWuser = $FormEntryData['ZOWuser'];

			# load clients info	
			$ClientsData =  $this->zt2016_clients_model->GetClient();
			# set update page url including entry number
			$formURL='zt2016_update_job/'.$OriginalDBEntry->id;		
		
			### check if the job had been already billed or paid
			#(in which case, its information cannot be edited)

			if ($OriginalDBEntry->Status=="BILLED" || $OriginalDBEntry->Status=="PAID" || $OriginalDBEntry->Status=="MARKETING" || $OriginalDBEntry->Status=="WAIVED") {
				$OriginalDBEntry->BilledPaid=1;
			} else{
				$OriginalDBEntry->BilledPaid=0;
			}

			# load list of timezones
			$TimezonesList = generate_timezone_array();

			# load ZOW staff list
			$ZOWStaff= array ();

			if ($OriginalDBEntry->Status=="COMPLETED" || $OriginalDBEntry->Status=="BILLED" || $OriginalDBEntry->Status=="PAID" || $OriginalDBEntry->Status=="MARKETING" || $OriginalDBEntry->Status=="WAIVED") {

				$ZOWStaffList = $FormEntryData['user_array'];
				
			} else{
				$ZOWStaffList =  $FormEntryData['user_array'];
			}

			foreach ($ZOWStaffList as $key=>$ZOWEntry){

				$ZOWStaff[$key]= $ZOWEntry;

			}		
		
			### Check for client change
			
			# 'Current_Client' is not set on initial load
			if 	(!isset($FormEntryData['Current_Client'])) {
				
				#use existing values as active
				$ActiveEntryClient = $OriginalDBEntry->Client; 
				$ActiveEntryContact= $OriginalDBEntry->Originator;
			}
			else {
				
				#use submitted form values as active
				$ActiveEntryClient=str_replace("_", " ", $FormEntryData['Current_Client']);
				$ActiveEntryClient=str_replace("~", "&", $ActiveEntryClient);
				
				# only reloaded edit job pages have 'Originator' Value set 
				if (isset($FormEntryData['Originator'])){
					$ActiveEntryContact=$FormEntryData['Originator'];
				} else{
					$ActiveEntryContact= $OriginalDBEntry->Originator;
				}

			}
			
	
			### load entry client info	
			$ActiveEntryClientData =  $this->zt2016_clients_model->GetClient($options = array('CompanyName'=>$ActiveEntryClient));

				# No entry client 
				if (!$ActiveEntryClientData){

					$ErrorMessage='Client '.$ActiveEntryClient." not found.";
					$this->session->set_flashdata('ErrorMessage', $ErrorMessage);

					if (isset($FormEntryData['Current_Client'])){
						$redirecturl='zt2016_edit_job/'.$OriginalDBEntry->id;
					} else {
						$redirecturl='tracking/zt2016_tracking';
					}
					redirect($redirecturl, 'refresh');
				} 

			### load entry client contacts	

			if ($OriginalDBEntry->Status=="COMPLETED" || $OriginalDBEntry->Status=="BILLED" || $OriginalDBEntry->Status=="PAID" || $OriginalDBEntry->Status=="MARKETING" || $OriginalDBEntry->Status=="WAIVED") {
				$EntryContactsData =  $this->zt2016_contacts_model->GetContact($options = array('CompanyName'=>$ActiveEntryClient));
			}					
			else {
				$EntryContactsData =  $this->zt2016_contacts_model->GetContact($options = array('CompanyName'=>$ActiveEntryClient, 'Active'=>1));
			}	
				# No entry client contacts	
				if (!$EntryContactsData){
					if ($OriginalDBEntry->Status=="COMPLETED" || $OriginalDBEntry->Status=="BILLED" || $OriginalDBEntry->Status=="PAID") {
						$ErrorMessage='No contacts found for client '.$OriginalDBEntry->Client;
					} else {
						$ErrorMessage='No active contacts found for client '.$OriginalDBEntry->Client;
					}
					$this->session->set_flashdata('ErrorMessage', $ErrorMessage);
					redirect('tracking/zt2016_tracking', 'refresh'); 
				} 				

			# If client has only 1 contact, use it	
			if (count($EntryContactsData)==1){
				$ActiveEntryOriginatorData=$EntryContactsData[0];
				$ActiveEntryOriginatorData->ActiveEntryClient=$ActiveEntryClient;
			} 
		
			### locate active  contact
			else {
				foreach($EntryContactsData as $Contact)
				{

					if (is_numeric($ActiveEntryContact)){
						if ($Contact->ID==$ActiveEntryContact){
							$ActiveEntryOriginatorData=$Contact;
							$ActiveEntryOriginatorData->ActiveEntryClient=$ActiveEntryClient;
							break;
						}
					}
					else {
						if ($Contact->FirstName." ".$Contact->LastName==$OriginalDBEntry->Originator){
							$ActiveEntryOriginatorData=$Contact;
							$ActiveEntryOriginatorData->ActiveEntryClient=$ActiveEntryClient;
							break;
						}
					}
				}				
			}

				# No entry originator data found 
				if (empty($ActiveEntryOriginatorData)){
						$ActiveEntryOriginatorData=new \stdClass();
						if (isset ($FormEntryData['Current_Client'])){
							$ActiveEntryOriginatorData->ActiveEntryClient=$FormEntryData['Current_Client'];
						}
						else{
							$ActiveEntryOriginatorData->ActiveEntryClient=$OriginalDBEntry->Client;
						}
				} 
			
		
			### Timezone management
		    
			# 'Current_Client' is not set on initial load
			if 	(isset($FormEntryData['Current_Client'])) {

				/*
				if ($OriginalDBEntry->TimeZoneOut != $ActiveEntryClientData->TimeZoneOut
				   || $OriginalDBEntry->TimeZoneIn != $ActiveEntryClientData->TimeZoneIn){
					$TimeZoneChanged=1;
				}
				*/
				# check if originator has changed
				if (isset($FormEntryData['Originator']) && $FormEntryData['Originator']!=$OriginalDBEntry->Originator){
					
					# check if originator's timezone is different from entry's time zone
					if ($OriginalDBEntry->TimeZoneOut!=$ActiveEntryOriginatorData->TimeZone){
						
						$OriginalDBEntry->TimeZoneIn= $ActiveEntryOriginatorData->TimeZone;
						$OriginalDBEntry->TimeZoneOut= $ActiveEntryOriginatorData->TimeZone;
						$DisplayWarningMessage='Time zone changed to match new originator '.$ActiveEntryOriginatorData->FirstName." ".$ActiveEntryOriginatorData->LastName;
					}

				}
				# if orignator not set, check if client change affects timezone
				else if($FormEntryData['Current_Client']!=$OriginalDBEntry->Client){ 

					# check if originator's timezone is different from entry's time zone
					if ($OriginalDBEntry->TimeZoneOut!=$ActiveEntryClientData->TimeZone){
						
						$OriginalDBEntry->TimeZoneIn= $ActiveEntryClientData->TimeZone;
						$OriginalDBEntry->TimeZoneOut= $ActiveEntryClientData->TimeZone;
						$DisplayWarningMessage='Time zone changed to match new client '.$ActiveEntryClientData->CompanyName;
					}					
					
				}
				
				
			} # 'Current_Client' setting
		





	
		##########################################		
		############## Create page
		##########################################		
	
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

		######### Display warning message
		if($this->session->flashdata('WarningMessage') || isset($DisplayWarningMessage)){		
			$page_content.='<div class="alert alert-warning" role="alert" style="">'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Warning:</span>'."\n";
			if($this->session->flashdata('WarningMessage')) {
				$page_content.=$this->session->flashdata('WarningMessage');
			} else{
				$page_content.=$DisplayWarningMessage;
			}
			$page_content.='</div>'."\n";
		}		
		
		######### Top panel
		
		
		####  panel header
		$page_content.='<div class="panel panel-info" id="top-panel" style="margin-top:1em;">'."\n"; 
		
		$page_content.='<div class="panel-heading">'."\n"; 
			
			$page_content.=	"<h4 style='display:inline;'>";
		    
				### Panel title

		
				$page_content.="Edit Job #".$OriginalDBEntry->id." - ";
				$page_content.="<small>";
				
				$SanitizedActiveEntryClient= str_replace(" ", "_", $ActiveEntryClientData->CompanyName); 
				$SanitizedActiveEntryClient=str_replace("&", "~",$SanitizedActiveEntryClient);

		
				$ActiveEntryClient=str_replace(" ", "_", $OriginalDBEntry->Client);
				$ActiveEntryClient=str_replace("&", "~", $ActiveEntryClient);
	
				if (!isset($FormEntryData['Current_Client'])){	
					

					if (isset($ActiveEntryOriginatorData->ID)){	
						$ActiveEntryOriginatorData->FullName= $ActiveEntryOriginatorData->FirstName." ". $ActiveEntryOriginatorData->LastName;
						$page_content.="<a href='".site_url()."contacts/zt2016_contact_info/".$ActiveEntryOriginatorData->ID."'><strong>".$ActiveEntryOriginatorData->FullName."</strong></a> for \n";

					}					
					
					$page_content.="<a href='".site_url()."clients/zt2016_client_info/".$ActiveEntryClient."'><strong>".$OriginalDBEntry->Client." (".$OriginalDBEntry->Code.")</strong></a>\n";
					
					if  (isset($ActiveEntryOriginatorData->ID) && $ActiveEntryOriginatorData->FullName !=$OriginalDBEntry->Originator){
					//if  (isset($FormEntryData['Originator'])) &&$FormEntryData['Originator'] {
						$page_content.="<br /> was ".$OriginalDBEntry->Originator." for ".$OriginalDBEntry->Client." (".$OriginalDBEntry->Code.") ";
						$page_content.="<a href='".site_url()."zt2016_edit_job/".$OriginalDBEntry->id."'>Reset</a>\n";					
					}

				} else { 
					
					if (isset($ActiveEntryOriginatorData->ID)){	
						$ActiveEntryOriginatorData->FullName= $ActiveEntryOriginatorData->FirstName." ". $ActiveEntryOriginatorData->LastName;
						$page_content.="<a href='".site_url()."contacts/zt2016_contact_info/".$ActiveEntryOriginatorData->ID."'>".$ActiveEntryOriginatorData->FullName."</a> for \n";
					}						
					
					$page_content.="<a href='".site_url()."clients/zt2016_client_info/".$SanitizedActiveEntryClient."'>".$ActiveEntryClientData->CompanyName." (".$ActiveEntryClientData->ClientCode.")</a>\n";
					$page_content.="<br /> was ".$OriginalDBEntry->Originator." for ".$OriginalDBEntry->Client." (".$OriginalDBEntry->Code.") ";
					$page_content.="<a href='".site_url()."zt2016_edit_job/".$OriginalDBEntry->id."'>Reset</a>\n";
				}
			
			$page_content.="</small></h4>\n";
		
			//$page_content.="<div class='clearfix'></div>\n";
		
			$page_content.=	"<div style='display:inline;'>";


		
			# Cancel edit button
			$page_content.= '<a href="'.site_url().'tracking/zt2016_tracking'.'" class="btn btn-info btn-sm pull-right cancel-button">Cancel Edit</a>'."\n";

			# Trash job button
			if ($OriginalDBEntry->BilledPaid==0){
				$page_content.='<a href="'.site_url().'tracking/zt2016_job_trash/'.$OriginalDBEntry->id.'" class="btn btn-danger btn-sm pull-right trash-button">Trash Job</a>'."\n";
			}

			# Duplicate job button
			
			$duplicateformURL=site_url()."tracking/zt2016_new_job";
			$attributes = array( 'id' => 'duplicate-job-form', 'style'=>"display:inline-block; margin-right:.5em;", 'class'=>"pull-right");
			$page_content .= form_open($duplicateformURL, $attributes)."\n";
			$page_content.= "<div class=\"row\">";

			#hidden fields
			$page_content .="<fieldset>\n";
			$page_content .=form_hidden('Current_Client', $ActiveEntryOriginatorData->ActiveEntryClient);
			if (isset($ActiveEntryOriginatorData->ID)){
				$page_content .=form_hidden('Current_Contact', $ActiveEntryOriginatorData->ID);
			}
			$page_content .="</fieldset>\n";
			$ndata = array('class' => 'submitButton btn btn-primary btn-xs form-control duplicate-button','value' => 'Duplicate Job','name' => 'JobDuplicateSubmit','id' => 'JobDuplicateSubmit','style'=>"  height:30px;position: relative;left: -17px;" );
			$page_content .= form_submit($ndata)."\n";	
			$page_content.= "</div><!--row close-->\n ";

			$page_content.= form_close()."\n";

		
			$page_content.=	"</div>";
		
			$page_content.="<div class='clearfix'></div>\n"; 
		
			$page_content.="</div><!--panel-heading-->\n";
//ravi
		
		######### panel body
		$page_content.='<div class="panel-body ">'."\n";

		
		## clients & contacts dropdowns
		
		$page_content .="<div class=\"row\" style=\"padding-bottom:2em;\">\n";
		$page_content .="<div class=\"col-sm-12 edit_job_client_btn\" >\n";

		## clients dropdown
		$FormURL="zt2016_edit_job/".$OriginalDBEntry->id;
		
		$page_content.= _display_clients_control($ClientsData,$ActiveEntryClientData,$FormURL);

		
		## contacts dropdown
		$ActiveEntryOriginatorData->BilledPaid= $OriginalDBEntry->BilledPaid;
		$page_content.= _display_contacts_control($EntryContactsData,$ActiveEntryOriginatorData,$FormURL);
		
		$page_content .="</div>\n";
		$page_content .="	</div> <!--row-->\n";
		
		$page_content.="	<div class='row'>\n";		
		$page_content .="<div class=\"col-sm-12\">\n";
		
		
		
		######## display job entry form
		
		$attributes = array( 'id' => 'job-details-form');
		$attributes['class'] = 'editEntry';
		$page_content .= form_open(site_url().$formURL, $attributes)."\n";
		
		#hidden fields
		$page_content .="<fieldset>\n";
		$page_content .=form_hidden('id', $OriginalDBEntry->id);
		//$page_content .=form_hidden('Client', $OriginalDBEntry->Client);
		//$page_content .=form_hidden('Client',$ActiveEntryOriginatorData->ActiveEntryClient);
		$page_content .=form_hidden('Client',$ActiveEntryClientData->CompanyName);
		$page_content .=form_hidden('Code',$ActiveEntryClientData->ClientCode);
		//$page_content .=form_hidden('Originator', $ActiveEntryOriginatorData->ActiveEntryClient);

		if (isset($ActiveEntryOriginatorData->ID)) {
			$page_content .=form_hidden('Originator', $ActiveEntryOriginatorData->ID);
		}
		$page_content .="</fieldset>\n";
		$page_content.= "<div class=\"row\" >\n";

			$page_content.= _generate_Job_Details_Form($OriginalDBEntry, $ClientsData, $ActiveEntryClientData, $EntryContactsData, $ActiveEntryOriginatorData, $TimezonesList, $ZOWStaff);				

			$page_content.=_generate_Additional_Form_Details($OriginalDBEntry,$ActiveEntryClientData,$ActiveEntryOriginatorData);
		$page_content.= "</div>\n";

		$page_content.= form_close()."\n";
		$page_content .="		</div>  <!--col-sm-12-->\n";		
		$page_content .="		</div> <!--row-->\n";
		
		
		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";
		
  		//if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {
  			
  		//}
		
		return $page_content;

	}
	
}

/* End of file zt2016_edit_job.php */
/* Location: ./system/application/controllers/tracking/zt2016_edit_job.php */
?>