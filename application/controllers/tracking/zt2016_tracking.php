<?php

class Zt2016_tracking extends MY_Controller {

	
	public function index()
	{

		$this->load->helper('URL');
		$this->load->library('session');
		$this->load->library('SimpleLoginSecure');
		//Load
		$this->load->library('form_validation');
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		
		//$this->load->helper(array('form','url','clients','general','userpermissions'));
		
		$this->load->helper(array('form','url','userpermissions','zt2016_tracking','zt2016_zowcalendar'));
		
		//$this->load->helper(array('form','url','clients','general','userpermissions'));

		$this->load->model('zt2016_entries_model', '', TRUE);
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$this->load->model('zt2016_clients_model', '', TRUE);

				
		
		$templateData['ZOWuser']= _getCurrentUser();
	
		$templateData['title'] = 'Tracking';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_tracking_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData); 


	}
	

	// ################## display tracking page ##################	
	function _display_tracking_page($ZOWuser)
	{
	
		####### load straight-forward data
		
		#load clients info	
		$ClientsData =  $this->zt2016_clients_model->GetClient();
		
		#load contacts info	
		$ContactsData=$this->zt2016_contacts_model->GetContact($options = array('Trash'=>'0','sortBy'=>'FirstContactIteration','sortDirection'=>'DESC'));
		

		#load ongoing entries	
		$RawOngoingEntries=$this->zt2016_entries_model->GetEntry($options = array('Trash'=>'0','Invoice'=>'NOT BILLED','Status !='=>'COMPLETED','sortBy'=>'DateOut','sortDirection'=>'DESC'));
		$OngoingEntries=_process_ongoing_jobs($ZOWuser,$RawOngoingEntries,$ContactsData);
//   print_r($OngoingEntries);
		####### load past entries filters
		
		
		$PastJobsFilters['PastJobsViewType']=$this->input->post('PastJobsViewType');

		if (!$PastJobsFilters['PastJobsViewType']) {
				if ($this->uri->segment(3)=="monthview"){
					$PastJobsFilters['PastJobsViewType']='month';			
				} else{
					$PastJobsFilters['PastJobsViewType']='list';
				}
			}

		
		$PastJobsFilters['NumberPastJobs']=$this->input->post('NumberPastJobs');
		if (!$PastJobsFilters['NumberPastJobs']) {$PastJobsFilters['NumberPastJobs']=20;}
		
		$PastJobsFilters['PastJobsClient']=$this->input->post('PastJobsClient');
		if (!$PastJobsFilters['PastJobsClient']) {
			$PastJobsFilters['PastJobsClient']=$this->uri->segment(6);
			$PastJobsFilters['PastJobsClient']=	str_replace( "_"," ",$PastJobsFilters['PastJobsClient']);
			$PastJobsFilters['PastJobsClient']=str_replace( "~","&", $PastJobsFilters['PastJobsClient']);
			if (!$PastJobsFilters['PastJobsClient']) {$PastJobsFilters['PastJobsClient']='all';}
		}
		
		$PastJobsFilters['PastJobsOriginator']=$this->input->post('PastJobsOriginator');
		if (!$PastJobsFilters['PastJobsOriginator']) {
			$PastJobsFilters['PastJobsOriginator']=$this->uri->segment(7);
			$PastJobsFilters['PastJobsOriginator']=	str_replace( "_"," ",$PastJobsFilters['PastJobsOriginator']);
			if (!$PastJobsFilters['PastJobsOriginator']) {$PastJobsFilters['PastJobsOriginator']='all';}
		}
		
		$AllClientContacts = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $PastJobsFilters['PastJobsClient'],'sortBy' => 'FirstName','sortDirection' => 'Asc'));		
		
		####### load past entries - list
		
		if ($PastJobsFilters['PastJobsViewType']=='list') {
		
			if ($PastJobsFilters['PastJobsClient']=='all') {
				$RawPastEntries = $this->zt2016_entries_model->GetCompletedEntries($options = array('Trash' => '0', 'sortByNew'=> '1', 'limit'=> $PastJobsFilters['NumberPastJobs']));
			} else {
				if ($PastJobsFilters['PastJobsOriginator']=='all') {
					$RawPastEntries = $this->zt2016_entries_model->GetCompletedEntries($options = array('Trash' => '0', 'sortByNew'=> '1', 'limit'=> $PastJobsFilters['NumberPastJobs'], 'Client'=>$PastJobsFilters['PastJobsClient']));

				} else {
					$RawPastEntries = $this->zt2016_entries_model->GetCompletedEntries($options = array('Trash' => '0', 'sortByNew'=> '1', 'limit'=> $PastJobsFilters['NumberPastJobs'], 'Client'=>$PastJobsFilters['PastJobsClient'],'Originator'=>$PastJobsFilters['PastJobsOriginator']));
					if(!$RawPastEntries){
						$RawPastEntries = $this->zt2016_entries_model->GetCompletedEntries($options = array('Trash' => '0', 'sortByNew'=> '1', 'limit'=> $PastJobsFilters['NumberPastJobs'], 'Client'=>$PastJobsFilters['PastJobsClient']));					
					}
				}
			}
		} 
		
		####### load past entries - month 
		
		else if ($PastJobsFilters['PastJobsViewType']=='month') {
			
			if ($this->uri->segment(3)=="monthview"){
				$PastJobsFilters['CalendarYear']=$this->uri->segment(4);
				$PastJobsFilters['CalendarMonth']=$this->uri->segment(5);
			} else{
				$PastJobsFilters['CalendarYear']=$this->input->post('CalendarYear');
				$PastJobsFilters['CalendarMonth']=$this->input->post('CalendarMonth');
			}
			
			if(!empty($PastJobsFilters['CalendarMonth']) && !empty($PastJobsFilters['CalendarYear']))  {
				$newdate=strtotime("1-".$PastJobsFilters['CalendarMonth']."-".$PastJobsFilters['CalendarYear']);
				$CalendarMonth = date( 'm', $newdate);
				$CalendarYear=date( 'Y', $newdate);

			}
			else {
				$PastJobsFilters['CalendarMonth'] = date( 'm', strtotime('now'));
				$PastJobsFilters['CalendarYear']=date( 'Y', strtotime('now'));
				
			}
				
			$StartDate = date('Y-m-d',strtotime($PastJobsFilters['CalendarYear'].'-'.$PastJobsFilters['CalendarMonth']));
			
			$EndDate = date('Y-m-t',strtotime($PastJobsFilters['CalendarYear'].'-'.$PastJobsFilters['CalendarMonth']));
			$options = array('Trash' => '0', 'sortBy'=> 'id', 'sortDirection'=> 'desc');
			if ($PastJobsFilters['PastJobsClient']!='all') {$options['Client']=$PastJobsFilters['PastJobsClient'];}
			if ($PastJobsFilters['PastJobsOriginator']!='all') {$options['Originator']=$PastJobsFilters['PastJobsOriginator']; }
				$RawPastEntries = $this->zt2016_entries_model->Get_entries_between_dates($options,$StartDate,$EndDate);
		 
			}
		
		#Create page
	
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
		$page_content.='<div class="panel panel-info" id="top-panel"><div class="panel-heading"><div class="tracking_page_button_order_desk">'."\n"; 
			
			$page_content.=	"<h4>";
		    
			#### Entries summary
			$page_content.=_displayOngoingJobsSummary($OngoingEntries,$ZOWuser);
		
		
			#### Trash button
			$page_content.= '<a href="'.site_url().'trash/zt2016_trash" class="btn btn-info btn-sm pull-right">View Trash</a>'."\n";
		
			#### Past jobs button
			$page_content.= '<a href="'.site_url().'tracking/zt2016_past_jobs" class="btn btn-default btn-sm pull-right">Past Jobs</a>'."\n";
		
			#### New job button
			$page_content.= '<a href="'.site_url().'zt2016_new_job" class="btn btn-primary btn-sm pull-right">New Job</a>'."\n";


			$page_content.="</h4></div><div class=\"tracking_page_button_order_mob\">\n";
			$page_content.=	"<h4>";
		    
			#### Entries summary
			$page_content.=_displayOngoingJobsSummary($OngoingEntries,$ZOWuser);
		
		
			#### Trash button
			$page_content.= '<p class="prepand"></p><a href="'.site_url().'zt2016_new_job" class="btn btn-primary btn-sm pull-right">New Job</a>'."\n";
			$page_content.= '<a href="'.site_url().'tracking/zt2016_past_jobs" class="btn btn-default btn-sm pull-right">Past Jobs</a>'."\n";
			$page_content.= '<a href="'.site_url().'trash/zt2016_trash" class="btn btn-info btn-sm pull-right">View Trash</a>'."\n";
		
			#### Past jobs button
		
			#### New job button

			$page_content.="</h4></div>\n";
			$page_content.="<div class='clearfix'></div>\n";
			$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body ">'."\n";
		
		$page_content.='<div class="table_loading_message">Loading ... </div>'."\n";

		#fetch ongoing jobs table
		$page_content .= _Display_Ongoing_Entries_Table($OngoingEntries);		

		$page_content.="</div></div><!--panel body-->\n</div><!--panel-->\n";
		
		/*		
		######### Bottom panel
		
		######### panel header
		$page_content.='<div class="panel panel-default" id="bottom-panel"><div class="panel-heading">'."\n"; 
		
		//$page_content.='<div class="col-sm-2">'."\n";
			$page_content.=	"<h4  style=\"display:inline; line-height:2em;\">";
			$page_content.="<a href='".site_url()."tracking/zt2016_past_jobs'>Past Jobs</a>";		
			$page_content.="</h4>\n";		
		//$page_content.="</div>\n";
		
		
		
		
		//*******
			$PastJobsFilters['URL']='tracking/zt2016_tracking';

			if (isset($PastJobsFilters['CalendarYear'])){
				$PastJobsFilters['URL'].= "/monthview/".$PastJobsFilters['CalendarYear']."/".$PastJobsFilters['CalendarMonth'];
			}
			$PastJobsFilters['URL'].="#bottom-panel";		
			$page_content .= _Past_Jobs_Control($PastJobsFilters,$ClientsData,$AllClientContacts);
		//*******
		
		
		$page_content.="<div class=\"clearfix\" ></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='	<div class="panel-body ">'."\n";
		
		$page_content.='<div class="table_loading_message">Loading ... </div>'."\n";
		
		$page_content.='<div class="table-responsive">'."\n";
		
		
		#fetch ongoing jobs list table			
		if ($PastJobsFilters['PastJobsViewType']=='list') {
			$page_content .= _Display_Past_Entries_List_Table($RawPastEntries);		
		
		} 
		
		#fetch ongoing jobs month table	
		else if ($PastJobsFilters['PastJobsViewType']=='month') {
			
				$suffix="";	

				if ($PastJobsFilters['PastJobsClient']!='all'){

					$suffix .="/";
					$safeName=str_replace( " ","_",$PastJobsFilters['PastJobsClient']);
					$safeName=str_replace( "&","~", $safeName);
					$suffix .= $safeName;

					if ($PastJobsFilters['PastJobsOriginator']!='all'){
						$suffix .="/";
						$safeName=str_replace(" ","_", $PastJobsFilters['PastJobsOriginator']);
						$suffix .= $safeName;
					}
				}
		
				$prefs = _Set_Past_Entries_Calendar_Prefs($suffix);

				$this->load->library('calendar', $prefs);

				/*
				if ($CalendarMonth=="") {
					$CalendarMonth=date( 'm');
				}
				if ($CalendarYear=="") {
					$CalendarYear=date( 'Y');
				}
				
				
				if (isset($CalendarData)) {
					unset($CalendarData);
				}
				
				$CalendarData=getZOWCalendarData($RawPastEntries,$PastJobsFilters['CalendarMonth'],$PastJobsFilters['CalendarYear']);
			
			$page_content .=$this->calendar->generate($PastJobsFilters['CalendarYear'],$PastJobsFilters['CalendarMonth'],$CalendarData);
		
		}
		
		
		$page_content.="</div><!--table-responsive-->\n";
		
		$page_content.="	</div><!--panel body-->\n";
		
		$page_content.="</div><!--panel-->\n";
		*/	
	
  		//if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {
  			
  		//}

		return $page_content;

	}	


}	


/* End of file zt2016_tracking.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>