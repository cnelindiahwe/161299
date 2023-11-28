<?php

class Financials extends MY_Controller {


	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions','financials','form'));
		$templateVars['ZOWuser']=_superuseronly(); 
		
		$this->load->helper(array());

		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		
		if ( isset($_POST['financialsdate']))
		{
			$currentmonth =$_POST['financialsdate'];
			$now =date( 'Y-m-15', strtotime($currentmonth));		
			$StartDate = date( 'Y-m-1', strtotime($currentmonth));
			$EndDate = date( 'Y-m-t', strtotime($currentmonth));
		}
		else
		{
			//$now = strtotime(date('Y-m-15'));
			$now =date( 'Y-m-15', strtotime('now'));		
			$StartDate = date( 'Y-m-1', strtotime('now'));
			$EndDate = date( 'Y-m-t', strtotime('now'));
		}		
		//Month totals
		//$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageOutput'] .= $this->_getfinancialsgpage($StartDate);


		$monthtotals = $this->_calculateMonthPrices($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		$templateVars['pageOutput'] .= _MonthPricestable($monthtotals,$StartDate);
		
		//Month splits
		$ClientList= $this->trakreports->ClientsByDate($options =  array('StartDate'=> $StartDate,'EndDate'=> $EndDate,'Booked'=>1,'SortBy'=> 'Client', 'sortDirection'=> 'Desc'));


		$templateVars['pageOutput'].= $this->_monthSplit($StartDate,$EndDate,$ClientList,$monthtotals);

		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "financials";
		$templateVars['pageType'] = "financials";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	// ################## top ##################	

	function  _getfinancialsgpage($StartDate)

	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Financials</h1>";
			$entries .=$this->_dateform($StartDate);
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}


	
// ------------------------------------------------------------------------

/**
 * _calculateMonthPrices
 *
 * Provides monthly prices per client
 *
 * @access	public
 * @return	string
 */
 
 	function _calculateMonthPrices($options=array()){
			$StartDate=$options['StartDate'];
			$EndDate=$options['EndDate'];
			unset ($options);
			$bookeddata = $this->trakreports->_getAllClientTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'Booked'=>2));
			if (isset($bookeddata)){
				foreach ($bookeddata as $row) {
					//$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $row->Client));
						
						foreach ($row as $key => $value) {
							$monthtotal[$row['Client']][$key]= $value;
						}
						$clientdata= $this->trakclients->GetEntry($options2 = array('CompanyName' => $row['Client']));
						//Apply edit price
						$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
						//Add slides and divide by slides per hour
						$subtotalbooked= $row['NewSlides']+$subtotalbooked;
						$subtotalbooked= $subtotalbooked/5;
						//Add hours to get the total
						$bookedtotal= $subtotalbooked+$row['Hours'];
						$monthtotal[$row['Client']]['Total']= $bookedtotal;
						$monthtotal[$row['Client']]['Price']= _fetchClientMonthPrice($clientdata,$bookedtotal);
						$monthtotal[$row['Client']]['Ammount']= $monthtotal[$row['Client']]['Total']*	$monthtotal[$row['Client']]['Price'];
						$monthtotal[$row['Client']]['Currency']=$clientdata->Currency;
					
				}
			}
			$bookeddata = $this->trakreports->_getAllClientBilledTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			
			if (isset($bookeddata)){
				foreach ($bookeddata as $row) {
							$this->db->where('Client', $row['Client']);
							$this->db->where('DateOut >=', $options['StartDate']);
							$this->db->where('DateOut <= ', $options['EndDate']);
							$this->db->where('Trash =',0);
							$this->db->limit(1);
							$this->db->from('zowtrakentries');
							$invoicedata=$this->db->get();
							$invoiceprice=$invoicedata->row()->InvoicePrice;

						if (isset($monthtotal[$row['Client']])){
							//echo $row['Client'].$row['InvoiceTime']."   ".$monthtotal[$row['Client']]['Total'].'   '.number_format($monthtotal[$row['Client']]['Total']+$row['InvoiceTime']).'<br/>';
							$monthtotal[$row['Client']]['Total']= number_format($monthtotal[$row['Client']]['Total']+$row['InvoiceTime']);
							$monthtotal[$row['Client']]['Ammount']= $monthtotal[$row['Client']]['Ammount']+$row['InvoiceEntryTotal'];
							$monthtotal[$row['Client']]['Price']= $monthtotal[$row['Client']]['Price']." / ".$invoiceprice;
						}
						else {
							$monthtotal[$row['Client']]['Price']= 0;
							foreach ($row as $key => $value) {
								$monthtotal[$row['Client']][$key]= $value;
							}
							$clientdata= $this->trakclients->GetEntry($options2 = array('CompanyName' => $row['Client']));
							$monthtotal[$row['Client']]['Total']= number_format($row['InvoiceTime']);
							$monthtotal[$row['Client']]['Ammount']= $row['InvoiceEntryTotal'];							
							$monthtotal[$row['Client']]['Price']= $invoiceprice;
							$monthtotal[$row['Client']]['Currency']=$clientdata->Currency;
						}
					}

			}


		if (!isset($monthtotal)) {$monthtotal="";	}
		 return $monthtotal;

	}
	


// ------------------------------------------------------------------------

