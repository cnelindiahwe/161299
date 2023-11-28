<?php class Trakclients extends CI_Model {


	
	function AddEntry($options =  array())
	{
		
		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('CompanyName','VATOther','Address','ZIPCode','City','Country','TimeZone','Website',
'ZOWContact', 'ClientContact',
		 'ClientCode', 'BilledBy', 'Currency','BasePrice','VolDiscount1Trigger','VolDiscount1Price','VolDiscount2Trigger','VolDiscount2Price','VolDiscount3Trigger','VolDiscount3Price','VolDiscount4Trigger','VolDiscount4Price','PriceEdits','RetainerHours','RetainerCycle','PaymentDueDate',
		 'OfficeVersion','CustomApps','ClientDir','GroupDir','ClientGuidelines','ZOWGuidelines','OtherGuidelines','Trash','Internal');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
		
		// Execute the query
		$this->db->insert('zowtrakclients');
		
 		// Return the ID of the inserted row, or false if the row could not be inserted
		return $this->db->insert_id();

	}
	
	function UpdateEntry($options = array())
	{

    // required values

		if(!$this->_required(array('ID'), $options)) return false;

		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('CompanyName','VATOther','Address','ZIPCode','City','Country','TimeZone','Website',
		'ZOWContact', 'ClientContact','ClientCode', 'BilledBy', 'Currency','BasePrice','VolDiscount1Trigger','VolDiscount1Price',
		'VolDiscount2Trigger','VolDiscount2Price','VolDiscount3Trigger','VolDiscount3Price','VolDiscount4Trigger',
		'VolDiscount4Price','PriceEdits','RetainerHours','RetainerCycle','PaymentDueDate','OfficeVersion','CustomApps','ClientDir',
		'GroupDir','ClientGuidelines','ZOWGuidelines','OtherGuidelines','Trash','Internal');
				
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}



	   // Execute the query
		$this->db->where('ID', $options['ID']);
		$this->db->update('zowtrakclients');
		// Return the number of rows updated, or false if the row could not be inserted
		return $this->db->affected_rows();
		}

	
	function DeleteEntry($options = array())
	{
   	 	// required values
		if(!$this->_required(array('ID'), $options)) return false;
	   // Execute the query
		$this->db->where('ID', $options['ID']);
		$this->db->delete('zowtrakclients');
		}
	
	function GetEntry($options = array())
	{

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
	   
		// Add where clauses to query
		$qualificationArray = array('ID','CompanyName','VATOther','Address','ZIPCode','City','Country','TimeZone','Website',
'ZOWContact', 'ClientContact', 
		'ClientCode', 'BilledBy', 'Currency','BasePrice','VolDiscount1Trigger','VolDiscount1Price','VolDiscount2Trigger','VolDiscount2Price','VolDiscount3Trigger','VolDiscount3Price','VolDiscount4Trigger','VolDiscount4Price','PriceEdits','RetainerHours','RetainerCycle','PaymentDueDate',
		 'OfficeVersion','CustomApps','ClientDir','GroupDir','ClientGuidelines','ZOWGuidelines','OtherGuidelines','Trash','Internal');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
	   
		// If limit / offset are declared (usually for pagination) then we need to take them into account
		//if(isset($options['limit']) &amp;&amp; isset($options['offset'])) $this->db->limit($options['limit'], $options['offset']);
		//else if(isset($options['limit'])) $this->db->limit($options['limit']);
	   
		// sort
		if(isset($options['sortBy'])) $this->db->order_by($options['sortBy'], $options['sortDirection']);
	   
		$query = $this->db->get('zowtrakclients');
		
		if($query->num_rows() == 0) return false;

		//if(isset($options['ID']) OR isset($options['CompanyName']) ) {
	    if(isset($options['ID']) || isset($options['CompanyName']) || isset($options['ClientCode']) ) {
	   
	    //http://stackoverflow.com/questions/8784584/if-isset-multiple-or-conditions 
			return $query->row(0);
		}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}
	}


	// ------------------------------------------------------------------------
	/**
	* GetNewClients gives number of new clients between dates
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/
	function GetNewClients($options = array())
	{
		  	
		  if(!$this->_required(array('StartDate','EndDate'), $options)) return false;
		  $this->_Addfirstclientiteration($options1 = array());
		  $this->db->where('FirstClientIteration >=', $options['StartDate']);
		  $this->db->where('FirstClientIteration <= ', $options['EndDate']);
		  $this->db->where('Trash =',0);
		  $query = $this->db->get('zowtrakclients');
		  return $query->result();
	}

	// ------------------------------------------------------------------------
	/**
	* _Addfirstclientiteration updates first interaction field in clients table
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/

	function _Addfirstclientiteration($options = array())
	{

		//add edits multuplier to entries

		$this->load->model('trakclients', '', TRUE);
		$this->load->model('trakentries', '', TRUE);
		$clientdata= $this->trakclients->GetEntry($options = array('Trash' => 0,'FirstClientIteration'=>"0000-00-00" ));
		foreach ($clientdata as $client) {
			//if ($client->FirstClientIteration!="0000-00-00") {
				$agequery = "SELECT MIN(DateOut) AS Firstdate  FROM zowtrakentries WHERE Trash = 0 AND Client='".$client->CompanyName."'";
				$monthsage =$this->db->query($agequery);	
				
				$this->db->set('FirstClientIteration',$monthsage->row()->Firstdate);
				$this->db->where('ID =', $client->ID);
				$this->db->update('zowtrakclients');
			//}	
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