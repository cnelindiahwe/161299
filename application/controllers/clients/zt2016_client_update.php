<?php

class Zt2016_client_update extends My_Controller {
	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));

		$zowuser=_superuseronly(); 
		
		$Field["ID"] = $this->uri->segment(3);		
		
		#### retrieve client id from url
		if ($Field["ID"]=="") {
			$Message='No Client ID provided.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('clients/zt2016_clients', 'refresh');
		}

		#### retrieve current client info from db		
		$this->load->model('zt2016_clients_model', '', TRUE);
		$OldClientInfo = $this->zt2016_clients_model->GetClient($options = array ("ID"=>$Field["ID"]));

		if (!$OldClientInfo){
			$Message='Client does not exist.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('clients/zt2016_clients', 'refresh');
		}

		$SafeclientName=str_replace(" ", "_", $OldClientInfo->CompanyName);
		$SafeclientName=str_replace("&", "~", $SafeclientName);

		#### load rest of fields submitted via the form
		$FormFields=$_POST;
		$FormFields['ID']=$Field["ID"];
		##### Reset Detault group
		if ($FormFields["Group"]=="DEFAULT"){
			$FormFields["Group"]="";
		}		
		$this->session->set_flashdata('FormValues',$FormFields);

			##### field validation for required
			$Required = array ("CompanyName","ClientCode","ZOWContact","BasePrice","Currency");
			
			foreach ($Required as $RequiredField) {
				if (!array_key_exists($RequiredField,$FormFields)) {
					
					$Message="Required field ".$key." is missing.";	
					$this->session->set_flashdata('ErrorMessage',$Message);
					//$this->session->set_flashdata('FormValues',$FormValues);
					redirect('clients/zt2016_client_edit/'.$SafeclientName, 'refresh');
				}				
			}

			##### field validation for required filled
			$required = array ("CompanyName","ClientCode","ZOWContact","BasePrice","Currency");
			$duplicateflag=0;
			foreach ($FormFields as $key=>$value) {
				
				$FormValues[$key]=trim($value);
			
				if (in_array($key,$required) && $value==""){
					$Message="Required field ".$key." is missing.";
					
					$this->session->set_flashdata('ErrorMessage',$Message);
					//$this->session->set_flashdata('FormValues',$FormValues);
					redirect('clients/zt2016_client_edit/'.$SafeclientName, 'refresh');
				}
				elseif ($FormValues[$key]!=$OldClientInfo->$key){					
						$duplicateflag=1;
				}
			} 

		##### field validation for duplicate of form and db data

		if ($duplicateflag==0){
				
			$Message="The data in the form below is the same as the one on the DB. Client not updated.";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('clients/zt2016_client_edit/'.$SafeclientName, 'refresh');			
			
		}

		

		##### field validation for exisitng client code and company name
		if ($FormValues['ClientCode']!=$OldClientInfo->ClientCode){
			
			#### retrieve all clients info		
			$this->load->model('zt2016_clients_model', '', TRUE);
			$AllClientsInfo = $this->zt2016_clients_model->GetClient();
			
			#### exit if new client code is already used for other client
			foreach ($AllClientsInfo as $ClientLoop) { 
				if ($ClientLoop->ClientCode==$FormFields['ClientCode'] && $ClientLoop->ID!=$FormValues['ID']){
											
					$Message='Client Code '.$FormFields['ClientCode'].' is in use. Please create a different one.';						
					$this->session->set_flashdata('ErrorMessage',$Message);					
					redirect('clients/zt2016_client_edit/'.$SafeclientName, 'refresh');
					
				} else if ($ClientLoop->CompanyName==$FormFields['CompanyName'] && $ClientLoop->ID!=$FormValues['ID']){
					
					$Message='Company name '.$FormFields['CompanyName'].' is in use. Please create a different one.';						
					$this->session->set_flashdata('ErrorMessage',$Message);					
					redirect('clients/zt2016_client_edit/'.$SafeclientName, 'refresh');
					
				} 
				
				
			}
		}

		##### Reset Detault group
		if ($FormValues["Group"]=="DEFAULT"){
			$FormValues["Group"]="";
		}
		
		
		
		##### Update contact
		$updated_client = $this->zt2016_clients_model->UpdateClient($FormValues);

		if($updated_client)	{
			
			##Update databases if company name changes
			if 	($OldClientInfo->CompanyName!=$FormValues["CompanyName"] || $OldClientInfo->ClientCode!=$FormValues["ClientCode"]  ){
	
				$SafeclientName=str_replace(" ", "_", $FormValues["CompanyName"]);
				$SafeclientName=str_replace("&", "~", $SafeclientName);
	
				
				#### update contacts
				$this->load->model('Zt2016_contacts_model', '', TRUE);
				$companycontacts = $this->Zt2016_contacts_model->GetContact(array ("CompanyName"=>$OldClientInfo->CompanyName));
				if ($companycontacts ){
					foreach ($companycontacts as $row){
						$updated_contact = $this->Zt2016_contacts_model->UpdateContact(array("ID"=>$row->ID,"CompanyName"=>$FormValues["CompanyName"]));
					}
				}
				
				
				#### update invoices	
				$this->load->model('Zt2016_invoices_model', '', TRUE);
				$company_invoices = $this->Zt2016_invoices_model->GetInvoice($options = array("Client"=>$OldClientInfo->CompanyName));
				if ($company_invoices){
					foreach ($company_invoices as $row){
						$updated_invoice = $this->Zt2016_invoices_model->UpdateInvoice(array("InvoiceNumber"=>$row->InvoiceNumber,"Client"=>$FormValues["CompanyName"]));
					}
				}

				
				#### update entries				
				$this->load->model('trakentries', '', TRUE);
				$companyentries = $this->trakentries->GetEntry(array ("Client"=>$OldClientInfo->CompanyName));
				if ($companyentries ){
					foreach ($companyentries as $row){
						$updated_entry = $this->trakentries->UpdateEntry(array("id"=>$row->id,"Client"=>$FormValues["CompanyName"],"Code"=>$FormValues["ClientCode"]));
					}
				}/**/


			}

			$Message='Client '.$FormFields['CompanyName'].' has been updated.';						
			$this->session->set_flashdata('SuccessMessage',$Message);
			//die( $Message);
			
			redirect('clients/zt2016_client_info/'.$SafeclientName, 'refresh');
		}
		
		else{
			
		$Message="There was an error updating ".$FormValues["CompanyName"].", which has not been updated.\n";
		
		//die($Message);
		
		$this->session->set_flashdata('ErrorMessage',$Message);
		redirect('clients/zt2016_client_edit/'.$SafeclientName, 'refresh');
			
		}	
		
	}
}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>