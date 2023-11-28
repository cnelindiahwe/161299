<?php

class Uploadlimbofile extends MY_Controller {
	
	function index()
	{
		$currentdir=$this->input->post('currentdir');		
		if ($_FILES["fileuploadname"]["error"] > 0)
		  {
		  echo "Error: " . $_FILES["fileuploadname"]["error"] . "<br />";
		  }
		else
		  {
		  echo "Upload: " . $_FILES["fileuploadname"]["name"] . "<br />";
		  echo "Type: " . $_FILES["fileuploadname"]["type"] . "<br />";
		  echo "Size: " . ($_FILES["fileuploadname"]["size"] / 1024) . " Kb<br />";
		  echo "Stored in: " . $_FILES["fileuploadname"]["tmp_name"]. " <br />";
		  }
		if (file_exists("/f5/zowtest/private/limbo/" . $_FILES["fileuploadname"]["name"]))
	      {
	      echo $_FILES["fileuploadname"]["name"] . " already exists. ";
	      }
	    else
	      {
	      	
		  	
	      move_uploaded_file($_FILES["fileuploadname"]["tmp_name"],
	      "/f5/zowtest/private/limbo/" . $_FILES["fileuploadname"]["name"]);
	      echo "Stored in: " . "upload/" . $_FILES["fileuploadname"]["name"];
		  
		  $file="/f5/zowtest/public/zowtrak2012/limbouploads/" . $_FILES["fileuploadname"]["name"];
		  $newfile="/f5/zowtest/private/limbo/" . $_FILES["fileuploadname"]["name"];
		  copy($file, $newfile);
	      }

		//find redirection dir
		/*$redirectdir="";
		for ($i = 3; $i <= $numsegments-1; $i++) {
		   $redirectdir.="/".$this->uri->segment($i);
		}
		redirect(Base_Url().'/limbo/limbodir/'.$redirectdir);
		*/

	 }
	// ################## ftp content ##################
	function _limboupload($deletefile)
	{


		return ;
	}


	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>