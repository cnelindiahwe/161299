<?php

class Split extends MY_Controller {

	function Split()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		

		$this->load->helper(array('form','url','reports'));

		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));


		//Get first day of current month
		$StartDate = date( 'Y-m-1');
		//Get last day of current month
		$EndDate = date( 'Y-m-t');

		$now = strtotime(date('Y-m-15'));

 

		$templateVars['pageOutput'] = $this->_getMonthTotals($ClientList,$StartDate,$EndDate);
		
		$now = strtotime(date('Y-m-15'));
		for ($i = 1; $i <= 3; $i++) {
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			//echo $StartDate;
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			$templateVars['pageOutput'] .= $this->_getMonthTotals($ClientList,$StartDate,$EndDate);
		} 


		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Billing";
		$templateVars['pageType'] = "billing";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtraktemplate');


	}
	// ################## Generate month totals ##################	
	function  _getMonthTotals($Clientlist,$StartDate,$EndMonth)
	{

		$split =date('M Y', strtotime($StartDate))." ".$StartDate." - ".$EndMonth."<br/>\n";
		$split .= "<table><thead><tr><th>Partner</th><th>Scheduled</th><th>Worked</th><th>Proofed</th><th>Completed</th></tr></thead>\n<tbody>\n";
		$booked =0;
		$ScheduledTotal =0;
		$WorkedTotal =0;
		$ProofedTotal =0;
		$CompletedTotal =0;
		$jobs=0;
		
			$Partners=array('Miguel','Sunil','Paul');
			foreach($Partners as $Partner)
				{
				$Status=array('ScheduledBy','WorkedBy','ProofedBy','CompletedBy');
			   $split .= "<tr><td class=\"partner\">".$Partner."</td>\n"; 
				foreach($Status as $StatusType)
					{
					$booked =0;
					foreach($Clientlist as $clientb)
						{
					
						$querybooked =  $this->_getDBTotals($Clientlist,$StartDate,$EndMonth,$clientb,$StatusType,$Partner);
			  
						if ($querybooked) {  
						  //Get client details from db
						  $this->load->model('trakclients', '', TRUE);
						  $query2 = $this->trakclients->GetEntry($options = array('CompanyName' => $clientb->CompanyName));
				
						  //Apply edit price
						  $subtotalbooked= $querybooked->row()->EditedSlides*$query2->PriceEdits;
						  //Add slides and divide by slides per hour
						   $subtotalbooked= $subtotalbooked+$querybooked->row()->NewSlides;
						   $subtotalbooked= $subtotalbooked/5;
						  //Add hours to get the total
						  $bookedtotal= $subtotalbooked+$querybooked->row()->Hours;
							//if ($bookedtotal!=0){
								//$split .= $clientb->CompanyName." ".$bookedtotal."<br/>\n";
							$booked = $booked + $bookedtotal;
							//}
						 }
					 }
					 $split .= "<td class=\"".$StatusType."\">".$booked."</td>\n";
					 if ($StatusType == "ScheduledBy") {
						$ScheduledTotal =$ScheduledTotal+$booked ;
					} elseif ($StatusType == "WorkedBy") {
						$WorkedTotal =$WorkedTotal+$booked ;
					} elseif ($StatusType == "ProofedBy") {
						$ProofedTotal =$ProofedTotal+$booked ;
					} elseif ($StatusType=="CompletedBy") {
						$CompletedTotal =$CompletedTotal+$booked ;
						$split .= "</tr>";
					}		 
			 	}
			 }
		 $split .= "<tr><td>Total</td>"; 
		 $split .= "<td>".$ScheduledTotal."</td>"; 
		 $split .= "<td>".$WorkedTotal."</td>"; 
		 $split .= "<td>".$ProofedTotal."</td>"; 
		 $split .= "<td>".$CompletedTotal."</td></tr>"; 
		 $split .= "</tbody></table>\n"; 
		 return $split; 
	}

	function  _getDBTotals($Clientlist,$StartDate,$EndMonth,$clientb,$StatusType,$Partner)
	{


		
		  //Get BOOKED totals from db
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$clientb->CompanyName);
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndMonth);
		  $this->db->where($StatusType, $Partner);
		  $this->db->where('Trash =',0);

		  $querybooked = $this->db->get();
		  //echo  $this->db->last_query()."<br/>";
		  return $querybooked;


	}
	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>