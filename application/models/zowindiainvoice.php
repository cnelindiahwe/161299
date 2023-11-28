<?php
class zowindiainvoice extends CI_Model {
    function addinvoice($options = array()){
        
        if(!$this->_required(array('invoiceNumber'), $options)) return false;
      
        $options['date'] = date("Y-m-d");
        $qualificationArray = array('client','invoiceNumber','address','description','hour','rate','amount','date','currency');
        foreach($qualificationArray as $qualifier)
        {
            if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
        }

        $this->db->insert('zowindiainvoice');

        return $this->db->insert_id();

    }
    function getinvoice($options = array()){
        
      
        $qualificationArray = array('client','invoiceNumber','address','description','hour','rate','amount','date','currency');
        foreach($qualificationArray as $qualifier)
        {
            if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
        }
        $query = $this->db->get('zowindiainvoice');
        
        if($query->num_rows() == 0) return false;

        if(isset($options['invoiceNumber'])) {
        
            return $query->row(0);
        }
        else{
            return $query->result();
        }

    }
    function updateinvoice($options = array()){
        
      
        $qualificationArray = array('client','invoiceNumber','address','description','hour','rate','amount','date','currency');
        foreach($qualificationArray as $qualifier)
        {
            if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
        }
        $this->db->where('invoiceNumber',$options['invoiceNumber']);
        $this->db->update('zowindiainvoice');

        return $this->db->affected_rows();
        
        
    }
    function _required($required, $data)
	{
		foreach($required as $field) if(!isset($data[$field])) return false;
		return true;
	}
}
?>