<?php

class Uploadclientmaterials extends My_Controller {

	function index()
	{
		$clientdir=$this->input->post('clientdir');
		
		if ($_FILES["fileuploadname"]["error"] > 0){
				echo "Error: " . $_FILES["fileuploadname"]["error"] . "<br />";
		} else {
			  	$sanitizedfilename=$this->security->sanitize_filename ($_FILES["fileuploadname"]["name"]);
				$sanitizedfilename=url_title($sanitizedfilename,"_");
				if (file_exists($_SERVER['NFSN_SITE_ROOT'] . 'protected/clientmaterials/'.$clientdir."/". $sanitizedfilename)) {
				    echo "protected/clientmaterials/".$clientdir."/".$_FILES["fileuploadname"]["name"] . " already exists. Please delete it first.";
				} else {
					if (is_uploaded_file($_FILES['fileuploadname']['tmp_name'])) {
						move_uploaded_file($_FILES["fileuploadname"]["tmp_name"],
			     		$_SERVER['NFSN_SITE_ROOT'] . 'protected/clientmaterials/' . $clientdir."/".$sanitizedfilename);
			      	}
				 }
		  }
	 }
}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>