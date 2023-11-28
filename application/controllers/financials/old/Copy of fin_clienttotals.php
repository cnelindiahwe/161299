<?php

class Fin_clienttotals extends MY_Controller {


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
		//$EndDate = date('Y-n-j',strtotime("now"));
		//$StartDate=date('Y-n-1',strtotime("3 month ago"));
		//$timeframe=$StartDate.','.$EndDate;


		
		//Month totals

		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$Worktype="";
		$templateVars['pageOutput'] .= $this->_gettopmenu();

		$templateVars['pageOutput'] .= "<div class=\"content\">";
		$templateVars['pageOutput'] .= $this->_yearclienttotals();
		$templateVars['pageOutput'] .= "</div><!-- content -->";
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "fin_clienttotals";
		$templateVars['pageType'] = "fin_clienttotals";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	// ################## top ##################	

	function  _gettopmenu()

	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Financials - Totals</h1>";

			//Add splits button
			$entries .="<a href=\"".site_url()."financials\">Monthly Splits</a>";
			//Add trends button
			$entries .="<a href=\"".site_url()."financials/fin_trends\">Trends</a>";
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}


	
// ------------------------------------------------------------------------

/**
 * _yearclienttotals
 *
 * Provides total breakdown per year
 *
 * @access	public
 * @return	string
 */
 
 	function _yearclienttotals($options=array()){
 		$year=2012;
		$yeartotals="";
		$current_year = date('Y');
		$yeartotals .="<h3>".$year." Total Client Breakdown</h3>";
		$yeartotals .="<table class=\"yearclienttotals\"><thead><tr><th>Client</th><th>Revenue</th><th>Currency</th></tr></thead><tbody>";	

		
		if ($year== $current_year) {
			$yeartotals .=$this->_calculateyeartotal($thisyear);
		}
		else {

			$ClientTableRaw  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
			foreach ($ClientTableRaw as $row){
				 $ClientCurrency[$row->CompanyName] =$row->Currency;
			}


			$StartDate=$year.'-1-1';
			$this->db->select();
			$this->db->where('Year',$StartDate);
			$rawentries = $this->db->get('zowtrakyearsummaries');

			
			
			if ($rawentries) {
				foreach ($rawentries->result() as $row){
					$ClientList=explode(",", $row->ClientList);
					$ClientRevenue=explode(",", $row->ClientRevenue);
					for ($i = 1; $i < count($ClientList); $i++) {
						$yeartotals .="<tr><th scope=\"row\">".$ClientList[$i]."</th><td>".$ClientRevenue[$i]."</td><td>".$ClientCurrency[$ClientList[$i]]."</td></tr>";
					}
				}	
			}
		}
				
		$yeartotals .="</tbody></table>";
		return $yeartotals;				
	}	


// ------------------------------------------------------------------------

/**
 * _calculateyeartotal
 *
 * Calculates totals for the year
 *
 * @access	public
 * @return	string
 */
function _calculateyeartotal($thisyear) {
				$current_year = date('Y');
				$StartDate=$thisyear.'-1-1';
				$EndDate=$thisyear.'-12-31';
				
				$yeartotals="";
				
				$this->db->select('Client');
				$this->db->select_min('DateOut','StartDate') ;
				$this->db->select('count(id) as Jobs', FALSE);
				//$this->db->select_count('id','Jobs');
				$this->db->select_sum('InvoiceEntryTotal','Revenues');
				$this->db->select_sum('InvoiceTime','Hours');
				//if (isset($options['StartDate']) && $options['StartDate']!="") {
					$this->db->where('DateOut >=',  $StartDate);
				  	$this->db->where('DateOut <= ',  $EndDate);
				//}
				$this->db->where('Trash',0);
				$this->db->where("Invoice != 'NOT BILLED'");
				$this->db->group_by('Client');
				$rawentries = $this->db->get('zowtrakentries');
	
				
				if ($rawentries) {
					//get client list
					$ClientTableRaw  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
					
					
					foreach ($ClientTableRaw as $row){
						 $ClientCurrency[$row->CompanyName] =$row->Currency;
					}
					$eurtotal=0;
					 $usdtotal=0;
					
					foreach ($rawentries->result() as $row){
	
	
						 if ($ClientCurrency[$row->Client]=="EUR") { $eurtotal+=$row->Revenues;}
						 else if ($ClientCurrency[$row->Client]=="USD") { $usdtotal+=$row->Revenues;}
						
					}
				
					$unbilled=$this->_getunbilledTotals();
					$eurtotal+=$unbilled['eurtotal'];
					$usdtotal+=$unbilled['usdtotal'];
				//############ Check if current year
				$timestamp_year_est="";
				$timestamp_year = date('Y');

				$timestamp_year_est= $timestamp_year;//. " Est.";
				$timestamp_year= $timestamp_year. " YTD";				

				}

					$start = strtotime('2013-01-01');
					$end = strtotime('now');
					$daysellaspsed = ceil(abs($end - $start) / 86400);


					$est_euro= ($eurtotal/$daysellaspsed)*365;
					$est_usd= ($usdtotal/$daysellaspsed)*365;


					
					$yeartotals .="<tr><th scope=\"row\">".$timestamp_year_est."</th><td>".number_format($est_euro,2)."</td><td>".number_format($est_usd,2)."</td></tr>";	
				$yeartotals .="<tr><th scope=\"row\">".$timestamp_year."</th><td>".number_format($eurtotal,2)."</td><td>".number_format($usdtotal,2)."</td></tr>";	

		return $yeartotals;
}

 	
 	
 		


/* End of file fin_clienttotals.php */
/* Location: ./system/application/controllers/financials/fin_totals.php */
}

?>