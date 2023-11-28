<?php

class Zt2016_deleteclientmaterials extends MY_Controller {
	
	function index()
	{
			
		$this->load->helper(array('url'));	
		
		$clientcode = $this->uri->segment(3);
		$dirname = $this->uri->segment(4);
		if ( $dirname !="group") {
			$filename = $this->uri->segment(5);
			
		} else {
			$dirname = $this->uri->segment(4)."/".$this->uri->segment(5);
			$filename = $this->uri->segment(6);
		}
			
		if ($filename!="" && $dirname!="") {
			$deletefile=$dirname."/".$filename;
			$this->_limbodeletefile($deletefile,$dirname,$clientcode);
		}
			
		
		//redirect(Base_Url().'clients/zt2016_manageclientmaterials/'.$dirname);


	 }
	// ################## ftp content ##################
	function _limbodeletefile($deletefile,$dirname,$clientcode)
	{
		$deletefile= $_SERVER['DOCUMENT_ROOT'].'/zowtempa/etc/clientmaterials/'.$deletefile;	
		if (file_exists($deletefile)) {
				
			unlink ($deletefile);
			
			redirect(Base_Url().'clients/zt2016_manageclientmaterials/'.$clientcode);

		}
		else {
			echo "<p>File not found:".$deletefile."</p>";	
			echo "<p><a href=\"".Base_Url().'clients/zt2016_manageclientmaterials/'.$clientcode."\">Please try again</a></p>";	

		}
		return ;
	}


	
}

/* End of file zt2016_deleteclientmaterials */
/* Location: ./system/application/controllers/clients/zt2016_deleteclientmaterials */
?>