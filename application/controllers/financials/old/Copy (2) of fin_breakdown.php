<?php

class Fin_breakdown extends MY_Controller {


	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions','financialsnew','form','reports', 'url'));
		$templateVars['ZOWuser']=_superuseronly(); 
		
		$this->load->helper(array());

		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		//$EndDate = date('Y-n-j',strtotime("now"));
		//$StartDate=date('Y-n-1',strtotime("3 month ago"));
		//$timeframe=$StartDate.','.$EndDate;


		

		if ( isset($_POST['year']))
			{
				$year=	$_POST['year'];
		
		} else {
			$year= $this->uri->segment(3);
		}
		$admittedyears = array ();
		for ($i=2010; $i<=date("Y"); $i++){
			array_push($admittedyears, $i);
		}

		if (!in_array($year, $admittedyears)) {
			$year=date("Y");
		}
		
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$Worktype="";
		$templateVars['pageOutput'] .= $this->_gettopmenu($options = array('Year'=>$year));

		$templateVars['pageOutput'] .= "<div class=\"content\">";
		$templateVars['pageOutput'] .= $this->_yearclienttotals($options = array('Year'=>$year));
		$templateVars['pageOutput'] .= "</div><!-- content -->";
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "fin_breakdown";
		$templateVars['pageType'] = "fin_breakdown";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	// ################## top ##################	

	function  _gettopmenu($options)

	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>".$options['Year']." Breakdown by Client</h1>";
			//Add yeardropdown
			$entries .=$this->_gettimeframeDropDown($options['Year']);
			
			//Add splits button
			$entries .="<a href=\"".site_url()."financials\">Monthly Splits</a>";
			//Add trends button
			$entries .="<a href=\"".site_url()."financials/fin_trends\">Trends</a>";
			//Add totals button
			$entries .="<a href=\"".site_url()."financials/fin_totals\" >Totals</a>";
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
 		$year=$options['Year'];
		$yeartotals="";
		$current_year = date('Y');

		$ClientTableRaw  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		foreach ($ClientTableRaw as $row){
			 $ClientCurrency[$row->CompanyName] =$row->Currency;
		}

		
		if ($year== $current_year) {
			$yeartotals .=$this->_calculateyeartotal($year,$ClientTableRaw);
		}




			$StartDate=$year.'-1-1';
			$this->db->select();
			$this->db->where('Year',$StartDate);
			$rawentries = $this->db->get('zowtrakyearsummaries');

			
			
			if ($rawentries) {
				$eurclienttotals= array();
				$usdclienttotals= array();
				foreach ($rawentries->result() as $row){
					$ClientList=explode(",", $row->ClientList);
					$ClientRevenue=explode(",", $row->ClientRevenue);
					array_multisort($ClientRevenue, SORT_DESC,$ClientList);
					for ($i = 0; $i < count($ClientList); $i++) {
						if ($ClientCurrency[$ClientList[$i]]=="EUR") {
							array_push($eurclienttotals, array('Client'=>$ClientList[$i],'Revenue'=>$ClientRevenue[$i]));	
						} else {
							
							array_push($usdclienttotals, array('Client'=>$ClientList[$i],'Revenue'=>$ClientRevenue[$i]));	
						}
					}
				}	
			}
		

