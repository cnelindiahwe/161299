<?php

class zt2016_group_create extends MY_Controller {

	
	function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','zt2016_groups','general','userpermissions'));

		$zowuser=_superuseronly(); 
		
		
		
		$FormValues['GroupName']=$this->input->post('GroupName');
		if (empty ($FormValues['GroupName'])) {			 
			$this->session->set_flashdata('ErrorMessage','Failed to create group: group name was not provided.');
			redirect('groups/zt2016_group_new', 'refresh');
		 }else{
			$FormValues['GroupName']=strtoupper($FormValues['GroupName']);
		}

		$FormValues['DefaultPrice']=$this->input->post('DefaultPrice');
		if (empty ($FormValues['DefaultPrice'])) {			 
			$this->session->set_flashdata('ErrorMessage','Failed to create group: default price was not provided.');
			redirect('groups/zt2016_group_new', 'refresh');
		 } 
		
		$FormValues['DefaultCurrency']=$this->input->post('DefaultCurrency');
		if (empty ($FormValues['DefaultPrice'])) {			 
			$this->session->set_flashdata('ErrorMessage','Failed to create group: default currency was not provided.');
			redirect('groups/zt2016_group_new', 'refresh');
		 } 
		
		$FormValues['DefaultPaymentDays']=$this->input->post('DefaultPaymentDays');
		if (empty ($FormValues['DefaultPrice'])) {			 
			$this->session->set_flashdata('ErrorMessage','Failed to create group: default payment days was not provided.');
			redirect('groups/zt2016_group_new', 'refresh');
		 } 		
		
		$FormValues['DefaultCountry']=$this->input->post('DefaultCountry');
		if (empty ($FormValues['DefaultCountry'])) {			 
			$this->session->set_flashdata('ErrorMessage','Failed to create group: default country was not provided.');
			redirect('groups/zt2016_group_new', 'refresh');
		 } 		
		
		$FormValues['DefaultTimeZone']=$this->input->post('DefaultTimeZone');
		if (empty ($FormValues['DefaultTimeZone'])) {			 
			$this->session->set_flashdata('ErrorMessage','Failed to create group: default time zone was not provided.');
			redirect('groups/zt2016_group_new', 'refresh');
		 } 		
		$FormValues['Active']=1;
		$FormValues['Trash']=0;

		#### retrieve all clients info		
		$this->load->model('zt2016_groups_model', '', TRUE);
		$GroupsData = $this->zt2016_groups_model->GetGroup($options = array('Trash'=>'0','sortBy'=>'GroupName','sortDirection'=>'ASC	'));


		#### exit if new group name is already used for other group
		foreach ($GroupsData as $GroupLoop) { 
			if ($GroupLoop->GroupName==$FormValues['GroupName'] ){
				
				$Message='Group Name '.$GroupLoop->GroupName.' is in use. Please use a different one.';						
				$this->session->set_flashdata('ErrorMessage',$Message);
				
				redirect('groups/zt2016_group_new', 'refresh');
			}				
		}
		
		
		$created_group = $this->zt2016_groups_model->AddGroup($FormValues);

		if($created_group)	{
			


			$Message='Group '.$FormValues['GroupName'].' has been created.';						
			$this->session->set_flashdata('SuccessMessage',$Message);
			//die( $Message);
				
			redirect('groups/zt2016_Groups', 'refresh');
		}

		
		$Message="There was an error creating the new group, which has not been created.";
		
		//die($Message);
		
		$this->session->set_flashdata('ErrorMessage',$Message);
		redirect('groups/zt2016_groups', 'refresh');

	}	


}

/* End of file editclient.php */
/* Location: ./system/application/controllers/groups/zt2016_group_info.php */
?>