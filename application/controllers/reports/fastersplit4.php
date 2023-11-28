<?php

class Fastersplit4 extends MY_Controller {

	function Fastersplit4()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		

		$this->load->helper(array('form','url','reports'));

		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		
		//Get day 15 of current month
		$templateVars['pageOutput'] ="";

 		$now = strtotime(date('Y-m-15'));
		for ($i = 0; $i <= 0; $i++) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			$ClientList= $this->trakreports->ClientsByDate($options =  array('StartDate'=> $StartDate,'EndDate'=> $EndDate,'SortBy'=> 'Client', 'sortDirection'=> 'Desc'));
			$templateVars['pageOutput'].=$this-> _monthSplit($StartDate,$EndDate,$ClientList);
		}	
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Billing";
		$templateVars['pageType'] = "billing";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtraktemplate');
				 
	}

	function  _monthSplit($StartDate,$EndDate,$ClientList)
	{
			$AllTotals=array();
			$SummaryTotals=array();
			$split="<h3>".Date('F Y', strtotime($StartDate))."</h3>";
			
			
			$StatusType=array('ScheduledBy','WorkedBy','ProofedBy','CompletedBy'); 
			foreach($StatusType as $Status) {

				foreach ($ClientList as $clientb) {
					$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $clientb->Client));
					$Output = $this->_getDBTotals($StartDate,$EndDate,$clientb,$Status);
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
		$split.=$this->_buildSplitTables($AllTotals,$StartDate,$Status,$ClientList);
		return $split;
	}
			
	function  _buildSplitTables($AllTotals,$StartDate,$Status,$ClientList)
	{
			
				
				//Build table			
				$split="";
				$Partners=array('Miguel','Sunil','Paul');
				$StatusType=array('ScheduledBy','WorkedBy','ProofedBy','CompletedBy'); 
				foreach($Partners as $Partner)	{
					$split.="<table class=\"financials\">\n<thead><tr><th>".$Partner."</th>";
					foreach($StatusType as $Status) {
						$split.="<th>".$Status."</th>";			
					}
					$split.="<th>Total</th>";
					$split.="</tr></thead>\n";
					$rowtotal=0;

					
					foreach ($ClientList as $clientb) {
						$split.="<tr>\n";
						$split.="<td>".$clientb->Client."</td>";
					foreach($StatusType as $Status) {

								if (isset($AllTotals[$clientb->Client][$Status][$Partner]['total'])){
									$cell=$AllTotals[$clientb->Client][$Status][$Partner]['total']."<span>".$AllTotals[$clientb->Client][$Status][$Partner]['span']."</span>";
									$rowtotal=$rowtotal+$AllTotals[$clientb->Client][$Status][$Partner]['total'];	
								}
								else{
									$cell="-";
								}
								$split.="<td>".$cell."</td>";	
							}
							$split.="<td>".$rowtotal."</td>";
							$split.="</tr>\n";
					}		
				$split.="</table>";
				}
			
				
			
			return $split;
	}




	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>