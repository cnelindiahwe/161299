<?php class Zt2016_contacts_model extends CI_Model {
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Retrieve existing contact in db
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * GetContact
	 * 
	 */	
	function GetContact($options = array())
	{
		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
	   

		// Add where clauses to query
		$qualificationArray = array('ID','LastName','FirstName','CompanyName','FirstContactIteration','Title','Gender','TimeZone','Email1', 'Email2', 'Cellphone1','Cellphone2','Officephone1','Officephone2','Homephone1','Homephone2','OfficeAddress','OfficeZipcode','OfficeCity','OfficeCountry','HomeAddress','HomeZipcode','HomeCity','HomeCountry','ContactProductionGuidelines','ContactBillingGuidelines','Notes','SocialUrl','Active','Trash');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
		
		// If limit / offset are declared (usually for pagination) then we need to take them into account
		 if(isset($options['limit'])) $this->db->limit($options['limit']);
	   
		// sort
		if(isset($options['sortBy'])) {
			if(isset($options['sortDirection'])) {
				$this->db->order_by($options['sortBy'], $options['sortDirection']);
			} else{
				$this->db->order_by($options['sortBy'], 'asc');
			}
		}

	   
		$query = $this->db->get('zowtrakcontacts');
		if($query->num_rows() == 0) return false;
		
		
	   
		if((isset($options['ID'])) || (isset($options['limit']) && $options['limit']==1))	{
			//If we know that we're returning a singular record, then let's just return the object
			return $query->row(0);
			
		}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}
	}
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Insert contact into db
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * AddContact
	 * 
	 */	
	function AddContact($options =  array())
	{
		
		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('LastName','FirstName','CompanyName','FirstContactIteration','Title','Gender','TimeZone','Email1', 'Email2', 'Cellphone1','Cellphone2','Officephone1','Officephone2','Homephone1','Homephone2','OfficeAddress','OfficeZipcode','OfficeCity','OfficeCountry','HomeAddress','HomeZipcode','HomeCity','HomeCountry','ContactProductionGuidelines','ContactBillingGuidelines','Notes','SocialUrl','Active','Trash');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
		
		// Execute the query
		$this->db->insert('zowtrakcontacts');
		
 		// Return the ID of the inserted row, or false if the row could not be inserted
		return $this->db->insert_id();

	}
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Update existing contact in db
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * UpdateContact
	 *
	 */	
	function UpdateContact($options = array())
	{

    // required values

		if(!$this->_required(array('ID'), $options)) return false;

		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('ID','LastName','FirstName','CompanyName','FirstContactIteration','Title','Gender','TimeZone','Email1', 'Email2', 'Cellphone1','Cellphone2','Officephone1','Officephone2','Homephone1','Homephone2','OfficeAddress','OfficeZipcode','OfficeCity','OfficeCountry','HomeAddress','HomeZipcode','HomeCity','HomeCountry','ContactProductionGuidelines','ContactBillingGuidelines','Notes','SocialUrl','Active','Trash');
				
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}



	   // Execute the query
		$this->db->where('ID', $options['ID']);
		$this->db->update('zowtrakcontacts');
		// Return the number of rows updated, or false if the row could not be inserted
		return $this->db->affected_rows();
		}

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Delete existing contact in db
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * DeleteContact
	 * 
	 */	
	function DeleteContact($options = array())
	{
   	 	// required values
		if(!$this->_required(array('ID'), $options)) return false;
	   // Execute the query
		$this->db->where('ID', $options['ID']);
		$this->db->delete('zowtrakcontacts');
		return $this->db->affected_rows();
		}




	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Search contact db for approximate name
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * GetContactLike
	 * 
	 */
	function GetContactLike($options = array())
	{

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
	   
		// Add where clauses to query
		$qualificationArray = array('ID','LastName','FirstName','CompanyName','FirstContactIteration','Title','Gender','TimeZone','Email1', 'Email2', 'Cellphone1','Cellphone2','Officephone1','Officephone2','Homephone1','Homephone2','OfficeAddress','OfficeZipcode','OfficeCity','OfficeCountry','HomeAddress','HomeZipcode','HomeCity','HomeCountry','ContactProductionGuidelines','ContactBillingGuidelines','Notes','SocialUrl','Active','Trash');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->like($qualifier, $options[$qualifier]);
		}
	   
		// If limit / offset are declared (usually for pagination) then we need to take them into account
		//if(isset($options['limit']) &amp;&amp; isset($options['offset'])) $this->db->limit($options['limit'], $options['offset']);
		//else if(isset($options['limit'])) $this->db->limit($options['limit']);
	   
		// sort
		if(isset($options['sortBy'])) $this->db->order_by($options['sortBy'], $options['sortDirection']);
	   
		$query = $this->db->get('zowtrakcontacts');
		if($query->num_rows() == 0) return false;
	   
		if(isset($options['ID']) )
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


	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get contacts between dates
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * GetNewContacts
	 * 
	 */

	function GetNewContacts($options = array())
	{

		  if(!$this->_required(array('StartDate','EndDate'), $options)) return false;
		  $this->_addfirstcontactiteration($options1 = array());
		  $this->db->where('FirstContactIteration >=', $options['StartDate']);
		  $this->db->where('FirstContactIteration <= ', $options['EndDate']);
		  $this->db->where('Trash =',0);
		  $query = $this->db->get('zowtrakcontacts');
		  return $query->result();
	}

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Batch add first contact iteration date field into existing contacts in db
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _addfirstcontactiteration
	 * 
	 */
	
	function _addfirstcontactiteration ($options = array())
	{
		$this->load->model('trakcontacts', '', TRUE);
		$this->load->model('trakentries', '', TRUE);
		$contactdata= $this->trakcontacts->GetEntry($options = array('Trash' => 0,'FirstContactIteration'=>"0000-00-00"));
		foreach ($contactdata as $contact) {
				$contactname= $contact->FirstName." ".$contact->LastName;
				$agequery = "SELECT MIN(DateOut) AS Firstdate  FROM zowtrakentries WHERE Trash = 0 AND Originator='".$contactname."'";
				
				$monthsage =$this->db->query($agequery);				
				$this->db->set('FirstContactIteration',$monthsage->row()->Firstdate);
				$this->db->where('ID =', $contact->ID);
				$this->db->update('zowtrakcontacts');
		}
		

	}
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Add first contact iteration date field into single existing contact in db
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _addfirstcontactiteration
	 * 
	 */
	function firstcontactiteration ($options = array())
	{

		  if(!$this->_required(array('Originator'), $options)) return false;

		  $this->db->where("CONCAT(`FirstName`,' ',`LastName`)",  $options['Originator']);
		  $this->db->where('FirstContactIteration', "0000-00-00");
		  $this->db->where('Trash =',0);
		  $this->db->set('FirstContactIteration', date("Y-m-d")); 
		  $this->db->update('zowtrakcontacts');
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
	
	// ------------------------------------------------------------------------
	/**
	* searchoriginator provides originator(s) id by search
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/
	function SearchOriginator($options = array())
	{

		  if(!$this->_required(array('Originator'), $options)) return false;		  
		  $this->db->like("CONCAT(`FirstName`,' ',`LastName`)", $options['Originator']);
		  $this->db->where('Trash =',0);
		  // sort
		 if(isset($options['sortBy'])) $this->db->order_by($options['sortBy'], $options['sortDirection']);
		  $query = $this->db->get('zowtrakcontacts');
		  return $query->result();
		 //return $this->db->last_query();
	}


}

?>