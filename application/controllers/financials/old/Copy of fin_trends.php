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
		$templateVars['pageOutput'] .= $this->_monthclienttotals();
		$templateVars['pageOutput'] .= $this->_monthoriginatortotals();
		$templateVars['pageOutput'] .= $this->_newclienttotals();
		$templateVars['pageOutput'] .= $this->_neworiginatorstotals();
		$templateVars['pageOutput'] .= $this->_monthjobtotals();
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
			//Add splits button
			$entries .="<a href=\"".site_url()."financials\" >Monthly Splits</a>";
			//Add totals button
			$entries .="<a href=\"".site_url()."financials/fin_totals\" >Totals</a>";
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}

// ------------------------------------------------------------------------

/**
 * _monthclienttotals
 *
 * Provides number of clients per month
 *
 * @access	public
 * @return	string
 */
 	function _monthclienttotals($options=array()){
 		$reportresults ="<div class=\"clientreportlayout\"><h3>Number of clients per month</h3>";
		$reportresults .="<table class=\"graphtable\">";
		$reportresults .="<thead><td></td><th>Clients</th></thead><tbody>";
 		$now = strtotime(date('Y-m-15'));
 		for ($i =12; $i >=0; $i--) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			
			$rawresults =$this->trakreports->ClientsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			$reportresults .= "<tr><th scope=\"row\">".Date('Y F',strtotime($StartDate))."</th><td>".count($rawresults)."</td></tr>";			
		}
		$reportresults .="</tbody></table></div>";
		return $reportresults;
	}	
// ------------------------------------------------------------------------

/**
 * _monthoriginatortotals
 *
 * Provides number of clients per month
 *
 * @access	public
 * @return	string
 */
 	function _monthoriginatortotals($options=array()){
 		$reportresults ="<div class=\"clientreportlayout\"><h3>Number of originators per month</h3>";
		$reportresults .="<table class=\"graphtable\">";
		$reportresults .="<thead><td></td><th>Originators</th></thead><tbody>";
 		$now = strtotime(date('Y-m-15'));
 		for ($i =12; $i >=0; $i--) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			
			$rawresults =$this->trakreports->OriginatorsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			$reportresults .= "<tr><th scope=\"row\">".Date('Y F',strtotime($StartDate))."</th><td>".count($rawresults)."</td></tr>";			
		}
		$reportresults .="</tbody></table></div>";
		return $reportresults;
	}
	
// ------------------------------------------------------------------------

/**
 * _monthjobtotals
 *
 * Provides number of jobs per month
 *
 * @access	public
 * @return	string
 */
 	function _monthjobtotals($options=array()){
 		$reportresults ="<div class=\"clientreportlayout\"><h3>Number of jobs per month</h3>";
		$reportresults .="<table class=\"graphtable\">";
		$reportresults .="<thead><td></td><th>Jobs</th></thead><tbody>";
 		$now = strtotime(date('Y-m-15'));
 		for ($i =12; $i >=0; $i--) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			
			$rawresults =$this->trakreports->_NumJobsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			$reportresults .= "<tr><th scope=\"row\">".Date('Y F',strtotime($StartDate))."</th><td>".$rawresults."</td></tr>";			
		}
		$reportresults .="</tbody></table></div>";
		return $reportresults;
	}
// ------------------------------------------------------------------------

/**
 * _neworiginatorstotals
 *
 * Provides number of new originators per month
 *
 * @access	public
 * @return	string
 */
 	function _neworiginatorstotals($options=array()){
 	
		
		
 		$reportresults ="<div class=\"clientreportlayout\"><h3>Number of new originators per month</h3>";
		$reportresults .="<table class=\"graphtable\">";
		$reportresults .="<thead><td></td><th>Originators</th></thead><tbody>";
 		$now = strtotime(date('Y-m-15'));
 		for ($i =12; $i >=0; $i--) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			
			$rawresults =$this->trakcontacts->GetNewContacts($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			$reportresults .= "<tr><th scope=\"row\">".Date('Y F',strtotime($StartDate))."</th><td>".count($rawresults)."</td></tr>";			
		}
		$reportresults .="</tbody></table></div>";
		return $reportresults;
	}	

// ------------------------------------------------------------------------

/**
 * _neworiginatorstotals
 *
 * Provides number of new originators per month
 *
 * @access	public
 * @return	string
 */
 	function _newclienttotals($options=array()){
 	
		
		
 		$reportresults ="<div class=\"clientreportlayout\"><h3>Number of new clients per month</h3>";
		$reportresults .="<table class=\"graphtable\">";
		$reportresults .="<thead><td></td><th>Clients</th></thead><tbody>";
 		$now = strtotime(date('Y-m-15'));
 		for ($i =12; $i >=0; $i--) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			
			$rawresults =$this->trakclients->GetNewClients($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			$reportresults .= "<tr><th scope=\"row\">".Date('Y F',strtotime($StartDate))."</th><td>".count($rawresults)."</td></tr>";			
		}
		$reportresults .="</tbody></table></div>";
		return $reportresults;
	}	

 		
 	
 	
 		


/* End of file fin_totals.php */
/* Location: ./system/application/controllers/financials/fin_totals.php */
}

?>