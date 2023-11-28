<?php

class Zt2016_job_restore extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));
		$this->load->model('zt2016_entries_model', '', TRUE);
		
		//$zowuser=_superuseronly(); 

		
		//$templateData['ZOWuser']= _getCurrentUser();
		

		$EntryID=$this->uri->segment(3);



		######### check if client name is provided
		 if (empty ($EntryID)) {
				
			$Message='No job ID provided for deletion.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('trash/zt2016_trash', 'refresh');
		 }
		 
		########### check if client is in the db
	
		
		$EntryInfo = $this->zt2016_entries_model->GetEntry($options = array('id' => $EntryID));
		
		if (!$EntryInfo){
			
			$Message="There is no job with supplied ID (".$ClientID.")\n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('trash/zt2016_trash', 'refresh');
		}


		########### update valid client trash field to 0

		$UpdatedEntry = $this->zt2016_entries_model->UpdateEntry($options = array("id"=>$EntryInfo->id, 'Trash'=>'0'));

		if (!$UpdatedEntry){
			die ($this->db->last_query());
			$Message="There was a problem restoring job #".$EntryInfo->id.". \n";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('trash/zt2016_trash', 'refresh');
		}		

		########### return with success
		
		$Message="Job #".$EntryInfo->id." has been restored.\n";
		$this->session->set_flashdata('SuccessMessage',$Message);
		redirect('tracking/zt2016_tracking', 'refresh');


	}	

}

/* End of file zt2016_job_restore.php */
/* Location: ./system/application/controllers/tracking/zt2016_job_restore.php */
?>