<?php class Zt2016_reports_model extends CI_Model {

	
	
	
	
	
	// ------------------------------------------------------------------------

	/**
	* _NumJobsByDate gives number of jobs between dates
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/
	function  _NumJobsByDate($options = array())
	{

		  //Get BOOKED totals from db
		  $this->db->where('DateOut >=', $options['StartDate']);
		  $this->db->where('DateOut <= ', $options['EndDate']);
		  $this->db->where('Trash =',0);
			if (isset($options['Client'])) {
				$this->db->where('Client',  $options['Client']);
			}
			if (isset($options['Originator'])) {
				$this->db->where('Originator',  $options['Originator']);
			}
			if (isset($options['WorkType'])) {
				if ($options['WorkType']!="") {
					$this->db->where('WorkType',  $options['WorkType']);
				}
			}
		  $this->db->from('zowtrakentries');
 		  return $this->db->count_all_results();

	}
	
	
	
	
	/**
	* ClientHistorical
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/

	function _ClientHistorical($options =  array())
	{
		if(!$this->_required(array('Client','CurrentMonth'), $options)) return false;
		
		$StartDate = date( 'Y-m-d', strtotime('first day of '.$options['CurrentMonth']));
		$EndDate = date( 'Y-m-d', strtotime('last day of '.$options['CurrentMonth']));
		//echo $StartDate." - ".$EndDate. "<br/>";
		
		
		 
		$historical['month'] = date( 'M Y', strtotime( $StartDate));
		//Get billed totals from db
		$this->db->select_sum('Hours','Hours');
		$this->db->select_sum('NewSlides','NewSlides');
		$this->db->select_sum('EditedSlides','EditedSlides');
		$this->db->select_sum('InvoiceTime','InvoiceTime');
		$this->db->from('zowtrakentries');
		$this->db->where('Client', $options['Client']->CompanyName);
		$this->db->where('DateOut >=', $StartDate);
		$this->db->where('DateOut <= ', $EndDate);
		$this->db->where('Trash =',0);
		$this->db->where('Invoice !=','NOT BILLED');
		$query = $this->db->get();

		$historical['total'] = $query->row()->InvoiceTime;
		$historical['newslides'] = $query->row()->NewSlides;
		$historical['editslides'] = $query->row()->EditedSlides;
		$historical['hours'] = $query->row()->Hours;


	 	$this->db->from('zowtrakentries');
	  	$this->db->where('Client', $options['Client']->CompanyName);
	  	$this->db->where('DateOut >=', $StartDate);
	  	$this->db->where('DateOut <= ', $EndDate);
	  	$this->db->where('Trash =',0);
	  	$query2 = $this->db->get();
		
		$historical['jobs'] = $query2->num_rows();;
			
		return $historical;	
			
	}		

// ------------------------------------------------------------------------

	/**
	* ClientsByDate gives number of clients between dates
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/
	function ClientsByDate($options =  array())
	{
		 
		 if (!isset($options['Booked'])) {$options['Booked']=0;}
		 
		  $this->db->distinct();
		  $this->db->select('Client');
		  
		 

		  $this->db->where('DateOut >=', $options['StartDate']);
		  $this->db->where('DateOut <= ', $options['EndDate']);
		  $this->db->where('Trash =',0);
		  if($options['Booked']==1) {
		   	$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status = 'WAIVED' OR Status = 'MARKETING')";
			$this->db->where($where);
		  }
			if (isset($options['WorkType'])) {
				$this->db->where('WorkType',  $options['WorkType']);
			}
			if (isset($options['Currency'])) {
				$this->db->where('Currency',  $options['Currency']);
			}
		  $this->db->from('zowtrakentries');
		  $this->db->order_by('Client', 'Asc');
		  $query = $this->db->get();
		   
		  return $query->result();

	}
	
// ------------------------------------------------------------------------

	/**
	* OriginatorsByDate gives number of clients between dates
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/
	function OriginatorsByDate($options =  array())
	{
		 
		 if (!isset($options['Booked'])) {$options['Booked']=0;}
		 
		  $this->db->distinct();
		  $this->db->select('Originator');
		  
		 

		  $this->db->where('DateOut >=', $options['StartDate']);
		  $this->db->where('DateOut <= ', $options['EndDate']);
		  $this->db->where('Trash =',0);
		  if($options['Booked']==1) {
		   	$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status = 'WAIVED' OR Status = 'MARKETING')";
			$this->db->where($where);
		  }
			if (isset($options['WorkType'])) {
				$this->db->where('WorkType',  $options['WorkType']);
			}
			if (isset($options['Currency'])) {
				$this->db->where('Currency',  $options['Currency']);
			}
		  $this->db->from('zowtrakentries');
		  $this->db->order_by('Originator', 'Asc');
		  $query = $this->db->get();
		   
		  return $query->result();

	}



	/**
	* _getAllClientTotalsByDate gives totals for all clients between datess
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/

	function  _getAllClientTotalsByDate($options = array())
	{

		 if (!isset($options['Booked'])) {$options['Booked']=0;}
		
		  //Get BOOKED totals from db
		  $this->db->select('Client');
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->group_by('Client');
		  
		  if($options['Booked']==1) {
		   		$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status = 'WAIVED' OR Status = 'MARKETING')";
				$this->db->where($where);
		  }
			elseif ($options['Booked']==2) {
				$this->db->where('Invoice','NOT BILLED');
				$this->db->where('Status','COMPLETED');
		  }
			elseif ($options['Booked']==3) {
				$this->db->where('Invoice','NOT BILLED');
				$this->db->where("Status != 'TENTATIVE'");
		  }

			if (isset($options['WorkType'])) {
				$this->db->where('WorkType',  $options['WorkType']);
			}
		  $this->db->where('DateOut >=',  $options['StartDate']);
		  $this->db->where('DateOut <= ',  $options['EndDate']);
		  $this->db->where('Trash =',0);
		  $this->db->from('zowtrakentries');
		  $querybooked = $this->db->get();
		  //echo $this->db->last_query();
		  //return $querybooked->result();
 		  return $querybooked->result_array();

	}	


	/**
	* _getAllClientBilledTotalsByDate gives billed totals for all clients between datess
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/

	function  _getAllClientBilledTotalsByDate($options = array())
	{

		 if (!isset($options['Booked'])) {$options['Booked']=0;}
		
		  //Get BOOKED totals from db
		  $this->db->select('Client');
		  $this->db->select_sum('InvoiceTime','InvoiceTime');
		  $this->db->select_sum('InvoiceEntryTotal','InvoiceEntryTotal');
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
			$this->db->where('Invoice !=','NOT BILLED');
		  $this->db->where('DateOut >=',  $options['StartDate']);
		  $this->db->where('DateOut <= ',  $options['EndDate']);
		  $this->db->where('Trash =',0);
			if (isset($options['WorkType'])) {
				if ($options['WorkType']!="") {
					$this->db->where('WorkType',  $options['WorkType']);
				}
			}
		  $this->db->group_by('Client');
		  $this->db->from('zowtrakentries');
		  $querybooked = $this->db->get();
		  //echo $this->db->last_query();
		  //return $querybooked->result();
 		  return $querybooked->result_array();

	}			

	/**
	* _getAllClientCompletedTotalsByDate gives Completed totals for all clients between datess
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/

	function  _getAllClientCompletedTotalsByDate($options = array())
	{

		 if (!isset($options['Booked'])) {$options['Booked']=0;}
		
		  //Get BOOKED totals from db
		  $this->db->select('Client');
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
			$this->db->where('Invoice','NOT BILLED');
			$this->db->where('Status','COMPLETED');
		  $this->db->where('DateOut >=',  $options['StartDate']);
		  $this->db->where('DateOut <= ',  $options['EndDate']);
		  $this->db->where('Trash =',0);
			if (isset($options['WorkType'])) {
				if ($options['WorkType']!="") {
					$this->db->where('WorkType',  $options['WorkType']);
				}
			}
		  $this->db->group_by('Client');
		  $this->db->from('zowtrakentries');
		  $querybooked = $this->db->get();
		  //echo $this->db->last_query();
		  //return $querybooked->result();
 		  return $querybooked->result_array();

	}			
	/**
	* _getAllClientEllapsedTotalsByDate gives booked totals for all clients between datess
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/

	function  _getAllClientEllapsedTotalsByDate($options = array())
	{

		 if (!isset($options['Booked'])) {$options['Booked']=0;}
		
		  //Get BOOKED totals from db
		  $this->db->select('Client');
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
			//$this->db->where('Invoice','NOT BILLED');
			$where = "( status = 'SCHEDULED' OR status = 'IN PROGRESS' OR status = 'IN PROOFING' )";
			$this->db->where($where);
		  $this->db->where('DateOut >=',  $options['StartDate']);
		  $this->db->where('DateOut <= ',  $options['EndDate']);
		  $this->db->where('Trash =',0);
			if (isset($options['WorkType'])) {
				if ($options['WorkType']!="") {
					$this->db->where('WorkType',  $options['WorkType']);
				}
			}
		  $this->db->group_by('Client');
		  $this->db->from('zowtrakentries');
		  $querybooked = $this->db->get();
		//echo $this->db->last_query();
		 //return $querybooked->result();
	  return $querybooked->result_array();

	}			
	/**
	* _getClientTotalsByDate gives totals for a single client and status between datess
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/	
	function  _getClientTotalsByDate($StartDate,$EndMonth,$clientb,$Status,$Booked)
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
		  if($Booked==1) {
		   	$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status = 'WAIVED' OR Status = 'MARKETING')";
				$this->db->where($where);
		  }
		  else if($Booked==2) {
				$this->db->where('Invoice','NOT BILLED');
				$this->db->where('Status','COMPLETED');
		  }
		  $this->db->where('Trash =',0);
		  $this->db->from('zowtrakentries');
		  $querybooked = $this->db->get();
		  return $querybooked->result_array();
	}
	/**
	* _getClientTotalsByDate gives billed totals for a single client and status between datess
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/	
	function  _getClientBilledTotalsByDate($StartDate,$EndMonth,$clientb,$Status)
	{
		  //Get BOOKED totals from db
		  $this->db->select($Status);
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
			$this->db->select_sum('InvoiceEntryTotal','InvoiceEntryTotal');
		  $this->db->group_by($Status);
		  $this->db->where('Client',$clientb->Client);
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndMonth);
			$this->db->where('Invoice!=','NOT BILLED');
		  $this->db->where('Trash =',0);
		  $this->db->from('zowtrakentries');
		  $querybooked = $this->db->get();
		  return $querybooked->result_array();
	}
	/**
	* _getPartnersByDate gives totals for partners between dates
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/
	function  _getPartnersByDate($StartDate,$EndMonth)
	{
		  //Get BOOKED totals from db

		  
		  $P1='SELECT ScheduledBy FROM zowtrakentries WHERE DateOut >="' .$StartDate.'" AND DateOut <="'.$EndMonth.'" AND Trash="0" AND (Status="COMPLETED" OR Status="BILLED" OR Status="PAID")'; 
		  $P1.=' UNION SELECT WorkedBy FROM zowtrakentries WHERE DateOut >= "'.$StartDate.'" AND DateOut <="'.$EndMonth.'" AND Trash="0" AND (Status="COMPLETED" OR Status="BILLED" OR Status="PAID")'; 
		  $P1.=' UNION SELECT ProofedBy FROM zowtrakentries WHERE DateOut >= "' .$StartDate.'" AND DateOut <="'.$EndMonth.'" AND Trash="0" AND (Status="COMPLETED" OR Status="BILLED" OR Status="PAID")' ; 
		  $P1.=' UNION SELECT CompletedBy FROM zowtrakentries WHERE DateOut >= "'.$StartDate.'" AND DateOut <="'.$EndMonth.'" AND Trash="0" AND (Status="COMPLETED" OR Status="BILLED" OR Status="PAID")'; 
		  $querybooked =$this->db->query ( $P1);
		  //echo  $querybooked->num_rows();
		  // echo  $this->db->last_query();
		  return $querybooked->result_array();
	}

	
	/**
	* _required method returns false if the $data array does not contain all of the keys assigned by the $required array.
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/
	function _required($required, $data)
	{
		foreach($required as $field) if(!isset($data[$field])) return false;
		return true;
	}


	/**
	* _getAllClientBilledTotalsByDate gives billed totals for all clients between datess
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/

	function  _getAllOriginatorsBilledTotalsByDate($options = array())
	{

		 if (!isset($options['Booked'])) {$options['Booked']=0;}
		
		  //Get BOOKED totals from db
		  $this->db->select('Client');
		  $this->db->select('Originator');
		  $this->db->select_sum('InvoiceTime','InvoiceTime');
		  $this->db->select_sum('InvoiceEntryTotal','InvoiceEntryTotal');
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		
			if ($options['Booked']==1){
				$this->db->where('Invoice !=','NOT BILLED');
			} 

			elseif ($options['Booked']==2){
				$this->db->where('Invoice','NOT BILLED');
			}

		  $this->db->where('DateOut >=',  $options['StartDate']);
		  $this->db->where('DateOut <= ',  $options['EndDate']);
		  $this->db->where('Trash =',0);
			if (isset($options['WorkType'])) {
				if ($options['WorkType']!="") {
					$this->db->where('WorkType',  $options['WorkType']);
				}
			}
		  $this->db->group_by('Originator');
		  $this->db->from('zowtrakentries');
		  $querybooked = $this->db->get();
		  
		  //$month_date['query']= $this->db->last_query();
		  //$month_date['result']=$querybooked->result_array(); 
  		  // return  $month_date;
   
		  return $querybooked->result();
	}			

	
	


}

?>