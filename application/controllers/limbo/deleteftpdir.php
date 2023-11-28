<?php

class Deleteftpdir extends MY_Controller {
	
	function index()
	{
		$this->load->library('ftp');
		$this->load->helper(array('url'));	
		$numsegments =  $this->uri->total_segments();
		$deletedir="";
		for ($i = 3; $i <= $numsegments; $i++) {
		   $deletedir.="/".$this->uri->segment($i);
		}
		$deletedir= str_replace("%20", " ", $deletedir);
		if ($numsegments>2) {
			$this->_FTPdeletedir($deletedir);
		}
		if ($numsegments<3) {	
			//redirect(Base_Url().'/limbo');
		}
		else  {
			$redirectdir="";
			for ($i = 3; $i <= $numsegments-1; $i++) {
			 //  $redirectdir.="/".$this->uri->segment($i);
			}
			//redirect(Base_Url().'/limbo/ftpdir/'.$redirectdir);
		}

	 }
	// ################## ftp content ##################
	function _FTPdeletedir($deletedir)
	{
		echo '/private/limbo'.$deletedir.'/';
		$this->ftp->connect();
		//$this->ftp->delete_dir('/private/limbo'.$deletedir.'/');
		//$this->ftp->delete_dir('/private/limbo/test/');
		$this->ftp->close();	
		return ;
	}


	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>