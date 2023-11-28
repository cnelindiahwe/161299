<?php class Zt2016_users_model extends CI_Model {

	################ Get client
	
	function GetUser($options = array())
	{

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
	   
		// Add where clauses to query
		$qualificationArray = array('user_id','user_email','user_pass','user_date','user_modified','user_last_login');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
		$this->db->where('status',0);
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
	   
		$query = $this->db->get('users');
		
		if($query->num_rows() == 0) return false;

	    //http://stackoverflow.com/questions/8784584/if-isset-multiple-or-conditions 
	    if(isset($options['user_id']) || isset($options['user_email'])) {
	   
			return $query->row(0);
		}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}
	}
	function GetUser_ascfname_nonstatus($options = array()){

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
		$options['sortBy']='fname';
		// Add where clauses to query
		$qualificationArray = array('user_id','user_email','user_pass','user_date','user_modified','user_last_login');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
// 		$this->db->where('status',0);
// 		$this->db->where('visibility',1);
	   
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
	   
		$query = $this->db->get('users');
		
		if($query->num_rows() == 0) return false;

	    //http://stackoverflow.com/questions/8784584/if-isset-multiple-or-conditions 
	    if(isset($options['user_id']) || isset($options['user_email'])) {
	   
			return $query->row(0);
		}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}
	}
	function GetUser_ascfname($options = array())
	{

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
		$options['sortBy']='fname';
		// Add where clauses to query
		$qualificationArray = array('user_id','user_email','user_pass','user_date','user_modified','user_last_login');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
		$this->db->where('status',0);
		$this->db->where('visibility',1);
	   
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
	   
		$query = $this->db->get('users');
		
		if($query->num_rows() == 0) return false;

	    //http://stackoverflow.com/questions/8784584/if-isset-multiple-or-conditions 
	    if(isset($options['user_id']) || isset($options['user_email'])) {
	   
			return $query->row(0);
		}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}
	}
function getsuer_name_by_id($id){
	$query = $this->db->select("fname");
	$query = $this->db->where("user_id",$id);
            $this->db->from('users');
            $query=$this->db->get();
			
		return $query->row(0);
		
}
function getsuer_name_by_string($str){
	
	$query = $this->db->select("fname");
	$query = $this->db->where("fname",$str);
            $this->db->from('users');
            $query=$this->db->get();
			
			return $query->row(0);
}
function getsuer_id_by_name($str){
	
	$query = $this->db->select("user_id");
	$query = $this->db->where("fname",$str);
            $this->db->from('users');
            $query=$this->db->get();
			
		return $query->row(0);
}


function getsuer_visibility($id){
	
	$query = $this->db->select("user_type");
	$query = $this->db->where("user_id",$id);
            $this->db->from('users');
            $query=$this->db->get();
			
		return $query->row(0);
}


function get_user_profile($id){
	
	$query = $this->db->select("dp");
	$query = $this->db->where("user_id",$id);
            $this->db->from('users');
            $query=$this->db->get();
			
		return $query->row(0);
}

	################ Update user
	
	function UpdateUser($options = array())
	{

    // required values

		if(!$this->_required(array('user_id'), $options)) return false;

		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('user_id','user_email','user_pass','user_date','user_modified','user_last_login','fname');
				
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}



	   // Execute the query
		$this->db->where('user_id', $options['user_id']);
		$this->db->update('users');
		// Return the number of rows updated, or false if the row could not be inserted
		return $this->db->affected_rows();
		}
		function UpdateUser_data($options = array())
		{
	
		// required values
	
			if(!$this->_required(array('user_id'), $options)) return false;
	
			// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
			$qualificationArray = array('user_id','user_email','user_pass','fname','lname','user_type','visibility','dp');
					
			foreach($qualificationArray as $qualifier)
			{
				if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
			}
		
	 
		   // Execute the query
			$this->db->where('user_id', $options['user_id']);
			$this->db->update('users');
			// Return the number of rows updated, or false if the row could not be inserted
			return $this->db->affected_rows();
			
			}
	################ add user
	
	function AddUser($options =  array())
	{
		
		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('user_id','user_email','user_pass','user_date','user_modified','user_last_login','fname','lname','dp');
		
		foreach($qualificationArray as $qualifier)
		{

			
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
		
		// Execute the query
		$this->db->insert('users');
		
 		// Return the ID of the inserted row, or false if the row could not be inserted
		return $this->db->insert_id();

	}

	################ Delete user
	
	function deleteUser($options = array())
	{
   	 	// required values
		if(!$this->_required(array('user_id'), $options)) return false;
	   // Execute the query

	   $this->db->set('status', 1);
		$this->db->where('user_id', $options['user_id']);
		$this->db->update('users');
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

	function get_single_user($id){
	 $this->db->where('user_id',$id);
	 $data = $this->db->get('users');
	return $data->result();
	}



}
?>