<?php

class Zt2016_uploadlimbofile extends MY_Controller {
	
	function index()
	{
		
		$currentdir=$this->input->post('currentdir');
		
		// Check $_FILES['upfile']['error'] value.
		switch ($_FILES['fileuploadname']['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new RuntimeException('No file sent.');
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new RuntimeException('Exceeded filesize limit.');
			default:
				throw new RuntimeException('Unknown errors.');
		}

		$sanitizedfilename=$this->security->sanitize_filename ($_FILES["fileuploadname"]["name"]);
		$sanitizedfilename=url_title($sanitizedfilename,"_");

		
		#if file already exists
		if (file_exists(dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/".$currentdir."/". $sanitizedfilename)) 	
		{
			
			$Message = "limbo/".$currentdir."/".$_FILES["fileuploadname"]["name"] . " already exists. Please delete it first.";
			$this->session->set_flashdata('ErrorMessage',$Message);

		}
		#fiel uplpaed
		else
		{
			if (is_uploaded_file($_FILES['fileuploadname']['tmp_name'])) {
				move_uploaded_file($_FILES["fileuploadname"]["tmp_name"],dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/".$currentdir."/".$sanitizedfilename);
				
				$Message = "limbo/".$currentdir."/".$_FILES["fileuploadname"]["name"] . " uploaded.";
				$this->session->set_flashdata('SuccessMessage',$Message);
				
			} else{
				
				$Message = "limbo/".$currentdir."/".$_FILES["fileuploadname"]["name"] . " could not be uploaded.";
				$this->session->set_flashdata('ErrorMessage',$Message);
			
			}
		}
		if ($currentdir=="" ){
			redirect(Base_Url().'limbo/zt2016_limbo'); 
		} else {
			redirect(Base_Url().'limbo/zt2016_limbodir/'.$currentdir);
		}

	}

	
}

/* End of file uploadlimbofile.php */
/* Location: ./system/application/controllers/limbo/uploadlimbofile.php */
?>