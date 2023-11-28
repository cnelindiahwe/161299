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
			$currentclient = $this->trakclients->GetEntry($options = array('ID' => $clientid));
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
	
	
	$reporttable="<table id=\"clientreportdata\">";
	$reporttable.= "<thead><tr><th>Month</th><th>Total</th><th>Jobs</th><th>New</th><th>Edits</th><th>Hours</th></tr></thead>";	
	$sixmonth=0;
	$twelvemonth=0;
		$this->load->model('trakreports', '', TRUE);
		if ($timeframe!="")
			{$now =strtotime(date( $timeframe));}
		else 
			{$now =strtotime(date('Y-m-15'));}
		
		
		
		for ($i = 0; $i <= 11; $i++) {
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now));

			$hdata= $this->trakreports->_ClientHistorical($options = array('Client' => $currentclient, 'StartDate'=> $StartDate,'EndDate'=> $EndDate));
			$reporttable.= "<tr><th scope='row'>".$hdata['month']."</td><td>".$hdata['total']." </td><td>".$hdata['jobs']."</td><td>".$hdata['newslides']."</td><td>".$hdata['editslides']."</td><td>".$hdata['hours']." </td></tr>";
			$twelvemonth=$twelvemonth+$hdata['total'];
			if ($i<5) {
				$sixmonth=$sixmonth+$hdata['total'];
			}
			
		} 
		$reporttable.="</table>";
		$average6=number_format($sixmonth/6,1);
		$average12=number_format($twelvemonth/12,1);
		
		$report="<div class=\"clientreportlayout\">";
		$report.="<h3>Historical report for ";
		$report.=$currentclient->CompanyName."</h3>";
		$report.="<p><strong>".$average12." hours billed on average per month last 12 months</strong> ";
		$report.="(Total last 12 months: ".$twelvemonth." hours)</p>";


		$report.="<p><strong>".$average6." hours billed on average  per month last 6 months </strong> ";
		$report.="(Total last 6 months: ".$sixmonth." hours)</p>";
		$report.=$reporttable;
		$report.="</div>";

		return $report;
	}



}

/* End of file Clientreport .php */
/* Location: ./system/application/controllers/reports/Clientreport .php */
?>