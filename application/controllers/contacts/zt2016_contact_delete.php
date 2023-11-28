<?php

class Zt2016_contact_delete extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_contacts','userpermissions'));

		$this->load->model('zt2016_contacts_model', '', TRUE);		
		
		$zowuser=_superuseronly(); 

		$ContactID=$this->uri->segment(3);


		######### check if client name is provided
		 if (empty ($ContactID)) {
				
			$Message='No client ID provided for deletion.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
		 }
		 else {
			#### retrieve current client info from db		

			$ContactInfo = $this->zt2016_contacts_model->GetContact($options = array ("ID"=>$ContactID));
			if (!$ContactInfo){
				$Message='Contact ID provided for deletion does not exist.';	
				$this->session->set_flashdata('ErrorMessage',$Message);
				//redirect('trash/zt2016_trash', 'refresh');
			}	 	
	 		else {
				
				$Contact_Full_Name=$ContactInfo->FirstName.' '.$ContactInfo->LastName;
				$ContactInfo->Contact_Full_Name=$Contact_Full_Name;
				#### check if client is trashed		
				if ($ContactInfo->Trash!=1){
					$Message=$Contact_Full_Name.' is not trashed. Contacts must be trashed before they can be permanently deleted.';	
					$this->session->set_flashdata('ErrorMessage',$Message);
					//redirect('trash/zt2016_trash', 'refresh');
				}
				else {
					########### check if client has entries 
					$this->load->model('zt2016_entries_model', '', TRUE);
					$ExistingEntries = $this->zt2016_entries_model-> GetEntry($options = array('Originator' => $Contact_Full_Name, 'Client'=> $ContactInfo->CompanyName));
			
					if ($ExistingEntries){
						
							# If entries with the same name exist, check if there is a duplicate untrashed contact
							$ExistingContact = $this->zt2016_contacts_model-> GetContact($options = array('FirstName' => $ContactInfo->FirstName,'LastName' => $ContactInfo->LastName,'CompanyName' => $ContactInfo->CompanyName,'Trash'=>0 ));
						
							if (!$ExistingContact){
								$Message=$Contact_Full_Name." has booking entries and thus cannot be deleted.\n";
								$this->session->set_flashdata('ErrorMessage',$Message);
							} 
						
							else {

								$Message = $this->_delete_contact($ContactInfo);

							}//if ($ExistingContact)*/
						}else {
	
							$Message = $this->_delete_contact($ContactInfo);
						}//if ($ExistingEntries)
					}// ($ClientInfo->Trash!=1)
				}//if (!$ClientInfo)
		}//if (empty ($ClientID))
		redirect('trash/zt2016_trash', 'refresh');
		
	}

	// ################## display clients info ##################	
	function _delete_contact($ContactInfo)
	{

		########### delete contact from db
		$DeletedContact = $this->zt2016_contacts_model->DeleteContact($options = array ("ID"=>$ContactInfo->ID));

		if ($DeletedContact==1){

			$Message="Contact ".$ContactInfo->Contact_Full_Name." was permanently deleted from the database.\n";
			$this->session->set_flashdata('SuccessMessage',$Message);
			//redirect('trash/zt2016_trash', 'refresh');
		} else {

			$Message='There was an error deleting '.$ContactInfo->Contact_Full_Name.".\n";
			$this->session->set_flashdata('ErrorMessage',$Message);
		}//if ($DeletedContact)*/	
		
	}
}

/* End of file Zt2016_trash.php */
/* Location: ./system/application/controllers/trash/Zt2016_trash.php */
?>