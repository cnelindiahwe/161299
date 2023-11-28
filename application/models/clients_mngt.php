<?php class Clients_mngt extends Model {

    function Clients_mngt()
    {
        parent::Model();
    }
	
	
	function AddEntry($options =  array())
	{
		
		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('Company','Code', 'Contact', 'Currency', 'PricePer0Hours', 'PricePer10Hours','PricePer20Hours', 'PricePer30Hours','PricePer40Hours', 'PriceEdits','Retainer','OfficeVersion');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
		
		// Execute the query
		$this->db->insert('zowtrakentries');
		
 		// Return the ID of the inserted row, or false if the row could not be inserted
		return $this->db->insert_id();

	}
	
	function UpdateEntry($options = array())
	{
	}
	
	function DeleteEntry($options = array())
	{
	}
	
	function GetEntry($options = array())
	{

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
	   
		// Add where clauses to query
		$qualificationArray = array('Company','Code', 'Contact', 'Currency', 'PricePer0Hours', 'PricePer10Hours','PricePer20Hours', 'PricePer30Hours','PricePer40Hours', 'PriceEdits','Retainer','OfficeVersion');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
	   
		// If limit / offset are declared (usually for pagination) then we need to take them into account
		//if(isset($options['limit']) &amp;&amp; isset($options['offset'])) $this->db->limit($options['limit'], $options['offset']);
		//else if(isset($options['limit'])) $this->db->limit($options['limit']);
	   
		// sort
		if(isset($options['sortBy'])) $this->db->order_by($options['sortBy'], $options['sortDirection']);
	   
		$query = $this->db->get('zowtrakentries');
		if($query->num_rows() == 0) return false;
	   
		//if(isset($options['userId']) &amp;&amp; isset($options['userEmail']))
		//{
			// If we know that we're returning a singular record, then let's just return the object
		//	return $query->row(0);
		//}
		//else
		//{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		//}
	}
}

?>