<?php class Zt2016_employee_model extends CI_Model {


	function getemployeedata($options = array()){
		$qualificationArray = array('dob','married','phone','gender','state','address','Country','Religion','ZIPCode','City','PrimaryName','PrimaryRelationship','PrimaryPhone','SecondryName','SecondryRelationship','SecondryPhone','BankName','AccountNumber','IFSCCode','PANNO','employeeid');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}

		$query = $this->db->get('zowemployee');
		$employeedata = array();
		if($query->num_rows() == 0) return false;

        if(isset($options['employeeid'])) {
        
            $employeedata['basicData']  = $query->row(0);
        }
        else{
            $employeedata['basicData'] =  $query->result();
        }

		$this->db->where('employeeid', $options['employeeid']);
		$query = $this->db->get('zowemployeefamily');
		$employeedata['familydata']  = $query->result();

		$this->db->where('employeeid', $options['employeeid']);
		$query = $this->db->get('zowemployeeeducation');
		$employeedata['education']  = $query->result();

		$this->db->where('employeeid', $options['employeeid']);
		$query = $this->db->get('zowemployeeexperience');
		$employeedata['experience']  = $query->result();

		return $employeedata;


	}
    function addemployeedata($options = array())
    {
		
        $qualificationArray = array('dob','married','phone','gender','state','address','Country','Religion','ZIPCode','City','PrimaryName','PrimaryRelationship','PrimaryPhone','SecondryName','SecondryRelationship','SecondryPhone','BankName','AccountNumber','IFSCCode','PANNO','employeeid');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
		
		// Execute the query
		$this->db->insert('zowemployee');

		$familyarray = array('familyName','familyRelationship','familydob','familyPhone');
		$count = count($options['familyName']);
		for ($i=0; $i < $count; $i++) { 
			$this->db->set('familyName', $options['familyName'][$i]);
			$this->db->set('familyRelationship', $options['familyRelationship'][$i]);
			$this->db->set('familydob', $options['familydob'][$i]);
			$this->db->set('familyPhone', $options['familyPhone'][$i]);
			$this->db->set('employeeid', $options['employeeid']);
			$this->db->insert('zowemployeefamily');
		}
	
		$count = count($options['Institution']);
		for ($i=0; $i < $count; $i++) { 
			$this->db->set('Institution', $options['Institution'][$i]);
			$this->db->set('Subject', $options['Subject'][$i]);
			$this->db->set('year', $options['year'][$i]);
			$this->db->set('Degree', $options['Degree'][$i]);
			$this->db->set('Grade', $options['Grade'][$i]);
			$this->db->set('employeeid', $options['employeeid']);
			$this->db->insert('zowemployeeeducation');
		}

		$count = count($options['CompanyName']);
		for ($i=0; $i < $count; $i++) { 
			$this->db->set('CompanyName', $options['CompanyName'][$i]);
			$this->db->set('Location', $options['Location'][$i]);
			$this->db->set('JobPosition', $options['JobPosition'][$i]);
			$this->db->set('PeriodFrom', $options['PeriodFrom'][$i]);
			$this->db->set('PeriodTo', $options['PeriodTo'][$i]);
			$this->db->set('employeeid', $options['employeeid']);
			$this->db->insert('zowemployeeexperience');
		}

		
 		// Return the ID of the inserted row, or false if the row could not be inserted
		return $this->db->insert_id();

    }
    function updateemployeedata($options = array())
    {
		
        $qualificationArray = array('dob','married','phone','gender','state','address','Country','Religion','ZIPCode','City','PrimaryName','PrimaryRelationship','PrimaryPhone','SecondryName','SecondryRelationship','SecondryPhone','BankName','AccountNumber','IFSCCode','PANNO','employeeid');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
		
		// Execute the query
		$this->db->where('employeeid', $options['employeeid']);
		$this->db->update('zowemployee');

		$familyarray = array('familyName','familyRelationship','familydob','familyPhone');
		$count = count($options['familyName']);
		for ($i=0; $i < $count; $i++) { 
			if(isset($options['id'][$i])){
				$this->db->set('familyName', $options['familyName'][$i]);
				$this->db->set('familyRelationship', $options['familyRelationship'][$i]);
				$this->db->set('familydob', $options['familydob'][$i]);
				$this->db->set('familyPhone', $options['familyPhone'][$i]);
				$this->db->set('employeeid', $options['employeeid']);
				$this->db->where('id', $options['id'][$i]);
				$this->db->update('zowemployeefamily');
			}
			else{
				$this->db->set('familyName', $options['familyName'][$i]);
				$this->db->set('familyRelationship', $options['familyRelationship'][$i]);
				$this->db->set('familydob', $options['familydob'][$i]);
				$this->db->set('familyPhone', $options['familyPhone'][$i]);
				$this->db->set('employeeid', $options['employeeid']);
				$this->db->insert('zowemployeefamily');
			}
			
		}
	
		$count = count($options['Institution']);
		for ($i=0; $i < $count; $i++) { 
			if(isset($options['id'][$i])){
				$this->db->set('Institution', $options['Institution'][$i]);
				$this->db->set('Subject', $options['Subject'][$i]);
				$this->db->set('year', $options['year'][$i]);
				$this->db->set('Degree', $options['Degree'][$i]);
				$this->db->set('Grade', $options['Grade'][$i]);
				$this->db->set('employeeid', $options['employeeid']);
				$this->db->where('id', $options['id'][$i]);
				$this->db->update('zowemployeeeducation');
			}
			else{
				$this->db->set('Institution', $options['Institution'][$i]);
				$this->db->set('Subject', $options['Subject'][$i]);
				$this->db->set('year', $options['year'][$i]);
				$this->db->set('Degree', $options['Degree'][$i]);
				$this->db->set('Grade', $options['Grade'][$i]);
				$this->db->set('employeeid', $options['employeeid']);
				$this->db->insert('zowemployeeeducation');
			}
			
		}

		$count = count($options['CompanyName']);
		for ($i=0; $i < $count; $i++) { 
			if(isset($options['id'][$i])){
				$this->db->set('CompanyName', $options['CompanyName'][$i]);
				$this->db->set('Location', $options['Location'][$i]);
				$this->db->set('JobPosition', $options['JobPosition'][$i]);
				$this->db->set('PeriodFrom', $options['PeriodFrom'][$i]);
				$this->db->set('PeriodTo', $options['PeriodTo'][$i]);
				$this->db->set('employeeid', $options['employeeid']);
				$this->db->where('id', $options['id'][$i]);
				$this->db->update('zowemployeeexperience');
			}
			else{
				$this->db->set('CompanyName', $options['CompanyName'][$i]);
				$this->db->set('Location', $options['Location'][$i]);
				$this->db->set('JobPosition', $options['JobPosition'][$i]);
				$this->db->set('PeriodFrom', $options['PeriodFrom'][$i]);
				$this->db->set('PeriodTo', $options['PeriodTo'][$i]);
				$this->db->set('employeeid', $options['employeeid']);
				$this->db->insert('zowemployeeexperience');
			}
			
		}

		
 		// Return the ID of the inserted row, or false if the row could not be inserted
		return true;

    }
}