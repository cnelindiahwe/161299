<?php

class Zt2016_contact_restore extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_contacts','userpermissions'));

		$this->load->model('zt2016_contacts_model', '', TRUE);		
		
		$zowuser=_superuseronly(); 

		$ContactID=$this->uri->segment(3);


		######### check if client name is provided
		 if (empty ($ContactID)) {
				
			$Message='No client ID provided for restoration.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
		 }
		 else {
			#### retrieve current client info from db		

			$ContactInfo = $this->zt2016_contacts_model->GetContact($options = array ("ID"=>$ContactID));
			if (!$ContactInfo){
				$Message='Contact ID provided for restoration does not exist.';	
				$this->session->set_flashdata('ErrorMessage',$Message);
				//redirect('trash/zt2016_trash', 'refresh');
			}	 	
	 		else {
				
				$Contact_Full_Name=$ContactInfo->FirstName.' '.$ContactInfo->LastName;
				#### check if client is trashed		
				if ($ContactInfo->Trash=0){
					$Message=$ClientInfo->$Contact_Full_Name.' is not trashed. Only trashed contacts may be restored.';	
					$this->session->set_flashdata('ErrorMessage',$Message);
					//redirect('trash/zt2016_trash', 'refresh');
				}
				else {
					########### check if client has entries 
					
					$ExistingContact = $this->zt2016_contacts_model-> GetContact($options = array('FirstName' => $ContactInfo->FirstName,'LastName' => $ContactInfo->LastName,'CompanyName' => $ContactInfo->CompanyName,'Trash'=>0 ));

					
					if ($ExistingContact){
						
						$Message=$Contact_Full_Name." - an untrashed contact with the same name already exists for ".$ContactInfo->CompanyName.". \n";
						$this->session->set_flashdata('ErrorMessage',$Message);
					} 
					
					else {

							$RestoreContact = $this->zt2016_contacts_model->UpdateContact($options = array ("ID"=>$ContactInfo->ID, 'Trash'=>0));

							if ($RestoreContact==1){

								$Message="Contact ".$Contact_Full_Name." was restored.\n";
								$this->session->set_flashdata('SuccessMessage',$Message);
								redirect('contacts/zt2016_contact_info/'.$ContactID , 'refresh');
							} else {

								$Message='There was an error restoring '. $Contact_Full_Name.".\n";
								$this->session->set_flashdata('ErrorMessage',$Message);
							}	
															
					}//if ($ExistingContact)
				}// ($ClientInfo->Trash!=1)
			}//if (!$ClientInfo)
		}//if (empty ($ClientID))
		redirect('trash/zt2016_trash', 'refresh');
		
	}
}

/* End of file Zt2016_trash.php */
/* Location: ./system/application/controllers/trash/Zt2016_trash.php */
?>