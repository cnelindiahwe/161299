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
		
		$this->load->helper(array('form','userpermissions','zt2016_tracking'));
		
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
	

	// ################## display tracking page ##################	
	function _display_tracking_page($ZOWuser)
	{
	
		#load clients info	
		$ClientsData =  $this->zt2016_clients_model->GetClient();
		
		#load contacts info	
		$ContactsData=$this->zt2016_contacts_model->GetContact($options = array('Trash'=>'0','sortBy'=>'FirstContactIteration','sortDirection'=>'DESC'));
		

		#load ongoing entries	
		$RawOngoingEntries=$this->zt2016_entries_model-> GetEntry($options = array('Trash'=>'0','Invoice'=>'NOT BILLED','Status !='=>'COMPLETED','sortBy'=>'DateOut','sortDirection'=>'ASC'));
		$OngoingEntries=_process_ongoing_jobs($ZOWuser,$RawOngoingEntries,$ContactsData);

	
		#load past entries	
		$RawPastEntries = $this->zt2016_entries_model->GetCompletedEntries($options = array('Trash' => '0', 'sortByNew'=> '1', 'limit'=> '20'));
		
		
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
		
		######### Top panel
		
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
		
		$page_content.='<div class="table_loading_message">Loading ... </div>'."\n";

		#fetch ongoing jobs table
		$page_content .= _Display_Ongoing_Entries_Table($OngoingEntries);		

		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";
		
				
		######### Bottom panel
		
		######### panel header
		$page_content.='<div class="panel panel-default" id="bottom-panel"><div class="panel-heading">'."\n"; 
		
		$page_content.='<div class="col-sm-4">'."\n";
			$page_content.=	"<h4>";
				$page_content.="Past Jobs";		
			$page_content.="</h4>\n";		
		$page_content.="</div>\n";
		
		$page_content.='<div class="col-sm-8">'."\n";
			$page_content .= $this-> _pastjobscontrol();
		$page_content.="</div>\n";
		$page_content.="<div class=\"clearfix\" style=\"height:2.5em;\"></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body ">'."\n";
		
		$page_content.='<div class="table_loading_message">Loading ... </div>'."\n";

		#fetch ongoing jobs table
		//$page_content .= $this->_Display_Past_Entries_Table($RawPastEntries);		
		$page_content .= _Display_Past_Entries_Table($RawPastEntries);		

		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";

  		//if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {
  			
  		//}

		return $page_content;

	}	

		// ################## past jobs control ##################	
	function   _pastjobscontrol()
	{
		$attributes['id'] = 'pastjobscontrol';
		$attributes['class'] = 'form pull-right';
		$pastjobscontrol= form_open(site_url()."tracking\zt2016_tracking",$attributes);
		 $pastjobscontrol.="<div class=\"form-group\">\n";

			
			//Submit
		     $more = 'class="form-control btn"';	
			$pastjobscontrol .=form_submit('PastJobsSubmit', 'Change',$more);

		
			//View type
			$options = array('list' => 'List', 'month'=>'Month');
			$more = 'id="viewtype" class="form-control"';	
			$selected='list';
			$pastjobscontrol .=form_label('View:','viewtypes');
			$pastjobscontrol .=form_dropdown('viewtype', $options,$selected,$more);

			//jobs listed
			$options = array('10' => '10', '20'=>'20', '50'=>'50', '100'=>'100', '200'=>'200', '400'=>'400','600'=>'600', '800'=>'800');
			$more = 'id="numberjobs" class="form-control"';	
			$selected='20';
			$pastjobscontrol .=form_label('Jobs listed:','numberjobs');
			$pastjobscontrol .=form_dropdown('numberjobs', $options,$selected,$more);
			
			//Clients
				$this->load->model('trakclients', '', TRUE);
				$Clientlist = $this->trakclients->GetEntry();
				$options=array();
				foreach($Clientlist as $client)
				{
				$options[$client->CompanyName]=$client->CompanyName;
				}
				asort($options);
				$options=array(''=>"All")+$options;		
				$more = 'id="pastjobsclient" class="form-control"';			
				$selected='all';
				$pastjobscontrol .=form_label('Client:','pastclientlist');
				$pastjobscontrol .=form_dropdown('pastclientlist', $options,$selected ,$more);

		$pastjobscontrol .= form_close()."\n";
		$pastjobscontrol.="</div>\n";//form group
		return $pastjobscontrol;
	
	}

}	


/* End of file zt2016_tracking.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>