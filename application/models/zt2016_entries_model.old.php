<?php class zt2016_entries_model extends CI_Model {

		// ################## Add Entry ##################	
	function AddEntry($options =  array())
	{
		
		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('Client','Code', 'DateIn','TimeIn','TimeZoneIn','DateOut','TimeOut','ScheduledDateOut','ScheduledTimeOut','TimeZoneOut', 'Originator', 'NewSlides', 'EditedSlides','Hours','RealTime','WorkType','FileName','ProjectName','Status','MonthPrice','Currency','JobPrice','HoursBilled','Invoice','Trash','TentativeBy','ScheduledBy','WorkedBy','ProofedBy','CompletedBy','BilledBy','PaidBy','EntryNotes');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
		
		// Execute the query
		$this->db->insert('zowtrakentries');
		
 		// Return the ID of the inserted row, or false if the row could not be inserted
		return $this->db->insert_id();

	}
	
	
		// ################## Update Entry ##################	
	
	function UpdateEntry($options = array())
	{

    // required values

		if(!$this->_required(array('id'), $options)) return false;

		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('id','Client','Code',  'DateIn','TimeIn','TimeZoneIn','DateOut','TimeOut','ScheduledDateOut','ScheduledTimeOut','TimeZoneOut',  'Originator', 'NewSlides', 'EditedSlides','Hours','RealTime','WorkType','FileName','ProjectName', 'NewDate','Status','MonthPrice','Currency','JobPrice','HoursBilled','Invoice','Trash','TentativeBy','ScheduledBy','WorkedBy','ProofedBy','CompletedBy','BilledBy','PaidBy','EntryNotes');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}



	   // Execute the query
		$this->db->where('id', $options['id']);
		$this->db->update('zowtrakentries');
		// Return the number of rows updated, or false if the row could not be inserted
		return $this->db->affected_rows();
		}


		// ################## Delete Entry ##################	
	
	function DeleteEntry($options = array())
	{
   	 	// required values
		if(!$this->_required(array('id'), $options)) return false;
	   // Execute the query
		$this->db->where('id', $options['id']);
		$this->db->delete('zowtrakentries');
		return $this->db->affected_rows();
	}



		// ################## Get Entry ##################	

	function GetEntry($options = array())
	{

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
	   
		// Add where clauses to query
		$qualificationArray = array('id','Client','Code',  'DateIn','TimeIn','TimeZoneIn','DateOut','TimeOut','ScheduledDateOut','ScheduledTimeOut','TimeZoneOut',  'Originator', 'NewSlides', 'EditedSlides','Hours','RealTime','WorkType','FileName','ProjectName','Status','MonthPrice','Currency','JobPrice','HoursBilled','Invoice','Trash','TentativeBy','ScheduledBy','WorkedBy','ProofedBy','CompletedBy','BilledBy','PaidBy','EntryNotes');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
			if(isset($options[$qualifier." !="])) $this->db->where($qualifier." !=", $options[$qualifier." !="]);
		}

	 //If limit / offset are declared (usually for pagination) then we need to take them into account
		if(isset($options['limit']) && isset($options['offset'])) $this->db->limit($options['limit'], $options['offset']);
		else if(isset($options['limit'])) $this->db->limit($options['limit']);
	   
		// sort
		if(isset($options['sortBy'])) {
			$this->db->order_by($options['sortBy'], $options['sortDirection']);
		}	
	   
		$query = $this->db->get('zowtrakentries');
		if($query->num_rows() == 0) return false;
	   
		if(isset($options['id']) )
		{
			//If we know that we're returning a singular record, then let's just return the object
			return $query->row(0);
			
		}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}
	}


	/**
	* _required method returns false if the $data array does not contain all of the keys assigned by the $required array.
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/
		function GetCompletedEntries($options = array())
	{

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
	   
		// Add where clauses to query
		$qualificationArray = array('id','Client','Code', 'DateIn','TimeIn','TimeZoneIn','DateOut','TimeOut','ScheduledDateOut','ScheduledTimeOut','TimeZoneOut',  'Originator', 'NewSlides', 'EditedSlides','Hours','RealTime','WorkType','FileName','ProjectName','MonthPrice','Currency','JobPrice','HoursBilled','Invoice','Trash','TentativeBy','ScheduledBy','WorkedBy','ProofedBy','CompletedBy','BilledBy','PaidBy','EntryNotes');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
			if(isset($options[$qualifier." !="])) $this->db->where($qualifier." !=", $options[$qualifier." !="]);
		}
		$status= array('COMPLETED', 'BILLED', 'PAID','MARKETING','WAIVED');
		$this->db->where_in('Status',$status);
	 //If limit / offset are declared (usually for pagination) then we need to take them into account
		if(isset($options['limit']) && isset($options['offset'])) $this->db->limit($options['limit'], $options['offset']);
		else if(isset($options['limit'])) $this->db->limit($options['limit']);
	   
		// sort
		if(isset($options['sortBy'])) $this->db->order_by($options['sortBy'], $options['sortDirection']);
	   
	   // sort by newest
		if(isset($options['sortByNew'])) {
			$this->db->order_by('DateOut','desc');
			$this->db->order_by('TimeOut','desc');
			}
	   
		$query = $this->db->get('zowtrakentries');
		if($query->num_rows() == 0) return false;
	   
		if(isset($options['id']) )
		{
			//If we know that we're returning a singular record, then let's just return the object
			return $query->row(0);
			
		}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}
	}



	/**
	* _required method returns false if the $data array does not contain all of the keys assigned by the $required array.
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/

	function Get_entries_between_dates($options = array(),$StartDate,$EndDate)
	{

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
	   
		// Add where clauses to query
		$qualificationArray = array('Client','Originator','Status','WorkType');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
		$StartDate=date('Y-m-d', strtotime($StartDate));
		$EndDate=date('Y-m-d', strtotime($EndDate));
		//echo $StartDate."<br/>".$EndDate;
		$this->db->where('DateOut >=', $StartDate);
	    $this->db->where('DateOut <= ', $EndDate);
		$this->db->where('Trash =',0);
		
		// If limit / offset are declared (usually for pagination) then we need to take them into account
		//if(isset($options['limit']) &amp;&amp; isset($options['offset'])) $this->db->limit($options['limit'], $options['offset']);
		//else if(isset($options['limit'])) $this->db->limit($options['limit']);
	   
		// sort
		//if(isset($options['sortBy'])) $this->db->order_by('id', 'desc');
	  $this->db->order_by('DateOut'); 
	  $query = $this->db->get('zowtrakentries');
		
				//echo $this->db->last_query();
		

		if ($query->num_rows() == 0) {
			return false;
		}
	   
		//if(isset($options['id']) )
		//{
			//If we know that we're returning a singular record, then let's just return the object
		//	return $query->row(0);
		//	
		//}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}
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

}

?>