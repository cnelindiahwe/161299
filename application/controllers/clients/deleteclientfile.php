<?php

class Deleteclientfile extends MY_Controller {
	
	function index()
	{
			
		$this->load->helper(array('url','form','download'));	
		$clientcode = $this->uri->segment(3);
		$filename = $this->uri->segment(4);
		
		echo $clientcode."/".$filename;
			
		//if ($filename!="" && $clientcode!="") {
		//	$deletefile=$clientcode."/".$filename;
		//	$this->_limbodeletefile($deletefile);
		//}
			
			
			
		/*
		 * $this->load->helper(array('url'));	
		//extract file url
		$numsegments =  $this->uri->total_segments();
		$deletefile="";
		for ($i = 3; $i <= $numsegments; $i++) {
		   $deletefile.="/".$this->uri->segment($i);
		}
		$deletefile= str_replace("%20", " ", $deletefile);
		//delete file
		if ($numsegments>2) {
			$this->_limbodeletefile($deletefile);
		}
		*/
		
		
		 //find redirection dir
		//redirect(Base_Url().'clients/manageclientmaterials/'.$clientcode);


	 }
	// ################## ftp content ##################
	function _limbodeletefile($deletefile)
	{
		$deletefile= $_SERVER['NFSN_SITE_ROOT'] . 'protected/clientmaterials/'.$deletefile;	
		if (file_exists($deletefile)) {
			unlink ($deletefile);
		}
		else {
			echo "File not found:".$deletefile;	
		}
		return ;
	}


	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>