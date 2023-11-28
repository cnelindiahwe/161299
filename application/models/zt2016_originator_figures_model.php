<?php class Zt2016_originator_figures_model extends CI_Model {
	
	
	################ add client
	
	function AddOriginatorAnnualData($options =  array())
	{
		
		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('ID','CompanyName','Originator','Year',"BilledHours","Jobs","NewSlides","EditedSlides","AdditionalHours");
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
		
		// Execute the query
		$this->db->insert('zowtrakannualoriginatorfigures');
		
 		// Return the ID of the inserted row, or false if the row could not be inserted
		return $this->db->insert_id();

	}	

	################ Update client
	
	function UpdateOriginatorAnnualData($options = array())
	{

    // required values

		if(!$this->_required(array('ID'), $options)) return false;

		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('ID','CompanyName','Originator','Year',"BilledHours","Jobs","NewSlides","EditedSlides","AdditionalHours");
				
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}



	   // Execute the query
		$this->db->where('ID', $options['ID']);
		$this->db->update('zowtrakannualoriginatorfigures');
		// Return the number of rows updated, or false if the row could not be inserted
		return $this->db->affected_rows();
		}
	
	
	
	################ Get client
	
	function GetOriginatorAnnualData($options = array())
	{

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
	   
		// Add where clauses to query
		$qualificationArray = array('ID','CompanyName','Originator','Year',"BilledHours","Jobs","NewSlides","EditedSlides","AdditionalHours");
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
	   
		// If limit / offset are declared (usually for pagination) then we need to take them into account
		//if(isset($options['limit']) &amp;&amp; isset($options['offset'])) $this->db->limit($options['limit'], $options['offset']);
		//else if(isset($options['limit'])) $this->db->limit($options['limit']);
	   
		// sort
		if(isset($options['sortBy'])) {
			if(isset($options['sortDirection'])) {
				$this->db->order_by($options['sortBy'], $options['sortDirection']);
			} else{
				$this->db->order_by($options['sortBy'], 'asc');
			}
		}
	   
		$query = $this->db->get('zowtrakannualoriginatorfigures');
		
		if($query->num_rows() == 0) return false;

	    //http://stackoverflow.com/questions/8784584/if-isset-multiple-or-conditions 
	    if(isset($options['ID']) || isset($options['CompanyName'])) {
			return $query->row(0);
		}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}
	}



	################ Delete client
	
	function DeleteOriginatorAnnualData($options = array())
	{
   	 	// required values
		if(!$this->_required(array('ID'), $options)) return false;
	   // Execute the query
		$this->db->where('ID', $options['ID']);
		$this->db->delete('zowtrakannualoriginatorfigures');
		return $this->db->affected_rows();
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