<?php

class Clientreport extends MY_Controller {

	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('zowtrakui','form','url','reports','invoice','userpermissions'));



		//Load all client data
		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));

		
		//Read client id from form
		$clientid=$this->input->post('reportsclient');
		//Read  client id from  url if no value from above
			if (!isset($clientid) || $clientid=="") {
				$clientid	= $this->uri->segment(3);
				
			}

		//Redirect to reports page if no value from above
		if (!isset($clientid) || $clientid=="" || $clientid=="all") {
			redirect('reports');
		}

		//Read client id from form
		$timeframe=$this->input->post('Timeframe');

			

		
		//Load client data from client table
				$this->load->model('trakclients', '', TRUE);
				if (is_numeric($clientid)) {
					$currentclient = $this->trakclients->GetEntry($options = array('ID' => $clientid));
				} else {
					$currentclient = $this->trakclients->GetEntry($options = array('CompanyName' => $clientid));					
				}
		//If client exists
		if ($currentclient){
		
		$this->session->set_flashdata('Client', $currentclient->CompanyName);
			
		//Load all client data
		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));


		$templateVars['ZOWuser']=_getCurrentUser();
		
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageOutput'] .=$this->_getTopBar($ClientList,$currentclient->ID);
		$reportresults = $this->_getReport($currentclient,$timeframe);
		$templateVars['pageOutput'] .= $reportresults;
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Client Report";
		$templateVars['pageType'] = "clientreport";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
		if ($this->input->is_ajax_request()) {
			echo $reportresults;
		}
		else {
			$this->load->vars($templateVars);		
			$this->load->view('zowtrak2012template');
		}
	}

	}


	// ################## top menu ##################	
	function  _getTopBar($ClientList,$currentclient)
	{
			$TopBar ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$TopBar .="<h1>Client Report</h1>";
			$TopBar .=_getClientReportDropDown($ClientList,$currentclient);
			//Add logout button
			$TopBar .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			//page switcher
			$TopBar .=_createpageswitcher();
			//Add tracking
				$TopBar .="<a href=\"".site_url()."tracking\" class=\"logout\">Tracking</a>";

			$TopBar .="</div>";

			
			return $TopBar;

	}

	
	function _getReport($currentclient,$timeframe)
	{
	
		$this->load->model('trakreports', '', TRUE);
		
		$start = new DateTime($currentclient->FirstClientIteration);
		$end = new DateTime('last day of last month');
		$interval = new DateInterval('P1M');
		$period = new DatePeriod($start, $interval, $end);
		
	
		$reporttable="<table id=\"clientreportdata\">";
		$reporttable.= "<thead><tr><th>Month</th><th>Billed Hours</th><th>Jobs</th><th>New</th><th>Edits</th><th>Hours</th></tr></thead>";	
		

			foreach ($period as $dt) {
				//echo $dt->format('F Y') ."<br/>";

				$row= $this->trakreports->_ClientHistorical($options = array('Client' => $currentclient, 'CurrentMonth'=>$dt->format('F Y')));
				
				$reporttable.= "<tr><th scope='row'>".$row['month']."</td><td>".number_format($row['total'], 1)." </td><td>".$row['jobs']."</td><td>".$row['newslides']."</td><td>".$row['editslides']."</td><td>".number_format($row['hours'],2)." </td></tr>";
				
			}
		//die();
		
		$reporttable.="</table>";
		//$average6=number_format($sixmonth/6,1);
		//$average12=number_format($twelvemonth/12,1);
		
		$report="<div class=\"clientreportlayout\">";
		$report.="<h3>Historical report for ";
		$report.=$currentclient->CompanyName."</h3>";
		$report.=$reporttable;
		$report.="</div>";
	return $report;

	}
		


}

/* End of file Clientreport .php */
/* Location: ./system/application/controllers/reports/Clientreport .php */
?>