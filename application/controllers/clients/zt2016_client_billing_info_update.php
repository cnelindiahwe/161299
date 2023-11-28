<?php

class Zt2016_client_billing_info_update extends My_Controller {



	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));

		$zowuser=_superuseronly(); 
		
		#### retrieve form values
		$FormValues['ID'] = $this->input->post('ID');		
		$FormValues['BillingGuidelines'] = $this->input->post('BillingGuidelines');		
		$InvoiceNumber = $this->input->post('InvoiceNumber');		
		
		#### check if client ID exists
		
		if ($FormValues['ID']=="") {
			$Message='No Client ID provided.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			if ($InvoiceNumber=="") {
				redirect('clients/zt2016_clients', 'refresh');
			} else {
				redirect('invoicing/zt2016_view_invoice/'.$InvoiceNumber, 'refresh');
			}
		}

		#### retrieve current client info from db		
		$this->load->model('zt2016_clients_model', '', TRUE);
		$OldClientInfo = $this->zt2016_clients_model->GetClient($options = array ("ID"=>$FormValues["ID"]));

		$safeclientName=str_replace(" ", "_", $OldClientInfo->CompanyName);
		$safeclientName=str_replace("&", "~",  $safeclientName);

 
		if ($OldClientInfo->BillingGuidelines!=$FormValues['BillingGuidelines']){
		
			#### update the database
						
			$updated_client = $this->zt2016_clients_model->UpdateClient($FormValues);
			
			if($updated_client)	{
				
				$Message='Billing guidelines for '.$OldClientInfo->CompanyName.'have been updated.';	
				$this->session->set_flashdata('SuccessMessage',$Message);

			} else{

				$Message='There was an error updating the database';	
				$this->session->set_flashdata('ErrorMessage',$Message);
				
			}		
		
		} else{
		
			$Message='The new client billing notes are the same as the existing ones. No changes have been made.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			
		}	

		if ($InvoiceNumber=="") {
			redirect('invoicing/zt2016_client_invoices/'.$safeclientName, 'refresh');
		} else {
			redirect('invoicing/zt2016_view_invoice/'.$InvoiceNumber, 'refresh');
		}	
		
	}
}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>