/**
 * _monthSplit
 *
 * Provides monthly splits per client
 *
 * @access	public
 * @return	string
 */
	function  _monthSplitNew($StartDate,$EndDate,$ClientList,$monthtotals)
	{
			if ($monthtotals=="") {
				$output="";
				return $output;
			}
			
			
			$AllTotals=array();
			$SummaryTotals=array();
			$split="";
			
			
			$StatusType=array('ScheduledBy','WorkedBy','ProofedBy','CompletedBy'); 
			foreach($StatusType as $Status) {

				foreach ($ClientList as $clientb) {
					$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $clientb->Client));
					$Output = $this->trakreports->_getClientTotalsByDate($StartDate,$EndDate,$clientb,$Status,2);
					foreach ($Output as $row) {
						//Apply edit price
						$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
						//Add slides and divide by slides per hour
						$subtotalbooked= $row['NewSlides']+$subtotalbooked;
						$subtotalbooked= $subtotalbooked/5;
						//Add hours to get the total
						$bookedtotal= $subtotalbooked+$row['Hours'];
				//echo $clientb->Client.",".$Status.",".$row[$Status]."<br/>";
						$AllTotals[$clientb->Client][$Status][$row[$Status]]['total']=$bookedtotal;
						$AllTotals[$clientb->Client][$Status][$row[$Status]]['span']="(".$row['NewSlides']." N, ".$row['EditedSlides']." E, ".$row['Hours']." H)";
					}
				}
				
				
		}
		$array=$this->trakreports-> _getPartnersByDate($StartDate,$EndDate);
		$i = 0;
		foreach ($array as $row) {
			$Partners[$i]=$row['ScheduledBy'];
			$i++;
		} 
		$split.=_buildSplitTables($AllTotals,$StartDate,$EndDate,$Status,$ClientList,$monthtotals,$Partners);
		return $split;
	}

// ------------------------------------------------------------------------

/**
 * _monthSplit
 *
 * Provides monthly splits per client
 *
 * @access	public
 * @return	string
 */
	function  _monthSplit($StartDate,$EndDate,$ClientList,$monthtotals)
	{
			if ($monthtotals=="") {
				$output="";
				return $output;
			}
			
			
			$AllTotals=array();
			$SummaryTotals=array();
			$split="";
			
			
			$StatusType=array('ScheduledBy','WorkedBy','ProofedBy','CompletedBy'); 
			foreach($StatusType as $Status) {

				foreach ($ClientList as $clientb) {
					$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $clientb->Client));
					$Output = $this->trakreports->_getClientTotalsByDate($StartDate,$EndDate,$clientb,$Status,2);
					foreach ($Output as $row) {
						//Apply edit price
						$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
						//Add slides and divide by slides per hour
						$subtotalbooked= $row['NewSlides']+$subtotalbooked;
						$subtotalbooked= $subtotalbooked/5;
						//Add hours to get the total
						$bookedtotal= $subtotalbooked+$row['Hours'];
				//echo $clientb->Client.",".$Status.",".$row[$Status]."<br/>";
						$AllTotals[$clientb->Client][$Status][$row[$Status]]['total']=$bookedtotal;
						$AllTotals[$clientb->Client][$Status][$row[$Status]]['span']="(".$row['NewSlides']." N, ".$row['EditedSlides']." E, ".$row['Hours']." H)";
					}
				}
				
				
		}
		$array=$this->trakreports-> _getPartnersByDate($StartDate,$EndDate);
		$i = 0;
		foreach ($array as $row) {
			$Partners[$i]=$row['ScheduledBy'];
			$i++;
			 
		} 
		$split.=_buildSplitTables($AllTotals,$StartDate,$EndDate,$Status,$ClientList,$monthtotals,$Partners);
		return $split;
	}

	
// ------------------------------------------------------------------------
	
// ------------------------------------------------------------------------

/**
 * _dateform
 *
 * Creates date selector dropdown
 *
 * @access	public
 * @return	string
 */
	function  _dateform($StartDate)
	{
	//get lowest date from db
	$this->db->select_min('DateOut');
	$query = $this->db->get('zowtrakentries');
	
	//echo $query->row(0)->DateOut;
	$initial=date( 'M Y', strtotime($query->row(0)->DateOut));
	
	$selecteddate =date( 'M Y', strtotime($StartDate));
	
	$EndDate = date( 'M Y', strtotime('now'));
	$i=0;
						
	
	do {
		$i++;
		$running =date( 'M Y', strtotime($initial.'+'.$i.'months'));
		$options[$running]=$running;
		//echo $running."<br/>";;
	} while ($running != $EndDate);
	
	$options = array_reverse($options);
	
	 $attributes['id'] = 'financialsmonthform';
	 $entryForm = form_open(site_url().'financials', $attributes)."\n";
	$entryForm .="<fieldset>";
		$more = 'id="financialsmonthpicker"';
		$selected=$selecteddate;
		$entryForm .=form_dropdown('financialsdate', $options,$selected,$more);
		$ndata = array('name' => 'submitbutton','value' => 'View', 'id'=>'financialsmonthsubmit');
		$entryForm .= form_submit($ndata)."\n";
	  $entryForm .="</fieldset>";  
	  $entryForm .= form_close()."\n";
	 return $entryForm ;
	}

}



/* End of file financials.php */
/* Location: ./system/application/controllers/billing/financials.php */
?>