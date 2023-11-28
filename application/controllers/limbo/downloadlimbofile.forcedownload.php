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
		$downloadfile= $_SERVER['NFSN_SITE_ROOT'] . 'protected/limbo/'.$downloadfile;

		$filename =$this->uri->segment($numsegments);


		if (file_exists($downloadfile)) {
				$data =_readfile_chunked($downloadfile);
			//$data = file_get_contents($downloadfile); // Read the file's contents		
			force_download($filename, $data);
	 		exit;
		}

	 }

function _readfile_chunked($filename,$retbytes=true) {
   $chunksize = 1*(1024*1024); // how many bytes per chunk
   $buffer = '';
   $cnt =0;
   // $handle = fopen($filename, 'rb');
   $handle = fopen($filename, 'rb');
   if ($handle === false) {
       return false;
   }
   while (!feof($handle)) {
       $buffer = fread($handle, $chunksize);
       echo $buffer;
       ob_flush();
       flush();
       if ($retbytes) {
           $cnt += strlen($buffer);
       }
   }
       $status = fclose($handle);
   if ($retbytes && $status) {
       return $cnt; // return num. bytes delivered like readfile() does.
   }
   return $status;

}
	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>