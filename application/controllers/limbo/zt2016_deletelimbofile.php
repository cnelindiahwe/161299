<?php

class Zt2016_deletelimbofile extends MY_Controller {
	
	function index()
	{
		
		// ################## set up file deletion ##################
		
		$this->load->helper(array('url'));	
		
		// Extract dir url
		$numsegments =  $this->uri->total_segments();
		
		// Check that directory name is provided
		// by ensuring there are at least 3 uri segments
		// the first 2 being "limbo/zt2016_deletelimbodir/"
		
		if ($numsegments<3) {
			$Message= "No file name provided.";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect(Base_Url().'limbo/zt2016_limbo');
		}	

		// Build final name of directory for deletion
		// By deleting the first 2 segments of the url
		
		$deletefile=str_replace("limbo/zt2016_deletelimbofile/", "", $this->uri->uri_string());
		$deletefile= str_replace("%20", " ", $deletefile);
		
		
		// run deletion function
		$this->_limbodeletefile($deletefile);
		
		// redirect to base limbo
		// if only level 1 file deletion
		if ($numsegments==3) {		
			redirect(Base_Url().'limbo/zt2016_limbo');
		}
		
		// redirect to limbo subdir(s)
		// if level 2 file deletion
		else {
			for ($i = 3; $i <= $numsegments-1; $i++) {
			   $redirectdir.="/".$this->uri->segment($i);
			}
			redirect(Base_Url().'limbo/zt2016_limbodir'.$redirectdir);
		} 		
		

	 }
	// ################## ftp content ##################
	function _limbodeletefile($deletefile)
	{

		// build file system directory name
		 $fulldeletefile=dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/".$deletefile;
		// check if directory exists
		if (file_exists($fulldeletefile)) {
			
			// delete file
			unlink ($fulldeletefile);
			
			// check if directory still exists
			// after deletion
			if (file_exists($fulldeletedir)) {
			
				$Message= "File 'limbo/".$deletefile."' could not be deleted.";
				$this->session->set_flashdata('ErrorMessage',$Message);
			
			}else{
			
				$Message= "File 'limbo/".$deletefile."' deleted.";
				$this->session->set_flashdata('SuccessMessage',$Message);
			}
		}
		
		else {
			
			$Message= "File 'limbo/".$deletefile."' not found.";
			$this->session->set_flashdata('ErrorMessage',$Message);
		}
		
		return ;
		
	}


	
}

/* End of file Zt2016_deletelimbofile.php */
/* Location: ./system/application/controllers/limbo/zt2016_deletelimbofile */
?>