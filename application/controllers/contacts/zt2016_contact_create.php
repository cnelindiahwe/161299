<?php

class Zt2016_contact_create extends My_Controller {


	
	function index()
	{
		
		
					
		
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_contacts','userpermissions'));

		$this->load->model('zt2016_contacts_model', '', TRUE);		
		$this->load->model('zt2016_clients_model', '', TRUE);	
		
		
		$zowuser=_superuseronly(); 

		

		#### load rest of fields submitted via the form
		$FormFields=$_POST;
		$this->session->set_flashdata('FormValues',$FormFields);
		

			##### field validation for required filled
		
		    $required = array ("FirstName","LastName","Email1","CompanyName");
			foreach ($FormFields as $key=>$value) {
				
				
				if (in_array($key,$required) && $value==""){
					$Message="Required field ".$key." is missing.";
					
					$this->session->set_flashdata('ErrorMessage',$Message);
					redirect('contacts/zt2016_contact_edit/'.$SafeclientName, 'refresh');
				}
				else{
					$FormValues[$key]=trim($value);
					//echo $key.":".$FormValues[$key]."<br>";					
				}
			} 

		##### field validation for email and name
		
		
			
		#### retrieve all contacts info	for the 	

		$AllCompanyContacts = $this->zt2016_contacts_model->GetContact($options = array ("CompanyName"=>$FormValues["CompanyName"]));
;


		#### exit if new contact email is already used for other contact in the same company
		if ($AllCompanyContacts) {
			foreach ($AllCompanyContacts as $ContactDetails) { 
				if ($ContactDetails->Email1==$FormFields['Email1'] || $ContactDetails->Email1==$FormFields['Email2'] ){

					$Message='Email '. $FormFields['Email1'].' is in use for '.$ContactDetails->CompanyName.'. Please create a different one.';						
					$this->session->set_flashdata('ErrorMessage',$Message);
					redirect('contacts/zt2016_contact_new', 'refresh');
				}	

				else if ($FormFields['Email2']!="") {
					if ($ContactDetails->Email2==$FormFields['Email1'] || $ContactDetails->Email2==$FormFields['Email2'] ){

						$Message='Email '. $FormFields['Email2'].' is in use for '.$ContactDetails->CompanyName.'. Please create a different one.';					
						$this->session->set_flashdata('ErrorMessage',$Message);
						redirect('contacts/zt2016_contact_new', 'refresh');
					}
				}

				elseif ($ContactDetails->FirstName==$FormFields['FirstName'] && $ContactDetails->LastName==$FormFields['LastName'] ){

					$Message='Name '.$FormFields['FirstName']." ".$FormFields['LastName'].' is in use for '.$ContactDetails->CompanyName.'. Please create a different one.';						
					$this->session->set_flashdata('ErrorMessage',$Message);					
					redirect('contacts/zt2016_contact_new', 'refresh');

				} 
			}
		} //if ($AllCompanyContacts)
		
		##### assign client's timezone if empty
		
		if (empty($FormFields['TimeZone'])) {
			$contact_client_details =$this->zt2016_clients_model->GetClient($options = array('CompanyName' => $FormFields['CompanyName'])); 
			if ($contact_client_details){
				$FormFields['TimeZone']=$contact_client_details->TimeZone;
			}
		}
		
		##### create contact
		
		$created_contact = $this->zt2016_contacts_model->AddContact($FormValues);
		
		if($created_contact)	{
			$Message='Contact '.$FormFields['FirstName']." ".$FormFields['LastName'].' has been created.';						
			$this->session->set_flashdata('SuccessMessage',$Message);
			redirect('contacts/zt2016_contact_info/'.$created_contact, 'refresh');
		}

		$Message="There was an error creating the new contact, which has not been created.";

		$this->session->set_flashdata('ErrorMessage',$Message);
		redirect('contacts/zt2016_contact_new', 'refresh');
		
	}
}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>