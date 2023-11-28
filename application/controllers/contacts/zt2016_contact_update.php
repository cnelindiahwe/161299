<?php

class Zt2016_contact_update extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));

		$zowuser=_superuseronly(); 
		

		#### retrieve  contact id from url
		$Field["ID"] = $this->uri->segment(3);		
		
		#### exit if no contact id
		if (empty($Field["ID"])) {
			$Message='No Contact ID provided. Contact not updated.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			
			die();
			redirect('contacts/zt2016_contacts_search', 'refresh');
		}

		
		#### exit if no contact id
		if (!is_numeric($Field["ID"])) {
			$Message='Provided contact ID incorrectly formatted. Contact not updated.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			
			die();
			redirect('contacts/zt2016_contacts_search', 'refresh');
		}		

		#### retrieve current client info from db		
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$OldContactInfo = $this->zt2016_contacts_model->GetContact($options = array ("ID"=>$Field["ID"]));

		if (!$OldContactInfo){
			$Message='Contact does not exist. Contact not updated.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			
			die();
			redirect('contacts/zt2016_contacts_search', 'refresh');
		}


		#### load rest of fields submitted via the form
		$FormFields=$_POST;
		$FormFields['ID']=$Field["ID"];
		$this->session->set_flashdata('FormValues',$FormFields);

		##### field validation for required
		/*$Required = array ("Active","Email1","CompanyName","FirstName","LastName");

		foreach ($Required as $RequiredField) {
			if (!array_key_exists($RequiredField,$FormFields)) {

				$Message="Required field ".$RequiredField." is missing. Contact not updated.";	
				$this->session->set_flashdata('ErrorMessage',$Message);
				$this->session->set_flashdata('FormValues',$FormValues);
				redirect('contacts/zt2016_contact_edit/'.$FormFields['ID'], 'refresh');
			}				
		}
		*/

		
		##### field validation for required filled
		$required = array ("Active","Email1","CompanyName","FirstName","LastName");
		$duplicateflag=0;
		foreach ($FormFields as $key=>$value) {

			$FormValues[$key]=trim($value);

			if (in_array($key,$required) && $value==""){
				$Message="Required field ".$key." is missing. Contact not updated.";
				$this->session->set_flashdata('ErrorMessage',$Message);
				redirect('contacts/zt2016_contact_edit/'.$FormFields["ID"], 'refresh');	
				
			}
			elseif ($FormValues[$key]!=$OldContactInfo->$key){					
					$duplicateflag=1;
			}
		} 

		if ($duplicateflag==0){
			$Message="At least some of the data in the form below is the same as the existing one. Contact not updated.";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('contacts/zt2016_contact_edit/'.$Field["ID"], 'refresh');			
		}

		

		
		##### check if the new email exists within the same company
		if ($FormValues['Email1']!=$OldContactInfo->Email1 || $FormValues['Email2']!=$OldContactInfo->Email2){
			
			#### retrieve all company contacts info		
			$this->load->model('zt2016_contacts_model', '', TRUE);
			$AllContactsTable = $this->zt2016_contacts_model->GetContact($options=array('CompanyName'=>$FormValues['CompanyName']));
			
			## loop throiugh contacyts
			foreach ($AllContactsTable as $ContactLoop) { 
				# ignore current record
				if 	($ContactLoop->ID!=$FormValues['ID'])	{
					
					if ($ContactLoop->Email1==$FormValues['Email1'] || $ContactLoop->Email1==$FormValues['Email2'] ){
						
						$EmailValue=$FormValues['Email1'];
						
					} else if ($ContactLoop->Email2==$FormValues['Email1'] || $ContactLoop->Email2==$FormValues['Email2'] ){
						
						$EmailValue=$FormValues['Email2'];
						
					}
					
					if (!empty($EmailValue)){
						
						$Message='Contact email  '.$EmailValue.' is already in use for '.$ContactLoop->FirstName.' '.$ContactLoop->LastName.' Please use a different one.';
			
						$this->session->set_flashdata('ErrorMessage',$Message);					
						redirect('contacts/zt2016_contact_edit/'.$Field["ID"], 'refresh');
						
					}
					
				}
				
			}
		}

						
	
						
		$updated_contact = $this->zt2016_contacts_model->UpdateContact($FormValues);

		if($updated_contact)	{
			
			##Update databases if name changes
			if 	($OldContactInfo->FirstName!=$FormValues["FirstName"] || $OldContactInfo->LastName!=$FormValues["LastName"]  ){

				$OldContactInfo->FullName=$OldContactInfo->FirstName." ".$OldContactInfo->LastName;
				$NewFullName=$FormValues["FirstName"]." ".$FormValues["LastName"];
				
				#### update invoices	
				
				$this->load->model('Zt2016_invoices_model', '', TRUE);
				$company_invoices = $this->Zt2016_invoices_model->GetInvoice($options = array("Client"=>$OldContactInfo->CompanyName));
				
				if ($company_invoices){
					foreach ($company_invoices as $row){
						$UpdatedInvoiceContacts = str_replace($OldContactInfo->FullName,$NewFullName,$row->Originators) ;
						$updated_invoice = $this->Zt2016_invoices_model->UpdateInvoice(array("InvoiceNumber"=>$row->InvoiceNumber, "Originators"=>$UpdatedInvoiceContacts));
					}
				}
				/**/
				
				#### update entries				
				
				$this->load->model('trakentries', '', TRUE);
				$companyentries = $this->trakentries->GetEntry(array ("Client"=>$OldContactInfo->CompanyName,"Originator"=>$OldContactInfo->FullName));
				
				if ($companyentries ){
					
					foreach ($companyentries as $row){
						$updated_entry = $this->trakentries->UpdateEntry(array("id"=>$row->id,"Originator"=>$NewFullName));
					}
				}


			}

			$Message='Contact '.$FormValues["FirstName"]." ".$FormValues["LastName"].' has been updated.';						
			$this->session->set_flashdata('SuccessMessage',$Message);
			redirect('contacts/zt2016_contact_info/'.$Field["ID"], 'refresh');	
		}
		
		else{
			
			$Message="There was an error updating ".$FormValues["CompanyName"].", which has not been updated.\n";

		
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('contacts/zt2016_contact_edit/'.$Field["ID"], 'refresh');	
			
		}	
		
	}
}

/* End of file zt2016_contact_update */
/* Location: ./system/application/controllers/contacts/<strong>updateclient.php</strong> */
?>