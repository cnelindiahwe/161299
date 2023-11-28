<?php

class zt2016_create_invoice extends MY_Controller {


	function index()
	{

		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions', 'url'));
		
		$zowuser=_superuseronly(); 

		
		$this->load->helper(array('form', 'invoice'));
		
		//Read form input values
		$formitems=array('Client','DueDays','Currency','StartDate','EndDate','InvoiceNumber','PricePerHour','BilledHours','InvoiceTotal','SumHours','SumNewSlides','SumEditedSlides','PriceEdits',"Originators","ExcludeList","InvoiceNumberStart");
		
		foreach ($formitems as $fitem) {
			$formfields[$fitem]=$this->input->post($fitem);
		}									
		$formfields[$fitem]=$this->input->post($fitem);



		$formfields['StartDate'] = date( 'Y-m-d',strtotime($formfields['StartDate']));
		$formfields['EndDate'] = date( 'Y-m-d',strtotime($formfields['EndDate']));

//echo $formfields['EndDate'];
//die;

		$createInvoice=$this->_addInvoicetoentries($formfields);
		
		if ($createInvoice) {

			$createInvoiceTable=$this->_addInvoicetotable($formfields);

			if ($createInvoiceTable) {
				$this->load->helper('url');

				redirect('invoicing/zt2016_view_invoice/'.$formfields['InvoiceNumber'], 'refresh');
			}
			else
			{
				echo "There was an error creating the invoice.";
				echo $this->db->last_query();
			}
			
		}
		else
		{
			echo "There was an error creating the invoice.";
			echo $this->db->last_query();
		}


	}
	
	//################### Add invoice to entries
	function _addInvoicetoentries($formfields )
	{
	
	   // Add invoice number, price and edits multiplier to entries
		$this->db->set('Invoice',$formfields['InvoiceNumber']);
		$this->db->set('InvoicePrice',$formfields['PricePerHour']);
		$this->db->set('InvoiceEditsMultiplier',$formfields['PriceEdits']);
		$this->db->set('Status','BILLED');
		
		$this->db->where('Status','COMPLETED');
		$this->db->where('Invoice','NOT BILLED');
		$this->db->where('Client', $formfields['Client']);
		$this->db->where('DateOut >=', $formfields['StartDate']);
	  $this->db->where('DateOut <= ', $formfields['EndDate']);
		$this->db->where('Trash',0);
	   	if ($formfields['ExcludeList']) {
	   		$ExcludeList = explode(",", $formfields['ExcludeList']);
	   		foreach ($ExcludeList as $excludename){
	   			$this->db->where('id !=',$excludename);				
	   		}

	   	}
	  $this->db->update('zowtrakentries');
		
		if ($this->db->affected_rows()) {
	   	// Calculate time and total per entry
			$this->db->where('Invoice', $formfields['InvoiceNumber']);
			$entries=$this->db->get('zowtrakentries');
			if ($entries) {
				foreach ($entries->result() as $thisentry) {
					$subtotal=0;
					$subtotalcash=0;
					$subtotal= $thisentry->EditedSlides*$thisentry->InvoiceEditsMultiplier;
					//Add slides and divide by slides per hour
					$subtotal= $thisentry->NewSlides+$subtotal;
					$subtotal= $subtotal/5;
					//Add hours to get the total
					$subtotal= $subtotal+$thisentry->Hours;
					$subtotalcash= $subtotal*$thisentry->InvoicePrice;
					
					//update entry
					$this->db->set('InvoiceTime',$subtotal);
					$this->db->set('InvoiceEntryTotal',$subtotalcash);
					$this->db->where('id',$thisentry->id);
					$this->db->update('zowtrakentries');
				}
				return 'Success' ;
			}
			// If not successful
			else {return false;}
		}
		// If not successful
		else {return false;}
		/**/
	}
	
	//################### Add invoice to invoice table
	
	function _addInvoicetotable($formfields)
	{

		/*
		 * $qualificationArray = array('InvoiceNumber','Client','Status','PricePerHour ','PriceEdits ','InvoiceTotal ','Currency','BilledHours ',
		'SumNewSlides','SumEditedSlides','SumHours ','Originators','StartDate','EndDate','BilledDate','DueDate','PaidDate','BillingNotes','Trash');
				
		foreach($qualificationArray as $qualifier)
		{
			if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
		}
		*/
		if($formfields['InvoiceNumberStart']){
			
			$this->db->set('InvoiceNumberStart',$formfields['InvoiceNumberStart']);
			$this->db->where('ID',1);
			$this->db->update('zowtrakglobalsetting');
	

		}
		foreach ($formfields as $key=>$value) {
			if ($key!='DueDays' and $key!='ExcludeList' and $key != 'InvoiceNumberStart'){		
				$this->db->set($key,$value);
			}
			
		}
									
		$this->db->set('Status','BILLED');
		//$date = date("Y-m-d");// current date
		
		$this->db->set('BilledDate', date( 'Y-m-d',strtotime($formfields['EndDate'])));
		$this->db->set('DueDate', date("Y-m-d",strtotime($formfields['EndDate']." +".$formfields['DueDays'].' days')));

	   // Execute the query	
	   $this->db->insert('zowtrakinvoices');
		
	   



		// Return the number of rows updated, or false if the row could not be inserted
		return $this->db->affected_rows();
		
		/**/
	}	
}

/* End of file createinvoice.php */
/* Location: ./system/application/controllers/billing/createinvoice.php */
?>