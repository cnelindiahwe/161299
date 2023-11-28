<?php

class Fastersplit3 extends MY_Controller {

	function Fastersplit3()
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

						$AllTotals[$clientb->Client][$row[$Status]]['total']=$bookedtotal;
						$AllTotals[$clientb->Client][$row[$Status]]['span']="(".$row['NewSlides']." N, ".$row['EditedSlides']." E, ".$row['Hours']." H)";
					}
				}
				$split.=$this->_buildSplitTables($AllTotals,$StartDate,$Status,$ClientList);
				
		}
		return $split;
	}
			
	function  _buildSplitTables($AllTotals,$StartDate,$Status,$ClientList)
	{
			
				
				//Build table			
				$split="<table>\n<tr><th>".$Status."</th>";
				foreach ($ClientList as $clientb) {
					$split.="<th>".$clientb->Client."</th>";			
				}
				$split.="<th>Total</th>";
				$split.="</tr>\n";
				
				$Partners=array('Miguel','Sunil','Paul');
				foreach($Partners as $Partner)	{
					$split.="<tr>\n";
					$split.="<td>".$Partner."</td>";
					$rowtotal=0;
					foreach ($ClientList as $clientb) {
						if (isset($AllTotals[$clientb->Client][$Partner]['total'])){
							$cell=$AllTotals[$clientb->Client][$Partner]['total']."<span>".$AllTotals[$clientb->Client][$Partner]['span']."</span>";
							$rowtotal=$rowtotal+$AllTotals[$clientb->Client][$Partner]['total'];	
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
			
			return $split;
	}



	function  _getDBTotals($StartDate,$EndMonth,$clientb,$Status)
	{


		
		  //Get BOOKED totals from db
		  $this->db->select($Status);
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->group_by($Status);
		  $this->db->where('Client',$clientb->Client);
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndMonth);
		  //$this->db->where($StatusType, $Partner);
		  $this->db->where('Trash =',0);
		  $this->db->from('zowtrakentries');
		  $querybooked = $this->db->get();
		  return $querybooked->result_array();


	}
	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>