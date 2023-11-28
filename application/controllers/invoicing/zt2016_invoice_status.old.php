<?php

class Zt2016_invoice_status extends MY_Controller {

	
	public function index()
	{
		$invoice =$this->input->post('Invoice');
		$status =$this->input->post('Status');
		$client= $this->input->post('Client');
		$InvoiceDate= $this->input->post('InvoiceDate');
		$InvoiceDueDays= $this->input->post('DueDays');

		//echo $InvoiceDueDays."<br/>";
		//echo $InvoiceDate."<br/>";
		//echo date('d M Y',strtotime("+ ".$InvoiceDueDays." days",strtotime($InvoiceDate)));
		//die;
		
		//http://stackoverflow.com/questions/11029769/function-to-check-if-a-string-is-a-date
		if (DateTime::createFromFormat('d M Y', $InvoiceDate) == FALSE) {
			$InvoiceDate= date("d M Y");			
		}
		if ($invoice =="" || $status =="" ||$client ==""){
			echo "Error - missing data";
		} else {
			$this->load->model('zt2016_invoices_model');
			if ($status!="CANCEL") {
				$this->zt2016_invoices_model->_changeInvoiceStatus($options =  array('invoice' => $invoice,'status' => $status,'invoicedate' => $InvoiceDate, 'invoiceduedays'=> $InvoiceDueDays));
				redirect('/invoicing/zt2016_view_invoice/'.$invoice);
			}
			else {
				$this->zt2016_invoices_model->_cancelInvoice($options =  array('invoice' => $invoice));
				
				$safeclientName=str_replace("&","~",$client);
				$safeclientName=str_replace(" ","_",$safeclientName);

				redirect('/invoicing/zt2016_new_client_invoice/'.$safeclientName);
			}
		}
		
		

	}

}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>