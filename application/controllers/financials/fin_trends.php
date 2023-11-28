<?php

class Fin_trends extends MY_Controller {


	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions','financialsnew','form','reports'));
		$templateVars['ZOWuser']=_superuseronly(); 
		
		$this->load->helper(array());

		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		$this->load->model('trakcontacts', '', TRUE);
		$this->load->model('traksummaries', '', TRUE);
		
		$EndDate = date('Y-n-j',strtotime("now"));
		$StartDate=date('Y-n-1',strtotime("3 month ago"));
		$timeframe=$StartDate.','.$EndDate;

		if ( isset($_POST['Timeframe']))
		{
			$timeframe=	$_POST['Timeframe'];
			$timeframedates=preg_split('/\,/', $_POST['Timeframe']);		
			$StartDate = $timeframedates[0];
			$EndDate = $timeframedates[1];
		}
		
		//Month totals
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$Worktype="";
		$templateVars['pageOutput'] .= $this->_getTopMenu($timeframe);

		$templateVars['pageOutput'] .= "<div class=\"content\">";
			$templateVars['pageOutput'] .= $this->_totalstables();
		
		
		
		$templateVars['pageOutput'] .= "</div><!-- content -->";
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "fin_trends";
		$templateVars['pageType'] = "fin_trends";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	// ################## top ##################	

	function  _getTopMenu($timeframe="")

	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Financials - Trends</h1>";
			//Add export button
			$entries .="<a href=\"".site_url()."financials/csv_trends\" >Export Data</a>";
			//Add splits button
			$entries .="<a href=\"".site_url()."financials\" >Monthly Splits</a>";
			//Add totals button
			$entries .="<a href=\"".site_url()."financials/fin_totals\" >Totals</a>";
			//Add breakdown button
			$entries .="<a href=\"".site_url()."financials/fin_breakdown\">Breakdown</a>";
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}

// ------------------------------------------------------------------------

/**
 * _totalstables
 *
 * Provides number of clients per month
 *
 * @access	public
 * @return	string
 */
 	function _totalstables($options=array()){
		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);
 		$reportresults ="<h3>Historical Data</h3>";
		$reportresults .="<p>Hover over legend to see values and trend line. Click on legend items to turn on or off the lines.</p><div class=\"clientreportlayout\">";
		
		$reportresults .="<table id=\"graphtable\">";
		$reportresults .="<thead><th></th><th>Billed Hours</th><th>Jobs</th><th>New Slides</th><th>Edited Slides</th><th>Hours</th><th>Clients</th><th>Originators</th><th>New Clients</th><th>New Originators</th></thead><tbody>";
 		$now = strtotime(date('Y-m-15'));
 		//for ($i =12; $i >=0; $i--) {
		
		$StartDate = date( 'Y-m-1', strtotime('2010-10-1'));
		$EndDate = date( 'Y-m-t', strtotime('-1 month',$now ));
		$rawresults =$this->traksummaries->GetMonthEntrybyDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		foreach($rawresults as $row)
		{
			$reportresults .= "<tr><th scope=\"row\">".date( 'M Y', strtotime($row->Date))."</th><td>".$row->BilledHours."</td><td>".$row->Jobs."</td>";
			
			// $rownew =  (($row->New/5)/$row->BilledHours)*100;
			
			if($row->Edits != 0  && $row->BilledHours !=0)
{
	$rownew =  (($row->New/5)/$row->BilledHours)*100;
	$rowedits =(($row->Edits/10)/$row->BilledHours)*100;
	$rowhours = ($row->Hours/$row->BilledHours)*100;
	$reportresults .= "<td>".number_format($rownew,0)."</td><td>".number_format($rowedits,0)."</td><td>".number_format($rowhours,0)."</td>";
	$reportresults .="<td>".$row->Clients."</td><td>".$row->Contacts."</td><td>".$row->NewClients."</td><td>".$row->NewContacts."</td></tr>";

}
		
	}			
		//}
		// die('fd');
		$reportresults .="</tbody></table></div>";

		return $reportresults;
	}	
// ------------------------------------------------------------------------

 	
 		


/* End of file fin_trends.php */
/* Location: ./system/application/controllers/financials/fin_totals.php */
}

?>