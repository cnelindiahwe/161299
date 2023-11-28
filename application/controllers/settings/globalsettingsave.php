<?php

class globalsettingsave extends My_Controller {


	
	function index()
	{
        
		// $this->load->library(array('session')); 
		// $this->load->helper(array('zt2016_clients','userpermissions'));

		// $zowuser=_superuseronly(); 

		

		#### load rest of fields submitted via the form
		$FormFields=$_POST;
		$this->session->set_flashdata('FormValues',$FormFields);
		

			##### field validation for required values
			foreach ($FormFields as $key=>$value) {
				$required = array ("fromAddress","contactName","mobNumber","email","bankAccount");
				
				if (in_array($key,$required) && $value==""){
					$Message="Required field ".$key." is missing.";
					
					$this->session->set_flashdata('ErrorMessage',$Message);
					redirect('settings/globalsetting', 'refresh');
				}
				else{
					$FormValues[$key]=trim($value);
				}
			} 
         
		##### field validation for client code
		

			
		#### update global setting		
		$this->load->model('globalSettingModal', '', TRUE);

		$created_client = $this->globalSettingModal->UpdateGlobalSetting($FormValues);

		if($FormValues['CCmail'] || $FormValues['emailBody']){
			$Message='Update Invoice Email Setting ';						
			$this->session->set_flashdata('SuccessMessage',$Message);
			redirect('settings/globalemailsetting', 'refresh');
		}else{
			$Message='Update Invoice Setting ';						
			$this->session->set_flashdata('SuccessMessage',$Message);
				
			redirect('settings/globalsetting', 'refresh');
		}
	}
}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>