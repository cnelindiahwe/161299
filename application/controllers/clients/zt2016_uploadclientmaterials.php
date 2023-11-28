<?php

class Zt2016_uploadclientmaterials extends My_Controller {

	function index()
	{


		$this->load->helper(array('url'));	

		$dirname=$this->input->post('clientdir');
		$clientcode=$this->input->post('clientcode');

		
		if ($_FILES["fileuploadname"]["error"] > 0){
				echo "<p>Error: " . $_FILES["fileuploadname"]["error"] . "</p>";
			   echo "<p><a href=\"".Base_Url().'clients/zt2016_manageclientmaterials/'.$dirname."\">Please try again.</a></p>";	
			
		} else {
			  	$sanitizedfilename=$this->security->sanitize_filename ($_FILES["fileuploadname"]["name"]);
				$sanitizedfilename=url_title($sanitizedfilename,"_");
				if (file_exists($_SERVER['DOCUMENT_ROOT'].'/zowtempa/etc/clientmaterials/'.$dirname."/". $sanitizedfilename)) {
				    echo "<p> protected/clientmaterials/".$dirname."/".$_FILES["fileuploadname"]["name"] . " already exists.<br/>.";
			        echo "<a href=\"".Base_Url().'clients/zt2016_manageclientmaterials/'.$clientcode."\">Please delete it first.</a></p>";	

				} else {
					if (is_uploaded_file($_FILES['fileuploadname']['tmp_name'])) {
						move_uploaded_file($_FILES["fileuploadname"]["tmp_name"],
						$_SERVER['DOCUMENT_ROOT'].'/zowtempa/etc/clientmaterials/' . $dirname."/".$sanitizedfilename);
						echo "file uploaded";
						redirect(Base_Url().'clients/zt2016_manageclientmaterials/'.$clientcode);
			      	}
				 }
		  }
	 }
}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>