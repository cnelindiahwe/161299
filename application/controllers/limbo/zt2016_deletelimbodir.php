<?php

class Zt2016_deletelimbodir extends MY_Controller {
	
	function index()
	{
		// ################## set up  directory deletion ##################
		
		$this->load->helper(array('url'));	
		
		// Extract dir url
		$numsegments =  $this->uri->total_segments();
		
		// Check that directory name is provided
		// by ensuring there are at least 3 uri segments
		// the first 2 being "limbo/zt2016_deletelimbodir/"
		
		if ($numsegments<3) {
			$Message= "No directory name provided.";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect(Base_Url().'limbo/zt2016_limbo');
		}		
				
		// Build final name of directory for deletion
		// By deleting the first 2 segments of the url
		
		$deletedir=str_replace("limbo/zt2016_deletelimbodir/", "", $this->uri->uri_string());
		$deletedir= str_replace("%20", " ", $deletedir);
		
		// echo $deletedir. " : ". $this->uri->uri_string()."<br/>";
		
		// run deletion function
		$this->_limbodeletedir($deletedir);
		
		
		// redirect to base limbo
		// if only level 1 dir deletion
		if ($numsegments==3) {		
			redirect(Base_Url().'limbo/zt2016_limbo');
		}
		// redirect to limbo subdir(s)
		// if level 2 dir deletion
		else {
			for ($i = 3; $i <= $numsegments-1; $i++) {
			   $redirectdir.="/".$this->uri->segment($i);
			}
			redirect(Base_Url().'limbo/zt2016_limbodir'.$redirectdir);
		} 

		 
	}

	// ################## delete directory  ##################
	function _limbodeletedir($deletedir)
	{

		// build file system directory name
		$fulldeletedir=$_SERVER['DOCUMENT_ROOT']."/zowtempa/etc/limbo/".$deletedir;
		
		// check if directory exists
		if (file_exists($fulldeletedir)) {
			
			// delete directory
			rmdir ($fulldeletedir);
			
			// check if directory still exists
			// after deletion
			if (file_exists($fulldeletedir)) {
			
				$Message= "Directory 'limbo/".$deletedir."' could not be deleted.";
				$this->session->set_flashdata('ErrorMessage',$Message);
			
			}else{
			
				$Message= "Directory 'limbo/".$deletedir."' deleted.";
				$this->session->set_flashdata('SuccessMessage',$Message);
			}
		}
		
		else {
			
			$Message= "Directory 'limbo/".$deletedir."' not found.";
			
			$this->session->set_flashdata('ErrorMessage',$Message);
		}
		
		return ;
		
	}
	
}

/* End of file zt2016_limbodeletedir.php */
/* Location: ./system/application/controllers/limbo/zt2016_limbodeletedir.php */
?>