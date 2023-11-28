<?php

class Zt2016_downloadclientmaterials extends MY_Controller {
	
	function index()
	
	{
		
		
		$this->load->helper(array('url','form','download'));
			
		// Extract dir url
		$numsegments =  $this->uri->total_segments();
		
		// Check that directory name is provided
		// by ensuring there are at least 3 uri segments
		// the first 2 being "limbo/zt2016_deletelimbodir/"
		
		if ($numsegments<3) {
			$Message= "No file name provided.";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect(Base_Url().'clients/zt2016_manageclientmaterials/');
		}	
		
		
		$dirname = $this->uri->segment(3);

		
		if ( $dirname !="group") {
			$filename = $this->uri->segment(4);
			
		} else {
			$dirname = $this->uri->segment(3)."/".$this->uri->segment(4);
			$filename = $this->uri->segment(5);
		}

		$downloadfile= $_SERVER['DOCUMENT_ROOT'].'/zowtempa/etc/clientmaterials/'.$dirname."/".$filename;
		$downloadfile= str_replace("%20", " ", $downloadfile);

		
		//http://www.jonasjohn.de/snippets/php/headers.htm
		//http://stackoverflow.com/questions/597159/sending-large-files-reliably-in-php
		if (file_exists($downloadfile)) {
						
			
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$filename.'"'); 
			header('Content-Transfer-Encoding: binary');				

			$f = fopen($downloadfile, 'r');
			while(!feof($f)){
				print fgets($f, 1024);
			}
			fclose($f);
			
		}	
		else {
			
			$Message= "Cannot find a file named '".$dirname."/".$filename."'";
			//die ($Message);
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect(Base_Url().'clients/zt2016_manageclientmaterials/'.$dirname);
		}
	}
}

/* End of file zt2016_downloadmaterials.php */
/* Location: ./system/application/controllers/clients/zt2016_downloadmaterials.php*/
?>