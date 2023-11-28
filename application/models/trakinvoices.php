<?php class Trakinvoices extends CI_Model {




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

		$this->db->select_max('DateOut');
		$this->db->from('zowtrakentries');
		$this->db->where("Client ='".$client."'"); 
		$this->db->where("Invoice !='NOT BILLED'");
		$this->db->where("Trash ='0'");
		$query = $this->db->get();

		
		if ($query->row()->DateOut=="") {
			$this->db->select_min('DateOut');
			$this->db->from('zowtrakentries');
			$this->db->where("Client ='".$client."'"); 
			$query = $this->db->get();
			$StartDate = date('Y-m-d', strtotime('-1 day'.$query->row()->DateOut));
		}
		else
		{
		$StartDate =  $query->row()->DateOut;
		}
		return $StartDate;
	}

// ------------------------------------------------------------------------

/**
 *  _getDateInvoice()
 *
 * Return date of the last invoice issued
 *
 * @access	public
 * @return	string
 */

	function  _getDateInvoice($options =  array())
	{
		if(!$this->_required(array('client'), $options)) return false;
		
		$this->db->select_max('DateOut');
		$this->db->from('zowtrakentries');
		$this->db->where('Client',$options['client']);
		if($options['invoicenumber']!=""){
			$this->db->where('Invoice',$options['invoicenumber']);
		}
		$this->db->where("Invoice <>'NOT BILLED'");
		$this->db->where("Trash ='0'");
		$query = $this->db->get();

		
		if ($query->row()->DateOut=="") {
			$this->db->select_min('DateOut');
			$this->db->from('zowtrakentries');
			$this->db->where("Client ='".$options['client']."'"); 
			$query = $this->db->get();
			$StartDate = date('Y-m-d', strtotime('-1 day'.$query->row()->DateOut));
		}
		else
		{
		$StartDate =  $query->row()->DateOut;
		}
		return $StartDate;
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
		if(!$this->_required(array('invoice','status'), $options)) return false;

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
		if ($options['status']=='PAID')
			{$this->db->set('PaidDate', date('Y-m-d'));}
		else if ($options['status']=='BILLED')
			{$this->db->set('PaidDate',0);}
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
/**
 *  _getBilledInvoices()
 *
 * Return links to past invoices
 *
 * @access	public
 * @return	string
 */
	function  _getBilledInvoices()
	{
		$BilledInvoices ='<div><h3>Outstanding Invoices</h3>';
		$this->load->model('trakclients', '', TRUE);
		$clientlist = $this->trakclients->GetEntry($options = array('sortBy' => 'CompanyName','sortDirection' => 'Asc'));
		foreach ($clientlist as $client){
				
			$this->db->distinct();
			$this->db->select('Invoice');
			$this->db->select('Status');
			$this->db->where('Status','BILLED');
			$this->db->where('Client', $client->CompanyName); 
			$getentries = $this->db->get('zowtrakentries');
			
			if($getentries->num_rows() != 0) {
				$BilledInvoices .="<p><strong>".$client->CompanyName."</strong></br>";
				$pastinvoices= $getentries->result_array();	
				foreach($pastinvoices as $invoice)
				{
					$dateinvoice=$this->_getDateInvoice($options =  array('client'=>$client->CompanyName,'invoicenumber'=>$invoice['Invoice']));
					$dateinvoice = date('d-M-Y', strtotime($dateinvoice));
					$BilledInvoices .="<a href=\"".site_url()."invoicing/viewinvoice/".$invoice['Invoice']."\">".$invoice['Invoice']."</a> (".$invoice['Status']." on ".$dateinvoice.")<br/>";	
				}
				$BilledInvoices .="</p>";
			}
		}
		$BilledInvoices .="</div>";
		return $BilledInvoices;
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