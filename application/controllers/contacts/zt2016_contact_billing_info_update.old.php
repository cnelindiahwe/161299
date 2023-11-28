<?php

class Zt2016_contact_billing_info_update extends My_Controller {



	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));

		$zowuser=_superuseronly(); 
		
		#### retrieve form values
		$FormValues['ID'] = $this->input->post('ID');		
		$FormValues['ContactBillingGuidelines'] = $this->input->post('ContactBillingGuidelines');		
		$InvoiceNumber = $this->input->post('InvoiceNumber');		
		
		#### check if client ID exists
		
		if ($FormValues['ID']=="") {
			$Message='No contact ID provided.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			if ($InvoiceNumber=="") {
				redirect('clients/zt2016_contacts', 'refresh');
			} else {
				redirect('invoicing/zt2016_view_invoice/'.$InvoiceNumber, 'refresh');
			}
		}

		#### retrieve current client info from db		
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$OldContactInfo = $this->zt2016_contacts_model->GetContact($options = array ("ID"=>$FormValues["ID"]));
		
		if (!$OldContactInfo){
			$Message='Incorrect Contact ID provided.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			if ($InvoiceNumber=="") {
				redirect('clients/zt2016_contacts', 'refresh');
			} else {
				redirect('invoicing/zt2016_view_invoice/'.$InvoiceNumber, 'refresh');
			}			
		}
		
 
		if ($OldContactInfo->ContactBillingGuidelines!=$FormValues['ContactBillingGuidelines']){
		
			#### update the database
						
			$updated_client = $this->zt2016_contacts_model->UpdateContact($FormValues);
			
			if($updated_client)	{
				
				$Message='Billing guidelines for '.$OldContactInfo->FirstName." ".$OldContactInfo->LastName.'have been updated.';	
				$this->session->set_flashdata('SuccessMessage',$Message);
				
				
			} else{

				$Message='There was an error updating the database';	
				$this->session->set_flashdata('ErrorMessage',$Message);
			
			}		
		
		} else{
		
			$Message='The new contact billing notes are the same as the existing ones. No changes have been made.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
				
		}	
		if ($InvoiceNumber=="") {
			redirect('contacts/zt2016_contact_info/'.$OriginatorID, 'refresh');
		} else {
			redirect('invoicing/zt2016_view_invoice/'.$InvoiceNumber, 'refresh');
		}	
		
	}
}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>