<?php

class Zt2016_contact_trash extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_contacts','userpermissions'));

		$this->load->model('zt2016_contacts_model', '', TRUE);		
		$this->load->model('zt2016_entries_model', '', TRUE);
		
		$zowuser=_superuseronly(); 
		
		$templateData['ZOWuser']= _getCurrentUser();

		$ContactID=$this->uri->segment(3);

		########### check if there is a user name
		 if (empty ($ContactID)) {
		
			$Message="Contact ID was missing in URL.\n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('contacts/zt2016_contacts', 'refresh');
		 
		 }
		 
	

		$ContactInfo = $this->zt2016_contacts_model->GetContact($options = array('ID' => $ContactID));
		
		if (!$ContactInfo){
			
			$Message="No contact found with ID ".$ContactID."\n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('contacts/zt2016_contacts', 'refresh');
		}

		
		
		########### check if contact has invoices 

		$Contact_Full_Name=$ContactInfo->FirstName.' '.$ContactInfo->LastName;
		
		//die ("#".$Contact_Full_Name."#");
		
		$ContactEntries = $this->zt2016_entries_model->GetEntry($options = array('Originator' => $Contact_Full_Name));
		
				
		if ($ContactEntries){
			
			$Message=$Contact_Full_Name." has booking entries and thus cannot be trashed.<br/>";
			$Message.= " Please consider inactivation instead.\n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('contacts/zt2016_contact_edit/'.$ContactID, 'refresh');
		}



		########### update valid client trash field to 1

		$UpdatedContact = $this->zt2016_contacts_model->UpdateContact($options = array("ID"=>$ContactInfo->ID, 'Trash'=>'1'));

		if (!$UpdatedContact){
			die ($this->db->last_query());
			$Message="There was a problem trashing ".$Contact_Full_Name.". \n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('contacts/zt2016_contacts', 'refresh');
		}		

		########### return with success
		
		$Message=$Contact_Full_Name." has been trashed.\n";
		$this->session->set_flashdata('SuccessMessage',$Message);
		redirect('contacts/zt2016_contacts', 'refresh');


	}	

}

/* End of file Zt2016_client_delete.php */
/* Location: ./system/application/controllers/clients/Zt2016_client_delete.php */
?>