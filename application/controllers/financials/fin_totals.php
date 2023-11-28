<?php

class Fin_totals extends MY_Controller {


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
		$templateVars['pageOutput'] .= $this->_yeartotals();
		//$templateVars['pageOutput'] .= $this->_originatorTotals();
		$templateVars['pageOutput'] .= "</div><!-- content -->";
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "fin_totals";
		$templateVars['pageType'] = "fin_totals";
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
			//Add breakdown button
			$entries .="<a href=\"".site_url()."financials/fin_breakdown\">Breakdown</a>";
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}


	
// ------------------------------------------------------------------------

/**
 * _yeartotals
 *
 * Provides totals per year
 *
 * @access	public
 * @return	string
 */
 
 	function _yeartotals($options=array()){
 		
		$yeartotals="";
	 	$years = array()	;

		for ($i=2010; $i<=date("Y"); $i++){
			array_push($years, $i);
		}
		$yeartotals .="<h3>Year Totals</h3>";
		$yeartotals .="<p>For 2013, dark color is YTD, light color is estimated based on YTD.<br/>Click on the bars to see breakdown by clients.<p>";
		$yeartotals .="<table class=\"yeartotals\"><thead><tr><th>Year</th><th>Euros</th><th>US Dollars</th></tr></thead><tbody>";	
		$current_year = date('Y');
		foreach ($years as $thisyear) {
			
			if ($current_year== $thisyear) {
				$yeartotals .=$this->_calculateyeartotal($thisyear);
			}
			else {
				$StartDate=$thisyear.'-1-1';
				$this->db->select();
				$this->db->where('Year',$StartDate);
				$rawentries = $this->db->get('zowtrakyearsummaries');
				if ($rawentries) {
					foreach ($rawentries->result() as $row){
							$yeartotals .="<tr><th scope=\"row\">".Date("Y",strtotime($row->Year))."</th><td>".number_format($row->TotalEUR,2)."</td><td>".number_format($row->TotalUSD,2)."</td></tr>";
						
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
				$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID')";
				$this->db->where($where );
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
					$jobs=0;
					$clientlist="";
					 $clientjobs="";	
					 $clientrevenue="";	
					foreach ($rawentries->result() as $row){

						//echo $row->Client." ".$row->Jobs." ".$jobs."<br/>";
						 if ($ClientCurrency[$row->Client]=="EUR") { $eurtotal+=$row->Revenues;}
						 else if ($ClientCurrency[$row->Client]=="USD") { $usdtotal+=$row->Revenues;}
						 $jobs+=$row->Jobs;
						 $clientlist.=",".$row->Client;	
						 $clientjobs.=",".$row->Jobs;	
						 $clientrevenue.=",".$row->Revenues;					
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

				$start = strtotime($thisyear.'-01-01');
				$end = strtotime('now');
				$daysellaspsed = ceil(abs($end - $start) / 86400);


				$est_euro= ($eurtotal/$daysellaspsed)*365;
				$est_usd= ($usdtotal/$daysellaspsed)*365;


				
				$yeartotals .="<tr><th scope=\"row\">".$timestamp_year_est."</th><td>".number_format($est_euro,2)."</td><td>".number_format($est_usd,2)."</td></tr>";	
				$yeartotals .="<tr><th scope=\"row\">".$timestamp_year."</th><td>".number_format($eurtotal,2)."</td><td>".number_format($usdtotal,2)."</td></tr>";	

				$clientlist=$this->_deleteinitialcomma ($clientlist);
				$clientjobs=$this->_deleteinitialcomma ($clientjobs);
				$clientrevenue=$this->_deleteinitialcomma ($clientrevenue);				

				$this->db->select();
				$this->db->where('Year', $StartDate);
				$query = $this->db->get('zowtrakyearsummaries');
				if ($query->num_rows() != 0) {$existflag=1;} else {$existflag=2;} ;
	
				$this->db->set("TotalEUR", $eurtotal);
				$this->db->set("TotalUSD", $usdtotal);
				$this->db->set("Jobs", $jobs);
				$this->db->set("ClientList", $clientlist);			
				$this->db->set("ClientJobs",$clientjobs);	
				$this->db->set("ClientRevenue", $clientrevenue);					
								
				if ($existflag==1) {
					$this->db->where('Year', $StartDate);
					$this->db->update('zowtrakyearsummaries');
				} 
				else if($existflag==2) {
					$this->db->set("Year", $StartDate);
					$this->db->insert('zowtrakyearsummaries');	
				}

		return $yeartotals;
}
// ------------------------------------------------------------------------

/**
 * _originatorTotals
 *
 * Provides monthly prices per originator
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
				 if ($row->Revenues!=0){
					 $financial_totals .="<td>".number_format($row->Revenues,2)."</td>";
					 $financial_totals .="<td>".number_format($row->Revenues/$row->Hours,2)."</td>";
					 $financial_totals .="<td>".$ClientCurrency[$row->Client]."</td>";
					 $financial_totals .="<td>".$row->Jobs."</td>";				 	
					 $financial_totals .="<td>".number_format($row->Hours,1)."</td></tr>";
				}
				
			}
			$financial_totals .="</tbody></table></div>";
		}
		 return $financial_totals;

	}
// ------------------------------------------------------------------------

/**
 * _getunbilledTotals
 *
 * Provides monthly prices per client
 *
 * @access	public
 * @return	string
 */
 
	// ################## Generate client totals ##################	
	function  _getunbilledTotals()
	{
		$this->load->model('trakclients', '', TRUE);
		$Clientlist= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));

		$totalusd=0;
		$totaleuro=0;

		foreach($Clientlist as $client)
		{


		$StartDate = date( 'Y-m-1', strtotime('2013-1-1'));
		$EndDate = date( 'Y-m-d', strtotime('now'));
		  
		  //Get entry totals from db
		  
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$client->CompanyName);
			$this->db->where('Status','COMPLETED');
			$this->db->where('Invoice','NOT BILLED');
		 
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndDate);
		   $this->db->where('Trash =',0);

		  $query = $this->db->get();
		  //var_dump($query);
		  

			
		  //Get client details from db
		  $this->load->model('trakclients', '', TRUE);
		  $query2 = $this->trakclients->GetEntry($options = array('CompanyName' => $client->CompanyName));
 
	  
		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$query2->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;


		  if ($htotal!=0){
		  		 	
//echo $client->CompanyName."   ".$query->row()->EditedSlides."<br/>";	
			 
			 $tprice =_fetchClientMonthPrice($query2,$htotal);
			
			 $invoicerevenue=$tprice*$htotal;

			 
			 //add to totals

			 
			 if ($query2->Currency=="EUR"){
			 	
				$totaleuro=$totaleuro+$invoicerevenue;
			 }
			 else if ($query2->Currency=="USD"){
			 	
				$totalusd=$totalusd+$invoicerevenue;
				
			 }
			 
		}	
	}

	$total['eurtotal']=$totaleuro;
	$total['usdtotal']=$totalusd;
	return $total;
		

	}

// ------------------------------------------------------------------------

/**
 * _deleteinitialcomma
 *
 * Deletes initial comma from series strings
 *
 * @access	public
 * @return	string
 */
 	
 function _deleteinitialcomma ($mystring){
	return substr($mystring,1,strlen($mystring)-1);
}	
 		


/* End of file fin_totals.php */
/* Location: ./system/application/controllers/financials/fin_totals.php */
}

?>