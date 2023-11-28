<?php

class Zt2016_downloaddbbackup extends MY_Controller {
	
	function index()
	{

		$this->load->helper(array('url','form','download'));	
		
		# Check that url correctly provides a file name
		# by ensuring there are at least 3 uri segments
		# the first 2 being "export/zt2016_downloaddbbackup/"
		$numsegments =  $this->uri->total_segments();		
		

		# If url does not correctly provide a file name
		# return to export pae with an error message
		if ($numsegments<3) {
			$Message= "No db backup file name provided.";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect(Base_Url().'export/zt2016_export');
		}			
		
		# Build final name of fiel for download
		# By deleting the first 2 segments of the url
		
		$downloadfile=str_replace("export/zt2016_downloaddbbackup/", "", $this->uri->uri_string());
		$downloadfile= $_SERVER['DOCUMENT_ROOT']."/zowtempa/etc/database_bu/".$downloadfile;
		
		# Download file if it exists
		if (file_exists($downloadfile)) {

			# http://www.jonasjohn.de/snippets/php/headers.htm
			# http://stackoverflow.com/questions/597159/sending-large-files-reliably-in-php
			
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$downloadfile.'"'); 
			header('Content-Transfer-Encoding: binary');				
			$f = fopen($downloadfile, 'r');
			while(!feof($f)){
			    print fgets($f, 1024);
			}
			fclose($f);
		
		# Return to export page if file name provided is not found
		} else{
			if ($numsegments<3) {
				$Message= "Cannot find a db backup file named '.$filename.'";
				$this->session->set_flashdata('ErrorMessage',$Message);
				redirect(Base_Url().'limbo/zt2016_limbo');
			}			
		}

	 }
	
}

/* End of file zt2016_downloaddbbackup.php */
/* Location: ./system/application/controllers/export/zt2016_downloaddbbackup.php */
?>