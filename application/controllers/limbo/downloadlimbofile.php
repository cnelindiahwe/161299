<?php

class Downloadlimbofile extends MY_Controller {
	
	function index()
	{
			
		$this->load->helper(array('url','form','download'));	
		
		//extract file url
		$numsegments =  $this->uri->total_segments();
		$downloadfile="";
		for ($i = 3; $i <= $numsegments; $i++) {
		   $downloadfile.="/".$this->uri->segment($i);
		}
		$downloadfile= str_replace("%20", " " , $downloadfile);
		$downloadfile= $_SERVER['DOCUMENT_ROOT']."/zowtempa/etc/limbo/".$downloadfile;

		$filename =$this->uri->segment($numsegments);
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



	 }


	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>