		$yeartotals .="<h4 class=\"eurotitle\">".$year." EURO Clients</h4>";
		$yeartotals .="<table id=\"eurclienttotals\"><thead><tr><th>Client</th><th>Revenue</th></tr></thead><tbody>";	
				for ($i = 0; $i < count($eurclienttotals); $i++) {
					$yeartotals .="<tr><th scope=\"row\">".$eurclienttotals[$i]['Client']."</th><td>".$eurclienttotals[$i]['Revenue']."</td></tr>";
				}
		$yeartotals .="</tbody></table>";
		$yeartotals .="<h4 class=\"usdtitle\">".$year." USD Clients</h4>";
		$yeartotals .="<table id=\"usdclienttotals\"><thead><tr><th>Client</th><th>Revenue</th></tr></thead><tbody>";	
				for ($i = 0; $i < count($usdclienttotals); $i++) {
					$yeartotals .="<tr><th scope=\"row\">".$usdclienttotals[$i]['Client']."</th><td>".$usdclienttotals[$i]['Revenue']."</td></tr>";
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
function _calculateyeartotal($thisyear,$ClientTableRaw) {
		$this->load->model('trakclients', '', TRUE);
		$thisyear=date('Y');
		$yearjobs="";
		$yearclients="";

		$eurtotal=0;
		$usdtotal=0;	
		$clientlist="";
		$clientjobs="";
		$clientrevenues="";
		$jobs=0;
		$StartDate=$thisyear.'-1-1';
		$EndDate=$thisyear.'-12-31';

			/*$this->db->select('Client');
			$this->db->select_min('DateOut','StartDate') ;
			$this->db->select('count(id) as Jobs', FALSE);
			//$this->db->select_count('id','Jobs');
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
			$this->db->select_sum('InvoiceTime','Hours');
			//if (isset($options['StartDate']) && $options['StartDate']!="") {
				$this->db->where('DateOut >=',  $StartDate);
			  	$this->db->where('DateOut <= ',  $EndDate);
			//}
			$this->db->where('Trash',0);
				$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID')";
				$this->db->where($where );
			$this->db->group_by('Client');
			$rawentries = $this->db->get('zowtrakentries');*/
		
			//if ($rawentries) {
				//### get client list
//				$ClientTableRaw  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
				foreach ($ClientTableRaw as $row){

		  
				  //Get entry totals from db
				  
				  $this->db->select_sum('Hours','Hours');
				  $this->db->select_sum('NewSlides','NewSlides');
				  $this->db->select_sum('EditedSlides','EditedSlides');
				  $this->db->select('count(id) as Jobs', FALSE);
				  $this->db->from('zowtrakentries');
				  $this->db->where('Client',$row->CompanyName);
					$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID')";
					$this->db->where($where );
				 
				  $this->db->where('DateOut >=', $StartDate);
				  $this->db->where('DateOut <= ', $EndDate);
				   $this->db->where('Trash =',0);
		
				  $query = $this->db->get();
		 
			  
				  //Apply edit price
				  $subtotal=$query->row()->EditedSlides*$row->PriceEdits;
				  //Add slides and divide by slides per hour
				  $subtotal=$subtotal+$query->row()->NewSlides;
				  $subtotal=$subtotal/5;
				  //Add hours to get the total
				  $htotal=$subtotal+$query->row()->Hours;
		
		
				  if ($htotal!=0){
					 $tprice =_fetchClientMonthPrice($row,$htotal);
					
					 $invoicerevenue=$tprice*$htotal;
		
					 
					 //add to totals
		
					 
					 if ($row->Currency=="EUR"){
					 	
						$eurtotal+=$invoicerevenue;
					 }
					 else if ($row->Currency=="USD"){
					 	
						$usdtotal+=$invoicerevenue;
						
					 }
		
					 $jobs+=$query->row()->Jobs;
					 $clientlist.=",".$row->CompanyName;
					 $clientjobs.=",".$query->row()->Jobs;
					 $clientrevenues.=",". $invoicerevenue;
				}

			}

			$this->db->select();
			$this->db->where('Year',$StartDate);
			$query = $this->db->get('zowtrakyearsummaries');			
			if ($query->num_rows() != 0) {$existflag=1;} else {$existflag=2;} ;
			
			$clientlist=$this->_deleteinitialcomma ($clientlist);
			$clientjobs=$this->_deleteinitialcomma ($clientjobs);
			$clientrevenues=$this->_deleteinitialcomma ($clientrevenues);

			$this->db->set('Year', $StartDate);
			$this->db->set('TotalEUR', number_format($eurtotal,2,".",""));
			$this->db->set('TotalUSD', number_format($usdtotal,2,".",""));
			$this->db->set('Jobs', $jobs);
			$this->db->set('ClientList', $clientlist);
			$this->db->set('ClientJobs', $clientjobs);
			$this->db->set('ClientRevenue', $clientrevenues);			

			if ($existflag==1) {
				$this->db->where('Year',$StartDate);
				$this->db->update('zowtrakyearsummaries');
			} 
			else if($existflag==2) {
				$this->db->set('Year',$StartDate);
				$this->db->insert('zowtrakyearsummaries');	
			}
					
			
	}


// ------------------------------------------------------------------------

/**
 * _getunbilledTotals
 *
 * Provides revenue from completed but not billed jobs 
 *
 * @access	public
 * @return	string
 */
 
	// ################## Generate client totals ##################	

 	function _gettimeframeDropDown($year){
 		$options=array();
		for ($i=2010; $i<=date("Y"); $i++){
			$options[$i]=$i;
		}
 		
 		$attributes['id'] = 'yearcontrol';
 		$WorktypeDropDown= form_open(site_url()."financials/fin_breakdown",$attributes);
		
		
		
 		//$options=array(''=>"All Time",'Office'=>"Last 3 months",'Non-Office'=>"Last 6 months");
		$more = 'id="year" class="year"';
		$WorktypeDropDown .=form_label('year:','year');
		$WorktypeDropDown .=form_dropdown('year', $options,$year,$more);
		$more = 'id="yearsubmit" class="yearsubmit"';			
		$WorktypeDropDown .=form_submit('yearsubmit', 'View',$more);
		$WorktypeDropDown .= form_close()."\n";
		return $WorktypeDropDown ;
 	}
 		


 	
 function _deleteinitialcomma ($mystring){
	return substr($mystring,1,strlen($mystring)-1);
}	
 		


/* End of file fin_breakdown.php */
/* Location: ./system/application/controllers/financials/fin_breakdown.php */
}

?>