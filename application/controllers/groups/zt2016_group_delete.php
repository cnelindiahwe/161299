<?php

class Zt2016_group_delete extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_groups','userpermissions'));

		$zowuser=_superuseronly(); 

		$GroupID=$this->uri->segment(3);



		######### check if group name is provided
		 if (empty ($GroupID)) {
				
			$Message='No group ID provided for deletion.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
		 }
		 else {
			#### retrieve current client info from db		
			$this->load->model('zt2016_groups_model', '', TRUE);
			$GroupInfo = $this->zt2016_groups_model->GetGroup($options = array ("ID"=>$GroupID));
			if (!$GroupInfo){
				$Message='Group ID provided for deletion does not exist.';	
				$this->session->set_flashdata('ErrorMessage',$Message);
				//redirect('trash/zt2016_trash', 'refresh');
			}	 	
	 		else {
				#### check if client is trashed		
				if ($GroupInfo->Trash!=1){
					$Message=$GroupInfo->GroupName.' is not trashed. Groups must be trashed before they can be permanently deleted.';	
					$this->session->set_flashdata('ErrorMessage',$Message);
					//redirect('trash/zt2016_trash', 'refresh');
				}
				else {
					########### check if group has clients 
					$this->load->model('zt2016_clients_model', '', TRUE);
					$GroupClients = $this->zt2016_clients_model-> GetClient($options = array('Group' => $GroupInfo->GroupName));
					
					if ($GroupClients){
						
						$Message=$clientName." has clients and thus cannot be trashed.\n";
						$this->session->set_flashdata('ErrorMessage',$Message);
						redirect('groups/zt2016_group_info/'.$GroupInfo->GroupName, 'refresh');
					} 
					
					else {
						########### delete client from db
						$DeletedGroup = $this->zt2016_groups_model->DeleteGroup($options = array ("ID"=>$GroupInfo->ID));

						if ($DeletedGroup==1){

							$Message=$GroupInfo->GroupName." was permanently deleted from the database.\n";
							$this->session->set_flashdata('SuccessMessage',$Message);
							//redirect('trash/zt2016_trash', 'refresh');
						} else {

							$Message='There was an error deleting '.$GroupInfo->GroupName.".\n";
							$this->session->set_flashdata('ErrorMessage',$Message);
						}	
															
					}//if ($ExistingEntries)
				}// ($GroupInfo->Trash!=1)
			}//if (!$GroupInfo)
		}//if (empty ($GroupID))
 
		redirect('trash/zt2016_trash', 'refresh');
		
	}
}

/* End of file Zt2016_trash.php */
/* Location: ./system/application/controllers/trash/Zt2016_trash.php */
?>