<?php

class Zt2016_group_update extends My_Controller {


	
	function index()
	{
		$this->load->library(array('session')); 
		$this->load->helper(array('zt2016_clients','userpermissions'));

		$zowuser=_superuseronly(); 

		$GroupPostData=$this->input->post();
		
		#### retrieve client id from url
		if (empty($GroupPostData['ID'])) {
			$Message='Group not updated. Group ID not provided.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('groups/zt2016_groups', 'refresh');
		}
		
		#### retrieve current group info from db		
		$this->load->model('zt2016_groups_model', '', TRUE);
		$GroupDBData = $this->zt2016_groups_model->GetGroup($options = array ("ID"=>$GroupPostData['ID']));

		if (!$GroupDBData){
			$Message='Group not updated. Group does not exist in DB.';	
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('groups/zt2016_groups', 'refresh');
		}		

		##### field validation for required filled

		
		unset($GroupPostData['GroupFormSubmit']);
		$requiredkeys = array ("DefaultPrice","DefaultCurrency","DefaultCountry","DefaultTimeZone","DefaultPaymentDays");
		$duplicateflag=0;
		foreach ($GroupPostData as $key=>$value) {

			$GroupPostData[$key]=trim($value);

			if (in_array($key,$requiredkeys) && $value==""){
				$Message="Required field ".$key." is missing.";
				$this->session->set_flashdata('ErrorMessage',$Message);
				//$this->session->set_flashdata('FormValues',$FormValues);
				redirect('groups/zt2016_group_info/'.$GroupPostData['GroupName'], 'refresh');
			}
			else{
				$presentkeys[]=$key;	
			}
			if ($GroupPostData[$key]!=$GroupDBData->$key){
				//echo $key." ".$GroupPostData[$key]." ".$GroupDBData->$key;
				$duplicateflag=1;
			}
		} 

		
		##### required field validation 
		
		if (array_diff($requiredkeys, $presentkeys)){
				$missingkeys="";
				foreach (array_diff($requiredkeys, $presentkeys) as $requiredmissing) {
					$missingkeys.= $requiredmissing;
				}
				$Message="Required field(s) ".$missingkeys." are missing.";
				echo $Message;
				$this->session->set_flashdata('ErrorMessage',$Message);
				redirect('groups/zt2016_group_info/'.$GroupPostData['GroupName'], 'refresh');			
		}		

		
		##### field change from DB validation 

		if ($duplicateflag==0){
				
			$Message="Group not updated. The data in the form below is the same as the one on the DB.";

			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('groups/zt2016_group_edit/'.$GroupPostData['GroupName'], 'refresh');		
			
		}

		
		##### field validation for exisitng group name
		if ($GroupPostData['GroupName']!=$GroupDBData->GroupName){
			if ($GroupPostData['GroupName']=="DEFAULT"){
				$Message="Group not updated. DEFAULT group name cannot be changed.";
				$this->session->set_flashdata('ErrorMessage',$Message);
				redirect('groups/zt2016_group_edit/'.$GroupPostData['GroupName'], 'refresh');					
			}
			else {
				#### retrieve all group info		
				$this->load->model('zt2016_groups_model', '', TRUE);
				$AllGroupsData = $this->zt2016_groups_model->GetGroup();

				#### exit if new group name is already used for other group
				foreach ($AllGroupsData as $GroupDataLoop) { 
					if ($GroupDataLoop->GroupName==$GroupPostData['GroupName']){

						$Message='Client Code '.$GroupPostData['GroupName'].' is in use. Please create a different one.';						
						$this->session->set_flashdata('ErrorMessage',$Message);					
						redirect('groups/zt2016_group_info/'.$GroupPostData['GroupName'], 'refresh');			

					} 
				}

			}
		}
		
		$updated_group = $this->zt2016_groups_model->UpdateGroup($GroupPostData);
			
		
		if($updated_group)	{
			
			##Update databases if company name changes
			if 	($GroupPostData['GroupName']!=$GroupDBData->GroupName){

				$this->load->model('zt2016_clients_model', '', TRUE);
				$AllClientsData = $this->zt2016_clients_model->_UpdateClientsGroupName($GroupDBData->GroupName,$GroupPostData['GroupName']);							

			}

			$Message='Group '.$GroupPostData['GroupName'].' has been updated.';						
			$this->session->set_flashdata('SuccessMessage',$Message);
			
			redirect('groups/zt2016_group_info/'.$GroupPostData['GroupName'], 'refresh');
		}
		
		else{
			
			$Message="There was an error updating ".$GroupPostData['GroupName'].", which has not been updated.\n";

			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('groups/zt2016_group_info/'.$GroupPostData['GroupName'], 'refresh');
			
		}	
		
	}
}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>