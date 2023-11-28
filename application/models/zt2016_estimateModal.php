<?php class Zt2016_estimateModal extends CI_Model {
    function addestimate($options = array()){
      
        if(!$this->_required(array('quotationNumber'), $options)) return false;
		
		$options['date'] = time();
		
        if($options['items']){
            foreach($options['items'] as $items){
                $this->db->set('quotationNumber',$options['quotationNumber']);
                $this->db->set('date',$items['date']);
                $this->db->set('originator',$items['originator']);
                $this->db->set('filename',$items['filename']);
                $this->db->set('newslides',$items['newslides']);
                $this->db->set('editslides',$items['editslides']);
                $this->db->set('hour',$items['hour']);
                $this->db->insert('zowestimateentries');
            }
        }
		$qualificationArray = array('quotationNumber','Client','Project','Email','ClientAddress','BillingAddress','Estimagedate','ExpiryDate','TotalHour','Discount','PerHour','otherInformation','estimateTotal','SumNewSlides','SumEditedSlides','SumHours');
		foreach($qualificationArray as $qualifier)

		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}

        
		// echo $this->db->last_query();
		$this->db->insert('zowestimate');
		
 		// Return the ID of the inserted row, or false if the row could not be inserted
		return $this->db->insert_id();
    }
    function updateestimate($options = array()){
      
        if(!$this->_required(array('quotationNumber'), $options)) return false;
		
		$options['date'] = time();
        if($options['items']){
            foreach($options['items'] as $items){
				if (!isset($items['id'])){
					$this->db->set('quotationNumber',$options['quotationNumber']);
					$this->db->set('date',$items['date']);
					$this->db->set('originator',$items['originator']);
					$this->db->set('filename',$items['filename']);
					$this->db->set('newslides',$items['newslides']);
					$this->db->set('editslides',$items['editslides']);
					$this->db->set('hour',$items['hour']);
					$this->db->insert('zowestimateentries');
				}
				else{
					$this->db->set('quotationNumber',$options['quotationNumber']);
					$this->db->set('date',$items['date']);
					$this->db->set('originator',$items['originator']);
					$this->db->set('filename',$items['filename']);
					$this->db->set('newslides',$items['newslides']);
					$this->db->set('editslides',$items['editslides']);
					$this->db->set('hour',$items['hour']);
					$this->db->where('id', $items['id']);
					$this->db->update('zowestimateentries');
				}
                
            }
        }
		$qualificationArray = array('quotationNumber','Client','Project','Email','ClientAddress','BillingAddress','Estimagedate','ExpiryDate','TotalHour','Discount','PerHour','otherInformation','estimateTotal','SumNewSlides','SumEditedSlides','SumHours');
		foreach($qualificationArray as $qualifier)

		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}

        
		// echo $this->db->last_query();
		// die;
		$this->db->where('quotationNumber', $options['quotationNumber']);
		$this->db->update('zowestimate');

   

    	return $this->db->affected_rows();
    }

	function getestimateentries($options = array()){
		$qualificationArray = array('quotationNumber','Client','Project','Email','ClientAddress','BillingAddress','Estimagedate','ExpiryDate','TotalHour','Discount','PerHour','otherInformation');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
		$query = $this->db->get('zowestimateentries');
		
		if($query->num_rows() == 0) return false;

		return $query->result();
	}
	function getestimate($options = array()){
		$qualificationArray = array('quotationNumber','Client','Project','Email','ClientAddress','BillingAddress','Estimagedate','ExpiryDate','TotalHour','Discount','PerHour','otherInformation');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
		$query = $this->db->get('zowestimate');
		
		if($query->num_rows() == 0) return false;

		if(isset($options['quotationNumber'] )) {
	
			return $query->row(0);
		}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}

	}

	function filterestimateentries($options = array()){
		$startDate = $options['startDate'];
    	$endDate = $options['endDate'];
		$qualificationArray = array('quotationNumber','Client','Project','Email','ClientAddress','BillingAddress','Estimagedate','ExpiryDate','TotalHour','Discount','PerHour','otherInformation');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
		if (!empty($startDate) && !empty($endDate)) {
			$this->db->where('Estimagedate >=', $startDate);
			$this->db->where('Estimagedate <=', $endDate);
		}
		$query = $this->db->get('zowestimate');
		
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