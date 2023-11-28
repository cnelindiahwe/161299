<?php

class Zt2016_client_restore extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));
		$this->load->model('zt2016_clients_model', '', TRUE);
		
		$zowuser=_superuseronly(); 

		
		//$templateData['ZOWuser']= _getCurrentUser();
		

		$ClientID=$this->uri->segment(3);



		######### check if client name is provided
		 if (empty ($ClientID)) {
				
			$Message='No client ID provided for deletion.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('trash/zt2016_trash', 'refresh');
		 }
		 
		########### check if client is in the db
	
		
		$ClientInfo = $this->zt2016_clients_model->GetClient($options = array('ID' => $ClientID));
		
		if (!$ClientInfo){
			
			$Message="There is no client with supplied ID (".$ClientID.")\n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('trash/zt2016_trash', 'refresh');
		}


		########### update valid client trash field to 0

		$UpdatedClient = $this->zt2016_clients_model->UpdateClient($options = array("ID"=>$ClientInfo->ID, 'Trash'=>'0'));

		if (!$UpdatedClient){
			die ($this->db->last_query());
			$Message="There was a problem restoring ".$ClientInfo->CompanyName.". \n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('trash/zt2016_trash', 'refresh');
		}		

		########### return with success
		
		$Message=$ClientInfo->CompanyName." has been restored.\n";
		$this->session->set_flashdata('SuccessMessage',$Message);
		redirect('clients/zt2016_clients', 'refresh');


	}	

}

/* End of file Zt2016_client_delete.php */
/* Location: ./system/application/controllers/clients/Zt2016_client_delete.php */
?>