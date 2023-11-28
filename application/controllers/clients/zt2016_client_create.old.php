<?php

class Zt2016_client_create extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));

		$zowuser=_superuseronly(); 

		

		#### load rest of fields submitted via the form
		$FormFields=$_POST;
		$this->session->set_flashdata('FormValues',$FormFields);
		
		$SafeclientName=str_replace(" ", "_", $FormFields["CompanyName"]);
		$SafeclientName=str_replace("&", "~", $SafeclientName);


			##### field validation for required
			/*$Required = array ("CompanyName","ClientCode","ZOWContact","BasePrice","Currency");
			
			foreach ($Required as $RequiredField) {
				if (!array_key_exists($RequiredField,$FormFields)) {
					
					$Message="Required field ".$key." is missing.";	
					$this->session->set_flashdata('ErrorMessage',$Message);
					redirect('clients/zt2016_client_new', 'refresh');
				}				
			}
			*/

			##### field validation for required filled
			foreach ($FormFields as $key=>$value) {
				$required = array ("CompanyName","ClientCode","ZOWContact","BasePrice","Currency");
				
				if (in_array($key,$required) && $value==""){
					$Message="Required field ".$key." is missing.";
					
					$this->session->set_flashdata('ErrorMessage',$Message);
					redirect('clients/zt2016_client_edit/'.$SafeclientName, 'refresh');
				}
				else{
					$FormValues[$key]=trim($value);
					//echo $key.":".$FormValues[$key]."<br>";					
				}
			} 



		##### field validation for client code
		

			
			#### retrieve all clients info		
			$this->load->model('zt2016_clients_model', '', TRUE);
			$AllClientsInfo = $this->zt2016_clients_model->GetClient();
			
			#### exit if new client code is already used for other client
			foreach ($AllClientsInfo as $ClientLoop) { 
				if ($ClientLoop->ClientCode==$FormFields['ClientCode'] ){
											
					$Message='Client Code '.$FormFields['ClientCode'].' is in use. Please create a different one.';						
					$this->session->set_flashdata('ErrorMessage',$Message);
					
					redirect('clients/zt2016_client_new', 'refresh');
				}				
				elseif (strtoupper($ClientLoop->CompanyName)==strtoupper($FormFields['CompanyName'])){
					
					$Message='Company name '.$FormFields['CompanyName'].' is in use. Please create a different one.';						
					$this->session->set_flashdata('ErrorMessage',$Message);					
					redirect('clients/zt2016_client_new', 'refresh');
					
				} 
			}

		$created_client = $this->zt2016_clients_model->AddClient($FormValues);

		if($created_client)	{
			


			$Message='Client '.$FormFields['CompanyName'].' has been created.';						
			$this->session->set_flashdata('SuccessMessage',$Message);
			//die( $Message);
			
			$clientName=str_replace(" ", "_", $FormFields['CompanyName']);
			$SafeclientName=str_replace("&", "~", $SafeclientName);		
				
			redirect('clients/zt2016_client_info/'.$SafeclientName, 'refresh');
		}

		
		$Message="There was an error creating the new client, which has not been created.";
		
		//die($Message);
		
		$this->session->set_flashdata('ErrorMessage',$Message);
		redirect('clients/zt2016_client_edit/'.$SafeclientName, 'refresh');
					redirect('clients/zt2016_client_new', 'refresh');		
	}
}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>