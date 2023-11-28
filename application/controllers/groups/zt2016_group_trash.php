<?php

class zt2016_group_trash extends MY_Controller {

	
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
		
		
		
		$GroupName=$this->input->post('GroupName');		
		if (empty ($GroupName)) {	
			$this->session->set_flashdata('ErrorMessage','Failed to delete group: group name was not provided.');
			redirect('groups/zt2016_groups', 'refresh');
		 }

		#### retrieve group info		
		$this->load->model('zt2016_groups_model', '', TRUE);
		$GroupData = $this->zt2016_groups_model->GetGroup($options = array('Trash'=>'0','GroupName'=>$GroupName));


		if (empty ($GroupData)) {			 
			$this->session->set_flashdata('ErrorMessage','Failed to delete group: group was not found.');
			redirect('groups/zt2016_groups', 'refresh');
		 }		

		#### retrieve group clients		
		$this->load->model('zt2016_clients_model', '', TRUE);
		$GroupClients = $this->zt2016_clients_model->GetClient($options = array('Group'=>$GroupName));
		
		
		if (!empty ($GroupClients)) {			 
			$this->session->set_flashdata('ErrorMessage','Failed to delete group: group has clients assigned to it.');
			redirect('groups/zt2016_group_info/'.$GroupName, 'refresh');
		 }	
		
	
		########### update valid client trash field to 1

		$UpdatedGroup = $this->zt2016_groups_model->UpdateGroup($options = array("ID"=>$GroupData->ID, 'Trash'=>'1'));

		if (!$UpdatedGroup){
			$Message="There was a problem trashing ".$GroupName.". \n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('groups/zt2016_group_edit/'.$GroupName, 'refresh');
		}		

		########### return with success
		
		$Message=$GroupName." has been trashed.\n";
		$this->session->set_flashdata('SuccessMessage',$Message);
		redirect('groups/zt2016_groups', 'refresh');
		
	}

}

/* End of file editclient.php */
/* Location: ./system/application/controllers/groups/zt2016_group_info.php */
?>