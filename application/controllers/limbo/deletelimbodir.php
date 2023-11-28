<?php

class Deletelimbodir extends MY_Controller {
	
	function index()
	{
		$this->load->helper(array('url'));	
		
		//extract file url
		$numsegments =  $this->uri->total_segments();
		$deletefile="";
		for ($i = 3; $i <= $numsegments; $i++) {
		   $deletefile.="/".$this->uri->segment($i);
		}
		$deletefile= str_replace("%20", " ", $deletefile);
		//delete file
		if ($numsegments>2) {
			$this->_limbodeletedir($deletefile);
		}

		//find redirection dir
		$redirectdir="";
		for ($i = 3; $i <= $numsegments-1; $i++) {
		   $redirectdir.="/".$this->uri->segment($i);
		}
		redirect(Base_Url().'limbo/limbodir'.$redirectdir);
		 
	}

	// ################## ftp content ##################
	function _limbodeletedir($deletefile)
	{
		$deletefile= $_SERVER['DOCUMENT_ROOT']."/zowtempa/etc/limbo/".$deletefile;
		if (file_exists($deletefile)) {
			rmdir ($deletefile);
		}
		else {
			echo "Directory not found:".$deletefile;	
		}
		return ;
	}

	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>