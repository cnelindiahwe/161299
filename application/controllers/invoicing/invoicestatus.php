<?php

class Invoicestatus extends MY_Controller {

	
	function index()
	{
		$invoice =$this->input->post('Invoice');
		$status =$this->input->post('Status');
		$client= $this->input->post('Client');
		
		//echo $invoice;
		//echo $status;
		$this->load->model('trakinvoices');
		if ($status!="CANCEL") {
			$this->trakinvoices->_changeInvoiceStatus($options =  array('invoice' => $invoice,'status' => $status));
		}
		else {
			$this->trakinvoices-> _cancelInvoice($options =  array('invoice' => $invoice));
		}
		
		$this->load->model('trakclients');
		$clientinfo=$this->trakclients->GetEntry($options =  array('CompanyName' => $client));
		redirect('/invoicing/clientinvoices/'.$clientinfo->ID);		
	}

}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>