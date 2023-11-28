<?php

class Fastersplit2 extends MY_Controller {

	function Fastersplit2()
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
		for ($i = 0; $i <= 6; $i++) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			$ClientList= $this->trakreports->ClientsByDate($options =  array('StartDate'=> $StartDate,'EndDate'=> $EndDate));
			
			echo $this-> _monthSplit($StartDate,$EndDate,$ClientList);
		} 
	}

	function  _monthSplit($StartDate,$EndDate,$ClientList)
	//Basic month loop
	/*{
			$split="<h3>".Date('F Y', strtotime($StartDate))."</h3>";
			foreach ($ClientList as $clientb) {
				$split.="<table>\n<thead><tr><th>".$clientb->Client."</th><th>ScheduledBy</th></thead>\n<tbody>";
				$Output = $this->_getDBTotals($StartDate,$EndDate,$clientb);
				foreach ($Output as $row) {
					if($row['ScheduledBy']="Miguel"){
						
					}
					elseif 
					$split.="<tr><td>". $row['ScheduledBy']."</td><td>".$row['EditedSlides']."</td></tr>";
					
				}
			}
			$split.="</tbody></table>";
			return $split;
	}

	function  _monthArrays($StartDate,$EndDate,$ClientList)
*/	{
			$AllTotals=array();
			foreach ($ClientList as $clientb) {
			 	$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $clientb->Client));
				$Output = $this->_getDBTotals($StartDate,$EndDate,$clientb);
				foreach ($Output as $row) {
					//Apply edit price
					$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
					//Add slides and divide by slides per hour
					$subtotalbooked= $row['NewSlides']+$subtotalbooked;
					$subtotalbooked= $subtotalbooked/5;
					//Add hours to get the total
					$bookedtotal= $subtotalbooked+$row['Hours'];

					$AllTotals[$clientb->Client][$row['ScheduledBy']]['total']=$bookedtotal;
					$AllTotals[$clientb->Client][$row['ScheduledBy']]['span']="(".$row['NewSlides']." N, ".$row['EditedSlides']." E, ".$row['Hours']." H ".$clientdata->PriceEdits.")";
				}
			}
			
			//build table
			$split="<h3>".Date('F Y', strtotime($StartDate))."</h3>";
			$split.="<table>\n<tr><td></td>";
			foreach ($ClientList as $clientb) {
				$split.="<td>".$clientb->Client."</td>";			
			}
			$split.="</tr>\n";
			
			$Partners=array('Miguel','Sunil','Paul');
			foreach($Partners as $Partner)	{
				$split.="<tr>\n";
				$split.="<td>".$Partner."<td>";
				$rowtotal=0;
				foreach ($ClientList as $clientb) {
					if (isset($AllTotals[$clientb->Client][$Partner]['total'])){
						$cell=$AllTotals[$clientb->Client][$Partner]['total']."<span>".$AllTotals[$clientb->Client][$Partner]['span']."</span>";	
					}
					else{
						$cell="-";
					}
					$split.="<td>".$cell."</td>";	
				}
				$split.="<tr>\n";		
			}
		
			$split.="</table>";
			return $split;
	}



	function  _getDBTotals($StartDate,$EndMonth,$clientb)
	{


		
		  //Get BOOKED totals from db
		  $this->db->select('ScheduledBy');
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->group_by("ScheduledBy");
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