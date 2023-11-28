<?php

class Zt2016_createlimbodir extends MY_Controller {
	
	function index()
	{

		$this->load->helper(array('url'));	
		
		$currentdir = $this->input->post('currentdir');
		$createdirname = $this->input->post('createdirname');
		
		if ($createdirname!="") {
			$createdirname=str_replace(".","-" ,$createdirname);
			$createdirname=str_replace("/","-" ,$createdirname);
			$createdirname=str_replace("\\","-" ,$createdirname);
			
			if ($currentdir=="") {
				$finalcreatedirname=dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/".$createdirname;
			} else{
				$finalcreatedirname=dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/".$currentdir."/".$createdirname;
			}
			
			### check that directory does not exist
			if (!is_dir($finalcreatedirname)) {
	
				mkdir($finalcreatedirname,0777, true);
				
				if (is_dir($finalcreatedirname)) {
					
					### directory created
					if ($currentdir=="") {	
						$Message= "Directory 'limbo/".$createdirname."' created.";
					} else {
						$Message= "Directory 'limbo/".$currentdir."/".$createdirname."' created.";
					}
					$this->session->set_flashdata('SuccessMessage',$Message);					
				} else{
					
					if ($currentdir=="") {	
						$Message= "Could not create directory 'limbo/".$createdirname."' due to an unknown problem.";
					} else {
						$Message= "Could not create directory 'limbo/".$currentdir."/".$createdirname."' due to an unknown problem.";
					}
					$this->session->set_flashdata('ErrorMessage',$Message);					
				}				
			} 
			### if directory exists
			else {
				if ($currentdir=="") {	
					$Message= "Directory 'limbo/".$createdirname."' exists.";
				} else {
					$Message= "Directory 'limbo/".$currentdir."/".$createdirname."' exists.";
				}
				$this->session->set_flashdata('ErrorMessage',$Message);
			}
		}
		if ($currentdir=="") {	
			redirect(Base_Url().'limbo/zt2016_limbo');
		}
		else  {	
			redirect(Base_Url().'limbo/zt2016_limbodir/'.$currentdir);
		}
	}



	
}

/* End of file ajax_createdir */
/* Location: ./system/application/controllers/limbo/ajax_createdir */
?>