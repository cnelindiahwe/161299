<?php

class Zt2016_client_delete extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));

		$zowuser=_superuseronly(); 

		$ClientID=$this->uri->segment(3);



		######### check if client name is provided
		 if (empty ($ClientID)) {
				
			$Message='No client ID provided for deletion.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
		 }
		 else {
			#### retrieve current client info from db		
			$this->load->model('zt2016_clients_model', '', TRUE);
			$ClientInfo = $this->zt2016_clients_model->GetClient($options = array ("ID"=>$ClientID));
			if (!$ClientInfo){
				$Message='Client ID provided for deletion does not exist.';	
				$this->session->set_flashdata('ErrorMessage',$Message);
				//redirect('trash/zt2016_trash', 'refresh');
			}	 	
	 		else {
				#### check if client is trashed		
				if ($ClientInfo->Trash!=1){
					$Message=$ClientInfo->CompanyName.' is not trashed. Clients must be trashed before they can be permanently deleted.';	
					$this->session->set_flashdata('ErrorMessage',$Message);
					//redirect('trash/zt2016_trash', 'refresh');
				}
				else {
					########### check if client has entries 
					$this->load->model('zt2016_entries_model', '', TRUE);
					$ExistingEntries = $this->zt2016_entries_model-> GetEntry($options = array('Client' => $ClientInfo->CompanyName));
					
					if ($ExistingEntries){
						
						$Message=$clientName." has entries and thus cannot be trashed.\n";
						$this->session->set_flashdata('ErrorMessage',$Message);
						redirect('clients/zt2016_client_info/'.$SafeclientName, 'refresh');
					} 
					
					else {

						########### check if client has invoices 
						$this->load->model('zt2016_invoices_model', '', TRUE);
						$PastInvoices = $this->zt2016_invoices_model->GetInvoice($options = array('Client' => $ClientInfo->CompanyName));
						
						if ($PastInvoices){
							
							$Message=$clientName." has invoices and thus cannot be deleted.\n";
							$this->session->set_flashdata('ErrorMessage',$Message);
							//redirect('trash/zt2016_trash', 'refresh');
						}  else {
				
							########### check if client has contacts 
							$this->load->model('zt2016_contacts_model', '', TRUE);
							$ExistingContacts = $this->zt2016_contacts_model-> GetContact($options = array('CompanyName' => $ClientInfo->CompanyName));
							
							if ($ExistingContacts){
								
								$Message=$clientName." has contacts and thus cannot be deleted.\n";
								$this->session->set_flashdata('ErrorMessage',$Message);
								//redirect('trash/zt2016_trash', 'refresh');
							}
							else{
								
								########### delete client from db
								$DeletedClient = $this->zt2016_clients_model->DeleteClient($options = array ("ID"=>$ClientInfo->ID));
	
								if ($DeletedClient==1){
								
									$Message=$ClientInfo->CompanyName." was permanently deleted from the database.\n";
									$this->session->set_flashdata('SuccessMessage',$Message);
									//redirect('trash/zt2016_trash', 'refresh');
								} else {
									
									$Message='There was an error deleting '. $ClientInfo->CompanyName.".\n";
									$this->session->set_flashdata('ErrorMessage',$Message);
								}	
															
							}//if ($ExistingContacts)
						}//if ($PastInvoices)
					}//if ($ExistingEntries)
				}// ($ClientInfo->Trash!=1)
			}//if (!$ClientInfo)
		}//if (empty ($ClientID))
		//die ($Message);
		redirect('trash/zt2016_trash', 'refresh');
		
	}
}

/* End of file Zt2016_trash.php */
/* Location: ./system/application/controllers/trash/Zt2016_trash.php */
?>