<?php

class Deleteftpfile extends MY_Controller {
	
	function index()
	{
		$this->load->library('ftp');
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
			$this->_FTPdeletefile($deletefile);
		}
		//find redirection dir
		$redirectdir="";
		for ($i = 3; $i <= $numsegments-1; $i++) {
		   $redirectdir.="/".$this->uri->segment($i);
		}
		redirect(Base_Url().'/limbo/ftpdir/'.$redirectdir);


	 }
	// ################## ftp content ##################
	function _FTPdeletefile($deletefile)
	{
		$deletefile= '/private/limbo'.$deletefile;
		$this->ftp->connect();
		$this->ftp->delete_file($deletefile);
		$this->ftp->close();	
		return ;
	}


	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>