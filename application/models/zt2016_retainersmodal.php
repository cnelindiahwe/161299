<?php
 class Zt2016_retainersmodal extends CI_Model{

    function addretainersdate($options = array()){

        $qualificationArray = array('client','startDate','endDate','note');
        foreach($qualificationArray as $qualifier)
        {
            if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
        }
        if(isset($options['id']) && !empty($options['id'])){
            $this->db->where('id',$options['id']);
            $this->db->update('zowretainers');
            return "update";
        }else{
            $query = $this->db->insert('zowretainers');

            return 'insert';
        }
        
    }

    function getinvoicehours($options = array()){
        $startDate = $options['startDate'];
    	$endDate = $options['endDate'];

		if (!empty($startDate) && !empty($endDate)) {
            $this->db->select('SUM(BilledHours) as totalhours');
			$this->db->where('Client', $options['client']);
			$this->db->where('BilledDate >=', $startDate);
			$this->db->where('BilledDate <=', $endDate);
            $query = $this->db->get('zowtrakinvoices');

            // echo $this->db->last_query();
            // die;
		
            if($query->num_rows() == 0) return false;

            return $query->row()->totalhours;
		}else{
            return false;
        }
		
	}
    function gettotalhours($options = array()){
        $startDate = $options['startDate'];
    	$endDate = $options['endDate'];

		if (!empty($startDate) && !empty($endDate)) {
            $this->db->select('SUM(BilledHours) as totalhours, SUM(SumNewSlides) as sumnew, SUM(SumEditedSlides) as sumedit, SUM(SumHours) as sumhours');
			$this->db->where('Client', $options['client']);
			$this->db->where('BilledDate >=', $startDate);
			$this->db->where('BilledDate <=', $endDate);
            $query = $this->db->get('zowtrakinvoices');

            // echo $this->db->last_query();
            // die;
		
            if($query->num_rows() == 0) return false;

            return $query->row();
		}else{
            return false;
        }
		
	}
    function invoiceEntries($options = array()){
        $startDate = $options['startDate'];
    	$endDate = $options['endDate'];
        $this->db->order_by('DateOut','asc');

		if (!empty($startDate) && !empty($endDate)) {
            // $this->db->select('SUM(BilledHours) as totalhours, SUM(SumNewSlides) as sumnew, SUM(SumEditedSlides) as sumedit, SUM(SumHours) as sumhours');
			$this->db->where('Client', $options['client']);
			$this->db->where('DateOut >=', $startDate);
			$this->db->where('DateOut <=', $endDate);
            $query = $this->db->get('zowtrakentries');

            // echo $this->db->last_query();
            // die;
		
            if($query->num_rows() == 0) return false;

            return $query->result();
		}else{
            return false;
        }
		
	}
    function getretainersdate($options = array()){
        $qualificationArray = array('client');
        foreach($qualificationArray as $qualifier)
        {
            if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
        }
        $query = $this->db->get('zowretainers');
        
        if($query->num_rows() == 0) return false;

        if(isset($options['client'])) {
        
            return $query->row(0);
        }
    }
 }
?>