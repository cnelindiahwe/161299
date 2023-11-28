<?php

class zt2016_trash extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','clients','general','userpermissions'));

		$this->load->model('zt2016_entries_model', '', TRUE);
		$this->load->model('zt2016_clients_model', '', TRUE);
		$this->load->model('zt2016_contacts_model', '', TRUE);
		

		$zowuser=_superuseronly(); 
	
		$templateData['ZOWuser']= _getCurrentUser();
		
		$templateData['title'] = 'Trash';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _display_trash_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData); 


	}

	// ################## create trash page ##################	
	function  _display_trash_page($ZOWuser)
	{	

			#Create page
			//$page_content ='<div class="page_content">';
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
		
		
		$page_content .=$this->_display_trashed_jobs($ZOWuser);
		$page_content .=$this->_display_trashed_clients($ZOWuser);
		$page_content .=$this->_display_trashed_contacts($ZOWuser);
		
		return $page_content;
	}

	
		// ################## display trashed jobs ##################	
		function  _display_trashed_jobs($ZOWuser)
		{


			#load clients info	

			$TrashedJobsData = $this->zt2016_entries_model->GetEntry($options = array('Trash'=>'1','sortBy'=>'Client','sortDirection'=>'ASC	'));

			######### panel header

			$page_content='<div class="panel panel-warning"><div class="panel-heading">'."\n"; 
			$page_content.='<h4>';

			if ($TrashedJobsData) {
				$page_content.=count($TrashedJobsData)." trashed jobs"."\n";
			} else{
				$page_content.="No trashed jobs"."\n";
			}

			
			$page_content.="</h4>\n";
			$page_content.="<div class='clearfix'></div>\n";
			$page_content.="</div><!--panel-heading-->\n";


			######### panel body
			$page_content.='<div class="panel-body">'."\n";
			$page_content.='<div class="table_loading_message">Loading ... </div>'."\n";


			#fetch clients table
			if ($TrashedJobsData) {
				$page_content .= $this-> _trashed_jobs_table($TrashedJobsData)	;		
			}

			$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";


			if ($ZOWuser=="miguel" || $ZOWuser=="sunil.singal") {


			}


			return $page_content;



		}	
	
		// ################## create jobs table ##################	
		function  _trashed_jobs_table($TrashedJobsData)
		{

			
			#load trashes jobs

			
			$JobsTable ='<table class="table table-striped table-condensed responsive table-results" style="width:100%;display:none;" id="trashed_jobs_table">'."\n";
			$JobsTable .="<thead><tr>
			<th data-sortable=\"true\">Status</th>
			<th data-sortable=\"true\">Client</th>
			<th data-sortable=\"true\">Date</th>
			<th data-sortable=\"true\">Originator</th>
			<th data-sortable=\"true\">New</th>
			<th data-sortable=\"true\">Edits</th>
			<th data-sortable=\"true\">Hours</th>			
			
			<th></th><th</th></tr></thead>\n";
			//$JobsTable .="<tfoot><tr><th data-sortable=\"true\">Client</th><th>Restore</th><th>Delete</th></tr></tfoot>\n";		
			$JobsTable .="<tbody>\n";

			foreach($TrashedJobsData as $TrashedJob)
			{
				$JobsTable .="<tr>\n";

				$SafeClientName=str_replace(" ", "_", $TrashedJob->Client);
				$SafeClientName=str_replace(" ", "_", $TrashedJob->Client);
				$SafeClientName=str_replace("&", "~", $SafeClientName);			

				$JobsTable .='<td>'.$TrashedJob->Status.'</td>'."\n";
				$JobsTable .='<td>'.$TrashedJob->Client.'</td>'."\n";
				$JobsTable .='<td>'.date('j-M-Y', strtotime($TrashedJob->DateIn)).'</td>'."\n";
				$JobsTable .='<td>'.$TrashedJob->Originator.'</td>'."\n";
				$JobsTable .='<td>'.$TrashedJob->NewSlides.'</td>'."\n";
				$JobsTable .='<td>'.$TrashedJob->EditedSlides.'</td>'."\n";
				$JobsTable .='<td>'.$TrashedJob->Hours.'</td>'."\n";
				$JobsTable .='<td >'.$TrashedJob->FileName.'</td>'."\n";
				
				
				$JobsTable .='<td> <a href="'.site_url().'tracking/zt2016_job_restore/'.$TrashedJob->id.'" class="btn btn-success btn-xs" id="RestoreJob'.$TrashedJob->id.'">Restore</a></td>'."\n";
				$JobsTable .='<td> <a href="'.site_url().'tracking/zt2016_job_delete/'.$TrashedJob->id.'" class="btn btn-danger btn-xs btn-delete" id="DeleteJob'.$TrashedJob->id.'">Delete</a></td>'."\n";

				$JobsTable .="</tr>\n";

			}
				$JobsTable .="</tbody>\n";
			$JobsTable .="</table>\n";		
			return $JobsTable;
		}
	
	// ################## display trashed clients info ##################	
		function  _display_trashed_clients($ZOWuser)
		{


			#load clients info	

			$ClientsData = $this->zt2016_clients_model->GetClient($options = array('Trash'=>'1','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));

			######### panel header

			$page_content='<div class="panel panel-info"><div class="panel-heading">'."\n"; 
			$page_content.='<h4>';

			if ($ClientsData) {
				$page_content.=count($ClientsData)." trashed clients"."\n";
			} else{
				$page_content.="No trashed clients"."\n";
			}

			
			########## View clients button
			$page_content.= '<a href="'.site_url().'clients/zt2016_clients'.'" class="btn btn-info btn-sm pull-right">View Clients</a>'."\n";

			$page_content.="</h4>\n";
			$page_content.="<div class='clearfix'></div>\n";
			$page_content.="</div><!--panel-heading-->\n";


			######### panel body
			$page_content.='<div class="panel-body">'."\n";
			$page_content.='<div class="table_loading_message">Loading ... </div>'."\n";


			#fetch clients table
			if ($ClientsData) {
				$page_content .= $this-> _trashed_clients_table($ClientsData)	;		
			}

			$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";


			if ($ZOWuser=="miguel" || $ZOWuser=="sunil.singal") {


			}


			return $page_content;


			$JobsTable .="</tbody>\n";
			$JobsTable .="</table>\n";

			return $JobsTable;


		}


		// ################## create client table ##################	
		function  _trashed_clients_table($ClientsData)
		{

			$ClientsTable ='<table class="table table-striped table-condensed responsive table-results" style="width:100%;display:none;" id="trashed_clients_table">'."\n";
			$ClientsTable .="<thead><tr><th data-sortable=\"true\">Client</th><th></th><th</th></tr></thead>\n";
			//$ClientsTable .="<tfoot><tr><th data-sortable=\"true\">Client</th><th>Restore</th><th>Delete</th></tr></tfoot>\n";		
			$ClientsTable .="<tbody>\n";


			foreach($ClientsData as $ClientDetails)
			{
				$ClientsTable .="<tr>\n";

				$SafeClientName=str_replace(" ", "_", $ClientDetails->CompanyName);
				$SafeClientName=str_replace("&", "~", $SafeClientName);			

				$ClientsTable .='<td><strong>'.$ClientDetails->CompanyName.'</strong></td>'."\n";
				$ClientsTable .='<td> <a href="'.site_url().'clients/zt2016_client_restore/'.$ClientDetails->ID.'" class="btn btn-success btn-xs" id="Restore'.$ClientDetails->CompanyName.'">Restore</a></td>'."\n";
				$ClientsTable .='<td> <a href="'.site_url().'clients/zt2016_client_delete/'.$ClientDetails->ID.'" class="btn btn-danger btn-xs btn-delete" id="Delete'.$ClientDetails->CompanyName.'">Delete</a></td>'."\n";

				$ClientsTable .="</tr>\n";

			}


			$ClientsTable .="</tbody>\n";
			$ClientsTable .="</table>\n";



			return $ClientsTable;

		}


		// ################## display trashed clients info ##################	
		function  _display_trashed_contacts($ZOWuser)
		{


			#load contacts info	

			$ContactsData = $this->zt2016_contacts_model->GetContact($options = array('Trash'=>'1','sortBy'=>'FirstName','sortDirection'=>'ASC	'));

			######### panel header

			$page_content='<div class="panel panel-success"><div class="panel-heading">'."\n"; 
			$page_content.='<h4>';

			if ($ContactsData) {
				$page_content.=count($ContactsData)." trashed contacts"."\n";
			} else{
				$page_content.="No trashed contacts"."\n";
			}

			########## View clients button
			$page_content.= '<a href="'.site_url().'contacts/zt2016_contacts'.'" class="btn btn-success btn-sm pull-right">View Contacts</a>'."\n";

			$page_content.="</h4>\n";
			$page_content.="<div class='clearfix'></div>\n";
			$page_content.="</div><!--panel-heading-->\n";


			######### panel body
			$page_content.='<div class="panel-body">'."\n";
			$page_content.='<div class="table_loading_message">Loading ... </div>'."\n";


			#fetch clients table
			if ($ContactsData) {
				$page_content .= $this-> _trashed_contacts_table($ContactsData)	;		
			}

			$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";


			if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {


			}


			return $page_content;



		}	


		// ################## create client table ##################	
		function  _trashed_contacts_table($ContactsData)
		{

			$ContactsTable ='<table class="table table-striped table-condensed responsive table-results" style="width:100%;display:none;" id="trashed_clients_table">'."\n";
			$ContactsTable .="<thead><tr><th data-sortable=\"true\">Contact</th><th data-sortable=\"true\">Company</th><th></th><th></th></tr></thead>\n";
			//$ClientsTable .="<tfoot><tr><th data-sortable=\"true\">Client</th><th>Restore</th><th>Delete</th></tr></tfoot>\n";		
			$ContactsTable .="<tbody>\n";


			foreach($ContactsData as $ContactDetails)
			{
				$ContactsTable .="<tr>\n";

				$Contact_Full_Name=$ContactDetails->FirstName.' '.$ContactDetails->LastName;		

				$ContactsTable .='<td><strong>'.$Contact_Full_Name.'</strong></td>'."\n";
				
				$ContactsTable .='<td>'.$ContactDetails->CompanyName.'</td>'."\n";
				$ContactsTable .='<td> <a href="'.site_url().'contacts/zt2016_contact_restore/'.$ContactDetails->ID.'" class="btn btn-success btn-xs" id="Restore'.$Contact_Full_Name.'">Restore</a></td>'."\n";
				$ContactsTable .='<td> <a href="'.site_url().'contacts/zt2016_contact_delete/'.$ContactDetails->ID.'" class="btn btn-danger btn-xs btn-delete" id="Delete'.$Contact_Full_Name.'">Delete</a></td>'."\n";

				$ContactsTable .="</tr>\n";

			}


			$ContactsTable .="</tbody>\n";
			$ContactsTable .="</table>\n";




			return $ContactsTable;

		}


}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>