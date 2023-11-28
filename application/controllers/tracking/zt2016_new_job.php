<?php

class Zt2016_new_job extends MY_Controller {

	
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
		$new_user_arr['']='Select Option';
		
		foreach($UsersData as $user_list){
				$name = ucfirst($user_list->fname);
			
			$new_user_arr[$user_list->user_id]=$name;

		}
        
        
		$templateData['user_array']= $new_user_arr;	
		//$zowuser=_superuseronly(); 		
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		$templateData['title'] = 'New Job';
		$templateData['sidebar_content']='sidebar';

		$templateData['main_content'] =$this->_display_edit_entry_page($templateData); 
		$this->load->view('admin_temp/main_temp',$templateData); 
	}
	

	// ################## display page ##################	
	function _display_edit_entry_page($ZOWuser)
	{
		
		# load clients info	
		$ClientsData =  $this->zt2016_clients_model->GetClient();
		
		// Check for client
		$ActiveEntry = new stdClass();
		$ActiveEntry->BilledPaid = 0;
		
		if ($this->input->post('Current_Client')){
			$SafeclientName=$this->input->post('Current_Client');
			$ActiveEntry->CompanyName=str_replace("_", " ", $SafeclientName);
			$ActiveEntry->CompanyName=str_replace("~", "&", $ActiveEntry->CompanyName);
		} else {
			$ActiveEntry->CompanyName = "All";
		}
		
		$ActiveEntry->Status="SCHEDULED";
		if ($ActiveEntry->CompanyName != "All") {

			#load entry client info	
			$EntryClientData =  $this->zt2016_clients_model->GetClient($options = array('CompanyName'=>$ActiveEntry->CompanyName));

		// No entry client 
			if (!$EntryClientData){
				$ErrorMessage='Client '.$ActiveEntryClient." not found.";
				$this->session->set_flashdata('ErrorMessage', $ErrorMessage);			
				redirect('tracking/zt2016_tracking', 'refresh');
			} 

			// Check for contact change
			if ($this->input->post('Current_Contact')){
				$ActiveEntryContactID=$this->input->post('Current_Contact');
			}			
			
			#load entry client contacts	
			$EntryContactsData =  $this->zt2016_contacts_model->GetContact($options = array('CompanyName'=>$ActiveEntry->CompanyName,'Active'=>'1'));

				# No entry client contacts	
				if (!$EntryContactsData){
					$ErrorMessage='No contacts found for client '.$ActiveEntry->Client;
					$this->session->set_flashdata('ErrorMessage', $ErrorMessage);
					redirect('tracking/zt2016_tracking', 'refresh'); 
				} 				
			
			# If client has only 1 contact, use it	
			if (count($EntryContactsData)==1){
				$EntryOriginatorData=$EntryContactsData[0];
				$EntryOriginatorData->ActiveEntryClient=$EntryOriginatorData->CompanyName;
			} 
			
			# if there is an originator set,
			# match it with exisiting client contact	
			elseif (isset ($ActiveEntryContactID)){

				foreach($EntryContactsData as $Contact)
				{
					if ($Contact->ID==$ActiveEntryContactID){
						$EntryOriginatorData=$Contact;
						$EntryOriginatorData->ActiveEntryClient=$EntryOriginatorData->CompanyName;
						break;
					}
				}
			}

			# No entry originator data found 
			if (empty($EntryOriginatorData)){
				if ($this->input->post('Current_Client')){
					$EntryOriginatorData=new \stdClass();
					$EntryOriginatorData->ActiveEntryClient=$this->input->post('Current_Client');	
				} else {
					$ErrorMessage=$currententry->Originator. ' not found as contact for client '.$ActiveEntry->Client.".";
					$this->session->set_flashdata('ErrorMessage', $ErrorMessage);			
					redirect('tracking/zt2016_tracking', 'refresh'); 
				}
			} 	

			else if ( $this->input->post('Current_Contact') &&  $this->input->post('Current_Client')){
					$EntryOriginatorData->ActiveEntryClient=$this->input->post('Current_Client');
			} 			
		}
		
		if (isset($EntryOriginatorData->ID)){ 
			
			$ActiveEntry->Originator= $EntryOriginatorData->FirstName." ".$EntryOriginatorData->LastName;

		}		
		
		$TimezonesList = generate_timezone_array();
		
		$ZOWStaff= array ();
		$ZOWStaff['']='';
		// $ZOWStaffList = array ("","Arpita","Ashish", "Ganesh", "Hiren", "Jemema", "Jirka", "Miguel", "Nainesh", "Nandhinipriya", "Pranjali", "Seemakaur", "Sijo", "Subathra", "Sunil",  "Tarun");
			
		
		// foreach ($ZOWStaffList as $ZOWEntry){
			
		// 	$ZOWStaff[$ZOWEntry]= $ZOWEntry;
			
		// }
		$UsersData = $this->zt2016_users_model->GetUser_ascfname();
		$new_user_arr = array();
		$new_user_arr['']='';
		
		foreach($UsersData as $user_list){
				$name = ucfirst($user_list->fname);
			
			$ZOWStaff[$user_list->user_id]=$name;

		}
		
		
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

		$formURL='zt2016_create_job';

		######### panel header
		$page_content.='<div class="panel panel-info" id="top-panel" style="margin-top:1em;">'."\n"; 
		
		$page_content.='<div class="panel-heading d-flex p-4">'."\n"; 
			
			$page_content.=	"<div class=\"row w-100\"><div class=\"col-sm-10\">";	
				$page_content.=	"<h4>";
		
				#### Panel title
					$page_content.=	"New job";
					$page_content.="<small>";
					if (isset($ActiveEntry->Originator)){
						$page_content.=	" for ";
						$page_content.="<a href='".site_url()."contacts/zt2016_contact_info/".$EntryOriginatorData->ID."'><strong>".$ActiveEntry->Originator."</strong></a>\n";
					}

					if (isset($ActiveEntry->CompanyName) && $ActiveEntry->CompanyName!="All"){

						$page_content.=	" for ";
						$page_content.="<a href='".site_url()."clients/zt2016_client_info/".$this->input->post('Current_Client')."'><strong>".$ActiveEntry->CompanyName."</strong></a>\n";
					}
					$page_content.="</small>";
			
			$page_content.="</h4>\n";
			$page_content.=	"</div>";
		
			# Cancel edit button
			$page_content.=	"<div class=\"col-sm-2\">";
					
			$page_content.= '<a href="'.site_url().'tracking/zt2016_tracking'.'" class="btn btn-info btn-sm pull-right new_job_hsd">Cancel</a>'."\n";			
	
			$page_content.=	"</div></div>";
		
			$page_content.="<div class='clearfix'></div>\n"; 
		
			$page_content.="</div><!--panel-heading-->\n";

		######### panel body
		$page_content.='<div class="panel-body ">'."\n";

		
		## clients & contacts dropdowns
		
		$page_content .="<div class=\"row\" style=\"padding-bottom:2em;\">\n";
		$page_content .="<div class=\"col-sm-12 edit_job_client_btn\">\n";

		## contacts dropdown
		$FormURL="zt2016_new_job";
		$page_content.= _display_clients_control($ClientsData,$ActiveEntry,$FormURL);

		
		
		## contacts dropdown
		if ($ActiveEntry->CompanyName != "All"){
		
			$page_content.= _display_contacts_control($EntryContactsData,$EntryOriginatorData,$FormURL);			
		
			if (isset($EntryOriginatorData->ID)){


				## fill in default values
				
				$ActiveEntry->Originator= $EntryOriginatorData->FirstName." ".$EntryOriginatorData->LastName;
				
				$ActiveEntry->Status="SCHEDULED";
				$ActiveEntry->NewSlides=" ";
				$ActiveEntry->EditedSlides=" ";
				$ActiveEntry->Hours=" ";
				$ActiveEntry->FileName='';
				$ActiveEntry->WorkType="Office";
				
				$DateNow = new DateTime("now", new DateTimeZone($EntryOriginatorData->TimeZone) );
				$ActiveEntry->DateOut=$DateNow->format('Y-m-d');
				$ActiveEntry->DateIn=$DateNow->format('Y-m-d');
				$ActiveEntry->TimeOut=$DateNow->format('H:i');
				$ActiveEntry->TimeIn=$DateNow->format('H:i');
				
				//die ($ActiveEntry->TimeOut);
				
				$ActiveEntry->TimeZoneOut=$EntryOriginatorData->TimeZone;
				$ActiveEntry->TimeZoneIn=$EntryOriginatorData->TimeZone;
				
				$ActiveEntry->EntryNotes="";
				$ActiveEntry->ProjectName="";
				
				#ZOWStaff
				
				// $ZOWUserArray=explode(".",$ZOWuser);
				// $ZOWUserArray=explode(".",$ZOWuser);
				// if (isset($ZOWUserArray[1])){
				// 	if ($ZOWUserArray[1]=="Poojari"){
				// 		$ZOWUser= $ZOWUserArray[1];
				// 	}else{
				// 		$ZOWUser= $ZOWUserArray[0];
				// 	}
				// } else{
				// 	$ZOWUser= $ZOWUserArray[0];
				// }
				$ZOWUser=$this->session->userdata('user_id');//ucwords($ZOWUser);
				// $templateData['user_array']
				$ActiveEntry->TentativeBy=$ZOWUser;
				$ActiveEntry->ScheduledBy=$ZOWUser;


				$ActiveEntry->WorkedBy="";
				$ActiveEntry->ProofedBy="";
				$ActiveEntry->CompletedBy="";
				
				$page_content .="</div>\n";
				$page_content .="	</div> <!--row-->\n";

				//$page_content.="<div class='clearfix'></div>\n";


				$page_content.="	<div class='row'>\n";		
				$page_content .="<div class=\"col-sm-12\">\n";
				#display job entry form

				$attributes = array( 'id' => 'job-details-form');
				$attributes['class'] = 'editEntry';
				$page_content .= form_open(site_url().$formURL, $attributes)."\n";

				$page_content .=form_hidden('Client',$EntryOriginatorData->ActiveEntryClient);
				$page_content .=form_hidden('Code',$EntryClientData->ClientCode);
				$page_content .=form_hidden('Current_Client', $EntryOriginatorData->ActiveEntryClient);
				$page_content .=form_hidden('Originator', $EntryOriginatorData->FirstName." ".$EntryOriginatorData->LastName);
				$page_content .=form_hidden('Originator_id', $EntryOriginatorData->ID);
				$page_content .=form_hidden('ZOWUser', $ZOWUser);
				$page_content .="<div class=\"row\">\n";
						$page_content.= _generate_Job_Details_Form($ActiveEntry, $ClientsData, $EntryClientData, $EntryContactsData, $EntryOriginatorData, $TimezonesList, $ZOWStaff);

						$page_content.=_generate_Additional_Form_Details($ActiveEntry,$EntryClientData,$EntryOriginatorData);

				$page_content.= "</div><!--row close-->\n ";
				$page_content.= form_close()."\n";
				$page_content .="		</div>  <!--col-sm-12-->\n";		
				$page_content .="		</div> <!--row-->\n";

				$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";

				//if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {

				//}			

				
			}

		}
		return $page_content;

	}
	
	// ------------------------------------------------------------------------
/**
 * _display_contact_control
 *
 * Displays entry form
 *
 * @access	public
 * @return	string
 */	
	// ################## contacts control ##################	
	function   _display_originator_dropdown($EntryContactsData,$ContactInfo)
	{
		
		#top client dropdown
		$FormInputInfo['FormURL']="clients/zt2016_client_info";
		$FormInputInfo['type']= 'NoSubmit';
		$FormInputInfo['ID'] = 'contact_dropdown_form';
		$FormInputInfo['class'] = 'form-control';
		$FormInputInfo['required'] = 'yes';
 			
		$clients_top_dropdown=zt2016_contacts_dropdown_control($EntryContactsData,$ContactInfo)."\n";
		

		return $clients_top_dropdown;
	
	}	
	
	
}
	
	
	



/* End of file zt2016_tracking.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>