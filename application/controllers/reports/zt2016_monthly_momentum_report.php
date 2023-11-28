<?php

class Zt2016_monthly_momentum_report extends MY_Controller {

	
	public function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		

		$this->load->helper(array('form','url','userpermissions','zt2016_tracking','zt2016_zowcalendar','zt2016_reports_helper'));


		$this->load->model('zt2016_entries_model', '', TRUE);
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$this->load->model('zt2016_clients_model', '', TRUE);

		//$zowuser=_superuseronly(); 		
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		$report_month=$this->uri->segment(3);
		$report_month=str_replace ("%20"," ",$report_month);


		if (empty ($report_month)) {
			
		 	if ($this->input->post('selected_month')){
				$report_month=$this->input->post('selected_month');
		 	} else{
				$report_month= date('M Y');
		 	}
			
		}	
		
		$templateData['report_month']=$report_month;

		$templateData['title'] = 'Monthy Momentum Report';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_tracking_page($report_month,$templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData); 


	}
	

	// ################## display past jobs page ##################	
	function _display_tracking_page($report_month,$ZOWuser)
	{
	
		####### load straight-forward data
		
		#load clients info	
		$ClientsData =  $this->zt2016_clients_model->GetClient();
		
		#load contacts info	
		$ContactsData=$this->zt2016_contacts_model->GetContact($options = array('Trash'=>'0','sortBy'=>'FirstContactIteration','sortDirection'=>'DESC'));
	


			
			$StartDate = date('Y-m-d',strtotime($report_month));
			
			$EndDate = date('Y-m-t',strtotime($report_month));
		
			$options = array('Trash' => '0', 'sortBy'=> 'id', 'sortDirection'=> 'desc');
		


			############### load and process monthly entries
			 $RawMonthEntries = $this->zt2016_entries_model->Get_entries_between_dates($options,$StartDate,$EndDate);
		
			if (date( 'M Y', strtotime($StartDate))==date( 'M Y', strtotime('now'))){	
					$thismonth=1;
			}
				
				
			$TotalJobs=0;
			$TotalNewSlides=0;
			$TotalEditedSlides=0;
			$TotalHours=0;
			$TotalBilledHours=0;
		
			# put relevant relevant data into a processed array
			foreach ($RawMonthEntries as $row){
				
			if (isset($ProcessedMonthEntries[$row->DateOut])){
				$ProcessedMonthEntries[$row->DateOut]['Jobs']=$ProcessedMonthEntries[$row->DateOut]['Jobs']+1;
				$ProcessedMonthEntries[$row->DateOut]['NewSlides']=$ProcessedMonthEntries[$row->DateOut]['NewSlides']+$row->NewSlides;
				$ProcessedMonthEntries[$row->DateOut]['EditedSlides']=$ProcessedMonthEntries[$row->DateOut]['EditedSlides']+$row->EditedSlides;
				$ProcessedMonthEntries[$row->DateOut]['Hours']=$ProcessedMonthEntries[$row->DateOut]['Hours']+$row->Hours;				} 
			else {
				$ProcessedMonthEntries[$row->DateOut]['DateOut']=$row->DateOut;
				$ProcessedMonthEntries[$row->DateOut]['Jobs']=1;
				$ProcessedMonthEntries[$row->DateOut]['NewSlides']=$row->NewSlides;
				$ProcessedMonthEntries[$row->DateOut]['EditedSlides']=$row->EditedSlides;
				$ProcessedMonthEntries[$row->DateOut]['Hours']=$row->Hours;	
			}
				
			$TotalJobs++;
			$TotalNewSlides=$TotalNewSlides+$row->NewSlides;
			$TotalEditedSlides=$TotalEditedSlides+$row->EditedSlides;
			$TotalHours=$TotalHours+$row->Hours;
			
			}
		
			# Calculate billed hours
		
			foreach ($ProcessedMonthEntries as $row){
				$BilledHours=0;
				
				if (isset($row['NewSlides'])){
					$BilledHours=$BilledHours+ $row['NewSlides'] / 5;
				}
				if (isset($row['EditedSlides'])){				
					$BilledHours=$BilledHours+ $row['EditedSlides'] / 10;
				}
				if (isset($row['Hours'])){				
					$BilledHours=$BilledHours+ $row['Hours'];
				}
				
				$ProcessedMonthEntries [$row['DateOut']]['BilledHours']=$BilledHours;
				
				$TotalBilledHours=$TotalBilledHours+$BilledHours;
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
		
	
		########## panel head
		$page_content.='<div id="client_info_panel" class="panel panel-success">'."\n";
		$page_content.='<div class="panel-heading">'."\n";
		
			### Month Tile and selector
			
				# Query lowest date from db
				$this->db->select_min('DateOut');
				$start_month = $this->db->get('zowtrakentries');		

				# month selector options
				$month_selector_info['FormURL']=site_url()."reports/zt2016_monthly_momentum_report";
				$month_selector_info['labeltext']= 'Month';
				$month_selector_info['id'] = 'month_dropdown_form';
				$month_selector_info['style'] = 'display:inline-block';

				$page_content.='<h4 style="height:1.5em;">'."\n";
				$page_content.=	'<div class="pull-left" style="margin-right:1rem; padding-top:.7rem;">Monthly Momentum Report for</div>'."\n";
				$page_content.=month_selector($report_month,$start_month,$month_selector_info)."\n";

				########## breakdown button
				$page_content.='<a href="'.site_url().'reports/zt2016_monthly_breakdown/'.$report_month.'" class="btn btn-info btn-sm pull-right">Breakdown</a>';

		$page_content.= '</h4>'."\n";


		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='	<div class="panel-body ">'."\n";
		

	// ################## create month summary ##################

				$monthvar = date( 'm', strtotime($StartDate));
				$yearvar = date( 'Y', strtotime($StartDate));
		
			if (!isset($thismonth)){
				$daysEllapsed = cal_days_in_month(CAL_GREGORIAN, $monthvar, $yearvar);
			}
			else {
				$daysEllapsed= date( 'd', strtotime('now'));
			}
			$dailyAverage = number_format($TotalBilledHours/$daysEllapsed, 2);
			//$page_content="<div class=\"monthtotal\">";

			$page_content.="<div class=\"row\">";
		
			$page_content.="	<div class=\"col-sm-6\">";

			$page_content.="		<h4><strong id=\"hoursavg\">";
			$page_content.=$dailyAverage."</strong>  hours billed per day";
			$page_content.="<small> (average ".$daysEllapsed." days)</small>" ;
			$page_content.="		</h4>";


			$page_content.="		<h4><strong>".number_format($TotalBilledHours,2)."</strong> total hours";

			$page_content.="		</h4>";

			$page_content.="		<h4><strong>".$TotalJobs."</strong> jobs ";
			$page_content.="		</h4>";		

			$page_content.="	</div>";
			


			## Jobs
			$page_content.="	<div class=\"col-sm-6\">";


			$page_content.="		<h4><strong>".number_format($TotalNewSlides)."</strong> Complex slides ( ".number_format($TotalNewSlides/5,2)." hours)";
			$page_content.="		</h4>";

			$page_content.=" 		<h4><strong>".number_format($TotalEditedSlides)."</strong> Simple slides ( ".number_format($TotalEditedSlides/10,2)." hours)";
			$page_content.="		</h4>";

			$page_content.=" 		<h4><strong>".number_format($TotalHours,2)."</strong> Additional hours";
			$page_content.="		</h4>";	

			$page_content.="	</div>";
			$page_content.="	<div class=\"col-sm-12\"><hr /></div>";
			$page_content.="	</div>";
		
		############### chart
		
		$page_content.='<div class="col-sm-12"><div id="chart-area"></div></div>'."\n";
		
		$page_content.='<div class="table-responsive">'."\n";

		
		$page_content.='<table id="days-table" class="table table-striped table-condensed display compact">';
		
		$page_content.="\n<thead><tr><th class=\"header\">Date</th><th class=\"header\">Billed Hours</th><th class=\"header\">Jobs</th><th class=\"header\">New</th><th class=\"header\">Edits</th><th class=\"header\" >Hours</th></tr></thead>";
		
		//var_dump($RawPastEntries);
		
		foreach ($ProcessedMonthEntries as $row){
			$page_content.="<tr>";
			//$page_content.="<td>".$row->DateIn."</td>";
			//var_dump ($row);
			
			$linkURL = "<a href=\"".site_url().'tracking/zt2016_past_jobs/dateview/'.date_format(new DateTime ($row['DateOut']), 'Y')."/".date_format(new DateTime ($row['DateOut']), 'M')."/".date_format(new DateTime ($row['DateOut']), 'd').'">';
	
			$page_content.="<td>".$linkURL.date_format(new DateTime ($row['DateOut']), 'D d-M-y')."</a></td>";
			$page_content.="<td>".$linkURL.$row['BilledHours']."</a></td>";
			$page_content.="<td>".$linkURL.$row['Jobs']."</a></td>";		
			$page_content.="<td>".$linkURL.$row['NewSlides']."</a></td>";
			$page_content.="<td>".$linkURL.$row['EditedSlides']."</a></td>";
			$page_content.="<td>".$row['Hours']."</a></td>";
			
			$page_content.="</tr>";

		}
		
		$page_content.="\n<tfoot><tr><th class=\"header\" ></th><th class=\"header\" >".$TotalBilledHours."</th><th class=\"header\">".$TotalJobs."</th><th class=\"header\" >".$TotalNewSlides."</th><th class=\"header\" >".$TotalEditedSlides."</th><th class=\"header\" >".$TotalHours."</th></tr></tfoot>";		
		
		$page_content.="</table>\n";
		
		//var_dump($RawPastEntries);
		
		
		$page_content.="</div><!--table-responsive-->\n";
		
		$page_content.="	</div><!--panel body-->\n";
		
		$page_content.="</div><!--panel-->\n";

  		//if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {
  			
  		//}

		return $page_content;

	}	


}	


/* End of file zt2016_monthly_day_report.php */
/* Location: ./system/application/controllers/reports/zt2016_monthly_day_report.php */
?>