<?php class Zt2016_payment_model extends CI_Model {
    function addpayment($options = array()){

        if(!$this->_required(array('client'), $options)) return false;
		
		$options['date'] = time();
		$qualificationArray = array('invoice','client','date','paymnet_status','payment_type','amount');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
		// echo $this->db->last_query();
		$this->db->insert('zowpaymententries');
		
 		// Return the ID of the inserted row, or false if the row could not be inserted
		return $this->db->insert_id();
    }

	function getpaymententries($options = array()){
		$qualificationArray = array('invoice','client','date','paymnet_status','payment_type','amount');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
		$query = $this->db->get('zowpaymententries');
		
		if($query->num_rows() == 0) return false;

		return $query->result();
	}
	
	function _required($required, $data)
	{
		foreach($required as $field) if(!isset($data[$field])) return false;
		return true;
	}
}
?>