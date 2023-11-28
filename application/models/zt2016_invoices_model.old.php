<?php class Zt2016_invoices_model extends CI_Model {

	function GetInvoice($options = array())
	{

		// default values
		//$options = $this->_default(array('sortDirection' => 'asc'), $options);
	   
		// Add where clauses to query
		$qualificationArray = array('ID','InvoiceNumber','Client','Status','PricePerHour ','PriceEdits ','InvoiceTotal ','Currency','BilledHours ',
		'SumNewSlides','SumEditedSlides','SumHours ','Originators','StartDate','EndDate','BilledDate','DueDate','PaidDate','BillingNotes','Trash');
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
	   
		// If limit / offset are declared (usually for pagination) then we need to take them into account
		//if(isset($options['limit']) &amp;&amp; isset($options['offset'])) $this->db->limit($options['limit'], $options['offset']);
		//else if(isset($options['limit'])) $this->db->limit($options['limit']);
	   
		// sort
		if(isset($options['sortBy'])) $this->db->order_by($options['sortBy'], $options['sortDirection']);
	   
		$query = $this->db->get('zowtrakinvoices');
		
		if($query->num_rows() == 0) return false;

		//http://stackoverflow.com/questions/8784584/if-isset-multiple-or-conditions 
	    if(isset($options['ID']) || isset($options['InvoiceNumber'] )) {
	    
			return $query->row(0);
		}
		else
		{
			// If we could be returning any number of records then we'll need to do so as an array of objects
			return $query->result();
		}
	}


	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Update existing invoice in db
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * UpdateContact
	 *
	 */	
	function UpdateInvoice($options = array())
	{

    // required values

		if(!$this->_required(array('InvoiceNumber'), $options)) return false;


// Add where clauses to query

		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('ID','Client','Status','PricePerHour ','PriceEdits ','InvoiceTotal ','Currency','BilledHours ',
		'SumNewSlides','SumEditedSlides','SumHours ','Originators','StartDate','EndDate','BilledDate','DueDate','PaidDate','BillingNotes','Trash');
				
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}



	   // Execute the query
		$this->db->where('InvoiceNumber', $options['InvoiceNumber']);
		$this->db->update('zowtrakinvoices');
		// Return the number of rows updated, or false if the row could not be inserted
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
 *  _changeInvoiceStatus()
 *
 * Change the Status of all entries in invoice
 *
 * @access	public
 * @return	string
 */

	function  _changeInvoiceStatus($options =  array())
	{
		if(!$this->_required(array('invoice','status','invoicedate'), $options)) return false;

		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		$qualificationArray = array('status');
		
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
	   // Execute the query
		$this->db->where('Invoice', $options['invoice']);
		$this->db->update('zowtrakentries');
		// Return the number of rows updated, or false if the row could not be inserted
		
		$this->db->set('Status', $options['status']);
		if ($options['status']=='BILLED'){
			$this->db->set('PaidDate',0);
			$this->db->set('BilledDate', date('Y-m-d',strtotime($options['invoicedate'])));

			$this->db->set('DueDate', date('Y-m-d',strtotime("+ ".$options['invoiceduedays']." days",strtotime($options['invoicedate']))));
		}
		//if ($options['status']=='PAID' || $options['status']=='WAIVED' || $options['status']=='MARKETING'){
		else{	
			$this->db->set('PaidDate', date('Y-m-d',strtotime($options['invoicedate'])));
		}
		
		$this->db->where('InvoiceNumber',$options['invoice']);
		$this->db->update('zowtrakinvoices');
		return $this->db->affected_rows();

	}
	
	
// ------------------------------------------------------------------------

/**
 *  _cancelInvoice()
 *
 * Change the Status of all entries in invoice
 *
 * @access	public
 * @return	string
 */

	function  _cancelInvoice($options =  array())
	{
		if(!$this->_required(array('invoice'), $options)) return false;

		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		 $this->db->set('Invoice','NOT BILLED');
		 $this->db->set('InvoicePrice',0);
		 $this->db->set('InvoiceEditsMultiplier',0);
		 $this->db->set('InvoiceEntryTotal',0);
		 $this->db->set('InvoiceTime',0);
		 $this->db->set('BilledBy','');
		 $this->db->set('PaidBy','');
		 $this->db->set('Status','COMPLETED');
	   // Execute the query
		$this->db->where('Invoice', $options['invoice']);
		$this->db->update('zowtrakentries');
		// Return the number of rows updated, or false if the row could not be inserted

		$this->db->where('InvoiceNumber',$options['invoice']);
		$this->db->delete('zowtrakinvoices');
		// Return the number of rows updated, or false if the row could not be inserted
		return $this->db->affected_rows();


	}	



// ------------------------------------------------------------------------

/**
 *  _getDateLastInvoice()
 *
 * Return date of the last invoice issued
 *
 * @access	public
 * @return	string
 */

	function  _getDateLastInvoice($client)
	{

		$this->db->select_max('BilledDate');
		$this->db->from('zowtrakinvoices');
		$this->db->where("Client ='".$client."'"); 
		$this->db->where("Trash ='0'");
		$query = $this->db->get();
		//echo $client."***". $query->row()->BilledDate."***<br/>";
		
		$LastInvoiceDate = $query->row()->BilledDate;
		return $LastInvoiceDate;
	}


}

?>