<?php

class Zt2016_downloadlimbofile extends CI_Controller {
	
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
			redirect(Base_Url().'limbo/zt2016_limbo');
		}			
		
		// Build final name of directory for deletion
		// By deleting the first 2 segments of the url
		
		$downloadfile=str_replace("limbo/zt2016_downloadlimbofile/", "", $this->uri->uri_string());
		$downloadfile= str_replace("%20", " ", $downloadfile);
		
		 $downloadfile= dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/".$downloadfile;

		$filename =$this->uri->segment($numsegments);
		
		
		if (file_exists($downloadfile)) {

			// http://www.jonasjohn.de/snippets/php/headers.htm
			// http://stackoverflow.com/questions/597159/sending-large-files-reliably-in-php
			
// 			header('Content-Type: application/octet-stream');
// 			header('Content-Disposition: attachment; filename="'.$filename.'"'); 
// 			header('Content-Transfer-Encoding: binary');	
			define('CHUNK_SIZE', 1024*1024);
		    ini_set('memory_limit', '2048M');
		    @ini_set('zlib.output_compression', 'Off');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$filename.'"'); 
			header('Content-Transfer-Encoding: binary');
			header("Pragma: public");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Length: ' . filesize($downloadfile));
			$f = fopen($downloadfile, 'r');
			while(!feof($f)){
			    print fgets($f, 1024);
			}
			fclose($f);

		} else{
			if ($numsegments<3) {
				$Message= "Cannot find a file named '.$filename.'";
				$this->session->set_flashdata('ErrorMessage',$Message);
				redirect(Base_Url().'limbo/zt2016_limbo');
			}			
		}

	 }
	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>