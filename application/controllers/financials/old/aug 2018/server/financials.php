<?php

class Financials extends MY_Controller {


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

		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$Worktype="";
		$templateVars['pageOutput'] .= $this->_gettopmenu($StartDate,$Worktype);


		$monthtotals = $this->_calculateMonthPrices($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		$templateVars['pageOutput'] .= _MonthPricestable($monthtotals,$StartDate);
		
		//Month splits
		$ClientList= $this->trakreports->ClientsByDate($options =  array('StartDate'=> $StartDate,'EndDate'=> $EndDate,'Booked'=>1,'SortBy'=> 'Client', 'sortDirection'=> 'Desc'));


		$templateVars['pageOutput'].= $this->_monthSplitnew($StartDate,$EndDate,$ClientList,$monthtotals);

		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "financials";
		$templateVars['pageType'] = "financials";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	// ################## top ##################	

	function  _gettopmenu($StartDate,$Worktype)

	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Financials - Monthly Splits</h1>";
			//$entries .=$this->_getWorktypeDropDown($Worktype);
			$entries .=$this->_dateform($StartDate);
			//Add trends button
			$entries .="<a href=\"".site_url()."financials/fin_trends\">Trends</a>";		
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
						$bookedtotal= number_format( $subtotalbooked+$row['Hours'],2);
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
							$this->db->where('Invoice !=','NOT BILLED');
							$this->db->limit(1);
							$this->db->from('zowtrakentries');
							$invoicedata=$this->db->get();
							$invoiceprice=$invoicedata->row()->InvoicePrice;
							
						if (isset($monthtotal[$row['Client']])){
							
							$monthtotal[$row['Client']]['Total']= number_format($monthtotal[$row['Client']]['Total']+$row['InvoiceTime'],2);
							$monthtotal[$row['Client']]['Ammount']= $monthtotal[$row['Client']]['Ammount']+$row['InvoiceEntryTotal'];
							$monthtotal[$row['Client']]['Price']= $monthtotal[$row['Client']]['Price']." / ".$invoiceprice;
						}
						else {
							$monthtotal[$row['Client']]['Price']= 0;
							foreach ($row as $key => $value) {
								$monthtotal[$row['Client']][$key]= $value;
							}
							$clientdata= $this->trakclients->GetEntry($options2 = array('CompanyName' => $row['Client']));
							$monthtotal[$row['Client']]['Total']= number_format($row['InvoiceTime'],2);
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
					
					//non-billed data
					$Output = $this->trakreports->_getClientTotalsByDate($StartDate,$EndDate,$clientb,$Status,2);
					foreach ($Output as $row) {
						//Apply edit price
						$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
						//Add slides and divide by slides per hour
						$subtotalbooked= $row['NewSlides']+$subtotalbooked;
						$subtotalbooked= $subtotalbooked/5;
						//Add hours to get the total
						$bookedtotal= $subtotalbooked+$row['Hours'];
						$AllTotals[$clientb->Client][$Status][$row[$Status]]['cashtotal']=$bookedtotal*$monthtotals[$clientb->Client]['Price'];
						$AllTotals[$clientb->Client][$Status][$row[$Status]]['total']=$bookedtotal;
						$AllTotals[$clientb->Client][$Status][$row[$Status]]['span']="(".$row['NewSlides']." N, ".$row['EditedSlides']." E, ".$row['Hours']." H)";
					}
					
					//billed data
					$this->db->select($Status);
					$this->db->select_sum('InvoiceEntryTotal','PartnerTotal');
					$this->db->select_sum('InvoiceTime','InvoiceTime');
					$this->db->select_sum('Hours','BilledHours');
					$this->db->select_sum('NewSlides','BilledNewSlides');
					$this->db->select_sum('EditedSlides','BilledEditedSlides');
					$this->db->where('Client', $clientb->Client);
					$this->db->where('DateOut >=', $StartDate);
					$this->db->where('DateOut <= ', $EndDate);
					$this->db->where('Trash =',0);
					$this->db->where("Invoice != 'NOT BILLED'");
					$this->db->group_by($Status);
					$billedquery=$this->db->get('zowtrakentries');
					$billed=$billedquery->result_array();
					
					foreach ($billed as $partner) {
						if (!isset($AllTotals[$clientb->Client][$Status][$partner[$Status]]['total'])) {
						
							$AllTotals[$clientb->Client][$Status][$partner[$Status]]['cashtotal']=$partner['PartnerTotal'];
							$AllTotals[$clientb->Client][$Status][$partner[$Status]]['total']=number_format($partner['InvoiceTime'],1);
							$AllTotals[$clientb->Client][$Status][$partner[$Status]]['span']="(".$partner['BilledNewSlides']." N, ".$partner['BilledEditedSlides']." E, ".$partner['BilledHours']." H)";
							
						}
						else
						{
							$AllTotals[$clientb->Client][$Status][$partner[$Status]]['cashtotal']=$AllTotals[$clientb->Client][$Status][$partner[$Status]]['cashtotal']+$partner['PartnerTotal'];
							$AllTotals[$clientb->Client][$Status][$partner[$Status]]['total']=$AllTotals[$clientb->Client][$Status][$row[$Status]]['total']+number_format($partner['InvoiceTime'],1);
							$AllTotals[$clientb->Client][$Status][$partner[$Status]]['span']="(".($row['NewSlides']+$partner['BilledNewSlides'])." N, ".($row['EditedSlides']+$partner['BilledEditedSlides'])." E, ".($row['Hours']+$partner['BilledHours'])." H)";
						}	
					}
							
							

				}									
				
		}
		$array=$this->trakreports-> _getPartnersByDate($StartDate,$EndDate);
		$i = 0;
		foreach ($array as $row) {
			$Partners[$i]=$row['ScheduledBy'];
			$i++;
		} 
		
		//Bellow fix for before there is an entry by Miguel crashes java script
		/*
		 * if (!in_array("Miguel", $Partners )){
			$Partners[$i]="Miguel";
		}
		*/
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
					$Output = $this->trakreports->_getClientTotalsByDate($StartDate,$EndDate,$clientb,$Status,1);
					foreach ($Output as $row) {
						//Apply edit price
						$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
						//Add slides and divide by slides per hour
						$subtotalbooked= $row['NewSlides']+$subtotalbooked;
						$subtotalbooked= $subtotalbooked/5;
						//Add hours to get the total
						$bookedtotal= $subtotalbooked+$row['Hours'];

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


// ------------------------------------------------------------------------

/**
 * _getWorktypeDropDown
 *
 * Creates worktype selector dropdown
 *
 * @access	public
 * @return	string
 */


	function  _getWorktypeDropDown($Worktype="")
	{
		$attributes['id'] = 'worktypecontrol';
			$WorktypeDropDown= form_open(site_url()."financials",$attributes);

			
			//dropdown
				$options=array(''=>"All",'Office'=>"Office",'Non-Office'=>"Non-Office");
				$more = 'id="WorkType" class="WorkType"';
				$WorktypeDropDown .=form_label('Work type:','WorkType');
				$WorktypeDropDown .=form_dropdown('WorkType', $options,$Worktype,$more);
				$more = 'id="worktypesubmit" class="worktypesubmit"';			
				$WorktypeDropDown .=form_submit('worktypesubmit', 'View',$more);
				$WorktypeDropDown .= form_close()."\n";

		return $WorktypeDropDown;	


	}

/* End of file financials.php */
/* Location: ./system/application/controllers/billing/financials.php */
}

?>