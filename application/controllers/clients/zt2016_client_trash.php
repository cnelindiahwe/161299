<?php

class Zt2016_client_trash extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));

		$this->load->model('zt2016_clients_model', '', TRUE);
		$this->load->model('zt2016_entries_model', '', TRUE);
		
		$zowuser=_superuseronly(); 

		$templateData['ZOWuser']= _getCurrentUser();
		
		$SafeclientName=$this->uri->segment(3);

		########### check if there is a user name
		 if (empty ($SafeclientName)) {
		
			$Message="Trash client request was missing client in URL.\n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('clients/zt2016_clients', 'refresh');
		 
		 }
		 
		########### check if client is in the db
		$clientName=str_replace("_", " ", $SafeclientName);
		$clientName=str_replace("~", "&", $clientName);


		$ClientInfo = $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $clientName));
		
		if (!$ClientInfo){
			
			$Message="There is no client named ".$clientName."\n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('clients/zt2016_clients', 'refresh');
		}


		########### check if client has invoices 
		$this->load->model('zt2016_invoices_model', '', TRUE);
		
		$ClientEntries = $this->zt2016_entries_model->GetEntry($options = array('Client' => $clientName));
  
		if ($ClientEntries){
			
			$Message=$clientName." has booking entries and thus cannot be trashed.\n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('clients/zt2016_client_info/'.$SafeclientName, 'refresh');
		}


		########### update valid client trash field to 1

		$UpdatedClient = $this->zt2016_clients_model->UpdateClient($options = array("ID"=>$ClientInfo->ID, 'Trash'=>'1'));

		if (!$UpdatedClient){
			die ($this->db->last_query());
			$Message="There was a problem trashing ".$clientName.". \n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('clients/zt2016_clients', 'refresh');
		}		

		########### return with success
		
		$Message=$clientName." has been trashed.\n";
		$this->session->set_flashdata('SuccessMessage',$Message);
		redirect('clients/zt2016_clients', 'refresh');


	}	

}

/* End of file Zt2016_client_delete.php */
/* Location: ./system/application/controllers/clients/Zt2016_client_delete.php */
?>