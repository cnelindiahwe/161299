<?php

class Fin_totalsold extends MY_Controller {


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
		$templateVars['pageOutput'] .= $this->_getfinancialsgpage($timeframe);

		$templateVars['pageOutput'] .= "<div class=\"content\">";
		$templateVars['pageOutput'] .= $this->_originatorTotals($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		$templateVars['pageOutput'] .= $this->_clientTotals($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		$templateVars['pageOutput'] .= "</div><!-- content -->";
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "fin_totals";
		$templateVars['pageType'] = "fin_totals";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	// ################## top ##################	

	function  _getfinancialsgpage($timeframe="")

	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Financials - Totals</h1>";
			$entries .=$this->_gettimeframeDropDown($timeframe);
			//Add splits button
			$entries .="<a href=\"".site_url()."financials\>Monthly Splits</a>";
			//Add trends button
			$entries .="<a href=\"".site_url()."financials/fin_trends\">Trends</a>";
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}


	
// ------------------------------------------------------------------------

/**
 * _originatorTotals
 *
 * Provides monthly prices per client
 *
 * @access	public
 * @return	string
 */
 
 	function _originatorTotals($options=array()){
 		//run db query	
 		$this->db->select('Client');
		$this->db->select('Originator');
		$this->db->select_min('DateOut','StartDate') ;
		$this->db->select('count(id) as Jobs', FALSE);
		//$this->db->select_count('id','Jobs');
		$this->db->select_sum('InvoiceEntryTotal','Revenues');
		$this->db->select_sum('InvoiceTime','Hours');
		if (isset($options['StartDate']) && $options['StartDate']!="") {
			$this->db->where('DateOut >=',  $options['StartDate']);
		  	$this->db->where('DateOut <= ',  $options['EndDate']);
		}
		$this->db->where('Trash',0);
		$this->db->where("Invoice != 'NOT BILLED'");
		$this->db->group_by('Originator');
		$rawentries = $this->db->get('zowtrakentries');
		//if results exist, list them
		if ($rawentries) {
			//get client list
			$ClientTableRaw  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
			
			foreach ($ClientTableRaw as $row){
				 $ClientCurrency[$row->CompanyName] =$row->Currency;
			}
			$financial_totals ="<div class=\"breakdown\"><h3>By Originator</h3>";
			$financial_totals .="<table class=\"originatorsdata\"><thead><tr><th>Originator</th><th>Client</th><th>Revenues</th><th>Avg. Price</th><th>Currency</th><th>Jobs</th><th>Hours</th></tr></thead><tbody>";	
			foreach ($rawentries->result() as $row){
				 $financial_totals .="<tr><th scope=\"row\">".$row->Originator."</th>";
				 $financial_totals .="<td>".$row->Client."</td>";
				 $financial_totals .="<td>".number_format($row->Revenues,2)."</td>";
				 $financial_totals .="<td>".number_format($row->Revenues/$row->Hours,2)."</td>";
				 $financial_totals .="<td>".$ClientCurrency[$row->Client]."</td>";
				 $financial_totals .="<td>".$row->Jobs."</td>";				 	
				 $financial_totals .="<td>".number_format($row->Hours,1)."</td></tr>";
				
			}
			$financial_totals .="</tbody></table></div>";
		}
		 return $financial_totals;

	}
// ------------------------------------------------------------------------

/**
 * _clientTotals
 *
 * Provides monthly prices per client
 *
 * @access	public
 * @return	string
 */
 
 	function _clientTotals($options=array()){
 		//run db query	
 		$this->db->select('Client');
		$this->db->select_min('DateOut','StartDate') ;
		$this->db->select('count(id) as Jobs', FALSE);
		//$this->db->select_count('id','Jobs');
		$this->db->select_sum('InvoiceEntryTotal','Revenues');
		$this->db->select_sum('InvoiceTime','Hours');
		if (isset($options['StartDate']) && $options['StartDate']!="") {
			$this->db->where('DateOut >=',  $options['StartDate']);
		  	$this->db->where('DateOut <= ',  $options['EndDate']);
		}
		$this->db->where('Trash',0);
		$this->db->where("Invoice != 'NOT BILLED'");
		$this->db->group_by('Client');
		$rawentries = $this->db->get('zowtrakentries');
		//if results exist, list them
		if ($rawentries) {
			//get client list
			$ClientTableRaw  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
			
			foreach ($ClientTableRaw as $row){
				 $ClientCurrency[$row->CompanyName] =$row->Currency;
			}
			$financial_totals ="<div class=\"breakdown\"><h3>By Client</h3>";
			$financial_totals .="<table class=\"clientsdata\"><thead><tr><th>Client</th><th>Revenues</th><th>Avg. Price</th><th>Currency</th><th>Jobs</th><th>Hours</th></tr></thead><tbody>";	
			foreach ($rawentries->result() as $row){
				 $financial_totals .="<tr><th scope=\"row\">".$row->Client."</th>";
				 $financial_totals .="<td>".number_format($row->Revenues,2)."</td>";
				 $financial_totals .="<td>".number_format($row->Revenues/$row->Hours,2)."</td>";
				 $financial_totals .="<td>".$ClientCurrency[$row->Client]."</td>";
				 $financial_totals .="<td>".$row->Jobs."</td>";				 	
				 $financial_totals .="<td>".number_format($row->Hours,1)."</td></tr>";
				
			}
			$financial_totals .="</tbody></table></div>";
		}
		 return $financial_totals;

	}
	
// ------------------------------------------------------------------------

/**
 * _gettimeframeDropDown
 *
 * Provides monthly prices per client
 *
 * @access	public
 * @return	string
 */
 
 	function _gettimeframeDropDown($timeframe=""){
 		//Number of months active

 				
 		$options=array(','=>"All Time");
 		$enddate=date('Y-n-j',strtotime("now"));
 		$startdate=date('Y-n-1',strtotime("- 3 months"));
  		$options=array($startdate.','.$enddate=>"Last 3 months");
		
		
  		$startdate=date('Y-n-1',strtotime(" - 6 months"));
 		$options[$startdate.','.$enddate]="Last 6 months";
		
		$options['2011-1-1,2011-12-31']="2011";
		$options['2012-1-1,2012-12-31']="2012";

		
		$startdate=date('Y-1-1',strtotime("now"));
		$options[$startdate.','.$enddate]=date('Y',strtotime("now"));
		
		$options['2011-1-1,'.$enddate]="All Time";
		
 		$attributes['id'] = 'timeframecontrol';
 		$WorktypeDropDown= form_open(site_url()."financials/fin_totalsold",$attributes);
		
		
		
 		//$options=array(''=>"All Time",'Office'=>"Last 3 months",'Non-Office'=>"Last 6 months");
		$more = 'id="Timeframe" class="Timeframe"';
		$WorktypeDropDown .=form_label('Time frame:','Timeframe');
		$WorktypeDropDown .=form_dropdown('Timeframe', $options,$timeframe,$more);
		$more = 'id="timeframesubmit" class="timeframesubmit"';			
		$WorktypeDropDown .=form_submit('timeframesubmit', 'View',$more);
		$WorktypeDropDown .= form_close()."\n";
		return $WorktypeDropDown ;
 	}
 		
 	
 	
 		


/* End of file fin_totals.php */
/* Location: ./system/application/controllers/financials/fin_totals.php */
}

?>