<?php

class Zt2016_tracking extends MY_Controller {

	
	public function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		
		//$this->load->helper(array('form','url','clients','general','userpermissions'));
		
		$this->load->helper(array('userpermissions','zt2016_tracking'));
		
		//$this->load->helper(array('form','url','clients','general','userpermissions'));

		$this->load->model('zt2016_entries_model', '', TRUE);
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$this->load->model('zt2016_clients_model', '', TRUE);

		$zowuser=_superuseronly(); 		
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		$templateData['title'] = 'Tracking';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_tracking_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData); 


	}
	

	// ################## display contacts info ##################	
	function _display_tracking_page($ZOWuser)
	{
					
	
		#load entries	
		$RawOngoingEntries=$this->zt2016_entries_model-> GetEntry($options = array('Trash'=>'0','Invoice'=>'NOT BILLED','Status !='=>'COMPLETED','sortBy'=>'DateOut','sortDirection'=>'ASC'));
	
		#load contacts info	
		$ContactsData=$this->zt2016_contacts_model->GetContact($options = array('Trash'=>'0','sortBy'=>'FirstContactIteration','sortDirection'=>'DESC'));
		
		#load clients info	
		$ClientsData =  $this->zt2016_clients_model->GetClient();

		$OngoingEntries=_process_ongoing_jobs($ZOWuser,$RawOngoingEntries,$ContactsData);
		

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
		
			
	#Create page
		
		
		######### panel header
		$page_content.='<div class="panel panel-info" id="top-panel"><div class="panel-heading">'."\n"; 
			
			$page_content.=	"<h4>";
		    
			#### Entries summary
			$page_content.=_displayOngoingJobsSummary($OngoingEntries,$ZOWuser);
		
		
			#### New job button
			$page_content.= '<a href="'.site_url().' '.'" class="btn btn-info btn-sm pull-right">New Job</a>'."\n";


			$page_content.="</h4>\n";
			$page_content.="<div class='clearfix'></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body ">'."\n";
		
		$page_content.='<div id="table_loading_message">Loading ... </div>'."\n";

		#fetch ongoing jobs table
		$page_content .= _Display_Ongoing_Entries_Table($OngoingEntries);		

		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";

  		//if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {
  			
  		//}

		return $page_content;

	}	

	




}	


/* End of file zt2016_tracking.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>