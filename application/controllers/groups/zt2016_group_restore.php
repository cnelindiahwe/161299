<?php

class Zt2016_group_restore extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));
		$this->load->model('zt2016_groups_model', '', TRUE);
		
		$zowuser=_superuseronly(); 

		
		//$templateData['ZOWuser']= _getCurrentUser();
		

		$GroupID=$this->uri->segment(3);



		######### check if client name is provided
		 if (empty ($GroupID)) {
				
			$Message='No group ID provided for deletion.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('trash/zt2016_trash', 'refresh');
		 }
		 
		########### check if client is in the db
	
		
		$GroupInfo = $this->zt2016_groups_model->GetGroup($options = array('ID' => $GroupID));
		
		if (!$GroupInfo){
			
			$Message="There is no group with supplied ID (".$GroupID.")\n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('trash/zt2016_trash', 'refresh');
		}


		########### update valid client trash field to 0

		$UpdatedGroup = $this->zt2016_groups_model->UpdateGroup($options = array("ID"=>$GroupInfo->ID, 'Trash'=>'0'));

		if (!$UpdatedGroup){
			die ($this->db->last_query());
			$Message="There was a problem restoring ".$GroupInfo->GroupName.". \n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('trash/zt2016_trash', 'refresh');
		}		

		########### return with success
		
		$Message=$GroupInfo->GroupName." has been restored.\n";
		$this->session->set_flashdata('SuccessMessage',$Message);
		redirect('groups/zt2016_groups', 'refresh');


	}	

}

/* End of file Zt2016_client_delete.php */
/* Location: ./system/application/controllers/clients/Zt2016_client_delete.php */
?>