<?php

class Zt2016_job_delete extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));

		$zowuser=_superuseronly(); 

		$EntryID=$this->uri->segment(3);



		######### check if client name is provided
		 if (empty ($EntryID)) {
				
			$Message='No job ID provided for deletion.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('trash/zt2016_trash', 'refresh'); 
		 }

			#### retrieve current client info from db		
			$this->load->model('zt2016_entries_model', '', TRUE);
			$JobInfo = $this->zt2016_entries_model->GetEntry($options = array ("id"=>$EntryID));
			if (!$JobInfo){
				$Message='Job ID provided for deletion does not exist.';	
				$this->session->set_flashdata('ErrorMessage',$Message);
				//redirect('trash/zt2016_trash', 'refresh');
			}	

			if ($JobInfo->Trash!=1){
				$Message="Job #".$JobInfo->id.' is not trashed. Jobss must be trashed before they can be permanently deleted.';	
				$this->session->set_flashdata('ErrorMessage',$Message);
				redirect('tracking/zt2016_edit_job/$EntryID', 'refresh');
			}

			########### delete client from db
			$DeletedClient = $this->zt2016_entries_model->DeleteEntry($options = array ("id"=>$JobInfo->id));

			if ($DeletedClient==1){

				$Message="Job #".$JobInfo->id." was permanently deleted from the database.\n";
				$this->session->set_flashdata('SuccessMessage',$Message);
				//redirect('trash/zt2016_trash', 'refresh');
			} else {

				$Message='There was an error deleting '. $JobInfo->id.".\n";
				$this->session->set_flashdata('ErrorMessage',$Message);
			}	
															

		redirect('trash/zt2016_trash', 'refresh');
		
	}
}

/* End of file Zt2016_trash.php */
/* Location: ./system/application/controllers/trash/Zt2016_trash.php */
?>