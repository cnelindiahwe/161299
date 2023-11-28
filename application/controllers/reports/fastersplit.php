<?php

class Fastersplit extends MY_Controller {

	function Fastersplit()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		

		$this->load->helper(array('form','url','reports'));

		$this->load->model('trakreports', '', TRUE);
		//$ClientList= $this->trakreports->ClientsByDate($options =  array());


		//Get first day of current month
	//	$StartDate = date( 'Y-m-1');
		//Get last day of current month
		//$EndDate = date( 'Y-m-t');
		$templateVars['pageOutput'] ="";

 		$now = strtotime(date('Y-m-15'));
		for ($i = 0; $i <= 6; $i++) {
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			echo $StartDate;
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			$ClientList= $this->trakreports->ClientsByDate($options =  array('StartDate'=> $StartDate,'EndDate'=> $EndDate));
			foreach ($ClientList as $clientb) {
				echo $clientb->Client."<br/>";
				$Output = $this->_getDBTotals($StartDate,$EndDate,$clientb);
				foreach ($Output as $row) {
					echo $row['ScheduledBy'].": ".$row['EditedSlides'];
					echo "<br/>";
				}
			}
		} 
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