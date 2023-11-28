<?php

class Zt2016_invoice_billing_notes_update extends My_Controller {



	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));

		$zowuser=_superuseronly(); 
		
		#### retrieve form values
	
		$FormValues['BillingNotes'] = $this->input->post('InvoiceBillingNotes');		
		$FormValues['InvoiceNumber'] = $this->input->post('InvoiceNumber');		


		#### check if client ID exists
		if ($FormValues['InvoiceNumber']=="") {
			$Message='No invoice number provided.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			
			//redirect('invoicing/zt2016_pending_invoices', 'refresh');

		}

		#### retrieve current client info from db		
		$this->load->model('zt2016_invoices_model', '', TRUE);
		$OldInvoiceInfo = $this->zt2016_invoices_model->GetInvoice($options = array ("InvoiceNumber"=>$FormValues['InvoiceNumber']));

	
			
		if ($OldInvoiceInfo->BillingNotes==$FormValues['BillingNotes']){
			
			$Message='The new invoice billing notes are the same as the existing ones. No changes have been made.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
		
		} else{
		
			#### update the database
						
			$updated_invoice = $this->zt2016_invoices_model->UpdateInvoice($FormValues);
			

					
			if($updated_invoice)	{
				
				$Message='Invoice billing notes for invoice '.$FormValues['InvoiceNumber'].' have been updated.';	
				$this->session->set_flashdata('SuccessMessage',$Message);
				
			} else{

				$Message='There was an error updating the database';	
				$this->session->set_flashdata('ErrorMessage',$Message);
					
			}
							
				
		}	
		
		redirect('invoicing/zt2016_view_invoice/'.$FormValues['InvoiceNumber'], 'refresh');
		
	}



}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>