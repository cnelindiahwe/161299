<?php

class Zt2016_createclientmaterialsfolder extends MY_Controller {
	
	function index()
	{
			
		$this->load->helper(array('url'));	
		
		$clientcode = $this->uri->segment(3);
		
		$newdirname = $this->uri->segment(4);
		if ( $newdirname =="group") {
			$newdirname = $newdirname . "/". $this->uri->segment(5);
		} 
		$newdirname = dirname(dirname(dirname(__FILE__))).'/zowtempa/etc/clientmaterials/'.$newdirname;

		if (!is_dir($newdirname)) {
			echo "creating ".$newdirname;
		
			mkdir($newdirname,0777);
			
				$rpath = Base_Url().'clients/zt2016_manageclientmaterials/'.$clientcode;
				?> 
				<script>
				    window.location.replace("<?php echo $rpath; ?>");
				</script>
				<?php
			redirect($rpath);
		}
		else {
			echo "<p>Error creating: ".$newdirname."</p>";	
			echo "<p><a href=\"".Base_Url().'clients/zt2016_manageclientmaterials/'.$clientcode."\">Please try again</a></p>";	
		}

	 }
	
}

/* End of file zt2016_deleteclientmaterials */
/* Location: ./system/application/controllers/clients/zt2016_deleteclientmaterials */
?>