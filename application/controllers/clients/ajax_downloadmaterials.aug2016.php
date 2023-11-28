<?php

class Ajax_downloadmaterials extends MY_Controller {


	
	function index()
	{
		$this->load->helper(array('url','form','download'));	
		$clientcode = $this->uri->segment(3);
		$filename = $this->uri->segment(4);
		//echo $clientcode."/".$filename;

				//extract file url
		//$numsegments =  $this->uri->total_segments();
		//$downloadfile="";
		//for ($i = 3; $i <= $numsegments; $i++) {
		//   $downloadfile.="/".$this->uri->segment($i);
		//}
		//$downloadfile= str_replace("%20", " " , $downloadfile);
		$downloadfile= $_SERVER['NFSN_SITE_ROOT'] . 'protected/clientmaterials/'.$clientcode."/".$filename;


		//http://www.jonasjohn.de/snippets/php/headers.htm
		//http://stackoverflow.com/questions/597159/sending-large-files-reliably-in-php
		if (file_exists($downloadfile)) {
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.$filename.'"'); 
				header('Content-Transfer-Encoding: binary');				
				$f = fopen($downloadfile, 'r');
				while(!feof($f)){
				    print fgets($f, 1024);		//regular users
					
				}
				fclose($f);
			}	
			
			//$finaldir = $_SERVER['NFSN_SITE_ROOT'] . "protected/clientmaterials/".$clientcode."/".$filename;
			//$data = file_get_contents($finaldir); // Read the file's contents
			//$name = $filename;
			//force_download($name, $data);
		}
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>